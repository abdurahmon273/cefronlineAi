<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\City;
use App\Models\Role;
use App\Models\Shablon;
use App\Models\User;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        return view('admin.users.index', );
    }
    public function extra_admin_index()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        return view('admin.ExtraAdmin.index', );
    }
    public function workers_index()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        return view('admin.workers.index', );
    }
    public function referrals_index()
    {
        abort_if(Gate::denies('referral_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        return view('admin.referral.index', );
    }
    public function pay_referral(Request $request)
    {
        abort_if(Gate::denies('referral_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $id = $request->id ?? null;
        $startDate=$request->startDate ?? null;
        $endDate=$request->endDate ?? null;

        return view('admin.referral.payReferral', compact('id','startDate','endDate'));
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (\auth()->user()->is_admin){
            $roles = Role::pluck('title', 'id');
        }else{
            $roles = Role::whereNotIn('title', ['admin', 'extra_admin'])->pluck('title', 'id');

        }

        $cities = City::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.users.create', compact('cities', 'roles'));
    }
    public function extra_admin_create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::pluck('title', 'id');

        $cities = City::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.ExtraAdmin.create', compact('cities', 'roles'));
    }
    public function workers_create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::pluck('title', 'id');

        $cities = City::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.workers.create', compact('cities', 'roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->all();
        if ($request->image){
            $file = $request->file('image');
            $image_name = uniqid() . $file->getClientOriginalName();
            $data['image'] = $image_name;
            $file->move(public_path('storage/userImage'), $image_name);
        }
        $data['created_by_id'] = Auth::user()->id;

        $user = User::create($data);

        $code = rand(1000, 9999);

        $user->update([
            'mobile_verified_at' => Carbon::now(),
            'mobile_verify_code'=>$code
        ]);

        $user->roles()->sync($request->input('roles', []));
        return redirect()->route('admin.users.index');
    }
    public function extra_admin_store(Request $request)
    {
        // Validate the request data
        $this->validate($request, [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'mobil_number' => 'required|min:13|max:13|unique:users',
        ]);

        $data = $request->all();

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $extraAdminRole = Role::where('title', 'extra_admin')->first();

        if ($extraAdminRole) {
            $user->roles()->sync([$extraAdminRole->id]);
        } else {
            return redirect()->back()->withErrors(['roles' => 'The "extra_admin" role does not exist.']);
        }

        return redirect()->route('admin.extra_admins.index')->with('success', 'Extra admin created successfully.');
    }
    public function workers_store(Request $request)
    {
        // Validate the request data
        $this->validate($request, [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'mobil_number' => 'required|min:13|max:13|unique:users',
        ]);

        $data = $request->all();

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $user->roles()->sync($request->input('roles', []));


        return redirect()->route('admin.workers.index')->with('success', 'Extra admin created successfully.');
    }


    public function edit(User $user)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::pluck('title', 'id');

        $cities = City::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $user->load('roles', 'city');

        return view('admin.users.edit', compact('cities', 'roles', 'user'));
    }

    public function extra_admin_edit($id)
    {
        $user=User::find($id);
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::pluck('title', 'id');

        $cities = City::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $user->load('roles', 'city');

        return view('admin.ExtraAdmin.edit', compact('cities', 'roles', 'user'));
    }
    public function workers_edit($id)
    {
        $user=User::find($id);
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::pluck('title', 'id');

        $user->load('roles', 'city');

        return view('admin.workers.edit', compact( 'roles', 'user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->all();

        if ($request->image){
            $file = $request->file('image');
            $image_name = uniqid() . $file->getClientOriginalName();
            $data['image'] = $image_name;
            $file->move(public_path('storage/userImage'), $image_name);
        }

        if (!$user->mobile_verified_at || !$user->mobile_verify_code ){
            $code = rand(1000, 9999);
            $user->update([
                'mobile_verified_at' => Carbon::now(),
                'mobile_verify_code'=>$code
            ]);
        }

        $user->update($data);
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index');
    }
    public function extra_admin_update(Request $request, $id)
    {
        // Find the user
        $user = User::findOrFail($id);

        // Validate the request data
        $this->validate($request, [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6', // Make password optional
            'mobil_number' => 'required|min:13|max:13|unique:users,mobil_number,' . $id,
        ]);

        // Get all request data
        $data = $request->all();

        // Hash the password only if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Remove the password field to prevent overwriting it with null
            unset($data['password']);
        }

        // Update the user
        $user->update($data);

        // Redirect to the users index page
        return redirect()->route('admin.extra_admins.index')->with('success', 'Extra admin updated successfully.');
    }
    public function workers_update(Request $request, $id)
    {
        // Find the user
        $user = User::findOrFail($id);

        // Validate the request data
        $this->validate($request, [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6', // Make password optional
            'mobil_number' => 'required|min:13|max:13|unique:users,mobil_number,' . $id,
        ]);

        // Get all request data
        $data = $request->all();

        // Hash the password only if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Remove the password field to prevent overwriting it with null
            unset($data['password']);
        }

        // Update the user
        $user->update($data);

        $user->roles()->sync($request->input('roles', []));

        // Redirect to the users index page
        return redirect()->route('admin.workers.index')->with('success', 'Extra admin updated successfully.');
    }


    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->load('roles', 'city');

        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return back();
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        $users = User::find(request('ids'));

        foreach ($users as $user) {
            $user->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
