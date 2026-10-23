<?php

namespace App\Http\Controllers;

use App\Models\ReadingExercise;
use App\Models\SpeakingExercise;
use App\Models\SpeakingPartOneFirst;
use Illuminate\Http\Request;

class SpeakingPartOneFirstController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $speaking_exercise = SpeakingExercise::with('part_one_first.questions','part_one_second.questions','part_one_second.images')->find($id);

        $exercise_id=$speaking_exercise->exercise_id;
        return view('admin.speaking.part_one.index' ,compact('speaking_exercise','exercise_id'));
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
    public function show(SpeakingPartOneFirst $speakingPartOneFirst)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SpeakingPartOneFirst $speakingPartOneFirst)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SpeakingPartOneFirst $speakingPartOneFirst)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SpeakingPartOneFirst $speakingPartOneFirst)
    {
        //
    }
}
