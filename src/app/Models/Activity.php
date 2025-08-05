<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Activity",
 *     title="Activity",
 *     description="Activity model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Продовольствие"),
 *     @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="parent", ref="#/components/schemas/Activity"),
 *     @OA\Property(property="children", type="array", @OA\Items(ref="#/components/schemas/Activity")),
 *     @OA\Property(property="organizations", type="array", @OA\Items(ref="#/components/schemas/Organization"))
 * )
 */
class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Activity::class, 'parent_id');
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'activity_organization');
    }

    public function scopeSearchByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    public static function withNestedActivities(string $activityName)
    {
        $root = self::where('name', 'like', "%{$activityName}%")->first();

        if (!$root) {
            return collect();
        }

        $allIds = $root->getAllChildrenIds();

        return self::whereIn('id', $allIds)->get();
    }

    public function getAllChildrenIds()
    {
        $ids = collect([$this->id]);

        foreach ($this->children as $child) {
            $ids = $ids->merge($child->getAllChildrenIds());
        }

        return $ids;
    }
}
