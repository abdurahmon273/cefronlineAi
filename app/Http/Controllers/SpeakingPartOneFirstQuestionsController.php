<?php

namespace App\Http\Controllers;

use App\Jobs\SpeakingTextToAudioJob;
use App\Models\ReadingPartOneFiveTest;
use App\Models\SpeakingExercise;
use App\Models\SpeakingPartOneFirstQuestions;
use Illuminate\Http\Request;

class SpeakingPartOneFirstQuestionsController extends Controller
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
    public function create1($id)
    {
        $speaking_exercise = SpeakingExercise::with('part_one_first')->find($id);


        return view('admin.speaking.part_one.create_question1',compact('speaking_exercise')  );
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
    public function store1(Request $request)
    {
        $this->validate($request, [
            'question' => 'required|min:3',
            'seconds' => 'required|numeric|min:10|max:90',
        ]);
        $question=SpeakingPartOneFirstQuestions::create([
            'question'=>$request->question,
            'modal_answer'=>$request->modal_answer,
            'time'=>$request->seconds,
            'part_one_first_id'=>$request->question_id,
        ]);

        SpeakingTextToAudioJob::dispatch('one_one',$question->id);

        return redirect()->route('admin.speaking.part1.index',['id'=>$request->speaking_exercise_id]);

    }
    /**
     * Display the specified resource.
     */
    public function show(SpeakingPartOneFirstQuestions $speakingPartOneFirstQuestions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SpeakingPartOneFirstQuestions $speakingPartOneFirstQuestions)
    {
        //
    }
    public function edit1($id,$speaking_exercise_id)
    {
        $question=SpeakingPartOneFirstQuestions::find($id);
        $speaking_exercise = SpeakingExercise::with('part_one_first')->find($speaking_exercise_id);


        return view('admin.speaking.part_one.edit_question1',compact('speaking_exercise','question')  );
//        dd($speaking_exercise);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SpeakingPartOneFirstQuestions $speakingPartOneFirstQuestions)
    {
        //
    }
    public function update1(Request $request,$id)
    {
        $this->validate($request,[
            'question'=>'required|min:3',
            'seconds'=>'required|numeric|min:10|max:90',
        ]);
        $question=SpeakingPartOneFirstQuestions::find($id);

        $question->update([
            'modal_answer'=>$request->modal_answer,
            'question'=>$request->question,
            'time'=>$request->seconds,
        ]);

        if ($question->wasChanged('question')) {
            SpeakingTextToAudioJob::dispatch('one_one', $question->id);
        }

        return redirect()->route('admin.speaking.part1.index',['id'=>$request->speaking_exercise_id]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $question=SpeakingPartOneFirstQuestions::find($id);
        if ($question){
            $question->delete();
        }
        return redirect()->back();
    }
}
