<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\LinksController;
use App\Http\Controllers\Admin\AwardsController;
use App\Http\Controllers\Admin\EventsController;
use App\Http\Controllers\Admin\OffertsController;
use App\Http\Controllers\Admin\CampusesController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AwardManagementController;
use App\Http\Controllers\Admin\EventManagementController;
use App\Http\Controllers\Admin\OffertManagementController;

Route::prefix('auth')->group(function () {
    // Route::post('upload-users', [AuthController::class, 'uploadUsers']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signup']);
    Route::get('signup/activate/{token}', [AuthController::class, 'signupActivate']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::post('upgrade', [AuthController::class, 'upgrade']);
        Route::post('update-profile/{user}', [AuthController::class, 'updateProfile']);
    });
});

Route::middleware('api')->group(function () {
    Route::prefix('password')->group(function () {
        Route::post('create', [PasswordResetController::class, 'create']);
        Route::get('find/{token}', [PasswordResetController::class, 'find']);
        Route::post('reset', [PasswordResetController::class, 'reset']);
    });
});

Route::middleware(['auth:api'])->group(function () {
    Route::prefix('user-management')->name('user-manage.')->group(function () {
        Route::post('change-status', [UserManagementController::class, 'changeStatus'])->name('change-status');
    });

    Route::prefix('event-management')->name('event-manage.')->group(function () {
        Route::post('join-event/{id}', [EventManagementController::class, 'joinEvent'])->name('join-event');
        Route::post('leave-event/{id}', [EventManagementController::class, 'leaveEvent'])->name('leave-event');
        Route::post('register-checkpoint/{id}', [EventManagementController::class, 'registerCheckpoint'])->name('register-checkpoint');
        Route::post('change-status/{id}', [EventManagementController::class, 'changeStatus'])->name('change-status');
        Route::get('events-list', [EventManagementController::class, 'eventsList'])->name('events-list');
    });

    Route::prefix('award-management')->name('award-manage.')->group(function () {
        Route::post('claim-award', [AwardManagementController::class, 'claimAward'])->name('claim-award');
    });

    Route::prefix('offert-management')->name('offert-manage.')->group(function () {
        Route::post('claim-offert', [OffertManagementController::class, 'claimOffert'])->name('claim-offert');
    });

    // Endpoints for app users
    Route::get('news', [NewsController::class, 'list']);
    Route::get('news/{new}', [NewsController::class, 'details']);
    Route::get('links', [LinksController::class, 'list']);
    Route::get('links/{link}', [LinksController::class, 'details']);
    Route::get('awards', [AwardsController::class, 'list']);
    Route::get('awards/{award}', [AwardsController::class, 'details']);
    Route::get('events', [EventsController::class, 'list']);
    Route::get('events/{event}', [EventsController::class, 'details']);
    Route::get('offerts', [OffertsController::class, 'list']);
    Route::get('offerts/{offert}', [OffertsController::class, 'details']);
    Route::get('campuses', [CampusesController::class, 'list']);
    Route::get('campuses/{campus}', [CampusesController::class, 'details']);
});
