<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\SearchOrganizationsRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SearchOrganizationsRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_request_with_activity_passes_validation()
    {
        $request = new SearchOrganizationsRequest();
        $request->merge([
            'activity' => 'Technology'
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $request->withValidator($validator);
        $this->assertTrue($validator->passes());
    }

    public function test_valid_request_with_name_passes_validation()
    {
        $request = new SearchOrganizationsRequest();
        $request->merge([
            'name' => 'Tech Solutions'
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $request->withValidator($validator);
        $this->assertTrue($validator->passes());
    }

    public function test_valid_request_with_both_parameters_passes_validation()
    {
        $request = new SearchOrganizationsRequest();
        $request->merge([
            'activity' => 'Technology',
            'name' => 'Tech Solutions'
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $request->withValidator($validator);
        $this->assertTrue($validator->passes());
    }

    public function test_request_without_any_parameters_fails_validation()
    {
        $request = new SearchOrganizationsRequest();
        $request->merge([]);

        $validator = Validator::make($request->all(), $request->rules());
        $request->withValidator($validator);
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('search'));
    }

    public function test_activity_too_long_fails_validation()
    {
        $request = new SearchOrganizationsRequest();
        $request->merge([
            'activity' => str_repeat('a', 256) // 256 characters, exceeds max
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $request->withValidator($validator);
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('activity'));
    }

    public function test_name_too_long_fails_validation()
    {
        $request = new SearchOrganizationsRequest();
        $request->merge([
            'name' => str_repeat('a', 256) // 256 characters, exceeds max
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $request->withValidator($validator);
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    public function test_activity_with_invalid_type_fails_validation()
    {
        $request = new SearchOrganizationsRequest();
        $request->merge([
            'activity' => 123 // Should be string
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $request->withValidator($validator);
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('activity'));
    }

    public function test_name_with_invalid_type_fails_validation()
    {
        $request = new SearchOrganizationsRequest();
        $request->merge([
            'name' => 123 // Should be string
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $request->withValidator($validator);
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    public function test_empty_activity_string_passes_validation()
    {
        $request = new SearchOrganizationsRequest();
        $request->merge([
            'activity' => ''
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $request->withValidator($validator);
        $this->assertTrue($validator->passes());
    }

    public function test_empty_name_string_passes_validation()
    {
        $request = new SearchOrganizationsRequest();
        $request->merge([
            'name' => ''
        ]);

        $validator = Validator::make($request->all(), $request->rules());
        $request->withValidator($validator);
        $this->assertTrue($validator->passes());
    }

    public function test_custom_error_messages_are_defined()
    {
        $request = new SearchOrganizationsRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('activity.string', $messages);
        $this->assertArrayHasKey('activity.max', $messages);
        $this->assertArrayHasKey('name.string', $messages);
        $this->assertArrayHasKey('name.max', $messages);
    }

    public function test_authorize_returns_true()
    {
        $request = new SearchOrganizationsRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_with_validator_adds_custom_validation_rule()
    {
        $request = new SearchOrganizationsRequest();
        $request->merge([]);

        $validator = Validator::make($request->all(), $request->rules());
        $request->withValidator($validator);
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('search'));
        $this->assertEquals(
            'At least one search parameter (activity or name) is required.',
            $validator->errors()->first('search')
        );
    }
} 