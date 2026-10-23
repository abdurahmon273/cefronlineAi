<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {

    Route::get('speaking/exercises',[\App\Http\Controllers\Admin\ExcerciseController::class,'speaking_index'])->name('speaking.exercises.index');

    // magic exercises
    Route::get('speaking/part1/exercises',[\App\Http\Controllers\Admin\ExcerciseController::class,'speaking_part1'])->name('speaking.part1.exercise.index');
    Route::get('speaking/part1/exercises/create',[\App\Http\Controllers\Admin\ExcerciseController::class,'speaking_part1_create'])->name('speaking.part1.exercises.create');
    Route::post('speaking/part1/exercises/store',[\App\Http\Controllers\Admin\ExcerciseController::class,'speaking_part1_store'])->name('speaking.part1.exercises.store');
    Route::get('speaking/part1/exercises/{excercise}/edit', [\App\Http\Controllers\Admin\ExcerciseController::class, 'speaking_part1_edit'])->name('speaking.part1.exercises.edit');
    Route::put('speaking/part1/exercises/{excercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'speaking_part1_update'])->name('speaking.part1.exercises.update');
    Route::delete('speaking/part1/exercises/{exercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'speaking_destroy'])->name('writing.part1.exercises.destroy');

    Route::get('speaking/part2/exercises',[\App\Http\Controllers\Admin\ExcerciseController::class,'speaking_part2'])->name('speaking.part2.exercise.index');
    Route::get('speaking/part2/exercises/create',[\App\Http\Controllers\Admin\ExcerciseController::class,'speaking_part2_create'])->name('speaking.part2.exercises.create');
    Route::post('speaking/part2/exercises/store',[\App\Http\Controllers\Admin\ExcerciseController::class,'speaking_part2_store'])->name('speaking.part2.exercises.store');
    Route::get('speaking/part2/exercises/{excercise}/edit', [\App\Http\Controllers\Admin\ExcerciseController::class, 'speaking_part2_edit'])->name('speaking.part2.exercises.edit');
    Route::put('speaking/part2/exercises/{excercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'speaking_part2_update'])->name('speaking.part2.exercises.update');
    Route::delete('speaking/part2/exercises/{exercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'speaking_destroy'])->name('speaking.part2.exercises.destroy');

    Route::get('speaking/part3/exercises',[\App\Http\Controllers\Admin\ExcerciseController::class,'speaking_part3'])->name('speaking.part3.exercise.index');
    Route::get('speaking/part3/exercises/create',[\App\Http\Controllers\Admin\ExcerciseController::class,'speaking_part3_create'])->name('speaking.part3.exercises.create');
    Route::post('speaking/part3/exercises/store',[\App\Http\Controllers\Admin\ExcerciseController::class,'speaking_part3_store'])->name('speaking.part3.exercises.store');
    Route::get('speaking/part3/exercises/{excercise}/edit', [\App\Http\Controllers\Admin\ExcerciseController::class, 'speaking_part3_edit'])->name('speaking.part3.exercises.edit');
    Route::put('speaking/part3/exercises/{excercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'speaking_part3_update'])->name('speaking.part3.exercises.update');

    Route::get('speaking/full/exercises',[\App\Http\Controllers\Admin\ExcerciseController::class,'speaking_full'])->name('speaking.full.exercise.index');
    Route::get('speaking/full/exercises/create',[\App\Http\Controllers\Admin\ExcerciseController::class,'speaking_full_create'])->name('speaking.full.exercises.create');
    Route::post('speaking/full/exercises/store',[\App\Http\Controllers\Admin\ExcerciseController::class,'speaking_full_store'])->name('speaking.full.exercises.store');
    Route::get('speaking/full/exercises/{excercise}/edit', [\App\Http\Controllers\Admin\ExcerciseController::class, 'speaking_full_edit'])->name('speaking.full.exercises.edit');
    Route::put('speaking/full/exercises/{excercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'speaking_full_update'])->name('speaking.full.exercises.update');


    // speaking exercises
    Route::get('speaking_exercise/part_one/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'index_part_one'])->name('speakings_exercise.part_one.index');
    Route::get('speaking_exercise/part_one/create/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'part_one_create'])->name('speakings_exercise.part_one.create');
    Route::put('speaking_exercise/part_one/store', [\App\Http\Controllers\SpeakingExerciseController::class, 'part_one_store'])->name('speakings_exercise.part_one.store');
    Route::get('speaking_exercise/part_one/edit/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'part_one_edit'])->name('speakings_exercise.part_one.edit');
    Route::put('speaking_exercise/part_one/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'part_one_update'])->name('speakings_exercise.part_one.update');
    Route::delete('speaking_exercise/delete/{exercise}', [\App\Http\Controllers\SpeakingExerciseController::class, 'destroy'])->name('speaking.exercises.destroy');

    Route::get('speaking_exercise/part_two/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'index_part_two'])->name('speakings_exercise.part_two.index');
    Route::get('speaking_exercise/part_two/create/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'part_two_create'])->name('speakings_exercise.part_two.create');
    Route::put('speaking_exercise/part_two/store', [\App\Http\Controllers\SpeakingExerciseController::class, 'part_two_store'])->name('speakings_exercise.part_two.store');
    Route::get('speaking_exercise/part_two/edit/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'part_two_edit'])->name('speakings_exercise.part_two.edit');
    Route::put('speaking_exercise/part_two/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'part_two_update'])->name('speakings_exercise.part_two.update');

    Route::get('speaking_exercise/part_three/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'index_part_three'])->name('speakings_exercise.part_three.index');
    Route::get('speaking_exercise/part_three/create/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'part_three_create'])->name('speakings_exercise.part_three.create');
    Route::put('speaking_exercise/part_three/store', [\App\Http\Controllers\SpeakingExerciseController::class, 'part_three_store'])->name('speakings_exercise.part_three.store');
    Route::get('speaking_exercise/part_three/edit/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'part_three_edit'])->name('speakings_exercise.part_three.edit');
    Route::put('speaking_exercise/part_three/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'part_three_update'])->name('speakings_exercise.part_three.update');

    Route::get('speaking_exercise/full/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'index_full'])->name('speakings_exercise.full.index');
    Route::get('speaking_exercise/full/create/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'full_create'])->name('speakings_exercise.full.create');
    Route::put('speaking_exercise/full/store', [\App\Http\Controllers\SpeakingExerciseController::class, 'full_store'])->name('speakings_exercise.full.store');
    Route::get('speaking_exercise/full/edit/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'full_edit'])->name('speakings_exercise.full.edit');
    Route::put('speaking_exercise/full/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'full_update'])->name('speakings_exercise.full.update');


    // part 1
    Route::get('speaking/part1/{id}', [\App\Http\Controllers\SpeakingPartOneFirstController::class, 'index'])->name('speaking.part1.index');

    Route::get('speaking/part1_1/question/create/{id}', [\App\Http\Controllers\SpeakingPartOneFirstQuestionsController::class, 'create1'])->name('speaking.part1.first.question.create');
    Route::post('speaking/part1_1/question/store', [\App\Http\Controllers\SpeakingPartOneFirstQuestionsController::class, 'store1'])->name('speaking.part1.1.question.store');
    Route::get('speaking/part1_1/question/edit/{id}/{speaking_exercise_id}', [\App\Http\Controllers\SpeakingPartOneFirstQuestionsController::class, 'edit1'])->name('speaking.part1.1.question.edit');
    Route::post('speaking/part1_1/question/update/{id}', [\App\Http\Controllers\SpeakingPartOneFirstQuestionsController::class, 'update1'])->name('speaking.part1.1.question.update');
    Route::delete('speaking/part1_1/question/destroy/{id}', [\App\Http\Controllers\SpeakingPartOneFirstQuestionsController::class, 'destroy'])->name('speaking.part1.1.question.destroy');

    Route::get('speaking/part1_2/question/create/{id}', [\App\Http\Controllers\SpeakingPartOneSecondQuestionsController::class, 'create'])->name('speaking.part1.2.question.create');
    Route::post('speaking/part1_2/question/store', [\App\Http\Controllers\SpeakingPartOneSecondQuestionsController::class, 'store'])->name('speaking.part1.2.question.store');
    Route::get('speaking/part1_2/question/edit/{id}/{speaking_exercise_id}', [\App\Http\Controllers\SpeakingPartOneSecondQuestionsController::class, 'edit'])->name('speaking.part1.2.question.edit');
    Route::post('speaking/part1_2/question/update/{id}', [\App\Http\Controllers\SpeakingPartOneSecondQuestionsController::class, 'update'])->name('speaking.part1.2.question.update');
    Route::delete('speaking/part1_2/question/destroy/{id}', [\App\Http\Controllers\SpeakingPartOneSecondQuestionsController::class, 'destroy'])->name('speaking.part1.2.question.destroy');

    Route::get('speaking/part1_2/image/create/{id}', [\App\Http\Controllers\SpeakingPartOneSecondImagesController::class, 'create'])->name('speaking.part1.2.image.create');
    Route::post('speaking/part1_2/image/store', [\App\Http\Controllers\SpeakingPartOneSecondImagesController::class, 'store'])->name('speaking.part1.2.image.store');
    Route::get('speaking/part1_2/image/edit/{id}/{speaking_exercise_id}', [\App\Http\Controllers\SpeakingPartOneSecondImagesController::class, 'edit'])->name('speaking.part1.2.image.edit');
    Route::post('speaking/part1_2/image/update/{id}', [\App\Http\Controllers\SpeakingPartOneSecondImagesController::class, 'update'])->name('speaking.part1.2.image.update');
    Route::delete('speaking/part1_2/image/destroy/{id}', [\App\Http\Controllers\SpeakingPartOneSecondImagesController::class, 'destroy'])->name('speaking.part1.2.image.destroy');

    // part 2
    Route::get('speaking/part2/{id}', [\App\Http\Controllers\SpeakingPartTwoController::class, 'index'])->name('speaking.part2.index');


    Route::get('speaking/part2/question/create/{id}', [\App\Http\Controllers\SpeakingPartTwoQuestionsController::class, 'create'])->name('speaking.part2.question.create');
    Route::post('speaking/part2/question/store', [\App\Http\Controllers\SpeakingPartTwoQuestionsController::class, 'store'])->name('speaking.part2.question.store');
    Route::get('speaking/part2/question/edit/{id}/{speaking_exercise_id}', [\App\Http\Controllers\SpeakingPartTwoQuestionsController::class, 'edit'])->name('speaking.part2.question.edit');
    Route::post('speaking/part2/question/update/{id}', [\App\Http\Controllers\SpeakingPartTwoQuestionsController::class, 'update'])->name('speaking.part2.question.update');
    Route::delete('speaking/part2/question/destroy/{id}', [\App\Http\Controllers\SpeakingPartTwoQuestionsController::class, 'destroy'])->name('speaking.part2.question.destroy');

    Route::get('speaking/part2/image/create/{id}', [\App\Http\Controllers\SpeakingPartOneSecondImagesController::class, 'create2'])->name('speaking.part2.image.create');
    Route::post('speaking/part2/image/store', [\App\Http\Controllers\SpeakingPartOneSecondImagesController::class, 'store2'])->name('speaking.part2.image.store');
    Route::get('speaking/part2/image/edit/{id}/{speaking_exercise_id}', [\App\Http\Controllers\SpeakingPartOneSecondImagesController::class, 'edit2'])->name('speaking.part2.image.edit');
    Route::post('speaking/part2/image/update/{id}', [\App\Http\Controllers\SpeakingPartOneSecondImagesController::class, 'update2'])->name('speaking.part2.image.update');
    Route::delete('speaking/part2/image/destroy/{id}', [\App\Http\Controllers\SpeakingPartOneSecondImagesController::class, 'destroy1'])->name('speaking.part2.image.destroy');


    // part 3
    Route::get('speaking/part3/{id}', [\App\Http\Controllers\SpeakingPartThreeController::class, 'index'])->name('speaking.part3.index');

    Route::get('speaking/part2/text/edit/{id}', [\App\Http\Controllers\SpeakingPartThreeQuestionsController::class, 'edit'])->name('part3.speaking.text.edit');
    Route::put('speaking/part2/text/update/{id}', [\App\Http\Controllers\SpeakingPartThreeController::class, 'update'])->name('part3.speaking.text.update');

//    Route::get('speaking/part3/for_detail/create/{id}', [\App\Http\Controllers\SpeakingPartThreeQuestionsController::class, 'create'])->name('speaking.part3.question.create');
//    Route::post('speaking/part3/for_detail/store', [\App\Http\Controllers\SpeakingPartThreeQuestionsController::class, 'store'])->name('speaking.part3.question.store');
//    Route::get('speaking/part3/for_detail/edit/{id}/{speaking_exercise_id}', [\App\Http\Controllers\SpeakingPartThreeQuestionsController::class, 'edit'])->name('speaking.part3.question.edit');
//    Route::post('speaking/part3/for_detail/update/{id}', [\App\Http\Controllers\SpeakingPartThreeQuestionsController::class, 'update'])->name('speaking.part3.question.update');
//    Route::delete('speaking/part3/for_detail/destroy/{id}', [\App\Http\Controllers\SpeakingPartThreeQuestionsController::class, 'destroy'])->name('speaking.part3.question.destroy');


    Route::get('speaking/part3/against_detail/create/{id}', [\App\Http\Controllers\SpeakingPartThreeQuestionsController::class, 'create1'])->name('speaking.part3.against.create');
    Route::post('speaking/part3/against_detail/store', [\App\Http\Controllers\SpeakingPartThreeQuestionsController::class, 'store1'])->name('speaking.part3.against.store');
    Route::get('speaking/part3/against_detail/edit/{id}/{speaking_exercise_id}', [\App\Http\Controllers\SpeakingPartThreeQuestionsController::class, 'edit'])->name('speaking.part3.against.edit');
    Route::post('speaking/part3/against_detail/update/{id}', [\App\Http\Controllers\SpeakingPartThreeQuestionsController::class, 'update'])->name('speaking.part3.against.update');
    Route::delete('speaking/part3/against_detail/destroy/{id}', [\App\Http\Controllers\SpeakingPartThreeQuestionsController::class, 'destroy'])->name('speaking.part3.against.destroy');


});
