<?php

use App\Http\Controllers\Admin\AudioController;
use App\Http\Controllers\Admin\StepPageController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SwaggerLoginController;
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\LoginBotController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\User\ResultController;
use App\Http\Controllers\User\TestController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\MarketController;
use App\Models\WritingPrompt;
use App\Services\DeepgramService;
use App\Services\SpeakingStoreService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

include "listening.php";
include "reading.php";
include "writing.php";
include "speaking.php";
include "full_mock.php";
include "premium.php";
include 'payment.php';

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

Route::get('/.well-known/assetlinks.json', function () {
    $path = public_path('api/assetlinks.json');
    return response()->file($path, ['Content-Type' => 'application/json']);
});

Route::get('/.well-known/apple-app-site-association', function () {
    $path = public_path('api/apple-app-site-association');
    return response()->file($path, ['Content-Type' => 'application/json']);
});

Route::get('/auth-callback', [GoogleAuthController::class, 'auth_callback'])->name('auth.callback');
// Web login — app link'ga BOG'LANMAGAN alohida path (interstitial tugmasidan keladi)
Route::get('/web-login', [GoogleAuthController::class, 'web_login_token'])->name('web.login.token');

Route::post('/telegram/webhook', [TelegramController::class, 'handle']);

Route::get('/sms_jonat', function () {

    function getToken($login, $password)
    {
        $url = 'https://notify.eskiz.uz/api/auth/login';

        $postData = [
            'email' => $login,
            'password' => $password
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $json = json_decode($response, true);
            return $json['data']['token'] ?? false;
        }

        return false;
    }

    $nik='4546';



    $login='muzaffar0511@gmail.com';
    $password='p8W0N2y8mOf16FNTGlOKeDk6mKKvVLR0UeAhnmoN';

    if (\request()->has('lcup') && \request()->input('lcup')==1) {
        $login='abduvohid.mirzamahmudov@gmail.com';
        $password='OyJKbddpUCTkwEA7l4TpMCQFdYZIVRyHDkb7yWpD';
    }

    $token=getToken($login,$password);
    info($token);
    if($token ){
        try {


            $client = new Client();
            $headers = [
                "Authorization" => "Bearer " . $token];
            $options = [
                'multipart' => [

                    [
                        'name' => 'mobile_phone',
                        'contents' => "998993271423"
                    ],
                    [
                        'name' => 'message',
                        'contents' => "Kodni hech kimga bermang! TM school ilovasiga kirish uchun tasdiqlash kodi: 0000 "
                    ],
                    [
                        'name' => 'from',
                        'contents' => $nik
                    ],

                ]];

            $request = new Request('POST', 'notify.eskiz.uz/api/message/sms/send', $headers);

            $res = $client->send($request, $options);




        }
        catch (\Exception $exception)
        {

        } catch (GuzzleException $e) {
        }

    }
});

Route::redirect('/', '/user');

Route::post('/login', [UserLoginController::class, 'login'])
    ->middleware('throttle:8,1')
    ->name('login.submit');

Route::post('/register', [UserLoginController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('register.submit');

Route::post('/forgot', [UserLoginController::class, 'forgotSubmit'])
    ->middleware('throttle:3,5')
    ->name('forgot.submit');

Route::post('/resend-verification-code', [UserLoginController::class, 'resendVerificationCode'])
    ->middleware('throttle:2,5')
    ->name('resend.verification.code');

Route::post('/reset-password', [UserLoginController::class,'resetPassword'])
    ->middleware('throttle:3,5')
    ->name('password.reset.submit');

Route::post('/confirm_check', [UserLoginController::class, 'confirmCheck'])
    ->middleware('throttle:10,1')
    ->name('confirm.check');

Route::post('/logout', [UserLoginController::class, 'logout'])
    ->name('logout');

Route::get('/confirm', [UserLoginController::class,'confirm'])->name('confirm');
Route::get('/reset-password', [UserLoginController::class,'showResetForm'])->name('password.reset.form');
Route::get('/forgot', [UserLoginController::class, 'showForgotForm'])->name('forgot.form');
Route::get('/register/{id?}', [UserLoginController::class, 'showRegisterForm'])->name('register');
Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/telegram', [LoginBotController::class, 'redirectToBot'])->name('telegram.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
Route::get('/auth/telegram/callback', [LoginBotController::class, 'callback'])->name('telegram.callback');

Route::prefix('admin')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');
});

Route::prefix('swagger')->middleware('swagger.guest')->group(function () {
    Route::get('/login', [SwaggerLoginController::class, 'showLoginForm'])->name('swagger.login');
    Route::post('/login', [SwaggerLoginController::class, 'login'])->name('swagger.login.submit');
});

Route::post('/swagger/logout', [SwaggerLoginController::class, 'logout'])
    ->middleware('swagger.docs')
    ->name('swagger.logout');





Route::group(['prefix' => 'user', 'namespace' => 'User', 'middleware' => ['redirect.if_admin_role','sessionCheck','auth']], function () {

    Route::get('/', [UserController::class,'index'])->name('user.dashboard');
    Route::get('/favorite', [UserController::class,'favorite'])->name('user.favorite');
    Route::get('/lessons', [UserController::class,'lessons'])->name('user.lessons');
    Route::get('/lesson-detail/{id}', [UserController::class,'lessonsDetail'])->name('user.lessons.detail');
    Route::get('/lesson/{id}/{video_id?}', [UserController::class,'lesson'])->name('user.lesson');
    Route::get('/practices', [TestController::class,'practices'])->name('user.practices');
    Route::get('/practice-parts/{type?}', [TestController::class,'practiceTests'])->name('user.practice-parts');
    Route::get('/practice-tests/{type?}', [TestController::class,'practiceTests'])->name('user.practice-tests');
    Route::get('/next-exercise/{type?}/{id}', [TestController::class,'nextExercise'])->name('user.next-exercise');
    Route::get('/practice-test/{id}/{type}/{difficulty}/{time_limit}/{part_category}/{title?}/{status?}', [TestController::class,'practiceTest'])->name('user.practice-test');
    Route::get('/mock-practice/{id}', [TestController::class,'practiceMock'])->name('user.mock-practice');
    Route::get('/market/{type?}', [MarketController::class,'index'])->name('user.market')->withoutMiddleware('premiumExpired');
    Route::get('/components', [UserController::class,'components'])->name('components');
    Route::get('/profile', [UserController::class,'profile'])->name('user.profile');
    Route::get('/contact', [UserController::class,'contact'])->name('user.contact');


    Route::group(['prefix' => 'results','middleware' => ['auth']], function () {
        Route::get('/', [ResultController::class,'index'])->name('user.results');
        Route::get('/show/{id?}/{type}', [ResultController::class,'resultsShow'])->name('user.results.show');
        Route::get('/full/show/{id}/{type?}', [ResultController::class,'fullResultsShow'])->name('user.full.results.show');
        Route::get('/download/{id}/{type}', [ResultController::class, 'DownloadResultPdf'])->name('user.results.DownloadResultPdf');
        Route::get('/reading/show/{id}',  [ResultController::class, 'ReadingResultDetail'])->name('user.reading.result.detail');
        Route::get('/listening/show/{id}',  [ResultController::class, 'ListeningResultDetail'])->name('user.listening.result.detail');

    });
    Route::group(['prefix' => 'exam'], function () {
        Route::get('/reading/{id}', [TestController::class,'reading'])->name('user.reading.start');
        Route::get('/listening/{id}', [TestController::class,'listening'])->name('user.listening.start');
        Route::get('/writing/{id}', [TestController::class,'writing'])->name('user.writing.start');
        Route::get('/speaking/{id}', [TestController::class,'speaking'])->name('user.speaking.start');

        Route::get('/listening/full/{id}', [TestController::class,'listening'])->name('user.full.listening.start');
        Route::get('/reading/full/{id}', [TestController::class,'reading'])->name('user.full.reading.start');
        Route::get('/writing/full/{id}', [TestController::class,'writing'])->name('user.full.writing.start');
        Route::get('/speaking/full/{id}', [TestController::class,'speaking'])->name('user.full.speaking.start');

        Route::post('/reading/store/{id}',  [TestController::class, 'storeReading'])->name('user.reading.store');
        Route::post('/listening/store/{id}',  [TestController::class, 'storeListening'])->name('user.listening.store');
        Route::post('/writing/store/{id}',  [TestController::class, 'storeWriting'])->name('user.writing.store');
        Route::post('/speaking/store/{id}',  [TestController::class, 'storeSpeaking'])->name('user.speaking.store');
        Route::get('/full/reset/{id}',  [TestController::class, 'fullReset'])->name('user.full.reset');


    });
});



Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth_admin']], function () {
    Route::get('/', 'HomeController@index')->name('dashboard');


    Route::get('uploads/',[\App\Http\Controllers\Admin\ExcerciseController::class,'index'])->name('uploads.index');

    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');
    Route::get('/user/login/{id}}', [UserController::class, 'AdminRedirectLogin'])->name('redirect.login');

    Route::get('extra_admins',[\App\Http\Controllers\Admin\UsersController::class,'extra_admin_index'])->name('extra_admins.index');
    Route::get('extra_admins/create',[\App\Http\Controllers\Admin\UsersController::class,'extra_admin_create'])->name('extra_admins.create');
    Route::post('extra_admins/store',[\App\Http\Controllers\Admin\UsersController::class,'extra_admin_store'])->name('extra_admins.store');
    Route::get('extra_admins/edit{id}',[\App\Http\Controllers\Admin\UsersController::class,'extra_admin_edit'])->name('extra_admins.edit');
    Route::post('extra_admins/update{id}',[\App\Http\Controllers\Admin\UsersController::class,'extra_admin_update'])->name('extra_admins.update');

    Route::get('workers',[\App\Http\Controllers\Admin\UsersController::class,'workers_index'])->name('workers.index');
    Route::get('workers/create',[\App\Http\Controllers\Admin\UsersController::class,'workers_create'])->name('workers.create');
    Route::post('workers/store',[\App\Http\Controllers\Admin\UsersController::class,'workers_store'])->name('workers.store');
    Route::get('workers/edit{id}',[\App\Http\Controllers\Admin\UsersController::class,'workers_edit'])->name('workers.edit');
    Route::post('workers/update{id}',[\App\Http\Controllers\Admin\UsersController::class,'workers_update'])->name('workers.update');

    Route::get('referrals',[\App\Http\Controllers\Admin\UsersController::class,'referrals_index'])->name('referrals.index');
    Route::get('pay/referrals/{id?}',[\App\Http\Controllers\Admin\UsersController::class,'pay_referral'])->name('pay.referral.index');
    // Region
    Route::delete('regions/destroy', 'RegionController@massDestroy')->name('regions.massDestroy');
    Route::resource('regions', 'RegionController');

    // City
    Route::delete('cities/destroy', 'CityController@massDestroy')->name('cities.massDestroy');
    Route::resource('cities', 'CityController');

    //information
    Route::get('information',[\App\Http\Controllers\InformationController::class,'index'])->name('information.index');
    Route::get('information/create',[\App\Http\Controllers\InformationController::class,'create'])->name('information.create');
    Route::post('information/store',[\App\Http\Controllers\InformationController::class,'store'])->name('information.store');
    Route::get('information/edit{id}',[\App\Http\Controllers\InformationController::class,'edit'])->name('information.edit');
    Route::put('information/update/{id}',[\App\Http\Controllers\InformationController::class,'update'])->name('information.update');
    Route::delete('information/destroy/{id}', [\App\Http\Controllers\InformationController::class,'destroy'])->name('information.destroy');

    //prepare Text
    Route::get('prepareText',[\App\Http\Controllers\PrepareTextController::class,'index'])->name('prepareText.index');
    Route::get('prepareText/edit{id}',[\App\Http\Controllers\PrepareTextController::class,'edit'])->name('prepareText.edit');
    Route::put('prepareText/update/{id}',[\App\Http\Controllers\PrepareTextController::class,'update'])->name('prepareText.update');

    //writing prompts
    Route::get('writingPrompt',[\App\Http\Controllers\WritingPromptController::class,'index'])->name('writingPrompt.index');
    Route::get('writingPrompt/edit/{id}',[\App\Http\Controllers\WritingPromptController::class,'edit'])->name('writingPrompt.edit');
    Route::put('writingPrompt/update/{id}',[\App\Http\Controllers\WritingPromptController::class,'update'])->name('writingPrompt.update');
    Route::get('speakingPrompt/edit/{id}',[\App\Http\Controllers\WritingPromptController::class,'speaking_edit'])->name('speakingPrompt.edit');
    Route::put('speakingPrompt/update/{id}',[\App\Http\Controllers\WritingPromptController::class,'speaking_update'])->name('speakingPrompt.update');

    Route::get('globalSettings',[\App\Http\Controllers\GlobalSettingController::class,'index'])->name('globalSettings.index');
    Route::post('globalSettings/login-bot',[\App\Http\Controllers\GlobalSettingController::class,'login_bot_update'])->name('globalSettings.loginBot.update');
    Route::get('gptSettings/edit',[\App\Http\Controllers\GlobalSettingController::class,'gpt_edit'])->name('gptSettings.edit');
    Route::put('gptSettings/update',[\App\Http\Controllers\GlobalSettingController::class,'gpt_update'])->name('gptSettings.update');
    Route::get('financeSettings/edit',[\App\Http\Controllers\GlobalSettingController::class,'finance_edit'])->name('financeSettings.edit');
    Route::put('financeSettings/update',[\App\Http\Controllers\GlobalSettingController::class,'finance_update'])->name('financeSettings.update');

    Route::get('extraSettings/edit',[\App\Http\Controllers\GlobalSettingController::class,'extra_edit'])->name('extraSettings.edit');
    Route::put('extraSettings/update',[\App\Http\Controllers\GlobalSettingController::class,'extra_update'])->name('extraSettings.update');

    Route::view('reports/daily', 'admin.reports.daily')->name('reports.daily');
    Route::view('reports/monthly', 'admin.reports.monthly')->name('reports.monthly');
    Route::view('reports/overview', 'admin.reports.overview')->name('reports.overview');

    // comments
    Route::get('comments',[\App\Http\Controllers\Admin\CommentController::class,'index'])->name('comments.index');

    // Step Pages
    Route::resource('stepPages', StepPageController::class);


    // News
    Route::delete('newss/destroy', 'NewsController@massDestroy')->name('newss.massDestroy');
    Route::post('newss/media', 'NewsController@storeMedia')->name('newss.storeMedia');
    Route::post('newss/ckmedia', 'NewsController@storeCKEditorImages')->name('newss.storeCKEditorImages');
    Route::resource('newss', 'NewsController');
    Route::get('newsJoin/{id}', [\App\Http\Controllers\Admin\QabulController::class, 'index'])->name('newsJoin');

    // Application
    Route::delete('applications/destroy', 'ApplicationController@massDestroy')->name('applications.massDestroy');
    Route::resource('applications', 'ApplicationController');

    // Shablon
    Route::delete('shablons/destroy', 'ShablonController@massDestroy')->name('shablons.massDestroy');
    Route::resource('shablons', 'ShablonController');

    // Message
    Route::delete('messages/destroy', 'MessageController@massDestroy')->name('messages.massDestroy');
    Route::resource('messages', 'MessageController');

    // Course
    Route::delete('courses/destroy', 'CourseController@massDestroy')->name('courses.massDestroy');
    Route::post('courses/media', 'CourseController@storeMedia')->name('courses.storeMedia');
    Route::post('courses/ckmedia', 'CourseController@storeCKEditorImages')->name('courses.storeCKEditorImages');
    Route::resource('courses', 'CourseController');

    // Saved Lessons
    Route::delete('saved-lessons/destroy', 'SavedLessonsController@massDestroy')->name('saved-lessons.massDestroy');
    Route::resource('saved-lessons', 'SavedLessonsController');

    // Lesson
    Route::delete('lessons/destroy', 'LessonController@massDestroy')->name('lessons.massDestroy');
    Route::post('lessons/media', 'LessonController@storeMedia')->name('lessons.storeMedia');
    Route::post('lessons/ckmedia', 'LessonController@storeCKEditorImages')->name('lessons.storeCKEditorImages');
    Route::put('admin/lessons/store2', [\App\Http\Controllers\Admin\LessonController::class, 'store2'])->name('lessons.store2');
    Route::get('lessons/index2/{id}', [\App\Http\Controllers\Admin\LessonController::class, 'index2'])->name('lessons.index2');
    Route::get('lessons/create2/{id}', [\App\Http\Controllers\Admin\LessonController::class, 'create2'])->name('lessons.create2');

    Route::resource('lessons', 'LessonController');

    // Video
    Route::delete('videos/destroy', 'VideoController@massDestroy')->name('videos.massDestroy');
    Route::post('videos/media', 'VideoController@storeMedia')->name('videos.storeMedia');
    Route::post('videos/ckmedia', 'VideoController@storeCKEditorImages')->name('videos.storeCKEditorImages');
    Route::get('videos/index2/{id}', [\App\Http\Controllers\Admin\VideoController::class, 'index2'])->name('videos.index2');
    Route::get('videos/create2/{id}', [\App\Http\Controllers\Admin\VideoController::class, 'create2'])->name('videos.create2');
    Route::put('videos/store2', [\App\Http\Controllers\Admin\VideoController::class, 'store2'])->name('videos.store2');
    Route::resource('videos', 'VideoController');

    // Comment
    Route::delete('comments/destroy', 'CommentController@massDestroy')->name('comments.massDestroy');
    Route::resource('comments', 'CommentController');
});

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});

Route::get('/download/api/{id}/{type}/{token}', [ResultController::class, 'ApiDownloadResultPdf'])->name('api.user.results.DownloadResultPdf');
Route::get('/audio/stream/{filename}', [AudioController::class, 'speakingStream'])->name('admin.speaking.audio.stream');
Route::get('/audio/result/stream/{filename}', [AudioController::class, 'speakingResultStream'])->name('admin.speaking.result.audio.stream');

Route::get('/test/speaking/{id}',function ($id){

    $speakingStoreService= new SpeakingStoreService();
    $speakingResult=\App\Models\SpeakingResult::find($id);
    $speakingExercise=\App\Models\SpeakingExercise::find($speakingResult->speaking_exercise_id);
    $user=auth()->user();

    $speakingStoreService->dispatchToJob($speakingResult,$speakingExercise,$user->id);
    dd($speakingResult,$speakingExercise);

});


Route::post('/botwebhook/confirm', [\App\Http\Controllers\LoginBotController::class, 'handle'])->name('botwebhook.confirm');
