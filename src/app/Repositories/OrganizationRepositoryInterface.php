<?php

namespace App\Repositories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;

interface OrganizationRepositoryInterface
{
    public function findById(int $id): ?Organization;
    public function findByBuilding(int $buildingId): Collection;
    public function findByActivity(int $activityId): Collection;
    public function findNearby(float $latitude, float $longitude, float $radius = 10): Collection;
    public function findInArea(float $lat1, float $lng1, float $lat2, float $lng2): Collection;
    public function searchByName(string $name): Collection;
    public function searchByActivity(string $activityName): Collection;
    public function searchByActivityAndName(string $activityName, string $organizationName): Collection;
} 