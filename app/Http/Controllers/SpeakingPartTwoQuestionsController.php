<?php

namespace App\Http\Controllers;

use App\Jobs\SpeakingTextToAudioJob;
use App\Models\SpeakingExercise;
use App\Models\SpeakingPartOneFirstQuestions;
use App\Models\SpeakingPartOneSecondQuestions;
use App\Models\SpeakingPartTwoQuestions;
use Illuminate\Http\Request;

class SpeakingPartTwoQuestionsController extends Controller
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
        $speaking_exercise = SpeakingExercise::with('part_two')->find($id);

        return view('admin.speaking.part_two.create_question',compact('speaking_exercise')  );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'question' => 'required|min:3',
        ]);
        $question=SpeakingPartTwoQuestions::create([
            'question'=>$request->question,
            'part_two_id'=>$request->question_id,
        ]);

//        SpeakingTextToAudioJob::dispatch('two',$question->id);

        return redirect()->route('admin.speaking.part2.index',['id'=>$request->speaking_exercise_id]);

    }

    /**
     * Display the specified resource.
     */
    public function show(SpeakingPartTwoQuestions $speakingPartTwoQuestions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id,$speaking_exercise_id)
    {
        $question=SpeakingPartTwoQuestions::find($id);
        $speaking_exercise = SpeakingExercise::with('part_two')->find($speaking_exercise_id);


        return view('admin.speaking.part_two.edit_question',compact('speaking_exercise','question')  );

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'question'=>'required|min:3',
        ]);
        $question=SpeakingPartTwoQuestions::find($id);

        $question->update([
            'question'=>$request->question,
        ]);

//        if ($question->wasChanged('question')) {
//            SpeakingTextToAudioJob::dispatch('two', $question->id);
//        }

        return redirect()->route('admin.speaking.part2.index',['id'=>$request->speaking_exercise_id]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $question=SpeakingPartTwoQuestions::find($id);
        if ($question){
            $question->delete();
        }
        return redirect()->back();
    }
}
