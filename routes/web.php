<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Redirect the site root to the students index
Route::get('/', function () {
    return redirect()->route('students.index');
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

