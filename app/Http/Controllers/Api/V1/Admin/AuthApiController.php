<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PermissionResource;
use App\Http\Resources\Admin\UserResource;
use App\Jobs\SendSmsJob;
use App\Models\City;
use App\Models\PremiumToUsers;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\SendingSms;

class AuthApiController extends Controller
{
    use SendingSms;

    public function register(Request $request)
    {


        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'mobil_number' => 'required|numeric|regex:/^\+?998\d{9}$/',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Ism kiritilmadi',
            'mobil_number.required' => 'Telefon raqam kiritilmadi',
            'mobil_number.regex' => 'Telefon raqam qiymati 12 tadan kam bolmasligi kerak ',
            'mobil_number.numeric' => 'Telefon raqam raqamda bo\'lishi kerak',
            'password.required' => 'Parol kiritilmadi',
            'password.digits' => "Parol 6 tadan ko'p bo'lishi kerak",
            'password.confirmed' => "Parol bir hil bo'lishi kerak",
            'birth_date.required' => 'Tu\'g\'ulgan kun sanasi kiritilmadi',
            'gender.required' => 'Jins tanlanishi shart',
            'city_id.required' => 'Shaxar tanlanishi shart',
            'city_id.exists' => 'Shaxar mavjud emas',
            'surname.required' => 'Familya kiritilmadi',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'errors'=>$validation->errors()
            ]);

        }
        $code = rand(1000, 9999);

        $data = $request->all();
        $data['mobil_number'] = $this->normalizePhoneNumber($data['mobil_number']);

        if (isset($data['image'])) {
            $file = $data['image'];
            $imageName = uniqid() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/userImage'), $imageName);
            $imagePath = 'https://teacher-muzaffar.com/storage/userImage/' . $imageName;
            $data['image'] = $imagePath;
        }
        $data['mobile_verify_code'] = $code;
        $user = User::with('city')->where('mobil_number', $data['mobil_number'])->exists();
        if (!$user) {


            $user2 = User::create([
                'name' => $data['name'],
                'surname' => $data['surname'],
                'mobil_number' => $data['mobil_number'],
                'password' => bcrypt($data['password']),
                'birth_date' => isset($data['birth_date']) ? $data['birth_date'] : null,
                'gender' => isset($data['gender']) ? (int)$data['gender'] : null,
                'city_id' => isset($data['city_id']) ? (int)$data['city_id'] : null,
                'mobile_verify_code' => $data['mobile_verify_code'],
                'image' => isset($data['image']) ? $data['image'] : null,
            ]);
            $user2->roles()->sync(2);
        } else {
            $user = User::with('city')->where('mobil_number', $data['mobil_number'])->first();
            if ($user->mobile_verified_at !== null) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'mobil_number' => ['Telefon raqam bor'],
                    ]
                ]);
            } else {
                if ($user->mobile_verified_at == null) {
                    $user->update([
                        'name' => $data['name'],
                        'surname' => $data['surname'],
                        'mobil_number' => $data['mobil_number'],
                        'password' => $data['password'],
                        'birth_date' => isset($data['birth_date']) ? $data['birth_date'] : null,
                        'gender' => isset($data['gender']) ? (int)$data['gender'] : null,
                        'city_id' => isset($data['city_id']) ? (int)$data['city_id'] : null,
                        'mobile_verify_code' => $data['mobile_verify_code'],
                        'image' => isset($data['image']) ? $data['image'] : null,
                    ]);
                    $user->roles()->sync(2);

                }
            }
        }

        $int_var = preg_replace('/[^0-9]/', '', $data['mobil_number']);
        $message = "Kodni hech kimga bermang! TM school ilovasiga kirish uchun tasdiqlash kodi: $code";
        SendSmsJob::dispatch($int_var, $message)->onQueue('sms');

        $data['city_id'] = isset($data['city_id']) ? (int)$data['city_id'] : null;
        $data['gender'] = isset($data['gender']) ? (int)$data['gender'] : null;
        unset($data['password']);
        unset($data['password_confirmation']);
//        unset($data['mobile_verify_code']);
        return response()->json([
            'success' => true,
            'message' => "Sms jo'natildi",
            "data" => $data,
        ]);
    }

    private function normalizePhoneNumber($phoneNumber)
    {
        // Faqat raqamlarni olib qolamiz
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Agar uzunligi 12 bo'lsa va 998 bilan boshlansa
        if (strlen($cleaned) === 12 && substr($cleaned, 0, 3) === '998') {
            return '+' . $cleaned;  // +998123456789
        }

        // Agar uzunligi 9 bo‘lsa (faqat mahalliy raqam berilgan bo‘lsa)
        if (strlen($cleaned) === 9) {
            return '+998' . $cleaned;  // +998123456789
        }

        // Har ehtimolga qarshi fallback
        if (strlen($cleaned) === 12) {
            return '+' . $cleaned;
        }

        return $phoneNumber;  // O'zgarmasdan qaytaramiz, noto'g'ri format bo'lsa
    }

    public function verify_sms(Request $request)
    {
        $this->validate($request, [
            'mobil_number'=>'required',
            'mobile_verify_code'=>'required',
        ],[
            'mobil_number.required' => 'Telefon raqam kiritilmadi',
            'mobile_verify_code.required' => 'code kiritilmadi',
        ]);

        $user = User::where('mobil_number', $request->mobil_number)->first();
        if ($user && $request->mobile_verify_code == $user['mobile_verify_code'] && $request->mobil_number == $user['mobil_number']) {
            $user->update([
                'mobile_verified_at' => Carbon::now(),
                'device_id' => $request->device_id ?? null,
            ]);
            Auth::login($user);
            $authToken = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'success' => true,
                "message" => "User ro'yhatdan muvaffaqiyatli o'tdi",
                'auth_token' => $authToken,
                'data' => new UserResource($user),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'errors' => 'Kod xato kiritildi',
            ]);
        }
    }

    public function login(Request $request)
    {


        $validation = Validator::make($request->all(), [
            'mobil_number' => 'required|numeric|regex:/^\+\d{12}$/',
            'password' => 'required|string|min:6',
        ], [
            'mobil_number.required' => 'Telefon raqam kiritilmadi',
            'mobil_number.regex' => 'Telefon raqam 12tadan kam bolmasligi kerak',
            'mobil_number.numeric' => 'Telefon raqam raqamda bo\'lishi kerak',
            'password.required' => 'Parol kiritilmadi',
            'password.digits' => "Parol 6 tadan ko'p bo'lishi kerak",
            'password.confirmed' => "Parol bir hil bo'lishi kerak",
        ]);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validation->errors(),
            ]);
        }
        $user = User::where('mobil_number', $request->mobil_number)->first();
        if ($user && $user->mobile_verified_at !== null) {
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user);
                $authToken = $user->createToken('auth_token')->plainTextToken;

                $cacheKey = 'user_permissions_' . $user->id;
                Cache::forget($cacheKey);

                return response()->json([
                    'success' => true,
                    'messages' => 'Foydalanuvchi logindan muvaffaqiyatli o\'tdi',
                    'token' => $authToken,
                    'data' => new UserResource($user),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'errors' => 'Parol xato kiritildi'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'errors' => 'Foydalanuvchi topilmadi'
            ]);
        }
    }
    public function login_v2(Request $request)
    {


        $validation = Validator::make($request->all(), [
            'mobil_number' => 'required|numeric|regex:/^\+\d{12}$/',
            'password' => 'required|string|min:6',
        ], [
            'mobil_number.required' => 'Telefon raqam kiritilmadi',
            'mobil_number.regex' => 'Telefon raqam 12tadan kam bolmasligi kerak',
            'mobil_number.numeric' => 'Telefon raqam raqamda bo\'lishi kerak',
            'password.required' => 'Parol kiritilmadi',
            'password.digits' => "Parol 6 tadan ko'p bo'lishi kerak",
            'password.confirmed' => "Parol bir hil bo'lishi kerak",
        ]);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validation->errors(),
            ]);
        }
        $user = User::where('mobil_number', $request->mobil_number)->first();
        if ($user && $user->mobile_verified_at !== null) {
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user);
                $authToken = $user->createToken('auth_token')->plainTextToken;

                $user->update(['device_id'=>$request->device_id]);

                $cacheKey = 'user_permissions_' . $user->id;
                Cache::forget($cacheKey);
                $today = now();

                $existingPlan = PremiumToUsers::where('user_id', $user->id)
                    ->where(function ($query) use ($today) {
                        $query->where('start_time', '<=', $today)
                            ->where('end_time', '>=', $today);
                    })
                    ->orderByDesc('id')
                    ->first();

                return response()->json([
                    'success' => true,
                    'messages' => 'Foydalanuvchi logindan muvaffaqiyatli o\'tdi',
                    'token' => $authToken,
                    'premium'=>$existingPlan ? 1 : 0,
                    'data' => new UserResource($user),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'errors' => 'Parol xato kiritildi'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'errors' => 'Foydalanuvchi topilmadi'
            ]);
        }
    }

    public function check_device_id(Request $request)
    {
        $this->validate($request,[
            'device_id' => 'required'
        ]);

        if (\auth()->user()->device_id== $request->device_id) {

            return response()->json(['success' => true]);
        }else{
            return response()->json(['success' => false]);
        }
    }
    public function forgotpassword(Request $request)
    {
        $validate = Validator::make($request->all(), ['mobil_number' => 'required|numeric|regex:/^\+\d{12}$/'],
            ['mobil_number.required' => 'Telefon raqam kiritilmadi!',
                'mobil_number.number' => 'Telefon raqam raqamda bolishi kerak!',
                'mobil_number.regex' => "Telefon raqam 12 tadan kam bolmasligi kerak!"]);
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validate->errors(),
            ]);
        }
        $user = User::where('mobil_number', $request->mobil_number)->first();
        if ($user) {
            $code = rand(1000, 9999);
            $user->update(['mobile_verify_code' => $code]);
            $int_var = preg_replace('/[^0-9]/', '', $request->mobil_number);
            $message = "Kodni hech kimga bermang! TM school ilovasiga kirish uchun tasdiqlash kodi: $code";
            SendSmsJob::dispatch($int_var, $message)->onQueue('sms');
            return response()->json([
                'success' => true,
                'data' => [
                    'code' => $code,
                    'message'=>"Userga sms jonatildi!"
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'errors' => 'Bunday foydalanuvchi mavjud emas!',
            ]);
        }
    }

    public function verify_password(Request $request)
    {
        $validate=Validator::make($request->all(),['mobil_number' => 'required|numeric|regex:/^\+\d{12}$/',
            'mobile_verify_code' => "required"], ['mobil_number.required' => 'Telefon raqam kiritilmadi!',
            'mobil_number.number' => 'Telefon raqam raqamda bolishi kerak!', 'mobil_number.regex' => "Telefon raqam 12 tadan kam bolmasligi kerak!",
            'mobile_verify_code.required' => 'mobile_verify_code kiritilmadi!',]);
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validate->errors(),
            ]);
        }
        $user = User::where('mobil_number', $request->mobil_number)->first();
        if ($user && $request->mobile_verify_code == $user['mobile_verify_code'] && $request->mobil_number == $user['mobil_number']) {
            $user->update([
                'mobile_verified_at' => Carbon::now(),
            ]);
            return response()->json([
                'success' => true,
                'data' => "Parolni ozgartirish mumkin"
            ]);
        } else {
            return response()->json([
                'success' => false,
                'errors' => 'Kod xato kiritildi',
            ]);
        }
    }

    public function resetPassword(Request $request)
    {
        $validate=Validator::make($request->all(),['mobil_number' => 'required|numeric|regex:/^\+\d{12}$/',
            'password' => "required", 'password' => 'min:6'],
            ['mobil_number.required' => 'Telefon raqam kiritilmadi!',
                'mobil_number.number' => 'Telefon raqam raqamda bolishi kerak!',
                'mobil_number.regex' => "Telefon raqam 12 tadan kam bolmasligi kerak!",
                'mobile_verify_code.required' => 'mobile_verify_code kiritilmadi!',
                'password.min' => "Parol 6 tadan kam bolmasligi kerak"]);
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validate->errors(),
            ]);
        }
        $user = User::where('mobil_number', $request->mobil_number)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
            Auth::login($user);
            $authToken = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => "Parolni muvaffaqiyatli o'zgartirildi",
                'auth_token' => $authToken,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'errors' => 'Bunday foydalanuvchi mavjud emas!',
            ]);
        }
    }
}

