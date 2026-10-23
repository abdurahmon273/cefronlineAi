<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\ListeningExercise;
use App\Models\ListeningPartOneFifeQuestion;
use App\Models\ListeningPartOneFifeTest;
use App\Models\ListeningPartTwoSixQuestion;
use App\Models\ListeningPartTwoSixTest;
use Illuminate\Http\Request;

class ListeningPartTwoSixQuestionController extends Controller
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
        $listening_exercise = ListeningExercise::with('part_two.listening_part_two_six_tests')->find($id);
        $Text=$this->putWordsInText($listening_exercise->part_two->text,$listening_exercise->part_two->listening_part_two_six_tests);

        return view('admin.part_two_tests.index' ,compact('listening_exercise','exercise_id','Text'));
    }

    public function index2($id,$exercise_id)
    {
        $listening_exercise = ListeningExercise::with([
            'part_six',
            'part_six.listening_part_two_six_tests'
        ])->find($id);
        $Text=$this->putWordsInText($listening_exercise->part_six->text,$listening_exercise->part_six->listening_part_two_six_tests);

        return view('admin.part_six_tests.index' ,compact('listening_exercise','exercise_id','Text'));
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
        $listening_exercise = ListeningExercise::with('listening_part_two_six_question.listening_part_two_six_tests')->find($id);
        $text=$listening_exercise->listening_part_two_six_question->text;
        $tests=$listening_exercise->listening_part_two_six_question->listening_part_two_six_tests;
        $array= $this->extractNumbersInBrackets($text,$tests);
        $updatedText=$this->putWordsInText($text,$tests);


        return view('admin.part_two_tests.create' ,compact('listening_exercise','array','updatedText'));
    }
    public function create2($id)
    {
        $listening_exercise = ListeningExercise::with('part_six.listening_part_two_six_tests')->find($id);
        $text=$listening_exercise->part_six->text;
        $tests=$listening_exercise->part_six->listening_part_two_six_tests;
        $array= $this->extractNumbersInBrackets($text,$tests);
        $updatedText=$this->putWordsInText($text,$tests);


        return view('admin.part_six_tests.create' ,compact('listening_exercise','array','updatedText'));
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

        ListeningPartTwoSixTest::create([
            'correct_answer'=>$request->correct_answer,
            'extra_answer'=>$request->extra_answer ?? null,
            'count_number'=>$request->count_number,
            'listening_part_two_six_question_id'=>$request->question_id,
        ]);
        return redirect()->route('admin.listenings.part2.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);

    }
    public function store2(Request $request)
    {
        $this->validate($request,[
            'correct_answer'=>'required',
            'count_number'=>'required',
        ]);
        ListeningPartTwoSixTest::create([
            'correct_answer'=>$request->correct_answer,
            'extra_answer'=>$request->extra_answer ?? null,
            'count_number'=>$request->count_number,
            'listening_part_two_six_question_id'=>$request->question_id,
        ]);
        return redirect()->route('admin.listenings.part6.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);

    }

    /**
     * Display the specified resource.
     */
    public function show(ListeningPartTwoSixQuestion $listeningPartTwoSixQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ListeningPartTwoSixQuestion $listeningPartTwoSixQuestion)
    {
        //
    }

    public function edit1($id)
    {
        $question=ListeningPartTwoSixQuestion::with('listening_part_two_six_tests','listeningExercise')->find($id);
      return view('admin.part_two_question.edit',compact('question'));
    }
    public function edit3($id)
    {
        $question=ListeningPartTwoSixQuestion::with('listening_part_two_six_tests','listeningExercise')->find($id);
      return view('admin.part_six_question.edit',compact('question'));
    }
    public function edit2($id,$listening_id)
    {
        $listening_exercise = ListeningExercise::with('listening_part_two_six_question.listening_part_two_six_tests')->find($listening_id);
        $test=ListeningPartTwoSixTest::find($id);
        $text=$listening_exercise->listening_part_two_six_question->text;
        $tests=$listening_exercise->listening_part_two_six_question->listening_part_two_six_tests;
        $array= $this->extractNumbersInBrackets($text,$tests);
        $updatedText=$this->putWordsInText($text,$tests);
        $array[$test->count_number]=$test->count_number;

        return view('admin.part_two_tests.edit' ,compact('listening_exercise','test','array','updatedText'));
    }
    public function edit4($id,$listening_id)
    {
        $listening_exercise = ListeningExercise::with('part_six.listening_part_two_six_tests')->find($listening_id);
        $test=ListeningPartTwoSixTest::find($id);
        $text=$listening_exercise->part_six->text;
        $tests=$listening_exercise->part_six->listening_part_two_six_tests;
        $array= $this->extractNumbersInBrackets($text,$tests);
        $updatedText=$this->putWordsInText($text,$tests);
        $array[$test->count_number]=$test->count_number;

        return view('admin.part_six_tests.edit' ,compact('listening_exercise','test','array','updatedText'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ListeningPartTwoSixQuestion $listeningPartTwoSixQuestion)
    {
        //
    }
    public function update1(Request $request, $id)
    {
        $question=ListeningPartTwoSixQuestion::with('listeningExercise')->find($id);
        $question->update(['text'=>$request->text]);
        return redirect()->route('admin.listenings.part2.index',['id'=>$question->listening_exercise_id,'exercise_id'=>$question->listeningExercise->exercise_id]);
    }
    public function update3(Request $request, $id)
    {
        $question=ListeningPartTwoSixQuestion::with('listeningExercise')->find($id);
        $question->update(['text'=>$request->text]);
        return redirect()->route('admin.listenings.part6.index',['id'=>$question->listening_exercise_id,'exercise_id'=>$question->listeningExercise->exercise_id]);
    }
    public function update2(Request $request,$id)
    {
     $test=ListeningPartTwoSixTest::find($id);

        $this->validate($request,[
            'correct_answer'=>'required',
            'count_number'=>'required',
        ]);
        $test->update([
            'extra_answer'=>$request->extra_answer ?? null,
            'correct_answer'=>$request->correct_answer,
            'count_number'=>$request->count_number,
        ]);
        return redirect()->route('admin.listenings.part2.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);

    }
    public function update4(Request $request,$id)
    {
      $test=ListeningPartTwoSixTest::find($id);

        $this->validate($request,[
            'correct_answer'=>'required',
            'count_number'=>'required',
        ]);
        $test->update([
            'correct_answer'=>$request->correct_answer,
            'extra_answer'=>$request->extra_answer ?? null,
            'count_number'=>$request->count_number,
        ]);
        return redirect()->route('admin.listenings.part6.index',['id'=>$request->listening_id,'exercise_id'=>$request->exercise_id]);

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ListeningPartTwoSixQuestion $listeningPartTwoSixQuestion)
    {
        //
    }
}
