<?php

namespace App\Http\Controllers;

use App\Models\ListeningExercise;
use App\Models\ListeningPartThreeFourAnswers;
use Illuminate\Http\Request;

class ListeningPartThreeFourAnswersController extends Controller
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
    public function create1($id) // part 3
    {
        $listening_exercise=ListeningExercise::with('listening_part_three_four.part_three_four_answers')->find($id);
        $array=ListeningPartThreeFourAnswers::LETTERS;
        $to_delete_keys=$listening_exercise->listening_part_three_four->part_three_four_answers->map(function($answer){
            return $answer->letter;
        })->toArray();
        $array = array_diff_key($array, array_flip($to_delete_keys));

        return view('admin.part_three_answers.create',compact('listening_exercise','array'));
    }
    public function create2($id) // part 4
    {
        $listening_exercise=ListeningExercise::with('part_four.part_three_four_answers')->find($id);
        $array=ListeningPartThreeFourAnswers::LETTERS;
        $to_delete_keys=$listening_exercise->part_four->part_three_four_answers->map(function($answer){
            return $answer->letter;
        })->toArray();
        $array = array_diff_key($array, array_flip($to_delete_keys));

        return view('admin.part_four_answers.create',compact('listening_exercise','array'));
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
        $this->validate($request,[
            'letter_key'=>'required',
            'answer'=>'required',
            'listening_part_three_four_id'=>'required',
        ]);
        $answer=ListeningPartThreeFourAnswers::create([
            'letter'=>$request->letter_key,
            'answer'=>$request->answer,
            'listening_part_three_four_id'=>$request->listening_part_three_four_id,
        ]);
        return redirect()->route('admin.listenings.part3.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);
    }
    public function store2(Request $request)
    {
        $this->validate($request,[
            'letter_key'=>'required',
            'listening_part_three_four_id'=>'required',
        ]);
        $answer=ListeningPartThreeFourAnswers::create([
            'letter'=>$request->letter_key,
            'answer'=>$request->answer ?? ' ',
            'listening_part_three_four_id'=>$request->listening_part_three_four_id,
        ]);
        return redirect()->route('admin.listenings.part4.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ListeningPartThreeFourAnswers $listeningPartThreeFourAnswers)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ListeningPartThreeFourAnswers $listeningPartThreeFourAnswers)
    {
        //
    }
    public function edit1($id,$listenin_id)
    {
        $answer=ListeningPartThreeFourAnswers::find($id);
        $listening_exercise=ListeningExercise::with('listening_part_three_four.part_three_four_answers')->find($listenin_id);
        $array1=ListeningPartThreeFourAnswers::LETTERS;
        $to_delete_keys=$listening_exercise->listening_part_three_four->part_three_four_answers->map(function($answer){
            return $answer->letter;
        })->toArray();
        $array = array_diff_key($array1, array_flip($to_delete_keys));
        $array[$answer->letter]=$array1[$answer->letter];

        return view('admin.part_three_answers.edit',compact('answer','listening_exercise','array'));

    }
    public function edit2($id,$listenin_id)
    {
        $answer=ListeningPartThreeFourAnswers::find($id);
        $listening_exercise=ListeningExercise::with('part_four.part_three_four_answers')->find($listenin_id);
        $array1=ListeningPartThreeFourAnswers::LETTERS;
        $to_delete_keys=$listening_exercise->part_four->part_three_four_answers->map(function($answer){
            return $answer->letter;
        })->toArray();
        $array = array_diff_key($array1, array_flip($to_delete_keys));
        $array[$answer->letter]=$array1[$answer->letter];

        return view('admin.part_four_answers.edit',compact('answer','listening_exercise','array'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ListeningPartThreeFourAnswers $listeningPartThreeFourAnswers)
    {
        //
    }
    public function update1(Request $request, $id)
    {
        $this->validate($request,[
            'letter_key'=>'required',
            'answer'=>'required',
        ]);
        $answer=ListeningPartThreeFourAnswers::find($id);
        $answer->update([
            'letter'=>$request->letter_key,
            'answer'=>$request->answer,
        ]);
        return redirect()->route('admin.listenings.part3.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);
    }
    public function update2(Request $request, $id)
    {
        $this->validate($request,[
            'letter_key'=>'required',
        ]);
        $answer=ListeningPartThreeFourAnswers::find($id);
        $answer->update([
            'letter'=>$request->letter_key,
            'answer'=>$request->answer ?? ' ',
        ]);
        return redirect()->route('admin.listenings.part4.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $answer=ListeningPartThreeFourAnswers::with('question')->find($id);
        if ($answer->question){
            return redirect()->back();
        }else{
            $answer->delete();
            return redirect()->back();
        }
    }
}
