<?php

namespace App\Http\Controllers;

use App\Models\ListeningExercise;
use App\Models\ListeningPartTwoSixQuestion;
use App\Models\ReadingExercise;
use App\Models\ReadingPartFour;
use App\Models\ReadingPartOneFiveQuestion;
use Illuminate\Http\Request;
use function Laravel\Prompts\text;

class ReadingPartOneFiveQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {

        $reading_exercise = ReadingExercise::with('part_one.answers')->find($id);
        $Text=$this->putWordsInText($reading_exercise->part_one->question_text ?? 'no text',$reading_exercise->part_one->answers ?? []);

        $exercise_id=$reading_exercise->exercise_id;

//        dd($reading_exercise);
        return view('admin.reading.part_one.index' ,compact('reading_exercise','exercise_id','Text'));
    }
    public function index1($id)
    {

        $reading_exercise = ReadingExercise::with('part_five.answers','part_five.tests')->find($id);
        $Text=$this->putWordsInText($reading_exercise->part_five->question_text ?? 'no text',$reading_exercise->part_five->answers ?? []);

        $exercise_id=$reading_exercise->exercise_id;

//        dd($reading_exercise);
        return view('admin.reading.part_five.index' ,compact('reading_exercise','exercise_id','Text'));
    }


    function putWordsInText($text,$tests) {
        $testAnswers = $tests->mapWithKeys(function ($test) {
            return [$test->count_number => $test->correct_answer];
        })->toArray();

        // Replace numbers in brackets in the text based on count_number mapping
        return  preg_replace_callback('/\[(\d+)\]/', function ($matches) use ($testAnswers) {
            $number = $matches[1];
            return '[' . $number . (isset($testAnswers[$number]) ? ' ' . $testAnswers[$number] : '') . ']';
        }, $text);
    }

    function extractNumbersInBrackets($text,$tests) {
        // Use regular expression to find numbers in brackets
        preg_match_all('/\[(\d+)\]/', $text, $matches);

        // Create an associative array where keys and values are the same
        $result = [];

        foreach ($matches[1] as $number) {
            $result[(int)$number] = (int)$number;
        }
        $countNumbersToRemove = $tests->map(function ($test) {
            return (int)$test->count_number;
        })->toArray();

        $filteredArray = array_diff_key($result, array_flip($countNumbersToRemove));

        return $filteredArray;
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
    public function show(ReadingPartOneFiveQuestion $readingPartOneFiveQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $part_five=ReadingPartOneFiveQuestion::with('reading_exercise')->find($id);
        return view('admin.reading.part_five.edit_reading_text',compact('part_five'));
    }

    public function edit1($id)
    {
        $question=ReadingPartOneFiveQuestion::with('answers','readingExercise')->find($id);
        return view('admin.reading.part_one.edit',compact('question'));
    }
    public function edit2($id)
    {
        $question=ReadingPartOneFiveQuestion::with('answers','readingExercise')->find($id);
        return view('admin.reading.part_five.edit',compact('question'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'text'=>'required'
        ]);
        $reading=ReadingPartOneFiveQuestion::find($id);
        $reading->reading_text=$request->text;
        $reading->save();
        return redirect()->route('admin.reading.part5.index',['id'=>$request->reading_id]);

    }
    public function update1(Request $request, $id)
    {
        request()->validate([
            'text' => 'required',
        ]);
        $question=ReadingPartOneFiveQuestion::find($id);
        $question->question_text=$request->text;
        $question->save();

        return redirect()->route('admin.reading.part1.index',$question->reading_exercise_id);
    }
    public function update2(Request $request, $id)
    {
        request()->validate([
            'text' => 'required',
        ]);
        $question=ReadingPartOneFiveQuestion::find($id);
        $question->question_text=$request->text;
        $question->save();

        return redirect()->route('admin.reading.part5.index',$question->reading_exercise_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReadingPartOneFiveQuestion $readingPartOneFiveQuestion)
    {
        //
    }
}
