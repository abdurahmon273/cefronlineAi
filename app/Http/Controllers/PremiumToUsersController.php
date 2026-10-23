<?php

namespace App\Http\Controllers;

use App\Models\BalanceEdit;
use App\Models\PremiumToUsers;
use App\Traits\BillingTrait;
use Illuminate\Http\Request;

class PremiumToUsersController extends Controller
{
    use BillingTrait;

    public function index()
    {
        return view('admin.premium_users.index');
    }
    public function package_index()
    {
        return view('admin.package_users.index');
    }

    public function create()
    {
        return view('admin.premium_users.create');
    }
    public function package_create()
    {
        return view('admin.package_users.create');
    }


    public function edit($id)
    {
        $premiumToUsers=PremiumToUsers::with('user','plan','promoCode')->find($id);
//        dd($premiumToUsers);
        return view('admin.premium_users.edit',compact('premiumToUsers'));
    }
    public function package_edit($id)
    {
        $packageToUsers=BalanceEdit::with('user','package','admin')->find($id);

        return view('admin.package_users.edit',compact('packageToUsers'));
    }

    public function package_destroy($id)
    {
        $packageToUser=BalanceEdit::find($id);

        if ($packageToUser) {
            $this->changeUserBalanceByAdmin($packageToUser->user_id,(-$packageToUser->price));

            $packageToUser->delete();
        }

        return redirect()->route('admin.package_users.index');
    }
    public function destroy($id)
    {
        $premiumToUser=PremiumToUsers::find($id);
        if ($premiumToUser) {
            $premiumToUser->delete();
        }

        return redirect()->route('admin.premium_users.index');
    }
}
