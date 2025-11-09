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

Route::resource('programs', ProgramController::class)->only(['index','store','update','destroy']);
Route::post('programs/export-excel', [ProgramController::class, 'exportExcel']);
Route::get('programs/export-pdf', [ProgramController::class, 'exportPDF']);

Route::resource('courses', CourseController::class)->only(['index','store','update','destroy']);
Route::post('courses/export-excel', [CourseController::class, 'exportExcel']);
Route::get('courses/export-pdf', [CourseController::class, 'exportPDF']);

Route::resource('instructors', InstructorController::class)->only(['index','store','update','destroy']);
Route::post('instructors/export-excel', [InstructorController::class, 'exportExcel']);
Route::get('instructors/export-pdf', [InstructorController::class, 'exportPDF']);

Route::resource('rooms', RoomController::class)->only(['index','store','update','destroy']);
Route::post('rooms/export-excel', [RoomController::class, 'exportExcel']);
Route::get('rooms/export-pdf', [RoomController::class, 'exportPDF']);

Route::resource('departments', DepartmentController::class)->only(['index','store','update','destroy']);
Route::post('departments/export-excel', [DepartmentController::class, 'exportExcel'])->name('departments.exportExcel');
Route::get('departments/export-pdf', [DepartmentController::class, 'exportPDF'])->name('departments.exportPDF');

Route::resource('enrollments', EnrollmentController::class)->only(['index','store','update','destroy']);
Route::post('enrollments/export-excel', [EnrollmentController::class, 'exportExcel'])->name('enrollments.exportExcel');
Route::get('enrollments/export-pdf', [EnrollmentController::class, 'exportPDF'])->name('enrollments.exportPDF');
// API: get courses/section rows for a section identifier (section_code or section_id)
Route::get('enrollments/sections/{identifier}/courses', [EnrollmentController::class, 'getSectionCourses']);

Route::resource('sections', SectionController::class)->only(['index','store','update','destroy']);
Route::post('sections/export-excel', [SectionController::class, 'exportExcel'])->name('sections.exportExcel');
Route::get('sections/export-pdf', [SectionController::class, 'exportPDF'])->name('sections.exportPDF');

Route::resource('terms', TermController::class)->only(['index','store','update','destroy']);
Route::post('terms/export-excel', [TermController::class, 'exportExcel'])->name('terms.exportExcel');
Route::get('terms/export-pdf', [TermController::class, 'exportPDF'])->name('terms.exportPDF');

Route::resource('course-prerequisites', CoursePrerequisiteController::class)->only(['index','store','update','destroy']);
Route::post('course-prerequisites/export-excel', [CoursePrerequisiteController::class, 'exportExcel'])->name('courseprereqs.exportExcel');
Route::get('course-prerequisites/export-pdf', [CoursePrerequisiteController::class, 'exportPDF'])->name('courseprereqs.exportPDF');

