<?php

namespace App\Http\Controllers;

use App\Jobs\SpeakingTextToAudioJob;
use App\Models\ListeningPartTwoSixQuestion;
use App\Models\SpeakingExercise;
use App\Models\SpeakingPartThree;
use Illuminate\Http\Request;

class SpeakingPartThreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $speaking_exercise = SpeakingExercise::with('part_three')->find($id);
        $exercise_id=$speaking_exercise->exercise_id;
        return view('admin.speaking.part_three.index' ,compact('speaking_exercise','exercise_id'));
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
    public function show(SpeakingPartThree $speakingPartThree)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SpeakingPartThree $speakingPartThree)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $question=SpeakingPartThree::find($id);
        $question->update(['question_text'=>$request->text]);


//        if ($question->wasChanged('question_text')) {
//            SpeakingTextToAudioJob::dispatch('three_question', $question->id);
//        }

        return redirect()->route('admin.speaking.part3.index',['id'=>$question->speaking_exercise_id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SpeakingPartThree $speakingPartThree)
    {
        //
    }
}
