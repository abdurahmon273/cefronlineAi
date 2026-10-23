<?php

namespace App\Http\Controllers;

use App\Models\ListeningPartOneFifeTest;
use App\Models\ReadingExercise;
use App\Models\ReadingPartFourTest;
use Illuminate\Http\Request;

class ReadingPartFourTestController extends Controller
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

    public function create1($id) // part 1
    {
        $reading_exercise=ReadingExercise::with('part_four.tests')->find($id);
        return view('admin.reading.part_four.create' ,compact('reading_exercise'));
    }

    public function create2($id) // part 1
    {
        $reading_exercise=ReadingExercise::with('part_four.tests')->find($id);
        return view('admin.reading.part_four.create_mchq' ,compact('reading_exercise'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function store1(Request $request) // part 1
    {
        $this->validate($request,[
            'answer_1'=>'required|min:2',
            'answer_2'=>'required|min:2',
            'answer_3'=>'required|min:2',
            'correct_answer'=>'required',
            'reading_part_four_id'=>'required',
        ]);
        ReadingPartFourTest::create([
            'question'=>$request->question,
            'answer_1'=>$request->answer_1,
            'answer_2'=>$request->answer_2,
            'answer_3'=>$request->answer_3,
            'answer_4'=>'not',
            'type'=>'1',
            'correct_answer'=>$request->correct_answer, // 1,2,3
            'reading_part_four_id'=>$request->reading_part_four_id,
        ]);
        return redirect()->route('admin.reading.part4.index',['id'=>$request->reading_id]);
    }
    public function store2(Request $request) // part 1
    {
        $this->validate($request,[
            'answer_1'=>'required|min:2',
            'answer_2'=>'required|min:2',
            'answer_3'=>'required|min:2',
//            'answer_4'=>'required|min:2',
            'correct_answer'=>'required',
            'reading_part_four_id'=>'required',
        ]);
//        dd($request->all());
        ReadingPartFourTest::create([
            'question'=>$request->question,
            'answer_1'=>$request->answer_1,
            'answer_2'=>$request->answer_2,
            'answer_3'=>$request->answer_3,
            'answer_4'=>$request->answer_4 ?? null,
            'type'=>'2',
            'correct_answer'=>$request->correct_answer, // 1,2,3
            'reading_part_four_id'=>$request->reading_part_four_id,
        ]);
        return redirect()->route('admin.reading.part4.index',['id'=>$request->reading_id]);
    }
    /**
     * Display the specified resource.
     */
    public function show(ReadingPartFourTest $readingPartFourTest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReadingPartFourTest $readingPartFourTest)
    {
        //
    }
    public function edit1($id) // part 5
    {
        $test=ReadingPartFourTest::with('part_four.reading_exercise')->find($id);
        $reading_exercise=$test->part_four->reading_exercise;
        return view('admin.reading.part_four.edit',compact('test','reading_exercise'));
    }
    public function edit2($id) // part 5
    {
        $test=ReadingPartFourTest::with('part_four.reading_exercise')->find($id);
        $reading_exercise=$test->part_four->reading_exercise;
        return view('admin.reading.part_four.edit_mchq',compact('test','reading_exercise'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReadingPartFourTest $readingPartFourTest)
    {
        //
    }

    function update1(Request $request, $id)
    {
        $this->validate($request,[
            'answer_1'=>'required|min:2',
            'answer_2'=>'required|min:2',
            'answer_3'=>'required|min:2',
            'correct_answer'=>'required',
            'reading_part_four_id'=>'required',
        ]);

        $test=ReadingPartFourTest::find($id);
        $test->update($request->all());
        return redirect()->route('admin.reading.part4.index',['id'=>$request->reading_id]);
    }

    function update2(Request $request, $id)
    {
        $this->validate($request,[
            'answer_1'=>'required|min:2',
            'answer_2'=>'required|min:2',
            'answer_3'=>'required|min:2',
            'correct_answer'=>'required',
            'reading_part_four_id'=>'required',
        ]);

        $test=ReadingPartFourTest::find($id);
        $test->update($request->all());
        return redirect()->route('admin.reading.part4.index',['id'=>$request->reading_id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $test=ReadingPartFourTest::find($id);
        $test->delete();
        return back();
    }
}
