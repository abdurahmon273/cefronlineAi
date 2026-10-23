<?php

namespace App\Http\Controllers\Api\V2\WithoutAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Http\Resources\Admin\RegionResource;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class V2RegionApiController extends Controller
{
    /**
     * @OA\Tag(
     *     name="Not Auth Pages",
     *     description="Public endpoints that do not require authentication"
     * )
     */

    /**
     * @OA\Get(
     *     path="/v2/regions",
     *     summary="Get all regions",
     *     description="Returns all regions with their related cities for unauthenticated clients.",
     *     tags={"Not Auth Pages"},
     *     @OA\Response(
     *         response=200,
     *         description="List of regions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Toshkent"),
     *                 @OA\Property(
     *                     property="region_cities",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=10),
     *                         @OA\Property(property="name", type="string", example="Chilonzor"),
     *                         @OA\Property(
     *                             property="region_id",
     *                             type="object",
     *                             nullable=true,
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Toshkent")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return RegionResource::collection(Region::with('regionCities')->get());
    }

    public function store(StoreRegionRequest $request)
    {
        $region = Region::create($request->all());

        return (new RegionResource($region))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Region $region)
    {
        abort_if(Gate::denies('region_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RegionResource($region);
    }

    public function update(UpdateRegionRequest $request, Region $region)
    {
        $region->update($request->all());

        return (new RegionResource($region))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Region $region)
    {
        abort_if(Gate::denies('region_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $region->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
