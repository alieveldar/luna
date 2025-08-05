<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AreaOrganizationsRequest;
use App\Http\Requests\NearbyOrganizationsRequest;
use App\Http\Requests\SearchOrganizationsRequest;
use App\Repositories\OrganizationRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Info(
 *     title="Organization Directory API",
 *     version="1.0.0",
 *     description="REST API for Organization Directory"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="api_key",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization",
 *     description="API Key for authentication"
 * )
 */
class OrganizationController extends Controller
{
    public function __construct(
        private OrganizationRepositoryInterface $organizationRepository
    ) {}

    /**
     * @OA\Get(
     *     path="/api/organizations/building/{building_id}",
     *     summary="Список организаций в здании",
     *     tags={"Organizations"},
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="building_id",
     *         in="path",
     *         required=true,
     *         description="ID Здания",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций в здании",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *         )
     *     ),
     *     @OA\Response(response=404, description="Building not found")
     * )
     */
    public function getByBuilding(int $buildingId): JsonResponse
    {
        $organizations = $this->organizationRepository->findByBuilding($buildingId);

        if ($organizations->isEmpty()) {
            return response()->json(['error' => 'Building not found or no organizations found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/activity/{activity_id}",
     *     summary="Список организаций по деятельности",
     *     tags={"Organizations"},
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="activity_id",
     *         in="path",
     *         required=true,
     *         description="ID Деятельности",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций по деятельности",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *         )
     *     ),
     *     @OA\Response(response=404, description="Activity not found")
     * )
     */
    public function getByActivity(int $activityId): JsonResponse
    {
        $organizations = $this->organizationRepository->findByActivity($activityId);

        if ($organizations->isEmpty()) {
            return response()->json(['error' => 'Activity not found or no organizations found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/nearby",
     *     summary="Список организаций по географической близости",
     *     tags={"Organizations"},
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="lat",
     *         in="query",
     *         required=true,
     *         description="Широта",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="lng",
     *         in="query",
     *         required=true,
     *         description="Долгота",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         required=false,
     *         description="Радиус в киллометрах (по умолчанию: 10)",
     *         @OA\Schema(type="number", format="float", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций по географической близости",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function getNearby(NearbyOrganizationsRequest $request): JsonResponse
    {
        $latitude = $request->input('lat');
        $longitude = $request->input('lng');
        $radius = $request->input('radius', 10);

        $organizations = $this->organizationRepository->findNearby($latitude, $longitude, $radius);

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/area",
     *     summary="Список организаций в географической области",
     *     tags={"Organizations"},
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="lat1",
     *         in="query",
     *         required=true,
     *         description="Первая широта",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="lng1",
     *         in="query",
     *         required=true,
     *         description="Первая долгота",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="lat2",
     *         in="query",
     *         required=true,
     *         description="Вторая широта",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="lng2",
     *         in="query",
     *         required=true,
     *         description="Вторая долгота",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций в географической области",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function getInArea(AreaOrganizationsRequest $request): JsonResponse
    {
        $lat1 = $request->input('lat1');
        $lng1 = $request->input('lng1');
        $lat2 = $request->input('lat2');
        $lng2 = $request->input('lng2');

        $organizations = $this->organizationRepository->findInArea($lat1, $lng1, $lat2, $lng2);

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/{id}",
     *     summary="Сведения об организации",
     *     tags={"Organizations"},
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Organization ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Сведения об организации",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Organization")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Organization not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $organization = $this->organizationRepository->findById($id);

        if (!$organization) {
            return response()->json(['error' => 'Organization not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $organization
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/search",
     *     summary="Поиск организации",
     *     tags={"Organizations"},
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="activity",
     *         in="query",
     *         required=false,
     *         description="Поиск организаций по деятельности",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         description="Поиск организаций по названию",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Результаты поиска организаций",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function search(SearchOrganizationsRequest $request): JsonResponse
    {
        $activity = $request->input('activity');
        $name = $request->input('name');

        $organizations = collect();

        if ($activity && $name) {
            $organizations = $this->organizationRepository->searchByActivityAndName($activity, $name);
        } elseif ($activity) {
            $organizations = $this->organizationRepository->searchByActivity($activity);
        } elseif ($name) {
            $organizations = $this->organizationRepository->searchByName($name);
        }

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }
}
