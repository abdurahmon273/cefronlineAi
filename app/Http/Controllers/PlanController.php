<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\GlobalSetting;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('admin.plans.index');
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
        return view('admin.plans.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'days' => ['required', 'integer', 'min:1', 'max:36500'],
            'credit_price' => ['required', 'integer', 'min:0', 'max:2147483647'],
        ]);

        Plan::create([
            'name' => $request->name,
            'price' => $request->price,
            'day' => $request->days,
            'credit_price' => $request->credit_price,
        ]);

        return redirect()->route('admin.plans.index');
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
        $plan=Plan::find($id);

        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'days' => ['required', 'integer', 'min:1', 'max:36500'],
            'credit_price' => ['required', 'integer', 'min:0', 'max:2147483647'],
        ]);

        $facility=Plan::find($id);

        $facility->update([
            'name' => $request->name,
            'price' => $request->price,
            'day' => $request->days,
            'credit_price' =>intval( $request->credit_price),
        ]);
        return redirect()->route('admin.plans.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $facility=Plan::find($id);

        $facility->delete();
        return redirect()->route('admin.plans.index');
    }
}
