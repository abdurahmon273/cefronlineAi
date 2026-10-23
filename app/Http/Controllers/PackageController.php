<?php

namespace App\Http\Controllers;

use App\Models\GlobalSetting;
use App\Models\Package;
use App\Models\Plan;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('admin.packages.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $globalSetting = GlobalSetting::orderByDesc('effective_at')->firstOrFail();
        $data=[
            'profit_tax'=>$globalSetting->profit_tax+$globalSetting->chat_gpt_tax,
            'one_writing_price'=>$globalSetting->one_writing_price,
            'one_speaking_price'=>$globalSetting->one_speaking_price,
            'usd_current_currency'=>$globalSetting->usd_current_currency,
        ];
        return view('admin.packages.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'price' => 'required',
            'writing_count' => 'required',
            'speaking_count' => 'required',
            'credit_price' => 'required',
        ]);

        Package::create([
            'price'=>$request->price,
            'credit_price'=>$request->credit_price,
            'writing_count'=>$request->writing_count,
            'speaking_count'=>$request->speaking_count,
        ]);

        return redirect()->route('admin.packages.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Plan $facility)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $package=Package::find($id);
        $globalSetting = GlobalSetting::orderByDesc('effective_at')->firstOrFail();
        $data=[
            'profit_tax'=>$globalSetting->profit_tax+$globalSetting->chat_gpt_tax,
            'one_writing_price'=>$globalSetting->one_writing_price,
            'one_speaking_price'=>$globalSetting->one_speaking_price,
            'usd_current_currency'=>$globalSetting->usd_current_currency,
            'package'=>$package,
        ];

        return view('admin.packages.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'price' => 'required',
            'writing_count' => 'required',
            'speaking_count' => 'required',
            'credit_price' => 'required',
        ]);

        $package=Package::find($id);

        $package->update([
            'price'=>$request->price,
            'credit_price'=>$request->credit_price,
            'writing_count'=>$request->writing_count,
            'speaking_count'=>$request->speaking_count,
        ]);
        return redirect()->route('admin.packages.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $facility=Package::find($id);

        $facility->delete();
        return redirect()->route('admin.packages.index');
    }
}
