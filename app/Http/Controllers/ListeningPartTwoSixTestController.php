<?php

namespace App\Http\Controllers;

use App\Models\ListeningPartTwoSixTest;
use Illuminate\Http\Request;

class ListeningPartTwoSixTestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(ListeningPartTwoSixTest $listeningPartTwoSixTest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ListeningPartTwoSixTest $listeningPartTwoSixTest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ListeningPartTwoSixTest $listeningPartTwoSixTest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id,$listening_id,$exercise_id)
    {
        $test=ListeningPartTwoSixTest::find($id);
        $test->delete();

        return redirect()->route('admin.listenings.part2.index',['id'=>$listening_id,'exercise_id'=>$exercise_id]);

    }
    public function destroy1($id,$listening_id,$exercise_id)
    {
        $test=ListeningPartTwoSixTest::find($id);
        $test->delete();

        return redirect()->route('admin.listenings.part6.index',['id'=>$listening_id,'exercise_id'=>$exercise_id]);

    }
}
