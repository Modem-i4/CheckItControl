<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\PromocodeController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

if (session()->has('locale')) {
    App::setLocale('uk');
}

Route::get('/', function () {return view('common.sitemap');});
Route::get('/admin', [DashboardController::class, 'index']);
Route::get('/quizzes', [QuizController::class, 'index']);
Route::get('/lessons', [LessonController::class, 'index']);
Route::get('/lessons/{id}', [LessonController::class, 'single']);
Route::get('/quiz/{id}', [QuizController::class, 'quizIndex']);

Route::get('set_locale/{locale}', [LocalizationController::class, 'index']);

Route::group(['middleware' => 'role:premium'], function () {
    Route::get('/class', [LessonController::class, 'index']);
    Route::get('/school', [SchoolController::class, 'index']);
    Route::get('/groups', [GroupController::class, 'index']);
});

Route::prefix('api')->group(function () {
    Route::post('checkCode', [PromocodeController::class, 'checkCode']);
    Route::get('quizzes', [QuizController::class, 'all']);
    Route::get('quizzes/{id}', [QuizController::class, 'getList']);
    Route::get('quizzes/quiz/pure/{id}', [QuizController::class, 'getQuizPureJSON']);
    Route::get('quizzes/quiz/formatted/{id}', [QuizController::class, 'getQuizFormattedJSON']);
    Route::get('quizzes/darts/pure/{id}', [QuizController::class, 'getDartsPureJSON']);
    Route::get('quizzes/darts/formatted/{id}', [QuizController::class, 'getDartsFormattedJSON']);
    Route::get('lessons', [LessonController::class, 'all']);
    Route::post('lessons', [LessonController::class, 'add']);
    Route::get('lessons/quizStats/{id}', [LessonController::class, 'getQuizStats']);
    Route::get('lessons/{id}', [LessonController::class, 'get']);
    Route::post('lessons/setQuizStats/{id}', [LessonController::class, 'setQuizStats']);
    Route::post('lessons/setDartsStats/{id}', [LessonController::class, 'setDartsStats']);
    Route::post('lessons/setStudentsStats/{id}', [LessonController::class, 'setStudentsStats']);
    Route::get('lessons/testStats/{id}', [LessonController::class, 'testStats']);
    Route::post('lessons/remove', [LessonController::class, 'remove']);
    Route::get('subjects', [SubjectController::class, 'all']);

    Route::get('quiz/{id}', [QuizController::class, 'get']);
    Route::patch('fav/{id}', [FavoriteController::class, 'favorite']);
    Route::post('quiz', [QuizController::class, 'add']);
    Route::put('quiz/{id}', [QuizController::class, 'update']);

    Route::group(['middleware' => 'role:premium'], function () {
        Route::get('schools', [SchoolController::class, 'all']);
        Route::get('schools/{id}', [SchoolController::class, 'get']);
        Route::get('users/school/{school_id}', [UserController::class, 'getBySchool']);
        Route::get('groups/school/{school_id}', [GroupController::class, 'getBySchool']);
        Route::post('users/set-school/{school_id}', [UserController::class, 'setSchool']);

        Route::get('groups', [GroupController::class, 'all']);
        Route::get('groups/list', [GroupController::class, 'list']);
        Route::post('groups', [GroupController::class, 'add']);
        Route::post('schools', [SchoolController::class, 'add']);
        Route::patch('groups/{id}', [GroupController::class, 'setStudents']);

    });
});
