<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotNumberRequest;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\Jobs\SendEmailVerification;
use App\Jobs\SendSmsJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UserLoginController extends Controller
{

    private function normalizePhone($input)
    {
        $mobile = preg_replace('/[^0-9+]/', '', $input);
        if (!preg_match('/^\+998/', $mobile)) {
            $mobile = ltrim($mobile, '+0');
            $mobile = '+998' . $mobile;
        }
        return $mobile;
    }

    public function showLoginForm()
    {
        if (!auth()->check()) {
            return view('user.auth.login');
        } else {
            return redirect()->route('user.dashboard');
        }
    }

    public function showForgotForm()
    {
        return view('user.auth.forgot');
    }

    public function forgotSubmit(ForgotNumberRequest $request)
    {

        $mobile = $this->normalizePhone($request->mobile_number);

        if ($request->type){

            $user = User::where('mobil_number', $mobile)->first();
        }else{

            $user = User::where('email', $request->email)->first();
        }

        if (!$user) {
            return redirect()->back()->withErrors(['mobile_number_or_email' => 'User not found'])->withInput();
        }

        if ( $request->type && $user->mobile_verified_at === null) {
            return redirect()->back()->withErrors(['mobile_number_or_email' => 'Phone has not been verified'])->withInput();
        }

        if ( !$request->type && $user->email_verified_at === null) {
            return redirect()->back()->withErrors(['mobile_number_or_email' => 'Email has not been verified'])->withInput();
        }

        try {
            $code = rand(1000, 9999);
            $message = "Kodni hech kimga bermang! TM school ilovasiga kirish uchun tasdiqlash kodi: $code";

            if ($request->type){

                $phoneNumber = str_replace('+', '', $mobile);
                SendSmsJob::dispatch($phoneNumber, $message)->onQueue('sms');

                $user->update([ 'mobile_verify_code' => $code ]);

                session(['confirm_phone' => $mobile, 'password_reset' => true]);
            }else{

                $email=$request->email;
                SendEmailVerification::dispatch($email,$code)->onQueue('email');

                $user->update([ 'email_verify_code' => $code ]);

                session(['confirm_email' => $email, 'password_reset' => true]);
            }



            return redirect()->route('confirm');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['mobil_number' => 'Mobile Number notogri'])->withInput();
        }
    }

    public function showRegisterForm($id = null)
    {
        return view('user.auth.register', ['id' => $id]);
    }


    public function login(UserLoginRequest $request)
    {
        // request ichida phone allaqachon: 998xxxxxxxxx
        if ($request->type){
            $user = User::where('mobil_number', $request->mobile_number)->first();
            if($user->mobile_verified_at === null) return back()->withErrors(['mobile_number' => 'User not verified, please register again']);

        }else{

            $user = User::where('email', $request->email)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'mobile_number' => 'Telefon yoki parol noto‘g‘ri',
            ])->withInput($request->only('mobile_number'));
        }

        if ($user->is_admin ?? false) {
            return back()->withErrors([
                'mobile_number' => 'Admin bu login orqali kira olmaydi',
            ])->withInput($request->only('mobile_number'));
        }

        Auth::login($user);

        $sessionId = session()->getId();
        $user->update([ 'session_id' => $sessionId ]);

        return redirect()->route('user.dashboard');
    }

    public function register(UserRegisterRequest $request){

        if ($request->type){
            $user = User::query()
                ->where('mobil_number', $request->mobile_number)
                ->first();
            if ($user && $user->mobile_verified_at !== null) return redirect()->back()->withErrors(['mobile_number_or_email' => "User already exists"])->withInput();

        }else{
            $user = User::query()
                ->where('email', $request->email)
                ->first();
            if ($user && $user->email_verified_at !== null) return redirect()->back()->withErrors(['mobile_number_or_email' => "User already exists"])->withInput();
        }

        try {
            if ($request->type){
                $code = rand(1000, 9999);
                $phoneNumber = str_replace('+', '', $request->mobile_number);
                $message = "Kodni hech kimga bermang! TM school ilovasiga kirish uchun tasdiqlash kodi: $code";
                SendSmsJob::dispatch($phoneNumber, $message)->onQueue('sms');

                $user = User::updateOrCreate(
                    ['mobil_number' => $request->mobile_number],
                    [
                        'name' => $request->name,
                        'mobile_verify_code' => $code,
                        'password' => Hash::make($request->password),
                        'created_by_id'=>$request->user_id ?? 1,
                    ]
                );

                $user->roles()->sync(2);

                session(['confirm_phone' => $request->mobile_number]);

            }else{
                $code = rand(1000, 9999);
                $email = $request->email;
                $message = "Kodni hech kimga bermang! TM school ilovasiga kirish uchun tasdiqlash kodi: $code";
                SendEmailVerification::dispatch($email, $code)->onQueue('email');

                $user = User::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'name' => $request->name,
                        'email_verify_code' => $code,
                        'password' => Hash::make($request->password),
                        'created_by_id'=>$request->user_id ?? 1,
                    ]
                );

                $user->roles()->sync(2);

                session(['confirm_email' => $request->email]);
            }



            return redirect()->route('confirm');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['mobil_number' => 'login yoki parolxato!!'])->withInput();
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }


    public function confirm()
    {
        $phone = session('confirm_phone');
        $email = session('confirm_email');

        if (!$phone && !$email) {
            return redirect()->route('login')->withErrors(['mobile_number_or_email' => 'No phone number or email provided']);
        }

        if ($phone){

        $phone = $this->normalizePhone($phone);
        $maskedPhone = substr($phone, 0, 6 ) . ' ***** ' . substr($phone, -2);
            $data=[
                'type'=>1,
                'maskedPhone'=>$maskedPhone,
                'maskedEmail'=>'',
                'new'=>0
            ];
        }elseif ($email){
            $data=[
                'type'=>0,
                'maskedPhone'=>'',
                'maskedEmail'=>$email,
                'new'=>0
            ];
        }

        return view('user.auth.confirm', $data );
    }

    public function confirmCheck(Request $request){
        $phone = session('confirm_phone');
        $email = session('confirm_email');

        if ($phone){
            $user = User::where("mobil_number", $phone)->first();
            $validator = Validator::make($request->all(), [
                'sms_code' => [
                    'required',
                    'size:4',
                    function ($attribute, $value, $fail) use ($user) {
                        if (!$user || $user->mobile_verify_code !== $value) {
                            $fail('Verification code is incorrect!');
                        }
                    },
                ],
            ]);

            if ($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();

            if ($user->code_expired_at && $user->code_expired_at->isPast()) return redirect()->back()->withErrors(['sms_code' => 'Tasdiqlash kodi muddati tugagan'])->withInput();

            $user->update([
                'mobile_verified_at' => Carbon::now()
            ]);


        }elseif ($email){
            $user = User::where("email", $email)->first();

            $validator = Validator::make($request->all(), [
                'sms_code' => [
                    'required',
                    'size:4',
                    function ($attribute, $value, $fail) use ($user) {
                        if (!$user || $user->email_verify_code !== $value) {
                            $fail('Verification code is incorrect!');
                        }
                    },
                ],
            ]);

            if ($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();


            $user->update([
                'mobile_verified_at' => Carbon::now()
            ]);


        }

        $resetFlow = session()->pull('password_reset', false);
        if ($resetFlow) {
            return redirect()->route('password.reset.form');
        }

        Auth::login($user);

        $sessionId = session()->getId();
        $user->update([ 'session_id' => $sessionId ]);

        session()->forget('confirm_phone');
        session()->forget('confirm_email');

        return redirect()->route('user.dashboard');
    }

    public function showResetForm()
    {
        $phone = session('confirm_phone');
        $email = session('confirm_email');
        if (!$phone && !$email) {
            return redirect()->route('login');
        }

        if ($phone){

            $phone = $this->normalizePhone($phone);
            $maskedPhone = substr($phone, 0, 6 ) . ' ***** ' . substr($phone, -2);
            $data=[
                'type'=>1,
                'maskedPhone'=>$maskedPhone,
                'maskedEmail'=>'',
                'new'=>0
            ];
        }elseif ($email){

            $data=[
                'type'=>0,
                'maskedPhone'=>'',
                'maskedEmail'=>$email,
                'new'=>0
            ];
        }


        return view('user.auth.reset-password', $data );
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $phone = session('confirm_phone');
        $email = session('confirm_email');

        if ($phone){

            $user = User::where('mobil_number', $phone)->first();
        }elseif ($email){

            $user = User::where('email', $email)->first();
        }else{
            return redirect()->route('login');
        }

        if (!$user) {
            return redirect()->route('login');
        }

//        dd($request->all(), $user,$phone,$email);
        $user->password = Hash::make($request->password);
        $user->save();

        Auth::login($user);

        $sessionId = session()->getId();
        $user->update([ 'session_id' => $sessionId ]);

        session()->forget('confirm_phone');
        session()->forget('confirm_email');


        return redirect()->route('user.dashboard');
    }

    public function resendVerificationCode(Request $request)
    {
        $phone = session('confirm_phone');
        if(!$phone) return response()->json(['error' => 'No phone in session'], 400);
        $phone = $this->normalizePhone($phone);

        $lastResend = session('last_code_resend');
        if ($lastResend && now()->diffInSeconds($lastResend) < 60) {
            return response()->json(['error' => 'Please wait 1 minute before resending code'], 429);
        }

        $user = User::where('mobil_number', $phone)->first();
        if(!$user) return response()->json(['error' => 'User not found'], 400);

        $verificationCode = rand(1000, 9999);
        $phoneNumber = str_replace('+', '', $phone);

        try {
            $user->update([
                'mobile_verify_code' => $verificationCode,
            ]);

            $message = "Kodni hech kimga bermang! TM school ilovasiga kirish uchun tasdiqlash kodi: $verificationCode";
            SendSmsJob::dispatch($phoneNumber, $message)->onQueue('sms');


            session(['last_code_resend' => now()]);
            return response()->json(['success' => 'Code resent']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
