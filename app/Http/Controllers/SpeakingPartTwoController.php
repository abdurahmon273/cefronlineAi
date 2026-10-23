<?php

namespace App\Http\Controllers;

use App\Models\SpeakingExercise;
use App\Models\SpeakingPartTwo;
use Illuminate\Http\Request;

class SpeakingPartTwoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $speaking_exercise = SpeakingExercise::with('part_two.questions','part_two.images')->find($id);

        $exercise_id=$speaking_exercise->exercise_id;
        return view('admin.speaking.part_two.index' ,compact('speaking_exercise','exercise_id'));
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
    public function show(SpeakingPartTwo $speakingPartTwo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SpeakingPartTwo $speakingPartTwo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SpeakingPartTwo $speakingPartTwo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SpeakingPartTwo $speakingPartTwo)
    {
        //
    }
}
