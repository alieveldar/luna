<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use App\Models\PhoneNumber;
use App\Repositories\OrganizationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private OrganizationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new OrganizationRepository();
    }

    public function test_find_by_id_returns_organization_with_relationships()
    {
        // Arrange
        $building = Building::factory()->create();
        $organization = Organization::factory()->create(['building_id' => $building->id]);
        $phoneNumber = PhoneNumber::factory()->create(['organization_id' => $organization->id]);
        $activity = Activity::factory()->create();
        $organization->activities()->attach($activity->id);

        // Act
        $result = $this->repository->findById($organization->id);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($organization->id, $result->id);
        $this->assertTrue($result->relationLoaded('building'));
        $this->assertTrue($result->relationLoaded('phoneNumbers'));
        $this->assertTrue($result->relationLoaded('activities'));
        $this->assertEquals($building->id, $result->building->id);
        $this->assertCount(1, $result->phoneNumbers);
        $this->assertCount(1, $result->activities);
    }

    public function test_find_by_id_returns_null_for_nonexistent_organization()
    {
        // Act
        $result = $this->repository->findById(999);

        // Assert
        $this->assertNull($result);
    }

    public function test_find_by_building_returns_organizations()
    {
        // Arrange
        $building = Building::factory()->create();
        $organization1 = Organization::factory()->create(['building_id' => $building->id]);
        $organization2 = Organization::factory()->create(['building_id' => $building->id]);
        $otherBuilding = Building::factory()->create();
        $otherOrganization = Organization::factory()->create(['building_id' => $otherBuilding->id]);

        // Act
        $result = $this->repository->findByBuilding($building->id);

        // Assert
        $this->assertCount(2, $result);
        $this->assertTrue($result->contains($organization1));
        $this->assertTrue($result->contains($organization2));
        $this->assertFalse($result->contains($otherOrganization));
    }

    public function test_find_by_building_returns_empty_collection_for_nonexistent_building()
    {
        // Act
        $result = $this->repository->findByBuilding(999);

        // Assert
        $this->assertTrue($result->isEmpty());
    }

    public function test_find_by_activity_returns_organizations()
    {
        // Arrange
        $activity = Activity::factory()->create();
        $organization1 = Organization::factory()->create();
        $organization2 = Organization::factory()->create();
        $organization1->activities()->attach($activity->id);
        $organization2->activities()->attach($activity->id);

        // Act
        $result = $this->repository->findByActivity($activity->id);

        // Assert
        $this->assertCount(2, $result);
        $this->assertTrue($result->contains($organization1));
        $this->assertTrue($result->contains($organization2));
    }

    public function test_find_by_activity_returns_empty_collection_for_nonexistent_activity()
    {
        // Act
        $result = $this->repository->findByActivity(999);

        // Assert
        $this->assertTrue($result->isEmpty());
    }

    public function test_find_nearby_returns_organizations_within_radius()
    {
        // Arrange - Use coordinates that are clearly far apart
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

        // Act - Search near NYC (40.7128, -74.0060) with 50km radius
        $result = $this->repository->findNearby(40.7128, -74.0060, 50);

        // Assert - Should find organizations in NYC area
        $this->assertTrue($result->contains($organization1));
        $this->assertTrue($result->contains($organization2));
        $this->assertFalse($result->contains($organization3)); // London is too far
    }

    public function test_find_in_area_returns_organizations_in_bounding_box()
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

        // Act - Search in NYC area bounding box
        $result = $this->repository->findInArea(40.7, -74.1, 40.8, -73.9);

        // Assert
        $this->assertTrue($result->contains($organization1));
        $this->assertTrue($result->contains($organization2));
        $this->assertFalse($result->contains($organization3)); // LA is outside the box
    }

    public function test_search_by_name_returns_matching_organizations()
    {
        // Arrange
        $organization1 = Organization::factory()->create(['name' => 'Tech Solutions Inc']);
        $organization2 = Organization::factory()->create(['name' => 'Digital Tech Corp']);
        $organization3 = Organization::factory()->create(['name' => 'Food Services LLC']);

        // Act
        $result = $this->repository->searchByName('Tech');

        // Assert
        $this->assertCount(2, $result);
        $this->assertTrue($result->contains($organization1));
        $this->assertTrue($result->contains($organization2));
        $this->assertFalse($result->contains($organization3));
    }

    public function test_search_by_activity_returns_organizations_with_matching_activity()
    {
        // Arrange
        $parentActivity = Activity::factory()->create(['name' => 'Technology']);
        $childActivity = Activity::factory()->create([
            'name' => 'Software Development',
            'parent_id' => $parentActivity->id
        ]);

        $organization1 = Organization::factory()->create();
        $organization2 = Organization::factory()->create();
        $organization1->activities()->attach($parentActivity->id);
        $organization2->activities()->attach($childActivity->id);

        // Debug: Check what activities are found
        $foundActivities = Activity::withNestedActivities('Technology');
        $this->assertCount(2, $foundActivities, 'Should find both parent and child activities');

        // Act
        $result = $this->repository->searchByActivity('Technology');

        // Assert
        $this->assertCount(2, $result);
        $this->assertTrue($result->contains($organization1));
        $this->assertTrue($result->contains($organization2));
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
        $result = $this->repository->searchByActivityAndName('Technology', 'Tech');

        // Assert
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($organization1));
        $this->assertFalse($result->contains($organization2));
    }
}
