@extends('layouts.app')

@section('page-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Admin Dashboard</h1>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card bg-white">
            <div class="stat-number">{{ $totalUsers }}</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card bg-white">
            <div class="stat-number">{{ $totalStudents }}</div>
            <div class="stat-label">Students</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card bg-white">
            <div class="stat-number">{{ $totalInstructors }}</div>
            <div class="stat-label">Faculty Members</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card bg-white">
            <div class="stat-number">{{ $totalCourses }}</div>
            <div class="stat-label">Courses</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card bg-white">
            <div class="stat-number">{{ $totalEnrollments }}</div>
            <div class="stat-label">Total Enrollments</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4 mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm me-2">
                    <i class="fas fa-plus"></i> Add Student
                </a>
                <a href="{{ route('admin.instructors.create') }}" class="btn btn-primary btn-sm me-2">
                    <i class="fas fa-plus"></i> Add Faculty
                </a>
                <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-sm me-2">
                    <i class="fas fa-plus"></i> Add Course
                </a>
                <a href="{{ route('admin.sections.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Section
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Enrollments -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Enrollments</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Section</th>
                            <th>Date Enrolled</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentEnrollments as $enrollment)
                        <tr>
                            <td>{{ $enrollment->student->first_name ?? 'N/A' }} {{ $enrollment->student->last_name ?? '' }}</td>
                            <td>{{ $enrollment->section->course->course_code ?? 'N/A' }}</td>
                            <td>{{ $enrollment->date_enrolled->format('M d, Y') ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-success">{{ $enrollment->status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.enrollments.show', $enrollment->enrollment_id) }}" class="btn btn-sm btn-info">
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No recent enrollments
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
