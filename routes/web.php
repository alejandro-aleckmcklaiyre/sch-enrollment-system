<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AuthController;

// Redirect the site root to login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->intended(match(auth()->user()->role) {
            'admin' => '/admin/dashboard',
            'faculty' => '/faculty/dashboard',
            'student' => '/student/dashboard',
            default => '/student/dashboard'
        });
    }
    return redirect('/login');
});

Route::get('/db-test', function () {
    $start = microtime(true);

    try {
        // run a lightweight query to test the connection
        $result = DB::select('SELECT 1 as ok');

        $elapsed = round((microtime(true) - $start) * 1000, 2);

        return response()->json([
            'status' => 'success',
            'message' => 'Database connection successful',
            'result' => $result,
            'elapsed_ms' => $elapsed,
        ]);
    } catch (\Exception $e) {
        $elapsed = round((microtime(true) - $start) * 1000, 2);

        // log the error for debugging locally
        Log::error('DB Test failed: ' . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Database connection failed',
            'error' => $e->getMessage(),
            'elapsed_ms' => $elapsed,
        ], 500);
    }
})->middleware('api');

// ==============================================
// AUTHENTICATION ROUTES (Public)
// ==============================================

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register/{role?}', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

use App\Http\Controllers\StudentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\CoursePrerequisiteController;


Route::resource('students', StudentController::class)->only(['index','store','update','destroy']);
Route::post('students/export-excel', [StudentController::class, 'exportExcel']);
Route::get('students/export-pdf', [StudentController::class, 'exportPDF']);
Route::post('students/backup', [StudentController::class, 'backup']);
Route::post('students/restore', [StudentController::class, 'restore']);
Route::get('students/backups', [StudentController::class, 'listBackups']);
Route::get('students/backups/{filename}', [StudentController::class, 'downloadBackup']);
Route::delete('students/backups/{filename}', [StudentController::class, 'deleteBackup']);

Route::resource('programs', ProgramController::class)->only(['index','store','update','destroy']);
Route::post('programs/export-excel', [ProgramController::class, 'exportExcel']);
Route::get('programs/export-pdf', [ProgramController::class, 'exportPDF']);
Route::post('programs/backup', [ProgramController::class, 'backup']);
Route::post('programs/restore', [ProgramController::class, 'restore']);
Route::get('programs/backups', [ProgramController::class, 'listBackups']);
Route::get('programs/backups/{filename}', [ProgramController::class, 'downloadBackup']);
Route::delete('programs/backups/{filename}', [ProgramController::class, 'deleteBackup']);

Route::resource('courses', CourseController::class)->only(['index','store','update','destroy']);
Route::post('courses/export-excel', [CourseController::class, 'exportExcel']);
Route::get('courses/export-pdf', [CourseController::class, 'exportPDF']);
Route::post('courses/backup', [CourseController::class, 'backup']);
Route::post('courses/restore', [CourseController::class, 'restore']);
Route::get('courses/backups', [CourseController::class, 'listBackups']);
Route::get('courses/backups/{filename}', [CourseController::class, 'downloadBackup']);
Route::delete('courses/backups/{filename}', [CourseController::class, 'deleteBackup']);

Route::resource('instructors', InstructorController::class)->only(['index','store','update','destroy']);
Route::post('instructors/export-excel', [InstructorController::class, 'exportExcel']);
Route::get('instructors/export-pdf', [InstructorController::class, 'exportPDF']);
Route::post('instructors/backup', [InstructorController::class, 'backup']);
Route::post('instructors/restore', [InstructorController::class, 'restore']);
Route::get('instructors/backups', [InstructorController::class, 'listBackups']);
Route::get('instructors/backups/{filename}', [InstructorController::class, 'downloadBackup']);
Route::delete('instructors/backups/{filename}', [InstructorController::class, 'deleteBackup']);

Route::resource('rooms', RoomController::class)->only(['index','store','update','destroy']);
Route::post('rooms/export-excel', [RoomController::class, 'exportExcel']);
Route::get('rooms/export-pdf', [RoomController::class, 'exportPDF']);
Route::post('rooms/backup', [RoomController::class, 'backup']);
Route::post('rooms/restore', [RoomController::class, 'restore']);
Route::get('rooms/backups', [RoomController::class, 'listBackups']);
Route::get('rooms/backups/{filename}', [RoomController::class, 'downloadBackup']);
Route::delete('rooms/backups/{filename}', [RoomController::class, 'deleteBackup']);

Route::resource('departments', DepartmentController::class)->only(['index','store','update','destroy']);
Route::post('departments/export-excel', [DepartmentController::class, 'exportExcel'])->name('departments.exportExcel');
Route::get('departments/export-pdf', [DepartmentController::class, 'exportPDF'])->name('departments.exportPDF');
Route::post('departments/backup', [DepartmentController::class, 'backup']);
Route::post('departments/restore', [DepartmentController::class, 'restore']);
Route::get('departments/backups', [DepartmentController::class, 'listBackups']);
Route::get('departments/backups/{filename}', [DepartmentController::class, 'downloadBackup']);
Route::delete('departments/backups/{filename}', [DepartmentController::class, 'deleteBackup']);

Route::resource('enrollments', EnrollmentController::class)->only(['index','store','update','destroy']);
Route::post('enrollments/export-excel', [EnrollmentController::class, 'exportExcel'])->name('enrollments.exportExcel');
Route::get('enrollments/export-pdf', [EnrollmentController::class, 'exportPDF'])->name('enrollments.exportPDF');
// API: get courses/section rows for a section identifier (section_code or section_id)
Route::get('enrollments/sections/{identifier}/courses', [EnrollmentController::class, 'getSectionCourses']);
Route::post('enrollments/backup', [EnrollmentController::class, 'backup']);
Route::post('enrollments/restore', [EnrollmentController::class, 'restore']);
Route::get('enrollments/backups', [EnrollmentController::class, 'listBackups']);
Route::get('enrollments/backups/{filename}', [EnrollmentController::class, 'downloadBackup']);
Route::delete('enrollments/backups/{filename}', [EnrollmentController::class, 'deleteBackup']);

Route::resource('sections', SectionController::class)->only(['index','store','update','destroy']);
Route::post('sections/export-excel', [SectionController::class, 'exportExcel'])->name('sections.exportExcel');
Route::get('sections/export-pdf', [SectionController::class, 'exportPDF'])->name('sections.exportPDF');
Route::post('sections/backup', [SectionController::class, 'backup']);
Route::post('sections/restore', [SectionController::class, 'restore']);
Route::get('sections/backups', [SectionController::class, 'listBackups']);
Route::get('sections/backups/{filename}', [SectionController::class, 'downloadBackup']);
Route::delete('sections/backups/{filename}', [SectionController::class, 'deleteBackup']);

Route::resource('terms', TermController::class)->only(['index','store','update','destroy']);
Route::post('terms/export-excel', [TermController::class, 'exportExcel'])->name('terms.exportExcel');
Route::get('terms/export-pdf', [TermController::class, 'exportPDF'])->name('terms.exportPDF');
Route::post('terms/backup', [TermController::class, 'backup']);
Route::post('terms/restore', [TermController::class, 'restore']);
Route::get('terms/backups', [TermController::class, 'listBackups']);
Route::get('terms/backups/{filename}', [TermController::class, 'downloadBackup']);
Route::delete('terms/backups/{filename}', [TermController::class, 'deleteBackup']);

Route::resource('course-prerequisites', CoursePrerequisiteController::class)->only(['index','store','update','destroy']);
Route::post('course-prerequisites/export-excel', [CoursePrerequisiteController::class, 'exportExcel'])->name('courseprereqs.exportExcel');
Route::get('course-prerequisites/export-pdf', [CoursePrerequisiteController::class, 'exportPDF'])->name('courseprereqs.exportPDF');
Route::post('course-prerequisites/backup', [CoursePrerequisiteController::class, 'backup']);
Route::post('course-prerequisites/restore', [CoursePrerequisiteController::class, 'restore']);
Route::get('course-prerequisites/backups', [CoursePrerequisiteController::class, 'listBackups']);
Route::get('course-prerequisites/backups/{filename}', [CoursePrerequisiteController::class, 'downloadBackup']);
Route::delete('course-prerequisites/backups/{filename}', [CoursePrerequisiteController::class, 'deleteBackup']);

// ==============================================
// ADMIN ROUTES (Protected by 'auth' and 'admin' middleware)
// ==============================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Admin Resources - Use existing controllers
    // Exclude 'show' to avoid conflicts with custom routes like 'export-pdf'
    Route::resource('students', StudentController::class)->except(['show']);
    Route::post('students/export-excel', [StudentController::class, 'exportExcel'])->name('students.export-excel');
    Route::get('students/export-pdf', [StudentController::class, 'exportPDF'])->name('students.export-pdf');
    Route::post('students/backup', [StudentController::class, 'backup'])->name('students.backup');
    Route::post('students/restore', [StudentController::class, 'restore'])->name('students.restore');
    Route::get('students/backups', [StudentController::class, 'listBackups'])->name('students.backups');
    Route::get('students/backups/{filename}', [StudentController::class, 'downloadBackup'])->name('students.backups.download');
    Route::delete('students/backups/{filename}', [StudentController::class, 'deleteBackup'])->name('students.backups.delete');

    Route::resource('programs', ProgramController::class);
    Route::post('programs/export-excel', [ProgramController::class, 'exportExcel']);
    Route::get('programs/export-pdf', [ProgramController::class, 'exportPDF']);
    Route::post('programs/backup', [ProgramController::class, 'backup']);
    Route::post('programs/restore', [ProgramController::class, 'restore']);
    Route::get('programs/backups', [ProgramController::class, 'listBackups']);
    Route::get('programs/backups/{filename}', [ProgramController::class, 'downloadBackup']);
    Route::delete('programs/backups/{filename}', [ProgramController::class, 'deleteBackup']);

    Route::resource('courses', CourseController::class);
    Route::post('courses/export-excel', [CourseController::class, 'exportExcel']);
    Route::get('courses/export-pdf', [CourseController::class, 'exportPDF']);
    Route::post('courses/backup', [CourseController::class, 'backup']);
    Route::post('courses/restore', [CourseController::class, 'restore']);
    Route::get('courses/backups', [CourseController::class, 'listBackups']);
    Route::get('courses/backups/{filename}', [CourseController::class, 'downloadBackup']);
    Route::delete('courses/backups/{filename}', [CourseController::class, 'deleteBackup']);

    Route::resource('instructors', InstructorController::class);
    Route::post('instructors/export-excel', [InstructorController::class, 'exportExcel']);
    Route::get('instructors/export-pdf', [InstructorController::class, 'exportPDF']);
    Route::post('instructors/backup', [InstructorController::class, 'backup']);
    Route::post('instructors/restore', [InstructorController::class, 'restore']);
    Route::get('instructors/backups', [InstructorController::class, 'listBackups']);
    Route::get('instructors/backups/{filename}', [InstructorController::class, 'downloadBackup']);
    Route::delete('instructors/backups/{filename}', [InstructorController::class, 'deleteBackup']);

    Route::resource('rooms', RoomController::class);
    Route::post('rooms/export-excel', [RoomController::class, 'exportExcel']);
    Route::get('rooms/export-pdf', [RoomController::class, 'exportPDF']);
    Route::post('rooms/backup', [RoomController::class, 'backup']);
    Route::post('rooms/restore', [RoomController::class, 'restore']);
    Route::get('rooms/backups', [RoomController::class, 'listBackups']);
    Route::get('rooms/backups/{filename}', [RoomController::class, 'downloadBackup']);
    Route::delete('rooms/backups/{filename}', [RoomController::class, 'deleteBackup']);

    Route::resource('departments', DepartmentController::class);
    Route::post('departments/export-excel', [DepartmentController::class, 'exportExcel']);
    Route::get('departments/export-pdf', [DepartmentController::class, 'exportPDF']);
    Route::post('departments/backup', [DepartmentController::class, 'backup']);
    Route::post('departments/restore', [DepartmentController::class, 'restore']);
    Route::get('departments/backups', [DepartmentController::class, 'listBackups']);
    Route::get('departments/backups/{filename}', [DepartmentController::class, 'downloadBackup']);
    Route::delete('departments/backups/{filename}', [DepartmentController::class, 'deleteBackup']);

    Route::resource('enrollments', EnrollmentController::class);
    Route::post('enrollments/export-excel', [EnrollmentController::class, 'exportExcel']);
    Route::get('enrollments/export-pdf', [EnrollmentController::class, 'exportPDF']);
    Route::get('enrollments/sections/{identifier}/courses', [EnrollmentController::class, 'getSectionCourses']);
    Route::post('enrollments/backup', [EnrollmentController::class, 'backup']);
    Route::post('enrollments/restore', [EnrollmentController::class, 'restore']);
    Route::get('enrollments/backups', [EnrollmentController::class, 'listBackups']);
    Route::get('enrollments/backups/{filename}', [EnrollmentController::class, 'downloadBackup']);
    Route::delete('enrollments/backups/{filename}', [EnrollmentController::class, 'deleteBackup']);

    Route::resource('sections', SectionController::class);
    Route::post('sections/export-excel', [SectionController::class, 'exportExcel']);
    Route::get('sections/export-pdf', [SectionController::class, 'exportPDF']);
    Route::post('sections/backup', [SectionController::class, 'backup']);
    Route::post('sections/restore', [SectionController::class, 'restore']);
    Route::get('sections/backups', [SectionController::class, 'listBackups']);
    Route::get('sections/backups/{filename}', [SectionController::class, 'downloadBackup']);
    Route::delete('sections/backups/{filename}', [SectionController::class, 'deleteBackup']);

    Route::resource('terms', TermController::class);
    Route::post('terms/export-excel', [TermController::class, 'exportExcel']);
    Route::get('terms/export-pdf', [TermController::class, 'exportPDF']);
    Route::post('terms/backup', [TermController::class, 'backup']);
    Route::post('terms/restore', [TermController::class, 'restore']);
    Route::get('terms/backups', [TermController::class, 'listBackups']);
    Route::get('terms/backups/{filename}', [TermController::class, 'downloadBackup']);
    Route::delete('terms/backups/{filename}', [TermController::class, 'deleteBackup']);

    Route::resource('course-prerequisites', CoursePrerequisiteController::class);
    Route::post('course-prerequisites/export-excel', [CoursePrerequisiteController::class, 'exportExcel']);
    Route::get('course-prerequisites/export-pdf', [CoursePrerequisiteController::class, 'exportPDF']);
    Route::post('course-prerequisites/backup', [CoursePrerequisiteController::class, 'backup']);
    Route::post('course-prerequisites/restore', [CoursePrerequisiteController::class, 'restore']);
    Route::get('course-prerequisites/backups', [CoursePrerequisiteController::class, 'listBackups']);
    Route::get('course-prerequisites/backups/{filename}', [CoursePrerequisiteController::class, 'downloadBackup']);
    Route::delete('course-prerequisites/backups/{filename}', [CoursePrerequisiteController::class, 'deleteBackup']);
});

// ==============================================
// FACULTY ROUTES (Protected by 'auth' and 'faculty' middleware)
// ==============================================
Route::middleware(['auth', 'faculty'])->prefix('faculty')->name('faculty.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Faculty\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/schedule', [App\Http\Controllers\Faculty\ScheduleController::class, 'index'])->name('schedule');
    Route::get('/sections', [App\Http\Controllers\Faculty\SectionsController::class, 'index'])->name('sections');
    Route::get('/sections/{section}', [App\Http\Controllers\Faculty\SectionsController::class, 'show'])->name('sections.show');
    Route::get('/sections/{section}/students', [App\Http\Controllers\Faculty\StudentRosterController::class, 'show'])->name('students.show');
    Route::get('/sections/{section}/grades', [App\Http\Controllers\Faculty\GradesController::class, 'show'])->name('grades.show');
    Route::post('/sections/{section}/grades', [App\Http\Controllers\Faculty\GradesController::class, 'store'])->name('grades.store');
    Route::get('/sections/{section}/enrollments', [App\Http\Controllers\Faculty\EnrollmentsController::class, 'show'])->name('enrollments.show');
    Route::get('/courses', [App\Http\Controllers\Faculty\CoursesController::class, 'index'])->name('courses');
    Route::get('/reports', [App\Http\Controllers\Faculty\ReportsController::class, 'index'])->name('reports');
    Route::get('/profile', [App\Http\Controllers\Faculty\ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Faculty\ProfileController::class, 'update'])->name('profile.update');
});

// ==============================================
// STUDENT ROUTES (Protected by 'auth' and 'student' middleware)
// ==============================================
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/enrollments', [App\Http\Controllers\Student\EnrollmentsController::class, 'index'])->name('enrollments');
    Route::get('/schedule', [App\Http\Controllers\Student\ScheduleController::class, 'index'])->name('schedule');
    Route::get('/courses', [App\Http\Controllers\Student\CoursesController::class, 'index'])->name('courses');
    Route::get('/courses/{course}', [App\Http\Controllers\Student\CoursesController::class, 'show'])->name('courses.show');
    Route::post('/courses/{course}/enroll', [App\Http\Controllers\Student\CoursesController::class, 'enroll'])->name('courses.enroll');
    Route::get('/transcript', [App\Http\Controllers\Student\TranscriptController::class, 'index'])->name('transcript');
    Route::get('/transcript/download', [App\Http\Controllers\Student\TranscriptController::class, 'download'])->name('transcript.download');
    Route::get('/progress', [App\Http\Controllers\Student\ProgressController::class, 'index'])->name('progress');
    Route::get('/announcements', [App\Http\Controllers\Student\AnnouncementsController::class, 'index'])->name('announcements');
    Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
});
