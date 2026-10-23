<?php

namespace App\Http\Controllers\Api\V2\Profile;

use App\Http\Controllers\Api\V2ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V2\Profile\ProfileUpdateApiRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\Premium\ActivePlanResource;
use App\Models\PremiumToUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class UsersApiController extends V2ApiController
{
    /**
     * @OA\Tag(
     *     name="Profile",
     *     description="Authenticated profile endpoints"
     * )
     */

    public function index()
    {
        return new UserResource(User::with(['roles', 'city'])->get());
    }

    /**
     * @OA\Get(
     *     path="/v2/profile",
     *     summary="Get current user profile",
     *     description="Returns the authenticated user's profile information. If `device_id` is provided and does not match the authenticated user's saved device id, the request is rejected.",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="device_id",
     *         in="query",
     *         required=false,
     *         description="Optional device identifier to verify the current session device.",
     *         @OA\Schema(type="string", example="device-123")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile data returned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profilga malumot keldi"),
     *             @OA\Property(property="id", type="integer", example=25),
     *             @OA\Property(property="name", type="string", nullable=true, example="Ali"),
     *             @OA\Property(property="surname", type="string", nullable=true, example="Valiyev"),
     *             @OA\Property(property="type", type="integer", example=0, description="0 = email bilan, 1 = telefon bilan"),
     *             @OA\Property(property="mobil_number", type="string", nullable=true, example="+998901234567"),
     *             @OA\Property(property="email", type="string", nullable=true, example="user@gmail.com"),
     *             @OA\Property(property="gender", type="integer", nullable=true, example=1),
     *             @OA\Property(property="birth_date", type="string", format="date", nullable=true, example="2000-01-15"),
     *             @OA\Property(property="city_id", type="integer", nullable=true, example=3),
     *             @OA\Property(property="image", type="string", nullable=true, example="https://teacher-muzaffar.com/storage/userImage/avatar.png"),
     *             @OA\Property(
     *                 property="active_plan",
     *                 type="object",
     *                 nullable=true,
     *                 description="Foydalanuvchining hozirgi aktiv premium rejasi. Agar mavjud bo'lmasa null qaytadi.",
     *                 @OA\Property(property="id", type="integer", example=12),
     *                 @OA\Property(property="user_id", type="integer", example=25),
     *                 @OA\Property(property="plan_id", type="integer", example=3),
     *                 @OA\Property(property="plan_name", type="string", nullable=true, example="Premium"),
     *                 @OA\Property(property="type", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="type_label", type="string", nullable=true, example="Oylik"),
     *                 @OA\Property(property="price", type="number", format="float", nullable=true, example=49900),
     *                 @OA\Property(property="discount", type="number", format="float", nullable=true, example=5000),
     *                 @OA\Property(property="promocode_id", type="integer", nullable=true, example=null),
     *                 @OA\Property(property="start_time", type="string", format="date-time", example="2026-06-01T00:00:00.000000Z"),
     *                 @OA\Property(property="end_time", type="string", format="date-time", example="2026-07-01T00:00:00.000000Z"),
     *                 @OA\Property(property="is_active", type="boolean", example=true)
     *             ),
     *             @OA\Property(
     *                 property="balance_data",
     *                 type="object",
     *                 @OA\Property(property="balance", type="string", example="10 000.12"),
     *                 @OA\Property(property="status", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated user or device mismatch",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Undeviceided")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="errors", type="string", example="Unauthenticated.")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="403 Forbidden")
     *         )
     *     )
     * )
     */
    public function profile(Request $request)
    {
        if ($request->device_id) {
            if (auth()->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }



        $user=\auth()->user();
        $today = now();

        $existingPlan = PremiumToUsers::with('plan')->where('user_id', $user->id)
            ->where(function ($query) use ($today) {
                $query->where('start_time', '<=', $today)
                    ->where('end_time', '>=', $today);
            })
            ->orderByDesc('id')
            ->first();

        if ($user){
        return \response()->json([
            'success'=>true,
            "message"=>"Profilga malumot keldi",
            'id'=>$user->id,
            'name'=>$user->name,
            'surname'=>$user->surname,
            'type'=>$user->email ? 0 : 1 ,
            'mobil_number'=>$user->mobil_number,
            'email'=>$user->email,
            'gender'=>intval($user->gender),
            'birth_date'=>$user->birth_date,
            'city_id'=>$user->city_id,
            'image'=>$user->image,
            'active_plan' => $existingPlan ? new ActivePlanResource($existingPlan) : null,
            'balance_data'=>[
                'balance' => number_format(floatval($user->balance),2,'.',' '),
                'status'=> floatval($user->balance) > 500 ? 1 : 0
            ]
        ]);
        }else{
            return \response()->json([
                'success'=>false,
                'errors'=>'User Kelmadi yoki yoq'
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/v2/logout",
     *     summary="Logout current user",
     *     description="Logs out the authenticated user by deleting the current access token.",
     *     tags={"Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Muvaffaqiyatli logout qilindi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User is already logged out or unauthenticated",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="errors", type="string", example="User logout qilib bolgan")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="message", type="string", example="Unauthenticated.")
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function logout()
    {

        $user = Auth::user();

        if ($user) {

            $user->currentAccessToken()->delete();

            return response()->json(['message' => 'Muvaffaqiyatli logout qilindi'], 200);
        } else {
            return response()->json(['errors' => 'User logout qilib bolgan'], 401);
        }
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->all());
        $user->roles()->sync($request->input('roles', []));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(User $user)
    {
        return new UserResource($user->load(['roles', 'city']));
    }

    /**
     * @OA\Post(
     *     path="/v2/profile-update",
     *     summary="Update user profile",
     *     description="Updates authenticated user's profile information.",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "surname"},
     *                 @OA\Property(property="name", type="string", example="Abduraxmon"),
     *                 @OA\Property(property="surname", type="string", example="Jalolov"),
     *                 @OA\Property(
     *                     property="birth_date",
     *                     type="string",
     *                     format="date",
     *                     nullable=true,
     *                     example="2000-01-15"
     *                 ),
     *                 @OA\Property(
     *                     property="city_id",
     *                     type="integer",
     *                     nullable=true,
     *                     example=3
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     nullable=true
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="user malumotlari"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="User muvaffaqiyatli taxrirlandi"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=25),
     *                     @OA\Property(property="name", type="string", example="Abduraxmon"),
     *                     @OA\Property(property="surname", type="string", example="Jalolov"),
     *                     @OA\Property(property="type", type="integer", example=1),
     *                     @OA\Property(property="mobil_number", type="string", nullable=true, example="+998941070524"),
     *                     @OA\Property(property="email", type="string", nullable=true, example="user@gmail.com"),
     *                     @OA\Property(property="gender", type="string", nullable=true, example="Erkak"),
     *                     @OA\Property(property="birth_date", type="string", format="date", nullable=true, example="2000-01-15"),
     *                     @OA\Property(property="city_id", type="integer", nullable=true, example=3),
     *                     @OA\Property(property="image", type="string", nullable=true, example="https://teacher-muzaffar.com/storage/userImage/avatar.png")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="validation failed")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function update(ProfileUpdateApiRequest $request)
    {


        $user=\auth()->user();

        if (!$user){
            return response()->json([
                'success' => false,
                'errors' => 'User Kelmadi yoki yoq'
            ]);
        }
        $data=$request->all();
        $imagePath = null;
        if (isset($data['image'])) {
            $file = $data['image'];
            $imageName = uniqid() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/userImage'), $imageName);
            $imagePath = 'https://teacher-muzaffar.com/storage/userImage/' . $imageName;
            $data['image'] = $imagePath;
        }

        $user->update([
            'name'=>$data['name'],
            'surname'=>$data['surname'],
            'birth_date' => isset($data['birth_date']) ? $data['birth_date'] : null,
            'city_id' =>isset($data['city_id']) ? (int) $data['city_id'] : null,
        ]);

        if (isset($data['image'])){
            $user->update([
                'image' => $data['image'],
            ]);
        }

        if ($user) {
            return $this->success([
                'success' => true,
                'message'=>'User muvaffaqiyatli taxrirlandi',
                'parol'=>'parol taxrirlanmadi',
                'data' =>[
                    'id'=>$user->id,
                    'name'=>$user->name,
                    'type'=>$user->email ? 0 : 1 ,
                    'mobil_number'=>$user->mobil_number,
                    'email'=>$user->email,
                    'gender'=>$user->gender ? 'Erkak' : 'Ayol',
                    'birth_date'=>$user->birth_date,
                    'city_id'=>$user->city_id,
                    'surname'=>$user->surname,
                    'image'=>$user->image,
                ]
            ],'user malumotlari');
        }else{
            return $this->error(["User yo'q"], 'Parolni tasdiqlang');
        }
    }

    /**
     * @OA\Post(
     *     path="/v2/password-update",
     *     summary="Update user password",
     *     description="Updates the selected user's password after confirming the current password.",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"history_password","password","password_confirmation"},
     *             @OA\Property(property="history_password", type="string", format="password", minLength=6, maxLength=16, example="password1115", description="Current password."),
     *             @OA\Property(property="password", type="string", format="password", minLength=6, maxLength=16, example="password1111", description="New password."),
     *             @OA\Property(property="password_confirmation", type="string", format="password", minLength=6, maxLength=16, example="password1111", description="Repeat the new password.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user malumotlari"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="User muvaffaqiyatli taxrirlandi"),
     *                 @OA\Property(property="parol", type="string", example="Parol taxrirlandi"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=25),
     *                     @OA\Property(property="name", type="string", nullable=true, example="Ali"),
     *                     @OA\Property(property="mobil_number", type="string", nullable=true, example="+998901234567"),
     *                     @OA\Property(property="gender", type="string", nullable=true, example="Erkak"),
     *                     @OA\Property(property="birth_date", type="string", format="date", nullable=true, example="2000-01-15"),
     *                     @OA\Property(property="city_id", type="integer", nullable=true, example=3),
     *                     @OA\Property(property="surname", type="string", nullable=true, example="Valiyev"),
     *                     @OA\Property(property="image", type="string", nullable=true, example="https://teacher-muzaffar.com/storage/userImage/avatar.png")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation failed, wrong current password, or user not found",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="message", type="string", example="validation failed"),
     *                     @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password field confirmation does not match.")),
     *                         @OA\Property(property="password_confirmation", type="array", @OA\Items(type="string", example="The password confirmation and password must match.")),
     *                         @OA\Property(property="history_password", type="array", @OA\Items(type="string", example="The history password field is required."))
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="message", type="string", example="Parolni tasdiqlang"),
     *                     @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         @OA\Property(property="history_password", type="string", example="Parol notogri ")
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="message", type="string", example="Qayta urinib koring "),
     *                     @OA\Property(property="errors", type="array", @OA\Items(type="string", example="User Kelmadi yoki yoq"))
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="403 Forbidden")
     *         )
     *     )
     * )
     */
    public function update_password(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'password' => 'required|string|min:6|max:16|confirmed',
            'password_confirmation' => 'required|string|min:6|max:16|same:password',
            'history_password' => 'required|string|min:6|max:16',
        ], [
            'password.required' => 'Parolni tasdiqlang',
        ]);

        if ($validation->fails()) {
            return $this->error($validation->errors(),'validation failed');
        }
        $user=\auth()->user();

        if (!$user){
            return $this->error(['User Kelmadi yoki yoq'],'Qayta urinib koring ');
        }
        $data=$request->all();

        if (!Hash::check($request->history_password, $user->password)){
             return $this->error(['history_password'=>'Parol notogri '], "Parolni tasdiqlang");
        }

        if ($data['password']){
            $user->update([
                'password' => bcrypt($data['password']),
            ]);
        }

        if ($user) {
            return $this->success([
                'success' => true,
                'message'=>'User muvaffaqiyatli taxrirlandi',
                'parol'=>$data['password'] ? 'Parol taxrirlandi' : 'parol taxrirlanmadi',
                'data' =>[
                    'id'=>$user->id,
                    'name'=>$user->name,
                    'type'=>$user->email ? 0 : 1 ,
                    'mobil_number'=>$user->mobil_number,
                    'email'=>$user->email,
                    'gender'=>$user->gender ? 'Erkak' : 'Ayol',
                    'birth_date'=>$user->birth_date,
                    'city_id'=>$user->city_id,
                    'surname'=>$user->surname,
                    'image'=>$user->image,
                ]
            ],'user malumotlari');
        }else{
            return $this->error(["User yo'q"], 'Parolni tasdiqlang');
        }
    }

    /**
     * @OA\Post(
     *     path="/v2/delete_account",
     *     summary="Delete account",
     *     description="Deletes the authenticated user and removes the user's tokens.",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User O'chirildi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated user",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="errors", type="string", example="User not authenticated")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="message", type="string", example="Unauthenticated.")
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function delete()
    {
        $user = Auth::user();

        if ($user) {
            $user->delete();
            $user->tokens()->delete(); // Hozirgi tokenni o'chirish (agar kerak bo'lsa)
            return response()->json(['message'=>'User O\'chirildi'], 200);
        } else {
            return response()->json(['errors' => 'User not authenticated'], 401);
        }
    }
    public function destroy(User $user)
    {
        $user->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
