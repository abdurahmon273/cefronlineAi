<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\PromoCodeCreateOnceRequest;
use App\Http\Requests\Admin\PromoCodeCreateRequest;
use App\Http\Requests\Admin\PromoCodeEditRequest;
use App\Models\Package;
use App\Models\Plan;
use App\Models\PremiumToUsers;
use App\Models\Promocode;
use App\Services\PromoCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromocodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.promoCodes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types=Promocode::TYPES;
        $plans=Plan::pluck('name','id');
        $packages=Package::pluck('price','id');

        return view('admin.promoCodes.create',compact('types','plans','packages'));
    }
    public function createOnce()
    {
        $types=Promocode::TYPES;
        $plans=Plan::pluck('name','id');
        $packages=Package::pluck('price','id');
        return view('admin.promoCodes.createOnce',compact('types','plans','packages'));
    }


    public function storeOnce(PromoCodeCreateOnceRequest $request, PromoCodeService $promoService)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {


            $promoCode=Promocode::create([
                'code'       => $data['code'],
                'type'       => intval($data['type']),
                'start_date' => $data['start_date'] ?? null,
                'end_date'   => $data['end_date'] ?? null,
                'discount'   => intval($data['discount']),
                'status'     => 1,
            ]);

            if (isset($data['plans'])){
                $promoCode->plans()->sync($data['plans']);
            }
            if (isset($data['packages'])){
                $promoCode->packages()->sync($data['packages']);
            }

            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();

            return redirect()
                ->route('admin.promoCodes.index')
                ->with('error', 'promoCode yaratishda xatolik qaytadan urinib koring');
        }


        return redirect()
            ->route('admin.promoCodes.index')
            ->with('success', 'Promocode yaratildi: ' . $promoCode->code);
    }
    public function store(PromoCodeCreateRequest $request, PromoCodeService $promoService)
    {
        $data = $request->validated();
//        dd($data);

//        DB::beginTransaction();
//        try {
            if (!empty($data['amount']) && $data['amount'] > 1) {
                $created = $promoService->createMany($data, (int) $data['amount']);
                return redirect()
                    ->route('admin.promoCodes.index')
                    ->with('success', count($created) . ' ta promo code yaratildi.');
            }

            $promo = $promoService->create($data);
//            DB::commit();
//        }catch (\Exception $exception){
//            DB::rollBack();
//
//            return redirect()
//                ->route('admin.promoCodes.index')
//                ->with('error', 'promoCode yaratishda xatolik qaytadan urinib koring');
//        }


        return redirect()
            ->route('admin.promoCodes.index')
            ->with('success', 'Promocode yaratildi: ' . $promo->code);
    }

    /**
     * Display the specified resource.
     */
    public function show(Promocode $promocode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $promoCode=Promocode::with(['plans'=>function ($query) {
            return $query->select('name','id');
        }])->find($id);
        $types=Promocode::TYPES;
        $plans=Plan::pluck('name','id');
        $packages=Package::pluck('price','id');
        $selectedPlans = $promoCode->plans->pluck('id')->all();
        $selectedPackages = $promoCode->packages->pluck('id')->all();

        return view('admin.promoCodes.edit',compact('promoCode','types','plans','selectedPlans','packages','selectedPackages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PromoCodeEditRequest $request, $id)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {


            $promoCode=Promocode::find($id);

            $promoCode->update([
                'code'       => $data['code'],
                'type'       => intval($data['type']),
                'start_date' => $data['start_date'] ?? null,
                'end_date'   => $data['end_date'] ?? null,
                'discount'   => intval($data['discount']),
                'status'     => 1,
            ]);

            if (isset($data['plans'])){
                $promoCode->plans()->sync($data['plans']);
            }

            if (isset($data['packages'])){
                $promoCode->packages()->sync($data['packages']);
            }


            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();

            return redirect()
                ->route('admin.promoCodes.index')
                ->with('error', 'Something went wrong , try again');
        }


        return redirect()
            ->route('admin.promoCodes.index')
            ->with('success', 'Promocode Edited : ' . $promoCode->code);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $promoCode=Promocode::find($id);
        if ($promoCode) {
            $promoCode->delete();
        }

        return redirect()->route('admin.promoCodes.index');
    }
}
