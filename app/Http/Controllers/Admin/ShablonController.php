<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyShablonRequest;
use App\Http\Requests\StoreShablonRequest;
use App\Http\Requests\UpdateShablonRequest;
use App\Models\Shablon;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShablonController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('shablon_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $shablons = Shablon::all();

        return view('admin.shablons.index', compact('shablons'));
    }

    public function create()
    {
        abort_if(Gate::denies('shablon_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.shablons.create');
    }

    public function store(StoreShablonRequest $request)
    {
        $shablon = Shablon::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.shablons.index');
    }

    public function edit(Shablon $shablon)
    {
        abort_if(Gate::denies('shablon_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.shablons.edit', compact('shablon'));
    }

    public function update(UpdateShablonRequest $request, Shablon $shablon)
    {

        $shablon->update($request->all());

        return redirect()->route('admin.shablons.index');
    }

    public function show(Shablon $shablon)
    {
        abort_if(Gate::denies('shablon_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.shablons.show', compact('shablon'));
    }

    public function destroy(Shablon $shablon)
    {
        abort_if(Gate::denies('shablon_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $shablon->delete();

        return back();
    }

    public function massDestroy(MassDestroyShablonRequest $request)
    {
        $shablons = Shablon::find(request('ids'));

        foreach ($shablons as $shablon) {
            $shablon->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
