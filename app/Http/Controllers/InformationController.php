<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Models\Information;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class InformationController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('information_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $informations = Information::orderBy('order_id')->get();

        $types=Information::TYPES;

        return view('admin.information.index', compact('informations','types'));
    }

    public function create()
    {
        abort_if(Gate::denies('information_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $types=Information::TYPES;
        return view('admin.information.create', compact('types'));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'information_link'=>'required',
            'type'=>'required',
        ]);
        $data=$request->all();
        $data['order_id']=Information::max('order_id')+1;

        $information = Information::create($data);

        return redirect()->route('admin.information.index');
    }

    public function edit($id)
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $information=Information::find($id);
        $types=Information::TYPES;


        return view('admin.information.edit', compact('information','types'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate($request,[
            'information_link'=>'required',
            'type'=>'required',
        ]);

        $information=Information::find($id);
        $information->update($request->all());

        return redirect()->route('admin.information.index');
    }

    public function show(Region $information)
    {
        abort_if(Gate::denies('information_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $information->load('informationCities');

        return view('admin.information.show', compact('information'));
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('information_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $information=Information::find($id);

        $information->delete();

        return back();
    }
}
