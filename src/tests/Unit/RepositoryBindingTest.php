<?php

namespace Tests\Unit;

use App\Repositories\OrganizationRepository;
use App\Repositories\OrganizationRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepositoryBindingTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_repository_interface_is_bound_to_implementation()
    {
        // Act
        $repository = app(OrganizationRepositoryInterface::class);

        // Assert
        $this->assertInstanceOf(OrganizationRepository::class, $repository);
    }

    public function test_organization_repository_implements_interface()
    {
        // Act
        $repository = new OrganizationRepository();

        // Assert
        $this->assertInstanceOf(OrganizationRepositoryInterface::class, $repository);
    }

    public function test_repository_can_be_resolved_from_container()
    {
        // Act
        $repository = resolve(OrganizationRepositoryInterface::class);

        // Assert
        $this->assertInstanceOf(OrganizationRepository::class, $repository);
    }

    public function test_repository_binding_is_singleton()
    {
        // Act
        $repository1 = app(OrganizationRepositoryInterface::class);
        $repository2 = app(OrganizationRepositoryInterface::class);

        // Assert
        $this->assertSame($repository1, $repository2);
    }
} 