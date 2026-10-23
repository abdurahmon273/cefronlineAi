<?php

namespace App\Http\Controllers;

use App\Models\ListeningExercise;
use App\Models\ListeningPartThreeFourAnswers;
use App\Models\ReadingExercise;
use App\Models\ReadingPartTwoThreeAnswer;
use Illuminate\Http\Request;

class ReadingPartTwoThreeAnswerController extends Controller
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
        $reading_exercise=ReadingExercise::with('part_two.answers')->find($id);
        $array=ReadingPartTwoThreeAnswer::LETTERS;
        $to_delete_keys=$reading_exercise->part_two->answers->map(function($answer){
            return $answer->letter;
        })->toArray();
        $array = array_diff_key($array, array_flip($to_delete_keys));

        return view('admin.reading.part_two.create_answer',compact('reading_exercise','array'));
    }
    public function create1($id)
    {
        $reading_exercise=ReadingExercise::with('part_three.answers')->find($id);
        $array=ReadingPartTwoThreeAnswer::LETTERS;
        $to_delete_keys=$reading_exercise->part_three->answers->map(function($answer){
            return $answer->letter;
        })->toArray();
        $array = array_diff_key($array, array_flip($to_delete_keys));

        return view('admin.reading.part_three.create_answer',compact('reading_exercise','array'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'letter_key'=>'required',
            'answer'=>'required',
            'reading_part_two_three_id'=>'required',
        ]);
        $answer=ReadingPartTwoThreeAnswer::create([
            'letter'=>$request->letter_key,
            'answer'=>$request->answer,
            'reading_part_two_three_id'=>$request->reading_part_two_three_id,
        ]);
        return redirect()->route('admin.reading.part2.index',['id'=>$request->reading_id]);

    }
    public function store1(Request $request)
    {
        $this->validate($request,[
            'letter_key'=>'required',
            'answer'=>'required',
            'reading_part_two_three_id'=>'required',
        ]);
        $answer=ReadingPartTwoThreeAnswer::create([
            'letter'=>$request->letter_key,
            'answer'=>$request->answer,
            'reading_part_two_three_id'=>$request->reading_part_two_three_id,
        ]);
        return redirect()->route('admin.reading.part3.index',['id'=>$request->reading_id]);

    }

    /**
     * Display the specified resource.
     */
    public function show(ReadingPartTwoThreeAnswer $readingPartTwoThreeAnswer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id,$reading_id)
    {
        $answer=ReadingPartTwoThreeAnswer::find($id);
        $reading_exercise=ReadingExercise::with('part_two.answers')->find($reading_id);
        $array1=ReadingPartTwoThreeAnswer::LETTERS;
        $to_delete_keys=$reading_exercise->part_two->answers->map(function($answer){
            return $answer->letter;
        })->toArray();
        $array = array_diff_key($array1, array_flip($to_delete_keys));
        $array[$answer->letter]=$array1[$answer->letter];

        return view('admin.reading.part_two.edit_answer',compact('answer','reading_exercise','array'));

    }
    public function edit1($id,$reading_id)
    {
        $answer=ReadingPartTwoThreeAnswer::find($id);
        $reading_exercise=ReadingExercise::with('part_three.answers')->find($reading_id);
        $array1=ReadingPartTwoThreeAnswer::LETTERS;
        $to_delete_keys=$reading_exercise->part_three->answers->map(function($answer){
            return $answer->letter;
        })->toArray();
        $array = array_diff_key($array1, array_flip($to_delete_keys));
        $array[$answer->letter]=$array1[$answer->letter];

        return view('admin.reading.part_three.edit_answer',compact('answer','reading_exercise','array'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'letter_key'=>'required',
            'answer'=>'required',
        ]);
        $answer=ReadingPartTwoThreeAnswer::find($id);
        $answer->update([
            'letter'=>$request->letter_key,
            'answer'=>$request->answer,
        ]);
        return redirect()->route('admin.reading.part2.index',['id'=>$request->reading_id]);
    }
    public function update1(Request $request, $id)
    {
        $this->validate($request,[
            'letter_key'=>'required',
            'answer'=>'required',
        ]);
        $answer=ReadingPartTwoThreeAnswer::find($id);
        $answer->update([
            'letter'=>$request->letter_key,
            'answer'=>$request->answer,
        ]);
        return redirect()->route('admin.reading.part3.index',['id'=>$request->reading_id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $answer=ReadingPartTwoThreeAnswer::with('text')->find($id);
        if ($answer->text){

        }else{
            $answer->delete();
        }

        return redirect()->back();
    }
}
