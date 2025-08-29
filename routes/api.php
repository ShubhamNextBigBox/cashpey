<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\FineractApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



// Fineract API's calling routes

// client routes
Route::prefix('cashpey')->group(function () {
    Route::get('create-client', [FineractApiController::class, 'createClient']);
    Route::get('delete-client', [FineractApiController::class, 'deleteClient']);
});


Route::prefix('cashpey')->group(function () {
    Route::post('create-loan', [FineractApiController::class, 'createLoan']);
    Route::get('create-sanction-repayment-schedule', [FineractApiController::class, 'createSanctionRepaymentSchedule']);
    Route::post('approve-loan', [FineractApiController::class, 'approveLoan']);
    Route::post('disburse-loan', [FineractApiController::class, 'disburseLoan']);
    Route::post('loan-details', [FineractApiController::class, 'loanDetails']);
});



  Route::post('cashpey/get-form-data', [ApiController::class, 'getFormData']);
  Route::post('cashpey/update-stage', [ApiController::class, 'updateStage']);
  Route::get('cashpey/get-states', [ApiController::class, 'getStates']);
  Route::post('cashpey/get-cities', [ApiController::class, 'getCities']);
  Route::post('cashpey/send-mobile-otp', [ApiController::class, 'sendMobileOTP']);
  Route::post('cashpey/resend-mobile-otp', [ApiController::class, 'resendMobileOTP']);
  Route::post('cashpey/pancard-verification', [ApiController::class, 'pancardVerification']);
  Route::post('cashpey/send-aadhar-otp', [ApiController::class, 'sendAadhaarOtp']);
  Route::post('cashpey/verify-aadhar-otp', [ApiController::class, 'verifyAadhaarOtp']);
 
// Route::get('cashpey-authenticate', [FineractApiController::class, 'authenticate']);





// Route::get('cashpey-client-activate', [FineractApiController::class, 'activateClient']);








// Route::get('check-reloan-customer', [ApiController::class, 'checkReloanCustomer']);
// Route::get('get-reloan-rmid', [ApiController::class, 'getRmID']);
// Route::get('check-customer-exist', [ApiController::class, 'checkCustomerExist']);
  Route::get('assign-rm-cm-fetch', [ApiController::class, 'assignRMandCMFetch']);
