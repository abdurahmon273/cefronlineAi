<?php

namespace App\Http\Controllers;

use App\Models\ListeningExercise;
use App\Models\ListeningPartThreeFourAnswers;
use App\Models\ListeningPartThreeFourQuestion;
use Illuminate\Http\Request;

class ListeningPartThreeFourQuestionController extends Controller
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
        $listening_exercise = ListeningExercise::with(['listening_part_three_four.part_three_four_answers' => function ($query) {
            $query->whereDoesntHave('question');
        }])->find($id);

        $answers = $listening_exercise->listening_part_three_four->part_three_four_answers;

        return view('admin.part_three_questions.create', compact('listening_exercise', 'answers'));
    }
    public function create2($id)
    {
        $listening_exercise = ListeningExercise::with(['part_four.part_three_four_answers' => function ($query) {
            $query->whereDoesntHave('question');
        }])->find($id);

        $answers = $listening_exercise->part_four->part_three_four_answers;

        return view('admin.part_four_questions.create', compact('listening_exercise', 'answers'));
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
//        dd($request->all());
        $this->validate($request, [
            'title' => 'required',
            'correct_answer_id' => 'required',
            'listening_part_three_four_id'=>'required',
        ]);

        $speaker=ListeningPartThreeFourQuestion::create([
            'title'=>$request->title,
            'correct_answer_id'=>$request->correct_answer_id,
            'listening_part_three_four_id'=>$request->listening_part_three_four_id,
        ]);
        return redirect()->route('admin.listenings.part3.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);
    }
    public function store2(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'correct_answer_id' => 'required',
            'listening_part_three_four_id'=>'required',
        ]);

        $speaker=ListeningPartThreeFourQuestion::create([
            'title'=>$request->title,
            'correct_answer_id'=>$request->correct_answer_id,
            'listening_part_three_four_id'=>$request->listening_part_three_four_id,
        ]);
        return redirect()->route('admin.listenings.part4.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ListeningPartThreeFourQuestion $listeningPartThreeFourQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ListeningPartThreeFourQuestion $listeningPartThreeFourQuestion)
    {
        //
    }
    public function edit1($id, $listenin_id)
    {
        $speaker = ListeningPartThreeFourQuestion::find($id);

        $listening_exercise = ListeningExercise::with(['listening_part_three_four.part_three_four_answers' => function ($query) use ($speaker) {
            $query->whereDoesntHave('question')->orWhere('id', $speaker->correct_answer_id);
        }])->find($listenin_id);

        $answers = $listening_exercise->listening_part_three_four->part_three_four_answers;
        return view('admin.part_three_questions.edit', compact('listening_exercise', 'answers', 'speaker'));
    }
    public function edit2($id, $listenin_id)
    {
        $question = ListeningPartThreeFourQuestion::find($id);

        $listening_exercise = ListeningExercise::with(['part_four.part_three_four_answers' => function ($query) use ($question) {
            $query->whereDoesntHave('question')->orWhere('id', $question->correct_answer_id);
        }])->find($listenin_id);

        $answers = $listening_exercise->part_four->part_three_four_answers;
        return view('admin.part_four_questions.edit', compact('listening_exercise', 'answers', 'question'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ListeningPartThreeFourQuestion $listeningPartThreeFourQuestion)
    {
        //
    }
    public function update1(Request $request, $id)
    {
        $this->validate($request,[
            'title' => 'required',
            'correct_answer_id' => 'required',
        ]);
        $speaker=ListeningPartThreeFourQuestion::find($id);
        $speaker->update([
            'title'=>$request->title,
            'correct_answer_id'=>$request->correct_answer_id,
        ]);
        return redirect()->route('admin.listenings.part3.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);
    }
    public function update2(Request $request, $id)
    {
        $this->validate($request,[
            'title' => 'required',
            'correct_answer_id' => 'required',
        ]);
        $speaker=ListeningPartThreeFourQuestion::find($id);
        $speaker->update([
            'title'=>$request->title,
            'correct_answer_id'=>$request->correct_answer_id,
        ]);
        return redirect()->route('admin.listenings.part4.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $speaker=ListeningPartThreeFourQuestion::find($id);
        $speaker->delete();
        return redirect()->back();
    }
}
