<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListeningExercise;
use App\Models\ListeningPartOneFifeQuestion;
use App\Models\ListeningPartOneFifeTest;
use Illuminate\Http\Request;

class ListeningPartOneFifeTestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function index1($id,$exercise_id)
    {
        $question=ListeningPartOneFifeQuestion::with('listeningExercise')->where('type','0')->where('listening_exercise_id',$id)->get()->first();
        $listening_id=$id;
        $question_id=$question->id;
        $tests=ListeningPartOneFifeTest::where('listening_part_one_fife_question_id',$question_id)->get();

        return view('admin.part_one_tests.index' ,compact('question_id','question','listening_id','exercise_id','tests'));
    }

    public function index2($id,$exercise_id)
    {
//        dd(ListeningExercise::with('part_one','part_two','part_three','part_four','part_five','part_six')->find($id));
        $question=ListeningPartOneFifeQuestion::where('listening_exercise_id',$id)->where('type','1')->get()->first();
        $listening_id=$id;
        $question_id=$question->id;
        $tests=ListeningPartOneFifeTest::where('listening_part_one_fife_question_id',$question_id)->get();

        return view('admin.part_fife_tests.index' ,compact('question_id','question','listening_id','exercise_id','tests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function create1($id,$id1,$id2) // part 1
    {
       $question_id=$id;
       $listening_id=$id1;
       $exercise_id=$id2;
       return view('admin.part_one_tests.create' ,compact('question_id','listening_id','exercise_id'));
    }
    public function create2($id,$id1,$id2) // part 5
    {
       $question_id=$id;
       $listening_id=$id1;
       $exercise_id=$id2;
       return view('admin.part_fife_tests.create' ,compact('question_id','listening_id','exercise_id'));
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
            'listening_part_one_fife_question_id'=>'required',
        ]);
        ListeningPartOneFifeTest::create([
            'question'=>'not',
            'answer_1'=>$request->answer_1,
            'answer_2'=>$request->answer_2,
            'answer_3'=>$request->answer_3,
            'type'=>'0',
            'correct_answer'=>$request->correct_answer, // 1,2,3
            'listening_part_one_fife_question_id'=>$request->listening_part_one_fife_question_id,
            ]);
        return redirect()->route('admin.listenings.part1.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);
    }
    public function store2(Request $request) // part 5
    {
//        dd(request()->all());
        $this->validate($request,[
            'answer_1'=>'required|min:2',
            'answer_2'=>'required|min:2',
            'answer_3'=>'required|min:2',
            'correct_answer'=>'required',
            'listening_part_one_fife_question_id'=>'required',
            'question'=>'required|min:3',
        ]);
        ListeningPartOneFifeTest::create([
            'question'=>$request->question,
            'answer_1'=>$request->answer_1,
            'answer_2'=>$request->answer_2,
            'answer_3'=>$request->answer_3,
            'type'=>'1',
            'correct_answer'=>$request->correct_answer, // 1,2,3
            'listening_part_one_fife_question_id'=>$request->listening_part_one_fife_question_id,
            ]);
        return redirect()->route('admin.listenings.part5.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ListeningPartOneFifeTest $listeningPartOneFifeTest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        dd($id);
    }
    public function edit1($id) // part 5
    {
        $test=ListeningPartOneFifeTest::with('question','question.listeningExercise')->find($id);
        $question=$test->relationsToArray('question')['question'];
        $question_id=$question['id'];
        $listening_id=$question['listening_exercise_id'];
        $exercise_id=$question['listening_exercise']['exercise_id'];
        return view('admin.part_one_tests.edit',compact('test','question_id','listening_id','exercise_id'));
    }
    public function edit2($id) // part 5
    {
        $test=ListeningPartOneFifeTest::with('question','question.listeningExercise')->find($id);
        $question=$test->relationsToArray('question')['question'];
        $question_id=$question['id'];
        $listening_id=$question['listening_exercise_id'];
        $exercise_id=$question['listening_exercise']['exercise_id'];
        return view('admin.part_fife_tests.edit',compact('test','question_id','listening_id','exercise_id'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ListeningPartOneFifeTest $listeningPartOneFifeTest)
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
            'listening_part_one_fife_question_id'=>'required',
        ]);

        $test=ListeningPartOneFifeTest::find($id);
        $test->update($request->all());
        return redirect()->route('admin.listenings.part1.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);
    }

    function update2(Request $request, $id)
    {
        $this->validate($request,[
            'answer_1'=>'required|min:2',
            'answer_2'=>'required|min:2',
            'answer_3'=>'required|min:2',
            'correct_answer'=>'required',
            'listening_part_one_fife_question_id'=>'required',
            'question'=>'required|min:3',
        ]);

        $test=ListeningPartOneFifeTest::find($id);
        $test->update($request->all());
        return redirect()->route('admin.listenings.part5.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $test=ListeningPartOneFifeTest::find($id);
        $test->delete();
        return back();
    }
}
