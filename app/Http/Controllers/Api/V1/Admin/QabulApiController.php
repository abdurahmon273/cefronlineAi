<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\QabulApiResourse;
use App\Models\News;
use App\Models\Qabul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QabulApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function join($id)
    {
        $news=News::where('id',$id)->first();
        if ($news){
            $qabul=Qabul::create([
                'news_id' => $news->id,
                'user_id'=>Auth::id()
            ]);
        return response()->json([
            'success' => true,
            'data'=>New QabulApiResourse($qabul)
        ]);
        }else{
            return response()->json([
                'success' => false,
                'errors'=>'Yangilikni idsi kelmadi'
            ]);
        }

    }
    public function store($id)
    {
        $qabul = Qabul::find($id);
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
