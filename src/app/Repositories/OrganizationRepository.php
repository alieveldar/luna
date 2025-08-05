<?php

namespace App\Repositories;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    public function findById(int $id): ?Organization
    {
        return Organization::with(['building', 'phoneNumbers', 'activities'])
            ->find($id);
    }

    public function findByBuilding(int $buildingId): Collection
    {
        $building = Building::find($buildingId);
        
        if (!$building) {
            return new Collection();
        }

        return Organization::with(['building', 'phoneNumbers', 'activities'])
            ->inBuilding($buildingId)
            ->get();
    }

    public function findByActivity(int $activityId): Collection
    {
        $activity = Activity::find($activityId);
        
        if (!$activity) {
            return new Collection();
        }

        return Organization::with(['building', 'phoneNumbers', 'activities'])
            ->withActivity($activityId)
            ->get();
    }

    public function findNearby(float $latitude, float $longitude, float $radius = 10): Collection
    {
        return Organization::with(['building', 'phoneNumbers', 'activities'])
            ->nearby($latitude, $longitude, $radius)
            ->get();
    }

    public function findInArea(float $lat1, float $lng1, float $lat2, float $lng2): Collection
    {
        return Organization::with(['building', 'phoneNumbers', 'activities'])
            ->inArea($lat1, $lng1, $lat2, $lng2)
            ->get();
    }

    public function searchByName(string $name): Collection
    {
        return Organization::with(['building', 'phoneNumbers', 'activities'])
            ->searchByName($name)
            ->get();
    }

    public function searchByActivity(string $activityName): Collection
    {
        $activityIds = Activity::withNestedActivities($activityName)->pluck('id');
        
        return Organization::with(['building', 'phoneNumbers', 'activities'])
            ->whereHas('activities', function ($query) use ($activityIds) {
                $query->whereIn('activities.id', $activityIds);
            })
            ->get();
    }

    public function searchByActivityAndName(string $activityName, string $organizationName): Collection
    {
        $activityIds = Activity::withNestedActivities($activityName)->pluck('id');
        
        return Organization::with(['building', 'phoneNumbers', 'activities'])
            ->whereHas('activities', function ($query) use ($activityIds) {
                $query->whereIn('activities.id', $activityIds);
            })
            ->searchByName($organizationName)
            ->get();
    }
} 