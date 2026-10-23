<?php

namespace App\Http\Controllers;

use App\Models\ListeningExercise;
use App\Models\ListeningPartTwoSixTest;
use App\Models\ReadingExercise;
use App\Models\ReadingPartOneFiveTest;
use App\Models\SpeakingExercise;
use Illuminate\Http\Request;

class ReadingPartOneFiveTestController extends Controller
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
        $reading_exercise = ReadingExercise::with('part_one.answers')->find($id);
        $text=$reading_exercise->part_one->question_text;
        $tests=$reading_exercise->part_one->answers;
        $array= $this->extractNumbersInBrackets($text,$tests);
        $updatedText=$this->putWordsInText($text,$tests);


        return view('admin.reading.part_one.create_answer' ,compact('reading_exercise','array','updatedText'));
    }

    public function create2($id)
    {
        $reading_exercise = ReadingExercise::with('part_five.answers')->find($id);
        $text=$reading_exercise->part_five->question_text;
        $tests=$reading_exercise->part_five->answers;
        $array= $this->extractNumbersInBrackets($text,$tests);
        $updatedText=$this->putWordsInText($text,$tests);


        return view('admin.reading.part_five.create_answer' ,compact('reading_exercise','array','updatedText'));
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
    public function store1(Request $request)
    {
        $this->validate($request,[
            'correct_answer'=>'required',
            'count_number'=>'required',
        ]);
        ReadingPartOneFiveTest::create([
            'correct_answer'=>$request->correct_answer,
            'extra_answer'=>$request->extra_answer ?? null,
            'count_number'=>$request->count_number,
            'reading_part_one_five_question_id'=>$request->question_id,
        ]);
        return redirect()->route('admin.reading.part1.index',['id'=>$request->reading_id]);

    }
    public function store2(Request $request)
    {
        $this->validate($request,[
            'correct_answer'=>'required',
            'count_number'=>'required',
        ]);
        ReadingPartOneFiveTest::create([
            'correct_answer'=>$request->correct_answer,
            'extra_answer'=>$request->extra_answer ?? null,
            'count_number'=>$request->count_number,
            'reading_part_one_five_question_id'=>$request->question_id,
        ]);
        return redirect()->route('admin.reading.part5.index',['id'=>$request->reading_id]);

    }

    /**
     * Display the specified resource.
     */
    public function show(ReadingPartOneFiveTest $readingPartOneFiveTest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReadingPartOneFiveTest $readingPartOneFiveTest)
    {
        //
    }

    public function edit2($id,$reading_id)
    {
        $reading_exercise = ReadingExercise::with('part_one.answers')->find($reading_id);
        $test=ReadingPartOneFiveTest::find($id);
        $text=$reading_exercise->part_one->question_text;
        $tests=$reading_exercise->part_one->answers;
        $array= $this->extractNumbersInBrackets($text,$tests);
        $updatedText=$this->putWordsInText($text,$tests);
        $array[$test->count_number]=$test->count_number;

        return view('admin.reading.part_one.edit_answer' ,compact('reading_exercise','test','array','updatedText'));
    }

    public function edit3($id,$reading_id)
    {
        $reading_exercise = ReadingExercise::with('part_five.answers')->find($reading_id);
        $test=ReadingPartOneFiveTest::find($id);
        $text=$reading_exercise->part_five->question_text;
        $tests=$reading_exercise->part_five->answers;
        $array= $this->extractNumbersInBrackets($text,$tests);
        $updatedText=$this->putWordsInText($text,$tests);
        $array[$test->count_number]=$test->count_number;

        return view('admin.reading.part_five.edit_answer' ,compact('reading_exercise','test','array','updatedText'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReadingPartOneFiveTest $readingPartOneFiveTest)
    {
        //
    }

    public function update1(Request $request,$id)
    {
        $test=ReadingPartOneFiveTest::find($id);

        $this->validate($request,[
            'correct_answer'=>'required',
            'count_number'=>'required',
        ]);
        $test->update([
            'correct_answer'=>$request->correct_answer,
            'extra_answer'=>$request->extra_answer ?? null,
            'count_number'=>$request->count_number,
        ]);
        return redirect()->route('admin.reading.part5.index',['id'=>$request->reading_id]);

    }
    public function update2(Request $request,$id)
    {
        $test=ReadingPartOneFiveTest::find($id);

        $this->validate($request,[
            'correct_answer'=>'required',
            'count_number'=>'required',
        ]);
        $test->update([
            'correct_answer'=>$request->correct_answer,
            'extra_answer'=>$request->extra_answer ?? null,
            'count_number'=>$request->count_number,
        ]);
        return redirect()->route('admin.reading.part1.index',['id'=>$request->reading_id]);

    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id,$reading_id)
    {
      $answer=ReadingPartOneFiveTest::find($id);
      $answer->delete();
      return redirect()->back();
    }
}
