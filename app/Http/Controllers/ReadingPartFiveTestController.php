<?php

namespace App\Http\Controllers;

use App\Models\ReadingExercise;
use App\Models\ReadingPartFiveTest;
use App\Models\ReadingPartFourTest;
use Illuminate\Http\Request;

class ReadingPartFiveTestController extends Controller
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
        $reading_exercise=ReadingExercise::with('part_five.tests')->find($id);
        return view('admin.reading.part_five.create_test' ,compact('reading_exercise'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request) // part 1
    {
        $this->validate($request,[
            'answer_1'=>'required|min:2',
            'answer_2'=>'required|min:2',
            'answer_3'=>'required|min:2',
//            'answer_4'=>'required|min:2',
            'correct_answer'=>'required',
            'reading_part_four_id'=>'required',
        ]);

        ReadingPartFiveTest::create([
            'question'=>$request->question,
            'answer_1'=>$request->answer_1,
            'answer_2'=>$request->answer_2,
            'answer_3'=>$request->answer_3,
            'answer_4'=>$request->answer_4 ?? '',
            'type'=>'2',
            'correct_answer'=>$request->correct_answer, // 1,2,3
            'reading_part_one_five_question_id'=>$request->reading_part_four_id,
        ]);
        return redirect()->route('admin.reading.part5.index',['id'=>$request->reading_id]);
    }
    /**
     * Display the specified resource.
     */
    public function show(ReadingPartFiveTest $readingPartFiveTest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $test=ReadingPartFiveTest::with('part_five.reading_exercise')->find($id);
        $reading_exercise=$test->part_five->reading_exercise;
        return view('admin.reading.part_five.edit_test',compact('test','reading_exercise'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'answer_1'=>'required|min:2',
            'answer_2'=>'required|min:2',
            'answer_3'=>'required|min:2',
//            'answer_4'=>'required|min:2',
            'correct_answer'=>'required',
            'reading_part_four_id'=>'required',
        ]);

        $create_array=$request->all();
        $create_array['answer_4']=$request->answer_4 ?? '';

        $test=ReadingPartFiveTest::find($id);
        $test->update($create_array);
        return redirect()->route('admin.reading.part5.index',['id'=>$request->reading_id]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $test=ReadingPartFiveTest::find($id);
        $test->delete();
        return back();
    }
}
