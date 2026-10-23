<?php

namespace App\Http\Controllers;

use App\Models\Information;
use App\Models\PrepareText;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PrepareTextController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('information_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $prepareTexts = PrepareText::all();

        $types=PrepareText::TYPES;

        return view('admin.prepareText.index', compact('prepareTexts','types'));
    }

    public function edit($id)
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $prepareText=PrepareText::find($id);
        $types=PrepareText::TYPES;

        return view('admin.prepareText.edit', compact('prepareText','types'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate($request,[
            'text'=>'required|min:3',
        ]);

        $prepareText=PrepareText::find($id);
        $prepareText->update($request->all());

        return redirect()->route('admin.prepareText.index');
    }

}
