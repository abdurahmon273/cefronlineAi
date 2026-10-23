<?php

namespace App\Http\Controllers;

use App\Models\ReadingExercise;
use App\Models\ReadingPartTwoThree;
use Illuminate\Http\Request;

class ReadingPartTwoThreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {

        $reading_exercise = ReadingExercise::with('part_two.texts.correct_answer','part_two.answers.text')->find($id);
        $Text='';
        $exercise_id=$reading_exercise->exercise_id;

        return view('admin.reading.part_two.index' ,compact('reading_exercise','exercise_id','Text'));
    }
    public function index1($id)
    {

        $reading_exercise = ReadingExercise::with('part_three.texts.correct_answer','part_three.answers.text')->find($id);
        $exercise_id=$reading_exercise->exercise_id;

        return view('admin.reading.part_three.index' ,compact('reading_exercise','exercise_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ReadingPartTwoThree $readingPartTwoThree)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReadingPartTwoThree $readingPartTwoThree)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReadingPartTwoThree $readingPartTwoThree)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReadingPartTwoThree $readingPartTwoThree)
    {
        //
    }
}
