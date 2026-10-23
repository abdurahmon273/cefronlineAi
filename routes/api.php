<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StepPageController;
use App\Http\Controllers\Api\V1\Admin\CourseApiController;
use App\Http\Controllers\Api\V1\Admin\Listenings\ExerciseApiController;
use App\Http\Controllers\Api\V2\Course\LessonV2ApiController;
use App\Http\Controllers\Api\V2\Course\SavedLessonsV2ApiController;
use App\Http\Controllers\Api\V2\WithoutAuth\V2CityApiController;
use App\Http\Controllers\Api\V2\WithoutAuth\V2RegionApiController;
use Illuminate\Support\Facades\Route;

//  VAQTINCHALIK: is_uzbekistan diagnostikasi. Tekshirib bo'lgach o'chirilsin.
    Route::get('/debug/ip', [\App\Http\Controllers\Api\V2\Test\DebugApiController::class, 'ip']);

//  v2

    Route::post('/v2/register', [\App\Http\Controllers\Api\V2\Auth\AuthApiController::class, 'register']);
    Route::post('/v2/verify_sms', [\App\Http\Controllers\Api\V2\Auth\AuthApiController::class, 'verify_sms']);
    Route::post('/v3/login', [\App\Http\Controllers\Api\V2\Auth\AuthApiController::class, 'login_v2']);
    Route::post('/v2/forgotpassword', [\App\Http\Controllers\Api\V2\Auth\AuthApiController::class, 'forgotpassword']);
    Route::post('/v2/verify_password', [\App\Http\Controllers\Api\V2\Auth\AuthApiController::class, 'verify_password']);
    Route::post('/v2/resetPassword', [\App\Http\Controllers\Api\V2\Auth\AuthApiController::class, 'resetPassword']);
    Route::get('/v2/google/link', [\App\Http\Controllers\Api\V2\Auth\AuthApiController::class, 'google_link']);
    Route::get('/v2/auth/google/callback', [\App\Http\Controllers\Api\V2\Auth\AuthApiController::class, 'google_callback']);
    Route::post('/v2/auth/exchange-token', [\App\Http\Controllers\Api\V2\Auth\AuthApiController::class, 'exchange_login_token']);

    Route::get('/v2/regions', [V2RegionApiController::class, 'index']);
    Route::get('/v2/cities/{id}', [V2CityApiController::class, 'index']);
    Route::get('/v2/stepPages',[StepPageController::class,'index']);


    Route::post('/v1/register', [\App\Http\Controllers\Api\V1\Admin\AuthApiController::class, 'register']);
    Route::post('/v1/verify_sms', [\App\Http\Controllers\Api\V1\Admin\AuthApiController::class, 'verify_sms']);
    Route::post('/v1/login', [\App\Http\Controllers\Api\V1\Admin\AuthApiController::class, 'login']);
    Route::post('/v2/login', [\App\Http\Controllers\Api\V1\Admin\AuthApiController::class, 'login_v2']);
    Route::post('/v1/forgotpassword', [\App\Http\Controllers\Api\V1\Admin\AuthApiController::class, 'forgotpassword']);
    Route::post('/v1/verify_password', [\App\Http\Controllers\Api\V1\Admin\AuthApiController::class, 'verify_password']);
    Route::post('/v1/resetPassword', [\App\Http\Controllers\Api\V1\Admin\AuthApiController::class, 'resetPassword']);
    Route::get('/v1/regions', [\App\Http\Controllers\Api\V1\Admin\RegionApiController::class, 'index']);
    Route::get('/v1/cities/{id}', [\App\Http\Controllers\Api\V1\Admin\CityApiController::class, 'index']);
    Route::get('/v1/stepPages',[StepPageController::class,'index']);




//. Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum','is_verified']], function () {

    Route::group(['prefix' => 'v2', 'as' => 'api.', 'middleware' => ['auth:sanctum','is_verified']], function () {

        // Profile
        Route::get('profile', [\App\Http\Controllers\Api\V2\Profile\UsersApiController::class, 'profile']);
        Route::post('profile-update', [\App\Http\Controllers\Api\V2\Profile\UsersApiController::class, 'update']);
        Route::post('password-update', [\App\Http\Controllers\Api\V2\Profile\UsersApiController::class, 'update_password']);
        Route::get('logout', [\App\Http\Controllers\Api\V2\Profile\UsersApiController::class, 'logout']);
        Route::post('/delete_account', [\App\Http\Controllers\Api\V2\Profile\UsersApiController::class, 'delete']);

        // Courses
        Route::get('/courses', [\App\Http\Controllers\Api\V2\Course\CourseApiController::class, 'index']);
        Route::get('/course/show/{id}', [LessonV2ApiController::class, 'index']);
        Route::get('videos/{id}', [\App\Http\Controllers\Api\V2\Course\VideoApiController::class, 'index']);
        Route::get('comments/{id}', [\App\Http\Controllers\Api\V2\Course\CommentApiController::class, 'index']);
        Route::post('comments-store/{id}', [\App\Http\Controllers\Api\V2\Course\CommentApiController::class, 'store'])->name('comments.store');
        Route::post('view_video', [\App\Http\Controllers\Api\V2\Course\CourseApiController::class,'percentage']);
        Route::post('saved-lessons-store/{id}', [SavedLessonsV2ApiController::class, 'store']);

        // Saved Lessons
        Route::get('saved-lessons/', [SavedLessonsV2ApiController::class, 'index']);


        // news
        Route::get('newss', [\App\Http\Controllers\Api\V2\News\NewsApiController::class, 'index']);
        Route::get('newss/show/{id}', [\App\Http\Controllers\Api\V2\News\NewsApiController::class, 'show']);
        Route::post('newss/join/{news}', [\App\Http\Controllers\Api\V2\News\QabulApiController::class,'join']);

        // Listening
        Route::get('listening/exercises',[\App\Http\Controllers\Api\V2\Home\ExerciseApiController::class,'index']);
        Route::get('listening_exercise',[\App\Http\Controllers\Api\V2\Listening\ListeningExerciseApiController::class,'index']);
        Route::get('listening/next/exercise/{id}',[\App\Http\Controllers\Api\V2\Listening\ListeningExerciseApiController::class,'next_exercise']);
        Route::post('listening_save',[\App\Http\Controllers\Api\V2\Listening\ListeningExerciseApiController::class,'save']);
        Route::get('listening/results',[\App\Http\Controllers\Api\V2\Listening\ListeningExerciseApiController::class,'results']);
        Route::get('listening/result/show/{id}',[\App\Http\Controllers\Api\V2\Listening\ListeningExerciseApiController::class,'result_show']);

        // Reading
        Route::get('reading/exercises',[\App\Http\Controllers\Api\V2\Home\ExerciseApiController::class,'reading']);
        Route::get('reading_exercise',[\App\Http\Controllers\Api\V2\Reading\ReadingExerciseApiController::class,'index']);
        Route::get('reading/next/exercise/{id}',[\App\Http\Controllers\Api\V2\Reading\ReadingExerciseApiController::class,'next_exercise']);
        Route::post('reading/save',[\App\Http\Controllers\Api\V2\Reading\ReadingExerciseApiController::class,'save']);
        Route::get('reading/results',[\App\Http\Controllers\Api\V2\Reading\ReadingExerciseApiController::class,'saved_results']);
        Route::get('reading/result/show/{id}',[\App\Http\Controllers\Api\V2\Reading\ReadingExerciseApiController::class,'result_show']);

        //Writing
        Route::get('writing/task1/exercises',[\App\Http\Controllers\Api\V2\Writing\WritingExerciseApiController::class,'writing_task1']);
        Route::get('writing/task2/exercises',[\App\Http\Controllers\Api\V2\Writing\WritingExerciseApiController::class,'writing_task2']);
        Route::get('writing/full/exercises',[\App\Http\Controllers\Api\V2\Writing\WritingExerciseApiController::class,'writing_full']);
        Route::get('writing_exercise',[\App\Http\Controllers\Api\V2\Writing\WritingExerciseApiController::class,'index']);
        Route::get('writing/next/exercise/{id}',[\App\Http\Controllers\Api\V2\Writing\WritingExerciseApiController::class,'next_exercise']);
        Route::post('writing/task1/save',[\App\Http\Controllers\Api\V2\Writing\WritingExerciseApiController::class,'task1_save']);
        Route::post('writing/task2/save',[\App\Http\Controllers\Api\V2\Writing\WritingExerciseApiController::class,'task2_save']);
        Route::post('writing/full/save',[\App\Http\Controllers\Api\V2\Writing\WritingExerciseApiController::class,'full_save']);
        Route::get('writing/results',[\App\Http\Controllers\Api\V2\Writing\WritingExerciseApiController::class,'saved_results']);
        Route::get('writing/result/status/{id}',[\App\Http\Controllers\Api\V2\Writing\WritingExerciseApiController::class,'get_result_status']);
        Route::get('writing/result/{id}',[\App\Http\Controllers\Api\V2\Writing\WritingExerciseApiController::class,'result_show']);
        Route::delete('writing/saved_result/delete',[\App\Http\Controllers\Api\V2\Writing\WritingExerciseApiController::class,'delete_saved_result']);
        Route::post('writing/image/change',[\App\Http\Controllers\Api\V2\Writing\WritingExerciseApiController::class,'readImage']);

        //Speaking
        Route::get('speaking/part1/exercises',[\App\Http\Controllers\Api\V2\Speaking\SpeakingExerciseApiController::class,'speaking_part1']);
        Route::get('speaking/part2/exercises',[\App\Http\Controllers\Api\V2\Speaking\SpeakingExerciseApiController::class,'speaking_part2']);
        Route::get('speaking/part3/exercises',[\App\Http\Controllers\Api\V2\Speaking\SpeakingExerciseApiController::class,'speaking_part3']);
        Route::get('speaking/full/exercises',[\App\Http\Controllers\Api\V2\Speaking\SpeakingExerciseApiController::class,'speaking_full']);
        Route::get('speaking_exercise',[\App\Http\Controllers\Api\V2\Speaking\SpeakingExerciseApiController::class,'index']);
        Route::get('speaking/next/exercise/{id}',[\App\Http\Controllers\Api\V2\Speaking\SpeakingExerciseApiController::class,'next_exercise']);
        Route::post('speaking/save',[\App\Http\Controllers\Api\V2\Speaking\SpeakingExerciseApiController::class,'save']);
        Route::get('speaking/result/status/{id}',[\App\Http\Controllers\Api\V2\Speaking\SpeakingExerciseApiController::class,'get_result_status']);
        Route::get('speaking/results',[\App\Http\Controllers\Api\V2\Speaking\SpeakingExerciseApiController::class,'saved_results']);
        Route::get('speaking/result/{id}',[\App\Http\Controllers\Api\V2\Speaking\SpeakingExerciseApiController::class,'result_show']);
        Route::get('speaking/answer/audio',[\App\Http\Controllers\Api\V2\Speaking\SpeakingExerciseApiController::class,'get_audio'])->name('speaking.answer.audio');
        Route::delete('speaking/saved_result/delete',[\App\Http\Controllers\Api\V2\Speaking\SpeakingExerciseApiController::class,'delete_saved_result']);

        //Full Exam
        Route::get('full/exercises',[\App\Http\Controllers\Api\V2\Home\ExerciseApiController::class,'full_index']);
        Route::get('full_exercise',[\App\Http\Controllers\Api\V2\FullExam\FullExerciseApiController::class,'index']);
        Route::post('Full/save',[\App\Http\Controllers\Api\V2\FullExam\FullExerciseApiController::class,'save']);
        Route::get('Full/results',[\App\Http\Controllers\Api\V2\FullExam\FullExerciseApiController::class,'results']);
        Route::get('Full/result/{id}',[\App\Http\Controllers\Api\V2\FullExam\FullExerciseApiController::class,'show']);
        Route::get('Full/reset/{id}',[\App\Http\Controllers\Api\V2\FullExam\FullExerciseApiController::class,'reset']);


        //premium
        Route::get('facilities',[\App\Http\Controllers\Api\V2\Premium\PremiumApiController::class,'facilities']);
        Route::get('informations',[\App\Http\Controllers\Api\V2\Profile\AboutUsApiController::class,'informations']);

        Route::get('plans',[\App\Http\Controllers\Api\V2\Premium\PremiumApiController::class,'plans']);
        Route::get('Packages',[\App\Http\Controllers\Api\V2\Premium\PremiumApiController::class,'Packages']);
        Route::post('checkPromoCode',[\App\Http\Controllers\Api\V2\Premium\PremiumApiController::class,'checkPromoCode']);
        Route::post('freePremium',[\App\Http\Controllers\Api\V2\Premium\PremiumApiController::class,'freePremium']);
        Route::post('freeCredit',[\App\Http\Controllers\Api\V2\Premium\PremiumApiController::class,'freeCredit']);

        Route::get('check_premium',[\App\Http\Controllers\Api\V2\Premium\PremiumApiController::class,'check_premium']);
        Route::get('credit/balance',[\App\Http\Controllers\Api\V2\Premium\PremiumApiController::class,'creditBalance']);

        Route::post('plan/clicked/pay',[\App\Http\Controllers\Api\V2\Premium\PremiumApiController::class,'plan_clicked_pay']);
        Route::post('package/clicked/pay',[\App\Http\Controllers\Api\V2\Premium\PremiumApiController::class,'package_clicked_pay']);


    });


    Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum','is_verified']], function () {

    Route::post('check_device',[\App\Http\Controllers\Api\V1\Admin\AuthApiController::class,'check_device_id']);

    Route::post('/delete_user', [\App\Http\Controllers\Api\V1\Admin\UsersApiController::class, 'delete']);

    Route::get('listening/exercises',[ExerciseApiController::class,'index']);
    Route::get('listening_exercise',[\App\Http\Controllers\Api\V1\Admin\Listenings\ListeningExerciseApiController::class,'index']);
    Route::post('listening_save',[\App\Http\Controllers\Api\V1\Admin\Listenings\ListeningExerciseApiController::class,'save']);
    Route::get('listening/saved_results',[\App\Http\Controllers\Api\V1\Admin\Listenings\ListeningExerciseApiController::class,'saved_results']);

    Route::get('reading/exercises',[ExerciseApiController::class,'reading']);
    Route::get('reading_exercise',[\App\Http\Controllers\Api\V1\Admin\Readings\ReadingExerciseApiController::class,'index']);
    Route::post('reading_save',[\App\Http\Controllers\Api\V1\Admin\Readings\ReadingExerciseApiController::class,'save']);
    Route::get('reading/saved_results',[\App\Http\Controllers\Api\V1\Admin\Readings\ReadingExerciseApiController::class,'saved_results']);


    Route::get('writing/task1/exercises',[\App\Http\Controllers\Api\V1\Admin\WritingExerciseApiController::class,'writing_task1']);
    Route::get('writing/task2/exercises',[\App\Http\Controllers\Api\V1\Admin\WritingExerciseApiController::class,'writing_task2']);
    Route::get('writing/full/exercises',[\App\Http\Controllers\Api\V1\Admin\WritingExerciseApiController::class,'writing_full']);
    Route::get('writing/exercises',[\App\Http\Controllers\Api\V1\Admin\WritingExerciseApiController::class,'writing']);
    Route::get('writing_exercise',[\App\Http\Controllers\Api\V1\Admin\WritingExerciseApiController::class,'index']);
    Route::post('writing_save',[\App\Http\Controllers\Api\V1\Admin\WritingExerciseApiController::class,'save']);
    Route::post('writing_full_save',[\App\Http\Controllers\Api\V1\Admin\WritingExerciseApiController::class,'full_save']);
    Route::get('writing/saved_results',[\App\Http\Controllers\Api\V1\Admin\WritingExerciseApiController::class,'saved_results']);
    Route::delete('writing/saved_result/delete',[\App\Http\Controllers\Api\V1\Admin\WritingExerciseApiController::class,'delete_saved_result']);

    Route::get('speaking/part1/exercises',[\App\Http\Controllers\Api\V1\Admin\SpeakingExerciseApiController::class,'speaking_part1']);
    Route::get('speaking/part2/exercises',[\App\Http\Controllers\Api\V1\Admin\SpeakingExerciseApiController::class,'speaking_part2']);
    Route::get('speaking/part3/exercises',[\App\Http\Controllers\Api\V1\Admin\SpeakingExerciseApiController::class,'speaking_part3']);
    Route::get('speaking/full/exercises',[\App\Http\Controllers\Api\V1\Admin\SpeakingExerciseApiController::class,'speaking_full']);
    Route::get('speaking_exercise',[\App\Http\Controllers\Api\V1\Admin\SpeakingExerciseApiController::class,'index']);

    Route::post('speaking_save',[\App\Http\Controllers\Api\V1\Admin\SpeakingExerciseApiController::class,'save']);
    Route::get('speaking/saved_results',[\App\Http\Controllers\Api\V1\Admin\SpeakingExerciseApiController::class,'saved_results']);
    Route::delete('speaking/saved_result/delete',[\App\Http\Controllers\Api\V1\Admin\SpeakingExerciseApiController::class,'delete_saved_result']);



    Route::get('full/exercises',[ExerciseApiController::class,'full_index']);
    Route::get('full_exercise',[\App\Http\Controllers\Api\V1\Admin\FullExerciseApiController::class,'index']);


    Route::get('facilities',[\App\Http\Controllers\Api\V1\Admin\PremiumApiController::class,'facilities']);
    Route::get('plans',[\App\Http\Controllers\Api\V1\Admin\PremiumApiController::class,'plans']);
    Route::get('check_premium',[\App\Http\Controllers\Api\V1\Admin\PremiumApiController::class,'check_premium']);
    Route::get('test_unpremium',[\App\Http\Controllers\Api\V1\Admin\PremiumApiController::class,'test_unpremium']);

    Route::get('informations',[\App\Http\Controllers\Api\V1\Admin\Aboutus\AboutUsApiController::class,'informations']);


    Route::post('checkPromoCode',[\App\Http\Controllers\Api\V1\Admin\PremiumApiController::class,'checkPromoCode']);
    Route::post('freePremium',[\App\Http\Controllers\Api\V1\Admin\PremiumApiController::class,'freePremium']);
    Route::post('clicked_pay',[\App\Http\Controllers\Api\V1\Admin\PremiumApiController::class,'clicked_pay'])->withoutMiddleware(['auth:sanctum','is_verified']);



    // Home
    Route::get('profile/{id}', [\App\Http\Controllers\Api\V1\Admin\UsersApiController::class, 'profile']);
    Route::post('profile-update/{id}', [\App\Http\Controllers\Api\V1\Admin\UsersApiController::class, 'update']);
    Route::get('logout', [\App\Http\Controllers\Api\V1\Admin\UsersApiController::class, 'logout']);

    //Qabul

    // Permissions
//    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
//    Route::apiResource('roles', 'RolesApiController');

    // Users
//    Route::apiResource('users', 'UsersApiController');

    // Region
//    Route::apiResource('regions', 'RegionApiController');

    // City
//    Route::apiResource('cities', 'CityApiController');

        // News
        Route::post('newss/media', 'NewsApiController@storeMedia')->name('newss.storeMedia');
        Route::post('newss/join/{news}', [\App\Http\Controllers\Api\V1\Admin\QabulApiController::class,'join'])->name('newss1.store');
        Route::get('newss/show/{id}', [\App\Http\Controllers\Api\V1\Admin\NewsApiController::class, 'show']);
        Route::apiResource('newss', \App\Http\Controllers\Api\V1\Admin\NewsApiController::class);

        // Application
//    Route::apiResource('applications', 'ApplicationApiController');

        // Shablon
//    Route::apiResource('shablons', 'ShablonApiController');

        // Message
//    Route::apiResource('messages', 'MessageApiController');

        // Course
        Route::post('courses/media', 'CourseApiController@storeMedia')->name('courses.storeMedia');
        Route::post('percentage', [\App\Http\Controllers\Api\V1\Admin\CourseApiController::class,'percentage'])->name('courses.percentage');
        Route::apiResource('courses', 'CourseApiController');

        // Saved Lessons
        Route::get('saved-lessons/', [\App\Http\Controllers\Api\V1\Admin\SavedLessonsApiController::class, 'index']);
        Route::post('saved-lessons-store/{id}', [\App\Http\Controllers\Api\V1\Admin\SavedLessonsApiController::class, 'store']);

        // Lesson
        Route::post('lessons/media', 'LessonApiController@storeMedia')->name('lessons.storeMedia');
        Route::get('lessons/{id}', [\App\Http\Controllers\Api\V1\Admin\LessonApiController::class, 'index'])->name('lessons.index');

        // Video
        Route::post('videos/media', 'VideoApiController@storeMedia')->name('videos.storeMedia');
        Route::get('videos/{id}', [\App\Http\Controllers\Api\V1\Admin\VideoApiController::class, 'index'])->name('videos.index');
        Route::get('info/{id}', [\App\Http\Controllers\Api\V1\Admin\VideoApiController::class, 'info']);

        // Comment
        Route::get('comments/{id}', [\App\Http\Controllers\Api\V1\Admin\CommentApiController::class, 'index'])->name('v1.comments.index');
        Route::post('comments-store/{id}', [\App\Http\Controllers\Api\V1\Admin\CommentApiController::class, 'store'])->name('v1.comments.store');
    });
