<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Building",
 *     title="Building",
 *     description="Building model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="address", type="string", example="Москва, улб. Ленина д. 1"),
 *     @OA\Property(property="latitude", type="number", format="float", example=55.7558),
 *     @OA\Property(property="longitude", type="number", format="float", example=37.6176),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="organizations", type="array", @OA\Items(ref="#/components/schemas/Organization"))
 * )
 */
class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }
}
