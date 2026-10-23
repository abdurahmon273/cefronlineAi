<?php

namespace App\Http\Controllers;

use App\Models\ListeningExercise;
use App\Models\ListeningPartThreeFourQuestion;
use App\Models\ReadingExercise;
use App\Models\ReadingPartTwoThree;
use App\Models\ReadingPartTwoThreeTexts;
use Illuminate\Http\Request;

class ReadingPartTwoThreeTextsController extends Controller
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
        $reading_exercise = ReadingExercise::with(['part_two.answers' => function ($query) {
            $query->whereDoesntHave('text');
        }])->find($id);

        $answers = $reading_exercise->part_two->answers;

        return view('admin.reading.part_two.create_text', compact('reading_exercise', 'answers'));
    }
    public function create1($id)
    {
        $reading_exercise = ReadingExercise::with(['part_three.answers' => function ($query) {
            $query->whereDoesntHave('text');
        }])->find($id);

        $answers = $reading_exercise->part_three->answers;

        return view('admin.reading.part_three.create_text', compact('reading_exercise', 'answers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'text' => 'required',
            'correct_answer_id' => 'required',
            'reading_part_two_three_id'=>'required',
        ]);
        $text=ReadingPartTwoThreeTexts::create([
            'text'=>$request->text,
            'correct_answer_id'=>$request->correct_answer_id,
            'reading_part_two_three_id'=>$request->reading_part_two_three_id,
        ]);
        return redirect()->route('admin.reading.part2.index',['id'=>$request->reading_id]);

    }
    public function store1(Request $request)
    {
        $this->validate($request, [
            'text' => 'required',
            'correct_answer_id' => 'required',
            'reading_part_two_three_id'=>'required',
        ]);
        $text=ReadingPartTwoThreeTexts::create([
            'text'=>$request->text,
            'correct_answer_id'=>$request->correct_answer_id,
            'reading_part_two_three_id'=>$request->reading_part_two_three_id,
        ]);
        return redirect()->route('admin.reading.part3.index',['id'=>$request->reading_id]);

    }

    /**
     * Display the specified resource.
     */
    public function show(ReadingPartTwoThreeTexts $readingPartTwoThreeTexts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id,$reading_id)
    {
        $text = ReadingPartTwoThreeTexts::find($id);

        $reading_exercise = ReadingExercise::with(['part_two.answers' => function ($query) use ($text) {
            $query->whereDoesntHave('text')->orWhere('id', $text->correct_answer_id);
        }])->find($reading_id);
        $answers = $reading_exercise->part_two->answers;

        return view('admin.reading.part_two.edit_text', compact('reading_exercise', 'answers', 'text'));

    }
    public function edit1($id,$reading_id)
    {
        $text = ReadingPartTwoThreeTexts::find($id);

        $reading_exercise = ReadingExercise::with(['part_three.answers' => function ($query) use ($text) {
            $query->whereDoesntHave('text')->orWhere('id', $text->correct_answer_id);
        }])->find($reading_id);
        $answers = $reading_exercise->part_three->answers;

        return view('admin.reading.part_three.edit_text', compact('reading_exercise', 'answers', 'text'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'text' => 'required',
            'correct_answer_id' => 'required',
        ]);
        $text=ReadingPartTwoThreeTexts::find($id);
        $text->update([
            'text'=>$request->text,
            'correct_answer_id'=>$request->correct_answer_id,
        ]);
        return redirect()->route('admin.reading.part2.index',['id'=>$request->reading_id]);

    }
    public function update1(Request $request, $id)
    {
        $this->validate($request,[
            'text' => 'required',
            'correct_answer_id' => 'required',
        ]);
        $text=ReadingPartTwoThreeTexts::find($id);
        $text->update([
            'text'=>$request->text,
            'correct_answer_id'=>$request->correct_answer_id,
        ]);
        return redirect()->route('admin.reading.part3.index',['id'=>$request->reading_id]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $speaker=ReadingPartTwoThreeTexts::find($id);
        $speaker->delete();
        return redirect()->back();
    }
}
