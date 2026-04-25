<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HistoryController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Email verification
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerifyEmailController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
        ->middleware('throttle:6,1')->name('verification.send');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Events
    Route::resource('events', EventController::class);

    // Participants
    Route::post('/events/{event}/register', [ParticipantController::class, 'store'])->name('participants.store');
    Route::get('/my-participants', [ParticipantController::class, 'myParticipants'])->name('participants.my');

    // Admin/Committee routes
    Route::middleware('role:admin,committee,head_csdl,head_baak,head_finance,head_gsd,head_sis,head_learning,acoo')->group(function () {
        Route::get('/participants', [ParticipantController::class, 'index'])->name('participants.index');
        Route::patch('/participants/{participant}/status', [ParticipantController::class, 'updateStatus'])->name('participants.updateStatus');

        // Attendance management
        Route::get('/events/{event}/attendance/generate', [AttendanceController::class, 'generate'])->name('attendance.generate');
        Route::post('/events/{event}/attendance/generate-qr', [AttendanceController::class, 'generateQR'])->name('attendance.generateQR');
        Route::get('/events/{event}/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');

        // Reports
        Route::get('/events/{event}/reports/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/events/{event}/reports', [ReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
        Route::get('/reports/{report}/pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
        Route::get('/reports/{report}/csv', [ReportController::class, 'exportCsv'])->name('reports.exportCsv');

        // Certificates management
        Route::get('/events/{event}/certificates', [CertificateController::class, 'manage'])->name('certificates.manage');
        Route::get('/events/{event}/certificates/design', [CertificateController::class, 'design'])->name('certificates.design');
        Route::post('/events/{event}/certificates/design', [CertificateController::class, 'saveDesign'])->name('certificates.saveDesign');
        Route::post('/events/{event}/certificates/activate', [CertificateController::class, 'activate'])->name('certificates.activate');
    });

    // Attendance check-in (for students)
    Route::get('/attendance/scan', [AttendanceController::class, 'showScanner'])->name('attendance.scan');
    Route::get('/attendance/checkin', [AttendanceController::class, 'checkinForm'])->name('attendance.checkin.form');
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');

    // Certificates (student download)
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::patch('/users/{user}/toggle-status', [\App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggleStatus');
        
        Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{log}', [\App\Http\Controllers\AuditLogController::class, 'show'])->name('audit-logs.show');
    });

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');

    // Lecturer Signature
    Route::get('/profile/signature', [DashboardController::class, 'signatureForm'])->name('profile.signature');
    Route::post('/profile/signature', [DashboardController::class, 'saveSignature'])->name('profile.signature.save');
});
