<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShablonRequest;
use App\Http\Requests\UpdateShablonRequest;
use App\Http\Resources\Admin\ShablonResource;
use App\Models\Shablon;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShablonApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('shablon_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ShablonResource(Shablon::all());
    }

    public function store(StoreShablonRequest $request)
    {
        $shablon = Shablon::create($request->all());

        return (new ShablonResource($shablon))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Shablon $shablon)
    {
        abort_if(Gate::denies('shablon_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ShablonResource($shablon);
    }

    public function update(UpdateShablonRequest $request, Shablon $shablon)
    {
        $shablon->update($request->all());

        return (new ShablonResource($shablon))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Shablon $shablon)
    {
        abort_if(Gate::denies('shablon_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $shablon->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
