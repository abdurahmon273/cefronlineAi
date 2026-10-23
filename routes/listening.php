<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {

    Route::get('listening/exercises',[\App\Http\Controllers\Admin\ExcerciseController::class,'listening_index'])->name('listening.exercises.index');
    Route::get('listening/exercises/create',[\App\Http\Controllers\Admin\ExcerciseController::class,'listening_create'])->name('listening.exercises.create');
    Route::post('listening/exercises/store',[\App\Http\Controllers\Admin\ExcerciseController::class,'listening_store'])->name('listening.exercises.store');
    Route::get('listening/exercises/{excercise}/edit', [\App\Http\Controllers\Admin\ExcerciseController::class, 'listening_edit'])->name('listening.exercises.edit');
    Route::put('listening/exercises/{excercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'listening_update'])->name('listening.exercises.update');
    Route::delete('listening/exercises/{exercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'destroy'])->name('listening.exercises.destroy');


    Route::resource('listening', \App\Http\Controllers\Admin\ListeningExerciseController::class);
    Route::get('listening/exercise/{id}', [\App\Http\Controllers\Admin\ListeningExerciseController::class, 'index1'])->name('listenings.exercise.index');
    Route::get('exercise/create/{id}', [\App\Http\Controllers\Admin\ListeningExerciseController::class, 'create1'])->name('listenings.exercise.create');
    Route::put('exercise/store', [\App\Http\Controllers\Admin\ListeningExerciseController::class, 'store1'])->name('listenings.exercise.store');
    Route::post('listening_full_exercise/update/{id}', [\App\Http\Controllers\Admin\ListeningExerciseController::class, 'update1'])->name('listening_full_exercise.update');

    Route::get('listening/full/index/{id}/{exercise_id}', [\App\Http\Controllers\Admin\ListeningExerciseController::class, 'listening_full_index'])->name('listenings.full_exercise.index');

    Route::get('listening/part1/index/{id}/{exercise_id}', [\App\Http\Controllers\Admin\ListeningPartOneFifeTestController::class, 'index1'])->name('listenings.part1.index');
    Route::get('listening/part1/create/{id}/{id1}/{id2}', [\App\Http\Controllers\Admin\ListeningPartOneFifeTestController::class, 'create1'])->name('listenings.part1.create');
    Route::post('listening/part1/store', [\App\Http\Controllers\Admin\ListeningPartOneFifeTestController::class, 'store1'])->name('listenings.part1.store');
    Route::delete('listening/part1/destroy/{id}', [\App\Http\Controllers\Admin\ListeningPartOneFifeTestController::class, 'destroy'])->name('listenings.part1.destroy');
    Route::get('listening/part1/edit1/{id}', [\App\Http\Controllers\Admin\ListeningPartOneFifeTestController::class, 'edit1'])->name('listenings.part1.edit');
    Route::post('listening/part1/update/{id}', [\App\Http\Controllers\Admin\ListeningPartOneFifeTestController::class, 'update1'])->name('listenings.part1.update');

    Route::get('listening/part5/index/{id}/{exercise_id}', [\App\Http\Controllers\Admin\ListeningPartOneFifeTestController::class, 'index2'])->name('listenings.part5.index');
    Route::get('listening/part5/create/{id}/{id1}/{id2}', [\App\Http\Controllers\Admin\ListeningPartOneFifeTestController::class, 'create2'])->name('listenings.part5.create');
    Route::post('listening/part5/store', [\App\Http\Controllers\Admin\ListeningPartOneFifeTestController::class, 'store2'])->name('listenings.part5.store');
    Route::get('listening/part5/edit2/{id}', [\App\Http\Controllers\Admin\ListeningPartOneFifeTestController::class, 'edit2'])->name('listenings.part5.edit');
    Route::post('listening/part5/update/{id}', [\App\Http\Controllers\Admin\ListeningPartOneFifeTestController::class, 'update2'])->name('listenings.part5.update');

    //part 2
    Route::get('listening/part2/full/index/{id}/{exercise_id}', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'index2'])->name('listenings.partfull.index');

    Route::get('listening/part2/index/{id}/{exercise_id}', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'index1'])->name('listenings.part2.index');
    Route::get('part2/question/edit/{id}', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'edit1'])->name('part2.question.edit');
    Route::put('part2/question/update/{id}', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'update1'])->name('part2.question.update');
    Route::get('part2/test/create/{id}', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'create1'])->name('part2.test.create');
    Route::post('part2/test/store', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'store1'])->name('part2.test.store');
    Route::get('part2/test/edit/{id}/{listening_id}', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'edit2'])->name('part2.test.edit');
    Route::post('part2/test/update/{id}', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'update2'])->name('part2.test.update');
    Route::delete('part2/test/destroy/{id}/{listening_id}/{exercise_id}', [\App\Http\Controllers\ListeningPartTwoSixTestController::class, 'destroy'])->name('part2.test.destroy');

    //part 6
    Route::get('listening/part6/index/{id}/{exercise_id}', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'index2'])->name('listenings.part6.index');
    Route::get('part6/test/create/{id}', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'create2'])->name('part6.test.create');
    Route::post('part6/test/store', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'store2'])->name('part6.test.store');
    Route::get('part6/question/edit/{id}', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'edit3'])->name('part6.question.edit');
    Route::put('part6/question/update/{id}', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'update3'])->name('part6.question.update');
    Route::get('part6/test/edit/{id}/{listening_id}', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'edit4'])->name('part6.test.edit');
    Route::post('part6/test/update/{id}', [\App\Http\Controllers\ListeningPartTwoSixQuestionController::class, 'update4'])->name('part6.test.update');
    Route::delete('part6/test/destroy/{id}/{listening_id}/{exercise_id}', [\App\Http\Controllers\ListeningPartTwoSixTestController::class, 'destroy1'])->name('part6.test.destroy');

    //part 3
    Route::get('listening/part3/index/{id}/{exercise_id}', [\App\Http\Controllers\ListeningPartThreeFourController::class, 'index1'])->name('listenings.part3.index');

    Route::get('part3/answer/create/{id}', [\App\Http\Controllers\ListeningPartThreeFourAnswersController::class, 'create1'])->name('part3.answer.create');
    Route::post('part3/answer/store', [\App\Http\Controllers\ListeningPartThreeFourAnswersController::class, 'store1'])->name('part3.answer.store');
    Route::delete('part3/answer/destroy/{id}', [\App\Http\Controllers\ListeningPartThreeFourAnswersController::class, 'destroy'])->name('part3.answer.destroy');
    Route::get('part3/answer/edit/{id}/{listening_id}', [\App\Http\Controllers\ListeningPartThreeFourAnswersController::class, 'edit1'])->name('part3.answer.edit');
    Route::post('part3/answer/update/{id}', [\App\Http\Controllers\ListeningPartThreeFourAnswersController::class, 'update1'])->name('part3.answer.update');

    Route::get('part3/speaker/create/{id}', [\App\Http\Controllers\ListeningPartThreeFourQuestionController::class, 'create1'])->name('part3.speaker.create');
    Route::post('part3/speaker/store', [\App\Http\Controllers\ListeningPartThreeFourQuestionController::class, 'store1'])->name('part3.speaker.store');
    Route::delete('part3/speaker/destroy/{id}', [\App\Http\Controllers\ListeningPartThreeFourQuestionController::class, 'destroy'])->name('part3.speaker.destroy');
    Route::get('part3/speaker/edit/{id}/{listening_id}', [\App\Http\Controllers\ListeningPartThreeFourQuestionController::class, 'edit1'])->name('part3.speaker.edit');
    Route::post('part3/speaker/update/{id}', [\App\Http\Controllers\ListeningPartThreeFourQuestionController::class, 'update1'])->name('part3.speaker.update');


    // part 4
    Route::get('listening/part4/index/{id}/{exercise_id}', [\App\Http\Controllers\ListeningPartThreeFourController::class, 'index2'])->name('listenings.part4.index');
    Route::get('part4/map/edit/{id}/{listening_id}', [\App\Http\Controllers\ListeningPartThreeFourController::class, 'edit1'])->name('part4.map.edit');
    Route::post('part4/map/update/{id}', [\App\Http\Controllers\ListeningPartThreeFourController::class, 'update1'])->name('part4.map.update');

    // location
    Route::get('part4/location/create/{id}', [\App\Http\Controllers\ListeningPartThreeFourAnswersController::class, 'create2'])->name('part4.location.create');
    Route::post('part4/location/store', [\App\Http\Controllers\ListeningPartThreeFourAnswersController::class, 'store2'])->name('part4.location.store');
    Route::delete('part4/location/destroy/{id}', [\App\Http\Controllers\ListeningPartThreeFourAnswersController::class, 'destroy'])->name('part4.location.destroy');
    Route::get('part4/location/edit/{id}/{listening_id}', [\App\Http\Controllers\ListeningPartThreeFourAnswersController::class, 'edit2'])->name('part4.location.edit');
    Route::post('part4/location/update/{id}', [\App\Http\Controllers\ListeningPartThreeFourAnswersController::class, 'update2'])->name('part4.location.update');

    // question
    Route::get('part4/question/create/{id}', [\App\Http\Controllers\ListeningPartThreeFourQuestionController::class, 'create2'])->name('part4.question.create');
    Route::post('part4/question/store', [\App\Http\Controllers\ListeningPartThreeFourQuestionController::class, 'store2'])->name('part4.question.store');
    Route::delete('part4/question/destroy/{id}', [\App\Http\Controllers\ListeningPartThreeFourQuestionController::class, 'destroy'])->name('part4.question.destroy');
    Route::get('part4/question/edit/{id}/{listening_id}', [\App\Http\Controllers\ListeningPartThreeFourQuestionController::class, 'edit2'])->name('part4.question.edit');
    Route::post('part4/question/update/{id}', [\App\Http\Controllers\ListeningPartThreeFourQuestionController::class, 'update2'])->name('part4.question.update');


});
