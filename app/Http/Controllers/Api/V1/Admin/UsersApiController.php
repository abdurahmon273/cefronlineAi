<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UsersApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('profile_password_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource(User::with(['roles', 'city'])->get());
    }
    public function profile($id, Request $request)
    {
        if ($request->device_id) {
            if (auth()->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }


        abort_if(Gate::denies('profile_password_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user=\auth()->user();
        if ($user){
        return \response()->json([
            'success'=>true,
            "message"=>"Profilga malumot keldi",
            'id'=>$user->id,
            'name'=>$user->name,
            'surname'=>$user->surname,
            'mobil_number'=>$user->mobil_number,
            'gender'=>$user->gender,
            'birth_date'=>$user->birth_date,
            'city_id'=>$user->city_id,
            'image'=>$user->image,
        ]);
        }else{
            return \response()->json([
                'success'=>false,
                'errors'=>'User Kelmadi yoki yoq'
            ]);
        }
    }
    public function logout()
    {
        abort_if(Gate::denies('profile_password_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
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
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource($user->load(['roles', 'city']));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
//            'gender' => 'required',
            'mobil_number' => 'required|numeric|regex:/^\+\d{12}$/',
        ], [
            'name.required' => 'Ism kiritilmadi',
            'mobil_number.required' => 'Telefon raqam kiritilmadi',
            'mobil_number.unique' => 'Telefon raqam bor',
            'mobil_number.numeric' => 'Telefon raqam raqamda bo\'lishi kerak',
            'birth_date.required' => 'Tu\'g\'ulgan kun sanasi kiritilmadi',
            'gender.required' => 'Jins tanlanishi shart',
            'city_id.required' => 'Shaxar tanlanishi shart',
            'city_id.exists' => 'Shaxar mavjud emas',
            'surname.required' => 'Familya kiritilmadi',
        ]);
        if ($validation->fails()) {
            return $validation->errors();
        }
        $user=User::find($id);
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
        if ($data['mobil_number']!==$user->mobil_number && User::where('mobil_number',$data['mobil_number'])->exists()) {
            return response()->json([
                'success' => false,
                'errors' => 'Mobil raqam allaqachin mavjud',
            ]);
        }

        $user->update([
            'name'=>$data['name'],
            'surname'=>$data['surname'],
            'mobil_number'=>$data['mobil_number'],
            'birth_date' => isset($data['birth_date']) ? $data['birth_date'] : null,
//            'gender' => isset($data['gender']) ? (int) $data['gender'] : null,
            'city_id' =>isset($data['city_id']) ? (int) $data['city_id'] : null,
        ]);
        if (isset($data['image'])){
            $user->update([
                'image' => $data['image'],
            ]);
        }
        if ($data['password']){
            if ($data['password_confirmation']!==$data['password']){
                return response()->json([
                    'success' => false,
                    'errors'=>"'Parolni tasdiqlang' 'parol' bilan bir xil bolishi kerak"
                ]);
            }
            $user->update([
                'password' => bcrypt($data['password']),
            ]);
        }
if ($user) {
        return \response()->json([
            'success' => true,
            'message'=>'User muvaffaqiyatli taxrirlandi',
            'parol'=>$data['password'] ? 'Parol taxrirlandi' : 'parol taxrirlanmadi',
            'data' =>[
                'id'=>$user->id,
                'name'=>$user->name,
                'mobil_number'=>$user->mobil_number,
                'gender'=>$user->gender ? 'Erkak' : 'Ayol',
                'birth_date'=>$user->birth_date,
                'city_id'=>$user->city_id,
                'surname'=>$user->surname,
                'image'=>$user->image,
            ]
        ]);
}else{
    return \response()->json([
        'success' => false,
        'errors' => "User yo'q"
    ]);
}
    }

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
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
