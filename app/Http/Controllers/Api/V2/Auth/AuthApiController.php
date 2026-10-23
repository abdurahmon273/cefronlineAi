<?php

namespace App\Http\Controllers\Api\V2\Auth;

use App\Http\Controllers\Api\V2ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiLoginRequest;
use App\Http\Requests\Auth\UserRegisterApiRequest;
use App\Http\Resources\Admin\PermissionResource;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\Premium\ActivePlanResource;
use App\Jobs\SendEmailVerification;
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
use Laravel\Socialite\Facades\Socialite;
use OpenApi\Annotations as OA;
use Stevebauman\Location\Facades\Location;

class AuthApiController extends V2ApiController
{
    /**
     * @OA\Tag(
     *     name="Auth",
     *     description="Authentication endpoints"
     * )
     */
    use SendingSms;

    /**
     * @OA\Post(
     *     path="/v2/register",
     *     summary="Register a new user by phone number or email",
     *     description="Accepts a single `mobile_number_or_email` field. If the value looks like an email address, the request is treated as email registration. Otherwise it is normalized and processed as a Uzbekistan phone number.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name","surname","mobile_number_or_email","password","password_confirmation"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     maxLength=255,
     *                     description="User first name.",
     *                     example="Ali"
     *                 ),
     *                 @OA\Property(
     *                     property="surname",
     *                     type="string",
     *                     maxLength=255,
     *                     description="User last name.",
     *                     example="Valiyev"
     *                 ),
     *                 @OA\Property(
     *                     property="mobile_number_or_email",
     *                     type="string",
     *                     description="Phone number or email used for registration. Accepted phone examples: `+998901234567` or `901234567`. Accepted email example: `ali@example.com`.",
     *                     example="+998901234567"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     format="password",
     *                     minLength=6,
     *                     description="User password. Must be at least 6 characters.",
     *                     example="secret123"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     format="password",
     *                     description="Repeat the same password for confirmation.",
     *                     example="secret123"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registration request accepted and verification code sent by SMS or email.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Sms jo'natildi"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Ali"),
     *                 @OA\Property(property="surname", type="string", example="Valiyev"),
     *                 @OA\Property(property="type", type="integer", description="`1` for phone registration, `0` for email registration.", example=1),
     *                 @OA\Property(property="mobile_number", type="string", nullable=true, example="+998901234567"),
     *                 @OA\Property(property="email", type="string", format="email", nullable=true, example="ali@example.com"),
     *                 @OA\Property(property="birth_date", type="string", format="date", nullable=true, example="2000-01-15"),
     *                 @OA\Property(property="gender", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="city_id", type="integer", nullable=true, example=3),
     *                 @OA\Property(property="image", type="string", nullable=true, example="https://teacher-muzaffar.com/storage/userImage/665e6e40_avatar.png")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="mobile_number_or_email", type="array", @OA\Items(type="string", example="Telefon raqam yoki email kiriting")),
     *                 @OA\Property(property="mobile_number", type="array", @OA\Items(type="string", example="Telefon raqam noto‘g‘ri")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="Email formati noto‘g‘ri")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="Parol bir xil bo'lishi kerak")),
     *                 @OA\Property(property="city_id", type="array", @OA\Items(type="string", example="Shaxar mavjud emas")),
     *                 @OA\Property(property="image", type="array", @OA\Items(type="string", example="Rasm jpg, jpeg, png yoki webp bo‘lishi kerak"))
     *             )
     *         )
     *     )
     * )
     */
    public function register(UserRegisterApiRequest $request)
    {

        $code = rand(1000, 9999);

        $data = $request->all();
        $type = $data['type'];


        if (isset($data['image'])) {
            $file = $data['image'];
            $imageName = uniqid() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/userImage'), $imageName);
            $imagePath = 'https://teacher-muzaffar.com/storage/userImage/' . $imageName;
            $data['image'] = $imagePath;
        }

        if ($data['type']) {
            $user = User::with('city')->where('mobil_number', $data['mobile_number'])->exists();
        }else{
            $user = User::with('city')->where('email', $data['email'])->exists();

        }

        if (!$user) {
            if ($type){
                $user2 = User::create([
                    'name' => $data['name'],
                    'surname' => $data['surname'],
                    'mobil_number' => $data['mobile_number'],
                    'password' => bcrypt($data['password']),
                    'birth_date' => isset($data['birth_date']) ? $data['birth_date'] : null,
                    'gender' => isset($data['gender']) ? (int)$data['gender'] : null,
                    'city_id' => isset($data['city_id']) ? (int)$data['city_id'] : null,
                    'mobile_verify_code' => $code,
                    'image' => isset($data['image']) ? $data['image'] : null,
                ]);
                $user2->roles()->sync(2);
            }else{
                $user2 = User::create([
                    'name' => $data['name'],
                    'surname' => $data['surname'],
                    'email' => $data['email'],
                    'password' => bcrypt($data['password']),
                    'birth_date' => isset($data['birth_date']) ? $data['birth_date'] : null,
                    'gender' => isset($data['gender']) ? (int)$data['gender'] : null,
                    'city_id' => isset($data['city_id']) ? (int)$data['city_id'] : null,
                    'email_verify_code' => $code,
                    'image' => isset($data['image']) ? $data['image'] : null,
                ]);
                $user2->roles()->sync(2);
            }
        }
        else {
            if ($type){
                $user = User::with('city')->where('mobil_number', $data['mobile_number'])->first();
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
                            'mobil_number' => $data['mobile_number'],
                            'password' => $data['password'],
                            'birth_date' => isset($data['birth_date']) ? $data['birth_date'] : null,
                            'gender' => isset($data['gender']) ? (int)$data['gender'] : null,
                            'city_id' => isset($data['city_id']) ? (int)$data['city_id'] : null,
                            'mobile_verify_code' => $code,
                            'image' => isset($data['image']) ? $data['image'] : null,
                        ]);
                        $user->roles()->sync(2);

                    }
                }
            }else{
                $user = User::with('city')->where('email', $data['email'])->first();
                if ($user->email_verified_at !== null) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'email' => ['Email allaqachon mavjud.'],
                        ]
                    ]);
                } else {
                    if ($user->email_verified_at == null) {
                        $user->update([
                            'name' => $data['name'],
                            'surname' => $data['surname'],
                            'email' => $data['email'],
                            'password' => $data['password'],
                            'birth_date' => isset($data['birth_date']) ? $data['birth_date'] : null,
                            'gender' => isset($data['gender']) ? (int)$data['gender'] : null,
                            'city_id' => isset($data['city_id']) ? (int)$data['city_id'] : null,
                            'email_verify_code' => $code,
                            'image' => isset($data['image']) ? $data['image'] : null,
                        ]);
                        $user->roles()->sync(2);

                    }
                }
            }

        }

        $int_var = preg_replace('/[^0-9]/', '', $data['mobile_number'] ?? '');
        $message = "Kodni hech kimga bermang! TM school ilovasiga kirish uchun tasdiqlash kodi: $code";

        if ($type){
            SendSmsJob::dispatch($int_var, $message)->onQueue('sms');
            $responseMessage = "Sms jo'natildi";
        }else{
            $email = $data['email'];
            SendEmailVerification::dispatch($email, $code)->onQueue('email');
            $responseMessage = "Emailga tasdiqlash kodi yuborildi";
        }


        $data['city_id'] = isset($data['city_id']) ? (int)$data['city_id'] : null;
        $data['gender'] = isset($data['gender']) ? (int)$data['gender'] : null;
        unset($data['password']);
        unset($data['password_confirmation']);
        unset($data['mobile_number_or_email']);

        return response()->json([
            'success' => true,
            'message' => $responseMessage,
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

    /**
     * @OA\Post(
     *     path="/v2/verify_sms",
     *     summary="Verify SMS or Email code after registration",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type","verify_code"},
     *             @OA\Property(
     *                 property="type",
     *                 type="integer",
     *                 enum={0,1},
     *                 example=1,
     *                 description="1 = phone verification, 0 = email verification"
     *             ),
     *             @OA\Property(
     *                 property="mobile_number",
     *                 type="string",
     *                 nullable=true,
     *                 example="+998901234567",
     *                 description="Required if type = 1"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 nullable=true,
     *                 example="user@gmail.com",
     *                 description="Required if type = 0"
     *             ),
     *             @OA\Property(
     *                 property="verify_code",
     *                 type="string",
     *                 example="1234"
     *             ),
     *             @OA\Property(
     *                 property="device_id",
     *                 type="string",
     *                 nullable=true,
     *                 example="device-123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Code verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User ro'yhatdan muvaffaqiyatli o'tdi"),
     *             @OA\Property(property="auth_token", type="string", example="1|tokenstring"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", nullable=true, example="Abduraxmon"),
     *                 @OA\Property(property="mobil_number", type="string", nullable=true, example="+998901234567"),
     *                 @OA\Property(property="email", type="string", nullable=true, example="user@gmail.com"),
     *                 @OA\Property(property="gender", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="birth_date", type="string", nullable=true, example="2004-05-16"),
     *                 @OA\Property(property="surname", type="string", nullable=true, example="Jalolov"),
     *                 @OA\Property(property="image", type="string", nullable=true, example="https://example.com/storage/users/avatar.png"),
     *                 @OA\Property(property="city_id", type="integer", nullable=true, example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid code or validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 oneOf={
     *                     @OA\Schema(type="string", example="Kod xato kiritildi"),
     *                     @OA\Schema(
     *                         type="object",
     *                         @OA\Property(
     *                             property="type",
     *                             type="array",
     *                             @OA\Items(type="string", example="The type field is required.")
     *                         ),
     *                         @OA\Property(
     *                             property="mobile_number",
     *                             type="array",
     *                             @OA\Items(type="string", example="Telefon raqam kiritilmadi")
     *                         ),
     *                         @OA\Property(
     *                             property="email",
     *                             type="array",
     *                             @OA\Items(type="string", example="Email kiritilmadi")
     *                         ),
     *                         @OA\Property(
     *                             property="verify_code",
     *                             type="array",
     *                             @OA\Items(type="string", example="Code kiritilmadi")
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     *     )
     * )
     */

    public function verify_sms(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|in:1,0',

            'mobile_number' => 'nullable|required_if:type,1',
            'email' => 'nullable|required_if:type,0|email',

            'verify_code' => 'required',
        ], [
            'mobile_number.required_if' => 'Telefon raqam kiritilmadi',
            'email.required_if' => 'Email kiritilmadi',
            'email.email' => 'Email format noto‘g‘ri',

            'verify_code.required' => 'Code kiritilmadi',
        ]);

        if ($request->type == 1) {
            $user = User::where('mobil_number', $request->mobile_number)->first();

            if ($user && $request->verify_code == $user['mobile_verify_code'] && $request->mobile_number == $user['mobil_number']) {
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
        }else{
            $user = User::where('email', $request->email)->first();

            if ($user && $request->verify_code == $user['email_verify_code'] && $request->email == $user['email']) {
                $user->update([
                    'email_verified_at' => Carbon::now(),
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


    }

    /**
     * @OA\Post(
     *     path="/v3/login",
     *     summary="Login user by phone number or email",
     *     description="Accepts a single `email_or_mobile_number` field. If an email is sent, the request is handled as email login. Otherwise the value is normalized as a Uzbekistan phone number and used for phone login.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email_or_mobile_number","password"},
     *             @OA\Property(
     *                 property="email_or_mobile_number",
     *                 type="string",
     *                 description="Phone number or email for login. Accepted phone examples: `+998901234567` or `901234567`. Accepted email example: `ali@example.com`.",
     *                 example="+998941070524"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 minLength=6,
     *                 description="User password.",
     *                 example="password1115"
     *             ),
     *             @OA\Property(
     *                 property="device_id",
     *                 type="string",
     *                 nullable=true,
     *                 description="Optional device identifier to attach to the user session.",
     *                 example="device-123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="messages", type="string", example="Foydalanuvchi logindan muvaffaqiyatli o'tdi"),
     *             @OA\Property(property="token", type="string", example="1|tokenstring"),
     *             @OA\Property(property="is_uzbekistan", type="boolean", description="Foydalanuvchining IP manzili O'zbekistonga tegishli bo'lsa `true`, aks holda `false`.", example=true),
     *             @OA\Property(property="premium", type="integer", example=1),
     *             @OA\Property(
     *                 property="active_plan",
     *                 nullable=true,
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=12),
     *                 @OA\Property(property="user_id", type="integer", example=25),
     *                 @OA\Property(property="plan_id", type="integer", nullable=true, example=2),
     *                 @OA\Property(property="plan_name", type="string", nullable=true, example="Premium 30 days"),
     *                 @OA\Property(property="type", type="integer", nullable=true, example=0),
     *                 @OA\Property(property="type_label", type="string", nullable=true, example="app"),
     *                 @OA\Property(property="price", type="number", format="float", nullable=true, example=99000),
     *                 @OA\Property(property="discount", type="number", format="float", nullable=true, example=10000),
     *                 @OA\Property(property="promocode_id", type="integer", nullable=true, example=4),
     *                 @OA\Property(property="start_time", type="string", format="date-time", nullable=true, example="2026-05-01T00:00:00.000000Z"),
     *                 @OA\Property(property="end_time", type="string", format="date-time", nullable=true, example="2026-05-31T23:59:59.000000Z"),
     *                 @OA\Property(property="is_active", type="boolean", example=true)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=25),
     *                 @OA\Property(property="name", type="string", nullable=true, example="Ali"),
     *                 @OA\Property(property="mobil_number", type="string", nullable=true, example="+998901234567"),
     *                 @OA\Property(property="email", type="string", format="email", nullable=true, example="ali@example.com"),
     *                 @OA\Property(property="gender", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="birth_date", type="string", format="date", nullable=true, example="2000-01-15"),
     *                 @OA\Property(property="surname", type="string", nullable=true, example="Valiyev"),
     *                 @OA\Property(property="image", type="string", nullable=true, example="https://teacher-muzaffar.com/storage/userImage/avatar.png"),
     *                 @OA\Property(property="city_id", type="integer", nullable=true, example=3)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="email_or_mobile_number", type="array", @OA\Items(type="string", example="Email yoki telefon raqam kiritilmadi")),
     *                 @OA\Property(property="mobile_number", type="array", @OA\Items(type="string", example="Telefon raqam noto‘g‘ri formatda")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="Email format noto‘g‘ri")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="Parol kamida 6 ta belgidan iborat bo‘lishi kerak"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials or unverified account",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 oneOf={
     *                     @OA\Schema(type="string", example="Parol xato kiritildi"),
     *                     @OA\Schema(type="string", example="Foydalanuvchi topilmadi")
     *                 }
     *             )
     *         )
     *     )
     * )
     */
    public function login_v2(ApiLoginRequest $request)
    {

        if ($request->type == 1) {
            $user = User::where('mobil_number', $request->mobile_number)->first();
        }else{
            $user = User::where('email', $request->email)->first();
        }

        if (( $request->type ? ($user && $user->mobile_verified_at !== null ) : ($user && $user->email_verified_at !== null) ) ) {
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user);
                $authToken = $user->createToken('auth_token')->plainTextToken;

                $user->update(['device_id'=>$request->device_id]);

                $cacheKey = 'user_permissions_' . $user->id;
                Cache::forget($cacheKey);
                $today = now();

                $existingPlan = PremiumToUsers::with('plan')->where('user_id', $user->id)
                    ->where(function ($query) use ($today) {
                        $query->where('start_time', '<=', $today)
                            ->where('end_time', '>=', $today);
                    })
                    ->orderByDesc('id')
                    ->first();

                $ip_address = $request->ip();
                $location = Location::get($ip_address);
                $is_uzbekistan = $location && $location->countryName == 'Uzbekistan';

                if ($user->mobil_number=='+998941070524'){
                    $is_uzbekistan=false;
                }

                return response()->json([
                    'success' => true,
                    'messages' => 'Foydalanuvchi logindan muvaffaqiyatli o\'tdi',
                    'token' => $authToken,
                    'is_uzbekistan' => $is_uzbekistan,
                    'premium'=>$existingPlan ? 1 : 0,
                    'active_plan' => $existingPlan ? new ActivePlanResource($existingPlan) : null,
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

    /**
     * @OA\Post(
     *     path="/v2/forgotpassword",
     *     summary="Send SMS or Email code for password reset",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mobile_number_or_email"},
     *             @OA\Property(
     *                 property="mobile_number_or_email",
     *                 type="string",
     *                 example="+998901234567",
     *                 description="Phone number or email. Example: +998901234567 or user@gmail.com"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Code sent for password reset",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="type", type="integer", example=1, description="1 = phone, 0 = email"),
     *                 @OA\Property(property="mobile_number", type="string", nullable=true, example="+998901234567"),
     *                 @OA\Property(property="email", type="string", nullable=true, example="user@gmail.com"),
     *                 @OA\Property(property="code", type="integer", example=1234),
     *                 @OA\Property(property="message", type="string", example="Userga sms jonatildi!")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or user not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 oneOf={
     *                     @OA\Schema(type="string", example="Bunday foydalanuvchi mavjud emas!"),
     *                     @OA\Schema(type="object")
     *                 }
     *             )
     *         )
     *     )
     * )
     */
    public function forgotpassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'mobile_number_or_email' => 'required|string',
        ], [
            'mobile_number_or_email.required' => 'Telefon raqam yoki email kiritilmadi!',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validate->errors(),
            ], 422);
        }

        $raw = trim($request->mobile_number_or_email);

        $type = 1;
        $mobile_number = null;
        $email = null;

        if (filter_var($raw, FILTER_VALIDATE_EMAIL)) {
            $type = 0;
            $email = strtolower($raw);
        } else {
            $digits = preg_replace('/\D+/', '', $raw);

            if (strlen($digits) > 9) {
                $digits = substr($digits, -9);
            }

            $mobile_number = strlen($digits) === 9
                ? '+998' . $digits
                : $raw;
        }

        if ($type == 1) {
            if (!preg_match('/^\+998\d{9}$/', $mobile_number)) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'mobile_number_or_email' => ['Telefon raqam noto‘g‘ri formatda']
                    ],
                ], 422);
            }

            $user = User::where('mobil_number', $mobile_number)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Bunday foydalanuvchi mavjud emas!',
                ], 422);
            }

            $code = rand(1000, 9999);

            $user->update([
                'mobile_verify_code' => $code
            ]);

            $int_var = preg_replace('/[^0-9]/', '', $mobile_number);

            $message = "Kodni hech kimga bermang! TM school ilovasiga kirish uchun tasdiqlash kodi: $code";

            SendSmsJob::dispatch($int_var, $message)->onQueue('sms');

            return response()->json([
                'success' => true,
                'data' => [
                    'type' => 1,
                    'mobile_number' => $mobile_number,
                    'email' => null,
                    'code' => $code,
                    'message' => "Userga sms jonatildi!"
                ]
            ]);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'errors' => 'Bunday foydalanuvchi mavjud emas!',
            ], 422);
        }

        $code = rand(1000, 9999);

        $user->update([
            'email_verify_code' => $code
        ]);

        SendEmailVerification::dispatch($email, $code)->onQueue('email');

        return response()->json([
            'success' => true,
            'data' => [
                'type' => 0,
                'mobile_number' => null,
                'email' => $email,
                'code' => $code,
                'message' => "Emailga tasdiqlash kodi yuborildi!"
            ]
        ]);
    }


    /**
     * @OA\Post(
     *     path="/v2/verify_password",
     *     summary="Verify password reset code",
     *     description="type = 1 bo'lsa SMS orqali yuborilgan kod tekshiriladi. type = 0 bo'lsa Email orqali yuborilgan kod tekshiriladi.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type","verify_code"},
     *             @OA\Property(
     *                 property="type",
     *                 type="integer",
     *                 enum={0,1},
     *                 example=1,
     *                 description="1 = phone, 0 = email"
     *             ),
     *             @OA\Property(
     *                 property="mobile_number",
     *                 type="string",
     *                 nullable=true,
     *                 example="+998901234567",
     *                 description="Faqat type = 1 bo'lsa majburiy"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 nullable=true,
     *                 example="user@gmail.com",
     *                 description="Faqat type = 0 bo'lsa majburiy"
     *             ),
     *             @OA\Property(
     *                 property="verify_code",
     *                 type="string",
     *                 example="1234",
     *                 description="SMS yoki Email orqali yuborilgan tasdiqlash kodi"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Code verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example="Parolni ozgartirish mumkin"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or invalid verification code",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Kod xato kiritildi"
     *                     ),
     *                     @OA\Schema(
     *                         type="object",
     *                         @OA\Property(
     *                             property="type",
     *                             type="array",
     *                             @OA\Items(type="string", example="The type field is required.")
     *                         ),
     *                         @OA\Property(
     *                             property="mobile_number",
     *                             type="array",
     *                             @OA\Items(type="string", example="Telefon raqam noto‘g‘ri formatda!")
     *                         ),
     *                         @OA\Property(
     *                             property="email",
     *                             type="array",
     *                             @OA\Items(type="string", example="Email format noto‘g‘ri!")
     *                         ),
     *                         @OA\Property(
     *                             property="verify_code",
     *                             type="array",
     *                             @OA\Items(type="string", example="verify_code kiritilmadi!")
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     *     )
     * )
     */
    public function verify_password(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'type' => 'required|in:1,0',

            'mobile_number' => [
                'exclude_unless:type,1',
                'required',
                'regex:/^\+998\d{9}$/',
                'exists:users,mobil_number',
            ],

            'email' => [
                'exclude_unless:type,0',
                'required',
                'email',
                'max:255',
                'exists:users,email',
            ],

            'verify_code' => 'required',
        ], [
            'mobile_number.required' => 'Telefon raqam kiritilmadi!',
            'mobile_number.regex' => 'Telefon raqam noto‘g‘ri formatda!',
            'mobile_number.exists' => 'Bunday telefon raqam mavjud emas!',

            'email.required' => 'Email kiritilmadi!',
            'email.email' => 'Email format noto‘g‘ri!',
            'email.exists' => 'Bunday email mavjud emas!',

            'verify_code.required' => 'verify_code kiritilmadi!',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validate->errors(),
            ], 422);
        }

        if ($request->type == 1) {
            $user = User::where('mobil_number', $request->mobile_number)->first();

            $isCodeCorrect = $user && $request->verify_code == $user->mobile_verify_code;
        } else {
            $user = User::where('email', $request->email)->first();

            $isCodeCorrect = $user && $request->verify_code == $user->email_verify_code;
        }

        if ($isCodeCorrect) {
            if ($request->type == 1) {
                $user->update([
                    'mobile_verified_at' => Carbon::now(),
                ]);
            } else {
                $user->update([
                    'email_verified_at' => Carbon::now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => 'Parolni ozgartirish mumkin',
            ]);
        }

        return response()->json([
            'success' => false,
            'errors' => 'Kod xato kiritildi',
        ], 422);
    }
    /**
     * @OA\Post(
     *     path="/v2/resetPassword",
     *     summary="Reset user password",
     *     description="type = 1 bo'lsa telefon raqam orqali, type = 0 bo'lsa email orqali parol yangilanadi.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type","password"},
     *             @OA\Property(
     *                 property="type",
     *                 type="integer",
     *                 enum={0,1},
     *                 example=1,
     *                 description="1 = phone, 0 = email"
     *             ),
     *             @OA\Property(
     *                 property="mobile_number",
     *                 type="string",
     *                 nullable=true,
     *                 example="+998901234567",
     *                 description="Faqat type = 1 bo'lsa majburiy"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 nullable=true,
     *                 example="user@gmail.com",
     *                 description="Faqat type = 0 bo'lsa majburiy"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 example="newsecret123",
     *                 description="Yangi parol, kamida 6 ta belgi"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Parolni muvaffaqiyatli o'zgartirildi"
     *             ),
     *             @OA\Property(
     *                 property="auth_token",
     *                 type="string",
     *                 example="1|tokenstring"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or user not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="string",
     *                         example="Bunday foydalanuvchi mavjud emas!"
     *                     ),
     *                     @OA\Schema(
     *                         type="object",
     *                         @OA\Property(
     *                             property="type",
     *                             type="array",
     *                             @OA\Items(type="string", example="The type field is required.")
     *                         ),
     *                         @OA\Property(
     *                             property="mobile_number",
     *                             type="array",
     *                             @OA\Items(type="string", example="Telefon raqam noto‘g‘ri formatda!")
     *                         ),
     *                         @OA\Property(
     *                             property="email",
     *                             type="array",
     *                             @OA\Items(type="string", example="Email format noto‘g‘ri!")
     *                         ),
     *                         @OA\Property(
     *                             property="password",
     *                             type="array",
     *                             @OA\Items(type="string", example="Parol 6 tadan kam bolmasligi kerak")
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     *     )
     * )
     */
    public function resetPassword(Request $request)
    {
        $validate=Validator::make($request->all(),[
            'type' => 'required|in:1,0',

            'mobile_number' => [
                'exclude_unless:type,1',
                'required',
                'regex:/^\+998\d{9}$/',
                'exists:users,mobil_number',
            ],

            'email' => [
                'exclude_unless:type,0',
                'required',
                'email',
                'max:255',
                'exists:users,email',
            ],

            'password' => "required|min:6",
        ],
            [
                'mobile_number.required' => 'Telefon raqam kiritilmadi!',
                'mobile_number.regex' => 'Telefon raqam noto‘g‘ri formatda!',
                'mobile_number.exists' => 'Bunday telefon raqam mavjud emas!',

                'email.required' => 'Email kiritilmadi!',
                'email.email' => 'Email format noto‘g‘ri!',
                'email.exists' => 'Bunday email mavjud emas!',

                'password.min' => "Parol 6 tadan kam bolmasligi kerak"
            ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validate->errors(),
            ]);
        }
        if ($request->type == 1) {
            $user = User::where('mobil_number', $request->mobile_number)->first();
        }else{
            $user = User::where('email', $request->email)->first();
        }

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




    /**
     * @OA\Get(
     *     path="/v2/google/link",
     *     operationId="getV2GoogleLink",
     *     tags={"Auth"},
     *     summary="Get Google and Telegram links",
     *     description="Returns Google login redirect link and Telegram support bot link.",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="google_link", type="string", example="http://127.0.0.1:8000/google/redirect"),
     *                 @OA\Property(property="telegram_link", type="string", example="https://t.me/cefr_online_support_login_bot")
     *             ),
     *             @OA\Property(property="message", type="string", example="google link va telegram link")
     *         )
     *     )
     * )
     */
    public function google_link(Request $request)
    {
        $googleAuthUrl = Socialite::driver('google')
            ->stateless()
            ->with(['state' => 'mobile']) // web uchun 'web'
            ->redirect()
            ->getTargetUrl();
        $ip_address = $request->ip();
        $location = Location::get($ip_address);
        $is_uzbekistan = $location && $location->countryName == 'Uzbekistan';

        return $this->success([
            'is_uzbekistan' => $is_uzbekistan,
            'google_link'=>$googleAuthUrl,
            'telegram_link' => 'https://t.me/cefr_online_support_login_bot?start=mobile',
        ],'google link va telegram link');

    }

    /**
     * @OA\Get(
     *     path="/v2/auth/google/callback",
     *     operationId="googleCallback",
     *     tags={"Auth"},
     *     summary="Google OAuth callback",
     *     description="Google tomonidan qaytarilgan callback. Google foydalanuvchisini email orqali topadi yoki yangi user yaratadi va Sanctum token qaytaradi.",
     *     @OA\Parameter(
     *         name="code",
     *         in="query",
     *         required=true,
     *         description="Google tomonidan berilgan authorization code",
     *         @OA\Schema(type="string", example="4/0AfJohXk...")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Muvaffaqiyatli login yoki ro'yxatdan o'tish",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Google orqali muvaffaqiyatli kirildi"),
     *             @OA\Property(property="auth_token", type="string", example="1|tokenstring"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", nullable=true, example="Ali"),
     *                 @OA\Property(property="surname", type="string", nullable=true, example="Valiyev"),
     *                 @OA\Property(property="email", type="string", nullable=true, example="ali@gmail.com"),
     *                 @OA\Property(property="mobil_number", type="string", nullable=true, example=null),
     *                 @OA\Property(property="gender", type="integer", nullable=true, example=null),
     *                 @OA\Property(property="birth_date", type="string", nullable=true, example=null),
     *                 @OA\Property(property="image", type="string", nullable=true, example="https://lh3.googleusercontent.com/..."),
     *                 @OA\Property(property="city_id", type="integer", nullable=true, example=null)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Google dan email olinmadi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="string", example="Google accountdan email olinmadi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Google callback xatosi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="string", example="Google callback xatosi")
     *         )
     *     )
     * )
     */
    public function google_callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Google callback xatosi',
            ], 500);
        }

        $email = $googleUser->getEmail();

        if (!$email) {
            return response()->json([
                'success' => false,
                'errors' => 'Google accountdan email olinmadi',
            ], 422);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name'              => $googleUser->getName() ?? $googleUser->getNickname(),
                'email'             => $email,
                'email_verified_at' => now(),
                'password'          => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
                'image'             => $googleUser->getAvatar(),
            ]);
            $user->roles()->sync(2);
        }

        $authToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'    => true,
            'message'    => "Google orqali muvaffaqiyatli kirildi",
            'auth_token' => $authToken,
            'data'       => new UserResource($user),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/v2/auth/exchange-token",
     *     operationId="exchangeLoginToken",
     *     tags={"Auth"},
     *     summary="Exchange one-time login_token for a real auth token",
     *     description="Google/Telegram callback universal link (`https://cefronline.uz/auth-callback?login_token=...`) orqali ilovaga yuborilgan bir martalik `login_token` ni haqiqiy Sanctum auth token va foydalanuvchi ma'lumotlariga almashtiradi. Token 10 daqiqa amal qiladi va faqat bir marta ishlatiladi (ikkinchi marta chaqirilsa `Token allaqachon ishlatilgan` xatosi qaytadi).",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login_token"},
     *             @OA\Property(
     *                 property="login_token",
     *                 type="string",
     *                 description="Universal link query'sidan olingan bir martalik token (64 belgi).",
     *                 example="aB3xYzQwErTyUiOpAsDfGhJkLzXcVbNmQwErTyUiOpAsDfGhJkLzXcVbNm123456"
     *             ),
     *             @OA\Property(
     *                 property="device_id",
     *                 type="string",
     *                 nullable=true,
     *                 description="Ixtiyoriy qurilma identifikatori.",
     *                 example="device-123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="messages", type="string", example="Foydalanuvchi logindan muvaffaqiyatli o'tdi"),
     *             @OA\Property(property="token", type="string", example="1|tokenstring"),
     *             @OA\Property(property="is_uzbekistan", type="boolean", description="Foydalanuvchining IP manzili O'zbekistonga tegishli bo'lsa `true`, aks holda `false`.", example=true),
     *             @OA\Property(property="premium", type="integer", description="`1` = aktiv premium bor, `0` = yo'q.", example=1),
     *             @OA\Property(
     *                 property="active_plan",
     *                 nullable=true,
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=12),
     *                 @OA\Property(property="user_id", type="integer", example=25),
     *                 @OA\Property(property="plan_id", type="integer", nullable=true, example=2),
     *                 @OA\Property(property="plan_name", type="string", nullable=true, example="Premium 30 days"),
     *                 @OA\Property(property="type", type="integer", nullable=true, example=0),
     *                 @OA\Property(property="type_label", type="string", nullable=true, example="app"),
     *                 @OA\Property(property="price", type="number", format="float", nullable=true, example=99000),
     *                 @OA\Property(property="discount", type="number", format="float", nullable=true, example=10000),
     *                 @OA\Property(property="promocode_id", type="integer", nullable=true, example=4),
     *                 @OA\Property(property="start_time", type="string", format="date-time", nullable=true, example="2026-05-01T00:00:00.000000Z"),
     *                 @OA\Property(property="end_time", type="string", format="date-time", nullable=true, example="2026-05-31T23:59:59.000000Z"),
     *                 @OA\Property(property="is_active", type="boolean", example=true)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=25),
     *                 @OA\Property(property="name", type="string", nullable=true, example="Ali"),
     *                 @OA\Property(property="mobil_number", type="string", nullable=true, example="+998901234567"),
     *                 @OA\Property(property="email", type="string", format="email", nullable=true, example="ali@example.com"),
     *                 @OA\Property(property="gender", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="birth_date", type="string", format="date", nullable=true, example="2000-01-15"),
     *                 @OA\Property(property="surname", type="string", nullable=true, example="Valiyev"),
     *                 @OA\Property(property="image", type="string", nullable=true, example="https://teacher-muzaffar.com/storage/userImage/avatar.png"),
     *                 @OA\Property(property="city_id", type="integer", nullable=true, example=3)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Token topilmadi, muddati tugagan yoki allaqachon ishlatilgan",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="errors",
     *                 oneOf={
     *                     @OA\Schema(type="string", example="Token topilmadi"),
     *                     @OA\Schema(type="string", example="Token allaqachon ishlatilgan"),
     *                     @OA\Schema(type="string", example="Token muddati tugagan"),
     *                     @OA\Schema(
     *                         type="object",
     *                         @OA\Property(
     *                             property="login_token",
     *                             type="array",
     *                             @OA\Items(type="string", example="login_token kiritilmadi")
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     *     )
     * )
     */
    public function exchange_login_token(Request $request)
    {
        $request->validate([
            'login_token' => 'required|string',
        ], [
            'login_token.required' => 'login_token kiritilmadi',
        ]);

        $user = User::where('login_token', $request->login_token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'errors' => 'Token topilmadi',
            ], 422);
        }

        if ($user->login_token_used_at !== null) {
            return response()->json([
                'success' => false,
                'errors' => 'Token allaqachon ishlatilgan',
            ], 422);
        }

        if ($user->login_token_expires_at === null || now()->greaterThan($user->login_token_expires_at)) {
            return response()->json([
                'success' => false,
                'errors' => 'Token muddati tugagan',
            ], 422);
        }

        // bir martalik: ishlatilgan deb belgilaymiz va tokenni o'chiramiz
        $user->update([
            'login_token_used_at' => now(),
            'login_token' => null,
            'device_id' => $request->device_id ?? $user->device_id,
        ]);

        Auth::login($user);
        $authToken = $user->createToken('auth_token')->plainTextToken;

        Cache::forget('user_permissions_' . $user->id);

        $today = now();
        $existingPlan = PremiumToUsers::with('plan')->where('user_id', $user->id)
            ->where('start_time', '<=', $today)
            ->where('end_time', '>=', $today)
            ->orderByDesc('id')
            ->first();
        $ip_address = $request->ip();
        $location = Location::get($ip_address);
        $is_uzbekistan = $location && $location->countryName == 'Uzbekistan';

        if ($user->mobil_number=='+998941070524'){
            $is_uzbekistan=false;
        }

        return response()->json([
            'success' => true,
            'messages' => 'Foydalanuvchi logindan muvaffaqiyatli o\'tdi',
            'token' => $authToken,
            'is_uzbekistan' => $is_uzbekistan,
            'premium' => $existingPlan ? 1 : 0,
            'active_plan' => $existingPlan ? new ActivePlanResource($existingPlan) : null,
            'data' => new UserResource($user),
        ]);
    }
}
