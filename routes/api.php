<?php
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return 'Welcome PCRM API';
});
// Route::get('session', [AuthController::class, 'generateSessionToken'])->name('generateSessionToken');
Route::get('createIdcard/{id}', [AuthController::class, 'createIdcard'])->name('createIdcard');



Route::get('essentials', [AuthController::class, 'getEssentials'])->name('getEssentials');
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
    Route::prefix('student')->controller(AuthController::class)->group(function () {
        Route::post('/', 'viewStudent')->name('view');
        Route::post('/academic', 'addAcademicDetails')->name('addAcademicDetails');
        Route::post('/personal', 'addPersonalDetails')->name('addPersonalDetails');
        Route::post('/professional', 'addProfessionalDetails')->name('addProfessionalDetails');
        Route::post('/demographic', 'addDemographicDetails')->name('addDemographicDetails');
        Route::post('/documents', 'addDocuments')->name('addDocuments');
        Route::get('/getme', 'getMe')->name('getMe');

    });

});






