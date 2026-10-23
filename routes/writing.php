<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {

    Route::get('writing/exercises',[\App\Http\Controllers\Admin\ExcerciseController::class,'writing_index'])->name('writing.exercises.index');

    Route::get('writing/task1/exercises',[\App\Http\Controllers\Admin\ExcerciseController::class,'writing_task1'])->name('writing.task1.exercise.index');
    Route::get('writing/exercises/create',[\App\Http\Controllers\Admin\ExcerciseController::class,'writing_task1_create'])->name('writing.task1.exercises.create');
    Route::post('writing/exercises/store',[\App\Http\Controllers\Admin\ExcerciseController::class,'writing_task1_store'])->name('writing.task1.exercises.store');
    Route::get('writing/exercises/{excercise}/edit', [\App\Http\Controllers\Admin\ExcerciseController::class, 'writing_task1_edit'])->name('writing.task1.exercises.edit');
    Route::put('writing/exercises/{excercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'writing_task1_update'])->name('writing.task1.exercises.update');
    Route::delete('writing/exercises/{exercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'writing_destroy'])->name('writing.task1.exercises.destroy');

    Route::get('writing/task2/exercises',[\App\Http\Controllers\Admin\ExcerciseController::class,'writing_task2'])->name('writing.task2.exercise.index');
    Route::get('writing/task2/exercises/create',[\App\Http\Controllers\Admin\ExcerciseController::class,'writing_task2_create'])->name('writing.task2.exercises.create');
    Route::post('writing/task2/exercises/store',[\App\Http\Controllers\Admin\ExcerciseController::class,'writing_task2_store'])->name('writing.task2.exercises.store');
    Route::get('writing/task2/exercises/{excercise}/edit', [\App\Http\Controllers\Admin\ExcerciseController::class, 'writing_task2_edit'])->name('writing.task2.exercises.edit');
    Route::put('writing/task2/exercises/{excercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'writing_task2_update'])->name('writing.task2.exercises.update');
    Route::delete('writing/task2/exercises/{exercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'writing_destroy'])->name('writing.task2.exercises.destroy');

    Route::get('writing/full/exercises',[\App\Http\Controllers\Admin\ExcerciseController::class,'writing_full'])->name('writing.full.exercises.index');
    Route::get('writing/full/exercises/create',[\App\Http\Controllers\Admin\ExcerciseController::class,'writing_full_create'])->name('writing.full.exercises.create');
    Route::post('writing/full/exercises/store',[\App\Http\Controllers\Admin\ExcerciseController::class,'writing_full_store'])->name('writing.full.exercises.store');
    Route::get('writing/full/exercises/{excercise}/edit', [\App\Http\Controllers\Admin\ExcerciseController::class, 'writing_full_edit'])->name('writing.full.exercises.edit');
    Route::put('writing/full/exercises/{excercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'writing_full_update'])->name('writing.full.exercises.update');




    Route::get('writing_exercise/task_one/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'index_task_one'])->name('writings_exercise.task_one.index');
    Route::get('writing_exercise/task_one/create/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'task_one_create'])->name('writings_exercise.task_one.create');
    Route::put('writing_exercise/task_one/store', [\App\Http\Controllers\WritingExerciseController::class, 'task_one_store'])->name('writings_exercise.task_one.store');
    Route::get('writing_exercise/task_one/edit/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'task_one_edit'])->name('writings_exercise.task_one.edit');
    Route::put('writing_exercise/task_one/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'task_one_update'])->name('writings_exercise.task_one.update');


    Route::get('writing_exercise/task_two/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'index_task_two'])->name('writings_exercise.task_two.index');
    Route::get('writing_exercise/task_two/create/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'task_two_create'])->name('writings_exercise.task_two.create');
    Route::put('writing_exercise/task_two/store', [\App\Http\Controllers\WritingExerciseController::class, 'task_two_store'])->name('writings_exercise.task_two.store');
    Route::get('writing_exercise/task_two/edit/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'task_two_edit'])->name('writings_exercise.task_two.edit');
    Route::put('writing_exercise/task_two/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'task_two_update'])->name('writings_exercise.task_two.update');

    Route::get('writing_exercise/full/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'index_full'])->name('writings_exercise.full.index');
    Route::get('writing_exercise/full/create/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'full_create'])->name('writings_exercise.full.create');
    Route::put('writing_exercise/full/store', [\App\Http\Controllers\WritingExerciseController::class, 'full_store'])->name('writings_exercise.full.store');
    Route::get('writing_exercise/full/edit/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'full_edit'])->name('writings_exercise.full.edit');
    Route::put('writing_exercise/full/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'full_update'])->name('writings_exercise.full.update');


    Route::get('writing/exercise/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'index'])->name('writings.exercise.index');
    Route::get('writing/exercise/create/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'create'])->name('writings.exercise.create');
    Route::put('writing/exercise/store', [\App\Http\Controllers\WritingExerciseController::class, 'store'])->name('writings.exercise.store');
    Route::get('writing/exercise/edit/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'edit'])->name('writings.exercise.edit');
    Route::put('writing/exercise/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'update'])->name('writings.exercise.update');
    Route::delete('writing/exercise/{exercise}', [\App\Http\Controllers\WritingExerciseController::class, 'destroy'])->name('writings.exercise.destroy');

});
