<?php

namespace App\Http\Controllers;

use App\Models\GlobalSetting;
use App\Models\SpeakingPrompt;
use App\Models\WritingPrompt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class GlobalSettingController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('information_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $globalSetting = GlobalSetting::orderByDesc('effective_at')->firstOrFail();

        return view('admin.globalSetting.index', compact('globalSetting'));
    }

    public function gpt_edit()
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $globalSetting = GlobalSetting::orderByDesc('effective_at')->firstOrFail();

        return view('admin.globalSetting.edit', compact('globalSetting'));
    }
    public function finance_edit()
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $globalSetting = GlobalSetting::orderByDesc('effective_at')->firstOrFail();


        return view('admin.globalSetting.finance_edit', compact('globalSetting'));
    }
    public function extra_edit()
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $globalSetting = GlobalSetting::orderByDesc('effective_at')->firstOrFail();


        return view('admin.globalSetting.extra_edit', compact('globalSetting'));
    }

    public function gpt_update(Request $request, )
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate($request,[
            'openai_chat_url'=>'required|min:3',
            'openai_model'=>'required|min:3',
            'openai_max_tokens'=>'required|integer',
            'openai_key'=>'required|min:3',
            'cloud_vision_key'=>'required|min:3',
        ]);

        $globalSetting = GlobalSetting::orderByDesc('effective_at')->firstOrFail();

       $globalSetting->openai_chat_url = $request->openai_chat_url;
       $globalSetting->openai_model = $request->openai_model;
       $globalSetting->openai_max_tokens = $request->openai_max_tokens;
       $globalSetting->openai_key = $request->openai_key;
       $globalSetting->cloud_vision_key = $request->cloud_vision_key;
       $globalSetting->save();


        return redirect()->route('admin.globalSettings.index');
    }
    public function finance_update(Request $request, )
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate($request,[
            'gpt_price_per_input_token'=>'required|min:3',
            'gpt_price_per_output_token'=>'required|min:3',
            'min_ai_credit_price'=>'required|min:3',
            'usd_current_currency'=>'required|min:3',
            'chat_gpt_tax'=>'required|integer|min:1|max:100',
            'profit_tax'=>'required|integer|min:1|max:100',
            'one_writing_price'=>'required',
            'one_speaking_price'=>'required',
        ]);
        $globalSetting = GlobalSetting::orderByDesc('effective_at')->firstOrFail();

        $globalSetting->gpt_price_per_input_token = $request->gpt_price_per_input_token;
        $globalSetting->gpt_price_per_output_token = $request->gpt_price_per_output_token;
        $globalSetting->min_ai_credit_price = $request->min_ai_credit_price;
        $globalSetting->usd_current_currency = $request->usd_current_currency;
        $globalSetting->chat_gpt_tax = $request->chat_gpt_tax;
        $globalSetting->profit_tax = $request->profit_tax;
        $globalSetting->one_writing_price = $request->one_writing_price;
        $globalSetting->one_speaking_price = $request->one_speaking_price;
        $globalSetting->save();


        return redirect()->route('admin.globalSettings.index');
    }
    public function extra_update(Request $request, )
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate($request,[
            'referral_profit_percentage'=>'required|min:0|max:100',
            'admin_contact'=>'required|min:3',
            'start_referral_date'=>'required|date',
        ]);
        $globalSetting = GlobalSetting::orderByDesc('effective_at')->firstOrFail();

        $globalSetting->referral_profit_percentage = $request->referral_profit_percentage;
        $globalSetting->admin_contact = $request->admin_contact;
        $globalSetting->start_referral_date = Carbon::parse($request->start_referral_date)->format('Y-m-d');
        $globalSetting->save();


        return redirect()->route('admin.globalSettings.index');
    }

    public function login_bot_update(Request $request)
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'login_bot_token' => 'required|string',
        ]);

        $globalSetting = GlobalSetting::orderByDesc('effective_at')->firstOrFail();
        $token = trim($request->login_bot_token);

        $globalSetting->update([
            'login_bot_token' => $token,
        ]);

        $webhookUrl = str_replace('http://', 'https://', route('botwebhook.confirm'));
//        $webhookUrl = route('botwebhook.confirm');

        try {
            Http::retry(2, 1000, throw: false)
                ->timeout(15)
                ->post("https://api.telegram.org/bot{$token}/deleteWebhook");
        } catch (Throwable $e) {
            Log::warning('Telegram deleteWebhook failed', [
                'message' => $e->getMessage(),
            ]);
        }

        try {
            $webhookResponse = Http::retry(2, 1000, throw: false)
                ->timeout(20)
                ->post("https://api.telegram.org/bot{$token}/setWebhook", [
                    'url' => $webhookUrl,
                ]);
        } catch (Throwable $e) {
            return redirect()
                ->route('admin.globalSettings.index')
                ->withErrors(['login_bot_token' => "Telegram bilan ulanishda xatolik yuz berdi: {$e->getMessage()}"]);
        }

        if ($webhookResponse->successful() && $webhookResponse->json('ok')) {
            return redirect()
                ->route('admin.globalSettings.index')
                ->with('message', 'Telegram Bot Tokeni saqlandi va webhook sozlandi.');
        }

        $message = $webhookResponse->json('description') ?? 'unknown error';

        return redirect()
            ->route('admin.globalSettings.index')
            ->withErrors(['login_bot_token' => "Telegram Bot Tokeni saqlandi, lekin webhookni sozlashda xatolik yuz berdi: {$message}"]);
    }
}
