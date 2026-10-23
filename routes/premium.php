<?php

use App\Http\Controllers\FacilityController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PromocodeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PremiumToUsersController;


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {

//    Route::resource('facilities', FacilityController::class);
    Route::get('facilities/index', [FacilityController::class, 'index'])->name('facilities.index');
    Route::get('facilities/create',[FacilityController::class,'create'])->name('facilities.create');
    Route::post('facilities/store',[FacilityController::class,'store'])->name('facilities.store');
    Route::get('facilities/edit/{id}', [FacilityController::class, 'edit'])->name('facilities.edit');
    Route::post('facilities/update/{id}', [FacilityController::class, 'update'])->name('facilities.update');
    Route::delete('facilities/delete/{id}', [FacilityController::class, 'destroy'])->name('facilities.destroy');

    Route::get('plans/index', [PlanController::class, 'index'])->name('plans.index');
    Route::get('plans/create',[PlanController::class,'create'])->name('plans.create');
    Route::post('plans/store',[PlanController::class,'store'])->name('plans.store');
    Route::get('plans/edit/{id}', [PlanController::class, 'edit'])->name('plans.edit');
    Route::post('plans/update/{id}', [PlanController::class, 'update'])->name('plans.update');
    Route::delete('plans/delete/{id}', [PlanController::class, 'destroy'])->name('plans.destroy');

    Route::get('packages/index', [PackageController::class, 'index'])->name('packages.index');
    Route::get('packages/create',[PackageController::class,'create'])->name('packages.create');
    Route::post('packages/store',[PackageController::class,'store'])->name('packages.store');
    Route::get('packages/edit/{id}', [PackageController::class, 'edit'])->name('packages.edit');
    Route::post('packages/update/{id}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('packages/delete/{id}', [PackageController::class, 'destroy'])->name('packages.destroy');

    Route::get('premium_users/index', [PremiumToUsersController::class, 'index'])->name('premium_users.index');
    Route::get('premium_users/create',[PremiumToUsersController::class,'create'])->name('premium_users.create');
    Route::get('premium_users/edit/{id}', [PremiumToUsersController::class, 'edit'])->name('premium_users.edit');
    Route::delete('premium_users/delete/{id}', [PremiumToUsersController::class, 'destroy'])->name('premium_users.destroy');

    Route::get('package_users/index', [PremiumToUsersController::class, 'package_index'])->name('package_users.index');
    Route::get('package_users/create',[PremiumToUsersController::class, 'package_create'])->name('package_users.create');
    Route::get('package_users/edit/{id}', [PremiumToUsersController::class, 'package_edit'])->name('package_users.edit');
    Route::delete('package_users/delete/{id}', [PremiumToUsersController::class, 'package_destroy'])->name('package_users.destroy');

    Route::get('promoCodes/index', [PromocodeController::class, 'index'])->name('promoCodes.index');
    Route::get('promoCodes/create',[PromocodeController::class,'create'])->name('promoCodes.create');
    Route::get('promoCodes/create/once',[PromocodeController::class,'createOnce'])->name('promoCodes.create.once');
    Route::post('promoCodes/store/once',[PromocodeController::class,'storeOnce'])->name('promoCodes.store.once');
    Route::post('promoCodes/store',[PromocodeController::class,'store'])->name('promoCodes.store');
    Route::get('promoCodes/edit/{id}', [PromocodeController::class, 'edit'])->name('promoCodes.edit');
    Route::post('promoCodes/update/{id}', [PromocodeController::class, 'update'])->name('promoCodes.update');
    Route::delete('promoCodes/delete/{id}', [PromocodeController::class, 'destroy'])->name('promoCodes.destroy');
});
