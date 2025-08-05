<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Get(
 *     path="/api/buildings",
 *     summary="Вывод всех зданий с их организациями",
 *     tags={"Buildings"},
 *     security={{"api_key":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Вывод всех зданий с их организациями",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Building"))
 *         )
 *     )
 * )
 */
class BuildingController extends Controller
{
    public function index(): JsonResponse
    {
        $buildings = Building::with('organizations')->get();

        return response()->json([
            'success' => true,
            'data' => $buildings
        ]);
    }
}
