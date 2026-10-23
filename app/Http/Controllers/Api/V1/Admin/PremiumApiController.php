<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Premium\FacilityResource;
use App\Http\Resources\Premium\PlanResource;
use App\Models\Facility;
use App\Models\Plan;
use App\Models\PremiumToUsers;
use App\Models\Promocode;
use App\Models\User;
use App\Services\PremiumApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class PremiumApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function facilities(Request $request)
    {

        if ($request->device_id) {
            if (auth()->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }

        $facilities = Facility::all();

        return facilityResource::collection($facilities);
    }
    public function plans(Request $request)
    {
        if ($request->device_id) {
            if (auth()->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }


        $plans = Plan::with(['premium_to_users'=>function ($query) {
            return $query->where('user_id', auth()->id());
        }])->get();

        return PlanResource::collection($plans);
    }
    public function clicked_pay(Request $request)
    {

        $user = User::findOrFail($request->user_id);
        $plan = Plan::findOrFail($request->plan_id);
        $price=$plan->price;
        if ($request->promo_code_id){
           $promoCode=Promocode::find($request->promo_code_id);
           $price=round($plan->price - ($plan->price*($promoCode->discount)/100),2);
        }

        $user->update([
            'tmp_payment_slug'=>$plan->id,
            'tmp_payment_price'=>$price,
            'tmp_payment_promocode'=>$request->promo_code_id ?? null,
            'tmp_payment_type'=>0,
        ]);

        return response()->json([
            'success' => true,
            'code'=>200,
            'message'=>'Success'
        ]);

    }

    public function freePremium(Request $request)
    {
        $user = auth()->user();
        $plan = Plan::findOrFail($request->plan_id);
        $start_time = now()->format('Y-m-d H:i:s');
        $end_time = Carbon::parse($start_time)->addDays($plan->day);
        $promoCodeId=$request->promocode;
        $discount=0;

        $promoCode = PromoCode::find($promoCodeId);

        if ($promoCode) {
            $promoCodeId = $promoCode->id;
            $discount = $promoCode->discount;
            $onceAlreadyUsedThisUser = Promocode::with('premium_to_users')
                ->whereHas('premium_to_users',function ($premium_to_user) use ($user){
                return $premium_to_user->where('user_id', $user->id);
                })->where('id',$promoCodeId)
                ->exists();

            if ($onceAlreadyUsedThisUser) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Bu promo kod avval foydalangansiz.',
                    'code'    => 'PROMO_ALREADY_USED',
                ], 404);
            }
        }else{
            return response()->json([
                'ok'      => false,
                'message' => 'promo kod mavjud emas.',
                'code'    => 'PROMO_NOT_FOUND',
            ], 404);
        }

        $today = now();

        $existingPlan = PremiumToUsers::where('user_id', $user->id)
            ->where(function ($query) use ($today) {
                $query->where('start_time', '<=', $today)
                    ->where('end_time', '>=', $today);
            })
            ->latest()
            ->first();

        if ($existingPlan) {
            $start_time = $existingPlan->end_time;
            $end_time = Carbon::parse($start_time)->addDays($plan->day);

        }
        $existingPlan = PremiumToUsers::create([
            'user_id' => $user->id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'type' => '0',
            'price' => $plan->price,
            'admin_id' => null,
            'plan_id' => $plan->id,
            'discount' => $discount,
            'promocode_id' => $promoCodeId,
        ]);

        $ip_address = $request->ip();
        $location = Location::get($ip_address);
        $is_uzbekistan = $location && $location->countryName == 'Uzbekistan';

        return response()->json([
            true,
            'start_time' => $existingPlan->start_time,
            'end_time' => $existingPlan->end_time,
            'is_uzbekistan' => $is_uzbekistan,
        ]);

    }
    public function checkPromoCode(Request $request)
    {
        $now   = Carbon::now();
        $code  = $request->input('code');

        $base = Promocode::query()->where('code', $code);

        if (! (clone $base)->exists()) {
            return response()->json([
                'ok'      => false,
                'message' => 'Bunday promo kod topilmadi.',
                'code'    => 'PROMO_NOT_FOUND',
            ], 404);
        }


        $dateValid = (clone $base)
            ->where(function ($q) use ($now) {
                $q->where(function ($qq) use ($now) {
                    $qq->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                })
                    ->orWhere(function ($qq) use ($now) {
                        $qq->whereNull('start_date')
                            ->where('end_date', '>=', $now);
                    })
                    ->orWhere(function ($qq) use ($now) {
                        $qq->where('start_date', '<=', $now)
                            ->whereNull('end_date');
                    });
            })
            ->exists();

        if (! $dateValid) {
            return response()->json([
                'ok'      => false,
                'message' => 'Promo kod aktiv emas.',
                'code'    => 'PROMO_NOT_ACTIVE',
            ], 404);
        }

        $onceAlreadyUsed = (clone $base)
            ->where('type', 0)
            ->whereHas('premium_to_users') // mavjud bog‘lanish bor = ishlatilgan
            ->exists();

        if ($onceAlreadyUsed) {
            return response()->json([
                'ok'      => false,
                'message' => 'Bu promo kod avval qo‘llangan (faqat bir marta foydalanish mumkin).',
                'code'    => 'PROMO_ALREADY_USED',
            ], 404);
        }

        $onceAlreadyUsedThisUser = (clone $base)
            ->whereHas('premium_to_users',function ($premium_to_user){
                return $premium_to_user->where('user_id', auth()->id());
            })
            ->exists();

        if ($onceAlreadyUsedThisUser) {
            return response()->json([
                'ok'      => false,
                'message' => 'Bu promo kod avval foydalangansiz.',
                'code'    => 'PROMO_ALREADY_USED',
            ], 404);
        }

        $promo = (clone $base)
            ->with(['plans:id,name'])
            ->first(['id','code','type','start_date','end_date','discount','status']);

        $discount = $promo ? (float) $promo->discount : 0;

        $plans = Plan::orderBy('order_id')->get();

        $plans->map(function ($p) use ($promo, $discount) {
            if ($promo && $promo->plans->contains('id', $p->id)) {
                $p->setAttribute('discount', (int) $discount);
                $p->setAttribute(
                    'discounted_price',
                    round(max(0, $p->price * ((100 - $discount) / 100)), 2)
                );
            } else {
                $p->setAttribute('discount', null);
                $p->setAttribute('discounted_price', (float) $p->price);
            }
            return $p;
        });

        return response()->json([
            'success'   => true,
            'data' => [
                'id'         => $promo->id,
                'code'       => $promo->code,
                'discount'   => (int)$promo->discount,
            ],
            'plans'=>PlanResource::collection($plans),
            'message' => 'Promo kod topildi.',
        ], 200);
    }

    public function check_premium(Request $request)
    {
        $user = auth()->user();
        $today = now();

        $ip_address = $request->ip();
        $location = Location::get($ip_address);
        $is_uzbekistan = $location && $location->countryName == 'Uzbekistan';

        if ($user->mobil_number=='+998941070524'){
            $is_uzbekistan=false;
        }

        $existingPlan = PremiumToUsers::where('user_id', $user->id)
            ->where(function ($query) use ($today) {
                $query->where('start_time', '<=', $today)
                    ->where('end_time', '>=', $today);
            })
            ->orderByDesc('id')
            ->first();

        if ($existingPlan) {
            return response()->json([
                 true,
                'start_time' => $existingPlan->start_time,
                'end_time' => $existingPlan->end_time,
                'is_uzbekistan' => $is_uzbekistan,
                'plan_id'=>$existingPlan->plan_id,
            ]);
        } else {
            if ($is_uzbekistan){
                return response()->json([
                    false,
                    'is_uzbekistan' => $is_uzbekistan,
                ]);
            }else{
                return response()->json([
                     true,
                    'is_uzbekistan' => $is_uzbekistan,
                    'start_time'      => $today->toDateTimeString(),
                    'end_time'        => $today->copy()->addDays(3)->toDateTimeString(),
                    'plan_id'=>null
                ]);
            }

        }
    }

    public function test_unpremium()
    {
        $premiumApi = new PremiumApiService();
        return $premiumApi->unPremium();
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
