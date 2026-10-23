<?php

namespace App\Http\Controllers;

use App\Jobs\SpeakingTextToAudioJob;
use App\Models\SpeakingExercise;
use App\Models\SpeakingPartOneFirstQuestions;
use App\Models\SpeakingPartOneSecond;
use App\Models\SpeakingPartOneSecondQuestions;
use Illuminate\Http\Request;

class SpeakingPartOneSecondQuestionsController extends Controller
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
    public function create($id)
    {
        $speaking_exercise = SpeakingExercise::with('part_one_second')->find($id);


        return view('admin.speaking.part_one.create_question2',compact('speaking_exercise')  );
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'question' => 'required|min:3',
            'seconds' => 'required|numeric|min:10|max:120',
        ]);
        $question=SpeakingPartOneSecondQuestions::create([
            'modal_answer'=>$request->modal_answer,
            'question'=>$request->question,
            'time'=>$request->seconds,
            'part_one_second_id'=>$request->question_id,
        ]);

        SpeakingTextToAudioJob::dispatch('one_two',$question->id);

        return redirect()->route('admin.speaking.part1.index',['id'=>$request->speaking_exercise_id]);

    }

    /**
     * Display the specified resource.
     */
    public function show(SpeakingPartOneSecond $speakingPartOneSecond)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id,$speaking_exercise_id)
    {
        $question=SpeakingPartOneSecondQuestions::find($id);
        $speaking_exercise = SpeakingExercise::with('part_one_second')->find($speaking_exercise_id);


        return view('admin.speaking.part_one.edit_question2',compact('speaking_exercise','question')  );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[

            'question'=>'required|min:3',
            'seconds'=>'required|numeric|min:10|max:90',
        ]);
        $question=SpeakingPartOneSecondQuestions::find($id);

        $question->update([
            'modal_answer'=>$request->modal_answer,
            'question'=>$request->question,
            'time'=>$request->seconds,
        ]);

        if ($question->wasChanged('question')) {
            SpeakingTextToAudioJob::dispatch('one_two', $question->id);
        }

        return redirect()->route('admin.speaking.part1.index',['id'=>$request->speaking_exercise_id]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $question=SpeakingPartOneSecondQuestions::find($id);
        if ($question){
            $question->delete();
        }
        return redirect()->back();
    }
}
