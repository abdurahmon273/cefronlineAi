<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {

    Route::get('reading/exercises',[\App\Http\Controllers\Admin\ExcerciseController::class,'reading_index'])->name('reading.exercises.index');
    Route::get('reading/exercises/create',[\App\Http\Controllers\Admin\ExcerciseController::class,'reading_create'])->name('reading.exercises.create');
    Route::post('reading/exercises/store',[\App\Http\Controllers\Admin\ExcerciseController::class,'reading_store'])->name('reading.exercises.store');
    Route::get('reading/exercises/{excercise}/edit', [\App\Http\Controllers\Admin\ExcerciseController::class, 'reading_edit'])->name('reading.exercises.edit');
    Route::put('reading/exercises/{excercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'reading_update'])->name('reading.exercises.update');
    Route::delete('reading/exercises/{exercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'reading_destroy'])->name('reading.exercises.destroy');

    Route::get('reading/exercise/{id}', [\App\Http\Controllers\ReadingExerciseController::class, 'index'])->name('readings.exercise.index');
    Route::get('reading/exercise/create/{id}', [\App\Http\Controllers\ReadingExerciseController::class, 'create'])->name('readings.exercise.create');
    Route::put('reading/exercise/store', [\App\Http\Controllers\ReadingExerciseController::class, 'store'])->name('readings.exercise.store');
    Route::get('reading/exercise/edit/{id}', [\App\Http\Controllers\ReadingExerciseController::class, 'edit'])->name('reading.exercise.edit');
    Route::put('reading/exercise/{id}', [\App\Http\Controllers\ReadingExerciseController::class, 'update'])->name('reading.exercise.update');
    Route::delete('reading/exercise/{exercise}', [\App\Http\Controllers\ReadingExerciseController::class, 'destroy'])->name('reading.exercise.destroy');
    Route::post('reading_full_exercise/update/{id}', [\App\Http\Controllers\ReadingExerciseController::class, 'update2'])->name('reading_full_exercise.update');

    Route::get('reading/full/index/{id}', [\App\Http\Controllers\ReadingExerciseController::class, 'reading_full_index'])->name('reading.full_exercise.index');

    // part 1
    Route::get('reading/part1/{id}', [\App\Http\Controllers\ReadingPartOneFiveQuestionController::class, 'index'])->name('reading.part1.index');
    Route::get('part1/text/edit/{id}', [\App\Http\Controllers\ReadingPartOneFiveQuestionController::class, 'edit1'])->name('part1.text.edit');
    Route::put('part1/text/update/{id}', [\App\Http\Controllers\ReadingPartOneFiveQuestionController::class, 'update1'])->name('part1.text.update');
    Route::get('check', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'create1'])->name('part1.answer.createq');

    Route::get('part1/answer/create/{id}', [\App\Http\Controllers\ReadingPartOneFiveTestController::class, 'create1'])->name('part1.answer.create');
    Route::post('part1/answer/store', [\App\Http\Controllers\ReadingPartOneFiveTestController::class, 'store1'])->name('part1.answer.store');
    Route::get('part1/answer/edit/{id}/{reading_id}', [\App\Http\Controllers\ReadingPartOneFiveTestController::class, 'edit2'])->name('part1.answer.edit');
    Route::post('part1/answer/update/{id}', [\App\Http\Controllers\ReadingPartOneFiveTestController::class, 'update2'])->name('part1.answer.update');
    Route::delete('part1/answer/destroy/{id}/{reading_id}', [\App\Http\Controllers\ReadingPartOneFiveTestController::class, 'destroy'])->name('part1.answer.destroy');

    // part 2
    Route::get('reading/part2/{id}', [\App\Http\Controllers\ReadingPartTwoThreeController::class, 'index'])->name('reading.part2.index');

    Route::get('part2/text/create/{id}', [\App\Http\Controllers\ReadingPartTwoThreeTextsController::class, 'create'])->name('part2.text.create');
    Route::post('part2/text/store', [\App\Http\Controllers\ReadingPartTwoThreeTextsController::class, 'store'])->name('part2.text.store');
    Route::get('part2/text/edit/{id}/{reading_id}', [\App\Http\Controllers\ReadingPartTwoThreeTextsController::class, 'edit'])->name('part2.text.edit');
    Route::post('part2/text/update/{id}', [\App\Http\Controllers\ReadingPartTwoThreeTextsController::class, 'update'])->name('part2.text.update');
    Route::delete('part2/text/destroy/{id}', [\App\Http\Controllers\ReadingPartTwoThreeTextsController::class, 'destroy'])->name('part2.text.destroy');

    Route::get( 'part2/answer/create/{id}', [\App\Http\Controllers\ReadingPartTwoThreeAnswerController::class, 'create'])->name('part2.answer.create');
    Route::post('part2/answer/store', [\App\Http\Controllers\ReadingPartTwoThreeAnswerController::class, 'store'])->name('part2.answer.store');
    Route::get( 'part2/answer/edit/{id}/{reading_id}', [\App\Http\Controllers\ReadingPartTwoThreeAnswerController::class, 'edit'])->name('part2.answer.edit');
    Route::post('part2/answer/update/{id}', [\App\Http\Controllers\ReadingPartTwoThreeAnswerController::class, 'update'])->name('part2.answer.update');
    Route::delete('part2/answer/destroy/{id}', [\App\Http\Controllers\ReadingPartTwoThreeAnswerController::class, 'destroy'])->name('part2.answer.destroy');

    // part 3
    Route::get('reading/part3/{id}', [\App\Http\Controllers\ReadingPartTwoThreeController::class, 'index1'])->name('reading.part3.index');

    Route::get('part3/text/create/{id}', [\App\Http\Controllers\ReadingPartTwoThreeTextsController::class, 'create1'])->name('part3.text.create');
    Route::post('part3/text/store', [\App\Http\Controllers\ReadingPartTwoThreeTextsController::class, 'store1'])->name('part3.text.store');
    Route::get('part3/text/edit/{id}/{reading_id}', [\App\Http\Controllers\ReadingPartTwoThreeTextsController::class, 'edit1'])->name('part3.text.edit');
    Route::post('part3/text/update/{id}', [\App\Http\Controllers\ReadingPartTwoThreeTextsController::class, 'update1'])->name('part3.text.update');
    Route::delete('part3/text/destroy/{id}', [\App\Http\Controllers\ReadingPartTwoThreeTextsController::class, 'destroy'])->name('part3.text.destroy');

    Route::get( 'part3/heading/create/{id}', [\App\Http\Controllers\ReadingPartTwoThreeAnswerController::class, 'create1'])->name('part3.heading.create');
    Route::post('part3/heading/store', [\App\Http\Controllers\ReadingPartTwoThreeAnswerController::class, 'store1'])->name('part3.heading.store');
    Route::get( 'part3/heading/edit/{id}/{reading_id}', [\App\Http\Controllers\ReadingPartTwoThreeAnswerController::class, 'edit1'])->name('part3.heading.edit');
    Route::post('part3/heading/update/{id}', [\App\Http\Controllers\ReadingPartTwoThreeAnswerController::class, 'update1'])->name('part3.heading.update');
    Route::delete('part3/heading/destroy/{id}', [\App\Http\Controllers\ReadingPartTwoThreeAnswerController::class, 'destroy'])->name('part3.heading.destroy');

    // part 4
    Route::get('reading/part4/{id}', [\App\Http\Controllers\ReadingPartFourController::class, 'index1'])->name('reading.part4.index');
    Route::get('part4/text/edit/{id}', [\App\Http\Controllers\ReadingPartFourController::class, 'edit'])->name('part4.text.edit');
    Route::post('part4/text/update/{id}', [\App\Http\Controllers\ReadingPartFourController::class, 'update'])->name('part4.text.update');

    Route::get('part4/tfn/create/{id}', [\App\Http\Controllers\ReadingPartFourTestController::class, 'create1'])->name('part4.tfn.create');
    Route::post('part4/tfn/store', [\App\Http\Controllers\ReadingPartFourTestController::class, 'store1'])->name('part4.tfn.store');
    Route::get('part4/tfn/edit/{id}', [\App\Http\Controllers\ReadingPartFourTestController::class, 'edit1'])->name('part4.tfn.edit');
    Route::post('part4/tfn/update/{id}', [\App\Http\Controllers\ReadingPartFourTestController::class, 'update1'])->name('part4.tfn.update');
    Route::delete('part4/tfn/destroy/{id}', [\App\Http\Controllers\ReadingPartFourTestController::class, 'destroy'])->name('part4.tfn.destroy');

    Route::get('part4/mchq/create/{id}', [\App\Http\Controllers\ReadingPartFourTestController::class, 'create2'])->name('part4.mchq.create');
    Route::post('part4/mchq/store', [\App\Http\Controllers\ReadingPartFourTestController::class, 'store2'])->name('part4.mchq.store');
    Route::get('part4/mchq/edit1/{id}', [\App\Http\Controllers\ReadingPartFourTestController::class, 'edit2'])->name('part4.mchq.edit');
    Route::post('part4/mchq/update/{id}', [\App\Http\Controllers\ReadingPartFourTestController::class, 'update1'])->name('part4.mchq.update');
    Route::delete('part4/mchq/destroy/{id}', [\App\Http\Controllers\ReadingPartFourTestController::class, 'destroy'])->name('part4.mchq.destroy');

    //part 5
    Route::get('reading/part5/{id}', [\App\Http\Controllers\ReadingPartOneFiveQuestionController::class, 'index1'])->name('reading.part5.index');
    Route::get('part5/text/edit/{id}', [\App\Http\Controllers\ReadingPartOneFiveQuestionController::class, 'edit2'])->name('part5.text.edit');
    Route::put('part5/text/update/{id}', [\App\Http\Controllers\ReadingPartOneFiveQuestionController::class, 'update2'])->name('part5.text.update');
//    Route::get('check', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'create1'])->name('part1.answer.createq');

    Route::get('part5/answer/create/{id}', [\App\Http\Controllers\ReadingPartOneFiveTestController::class, 'create2'])->name('part5.answer.create');
    Route::post('part5/answer/store', [\App\Http\Controllers\ReadingPartOneFiveTestController::class, 'store2'])->name('part5.answer.store');
    Route::get('part5/answer/edit/{id}/{reading_id}', [\App\Http\Controllers\ReadingPartOneFiveTestController::class, 'edit3'])->name('part5.answer.edit');
    Route::post('part5/answer/update/{id}', [\App\Http\Controllers\ReadingPartOneFiveTestController::class, 'update1'])->name('part5.answer.update');
    Route::delete('part5/answer/destroy/{id}/{reading_id}', [\App\Http\Controllers\ReadingPartOneFiveTestController::class, 'destroy'])->name('part5.answer.destroy');

    Route::get('part5/test/create/{id}', [\App\Http\Controllers\ReadingPartFiveTestController::class, 'create'])->name('part5.test.create');
    Route::post('part5/test/store', [\App\Http\Controllers\ReadingPartFiveTestController::class, 'store'])->name('part5.test.store');
    Route::get('part5/test/edit1/{id}', [\App\Http\Controllers\ReadingPartFiveTestController::class, 'edit'])->name('part5.test.edit');
    Route::post('part5/test/update/{id}', [\App\Http\Controllers\ReadingPartFiveTestController::class, 'update'])->name('part5.test.update');
    Route::delete('part5/test/destroy/{id}', [\App\Http\Controllers\ReadingPartFiveTestController::class, 'destroy'])->name('part5.test.destroy');

    Route::get('part5/reading_text/edit/{id}', [\App\Http\Controllers\ReadingPartOneFiveQuestionController::class, 'edit'])->name('part5.reading_text.edit');
    Route::post('part5/reading_text/update/{id}', [\App\Http\Controllers\ReadingPartOneFiveQuestionController::class, 'update'])->name('part5.reading_text.update');

});
