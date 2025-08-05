<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use App\Models\PhoneNumber;
use App\Models\User;
use App\Repositories\OrganizationRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrganizationControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $apiKey;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user with API key for authentication
        $this->user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        // Use the configured API key
        $this->apiKey = config('app.api_key');
    }

    public function test_get_by_building_returns_organizations()
    {
        // Arrange
        $building = Building::factory()->create();
        $organization1 = Organization::factory()->create(['building_id' => $building->id]);
        $organization2 = Organization::factory()->create(['building_id' => $building->id]);
        $otherBuilding = Building::factory()->create();
        $otherOrganization = Organization::factory()->create(['building_id' => $otherBuilding->id]);

        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson("/api/organizations/building/{$building->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'building_id',
                        'building',
                        'phone_numbers',
                        'activities'
                    ]
                ]
            ]);

        $responseData = $response->json('data');
        $this->assertCount(2, $responseData);
        $this->assertTrue(collect($responseData)->pluck('id')->contains($organization1->id));
        $this->assertTrue(collect($responseData)->pluck('id')->contains($organization2->id));
        $this->assertFalse(collect($responseData)->pluck('id')->contains($otherOrganization->id));
    }

    public function test_get_by_building_returns_404_for_nonexistent_building()
    {
        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson('/api/organizations/building/999');

        // Assert
        $response->assertStatus(404)
            ->assertJson(['error' => 'Building not found or no organizations found']);
    }

    public function test_get_by_activity_returns_organizations()
    {
        // Arrange
        $activity = Activity::factory()->create();
        $organization1 = Organization::factory()->create();
        $organization2 = Organization::factory()->create();
        $organization1->activities()->attach($activity->id);
        $organization2->activities()->attach($activity->id);

        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson("/api/organizations/activity/{$activity->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'building_id',
                        'building',
                        'phone_numbers',
                        'activities'
                    ]
                ]
            ]);

        $responseData = $response->json('data');
        $this->assertCount(2, $responseData);
        $this->assertTrue(collect($responseData)->pluck('id')->contains($organization1->id));
        $this->assertTrue(collect($responseData)->pluck('id')->contains($organization2->id));
    }

    public function test_get_by_activity_returns_404_for_nonexistent_activity()
    {
        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson('/api/organizations/activity/999');

        // Assert
        $response->assertStatus(404)
            ->assertJson(['error' => 'Activity not found or no organizations found']);
    }

    public function test_get_nearby_returns_organizations_within_radius()
    {
        // Arrange
        $building1 = Building::factory()->create([
            'latitude' => 40.7128,
            'longitude' => -74.0060
        ]);
        $building2 = Building::factory()->create([
            'latitude' => 40.7589,
            'longitude' => -73.9851
        ]);
        $building3 = Building::factory()->create([
            'latitude' => 51.5074, // London coordinates - definitely far from NYC
            'longitude' => -0.1278
        ]);

        $organization1 = Organization::factory()->create(['building_id' => $building1->id]);
        $organization2 = Organization::factory()->create(['building_id' => $building2->id]);
        $organization3 = Organization::factory()->create(['building_id' => $building3->id]);

        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson('/api/organizations/nearby?lat=40.7128&lng=-74.0060&radius=50');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'building_id',
                        'building',
                        'phone_numbers',
                        'activities'
                    ]
                ]
            ]);

        $responseData = $response->json('data');
        $this->assertTrue(collect($responseData)->pluck('id')->contains($organization1->id));
        $this->assertTrue(collect($responseData)->pluck('id')->contains($organization2->id));
        $this->assertFalse(collect($responseData)->pluck('id')->contains($organization3->id)); // London is too far
    }

    public function test_get_nearby_validates_required_parameters()
    {
        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson('/api/organizations/nearby');

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['lat', 'lng']);
    }

    public function test_get_nearby_validates_coordinate_ranges()
    {
        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson('/api/organizations/nearby?lat=100&lng=200&radius=10');

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['lat', 'lng']);
    }

    public function test_get_in_area_returns_organizations_in_bounding_box()
    {
        // Arrange
        $building1 = Building::factory()->create([
            'latitude' => 40.7128,
            'longitude' => -74.0060
        ]);
        $building2 = Building::factory()->create([
            'latitude' => 40.7589,
            'longitude' => -73.9851
        ]);
        $building3 = Building::factory()->create([
            'latitude' => 34.0522,
            'longitude' => -118.2437
        ]);

        $organization1 = Organization::factory()->create(['building_id' => $building1->id]);
        $organization2 = Organization::factory()->create(['building_id' => $building2->id]);
        $organization3 = Organization::factory()->create(['building_id' => $building3->id]);

        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson('/api/organizations/area?lat1=40.7&lng1=-74.1&lat2=40.8&lng2=-73.9');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'building_id',
                        'building',
                        'phone_numbers',
                        'activities'
                    ]
                ]
            ]);

        $responseData = $response->json('data');
        $this->assertTrue(collect($responseData)->pluck('id')->contains($organization1->id));
        $this->assertTrue(collect($responseData)->pluck('id')->contains($organization2->id));
        $this->assertFalse(collect($responseData)->pluck('id')->contains($organization3->id));
    }

    public function test_get_in_area_validates_required_parameters()
    {
        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson('/api/organizations/area');

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['lat1', 'lng1', 'lat2', 'lng2']);
    }

    public function test_show_returns_organization_details()
    {
        // Arrange
        $building = Building::factory()->create();
        $organization = Organization::factory()->create(['building_id' => $building->id]);
        $phoneNumber = PhoneNumber::factory()->create(['organization_id' => $organization->id]);
        $activity = Activity::factory()->create();
        $organization->activities()->attach($activity->id);

        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson("/api/organizations/{$organization->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'building_id',
                    'building',
                    'phone_numbers',
                    'activities'
                ]
            ]);

        $responseData = $response->json('data');
        $this->assertEquals($organization->id, $responseData['id']);
        $this->assertEquals($organization->name, $responseData['name']);
        $this->assertEquals($building->id, $responseData['building']['id']);
        $this->assertCount(1, $responseData['phone_numbers']);
        $this->assertCount(1, $responseData['activities']);
    }

    public function test_show_returns_404_for_nonexistent_organization()
    {
        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson('/api/organizations/999');

        // Assert
        $response->assertStatus(404)
            ->assertJson(['error' => 'Organization not found']);
    }

    public function test_search_by_name_returns_matching_organizations()
    {
        // Arrange
        $organization1 = Organization::factory()->create(['name' => 'Tech Solutions Inc']);
        $organization2 = Organization::factory()->create(['name' => 'Digital Tech Corp']);
        $organization3 = Organization::factory()->create(['name' => 'Food Services LLC']);

        // Debug: Check if organizations were created
        $this->assertDatabaseHas('organizations', ['name' => 'Tech Solutions Inc']);
        $this->assertDatabaseHas('organizations', ['name' => 'Digital Tech Corp']);

        // Debug: Test repository directly
        $repository = app(OrganizationRepositoryInterface::class);
        $directResult = $repository->searchByName('Tech');
        $this->assertCount(2, $directResult, 'Repository should find 2 organizations directly');

        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson('/api/organizations/search?name=Tech');

        // Debug: Check response
        if ($response->status() !== 200) {
            $this->fail('Response status is ' . $response->status() . ': ' . $response->content());
        }

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'building_id',
                        'building',
                        'phone_numbers',
                        'activities'
                    ]
                ]
            ]);

        $responseData = $response->json('data');
        $this->assertCount(2, $responseData);
        $this->assertTrue(collect($responseData)->pluck('id')->contains($organization1->id));
        $this->assertTrue(collect($responseData)->pluck('id')->contains($organization2->id));
        $this->assertFalse(collect($responseData)->pluck('id')->contains($organization3->id));
    }

    public function test_search_by_activity_returns_matching_organizations()
    {
        // Arrange
        $activity = Activity::factory()->create(['name' => 'Technology']);
        $organization1 = Organization::factory()->create();
        $organization2 = Organization::factory()->create();
        $organization1->activities()->attach($activity->id);
        $organization2->activities()->attach($activity->id);

        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson('/api/organizations/search?activity=Technology');

        // Assert
        $response->assertStatus(200);

        $responseData = $response->json('data');
        $this->assertCount(2, $responseData);
        $this->assertTrue(collect($responseData)->pluck('id')->contains($organization1->id));
        $this->assertTrue(collect($responseData)->pluck('id')->contains($organization2->id));
    }

    public function test_search_by_activity_and_name_returns_matching_organizations()
    {
        // Arrange
        $activity = Activity::factory()->create(['name' => 'Technology']);
        $organization1 = Organization::factory()->create(['name' => 'Tech Solutions Inc']);
        $organization2 = Organization::factory()->create(['name' => 'Food Services LLC']);
        $organization1->activities()->attach($activity->id);
        $organization2->activities()->attach($activity->id);

        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson('/api/organizations/search?activity=Technology&name=Tech');

        // Assert
        $response->assertStatus(200);

        $responseData = $response->json('data');
        $this->assertCount(1, $responseData);
        $this->assertTrue(collect($responseData)->pluck('id')->contains($organization1->id));
        $this->assertFalse(collect($responseData)->pluck('id')->contains($organization2->id));
    }

    public function test_search_validates_at_least_one_parameter()
    {
        // Act
        $response = $this->withHeaders(['Authorization' => $this->apiKey])
            ->getJson('/api/organizations/search');

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['search']);
    }


    public function test_requires_api_key_authentication()
    {
        // Act
        $response = $this->getJson('/api/organizations/1');

        // Assert
        $response->assertStatus(401);
    }
}
