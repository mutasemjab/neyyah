<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\IdentityVerificationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\MatchRequestAdminController;
use App\Http\Controllers\Admin\ConversationAdminController;
use App\Http\Controllers\Admin\InterestController;
use App\Http\Controllers\Admin\ConsultantAdminController;
use App\Http\Controllers\Admin\ConsultationAdminController;
use App\Http\Controllers\Admin\GuidedQuestionController;
use App\Http\Controllers\Admin\MatchmakerAdminController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PrivateRequestAdminController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Permission\Models\Permission;

define('PAGINATION_COUNT', 11);

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {

    Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin'], function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');

        // Admin account
        Route::get('/admin/edit/{id}', [LoginController::class, 'editlogin'])->name('admin.login.edit');
        Route::post('/admin/update/{id}', [LoginController::class, 'updatelogin'])->name('admin.login.update');

        // Roles & Employees
        Route::resource('employee', 'App\Http\Controllers\Admin\EmployeeController', ['as' => 'admin']);
        Route::get('role', 'App\Http\Controllers\Admin\RoleController@index')->name('admin.role.index');
        Route::get('role/create', 'App\Http\Controllers\Admin\RoleController@create')->name('admin.role.create');
        Route::get('role/{id}/edit', 'App\Http\Controllers\Admin\RoleController@edit')->name('admin.role.edit');
        Route::patch('role/{id}', 'App\Http\Controllers\Admin\RoleController@update')->name('admin.role.update');
        Route::post('role', 'App\Http\Controllers\Admin\RoleController@store')->name('admin.role.store');
        Route::delete('admin/role/delete/{id}', 'App\Http\Controllers\Admin\RoleController@delete')->name('admin.role.delete');

        Route::get('/permissions/{guard_name}', function ($guard_name) {
            return response()->json(Permission::where('guard_name', $guard_name)->get());
        });

        // ── User Management ───────────────────────────────────────────────────
        Route::get('users', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::get('users/{id}', [UserManagementController::class, 'show'])->name('admin.users.show');
        Route::patch('users/{id}/status', [UserManagementController::class, 'updateStatus'])->name('admin.users.update-status');

        // ── Identity Verifications ────────────────────────────────────────────
        Route::get('verifications', [IdentityVerificationController::class, 'index'])->name('admin.verifications.index');
        Route::get('verifications/{id}', [IdentityVerificationController::class, 'show'])->name('admin.verifications.show');
        Route::get('verifications/{id}/document/{type}', [IdentityVerificationController::class, 'document'])->name('admin.verifications.document');
        Route::post('verifications/{id}/approve', [IdentityVerificationController::class, 'approve'])->name('admin.verifications.approve');
        Route::post('verifications/{id}/reject', [IdentityVerificationController::class, 'reject'])->name('admin.verifications.reject');

        // ── Reports ───────────────────────────────────────────────────────────
        Route::get('reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('reports/{id}', [ReportController::class, 'show'])->name('admin.reports.show');
        Route::patch('reports/{id}', [ReportController::class, 'update'])->name('admin.reports.update');

        // ── Match Requests ────────────────────────────────────────────────────
        Route::get('match-requests', [MatchRequestAdminController::class, 'index'])->name('admin.match-requests.index');
        Route::get('match-requests/{id}', [MatchRequestAdminController::class, 'show'])->name('admin.match-requests.show');

        // ── Conversations ─────────────────────────────────────────────────────
        Route::get('conversations', [ConversationAdminController::class, 'index'])->name('admin.conversations.index');
        Route::get('conversations/{id}', [ConversationAdminController::class, 'show'])->name('admin.conversations.show');

        // ── Interests ─────────────────────────────────────────────────────────
        Route::get('interests', [InterestController::class, 'index'])->name('admin.interests.index');
        Route::delete('interests/{label}', [InterestController::class, 'destroy'])->name('admin.interests.destroy')->where('label', '.+');

        // ── Matchmakers ───────────────────────────────────────────────────────
        Route::get('matchmakers', [MatchmakerAdminController::class, 'index'])->name('admin.matchmakers.index');
        Route::get('matchmakers/{id}', [MatchmakerAdminController::class, 'show'])->name('admin.matchmakers.show');
        Route::post('matchmakers/{id}/approve', [MatchmakerAdminController::class, 'approve'])->name('admin.matchmakers.approve');
        Route::post('matchmakers/{id}/reject', [MatchmakerAdminController::class, 'reject'])->name('admin.matchmakers.reject');
        Route::post('matchmakers/{id}/toggle', [MatchmakerAdminController::class, 'toggleActive'])->name('admin.matchmakers.toggle');

        // ── Consultants ───────────────────────────────────────────────────────
        Route::get('consultants', [ConsultantAdminController::class, 'index'])->name('admin.consultants.index');
        Route::get('consultants/{id}', [ConsultantAdminController::class, 'show'])->name('admin.consultants.show');
        Route::post('consultants/{id}/approve', [ConsultantAdminController::class, 'approve'])->name('admin.consultants.approve');
        Route::post('consultants/{id}/reject', [ConsultantAdminController::class, 'reject'])->name('admin.consultants.reject');
        Route::post('consultants/{id}/toggle', [ConsultantAdminController::class, 'toggleActive'])->name('admin.consultants.toggle');

        // ── Private Match Requests ────────────────────────────────────────────
        Route::get('private-requests', [PrivateRequestAdminController::class, 'index'])->name('admin.private-requests.index');
        Route::get('private-requests/{id}', [PrivateRequestAdminController::class, 'show'])->name('admin.private-requests.show');

        // ── Consultation Sessions ─────────────────────────────────────────────
        Route::get('consultations', [ConsultationAdminController::class, 'index'])->name('admin.consultations.index');
        Route::get('consultations/reviews', [ConsultationAdminController::class, 'reviews'])->name('admin.consultations.reviews');
        Route::get('consultations/{id}', [ConsultationAdminController::class, 'show'])->name('admin.consultations.show');

        // ── Notifications ─────────────────────────────────────────────────────
        Route::get('notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::post('notifications', [NotificationController::class, 'store'])->name('admin.notifications.store');
        Route::get('notifications/search-users', [NotificationController::class, 'searchUsers'])->name('admin.notifications.search-users');

        // ── Guided Questions ──────────────────────────────────────────────────
        Route::get('guided-questions', [GuidedQuestionController::class, 'index'])->name('admin.guided-questions.index');
        Route::get('guided-questions/create', [GuidedQuestionController::class, 'create'])->name('admin.guided-questions.create');
        Route::post('guided-questions', [GuidedQuestionController::class, 'store'])->name('admin.guided-questions.store');
        Route::get('guided-questions/{id}/edit', [GuidedQuestionController::class, 'edit'])->name('admin.guided-questions.edit');
        Route::patch('guided-questions/{id}', [GuidedQuestionController::class, 'update'])->name('admin.guided-questions.update');
        Route::delete('guided-questions/{id}', [GuidedQuestionController::class, 'destroy'])->name('admin.guided-questions.destroy');

    });
});


Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'guest:admin'], function () {
    Route::get('login', [LoginController::class, 'show_login_view'])->name('admin.showlogin');
    Route::post('login', [LoginController::class, 'login'])->name('admin.login');
});
