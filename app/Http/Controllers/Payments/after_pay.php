<?php

use App\Models\BalanceEdit;
use App\Models\GlobalSetting;
use App\Models\Package;
use App\Models\Plan;
use App\Models\PremiumToUsers;
use App\Models\Promocode;
use App\Models\User;
use Carbon\Carbon;


if ($transaction->transactionable_type == "App\Models\User") {

    info('test',[$transaction->amount == $transaction->tmp_payment_price,[$transaction->amount ,$transaction->tmp_payment_price]]);
    if ($transaction->amount == $transaction->tmp_payment_price) {


        if ($transaction->tmp_payment_type==1){
            $user = User::find($transaction->transactionable_id);
            $package = Package::find($transaction->tmp_payment_slug);
            $globalSetting = GlobalSetting::orderByDesc('effective_at')->select('chat_gpt_tax','profit_tax','usd_current_currency')->firstOrFail();
            $promoCodeId=null;
            $discount=0;

            if ($transaction->tmp_payment_promocode) {
                $promoCode = PromoCode::find($transaction->tmp_payment_promocode);
                if ($promoCode) {
                    $promoCodeId = $promoCode->id;
                    $discount = $promoCode->discount;
                }
            }

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

            $currentBalance=floatval($user->balance);

            $user->update([
                'balance'=>$currentBalance+$package->price
            ]);

        }else{
            $user = User::find($transaction->transactionable_id);
            $plan = Plan::find($transaction->tmp_payment_slug);
            $start_time = now()->format('Y-m-d H:i:s');
            $end_time = Carbon::parse($start_time)->addDays($plan->day);
            $promoCodeId=null;
            $discount=0;
            $type=$transaction->tmp_payment_type == 2 ? '2' : '0';

            if ($transaction->tmp_payment_promocode) {
                $promoCode = PromoCode::find($transaction->tmp_payment_promocode);
                if ($promoCode) {
                    $promoCodeId = $promoCode->id;
                    $discount = $promoCode->discount;
                }
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

            if ( $plan->credit_price > 0 ){

                $currentBalance=floatval($user->balance);

                $globalSetting = GlobalSetting::orderByDesc('effective_at')->select('chat_gpt_tax','profit_tax','usd_current_currency')->firstOrFail();

                $all_tax=intval($globalSetting->chat_gpt_tax + $globalSetting->profit_tax);
                $current_currency=floatval($globalSetting->usd_current_currency) ;

                $usd_price=floatval(($plan->credit_price / ($current_currency) * 100 / ( $all_tax + 100 )) );

                $premium_user=BalanceEdit::create([
                    'user_id' => $user->id,
                    'price' => $plan->credit_price,
                    'value' =>$usd_price,
                    'current_currency'=>$globalSetting->usd_current_currency,
                    'current_tax'=>$all_tax,
                    'discount'=>0,
                    'current_balance'=>$user->balance,
                    'type'=>2,
                    'package_id'=>null,
                ]);

                $user->update([
                    'balance'=>$currentBalance+$plan->credit_price
                ]);
            }

            PremiumToUsers::create([
                'user_id' => $user->id,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'type' => $type,
                'price' => $plan->price,
                'admin_id' => null,
                'plan_id' => $plan->id,
                'discount' => $discount,
                'promocode_id' => $promoCodeId,
            ]);
        }


    }
}
