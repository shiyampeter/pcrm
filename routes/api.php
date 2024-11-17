<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\masters\StatusController;
use App\Http\Controllers\Api\masters\SubWorkCategoryController;
use App\Http\Controllers\Api\masters\WorkCategoryController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return 'Welcome PCRM API';
});
// Route::get('session', [AuthController::class, 'generateSessionToken'])->name('generateSessionToken');



Route::get('essentials', [Controller::class, 'essential'])->name('essential');
Route::get('student', [AuthController::class, 'getStudentDetails'])->name('getStudentDetails');
Route::post('student', [AuthController::class, 'createStudent'])->name('createStudent');
Route::post('studentData', [AuthController::class, 'uploadStudentData'])->name('uploadStudentData');
Route::post('emailverify', [AuthController::class, 'emailverfiy'])->name('emailverfiy');
Route::post('otpregister', [AuthController::class, 'sendOtpForRegister'])->name('sendOtpForRegister');
Route::post('otplogin', [AuthController::class, 'sendOtpForLogin'])->name('sendOtpForLogin');
Route::post('otpverify', [AuthController::class, 'otpverify'])->name('otpverify');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'studentlogout'])->name('studentlogout');


Route::group([
    'middleware' => 'auth:api',
], function () {
    Route::get('workCategory', [WorkCategoryController::class, 'listWorkCategory'])->name('listWorkCategory');
    Route::get('workCategory/{id}', [WorkCategoryController::class, 'viewWorkCategory'])->name('viewWorkCategory');
    Route::post('workCategory', [WorkCategoryController::class, 'createWorkCategory'])->name('createWorkCategory');
    Route::put('workCategory/{id}', [WorkCategoryController::class, 'updateWorkCategory'])->name('updateWorkCategory');
    Route::delete('workCategory/{id}', [WorkCategoryController::class, 'deleteWorkCategory'])->name('deleteWorkCategory');

    Route::get('subWorkCategory', [SubWorkCategoryController::class, 'listSubWorkCategory'])->name('listSubWorkCategory');
    Route::get('subWorkCategory/{id}', [SubWorkCategoryController::class, 'viewSubWorkCategory'])->name('viewSubWorkCategory');
    Route::post('subWorkCategory', [SubWorkCategoryController::class, 'createSubWorkCategory'])->name('createSubWorkCategory');
    Route::put('subWorkCategory/{id}', [SubWorkCategoryController::class, 'updateSubWorkCategory'])->name('updateSubWorkCategory');
    Route::delete('subWorkCategory/{id}', [SubWorkCategoryController::class, 'deleteSubWorkCategory'])->name('deleteSubWorkCategory');

    Route::get('subworkCategoryStatus', [StatusController::class, 'listSubWorkCategoryStatus'])->name('listSubWorkCategoryStatus');
    Route::get('subworkCategoryStatus/{id}', [StatusController::class, 'viewSubWorkCategoryStatus'])->name('viewSubWorkCategoryStatus');
    Route::post('subworkCategoryStatus', [StatusController::class, 'createSubWorkCategoryStatus'])->name('createSubWorkCategoryStatus');
    Route::put('subworkCategoryStatus/{id}', [StatusController::class, 'updateSubWorkCategoryStatus'])->name('updateSubWorkCategoryStatus');
    Route::delete('subworkCategoryStatus/{id}', [StatusController::class, 'deleteSubWorkCategoryStatus'])->name('deleteSubWorkCategoryStatus');
});






