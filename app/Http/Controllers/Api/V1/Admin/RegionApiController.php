<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Http\Resources\Admin\RegionResource;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class RegionApiController extends Controller
{
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
