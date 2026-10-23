<?php

namespace App\Http\Controllers\Api\V2\WithoutAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Http\Resources\Admin\CityResource;
use App\Models\City;
use Gate;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class V2CityApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v2/cities/{id}",
     *     summary="Get cities by region id",
     *     description="Returns all cities belonging to the given region id for unauthenticated clients.",
     *     tags={"Not Auth Pages"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Region identifier.",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of cities for the region",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=10),
     *                 @OA\Property(property="name", type="string", example="Chilonzor"),
     *                 @OA\Property(
     *                     property="region_id",
     *                     type="object",
     *                     nullable=true,
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Toshkent")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index($id)
    {
        $cities=City::with('region')->where('region_id',$id)->get();
//        abort_if(Gate::denies('city_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return CityResource::collection($cities);
    }

    public function store(StoreCityRequest $request)
    {
        $city = City::create($request->all());

        return (new CityResource($city))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(City $city)
    {
        abort_if(Gate::denies('city_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CityResource($city->load(['region']));
    }

    public function update(UpdateCityRequest $request, City $city)
    {
        $city->update($request->all());

        return (new CityResource($city))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(City $city)
    {
        abort_if(Gate::denies('city_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $city->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
