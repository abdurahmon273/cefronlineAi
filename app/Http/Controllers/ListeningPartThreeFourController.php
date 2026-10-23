<?php

namespace App\Http\Controllers;

use App\Models\ListeningExercise;
use App\Models\ListeningPartThreeFour;
use App\Models\ListeningPartThreeFourAnswers;
use App\Models\ListeningPartThreeFourQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListeningPartThreeFourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function index1($id, $exercise_id)
    {
        $listening_exercise = ListeningExercise::with([
            'listening_part_three_four.part_three_four_questions.correct_answer.question',
            'listening_part_three_four.part_three_four_answers' => function($query) {
                $query->orderBy('letter')->with('question');
            }
        ])->find($id);

        $Text = $listening_exercise->listening_part_three_four->text;

        return view('admin.part_three_tests.index', compact('listening_exercise', 'exercise_id', 'Text'));
    }

    public function index2($id, $exercise_id)
    {
        $listening_exercise = ListeningExercise::with([
            'part_four.part_three_four_questions.correct_answer.question',
            'part_four.part_three_four_answers.question',
            'part_four.part_three_four_answers' => function($query) {
                $query->orderBy('letter');
            }
        ])->find($id);


        $Text = $listening_exercise->part_four->text;

        return view('admin.part_four_tests.index', compact('listening_exercise', 'exercise_id', 'Text'));
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
    public function show(ListeningPartThreeFour $listeningPartThreeFour)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ListeningPartThreeFour $listeningPartThreeFour)
    {
        //
    }
    public function edit1($id, $listenin_id)
    {
        $three_four = ListeningPartThreeFour::find($id);

        $listening_exercise = ListeningExercise::with('part_four')->find($listenin_id);

        return view('admin.part_four_tests.edit', compact('listening_exercise', 'three_four'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ListeningPartThreeFour $listeningPartThreeFour)
    {
        //
    }
    public function update1(Request $request, $id)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg', // Add validation for the image
        ]);

        $three_four = ListeningPartThreeFour::find($id);

        if (!$three_four) {
            return redirect()->back()->with('error', 'Record not found.');
        }

        // Check if the old image exists and delete it
        if ($three_four->image && Storage::exists('public/maps/' . $three_four->image)) {
            Storage::delete('public/maps/' . $three_four->image);
        }

        $uniqueFilename = '';

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $uniqueFilename = 'image_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('image')->storeAs('public/maps', $uniqueFilename);

            // Save the new filename in the database
            $three_four->image = $uniqueFilename;
            $three_four->save();
        }

        return redirect()->route('admin.listenings.part4.index', [
            'id' => $request->listening_id,
            'exercise_id' => $request->exercise_id,
        ])->with('success', 'Image updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ListeningPartThreeFour $listeningPartThreeFour)
    {
        //
    }
}
