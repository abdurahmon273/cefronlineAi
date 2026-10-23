<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Information;
use App\Models\StepPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class StepPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//        abort_if(Gate::denies('stepPage_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stepPages = StepPage::orderBy('order_id')->get();

        return view('admin.stepPages.index', compact('stepPages'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('stepPage_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.stepPages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'       => 'required',
            'description' => 'required',
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->only(['title', 'description']);
        $data['order_id'] = StepPage::max('order_id') + 1;

        if ($request->hasFile('image')) {
            // storage/app/public/stepPages/ ichiga saqlaydi
            $path = $request->file('image')->store('stepPages', 'public');
            $data['image'] = $path; // DB ga faqat path yoziladi
        }

        StepPage::create($data);

        return redirect()->route('admin.stepPages.index');
    }


    /**
     * Display the specified resource.
     */
    public function show(StepPage $stepPage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort_if(Gate::denies('stepPage_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stepPage=StepPage::find($id);

        return view('admin.stepPages.edit', compact('stepPage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StepPage $stepPage)
    {
        $request->validate([
            'title'       => ['required', 'string'],
            'description' => ['required', 'string'],
            'image'       => ['nullable', 'image','mimes:jpeg,png,jpg,gif,svg,webp','max:2048'],
            'order_id'    => ['nullable','integer'],
        ]);

        $data = $request->only(['title','description','order_id']);

        if ($request->hasFile('image')) {
            // eski rasmni o‘chirish (agar kerak bo‘lsa)
            if ($stepPage->image && \Storage::disk('public')->exists($stepPage->image)) {
                \Storage::disk('public')->delete($stepPage->image);
            }
            $data['image'] = $request->file('image')->store('stepPages', 'public');
        }

        $stepPage->update($data);

        return redirect()->route('admin.stepPages.index')
            ->with('success', __('Updated successfully'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('stepPage_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stepPage=StepPage::find($id);

        $stepPage->delete();

        return back();
    }
}
