<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Organization",
 *     title="Organization",
 *     description="Organization model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="ООО Рога и Копыта"),
 *     @OA\Property(property="building_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="building", ref="#/components/schemas/Building"),
 *     @OA\Property(property="phone_numbers", type="array", @OA\Items(ref="#/components/schemas/PhoneNumber")),
 *     @OA\Property(property="activities", type="array", @OA\Items(ref="#/components/schemas/Activity"))
 * )
 */
class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'building_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(PhoneNumber::class);
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_organization');
    }

    public function scopeInBuilding($query, $buildingId)
    {
        return $query->where('building_id', $buildingId);
    }

    public function scopeNearby($query, $latitude, $longitude, $radius = 10)
    {
        $latDelta = $radius / 111;
        $lngDelta = $radius / (111 * cos(deg2rad($latitude)));

        return $query->whereHas('building', function ($q) use ($latitude, $longitude, $latDelta, $lngDelta) {
            $q->whereBetween('latitude', [$latitude - $latDelta, $latitude + $latDelta])
                ->whereBetween('longitude', [$longitude - $lngDelta, $longitude + $lngDelta]);
        });
//        return $query->whereHas('building', function ($q) use ($latitude, $longitude, $radius) {
//            $q->whereRaw('
//                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) *
//                cos(radians(longitude) - radians(?)) + sin(radians(?)) *
//                sin(radians(latitude)))) <= ?
//            ', [$latitude, $longitude, $latitude, $radius]);
//        });
    }

    public function scopeInArea($query, $lat1, $lng1, $lat2, $lng2)
    {
        return $query->whereHas('building', function ($q) use ($lat1, $lng1, $lat2, $lng2) {
            $q->whereBetween('latitude', [$lat1, $lat2])
              ->whereBetween('longitude', [$lng1, $lng2]);
        });
    }

    public function scopeWithActivity($query, $activityId)
    {
        return $query->whereHas('activities', function ($q) use ($activityId) {
            $q->where('activities.id', $activityId)
              ->orWhereHas('children', function ($childQ) use ($activityId) {
                  $childQ->where('activities.id', $activityId)
                         ->orWhereHas('children', function ($grandChildQ) use ($activityId) {
                             $grandChildQ->where('activities.id', $activityId);
                         });
              });
        });
    }

    public function scopeSearchByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }
}
