<?php

use App\Http\Controllers\Api\Admin\BlogController;
use App\Http\Controllers\Api\GoogleCalendar\GcEventController;
use App\Http\Controllers\Api\GoogleCalendar\GoogleAccountController;
use App\Http\Controllers\Api\Client\Authentication\AuthenticationController;
use App\Http\Controllers\Api\Coach\Authentication\AuthenticationController as CoachAuth;
use App\Http\Controllers\Api\Coach\Dashboard\ClientController;
use App\Http\Controllers\Api\Coach\Dashboard\CoachController;
use App\Http\Controllers\Api\Coach\Dashboard\PackageController;
use App\Http\Controllers\Api\Coach\Dashboard\ScheduleController;
use App\Http\Controllers\Api\Coach\Dashboard\SessionController;
use App\Http\Controllers\Api\Paypal\PaypalController;
use App\Http\Controllers\Api\Zoom\ZoomController;
use App\Http\Controllers\Api\Common\TimezoneController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AdminPagesController;
use App\Http\Controllers\Api\Client\Account\AccountController as ClientAccountController;
use App\Http\Controllers\Api\Coach\Account\AccountController as CoachAccountController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'v1'], function () {

    Route::group(['prefix' => 'google'], function () {

        Route::get('oauth', [GoogleAccountController::class, 'store']);

        Route::group(['middleware' => 'auth:api'], function () {
            # Managing Google accounts.
            Route::get('/', [GoogleAccountController::class, 'index']);
            Route::delete('{googleAccount}', [GoogleAccountController::class, 'destroy']);
        });
    });

    # Viewing events.
    Route::middleware(['middleware' => 'auth:api'])->get('google_events', [GcEventController::class, 'index']);
});
