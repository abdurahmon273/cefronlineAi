<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {

    Route::get('full_mock/exercises',[\App\Http\Controllers\Admin\ExcerciseController::class,'full_index'])->name('full.exercises.index');

    Route::get('listening/exercise/{id}', [\App\Http\Controllers\Admin\ListeningExerciseController::class, 'index1'])->name('listenings.exercise.index');

    Route::get('full/exercises/create',[\App\Http\Controllers\Admin\ExcerciseController::class,'full_create'])->name('full.exercises.create');
    Route::post('full/exercises/store',[\App\Http\Controllers\Admin\ExcerciseController::class,'full_store'])->name('full.exercises.store');
    Route::get('full/exercises/{excercise}/edit', [\App\Http\Controllers\Admin\ExcerciseController::class, 'full_edit'])->name('full.exercises.edit');
    Route::put('full/exercises/{excercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'full_update'])->name('full.exercises.update');
    Route::delete('full/exercises/{exercise}', [\App\Http\Controllers\Admin\ExcerciseController::class, 'destroy'])->name('full.exercises.destroy');

    // full exercise

    Route::get('full_exercise/{id}', [\App\Http\Controllers\FullExerciseController::class, 'index'])->name('full_exercise.index');
    Route::get('full_exercise/create/{id}', [\App\Http\Controllers\FullExerciseController::class, 'create'])->name('full_exercise.create');
    Route::put('full_exercise/store', [\App\Http\Controllers\FullExerciseController::class, 'store'])->name('full_exercise.store');
    Route::get('full_exercise/edit/{id}', [\App\Http\Controllers\FullExerciseController::class, 'edit'])->name('full_exercise.edit');
    Route::put('full_exercise/{id}', [\App\Http\Controllers\FullExerciseController::class, 'update'])->name('full_exercise.update');
    Route::delete('full_exercise/delete/{id}', [\App\Http\Controllers\FullExerciseController::class, 'destroy'])->name('full_exercise.delete');

    Route::get('full_exercise/more/{id}', [\App\Http\Controllers\FullExerciseController::class, 'index1'])->name('full_exercise.more.index');

    Route::get('full_mock/listening_exercise/change/{id}', [\App\Http\Controllers\Admin\ListeningExerciseController::class, 'change'])->name('full.listening_exercise.change');
    Route::put('full_mock/listening_exercise/store', [\App\Http\Controllers\Admin\ListeningExerciseController::class, 'store2'])->name('full.listening_exercise.store');
    Route::put('full_mock/listening_exercise/update', [\App\Http\Controllers\Admin\ListeningExerciseController::class, 'update2'])->name('full.listening_exercise.update');
    Route::get('listening_full_exercise/{id}', [\App\Http\Controllers\FullExerciseController::class, 'listening_index'])->name('listening_full_exercise.index');

    Route::get('full_mock/reading_exercise/change/{id}', [\App\Http\Controllers\ReadingExerciseController::class, 'change'])->name('full.reading_exercise.change');
    Route::put('full_mock/reading_exercise/store', [\App\Http\Controllers\ReadingExerciseController::class, 'store1'])->name('full.reading_exercise.store');
    Route::put('full_mock/reading_exercise/update', [\App\Http\Controllers\ReadingExerciseController::class, 'update1'])->name('full.reading_exercise.update');
    Route::get('reading_full_exercise/{id}', [\App\Http\Controllers\FullExerciseController::class, 'reading_index'])->name('reading_full_exercise.index');

    Route::get('full_mock/writing_exercise/change/{id}', [\App\Http\Controllers\WritingExerciseController::class, 'change'])->name('full.writing_exercise.change');
    Route::put('full_mock/writing_exercise/store', [\App\Http\Controllers\WritingExerciseController::class, 'full_store1'])->name('full.writing_exercise.store');
    Route::put('full_mock/writing_exercise/update', [\App\Http\Controllers\WritingExerciseController::class, 'full_update1'])->name('full.writing_exercise.update');


    Route::get('full_mock/speaking_exercise/change/{id}', [\App\Http\Controllers\SpeakingExerciseController::class, 'change'])->name('full.speaking_exercise.change');
    Route::put('full_mock/speaking_exercise/store', [\App\Http\Controllers\SpeakingExerciseController::class, 'full_store1'])->name('full.speaking_exercise.store');
    Route::put('full_mock/speaking_exercise/update', [\App\Http\Controllers\SpeakingExerciseController::class, 'full_update1'])->name('full.speaking_exercise.update');
    Route::get('speaking_full_exercise/{id}', [\App\Http\Controllers\FullExerciseController::class, 'speaking_index'])->name('speaking_full_exercise.index');


});
