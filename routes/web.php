<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\HemisAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageBuilderController;
use App\Http\Controllers\FallbackController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Include Face Attendance API routes
require __DIR__.'/api_face_attendance.php';

// Include LMS Test routes
require __DIR__.'/test_lms.php';

// Include HEMIS & Academic routes
require __DIR__.'/hemis_academic.php';

// Include HR routes
require __DIR__.'/hr.php';

// Include Teacher routes
require __DIR__.'/teacher.php';

// Include Dean routes
require __DIR__.'/dean.php';

// Error logging routes
Route::post('/api/log-frontend-error', [App\Http\Controllers\ErrorLogController::class, 'logFrontendError'])->name('api.log-frontend-error');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/error-logs', [App\Http\Controllers\ErrorLogController::class, 'viewLogs'])->name('admin.error-logs');
    Route::post('/admin/error-logs/clear', [App\Http\Controllers\ErrorLogController::class, 'clearLogs'])->name('admin.error-logs.clear');
});

// Database fallback and status routes
Route::get('/database/status', [FallbackController::class, 'checkStatus'])->name('database.status');
Route::get('/fallback', [FallbackController::class, 'index'])->name('fallback.index');

// Face Recognition Test Route
Route::get('/face-test', function() {
    return view('face-test');
})->name('face.test');

// Attendance Monitoring Route
Route::get('/attendance/monitoring', function() {
    return view('attendance.monitoring');
})->name('attendance.monitoring');

// Face Attendance Web Routes
Route::middleware(['auth'])->group(function () {
    // Student Face Attendance
    Route::get('/attendance/face-recognition', [App\Http\Controllers\FaceAttendanceController::class, 'showFaceRecognition'])->name('attendance.face-recognition');
    Route::get('/api/students/enrolled', [App\Http\Controllers\FaceAttendanceController::class, 'getEnrolledStudents'])->name('api.students.enrolled');
    Route::post('/attendance/register-face', [App\Http\Controllers\FaceAttendanceController::class, 'registerFace'])->name('attendance.register-face');
    Route::post('/attendance/check-in', [App\Http\Controllers\FaceAttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [App\Http\Controllers\FaceAttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::get('/attendance/history', [App\Http\Controllers\FaceAttendanceController::class, 'history'])->name('attendance.history');

    // Staff/Employee Face Attendance
    Route::get('/attendance/staff', [App\Http\Controllers\FaceAttendanceController::class, 'showStaffAttendance'])->name('attendance.staff');

    Route::get('/attendance/reports', function() {
        // Attendance reports page (to be implemented)
        return view('attendance.reports');
    })->name('attendance.reports');

    Route::get('/attendance/settings', function() {
        // Attendance settings page (to be implemented)
        return view('attendance.settings');
    })->name('attendance.settings');
});

// Cache tozalash — faqat autentifikatsiyadan o'tgan SuperAdmin/Admin uchun
Route::get('/clear-cache', function() {
    Artisan::call('optimize:clear');
    return 'Kesh tozalandi!';
})->middleware(['auth', 'role:SuperAdmin|Admin']);

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/about', [App\Http\Controllers\HomeController::class, 'about'])->name('about');
Route::get('/programs', [App\Http\Controllers\HomeController::class, 'programs'])->name('programs');
Route::get('/teachers', [App\Http\Controllers\HomeController::class, 'teachers'])->name('teachers');
Route::get('/statistika', [App\Http\Controllers\HomeController::class, 'statistics'])->name('statistics');
Route::get('/statistics', [App\Http\Controllers\HomeController::class, 'statistics']); // English alias
Route::get('/blog', [App\Http\Controllers\HomeController::class, 'blog'])->name('blog');
Route::get('/faq', [App\Http\Controllers\HomeController::class, 'faq'])->name('faq');
Route::get('/lang/{lang}', [App\Http\Controllers\HomeController::class, 'setLanguage'])->name('lang.switch');

// Public pages
Route::get('/fakultetlar', [App\Http\Controllers\PublicPagesController::class, 'faculties'])->name('faculties');
Route::get('/kafedralar', [App\Http\Controllers\PublicPagesController::class, 'departments'])->name('departments');
Route::get('/yangiliklar', [App\Http\Controllers\PublicPagesController::class, 'news'])->name('news');
Route::get('/yangiliklar/{id}', [App\Http\Controllers\PublicPagesController::class, 'newsShow'])->name('news.show');
Route::get('/aloqa', [App\Http\Controllers\PublicPagesController::class, 'contact'])->name('contact');
Route::post('/aloqa', [App\Http\Controllers\PublicPagesController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/davlat-ramzlari', [App\Http\Controllers\StateSymbolsController::class, 'index'])->name('state-symbols');

// Search routes
Route::get('/qidiruv', [App\Http\Controllers\SearchController::class, 'index'])->name('search');
Route::get('/api/search/quick', [App\Http\Controllers\SearchController::class, 'quick'])->name('search.quick');

// Support Chat routes - moved to api.php

// CMS sahifalar uchun catch-all route eng oxirida

// Frontend pages routes
// Route::view('/about', 'about')->name('frontend.about'); // Commented - using HomeController::about instead
Route::view('/faculties', 'faculties')->name('frontend.faculties');
// Route::view('/library', 'library')->name('frontend.library'); // Moved to PublicLibraryController
Route::view('/news', 'news')->name('frontend.news');
Route::view('/events', 'events')->name('frontend.events');
Route::view('/contacts', 'contacts')->name('frontend.contacts');

// Yangi sahifalar (old routes kept for compatibility)
Route::get('/virtual-tour', [App\Http\Controllers\PublicPagesController::class, 'virtualTour'])->name('virtual-tour');
Route::get('/interactive-map', [App\Http\Controllers\PublicPagesController::class, 'interactiveMap'])->name('interactive-map');
Route::get('/courses', [App\Http\Controllers\PublicPagesController::class, 'courses'])->name('courses');
// Route::get('/programs', [App\Http\Controllers\PublicPagesController::class, 'programs'])->name('programs'); // Commented - using HomeController::programs instead
Route::get('/research', [App\Http\Controllers\PublicPagesController::class, 'research'])->name('research');
Route::get('/videos', [App\Http\Controllers\PublicPagesController::class, 'videos'])->name('videos');
Route::get('/student-life', [App\Http\Controllers\PublicPagesController::class, 'studentLife'])->name('student-life');
Route::get('/sports', [App\Http\Controllers\PublicPagesController::class, 'sports'])->name('sports');
Route::get('/dormitories', [App\Http\Controllers\PublicPagesController::class, 'dormitories'])->name('dormitories');
Route::get('/scholarships', [App\Http\Controllers\PublicPagesController::class, 'scholarships'])->name('scholarships');
Route::get('/international', [App\Http\Controllers\PublicPagesController::class, 'internationalRelations'])->name('international');

// Admission routes (public)
Route::prefix('admission')->name('admission.')->group(function () {
    // Main application form (single-page version)
    Route::get('/apply', function () {
        return view('admission.apply-single');
    })->name('apply');
    Route::post('/apply', [App\Http\Controllers\AdmissionController::class, 'storeSingle'])->name('store');

    Route::get('/success/{applicationNumber}', [App\Http\Controllers\AdmissionController::class, 'success'])->name('success');
    Route::match(['get', 'post'], '/check-status', [App\Http\Controllers\AdmissionController::class, 'checkStatus'])->name('check-status');
    Route::get('/info', [App\Http\Controllers\AdmissionController::class, 'info'])->name('info');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    // SECURITY FIX: Rate limit login attempts to prevent brute force (BUG #29)
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    // SECURITY FIX: Rate limit registrations to prevent spam (BUG #33)
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:5,60');

    // Talabalar uchun soddalashtirilgan ro'yxatga olish
    Route::get('/student/register', [App\Http\Controllers\Auth\StudentRegisterController::class, 'showRegistrationForm'])->name('student.register.form');
    // SECURITY FIX: Rate limit student registrations (BUG #49)
    Route::post('/student/register', [App\Http\Controllers\Auth\StudentRegisterController::class, 'register'])->middleware('throttle:5,60')->name('student.register');

    // Xodimlar uchun ro'yxatga olish (pending approval)
    Route::get('/employee/register', [App\Http\Controllers\Auth\EmployeeRegisterController::class, 'showRegistrationForm'])->name('employee.register.form');
    // SECURITY FIX: Rate limit employee registrations (BUG #44)
    Route::post('/employee/register', [App\Http\Controllers\Auth\EmployeeRegisterController::class, 'register'])->middleware('throttle:3,60');

    // Pending registration success page
    Route::get('/registration/pending', [App\Http\Controllers\Auth\EmployeeRegisterController::class, 'showPendingPage'])->name('registration.pending');

    Route::get('/hemis/login', [HemisAuthController::class, 'redirectToHemis'])->name('hemis.login');
    Route::get('/hemis/callback', [HemisAuthController::class, 'handleCallback'])->name('hemis.callback');

    // OAuth Social Login
    Route::get('/auth/{provider}/redirect', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirect'])
        ->where('provider', 'google|facebook')->name('auth.social.redirect');
    Route::get('/auth/{provider}/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'callback'])
        ->where('provider', 'google|facebook')->name('auth.social.callback');
    Route::match(['get','post'], '/auth/telegram/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'telegramCallback'])
        ->name('auth.telegram.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/otp/verify', [RegisterController::class, 'showOTPForm'])->name('otp.verify');
    Route::post('/otp/verify', [RegisterController::class, 'verifyOTP']);
    // SECURITY FIX: Rate limit OTP resend to prevent SMS bombing (BUG #35)
    Route::post('/otp/resend', [RegisterController::class, 'resendOTP'])->middleware('throttle:3,60')->name('otp.resend');
    
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect(auth()->user()->getDashboardRoute());
    })->middleware(['signed'])->name('verification.verify');
    
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Tasdiqlash havolasi yuborildi!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Chat routes - Real-time Chat with Echo
    Route::get('/chat', function() { return view('chat.realtime'); })->name('chat.index');
    Route::post('/chat/create', [App\Http\Controllers\ChatController::class, 'createConversation'])->name('chat.create');
    Route::get('/chat/{conversationId}/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/{conversationId}/send', [App\Http\Controllers\ChatController::class, 'sendDirectMessage'])->name('chat.send-direct');
    Route::get('/chat/conversations', [App\Http\Controllers\ChatController::class, 'getConversations'])->name('chat.conversations');
    Route::get('/chat/search-users', [App\Http\Controllers\ChatController::class, 'searchUsers'])->name('chat.search-users');

    Route::prefix('admin')->middleware('role:SuperAdmin|Admin|admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

        // CMS Routes
        Route::get('/cms', [App\Http\Controllers\Admin\CmsController::class, 'index'])->name('admin.cms.index');
        Route::get('/cms/{section}', [App\Http\Controllers\Admin\CmsController::class, 'editSection'])->name('admin.cms.edit');
        Route::post('/cms/{section}', [App\Http\Controllers\Admin\CmsController::class, 'updateSection'])->name('admin.cms.update');

        // Menu Management Routes
        Route::resource('menu', App\Http\Controllers\Admin\MenuController::class)->names([
            'index' => 'admin.menu.index',
            'create' => 'admin.menu.create',
            'store' => 'admin.menu.store',
            'show' => 'admin.menu.show',
            'edit' => 'admin.menu.edit',
            'update' => 'admin.menu.update',
            'destroy' => 'admin.menu.destroy',
        ]);
    });

    Route::prefix('teacher')->middleware('role:teacher|Teacher|superadmin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'teacherDashboard'])->name('teacher.dashboard');
        Route::get('/schedule', [App\Http\Controllers\Teacher\ScheduleController::class, 'index'])->name('teacher.schedule');
        Route::get('/schedule/export', [App\Http\Controllers\Teacher\ScheduleController::class, 'exportPdf'])->name('teacher.schedule.export');
        Route::get('/subjects', [App\Http\Controllers\Teacher\SubjectController::class, 'index'])->name('teacher.subjects.index');
        Route::get('/subjects/{id}', [App\Http\Controllers\Teacher\SubjectController::class, 'show'])->name('teacher.subjects.show');

        // Attendance routes
        Route::get('/attendance', [App\Http\Controllers\Teacher\AttendanceController::class, 'index'])->name('teacher.attendance.index');
        Route::get('/attendance/{id}/journal', [App\Http\Controllers\Teacher\AttendanceController::class, 'journal'])->name('teacher.attendance.journal');
        Route::get('/attendance/{id}/create', [App\Http\Controllers\Teacher\AttendanceController::class, 'create'])->name('teacher.attendance.create');
        Route::post('/attendance/{id}/store', [App\Http\Controllers\Teacher\AttendanceController::class, 'store'])->name('teacher.attendance.store');
        Route::get('/attendance/entry/{id}', [App\Http\Controllers\Teacher\AttendanceController::class, 'show'])->name('teacher.attendance.show');

        // Grades routes
        Route::get('/grades', [App\Http\Controllers\Teacher\GradeController::class, 'index'])->name('teacher.grades.index');
        Route::get('/grades/{id}/create', [App\Http\Controllers\Teacher\GradeController::class, 'create'])->name('teacher.grades.create');
        Route::post('/grades/{id}/store', [App\Http\Controllers\Teacher\GradeController::class, 'store'])->name('teacher.grades.store');
        Route::get('/grades/{id}', [App\Http\Controllers\Teacher\GradeController::class, 'show'])->name('teacher.grades.show');
        Route::get('/grades/{groupSubjectId}/student/{studentId}', [App\Http\Controllers\Teacher\GradeController::class, 'studentGrades'])->name('teacher.grades.student');
        Route::put('/grades/{id}/update', [App\Http\Controllers\Teacher\GradeController::class, 'update'])->name('teacher.grades.update');
        Route::delete('/grades/{id}/delete', [App\Http\Controllers\Teacher\GradeController::class, 'destroy'])->name('teacher.grades.destroy');

        // Assignments routes
        Route::get('/assignments', [App\Http\Controllers\Teacher\AssignmentController::class, 'index'])->name('teacher.assignments.index');
        Route::get('/assignments/create', [App\Http\Controllers\Teacher\AssignmentController::class, 'create'])->name('teacher.assignments.create');
        Route::post('/assignments', [App\Http\Controllers\Teacher\AssignmentController::class, 'store'])->name('teacher.assignments.store');
        Route::get('/assignments/pending', [App\Http\Controllers\Teacher\AssignmentController::class, 'pending'])->name('teacher.assignments.pending');
        Route::get('/assignments/{id}', [App\Http\Controllers\Teacher\AssignmentController::class, 'show'])->name('teacher.assignments.show');
        Route::get('/assignments/{id}/edit', [App\Http\Controllers\Teacher\AssignmentController::class, 'edit'])->name('teacher.assignments.edit');
        Route::put('/assignments/{id}', [App\Http\Controllers\Teacher\AssignmentController::class, 'update'])->name('teacher.assignments.update');
        Route::delete('/assignments/{id}', [App\Http\Controllers\Teacher\AssignmentController::class, 'destroy'])->name('teacher.assignments.destroy');
        Route::get('/assignments/submission/{id}/grade', [App\Http\Controllers\Teacher\AssignmentController::class, 'gradeSubmission'])->name('teacher.assignments.grade');
        Route::post('/assignments/submission/{id}/grade', [App\Http\Controllers\Teacher\AssignmentController::class, 'storeGrade'])->name('teacher.assignments.storeGrade');

        // Journal routes
        Route::get('/journal', [App\Http\Controllers\Teacher\JournalController::class, 'index'])->name('teacher.journal.index');
        Route::get('/journal/{id}', [App\Http\Controllers\Teacher\JournalController::class, 'show'])->name('teacher.journal.show');
        Route::get('/journal/{id}/export/{format}', [App\Http\Controllers\Teacher\JournalController::class, 'export'])->name('teacher.journal.export');

        // Materials routes (Online Learning)
        Route::get('/materials', [App\Http\Controllers\Teacher\MaterialController::class, 'index'])->name('teacher.materials.index');
        Route::get('/materials/create', [App\Http\Controllers\Teacher\MaterialController::class, 'create'])->name('teacher.materials.create');
        Route::post('/materials', [App\Http\Controllers\Teacher\MaterialController::class, 'store'])->name('teacher.materials.store');
        Route::get('/materials/{id}', [App\Http\Controllers\Teacher\MaterialController::class, 'show'])->name('teacher.materials.show');
        Route::get('/materials/{id}/edit', [App\Http\Controllers\Teacher\MaterialController::class, 'edit'])->name('teacher.materials.edit');
        Route::put('/materials/{id}', [App\Http\Controllers\Teacher\MaterialController::class, 'update'])->name('teacher.materials.update');
        Route::delete('/materials/{id}', [App\Http\Controllers\Teacher\MaterialController::class, 'destroy'])->name('teacher.materials.destroy');
        Route::get('/materials/{id}/download', [App\Http\Controllers\Teacher\MaterialController::class, 'download'])->name('teacher.materials.download');

        // Topics routes (Course Content)
        Route::get('/topics', [App\Http\Controllers\Teacher\TopicController::class, 'index'])->name('teacher.topics.index');
        Route::get('/topics/subject/{subjectId}', [App\Http\Controllers\Teacher\TopicController::class, 'subjectTopics'])->name('teacher.topics.subject');
        Route::get('/topics/subject/{subjectId}/create', [App\Http\Controllers\Teacher\TopicController::class, 'create'])->name('teacher.topics.create');
        Route::post('/topics/subject/{subjectId}', [App\Http\Controllers\Teacher\TopicController::class, 'store'])->name('teacher.topics.store');
        Route::get('/topics/subject/{subjectId}/{topicId}', [App\Http\Controllers\Teacher\TopicController::class, 'show'])->name('teacher.topics.show');
        Route::get('/topics/subject/{subjectId}/{topicId}/edit', [App\Http\Controllers\Teacher\TopicController::class, 'edit'])->name('teacher.topics.edit');
        Route::put('/topics/subject/{subjectId}/{topicId}', [App\Http\Controllers\Teacher\TopicController::class, 'update'])->name('teacher.topics.update');
        Route::delete('/topics/subject/{subjectId}/{topicId}', [App\Http\Controllers\Teacher\TopicController::class, 'destroy'])->name('teacher.topics.destroy');
        Route::post('/topics/subject/{subjectId}/{topicId}/resource', [App\Http\Controllers\Teacher\TopicController::class, 'addResource'])->name('teacher.topics.resource.add');
        Route::delete('/topics/subject/{subjectId}/{topicId}/resource/{resourceId}', [App\Http\Controllers\Teacher\TopicController::class, 'deleteResource'])->name('teacher.topics.resource.delete');

        // Help and Reports
        Route::get('/help', function () {
            return view('teacher.help');
        })->name('teacher.help');
        Route::get('/reports', [App\Http\Controllers\Teacher\AttendanceController::class, 'report'])->name('teacher.reports');
    });

    Route::prefix('student')->middleware('role:Student|student')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'studentDashboard'])->name('student.dashboard');
        Route::get('/schedule', [App\Http\Controllers\Student\ScheduleController::class, 'index'])->name('student.schedule');
        Route::get('/attendance', [App\Http\Controllers\Student\AttendanceController::class, 'index'])->name('student.attendance.index');
        Route::get('/complete-profile', [App\Http\Controllers\Auth\StudentRegisterController::class, 'showCompleteProfile'])->name('student.complete-profile');
        Route::put('/update-profile', [App\Http\Controllers\Auth\StudentRegisterController::class, 'updateProfile'])->name('student.update-profile');

        // Assignments routes
        Route::get('/assignments', [App\Http\Controllers\AssignmentController::class, 'index'])->name('student.assignments.index');
        Route::get('/assignments/{id}', [App\Http\Controllers\AssignmentController::class, 'show'])->name('student.assignments.show');
        Route::post('/assignments/{id}/submit', [App\Http\Controllers\AssignmentController::class, 'submit'])->name('student.assignments.submit');

        // Profile routes
        Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'index'])->name('student.profile.index');
        Route::put('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('student.profile.update');
        Route::post('/profile/change-password', [App\Http\Controllers\Student\ProfileController::class, 'changePassword'])->name('student.profile.change-password');
        Route::get('/profile/id-card', [App\Http\Controllers\Student\ProfileController::class, 'idCard'])->name('student.profile.id-card');

        // Help route
        Route::get('/help', [App\Http\Controllers\Student\ProfileController::class, 'help'])->name('student.help');

        // Document routes
        Route::get('/documents', [App\Http\Controllers\Student\DocumentController::class, 'index'])->name('student.documents.index');
        Route::get('/documents/reference', [App\Http\Controllers\Student\DocumentController::class, 'generateReference'])->name('student.documents.reference');
        Route::get('/documents/transcript', [App\Http\Controllers\Student\DocumentController::class, 'generateTranscript'])->name('student.documents.transcript');
        Route::get('/documents/certificate', [App\Http\Controllers\Student\DocumentController::class, 'generateCertificate'])->name('student.documents.certificate');
        Route::get('/documents/diploma', [App\Http\Controllers\Student\DocumentController::class, 'generateDiploma'])->name('student.documents.diploma');
        Route::get('/documents/diploma-supplement', [App\Http\Controllers\Student\DocumentController::class, 'generateDiplomaSupplement'])->name('student.documents.diploma-supplement');
    });
    
    Route::prefix('pr')->middleware('role:PR|SuperAdmin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\PR\PRDashboardController::class, 'index'])->name('pr.dashboard');
    });

    Route::prefix('marketing')->middleware('role:Marketing|SuperAdmin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Marketing\MarketingDashboardController::class, 'index'])->name('marketing.dashboard');

        // Admission management for Marketing role
        Route::prefix('admission')->name('admission.')->group(function () {
            Route::get('/applications', [App\Http\Controllers\AdmissionController::class, 'applications'])->name('applications');
            Route::get('/applications/{application}', [App\Http\Controllers\AdmissionController::class, 'viewApplication'])->name('view-application');
            Route::put('/applications/{application}/status', [App\Http\Controllers\AdmissionController::class, 'updateStatus'])->name('update-status');
            Route::delete('/applications/{application}', [App\Http\Controllers\AdmissionController::class, 'deleteApplication'])->name('delete-application');
            Route::post('/applications/{application}/accept-enroll', [App\Http\Controllers\AdmissionController::class, 'acceptAndEnroll'])->name('accept-enroll');
            Route::get('/statistics', [App\Http\Controllers\AdmissionController::class, 'statistics'])->name('statistics');
            Route::get('/settings', [App\Http\Controllers\AdmissionController::class, 'settings'])->name('settings');
            Route::put('/settings', [App\Http\Controllers\AdmissionController::class, 'updateSettings'])->name('settings.update');
            Route::get('/forms', [App\Http\Controllers\AdmissionController::class, 'forms'])->name('forms');
            Route::post('/forms', [App\Http\Controllers\AdmissionController::class, 'updateForms'])->name('forms.update');
            Route::post('/forms/reset', [App\Http\Controllers\AdmissionController::class, 'resetForms'])->name('forms.reset');
            Route::get('/export', [App\Http\Controllers\AdmissionController::class, 'export'])->name('export');
            Route::get('/applications/{application}/pdf', [App\Http\Controllers\AdmissionController::class, 'exportPdf'])->name('export-pdf');
        });
    });
    
    Route::prefix('chat-admin')->middleware('role:ChatAdmin|SuperAdmin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\ChatAdmin\ChatAdminDashboardController::class, 'index'])->name('chat-admin.dashboard');

        // Telegram messages
        Route::get('/telegram', [App\Http\Controllers\ChatAdmin\ChatAdminDashboardController::class, 'telegramMessages'])->name('chat-admin.telegram');
        Route::post('/telegram/{message}/reply', [App\Http\Controllers\ChatAdmin\ChatAdminDashboardController::class, 'replyTelegramMessage'])->name('chat-admin.telegram.reply');
        Route::post('/telegram/{message}/status', [App\Http\Controllers\ChatAdmin\ChatAdminDashboardController::class, 'updateTelegramStatus'])->name('chat-admin.telegram.status');

        // Contact requests
        Route::get('/contact-requests', [App\Http\Controllers\ChatAdmin\ChatAdminDashboardController::class, 'contactRequests'])->name('chat-admin.contact-requests');
        Route::post('/contact-requests/{request}/respond', [App\Http\Controllers\ChatAdmin\ChatAdminDashboardController::class, 'respondContactRequest'])->name('chat-admin.contact-requests.respond');

        // Telegram bot settings
        Route::get('/telegram-settings', [App\Http\Controllers\ChatAdmin\ChatAdminDashboardController::class, 'telegramSettings'])->name('chat-admin.telegram-settings');
        Route::post('/telegram-settings', [App\Http\Controllers\ChatAdmin\ChatAdminDashboardController::class, 'updateTelegramSettings'])->name('chat-admin.telegram-settings.update');

        // User nicknames management
        Route::get('/nicknames', [App\Http\Controllers\ChatAdmin\ChatAdminDashboardController::class, 'userNicknames'])->name('chat-admin.nicknames');
        Route::post('/nicknames/{user}', [App\Http\Controllers\ChatAdmin\ChatAdminDashboardController::class, 'updateUserNickname'])->name('chat-admin.nicknames.update');

        // Support Chat management
        Route::get('/support-chat', [App\Http\Controllers\ChatAdmin\ChatAdminDashboardController::class, 'supportChat'])->name('chat-admin.support-chat');
        Route::get('/support-chat/{sessionId}', [App\Http\Controllers\ChatAdmin\ChatAdminDashboardController::class, 'supportChatSession'])->name('chat-admin.support-chat.session');
        Route::post('/support-chat/{sessionId}/send', [App\Http\Controllers\ChatAdmin\ChatAdminDashboardController::class, 'supportChatSend'])->name('chat-admin.support-chat.send');
    });
    
    Route::post('/hemis/sync', [HemisAuthController::class, 'syncWithHemis'])->name('hemis.sync');

    // Notifications Routes
    Route::prefix('notifications')->group(function () {
        Route::get('/all', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/sender', [App\Http\Controllers\NotificationController::class, 'sender'])->name('notifications.sender');
        Route::post('/send', [App\Http\Controllers\NotificationController::class, 'send'])->name('notifications.send');
        Route::get('/test', function() {
            return view('notifications.test');
        })->name('notifications.test');
        Route::post('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
        Route::post('/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('notifications.get');
    });
    
    // Student Contingent Module Routes
    Route::prefix('students')->group(function () {
        Route::get('/', [App\Http\Controllers\StudentContingent\StudentController::class, 'index'])->name('students.index');
        Route::get('/create', [App\Http\Controllers\StudentContingent\StudentController::class, 'create'])->name('students.create');
        Route::post('/', [App\Http\Controllers\StudentContingent\StudentController::class, 'store'])->name('students.store');
        Route::get('/{student}/id-card', [App\Http\Controllers\StudentContingent\StudentController::class, 'generateIdCard'])->name('students.id-card');
        Route::get('/{student}', [App\Http\Controllers\StudentContingent\StudentController::class, 'show'])->name('students.show');
        Route::get('/{student}/edit', [App\Http\Controllers\StudentContingent\StudentController::class, 'edit'])->name('students.edit');
        Route::put('/{student}', [App\Http\Controllers\StudentContingent\StudentController::class, 'update'])->name('students.update');
        Route::delete('/{student}', [App\Http\Controllers\StudentContingent\StudentController::class, 'destroy'])->name('students.destroy');
        Route::delete('/{student}/force', [App\Http\Controllers\StudentContingent\StudentController::class, 'forceDelete'])->name('students.force-delete');
    });

    // Student Groups Routes
    Route::prefix('student-contingent')->group(function () {
        Route::prefix('groups')->group(function () {
            Route::get('/', [App\Http\Controllers\StudentContingent\StudentGroupController::class, 'index'])->name('student-contingent.groups.index');
            Route::get('/create', [App\Http\Controllers\StudentContingent\StudentGroupController::class, 'create'])->name('student-contingent.groups.create');
            Route::post('/', [App\Http\Controllers\StudentContingent\StudentGroupController::class, 'store'])->name('student-contingent.groups.store');
            Route::get('/{group}', [App\Http\Controllers\StudentContingent\StudentGroupController::class, 'show'])->name('student-contingent.groups.show');
            Route::get('/{group}/edit', [App\Http\Controllers\StudentContingent\StudentGroupController::class, 'edit'])->name('student-contingent.groups.edit');
            Route::put('/{group}', [App\Http\Controllers\StudentContingent\StudentGroupController::class, 'update'])->name('student-contingent.groups.update');
            Route::delete('/{group}', [App\Http\Controllers\StudentContingent\StudentGroupController::class, 'destroy'])->name('student-contingent.groups.destroy');
            Route::post('/{group}/add-students', [App\Http\Controllers\StudentContingent\StudentGroupController::class, 'addStudents'])->name('student-contingent.groups.add-students');
            Route::delete('/{group}/remove-student/{student}', [App\Http\Controllers\StudentContingent\StudentGroupController::class, 'removeStudent'])->name('student-contingent.groups.remove-student');
            Route::get('/{group}/export', [App\Http\Controllers\StudentContingent\StudentGroupController::class, 'exportStudents'])->name('student-contingent.groups.export');

            // Teacher assignment routes
            Route::post('/{group}/assign-teacher', [App\Http\Controllers\TeacherAssignmentController::class, 'assignTeacher'])->name('student-contingent.groups.assign-teacher');
            Route::delete('/journal-entry/{entry}', [App\Http\Controllers\TeacherAssignmentController::class, 'removeTeacher'])->name('student-contingent.groups.remove-teacher');
            Route::get('/{group}/available-subjects', [App\Http\Controllers\TeacherAssignmentController::class, 'getAvailableSubjects'])->name('student-contingent.groups.available-subjects');
            Route::get('/available-teachers', [App\Http\Controllers\TeacherAssignmentController::class, 'getAvailableTeachers'])->name('student-contingent.groups.available-teachers');
        });
    });
    
    // University Structure Management Routes
    Route::prefix('structure')->group(function () {
        // Faculties
        Route::prefix('faculties')->group(function () {
            Route::get('/', [App\Http\Controllers\Structure\FacultyManagementController::class, 'index'])->name('structure.faculties.index');
            Route::get('/create', [App\Http\Controllers\Structure\FacultyManagementController::class, 'create'])->name('structure.faculties.create');
            Route::post('/', [App\Http\Controllers\Structure\FacultyManagementController::class, 'store'])->name('structure.faculties.store');
            Route::get('/{faculty}', [App\Http\Controllers\Structure\FacultyManagementController::class, 'show'])->name('structure.faculties.show');
            Route::get('/{faculty}/edit', [App\Http\Controllers\Structure\FacultyManagementController::class, 'edit'])->name('structure.faculties.edit');
            Route::put('/{faculty}', [App\Http\Controllers\Structure\FacultyManagementController::class, 'update'])->name('structure.faculties.update');
            Route::delete('/{faculty}', [App\Http\Controllers\Structure\FacultyManagementController::class, 'destroy'])->name('structure.faculties.destroy');
            Route::get('/{faculty}/departments', [App\Http\Controllers\Structure\FacultyManagementController::class, 'departments'])->name('structure.faculties.departments');
            Route::get('/{faculty}/departments/create', [App\Http\Controllers\Structure\FacultyManagementController::class, 'createDepartment'])->name('structure.faculties.createDepartment');
            Route::get('/{faculty}/staffing', [App\Http\Controllers\Structure\FacultyManagementController::class, 'staffing'])->name('structure.faculties.staffing');
            Route::post('/{faculty}/allocate-staff', [App\Http\Controllers\Structure\FacultyManagementController::class, 'allocateStaff'])->name('structure.faculties.allocateStaff');
        });
        
        // Departments
        Route::prefix('departments')->group(function () {
            Route::get('/', [App\Http\Controllers\Structure\DepartmentController::class, 'index'])->name('structure.departments.index');
            Route::get('/create', [App\Http\Controllers\Structure\DepartmentController::class, 'create'])->name('structure.departments.create');
            Route::post('/', [App\Http\Controllers\Structure\DepartmentController::class, 'store'])->name('structure.departments.store');
            Route::get('/{department}', [App\Http\Controllers\Structure\DepartmentController::class, 'show'])->name('structure.departments.show');
            Route::get('/{department}/edit', [App\Http\Controllers\Structure\DepartmentController::class, 'edit'])->name('structure.departments.edit');
            Route::put('/{department}', [App\Http\Controllers\Structure\DepartmentController::class, 'update'])->name('structure.departments.update');
            Route::delete('/{department}', [App\Http\Controllers\Structure\DepartmentController::class, 'destroy'])->name('structure.departments.destroy');
            Route::get('/{department}/staffing', [App\Http\Controllers\Structure\DepartmentController::class, 'staffing'])->name('structure.departments.staffing');
            Route::post('/{department}/allocate-staff', [App\Http\Controllers\Structure\DepartmentController::class, 'allocateStaff'])->name('structure.departments.allocateStaff');
        });
        
        // Positions
        Route::prefix('positions')->group(function () {
            Route::get('/', [App\Http\Controllers\Structure\PositionController::class, 'index'])->name('structure.positions.index');
            Route::get('/create', [App\Http\Controllers\Structure\PositionController::class, 'create'])->name('structure.positions.create');
            Route::post('/', [App\Http\Controllers\Structure\PositionController::class, 'store'])->name('structure.positions.store');
            Route::get('/hierarchy', [App\Http\Controllers\Structure\PositionController::class, 'hierarchy'])->name('structure.positions.hierarchy');
            Route::get('/{position}', [App\Http\Controllers\Structure\PositionController::class, 'show'])->name('structure.positions.show');
            Route::get('/{position}/edit', [App\Http\Controllers\Structure\PositionController::class, 'edit'])->name('structure.positions.edit');
            Route::put('/{position}', [App\Http\Controllers\Structure\PositionController::class, 'update'])->name('structure.positions.update');
            Route::delete('/{position}', [App\Http\Controllers\Structure\PositionController::class, 'destroy'])->name('structure.positions.destroy');
        });
        
        // Organizational Chart
        Route::prefix('chart')->group(function () {
            Route::get('/', [App\Http\Controllers\Structure\OrganizationalChartController::class, 'index'])->name('structure.chart.index');
            Route::get('/faculty/{faculty}', [App\Http\Controllers\Structure\OrganizationalChartController::class, 'facultyChart'])->name('structure.chart.faculty');
            Route::get('/division/{division}', [App\Http\Controllers\Structure\OrganizationalChartController::class, 'divisionChart'])->name('structure.chart.division');
            Route::get('/export', [App\Http\Controllers\Structure\OrganizationalChartController::class, 'exportChart'])->name('structure.chart.export');
            Route::get('/api/structure', [App\Http\Controllers\Structure\OrganizationalChartController::class, 'apiStructure'])->name('structure.chart.api');
        });
        
        
        // Academic Structure Routes
        Route::prefix('academic')->group(function () {
            // Educational Programs
            Route::prefix('programs')->group(function () {
                Route::get('/', [App\Http\Controllers\Structure\Academic\ProgramController::class, 'index'])->name('structure.academic.programs.index');
                Route::get('/create', [App\Http\Controllers\Structure\Academic\ProgramController::class, 'create'])->name('structure.academic.programs.create');
                Route::post('/', [App\Http\Controllers\Structure\Academic\ProgramController::class, 'store'])->name('structure.academic.programs.store');
                Route::get('/{program}', [App\Http\Controllers\Structure\Academic\ProgramController::class, 'show'])->name('structure.academic.programs.show');
                Route::get('/{program}/edit', [App\Http\Controllers\Structure\Academic\ProgramController::class, 'edit'])->name('structure.academic.programs.edit');
                Route::put('/{program}', [App\Http\Controllers\Structure\Academic\ProgramController::class, 'update'])->name('structure.academic.programs.update');
                Route::delete('/{program}', [App\Http\Controllers\Structure\Academic\ProgramController::class, 'destroy'])->name('structure.academic.programs.destroy');
                Route::get('/{program}/curriculum', [App\Http\Controllers\Structure\Academic\ProgramController::class, 'curriculum'])->name('structure.academic.programs.curriculum');
            });
            
            // Subjects
            Route::prefix('subjects')->group(function () {
                Route::get('/', [App\Http\Controllers\Structure\Academic\SubjectController::class, 'index'])->name('structure.academic.subjects.index');
                Route::get('/create', [App\Http\Controllers\Structure\Academic\SubjectController::class, 'create'])->name('structure.academic.subjects.create');
                Route::post('/', [App\Http\Controllers\Structure\Academic\SubjectController::class, 'store'])->name('structure.academic.subjects.store');
                Route::get('/{subject}', [App\Http\Controllers\Structure\Academic\SubjectController::class, 'show'])->name('structure.academic.subjects.show');
                Route::get('/{subject}/edit', [App\Http\Controllers\Structure\Academic\SubjectController::class, 'edit'])->name('structure.academic.subjects.edit');
                Route::put('/{subject}', [App\Http\Controllers\Structure\Academic\SubjectController::class, 'update'])->name('structure.academic.subjects.update');
                Route::delete('/{subject}', [App\Http\Controllers\Structure\Academic\SubjectController::class, 'destroy'])->name('structure.academic.subjects.destroy');
                Route::get('/{subject}/prerequisites', [App\Http\Controllers\Structure\Academic\SubjectController::class, 'prerequisites'])->name('structure.academic.subjects.prerequisites');
                Route::post('/{subject}/prerequisites', [App\Http\Controllers\Structure\Academic\SubjectController::class, 'updatePrerequisites'])->name('structure.academic.subjects.updatePrerequisites');

                // Subject Topics
                Route::get('/{subject}/topics', [App\Http\Controllers\SubjectTopicController::class, 'index'])->name('subjects.topics.index');
                Route::get('/{subject}/topics/create', [App\Http\Controllers\SubjectTopicController::class, 'create'])->name('subjects.topics.create');
                Route::post('/{subject}/topics', [App\Http\Controllers\SubjectTopicController::class, 'store'])->name('subjects.topics.store');
                Route::get('/{subject}/topics/{topic}', [App\Http\Controllers\SubjectTopicController::class, 'show'])->name('subjects.topics.show');
                Route::get('/{subject}/topics/{topic}/edit', [App\Http\Controllers\SubjectTopicController::class, 'edit'])->name('subjects.topics.edit');
                Route::put('/{subject}/topics/{topic}', [App\Http\Controllers\SubjectTopicController::class, 'update'])->name('subjects.topics.update');
                Route::delete('/{subject}/topics/{topic}', [App\Http\Controllers\SubjectTopicController::class, 'destroy'])->name('subjects.topics.destroy');
                Route::post('/{subject}/topics/reorder', [App\Http\Controllers\SubjectTopicController::class, 'reorder'])->name('subjects.topics.reorder');
            });
            
            // Curriculum Builder
            Route::prefix('curriculum')->group(function () {
                Route::get('/', [App\Http\Controllers\Structure\Academic\CurriculumController::class, 'index'])->name('structure.academic.curriculum.index');
                Route::get('/builder/{program}', [App\Http\Controllers\Structure\Academic\CurriculumController::class, 'builder'])->name('structure.academic.curriculum.builder');
                Route::post('/builder/{program}', [App\Http\Controllers\Structure\Academic\CurriculumController::class, 'save'])->name('structure.academic.curriculum.save');
                Route::get('/import', [App\Http\Controllers\Structure\Academic\CurriculumController::class, 'importForm'])->name('structure.academic.curriculum.import');
                Route::post('/import', [App\Http\Controllers\Structure\Academic\CurriculumController::class, 'import'])->name('structure.academic.curriculum.doImport');
                Route::get('/export/{program}', [App\Http\Controllers\Structure\Academic\CurriculumController::class, 'export'])->name('structure.academic.curriculum.export');
                Route::post('/approve/{program}', [App\Http\Controllers\Structure\Academic\CurriculumController::class, 'approve'])->name('structure.academic.curriculum.approve');
                Route::post('/copy/{program}', [App\Http\Controllers\Structure\Academic\CurriculumController::class, 'copy'])->name('structure.academic.curriculum.copy');
                
                // Curriculum Topics
                Route::get('/topics', [App\Http\Controllers\Structure\Academic\CurriculumController::class, 'topics'])->name('structure.academic.curriculum.topics');
                Route::post('/topics/save', [App\Http\Controllers\Structure\Academic\CurriculumController::class, 'saveTopics'])->name('structure.academic.curriculum.saveTopics');
                Route::post('/topics/import', [App\Http\Controllers\Structure\Academic\CurriculumController::class, 'importTopics'])->name('structure.academic.curriculum.importTopics');
                Route::get('/topics/template', [App\Http\Controllers\Structure\Academic\CurriculumController::class, 'downloadTemplate'])->name('structure.academic.curriculum.template');
            });
            
            // Hour Distribution
            Route::prefix('hours')->group(function () {
                Route::get('/', [App\Http\Controllers\Structure\Academic\HourDistributionController::class, 'index'])->name('structure.academic.hours.index');
                Route::get('/distribution/{subject}', [App\Http\Controllers\Structure\Academic\HourDistributionController::class, 'distribution'])->name('structure.academic.hours.distribution');
                Route::post('/distribution/{subject}', [App\Http\Controllers\Structure\Academic\HourDistributionController::class, 'saveDistribution'])->name('structure.academic.hours.saveDistribution');
                Route::get('/template', [App\Http\Controllers\Structure\Academic\HourDistributionController::class, 'template'])->name('structure.academic.hours.template');
                Route::post('/template', [App\Http\Controllers\Structure\Academic\HourDistributionController::class, 'saveTemplate'])->name('structure.academic.hours.saveTemplate');
                Route::get('/validate/{curriculum}', [App\Http\Controllers\Structure\Academic\HourDistributionController::class, 'validate'])->name('structure.academic.hours.validate');
            });
        });
    });
    
    // Standalone Attendance and Grades Routes (without journal parameter)
    Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'allAttendance'])->name('attendance.all');
    Route::get('/grades', [App\Http\Controllers\GradeController::class, 'allGrades'])->name('grades.all');
    
    // Electronic Journal and Schedule Routes
    Route::prefix('journal')->group(function () {
        // Journal Management
        Route::get('/', [App\Http\Controllers\JournalController::class, 'index'])->name('journal.index');
        Route::get('/create', [App\Http\Controllers\JournalController::class, 'create'])->name('journal.create');
        Route::post('/', [App\Http\Controllers\JournalController::class, 'store'])->name('journal.store');
        Route::get('/{journal}', [App\Http\Controllers\JournalController::class, 'show'])->name('journal.show');
        Route::get('/{journal}/edit', [App\Http\Controllers\JournalController::class, 'edit'])->name('journal.edit');
        Route::put('/{journal}', [App\Http\Controllers\JournalController::class, 'update'])->name('journal.update');
        Route::delete('/{journal}', [App\Http\Controllers\JournalController::class, 'destroy'])->name('journal.destroy');
        Route::get('/{journal}/analytics', [App\Http\Controllers\JournalController::class, 'analytics'])->name('journal.analytics');
        
        // Attendance Management
        Route::prefix('{journal}/attendance')->group(function () {
            Route::get('/', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
            Route::get('/create', [App\Http\Controllers\AttendanceController::class, 'create'])->name('attendance.create');
            Route::post('/', [App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');
            Route::get('/edit/{date}', [App\Http\Controllers\AttendanceController::class, 'edit'])->name('attendance.edit');
            Route::put('/{date}', [App\Http\Controllers\AttendanceController::class, 'update'])->name('attendance.update');
            Route::get('/report', [App\Http\Controllers\AttendanceController::class, 'report'])->name('attendance.report');
            Route::post('/bulk-mark', [App\Http\Controllers\AttendanceController::class, 'bulkMark'])->name('attendance.bulkMark');
            Route::get('/statistics', [App\Http\Controllers\AttendanceController::class, 'statistics'])->name('attendance.statistics');
        });
        
        // Grade Management
        Route::prefix('{journal}/grades')->group(function () {
            Route::get('/', [App\Http\Controllers\GradeController::class, 'index'])->name('grades.index');
            Route::get('/create', [App\Http\Controllers\GradeController::class, 'create'])->name('grades.create');
            Route::post('/', [App\Http\Controllers\GradeController::class, 'store'])->name('grades.store');
            Route::post('/add-column', [App\Http\Controllers\GradeController::class, 'addColumn'])->name('grades.addColumn');
            Route::get('/edit/{grade}', [App\Http\Controllers\GradeController::class, 'edit'])->name('grades.edit');
            Route::put('/{grade}', [App\Http\Controllers\GradeController::class, 'update'])->name('grades.update');
            Route::delete('/{grade}', [App\Http\Controllers\GradeController::class, 'destroy'])->name('grades.destroy');
            Route::post('/calculate', [App\Http\Controllers\GradeController::class, 'calculate'])->name('grades.calculate');
            Route::get('/export', [App\Http\Controllers\GradeController::class, 'export'])->name('grades.export');
        });
        
        // Assignment Management
        Route::prefix('assignments')->group(function () {
            Route::get('/', [App\Http\Controllers\AssignmentController::class, 'index'])->name('assignments.index');
            Route::get('/create', [App\Http\Controllers\AssignmentController::class, 'create'])->name('assignments.create');
            Route::post('/', [App\Http\Controllers\AssignmentController::class, 'store'])->name('assignments.store');
            Route::get('/{assignment}', [App\Http\Controllers\AssignmentController::class, 'show'])->name('assignments.show');
            Route::get('/{assignment}/edit', [App\Http\Controllers\AssignmentController::class, 'edit'])->name('assignments.edit');
            Route::put('/{assignment}', [App\Http\Controllers\AssignmentController::class, 'update'])->name('assignments.update');
            Route::delete('/{assignment}', [App\Http\Controllers\AssignmentController::class, 'destroy'])->name('assignments.destroy');
            
            // Submissions
            Route::get('/{assignment}/submissions', [App\Http\Controllers\AssignmentController::class, 'submissions'])->name('assignments.submissions');
            Route::post('/{assignment}/submit', [App\Http\Controllers\AssignmentController::class, 'submit'])->name('assignments.submit');
            Route::post('/submissions/{submission}/grade', [App\Http\Controllers\AssignmentController::class, 'gradeSubmission'])->name('assignments.grade');
        });
    });
    
    // Schedule Management Routes
    Route::prefix('schedule')->group(function () {
        Route::get('/', [App\Http\Controllers\ScheduleController::class, 'index'])->name('schedule.index');
        Route::get('/weekly', [App\Http\Controllers\ScheduleController::class, 'weekly'])->name('schedule.weekly');
        Route::get('/room-monitoring', [App\Http\Controllers\ScheduleController::class, 'roomMonitoring'])->name('schedule.room-monitoring');
        Route::get('/create', [App\Http\Controllers\ScheduleController::class, 'create'])->name('schedule.create');

        // Faculty-based Schedule Builder
        Route::get('/faculty-builder', [App\Http\Controllers\FacultyScheduleController::class, 'index'])->name('schedule.faculty-builder');
        Route::post('/faculty/get-courses', [App\Http\Controllers\FacultyScheduleController::class, 'getCourses'])->name('schedule.faculty.get-courses');
        Route::post('/faculty/get-groups', [App\Http\Controllers\FacultyScheduleController::class, 'getGroups'])->name('schedule.faculty.get-groups');
        Route::post('/faculty/get-schedule-grid', [App\Http\Controllers\FacultyScheduleController::class, 'getScheduleGrid'])->name('schedule.faculty.get-schedule-grid');
        Route::post('/faculty/store-slot', [App\Http\Controllers\FacultyScheduleController::class, 'storeSlot'])->name('schedule.faculty.store-slot');
        Route::delete('/faculty/delete-slot', [App\Http\Controllers\FacultyScheduleController::class, 'deleteSlot'])->name('schedule.faculty.delete-slot');
        Route::post('/faculty/apply-schedule', [App\Http\Controllers\FacultyScheduleController::class, 'applySchedule'])->name('schedule.faculty.apply-schedule');
        Route::post('/faculty/duplicate-week', [App\Http\Controllers\FacultyScheduleController::class, 'duplicateWeek'])->name('schedule.faculty.duplicate-week');
        Route::post('/faculty/auto-generate', [App\Http\Controllers\FacultyScheduleController::class, 'autoGenerate'])->name('schedule.faculty.auto-generate');

        Route::post('/', [App\Http\Controllers\ScheduleController::class, 'store'])->name('schedule.store');
        Route::get('/{schedule}', [App\Http\Controllers\ScheduleController::class, 'show'])->name('schedule.show');
        Route::get('/{schedule}/edit', [App\Http\Controllers\ScheduleController::class, 'edit'])->name('schedule.edit');
        Route::put('/{schedule}', [App\Http\Controllers\ScheduleController::class, 'update'])->name('schedule.update');
        Route::delete('/{schedule}', [App\Http\Controllers\ScheduleController::class, 'destroy'])->name('schedule.destroy');
        
        // Schedule Views
        Route::get('/teacher/{teacher}', [App\Http\Controllers\ScheduleController::class, 'teacherSchedule'])->name('schedule.teacher');
        Route::get('/student/{student}', [App\Http\Controllers\ScheduleController::class, 'studentSchedule'])->name('schedule.student');
        Route::get('/group/{group}', [App\Http\Controllers\ScheduleController::class, 'groupSchedule'])->name('schedule.group');
        Route::get('/room/{room}', [App\Http\Controllers\ScheduleController::class, 'roomSchedule'])->name('schedule.room');
        
        // Schedule Generation
        Route::post('/generate', [App\Http\Controllers\ScheduleController::class, 'generate'])->name('schedule.generate');
        Route::post('/{schedule}/approve', [App\Http\Controllers\ScheduleController::class, 'approve'])->name('schedule.approve');
        Route::post('/{schedule}/publish', [App\Http\Controllers\ScheduleController::class, 'publish'])->name('schedule.publish');
        
        // Export
        Route::get('/{schedule}/export', [App\Http\Controllers\ScheduleController::class, 'export'])->name('schedule.export');
        Route::get('/{schedule}/print', [App\Http\Controllers\ScheduleController::class, 'print'])->name('schedule.print');
    });
    
    // LMS (Online Ta'lim) Routes
    // CMS Routes
    Route::prefix('cms')->name('cms.')->group(function () {
        // Dashboard
        Route::get('/', function() {
            return view('cms.dashboard');
        })->name('dashboard');
        
        // Pages
        Route::resource('pages', App\Http\Controllers\CMS\PageController::class);
        Route::get('pages/{page}/builder', [App\Http\Controllers\CMS\PageBuilderController::class, 'builder'])->name('pages.builder');
        Route::post('pages/{page}/builder/save', [App\Http\Controllers\CMS\PageBuilderController::class, 'save'])->name('pages.builder.save');
        Route::post('pages/builder/upload-image', [App\Http\Controllers\CMS\PageBuilderController::class, 'uploadImage'])->name('pages.builder.upload');
        Route::get('pages/builder/templates', [App\Http\Controllers\CMS\PageBuilderController::class, 'getTemplates'])->name('pages.builder.templates');
        Route::get('pages/{page}/preview', [App\Http\Controllers\CMS\PageController::class, 'preview'])->name('pages.preview');
        Route::post('pages/{page}/duplicate', [App\Http\Controllers\CMS\PageController::class, 'duplicate'])->name('pages.duplicate');
        
        // Menus
        Route::resource('menus', App\Http\Controllers\CMS\MenuController::class);
        Route::post('menus/{menu}/items', [App\Http\Controllers\CMS\MenuController::class, 'storeItem'])->name('menus.items.store');
        Route::put('menus/{menu}/items/{item}', [App\Http\Controllers\CMS\MenuController::class, 'updateItem'])->name('menus.items.update');
        Route::delete('menus/{menu}/items/{item}', [App\Http\Controllers\CMS\MenuController::class, 'destroyItem'])->name('menus.items.destroy');

        // News
        Route::resource('news', App\Http\Controllers\CMS\NewsController::class);
        Route::get('news-categories', [App\Http\Controllers\CMS\NewsController::class, 'categories'])->name('news.categories');
        
        // Events
        Route::resource('events', App\Http\Controllers\CMS\EventController::class);
        Route::get('events/{event}/registrations', [App\Http\Controllers\CMS\EventController::class, 'registrations'])->name('events.registrations');
        
        // Media Library
        Route::get('media', [App\Http\Controllers\CMS\MediaController::class, 'index'])->name('media.index');
        Route::post('media/upload', [App\Http\Controllers\CMS\MediaController::class, 'upload'])->name('media.upload');
        Route::delete('media/{media}', [App\Http\Controllers\CMS\MediaController::class, 'destroy'])->name('media.destroy');

        // TinyMCE Image Upload
        Route::post('upload/image', [App\Http\Controllers\CMS\MediaController::class, 'uploadImage'])->name('upload.image');
        
        // Widgets
        Route::get('widgets', function() {
            return view('cms.widgets.index');
        })->name('widgets.index');
        
        // Site Templates (Design)
        Route::prefix('templates')->name('templates.')->group(function () {
            Route::get('/', [App\Http\Controllers\CMS\TemplateController::class, 'index'])->name('index');
            Route::post('/activate', [App\Http\Controllers\CMS\TemplateController::class, 'activate'])->name('activate');
            Route::get('/preview/{template}', [App\Http\Controllers\CMS\TemplateController::class, 'preview'])->name('preview');
        });
        
        // Settings
        Route::get('settings', function() {
            return view('cms.settings.index');
        })->name('settings.index');

        Route::post('settings/clear-cache', function() {
            $results = [];
            $commands = [
                'config' => 'config:clear',
                'cache'  => 'cache:clear',
                'route'  => 'route:clear',
                'view'   => 'view:clear',
            ];
            foreach ($commands as $label => $cmd) {
                try {
                    \Artisan::call($cmd);
                    $results[$label] = ['ok' => true, 'output' => trim(\Artisan::output()) ?: 'Tozalandi'];
                } catch (\Throwable $e) {
                    $results[$label] = ['ok' => false, 'output' => $e->getMessage()];
                }
            }
            return response()->json(['success' => true, 'results' => $results]);
        })->name('settings.clear-cache');

        // Theme Management
        Route::prefix('themes')->name('themes.')->group(function () {
            Route::get('/', [App\Http\Controllers\CMS\ThemeController::class, 'index'])->name('index');
            Route::put('/', [App\Http\Controllers\CMS\ThemeController::class, 'update'])->name('update');
            Route::post('/preview', [App\Http\Controllers\CMS\ThemeController::class, 'preview'])->name('preview');
        });

        // Header & Footer Management
        Route::prefix('header-footer')->name('header-footer.')->group(function () {
            Route::get('/header', [App\Http\Controllers\CMS\HeaderFooterController::class, 'header'])->name('header');
            Route::post('/header', [App\Http\Controllers\CMS\HeaderFooterController::class, 'updateHeader'])->name('header.update');
            Route::post('/header/add-menu', [App\Http\Controllers\CMS\HeaderFooterController::class, 'addHeaderMenu'])->name('header.add-menu');
            Route::post('/header/delete-menu', [App\Http\Controllers\CMS\HeaderFooterController::class, 'deleteHeaderMenu'])->name('header.delete-menu');
            Route::post('/header/toggle-menu', [App\Http\Controllers\CMS\HeaderFooterController::class, 'toggleMenuStatus'])->name('header.toggle-menu');
            Route::post('/header/add-submenu', [App\Http\Controllers\CMS\HeaderFooterController::class, 'addSubmenu'])->name('header.add-submenu');
            Route::post('/header/delete-submenu', [App\Http\Controllers\CMS\HeaderFooterController::class, 'deleteSubmenu'])->name('header.delete-submenu');
            Route::post('/header/update-submenu', [App\Http\Controllers\CMS\HeaderFooterController::class, 'updateSubmenu'])->name('header.update-submenu');
            Route::get('/footer', [App\Http\Controllers\CMS\HeaderFooterController::class, 'footer'])->name('footer');
            Route::post('/footer', [App\Http\Controllers\CMS\HeaderFooterController::class, 'updateFooter'])->name('footer.update');
            Route::post('/footer/add-link', [App\Http\Controllers\CMS\HeaderFooterController::class, 'addFooterLink'])->name('footer.add-link');
            Route::post('/footer/delete-link', [App\Http\Controllers\CMS\HeaderFooterController::class, 'deleteFooterLink'])->name('footer.delete-link');
        });

        // Homepage Management
        Route::prefix('homepage')->name('homepage.')->group(function () {
            Route::get('/', [App\Http\Controllers\CMS\HomePageController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\CMS\HomePageController::class, 'update'])->name('update');
            Route::post('/upload-image', [App\Http\Controllers\CMS\HomePageController::class, 'uploadImage'])->name('upload-image');
            Route::post('/delete-image', [App\Http\Controllers\CMS\HomePageController::class, 'deleteImage'])->name('delete-image');
        });

        // About Page Management
        Route::prefix('about')->name('about.')->group(function () {
            Route::get('/', [App\Http\Controllers\CMS\AboutPageController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\CMS\AboutPageController::class, 'update'])->name('update');
            Route::post('/upload-image', [App\Http\Controllers\CMS\AboutPageController::class, 'uploadImage'])->name('upload-image');
            Route::post('/delete-image', [App\Http\Controllers\CMS\AboutPageController::class, 'deleteImage'])->name('delete-image');
        });

        // Programs Page Management
        Route::prefix('programs')->name('programs.')->group(function () {
            Route::get('/', [App\Http\Controllers\CMS\ProgramsPageController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\CMS\ProgramsPageController::class, 'update'])->name('update');
        });

        // Statistics Page Management
        Route::prefix('statistics')->name('statistics.')->group(function () {
            Route::get('/', [App\Http\Controllers\CMS\StatisticsPageController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\CMS\StatisticsPageController::class, 'update'])->name('update');
        });

        // Contact Page Management
        Route::prefix('contact')->name('contact.')->group(function () {
            Route::get('/', [App\Http\Controllers\CMS\ContactPageController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\CMS\ContactPageController::class, 'update'])->name('update');
        });

        // Teachers Page Management
        Route::prefix('teachers')->name('teachers.')->group(function () {
            Route::get('/', [App\Http\Controllers\CMS\TeachersPageController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\CMS\TeachersPageController::class, 'update'])->name('update');
            Route::post('/import', [App\Http\Controllers\CMS\TeachersPageController::class, 'importTeachers'])->name('import');
            Route::post('/migrate-to-employees', [App\Http\Controllers\CMS\TeachersPageController::class, 'migrateToEmployees'])->name('migrate-to-employees');
            Route::post('/teacher', [App\Http\Controllers\CMS\TeachersPageController::class, 'storeTeacher'])->name('store-teacher');
            Route::put('/teacher/{id}', [App\Http\Controllers\CMS\TeachersPageController::class, 'updateTeacher'])->name('update-teacher');
            Route::delete('/teacher/{id}', [App\Http\Controllers\CMS\TeachersPageController::class, 'destroyTeacher'])->name('destroy');
        });

        // State Symbols Page Management
        Route::prefix('state-symbols')->name('state-symbols.')->group(function () {
            Route::get('/', [App\Http\Controllers\CMS\StateSymbolsPageController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\CMS\StateSymbolsPageController::class, 'update'])->name('update');
            Route::post('/upload-image', [App\Http\Controllers\CMS\StateSymbolsPageController::class, 'uploadImage'])->name('upload-image');
            Route::post('/delete-image', [App\Http\Controllers\CMS\StateSymbolsPageController::class, 'deleteImage'])->name('delete-image');
        });
    });
    
    // Public Library Route (without authentication)
    Route::get('/library', [App\Http\Controllers\PublicLibraryController::class, 'index'])->name('public.library');
    Route::get('/library/book/{id}', [App\Http\Controllers\PublicLibraryController::class, 'show'])->name('public.library.show');
    Route::get('/library/download/{id}', [App\Http\Controllers\PublicLibraryController::class, 'download'])->name('public.library.download');

    // Public Chat Routes (outside auth middleware)
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');

    // Public Forum Routes
    Route::prefix('forum')->name('forum.')->group(function () {
        Route::get('/', [App\Http\Controllers\ForumController::class, 'index'])->name('index');
        Route::get('/category/{slug}', [App\Http\Controllers\ForumController::class, 'category'])->name('category');
        Route::get('/topic/{slug}', [App\Http\Controllers\ForumController::class, 'topic'])->name('topic');
        Route::get('/search', [App\Http\Controllers\ForumController::class, 'search'])->name('search');
        Route::get('/rules', [App\Http\Controllers\ForumController::class, 'rules'])->name('rules');
        Route::get('/members', [App\Http\Controllers\ForumController::class, 'members'])->name('members');

        // Authenticated forum routes
        Route::middleware(['auth'])->group(function () {
            Route::get('/create-topic/{category?}', [App\Http\Controllers\ForumController::class, 'createTopic'])->name('create-topic');
            Route::post('/store-topic', [App\Http\Controllers\ForumController::class, 'storeTopic'])->name('store-topic');
            Route::post('/topic/{slug}/reply', [App\Http\Controllers\ForumController::class, 'storePost'])->name('reply');
            Route::post('/like/{type}/{id}', [App\Http\Controllers\ForumController::class, 'like'])->name('like');
        });
    });

    // LMS Routes
    Route::prefix('lms')->name('lms.')->group(function () {
        // Dashboard
        Route::get('/', [App\Http\Controllers\LMS\LmsDashboardController::class, 'index'])->name('dashboard');
        Route::get('/progress', [App\Http\Controllers\LMS\LmsDashboardController::class, 'myProgress'])->name('progress');
        
        // Courses
        Route::resource('courses', App\Http\Controllers\LMS\CourseController::class);
        Route::post('courses/{course}/enroll', [App\Http\Controllers\LMS\CourseController::class, 'enroll'])->name('courses.enroll');
        Route::get('courses/{course}/learn', [App\Http\Controllers\LMS\CourseController::class, 'learn'])->name('courses.learn');
        Route::get('courses/{course}/resources', [App\Http\Controllers\LMS\CourseController::class, 'resources'])->name('courses.resources');
        Route::post('courses/{course}/resources', [App\Http\Controllers\LMS\CourseController::class, 'uploadResource'])->name('courses.uploadResource');
        Route::delete('courses/{course}/resources/{resource}', [App\Http\Controllers\LMS\CourseController::class, 'deleteResource'])->name('courses.deleteResource');
        Route::post('courses/{course}/attach-material', [App\Http\Controllers\LMS\CourseController::class, 'attachMaterial'])->name('courses.attachMaterial');
        Route::post('courses/{course}/attach-video', [App\Http\Controllers\LMS\CourseController::class, 'attachVideo'])->name('courses.attachVideo');
        Route::post('courses/{course}/attach-test', [App\Http\Controllers\LMS\CourseController::class, 'attachTest'])->name('courses.attachTest');
        Route::post('courses/{course}/archive', [App\Http\Controllers\LMS\CourseController::class, 'archive'])->name('courses.archive');
        Route::post('courses/{course}/restore', [App\Http\Controllers\LMS\CourseController::class, 'restore'])->name('courses.restore');
        Route::post('courses/{course}/progress', [App\Http\Controllers\LMS\CourseController::class, 'updateProgress'])->name('courses.progress');
        Route::delete('courses/{course}/thumbnail', [App\Http\Controllers\LMS\CourseController::class, 'deleteThumbnail'])->name('courses.deleteThumbnail');
        Route::delete('courses/{course}/intro-video', [App\Http\Controllers\LMS\CourseController::class, 'deleteIntroVideo'])->name('courses.deleteIntroVideo');

        // Course Curriculum/Topics
        Route::get('courses/{course}/curriculum', [App\Http\Controllers\LMS\CourseController::class, 'curriculum'])->name('courses.curriculum');
        Route::post('courses/{course}/topics', [App\Http\Controllers\LMS\CourseController::class, 'storeTopic'])->name('courses.topics.store');
        Route::get('courses/{course}/topics/{topic}', [App\Http\Controllers\LMS\CourseController::class, 'getTopic'])->name('courses.topics.show');
        Route::put('courses/{course}/topics/{topic}', [App\Http\Controllers\LMS\CourseController::class, 'updateTopic'])->name('courses.topics.update');
        Route::delete('courses/{course}/topics/{topic}', [App\Http\Controllers\LMS\CourseController::class, 'destroyTopic'])->name('courses.topics.destroy');
        Route::post('courses/{course}/topics/{topic}/resources', [App\Http\Controllers\LMS\CourseController::class, 'storeTopicResource'])->name('courses.topics.resources.store');
        Route::delete('courses/{course}/topics/{topic}/resources/{resource}', [App\Http\Controllers\LMS\CourseController::class, 'destroyTopicResource'])->name('courses.topics.resources.destroy');

        // Learning Materials
        Route::resource('materials', App\Http\Controllers\LMS\LmsMaterialController::class);
        Route::get('materials/{material}/download', [App\Http\Controllers\LMS\LmsMaterialController::class, 'download'])->name('materials.download');
        
        // Video Lessons
        Route::resource('videos', App\Http\Controllers\LMS\LmsVideoController::class);
        Route::get('videos/{video}/watch', [App\Http\Controllers\LMS\LmsVideoController::class, 'watch'])->name('videos.watch');
        Route::post('videos/{video}/progress', [App\Http\Controllers\LMS\LmsVideoController::class, 'updateProgress'])->name('videos.progress');
        
        // Practice Tests
        Route::resource('tests', App\Http\Controllers\LMS\TestController::class);
        Route::get('tests/{test}/start', [App\Http\Controllers\LMS\TestController::class, 'start'])->name('tests.start');
        Route::post('tests/{test}/submit', [App\Http\Controllers\LMS\TestController::class, 'submit'])->name('tests.submit');
        Route::get('tests/{test}/result/{attempt}', [App\Http\Controllers\LMS\TestController::class, 'result'])->name('tests.result');
        Route::get('tests/{test}/export', [App\Http\Controllers\LMS\TestController::class, 'exportResults'])->name('tests.export');
        
        // Forum
        Route::resource('forum', App\Http\Controllers\LMS\LmsForumController::class);
        Route::post('forum/{post}/reply', [App\Http\Controllers\LMS\LmsForumController::class, 'reply'])->name('forum.reply');
        Route::post('forum/{post}/react', [App\Http\Controllers\LMS\LmsForumController::class, 'react'])->name('forum.react');
        Route::post('forum/{post}/best-answer', [App\Http\Controllers\LMS\LmsForumController::class, 'markBestAnswer'])->name('forum.bestAnswer');
        Route::post('forum/{post}/pin', [App\Http\Controllers\LMS\LmsForumController::class, 'pin'])->name('forum.pin');
        Route::post('forum/{post}/lock', [App\Http\Controllers\LMS\LmsForumController::class, 'lock'])->name('forum.lock');
        Route::post('forum/{post}/report', [App\Http\Controllers\LMS\LmsForumController::class, 'report'])->name('forum.report');
        Route::post('forum/{post}/moderate', [App\Http\Controllers\LMS\LmsForumController::class, 'moderate'])->name('forum.moderate');
        
        // E-Library
        Route::get('library/categories', [App\Http\Controllers\LMS\LibraryController::class, 'categories'])->name('library.categories');
        Route::post('library/categories', [App\Http\Controllers\LMS\LibraryController::class, 'storeCategory'])->name('library.categories.store');
        Route::put('library/categories/{category}', [App\Http\Controllers\LMS\LibraryController::class, 'updateCategory'])->name('library.categories.update');
        Route::delete('library/categories/{category}', [App\Http\Controllers\LMS\LibraryController::class, 'destroyCategory'])->name('library.categories.destroy');
        Route::get('library/{book}/read', [App\Http\Controllers\LMS\LibraryController::class, 'read'])->name('library.read');
        Route::get('library/{book}/download', [App\Http\Controllers\LMS\LibraryController::class, 'download'])->name('library.download');
        Route::resource('library', App\Http\Controllers\LMS\LibraryController::class)->parameters(['library' => 'book']);
        Route::post('library/{book}/bookmark', [App\Http\Controllers\LMS\LibraryController::class, 'bookmark'])->name('library.bookmark');
        Route::post('library/{book}/rate', [App\Http\Controllers\LMS\LibraryController::class, 'rate'])->name('library.rate');
        
        // Certificates
        Route::resource('certificates', App\Http\Controllers\LMS\LmsCertificateController::class);
        Route::get('certificates/{certificate}/download', [App\Http\Controllers\LMS\LmsCertificateController::class, 'download'])->name('certificates.download');
        Route::get('certificates/verify/{code}', [App\Http\Controllers\LMS\LmsCertificateController::class, 'verify'])->name('certificates.verify');

        // Exams - O'qituvchi uchun
        Route::prefix('exams')->name('exams.')->group(function () {
            // Imtihonlar boshqaruvi
            Route::get('/', [App\Http\Controllers\LMS\ExamController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\LMS\ExamController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\LMS\ExamController::class, 'store'])->name('store');
            Route::get('/{exam}', [App\Http\Controllers\LMS\ExamController::class, 'show'])->name('show');
            Route::get('/{exam}/edit', [App\Http\Controllers\LMS\ExamController::class, 'edit'])->name('edit');
            Route::put('/{exam}', [App\Http\Controllers\LMS\ExamController::class, 'update'])->name('update');
            Route::delete('/{exam}', [App\Http\Controllers\LMS\ExamController::class, 'destroy'])->name('destroy');

            // Savollar boshqaruvi
            Route::get('/{exam}/questions', [App\Http\Controllers\LMS\ExamController::class, 'questions'])->name('questions');
            Route::post('/{exam}/questions', [App\Http\Controllers\LMS\ExamController::class, 'storeQuestion'])->name('questions.store');
            Route::put('/questions/{question}', [App\Http\Controllers\LMS\ExamController::class, 'updateQuestion'])->name('questions.update');
            Route::delete('/questions/{question}', [App\Http\Controllers\LMS\ExamController::class, 'destroyQuestion'])->name('questions.destroy');
            Route::post('/{exam}/questions/reorder', [App\Http\Controllers\LMS\ExamController::class, 'reorderQuestions'])->name('questions.reorder');

            // Imtihon holati
            Route::post('/{exam}/publish', [App\Http\Controllers\LMS\ExamController::class, 'publish'])->name('publish');
            Route::post('/{exam}/unpublish', [App\Http\Controllers\LMS\ExamController::class, 'unpublish'])->name('unpublish');

            // Natijalar
            Route::get('/{exam}/results', [App\Http\Controllers\LMS\ExamController::class, 'results'])->name('results');
            Route::get('/attempts/{attempt}', [App\Http\Controllers\LMS\ExamController::class, 'attemptDetails'])->name('attempt');
            Route::post('/answers/{answer}/grade', [App\Http\Controllers\LMS\ExamController::class, 'gradeAnswer'])->name('grade-answer');
            Route::post('/{exam}/sync-journal', [App\Http\Controllers\LMS\ExamController::class, 'syncToJournal'])->name('sync-journal');

            // Talaba uchun
            Route::get('/my/list', [App\Http\Controllers\LMS\ExamController::class, 'studentExams'])->name('my-list');
            Route::get('/{exam}/info', [App\Http\Controllers\LMS\ExamController::class, 'studentExamInfo'])->name('info');
            Route::post('/{exam}/start', [App\Http\Controllers\LMS\ExamController::class, 'startExam'])->name('start');
            Route::get('/take/{attempt}', [App\Http\Controllers\LMS\ExamController::class, 'takeExam'])->name('take');

            // SECURITY FIX: Rate limit answer submissions to prevent spam (BUG #15)
            Route::post('/take/{attempt}/save', [App\Http\Controllers\LMS\ExamController::class, 'saveAnswer'])
                ->middleware('throttle:60,1')  // 60 requests per minute
                ->name('save-answer');

            Route::post('/take/{attempt}/submit', [App\Http\Controllers\LMS\ExamController::class, 'submitExam'])->name('submit');
            Route::get('/result/{attempt}', [App\Http\Controllers\LMS\ExamController::class, 'examResult'])->name('result');
            Route::post('/take/{attempt}/tab-switch', [App\Http\Controllers\LMS\ExamController::class, 'reportTabSwitch'])->name('tab-switch');
        });
    });
    
    // Employee Management Routes
    Route::prefix('employees')->group(function () {
        // Main employee routes
        Route::get('/', [App\Http\Controllers\Employees\EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/teachers', [App\Http\Controllers\Employees\EmployeeController::class, 'teachers'])->name('employees.teachers');
        Route::get('/administrative', [App\Http\Controllers\Employees\EmployeeController::class, 'administrative'])->name('employees.administrative');
        Route::get('/create', [App\Http\Controllers\Employees\EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/', [App\Http\Controllers\Employees\EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/{employee}', [App\Http\Controllers\Employees\EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/{employee}/edit', [App\Http\Controllers\Employees\EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/{employee}', [App\Http\Controllers\Employees\EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/{employee}', [App\Http\Controllers\Employees\EmployeeController::class, 'destroy'])->name('employees.destroy');
        
        // Teacher-specific routes
        Route::prefix('teachers')->group(function () {
            // Subject assignments
            Route::get('/{teacher}/subjects', [App\Http\Controllers\Employees\TeacherAssignmentController::class, 'assignSubjects'])->name('employees.teachers.subjects');
            Route::post('/{teacher}/subjects', [App\Http\Controllers\Employees\TeacherAssignmentController::class, 'storeSubjectAssignment'])->name('employees.teachers.subjects.store');
            Route::put('/{teacher}/subjects/{assignment}', [App\Http\Controllers\Employees\TeacherAssignmentController::class, 'updateSubjectAssignment'])->name('employees.teachers.subjects.update');
            Route::delete('/{teacher}/subjects/{assignment}', [App\Http\Controllers\Employees\TeacherAssignmentController::class, 'removeSubjectAssignment'])->name('employees.teachers.subjects.destroy');
            
            // Group assignments
            Route::get('/{teacher}/groups', [App\Http\Controllers\Employees\TeacherAssignmentController::class, 'assignGroups'])->name('employees.teachers.groups');
            Route::post('/{teacher}/groups', [App\Http\Controllers\Employees\TeacherAssignmentController::class, 'storeGroupAssignment'])->name('employees.teachers.groups.store');
            Route::delete('/{teacher}/groups/{group}', [App\Http\Controllers\Employees\TeacherAssignmentController::class, 'removeGroupAssignment'])->name('employees.teachers.groups.destroy');
            
            // Workload
            Route::get('/{teacher}/workload', [App\Http\Controllers\Employees\TeacherAssignmentController::class, 'workload'])->name('employees.teachers.workload');
        });
        
        // Orders - Commented out because EmploymentOrderController doesn't exist
        // Route::prefix('orders')->group(function () {
        //     Route::get('/', [App\Http\Controllers\Employees\EmploymentOrderController::class, 'index'])->name('employees.orders.index');
        //     Route::get('/create', [App\Http\Controllers\Employees\EmploymentOrderController::class, 'create'])->name('employees.orders.create');
        //     Route::post('/', [App\Http\Controllers\Employees\EmploymentOrderController::class, 'store'])->name('employees.orders.store');
        //     Route::get('/{order}', [App\Http\Controllers\Employees\EmploymentOrderController::class, 'show'])->name('employees.orders.show');
        //     Route::post('/{order}/approve', [App\Http\Controllers\Employees\EmploymentOrderController::class, 'approve'])->name('employees.orders.approve');
        //     Route::get('/{order}/pdf', [App\Http\Controllers\Employees\EmploymentOrderController::class, 'generatePdf'])->name('employees.orders.pdf');
        // });
        
        // Leaves - Commented out because EmployeeLeaveController doesn't exist
        // Route::prefix('leaves')->group(function () {
        //     Route::get('/', [App\Http\Controllers\Employees\EmployeeLeaveController::class, 'index'])->name('employees.leaves.index');
        //     Route::get('/create', [App\Http\Controllers\Employees\EmployeeLeaveController::class, 'create'])->name('employees.leaves.create');
        //     Route::post('/', [App\Http\Controllers\Employees\EmployeeLeaveController::class, 'store'])->name('employees.leaves.store');
        //     Route::post('/{leave}/approve', [App\Http\Controllers\Employees\EmployeeLeaveController::class, 'approve'])->name('employees.leaves.approve');
        //     Route::post('/{leave}/reject', [App\Http\Controllers\Employees\EmployeeLeaveController::class, 'reject'])->name('employees.leaves.reject');
        // });
        
        // Reports - Commented out because EmployeeReportController doesn't exist
        // Route::prefix('reports')->group(function () {
        //     Route::get('/staff-list', [App\Http\Controllers\Employees\EmployeeReportController::class, 'staffList'])->name('employees.reports.staff');
        //     Route::get('/workload', [App\Http\Controllers\Employees\EmployeeReportController::class, 'workloadReport'])->name('employees.reports.workload');
        //     Route::get('/statistics', [App\Http\Controllers\Employees\EmployeeReportController::class, 'statistics'])->name('employees.reports.statistics');
        // });
    });
    
    // Page Builder Routes
    Route::prefix('page-builder')->group(function () {
        Route::get('/', [PageBuilderController::class, 'index'])->name('page-builder.index');
        Route::get('/editor/{id?}', [PageBuilderController::class, 'editor'])->name('page-builder.editor');
        Route::post('/pages', [PageBuilderController::class, 'store'])->name('page-builder.store');
        Route::put('/pages/{id}', [PageBuilderController::class, 'update'])->name('page-builder.update');
        Route::post('/pages/{id}/content', [PageBuilderController::class, 'saveContent'])->name('page-builder.save-content');
        Route::post('/pages/{id}/duplicate', [PageBuilderController::class, 'duplicate'])->name('page-builder.duplicate');
        Route::get('/pages/{id}/preview', [PageBuilderController::class, 'preview'])->name('page-builder.preview');
        Route::post('/pages/{id}/publish', [PageBuilderController::class, 'publish'])->name('page-builder.publish');
        Route::get('/pages/{id}/revisions', [PageBuilderController::class, 'getRevisions'])->name('page-builder.revisions');
        Route::post('/pages/{pageId}/revisions/{revisionId}/restore', [PageBuilderController::class, 'restoreRevision'])->name('page-builder.restore-revision');
    });
});

// Public page routes (outside auth middleware)
Route::get('/page/{slug}', function($slug) {
    $page = \App\Models\PageBuilder\PbPage::where('slug', $slug)
        ->where('status', 'published')
        ->with(['sections.columns.elements', 'assets'])
        ->firstOrFail();
    
    return view('page-builder.public', compact('page'));
})->name('page.show');

// Password Reset Routes
Route::middleware('guest')->group(function () {
    Route::get('/password/reset', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/password/email', function (Request $request) {
        $request->validate(['email' => 'required|email']);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Bu email bilan foydalanuvchi topilmadi.']);
        }

        // Generate reset token
        $token = \Illuminate\Support\Str::random(64);

        // Store token in password_reset_tokens table
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => bcrypt($token),
                'created_at' => now()
            ]
        );

        // For now, show the reset link directly (since email may not be configured)
        $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($request->email));

        return back()->with('status', 'Parolni tiklash uchun administrator bilan bog\'laning yoki quyidagi havoladan foydalaning: ' . $resetUrl);
    })->name('password.email');

    Route::get('/password/reset/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token, 'email' => request('email')]);
    })->name('password.reset');

    Route::post('/password/reset', function (Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $resetRecord = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !\Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Noto\'g\'ri token yoki email.']);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            return back()->withErrors(['email' => 'Token muddati tugagan. Qaytadan so\'rov yuboring.']);
        }

        // Update user password
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user) {
            $user->password = $request->password;
            $user->save();

            // Delete reset token
            \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return redirect()->route('login')->with('status', 'Parol muvaffaqiyatli yangilandi! Endi tizimga kirishingiz mumkin.');
        }

        return back()->withErrors(['email' => 'Foydalanuvchi topilmadi.']);
    })->name('password.update');
});

// Finance Module Routes
Route::middleware(['auth'])->prefix('finance')->name('finance.')->group(function () {
    // Dashboard
    Route::get('/', [App\Http\Controllers\Finance\FinanceController::class, 'index'])->name('dashboard');
    Route::get('/transactions', [App\Http\Controllers\Finance\FinanceController::class, 'transactions'])->name('transactions');
    Route::get('/reports', [App\Http\Controllers\Finance\FinanceController::class, 'reports'])->name('reports');

    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Finance\PaymentController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Finance\PaymentController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Finance\PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [App\Http\Controllers\Finance\PaymentController::class, 'show'])->name('show');
        Route::get('/{payment}/edit', [App\Http\Controllers\Finance\PaymentController::class, 'edit'])->name('edit');
        Route::put('/{payment}', [App\Http\Controllers\Finance\PaymentController::class, 'update'])->name('update');
        Route::post('/{payment}/cancel', [App\Http\Controllers\Finance\PaymentController::class, 'cancel'])->name('cancel');
    });

    // Scholarships/Grants
    Route::prefix('scholarships')->name('scholarships.')->group(function () {
        Route::get('/', [App\Http\Controllers\Finance\ScholarshipController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Finance\ScholarshipController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Finance\ScholarshipController::class, 'store'])->name('store');
        Route::get('/{scholarship}', [App\Http\Controllers\Finance\ScholarshipController::class, 'show'])->name('show');
        Route::get('/{scholarship}/edit', [App\Http\Controllers\Finance\ScholarshipController::class, 'edit'])->name('edit');
        Route::put('/{scholarship}', [App\Http\Controllers\Finance\ScholarshipController::class, 'update'])->name('update');
        Route::post('/{scholarship}/award', [App\Http\Controllers\Finance\ScholarshipController::class, 'award'])->name('award');
        Route::post('/student-scholarship/{studentScholarship}/revoke', [App\Http\Controllers\Finance\ScholarshipController::class, 'revoke'])->name('revoke');
        Route::post('/student-scholarship/{studentScholarship}/payment', [App\Http\Controllers\Finance\ScholarshipController::class, 'processPayment'])->name('payment');
    });
});

// Reports Module Routes
Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [App\Http\Controllers\ReportController::class, 'index'])->name('index');
    Route::get('/students', [App\Http\Controllers\ReportController::class, 'students'])->name('students');
    Route::get('/finance', [App\Http\Controllers\ReportController::class, 'finance'])->name('finance');
    Route::get('/academic', [App\Http\Controllers\ReportController::class, 'academic'])->name('academic');
    Route::get('/attendance', [App\Http\Controllers\ReportController::class, 'attendance'])->name('attendance');
    Route::get('/export', [App\Http\Controllers\ReportController::class, 'export'])->name('export');
    Route::get('/print', [App\Http\Controllers\ReportController::class, 'print'])->name('print');
});

// Statistics Module Routes (dashboard - requires auth)
Route::middleware(['auth'])->prefix('statistics')->name('statistics.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\StatisticsController::class, 'index'])->name('index');
    Route::get('/realtime', [App\Http\Controllers\StatisticsController::class, 'realtime'])->name('realtime');
    Route::get('/finance', [App\Http\Controllers\StatisticsController::class, 'finance'])->name('finance');
    Route::get('/academic', [App\Http\Controllers\StatisticsController::class, 'academic'])->name('academic');
    Route::get('/comparison', [App\Http\Controllers\StatisticsController::class, 'comparison'])->name('comparison');
});

// Profile Routes
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/',               [App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
    Route::put('/',               [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
    Route::put('/change-password',[App\Http\Controllers\ProfileController::class, 'changePassword'])->name('change-password');
});

// Qo'shimcha imkoniyatlar
Route::middleware(['auth'])->get('/extra', function () {
    return view('extra.index');
})->name('extra.index');

// Local avatar generator (firewall-safe replacement for ui-avatars.com)
Route::get('/avatar', [App\Http\Controllers\AvatarController::class, 'show'])->name('avatar');

// Fallback file server for storage/app/public — works even if the
// `storage:link` symlink cannot be created on the server.
Route::get('/storage/{path}', [App\Http\Controllers\StorageController::class, 'show'])
    ->where('path', '.*')
    ->name('storage.file');

// Settings Module Routes
Route::middleware(['auth'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [App\Http\Controllers\SettingsController::class, 'index'])->name('index');

    // General Settings
    Route::get('/general', [App\Http\Controllers\SettingsController::class, 'general'])->name('general');
    Route::put('/general', [App\Http\Controllers\SettingsController::class, 'updateGeneral'])->name('general.update');

    // Academic Settings
    Route::get('/academic', [App\Http\Controllers\SettingsController::class, 'academic'])->name('academic');
    Route::put('/academic', [App\Http\Controllers\SettingsController::class, 'updateAcademic'])->name('academic.update');
    Route::post('/academic/year', [App\Http\Controllers\SettingsController::class, 'storeAcademicYear'])->name('academic.year.store');
    Route::post('/academic/semester', [App\Http\Controllers\SettingsController::class, 'storeSemester'])->name('academic.semester.store');
    Route::post('/academic/start-new-year', [App\Http\Controllers\SettingsController::class, 'startNewAcademicYear'])->name('academic.start-new-year');
    Route::get('/academic/preview-new-year', [App\Http\Controllers\SettingsController::class, 'previewNewAcademicYear'])->name('academic.preview-new-year');

    // Finance Settings
    Route::get('/finance', [App\Http\Controllers\SettingsController::class, 'finance'])->name('finance');
    Route::put('/finance', [App\Http\Controllers\SettingsController::class, 'updateFinance'])->name('finance.update');

    // System Settings
    Route::get('/system', [App\Http\Controllers\SettingsController::class, 'system'])->name('system');
    Route::put('/system', [App\Http\Controllers\SettingsController::class, 'updateSystem'])->name('system.update');
    Route::post('/system/backup', [App\Http\Controllers\SettingsController::class, 'createBackup'])->name('system.backup');
    Route::post('/system/cache-clear', [App\Http\Controllers\SettingsController::class, 'clearCache'])->name('system.cache-clear');

    // Security Settings
    Route::get('/security', [App\Http\Controllers\SettingsController::class, 'security'])->name('security');
    Route::put('/security', [App\Http\Controllers\SettingsController::class, 'updateSecurity'])->name('security.update');

    // Activity Logs
    Route::get('/activity-logs', [App\Http\Controllers\SettingsController::class, 'activityLogs'])->name('activity-logs');

    // Integrations
    Route::get('/integrations', [App\Http\Controllers\SettingsController::class, 'integrations'])->name('integrations');
    Route::get('/integrations/telegram', [App\Http\Controllers\SettingsController::class, 'telegram'])->name('integrations.telegram');
    Route::put('/integrations/telegram', [App\Http\Controllers\SettingsController::class, 'updateTelegram'])->name('integrations.telegram.update');


});

// Admin Settings Routes (Superadmin and Admin)
Route::middleware(['auth', 'role:SuperAdmin|Admin'])->prefix('admin/settings')->name('admin.settings.')->group(function () {
    // User Management
    Route::resource('users', App\Http\Controllers\Admin\UserManagementController::class);

    // Role Management
    Route::resource('roles', App\Http\Controllers\Admin\RoleManagementController::class);

    // Permission Management
    Route::resource('permissions', App\Http\Controllers\Admin\PermissionManagementController::class);

    // Module Visibility
    Route::get('modules', [App\Http\Controllers\Admin\ModuleVisibilityController::class, 'index'])->name('modules.index');
    Route::put('modules/{role}', [App\Http\Controllers\Admin\ModuleVisibilityController::class, 'update'])->name('modules.update');
    Route::post('modules/update-access', [App\Http\Controllers\Admin\ModuleVisibilityController::class, 'updateModuleAccess'])->name('modules.update-access');

    // OTP Settings
    Route::get('otp', [App\Http\Controllers\Admin\OtpSettingsController::class, 'index'])->name('otp.index');
    Route::put('otp', [App\Http\Controllers\Admin\OtpSettingsController::class, 'update'])->name('otp.update');
    Route::post('otp/test-sms', [App\Http\Controllers\Admin\OtpSettingsController::class, 'testSms'])->name('otp.test-sms');
    Route::post('otp/get-eskiz-token', [App\Http\Controllers\Admin\OtpSettingsController::class, 'getEskizToken'])->name('otp.get-eskiz-token');
});

// API routes for dynamic form loading

// DEBUG: Test database connection
Route::get('/api/debug-db', function() {
    try {
        $config = config('database.connections.mysql');
        $pdo = DB::connection()->getPdo();
        return response()->json([
            'status' => 'success',
            'connection' => 'Connected',
            'database' => $config['database'],
            'username' => $config['username'],
            'host' => $config['host'],
            'driver_name' => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'config' => config('database.connections.mysql'),
        ], 500);
    }
});

Route::get('/api/faculties/{faculty}/specialties', function($facultyId) {
    try {
        $specialties = \App\Models\Specialty::where('faculty_id', $facultyId)
            ->where('is_active', true)
            ->get(['id', 'name_uz', 'name_ru', 'name_en', 'code']);
        return response()->json($specialties);
    } catch (\Exception $e) {
        \Log::error('Specialty fetch error', [
            'faculty_id' => $facultyId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'error' => $e->getMessage(),
            'faculty_id' => $facultyId
        ], 500);
    }
});

Route::get('/api/specialties/{specialty}/groups', function($specialtyId) {
    try {
        $groups = \App\Models\Group::where('specialty_id', $specialtyId)
            ->where('is_active', true)
            ->where('name', 'NOT LIKE', 'Test%')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'course']);
        return response()->json($groups);
    } catch (\Exception $e) {
        \Log::error('Group fetch error', [
            'specialty_id' => $specialtyId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'error' => $e->getMessage(),
            'specialty_id' => $specialtyId
        ], 500);
    }
});

// Keyingi student ID ni olish
Route::get('/api/next-student-id', function() {
    $lastStudent = \App\Models\Student::orderBy('id', 'desc')->first();
    $nextNumber = $lastStudent ? ($lastStudent->id + 1) : 1;
    $studentId = 'EXA-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    return response()->json(['student_id' => $studentId]);
});

// HR routes are defined in routes/hr.php (loaded at top via require)

// =============================================================================
// Vacancy Routes - CMS catch-all dan oldin bo'lishi kerak
// =============================================================================
require __DIR__.'/vacancy.php';


// =============================================================================
// CMS Public Pages - CATCH-ALL route (ENG OXIRIDA BO'LISHI SHART!)
// Bu route boshqa routelardan keyin kelishi kerak, aks holda boshqa routelarni buzadi
// =============================================================================
Route::get('/{slug}', function($slug) {
    // Reserved sluglar - bu URLlar boshqa routelar tomonidan ishlatiladi
    $reserved = [
        'login', 'register', 'logout', 'password', 'dashboard', 'admin',
        'api', 'storage', 'css', 'js', 'images', 'assets', 'fonts',
        'favicon.ico', 'robots.txt', 'sitemap.xml',
        'vacancies', 'davlat-ramzlari', 'chat', 'forum', 'campus-tour'
    ];

    if (in_array($slug, $reserved)) {
        abort(404);
    }

    $page = \App\Models\CmsPage::where('slug', $slug)
        ->where('status', 'published')
        ->first();

    if (!$page) {
        abort(404);
    }

    $page->increment('views_count');

    return view('cms.pages.preview', compact('page'));
})->name('cms.page.public')->where('slug', '[a-zA-Z0-9\-]+');

