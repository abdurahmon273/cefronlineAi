<?php

namespace App\Http\Controllers\Api\V1\Admin\Aboutus;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AboutUs\AddressResource;
use App\Http\Resources\Admin\AboutUs\PhoneNumberResource;
use App\Http\Resources\Admin\AboutUs\TelegramResource;
use App\Models\Information;
use Illuminate\Http\Request;

class AboutUsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function informations()
    {
        $informations=Information::all();

        return [
            'tel'=>$informations->where('type','0')->pluck('information_link'),
            'url'=>$informations->where('type','1')->pluck('information_link'),
            'address'=>$informations->where('type','2')->pluck('information_link'),
        ];
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
