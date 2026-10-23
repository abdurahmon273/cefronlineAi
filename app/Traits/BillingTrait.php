<?php

namespace App\Traits;

use App\Models\BalanceEdit;
use App\Models\GlobalSetting;
use App\Models\SpeakingResult;
use App\Models\User;
use App\Models\WritingResult;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait BillingTrait
{


    public function FindDeepGramTextToAudioPrice($text)
    {
        $charCount = mb_strlen($text, 'UTF-8');

        $globalSetting = GlobalSetting::orderByDesc('effective_at')->firstOrFail();

        $pricePerChar = (float) $globalSetting->deepgram_price_per_token;

        $totalPrice = round($charCount * $pricePerChar, 6);

        return $totalPrice;
    }


    public function FindDeepGramAudioToTextPrice(float $durationSeconds): float
    {
        $pricePerMinute = (float)($globalSetting?->deepgram_price_per_minut ?? 0.0043);
        $durationMin    = $durationSeconds / 60.0;

        return round($durationMin * $pricePerMinute, 6);
    }

    public function changeUserBalance($id,$usd_price)
    {
        $user=User::find($id);
        $currentBalance=floatval($user->balance);

        $price=$this->calculateProfitPrice($usd_price);

        $user->update([
            'balance'=>$currentBalance-$price
        ]);

        return $price;
    }
    public function changeUserBalanceByAdmin($id,$price)
    {
        $user=User::find($id);
        $currentBalance=floatval($user->balance);

        $user->update([
            'balance'=>$currentBalance+$price
        ]);

        return $price;
    }

    public function getUserCreditStatus($with_ai,$balance)
    {
        if (!$with_ai) {return $with_ai ;}

        $newWithAi=1;
        $globalSetting=GlobalSetting::orderByDesc('effective_at')->select('min_ai_credit_price','chat_gpt_tax','profit_tax','usd_current_currency')->firstOrFail();
        $minPrice=floatval(($globalSetting->min_ai_credit_price * ($globalSetting->usd_current_currency) / 100 *( ( $globalSetting->chat_gpt_tax + $globalSetting->profit_tax ) + 100 )) );

        if (floatval($balance) < floatval($minPrice)) {
            $newWithAi=0;
        }

        return $newWithAi;
    }

    public function getAlertData($status,$errorText,$successText)
    {
        if ($status==1) {
            return ['type'=>'success','text'=>$successText];
        }else{
            return ['type'=>'error','text'=>$errorText];
        }
    }

    public function calculateProfitPrice($usd_price)
    {
        $globalSetting = GlobalSetting::orderByDesc('effective_at')->select('chat_gpt_tax','profit_tax','usd_current_currency')->firstOrFail();

        $all_tax=intval($globalSetting->chat_gpt_tax + $globalSetting->profit_tax);
        $current_currency=floatval($globalSetting->usd_current_currency) ;

        $price=floatval(($usd_price * ($current_currency) / 100 *( $all_tax + 100 )) );

        return $price;
    }
    public function calculateAiCheckPrice(array $usage): array
    {
        $globalSetting = GlobalSetting::orderByDesc('effective_at')->select('chat_gpt_tax','profit_tax','usd_current_currency','gpt_price_per_input_token','gpt_price_per_output_token')->firstOrFail();

        if (empty($usage)) {
            return 0.0;
        }

        $inputTokens = (int) ($usage['input'] ?? 0);
        $outputTokens = (int) ($usage['output'] ?? 0);



        $inputPricePerToken = (float) $globalSetting->gpt_price_per_input_token;
        $outputPricePerToken = (float) $globalSetting->gpt_price_per_output_token;

        $cost = ($inputTokens * $inputPricePerToken) + ($outputTokens * $outputPricePerToken);



        $all_tax=intval($globalSetting->chat_gpt_tax + $globalSetting->profit_tax);
        $current_currency=floatval($globalSetting->usd_current_currency) ;

        $price=floatval(($cost * ($current_currency) / 100 *( $all_tax + 100 )) );

//        Log::channel('telegram')->error(
//            "\n\n Checkking, Ai\n".
//            "\n input: ".$inputTokens.
//            "\n output: ".$outputTokens.
//            "\n price: ".$cost.
//            "\n uzs price: ".$price
//        );

        return ['usd_price'=>round($cost, 8),'uzs_price'=>$price];
    }


    public function getBalancePersentageData($allBalance, $user)
    {
        if ($allBalance <= 0) {
            return [
                'success' => false,
                'exists' => 0,
                'used' => 0,
            ];
        }

        $writingAllPrice = WritingResult::where('user_id', $user->id)
            ->sum('uzs_ai_check_price');

        $speakingAllPrice = SpeakingResult::where('user_id', $user->id)
            ->sum('uzs_ai_check_price');

        $used = $writingAllPrice + $speakingAllPrice;

        $exists = $allBalance - $used;

        if ($exists < 0) {
            $exists = 0;
        }

        $usedPercentage = ($used / $allBalance) * 100;
        $existsPercentage = 100 - $usedPercentage;

        return [
            'success' => true,
            'exists' => round($existsPercentage, 2),
            'used' => round($usedPercentage, 2),
        ];
    }


    public function createBalanceEditByPlan($user,$plan,$price)
    {
        $user=User::find($user->id);
        $currentBalance=floatval($user->balance);

        $globalSetting = GlobalSetting::orderByDesc('effective_at')->select('chat_gpt_tax','profit_tax','usd_current_currency')->firstOrFail();

        $all_tax=intval($globalSetting->chat_gpt_tax + $globalSetting->profit_tax);
        $current_currency=floatval($globalSetting->usd_current_currency) ;

        $usd_price=floatval(($price / ($current_currency) * 100 / ( $all_tax + 100 )) );

        $premium_user=BalanceEdit::create([
            'user_id' => $user->id,
            'price' => $price,
            'value' =>$usd_price,
            'current_currency'=>$globalSetting->usd_current_currency,
            'current_tax'=>$all_tax,
            'discount'=>0,
            'current_balance'=>$user->balance,
            'type'=>2,
            'admin_id' => auth()->user()->id,
            'package_id'=>null,
//            'plan_id'=>$plan->id,
        ]);

        $user->update([
            'balance'=>$currentBalance+$price
        ]);

        return $price;
    }
}
