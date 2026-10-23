<?php

namespace App\Http\Controllers;

use App\Models\SpeakingExercise;
use App\Models\SpeakingPartThree;
use App\Models\SpeakingPartThreeQuestions;
use App\Models\SpeakingPartTwoQuestions;
use Illuminate\Http\Request;

class SpeakingPartThreeQuestionsController extends Controller
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
        $speaking_exercise = SpeakingExercise::with('part_three')->find($id);

        return view('admin.speaking.part_three.create_forDetail',compact('speaking_exercise')  );
    }
    public function create1($id)
    {
        $speaking_exercise = SpeakingExercise::with('part_three')->find($id);

        return view('admin.speaking.part_three.create_againstDetail',compact('speaking_exercise')  );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'question' => 'required|min:3',
        ]);
        SpeakingPartThreeQuestions::create([
            'question'=>$request->question,
            'part_three_id'=>$request->question_id,
            'type'=>'0'
        ]);
        return redirect()->route('admin.speaking.part3.index',['id'=>$request->speaking_exercise_id]);
    }
    public function store1(Request $request)
    {
        $this->validate($request, [
            'question' => 'required|min:3',
        ]);
        SpeakingPartThreeQuestions::create([
            'question'=>$request->question,
            'part_three_id'=>$request->question_id,
            'type'=>'1'
        ]);
        return redirect()->route('admin.speaking.part3.index',['id'=>$request->speaking_exercise_id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SpeakingPartThreeQuestions $speakingPartThreeQuestions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $question=SpeakingPartThree::find($id);

        return view('admin.speaking.part_three.edit_text',compact('question'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'question'=>'required|min:3',
        ]);
        $question=SpeakingPartThreeQuestions::find($id);

        $question->update([
            'question'=>$request->question,
        ]);
        return redirect()->route('admin.speaking.part3.index',['id'=>$request->speaking_exercise_id]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $question=SpeakingPartThreeQuestions::find($id);
        if ($question){
            $question->delete();
        }
        return redirect()->back();
    }
}
