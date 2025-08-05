<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\AreaOrganizationsRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AreaOrganizationsRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_request_passes_validation()
    {
        $request = new AreaOrganizationsRequest();
        $request->merge([
            'lat1' => 40.7128,
            'lng1' => -74.0060,
            'lat2' => 40.7589,
            'lng2' => -73.9851
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_missing_lat1_fails_validation()
    {
        $request = new AreaOrganizationsRequest();
        $request->merge([
            'lng1' => -74.0060,
            'lat2' => 40.7589,
            'lng2' => -73.9851
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lat1'));
    }

    public function test_missing_lng1_fails_validation()
    {
        $request = new AreaOrganizationsRequest();
        $request->merge([
            'lat1' => 40.7128,
            'lat2' => 40.7589,
            'lng2' => -73.9851
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lng1'));
    }

    public function test_missing_lat2_fails_validation()
    {
        $request = new AreaOrganizationsRequest();
        $request->merge([
            'lat1' => 40.7128,
            'lng1' => -74.0060,
            'lng2' => -73.9851
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lat2'));
    }

    public function test_missing_lng2_fails_validation()
    {
        $request = new AreaOrganizationsRequest();
        $request->merge([
            'lat1' => 40.7128,
            'lng1' => -74.0060,
            'lat2' => 40.7589
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lng2'));
    }

    public function test_invalid_lat1_type_fails_validation()
    {
        $request = new AreaOrganizationsRequest();
        $request->merge([
            'lat1' => 'invalid',
            'lng1' => -74.0060,
            'lat2' => 40.7589,
            'lng2' => -73.9851
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lat1'));
    }

    public function test_invalid_lng1_type_fails_validation()
    {
        $request = new AreaOrganizationsRequest();
        $request->merge([
            'lat1' => 40.7128,
            'lng1' => 'invalid',
            'lat2' => 40.7589,
            'lng2' => -73.9851
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lng1'));
    }

    public function test_invalid_lat2_type_fails_validation()
    {
        $request = new AreaOrganizationsRequest();
        $request->merge([
            'lat1' => 40.7128,
            'lng1' => -74.0060,
            'lat2' => 'invalid',
            'lng2' => -73.9851
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lat2'));
    }

    public function test_invalid_lng2_type_fails_validation()
    {
        $request = new AreaOrganizationsRequest();
        $request->merge([
            'lat1' => 40.7128,
            'lng1' => -74.0060,
            'lat2' => 40.7589,
            'lng2' => 'invalid'
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lng2'));
    }

    public function test_lat1_out_of_range_fails_validation()
    {
        $request = new AreaOrganizationsRequest();
        $request->merge([
            'lat1' => 100, // Invalid latitude
            'lng1' => -74.0060,
            'lat2' => 40.7589,
            'lng2' => -73.9851
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lat1'));
    }

    public function test_lng1_out_of_range_fails_validation()
    {
        $request = new AreaOrganizationsRequest();
        $request->merge([
            'lat1' => 40.7128,
            'lng1' => 200, // Invalid longitude
            'lat2' => 40.7589,
            'lng2' => -73.9851
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lng1'));
    }

    public function test_lat2_out_of_range_fails_validation()
    {
        $request = new AreaOrganizationsRequest();
        $request->merge([
            'lat1' => 40.7128,
            'lng1' => -74.0060,
            'lat2' => -100, // Invalid latitude
            'lng2' => -73.9851
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lat2'));
    }

    public function test_lng2_out_of_range_fails_validation()
    {
        $request = new AreaOrganizationsRequest();
        $request->merge([
            'lat1' => 40.7128,
            'lng1' => -74.0060,
            'lat2' => 40.7589,
            'lng2' => -200 // Invalid longitude
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('lng2'));
    }

    public function test_custom_error_messages_are_defined()
    {
        $request = new AreaOrganizationsRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('lat1.required', $messages);
        $this->assertArrayHasKey('lat1.numeric', $messages);
        $this->assertArrayHasKey('lat1.between', $messages);
        $this->assertArrayHasKey('lng1.required', $messages);
        $this->assertArrayHasKey('lng1.numeric', $messages);
        $this->assertArrayHasKey('lng1.between', $messages);
        $this->assertArrayHasKey('lat2.required', $messages);
        $this->assertArrayHasKey('lat2.numeric', $messages);
        $this->assertArrayHasKey('lat2.between', $messages);
        $this->assertArrayHasKey('lng2.required', $messages);
        $this->assertArrayHasKey('lng2.numeric', $messages);
        $this->assertArrayHasKey('lng2.between', $messages);
    }

    public function test_authorize_returns_true()
    {
        $request = new AreaOrganizationsRequest();
        $this->assertTrue($request->authorize());
    }
} 