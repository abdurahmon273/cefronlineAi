<?php

namespace App\Http\Controllers\Api\V2\Premium;

use App\Http\Controllers\Api\V2ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Premium\ActivePlanResource;
use App\Http\Resources\Premium\FacilityResource;
use App\Http\Resources\V2\Premium\CreditResource;
use App\Http\Resources\V2\Premium\PlanResource;
use App\Models\BalanceEdit;
use App\Models\Facility;
use App\Models\GlobalSetting;
use App\Models\Package;
use App\Models\Plan;
use App\Models\PremiumToUsers;
use App\Models\Promocode;
use App\Models\User;
use App\Services\PremiumApiService;
use App\Traits\BillingTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class PremiumApiController extends V2ApiController
{
    use BillingTrait;

    /**
     * @OA\Get(
     *     path="/v2/facilities",
     *     operationId="getV2PremiumFacilities",
     *     tags={"Premium API"},
     *     summary="Get premium facilities , Not Auth",
     *     description="Returns premium facility list.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="device_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="device-123")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Unlimited access to premium exercises")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized or device mismatch",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Undeviceided")
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="Unauthenticated.")
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function facilities(Request $request)
    {

        if ($request->device_id && auth('sanctum')->user()) {
            if (auth('sanctum')->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401);
            }
        }

        $facilities = Facility::all();

        return facilityResource::collection($facilities);
    }
    /**
     * @OA\Get(
     *     path="/v2/plans",
     *     operationId="getV2PremiumPlans",
     *     tags={"Premium API"},
     *     summary="Get premium plans ,Not Auth",
     *     description="Returns premium plans with discount and discounted price.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="device_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="device-123")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="1 month"),
     *                     @OA\Property(property="days", type="integer", example=30),
     *                     @OA\Property(property="price", type="number", format="float", example=89000),
     *                     @OA\Property(property="credit_price", type="number", format="float", example=2),
     *                     @OA\Property(property="writing_count", type="integer", example=10),
     *                     @OA\Property(property="speaking_count", type="integer", example=5),
     *                     @OA\Property(property="status", type="boolean", example=false),
     *                     @OA\Property(property="discount", type="integer", nullable=true, example=20),
     *                     @OA\Property(property="discounted_price", type="number", format="float", example=71200)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized or device mismatch",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Undeviceided")
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="Unauthenticated.")
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function plans(Request $request)
    {
        if ($request->device_id && auth('sanctum')->user()) {
            if (auth('sanctum')->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401);
            }
        }

        $globalSetting = GlobalSetting::orderByDesc('effective_at')->select('one_writing_price','one_speaking_price','profit_tax','chat_gpt_tax','usd_current_currency')->firstOrFail();
        $data=[
            'one_writing_price'=>($globalSetting->one_writing_price * $globalSetting->usd_current_currency) / 100 *  (100 + $globalSetting->profit_tax+$globalSetting->chat_gpt_tax) ,
            'one_speaking_price'=>($globalSetting->one_speaking_price * $globalSetting->usd_current_currency) / 100 *  (100 + $globalSetting->profit_tax+$globalSetting->chat_gpt_tax) ,
        ];

        $plans = Plan::with(['premium_to_users'=>function ($query) {
            return $query->when(auth('sanctum')->user(),function($subQuery){
                return $subQuery->where('user_id', auth('sanctum')->id()) ;
            });
        }])->orderBy('order_id')->get();

        $plans->map(function ($plan) use ($data) {
            $plan->setAttribute('writing_count', $data['one_writing_price'] > 0
                ? (int) floor($plan->credit_price / $data['one_writing_price'])
                : 0);
            $plan->setAttribute('speaking_count', $data['one_speaking_price'] > 0
                ? (int) floor($plan->credit_price / $data['one_speaking_price'])
                : 0);
            return $plan;
        });

        return PlanResource::collection($plans);
    }
    /**
     * @OA\Get(
     *     path="/v2/Packages",
     *     operationId="getV2PremiumPackages",
     *     tags={"Premium API"},
     *     summary="Get credit packages ,Not Auth",
     *     description="Returns credit packages for writing and speaking checks.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="device_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="device-123")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="price", type="number", format="float", example=49000),
     *                     @OA\Property(property="writing_count", type="integer", example=10),
     *                     @OA\Property(property="speaking_count", type="integer", example=5),
     *                     @OA\Property(property="status", type="boolean", example=false),
     *                     @OA\Property(property="discount", type="integer", nullable=true, example=15),
     *                     @OA\Property(property="discounted_price", type="number", format="float", example=41650)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized or device mismatch",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Undeviceided")
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="Unauthenticated.")
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function Packages(Request $request)
    {
        if ($request->device_id && auth('sanctum')->user()) {
            if (auth('sanctum')->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401);
            }
        }


        $plans = Package::with(['balance_edits'=>function ($query) {
            return $query->when(auth('sanctum')->user(),function($subQuery){
                return $subQuery->where('user_id', auth('sanctum')->id()) ;
            });
        }])->orderBy('order_id')->get();

        return CreditResource::collection($plans);
    }
    /**
     * @OA\Post(
     *     path="/v2/plan/clicked/pay",
     *     operationId="postV2PremiumPlanClickedPay",
     *     tags={"Premium API"},
     *     summary="Prepare plan payment",
     *     description="Saves temporary payment data for the selected user and premium plan.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"plan_id"},
     *             @OA\Property(property="plan_id", type="integer", example=1),
     *             @OA\Property(property="promo_code_id", type="integer", nullable=true, example=4)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plan not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Plan] 1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function plan_clicked_pay(Request $request)
    {

        $user = auth('sanctum')->user();
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

    /**
     * @OA\Post(
     *     path="/v2/package/clicked/pay",
     *     operationId="postV2PremiumPackageClickedPay",
     *     tags={"Premium API"},
     *     summary="Prepare package payment",
     *     description="Saves temporary payment data for the selected user and credit package.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"package_id"},
     *             @OA\Property(property="package_id", type="integer", example=1),
     *             @OA\Property(property="promo_code_id", type="integer", nullable=true, example=4)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Package not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Package] 1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function package_clicked_pay(Request $request)
    {

        $user = auth('sanctum')->user();
        $package = Package::findOrFail($request->package_id);
        $price=$package->price;
        if ($request->promo_code_id){
            $promoCode=Promocode::find($request->promo_code_id);
            $price=round($package->price - ($package->price*($promoCode->discount)/100),2);
        }

        $user->update([
            'tmp_payment_slug'=>$package->id,
            'tmp_payment_price'=>$price,
            'tmp_payment_promocode'=>$request->promo_code_id ?? null,
            'tmp_payment_type'=>1,
        ]);

        return response()->json([
            'success' => true,
            'code'=>200,
            'message'=>'Success'
        ]);

    }

    /**
     * @OA\Post(
     *     path="/v2/checkPromoCode",
     *     operationId="postV2PremiumCheckPromoCode",
     *     tags={"Premium API"},
     *     summary="Check promo code, Not Auth",
     *     description="Checks promo code and returns discounted plans or packages depending on type. Use type=0 for plans, type=1 for packages.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code","type"},
     *             @OA\Property(property="code", type="string", example="SPRING20"),
     *             @OA\Property(property="type", type="integer", enum={0,1}, example=0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Promo code found",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=true),
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=4),
     *                         @OA\Property(property="code", type="string", example="SPRING20"),
     *                         @OA\Property(property="discount", type="integer", example=20)
     *                     ),
     *                     @OA\Property(
     *                         property="plans",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="1 month"),
     *                             @OA\Property(property="days", type="integer", example=30),
     *                             @OA\Property(property="price", type="number", format="float", example=89000),
     *                             @OA\Property(property="credit_price", type="number", format="float", example=2),
     *                             @OA\Property(property="writing_count", type="integer", example=10),
     *                             @OA\Property(property="speaking_count", type="integer", example=5),
     *                             @OA\Property(property="status", type="boolean", example=false),
     *                             @OA\Property(property="discount", type="integer", nullable=true, example=20),
     *                             @OA\Property(property="discounted_price", type="number", format="float", example=71200)
     *                         )
     *                     ),
     *                     @OA\Property(property="message", type="string", example="Promo kod topildi.")
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=true),
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=4),
     *                         @OA\Property(property="code", type="string", example="SPRING20"),
     *                         @OA\Property(property="discount", type="integer", example=20)
     *                     ),
     *                     @OA\Property(
     *                         property="packages",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="price", type="number", format="float", example=49000),
     *                             @OA\Property(property="writing_count", type="integer", example=10),
     *                             @OA\Property(property="speaking_count", type="integer", example=5),
     *                             @OA\Property(property="status", type="boolean", example=false),
     *                             @OA\Property(property="discount", type="integer", nullable=true, example=20),
     *                             @OA\Property(property="discounted_price", type="number", format="float", example=39200)
     *                         )
     *                     ),
     *                     @OA\Property(property="message", type="string", example="Promo kod topildi.")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Promo code error",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="ok", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Bunday promo kod topilmadi."),
     *                     @OA\Property(property="code", type="string", example="PROMO_NOT_FOUND")
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="ok", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Promo kod aktiv emas."),
     *                     @OA\Property(property="code", type="string", example="PROMO_NOT_ACTIVE")
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="ok", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Bu promo kod avval qo‘llangan (faqat bir marta foydalanish mumkin)."),
     *                     @OA\Property(property="code", type="string", example="PROMO_ALREADY_USED")
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="ok", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Bu promo kod avval foydalangansiz."),
     *                     @OA\Property(property="code", type="string", example="PROMO_ALREADY_USED")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
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

        if ($request->type == 0) {
            if (! $promo->plans()->exists()) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Bunday promo kod topilmadi.',
                    'code'    => 'PROMO_NOT_FOUND',
                ], 404);
            }

            $plans = Plan::orderBy('order_id')->get();
            $globalSetting = GlobalSetting::orderByDesc('effective_at')->select('one_writing_price','one_speaking_price','profit_tax','chat_gpt_tax','usd_current_currency')->firstOrFail();
            $data=[
                'one_writing_price'=>($globalSetting->one_writing_price * $globalSetting->usd_current_currency) / 100 *  (100 + $globalSetting->profit_tax+$globalSetting->chat_gpt_tax) ,
                'one_speaking_price'=>($globalSetting->one_speaking_price * $globalSetting->usd_current_currency) / 100 *  (100 + $globalSetting->profit_tax+$globalSetting->chat_gpt_tax) ,
            ];

            $plans->map(function ($p) use ($promo, $discount,$data) {
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

                $p->setAttribute('writing_count', $data['one_writing_price'] > 0
                    ? (int) floor($p->credit_price / $data['one_writing_price'])
                    : 0);
                $p->setAttribute('speaking_count', $data['one_speaking_price'] > 0
                    ? (int) floor($p->credit_price / $data['one_speaking_price'])
                    : 0);

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
        }else{
            if (! $promo->packages()->exists()) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Bunday promo kod topilmadi.',
                    'code'    => 'PROMO_NOT_FOUND',
                ], 404);
            }

            $packages = Package::orderBy('order_id')->get();

            $packages->map(function ($p) use ($promo, $discount) {
                if ($promo && $promo->packages->contains('id', $p->id)) {
                    $p->setAttribute('discount', (int) $discount);
                    $p->setAttribute(
                        'discounted_price',
                        round(max(0, floatval($p->price) * ((100 - $discount) / 100)), 2)
                    );
                } else {
                    $p->setAttribute('discount', null);
                    $p->setAttribute('discounted_price', (float) floatval($p->price));
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
                'packages'=>CreditResource::collection($packages),
                'message' => 'Promo kod topildi.',
            ], 200);
        }

    }

    /**
     * @OA\Post(
     *     path="/v2/freePremium",
     *     operationId="postV2PremiumFreePremium",
     *     tags={"Premium API"},
     *     summary="Activate free premium via promo code",
     *     description="Activates a premium plan for the authenticated user using a promo code. The promo code can be used only once per user. If the user already has an active plan, the new plan starts right after the current one ends.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"plan_id","promocode_id"},
     *             @OA\Property(property="plan_id", type="integer", example=1, description="ID of the plan to activate"),
     *             @OA\Property(property="promocode_id", type="integer", example=4, description="ID of the promo code to apply")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Premium successfully activated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=15),
     *                 @OA\Property(property="user_id", type="integer", example=42),
     *                 @OA\Property(property="plan_id", type="integer", example=1),
     *                 @OA\Property(property="plan_name", type="string", example="1 month"),
     *                 @OA\Property(property="type", type="integer", example=0),
     *                 @OA\Property(property="type_label", type="string", nullable=true, example="Promo"),
     *                 @OA\Property(property="price", type="number", format="float", example=89000),
     *                 @OA\Property(property="discount", type="integer", example=20),
     *                 @OA\Property(property="promocode_id", type="integer", example=4),
     *                 @OA\Property(property="start_time", type="string", format="date-time", example="2026-08-11 12:00:00"),
     *                 @OA\Property(property="end_time", type="string", format="date-time", example="2026-09-10 12:00:00"),
     *                 @OA\Property(property="is_active", type="boolean", example=true)
     *             ),
     *             @OA\Property(property="message", type="string", example="Premium successfully activated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Promo code error",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="promo kod mavjud emas."),
     *                     @OA\Property(property="code", type="string", example="PROMO_NOT_FOUND")
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Bu promo kod avval foydalangansiz."),
     *                     @OA\Property(property="code", type="string", example="PROMO_ALREADY_USED")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function freePremium(Request $request)
    {
        $user = auth()->user();
        $plan = Plan::findOrFail($request->plan_id);
        $start_time = now()->format('Y-m-d H:i:s');
        $end_time = Carbon::parse($start_time)->addDays($plan->day);
        $promoCodeId=$request->promocode_id;
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
                    'success'      => false,
                    'message' => 'Bu promo kod avval foydalangansiz.',
                    'code'    => 'PROMO_ALREADY_USED',
                ], 404);
            }
        }else{
            return response()->json([
                'success'      => false,
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

        if ($plan->credit_price>0){
            $this->createBalanceEditByPlan($user,$plan,$plan->credit_price);
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


        return response()->json([
            'success'   => true,
            'data'=>new ActivePlanResource($existingPlan),
            'message'=>'Premium successfully activated.',
        ]);

    }

    /**
     * @OA\Post(
     *     path="/v2/freeCredit",
     *     operationId="postV2PremiumFreeCredit",
     *     tags={"Premium API"},
     *     summary="Add free AI credit via promo code",
     *     description="Adds AI credit (balance) to the authenticated user for a given package using a promo code. The promo code can be used only once per user. The package price is added to the user's balance and a `balance_edits` record is created for auditing.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"package_id","promocode_id"},
     *             @OA\Property(property="package_id", type="integer", example=2, description="ID of the credit package to activate"),
     *             @OA\Property(property="promocode_id", type="integer", example=4, description="ID of the promo code to apply")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Credit successfully added",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="balance", type="string", example="1 500.00", description="User's balance after adding credit, formatted with 2 decimals"),
     *                 @OA\Property(property="status", type="integer", enum={0,1}, example=1, description="1 if balance is greater than 500, otherwise 0")
     *             ),
     *             @OA\Property(property="message", type="string", example="Premium successfully activated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Promo code error or package not found",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="promo kod mavjud emas."),
     *                     @OA\Property(property="code", type="string", example="PROMO_NOT_FOUND")
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Bu promo kod avval foydalangansiz."),
     *                     @OA\Property(property="code", type="string", example="PROMO_ALREADY_USED")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function freeCredit(Request $request)
    {
        $user = auth()->user();
        $package = Package::findOrFail($request->package_id);
        $promoCodeId=$request->promocode_id;
        $globalSetting = GlobalSetting::orderByDesc('effective_at')->select('chat_gpt_tax','profit_tax','usd_current_currency')->firstOrFail();
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
                    'success'      => false,
                    'message' => 'Bu promo kod avval foydalangansiz.',
                    'code'    => 'PROMO_ALREADY_USED',
                ], 404);
            }
        }else{
            return response()->json([
                'success'      => false,
                'message' => 'promo kod mavjud emas.',
                'code'    => 'PROMO_NOT_FOUND',
            ], 404);
        }

        $today = now();

        $premium_user=BalanceEdit::create([
            'user_id' => $user->id,
            'price' => $package->price,
            'value' =>$package->credit_price,
            'current_currency'=>$globalSetting->usd_current_currency,
            'current_tax'=>$globalSetting->chat_gpt_tax+$globalSetting->profit_tax,
            'discount'=>$discount,
            'current_balance'=>$user->balance,
            'type'=>0,
            'package_id'=>$package->id,
            'promocode_id'=>$promoCodeId,
        ]);

        $this->changeUserBalanceByAdmin($user->id,$package->price);

        $user->refresh();

        return response()->json([
            'success'   => true,
            'data'=>[
                 'balance' => number_format(floatval($user->balance),2,'.',' '),
                 'status'=> floatval($user->balance) > 500 ? 1 : 0
            ],
            'message'=>'Premium successfully activated.',
        ]);

    }


    /**
     * @OA\Get(
     *     path="/v2/check_premium",
     *     operationId="getV2PremiumCheckPremium",
     *     tags={"Premium API"},
     *     summary="Check premium status",
     *     description="Checks whether the authenticated user has an active premium plan. Current controller returns a JSON object with numeric key `0` for true/false status.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="0", type="boolean", example=true),
     *                     @OA\Property(property="start_time", type="string", format="date-time", example="2026-06-01 10:00:00"),
     *                     @OA\Property(property="end_time", type="string", format="date-time", example="2026-07-01 10:00:00"),
     *                     @OA\Property(property="is_uzbekistan", type="boolean", example=true),
     *                     @OA\Property(property="plan_id", type="integer", nullable=true, example=1)
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="0", type="boolean", example=false),
     *                     @OA\Property(property="is_uzbekistan", type="boolean", example=true)
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="0", type="boolean", example=true),
     *                     @OA\Property(property="is_uzbekistan", type="boolean", example=false),
     *                     @OA\Property(property="start_time", type="string", format="date-time", example="2026-06-04 12:00:00"),
     *                     @OA\Property(property="end_time", type="string", format="date-time", example="2026-06-07 12:00:00"),
     *                     @OA\Property(property="plan_id", type="integer", nullable=true, example=null)
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/v2/credit/balance",
     *     operationId="getV2PremiumCreditBalance",
     *     tags={"Premium API"},
     *     summary="Get user credit balance",
     *     description="Returns user AI credit balance data.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="expired", type="boolean", example=false),
     *                 @OA\Property(property="balance", type="string", example="25 000")
     *             ),
     *             @OA\Property(property="message", type="string", example="user balance")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function creditBalance()
    {
        $user = auth()->user();
        $balanceData=$user->balanceData();

        return $this->success($balanceData,'user balance');
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

}
