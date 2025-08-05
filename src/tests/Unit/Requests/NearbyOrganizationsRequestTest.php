<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\NearbyOrganizationsRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class NearbyOrganizationsRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_request_passes_validation()
    {
        $request = new NearbyOrganizationsRequest();
        $request->merge([
            'lat' => 40.7128,
            'lng' => -74.0060,
            'radius' => 10
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_valid_request_without_radius_passes_validation()
    {
        $request = new NearbyOrganizationsRequest();
        $request->merge([
            'lat' => 40.7128,
            'lng' => -74.0060
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_missing_lat_fails_validation()
    {
        $request = new NearbyOrganizationsRequest();
        $request->merge([
            'lng' => -74.0060,
            'radius' => 10
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lat'));
    }

    public function test_missing_lng_fails_validation()
    {
        $request = new NearbyOrganizationsRequest();
        $request->merge([
            'lat' => 40.7128,
            'radius' => 10
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lng'));
    }

    public function test_invalid_lat_type_fails_validation()
    {
        $request = new NearbyOrganizationsRequest();
        $request->merge([
            'lat' => 'invalid',
            'lng' => -74.0060,
            'radius' => 10
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lat'));
    }

    public function test_invalid_lng_type_fails_validation()
    {
        $request = new NearbyOrganizationsRequest();
        $request->merge([
            'lat' => 40.7128,
            'lng' => 'invalid',
            'radius' => 10
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lng'));
    }

    public function test_lat_out_of_range_fails_validation()
    {
        $request = new NearbyOrganizationsRequest();
        $request->merge([
            'lat' => 100, // Invalid latitude
            'lng' => -74.0060,
            'radius' => 10
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lat'));
    }

    public function test_lng_out_of_range_fails_validation()
    {
        $request = new NearbyOrganizationsRequest();
        $request->merge([
            'lat' => 40.7128,
            'lng' => 200, // Invalid longitude
            'radius' => 10
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lng'));
    }

    public function test_invalid_radius_type_fails_validation()
    {
        $request = new NearbyOrganizationsRequest();
        $request->merge([
            'lat' => 40.7128,
            'lng' => -74.0060,
            'radius' => 'invalid'
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('radius'));
    }

    public function test_radius_too_small_fails_validation()
    {
        $request = new NearbyOrganizationsRequest();
        $request->merge([
            'lat' => 40.7128,
            'lng' => -74.0060,
            'radius' => 0.05 // Too small
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('radius'));
    }

    public function test_radius_too_large_fails_validation()
    {
        $request = new NearbyOrganizationsRequest();
        $request->merge([
            'lat' => 40.7128,
            'lng' => -74.0060,
            'radius' => 1500 // Too large
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('radius'));
    }

    public function test_custom_error_messages_are_defined()
    {
        $request = new NearbyOrganizationsRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('lat.required', $messages);
        $this->assertArrayHasKey('lat.numeric', $messages);
        $this->assertArrayHasKey('lat.between', $messages);
        $this->assertArrayHasKey('lng.required', $messages);
        $this->assertArrayHasKey('lng.numeric', $messages);
        $this->assertArrayHasKey('lng.between', $messages);
        $this->assertArrayHasKey('radius.numeric', $messages);
        $this->assertArrayHasKey('radius.min', $messages);
        $this->assertArrayHasKey('radius.max', $messages);
    }

    public function test_authorize_returns_true()
    {
        $request = new NearbyOrganizationsRequest();
        $this->assertTrue($request->authorize());
    }
} 