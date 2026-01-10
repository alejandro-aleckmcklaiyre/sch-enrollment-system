@extends('layouts.app')

@section('page-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Student Dashboard</h1>
</div>

@if ($needsProfileSetup)
<!-- Welcome Section for new students -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">Welcome to Your Student Portal!</h5>
            </div>
            <div class="card-body">
                <p>Your account has been successfully created. Before you can view your course enrollments and grades, please complete your student profile.</p>
                <p>Click the button below to set up your profile information:</p>
                <a href="{{ route('student.profile') }}" class="btn btn-info">
                    <i class="fas fa-user-circle"></i> Complete Your Profile
                </a>
            </div>
        </div>
    </div>
</div>
@else
<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Welcome, {{ $student->first_name }} {{ $student->last_name }}</h5>
            </div>
            <div class="card-body">
                <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
                <p><strong>Email:</strong> {{ $student->email }}</p>
                <p><strong>Program:</strong> {{ $student->program->program_name ?? 'Not assigned' }}</p>
                @if($student->year_level)
                <p><strong>Year Level:</strong> {{ $student->year_level }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title text-muted">Current Enrollments</h6>
                <h3 class="text-primary">{{ $currentEnrollments->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title text-muted">Completed Courses</h6>
                <h3 class="text-success">{{ $completedEnrollments->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title text-muted">Total Enrollments</h6>
                <h3 class="text-info">{{ count($enrollments) }}</h3>
            </div>
        </div>
    </div>
    @if ($currentTerm)
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title text-muted">Current Term</h6>
                <h3 class="text-warning">{{ $currentTerm->term_code }}</h3>
                <small class="text-muted">{{ $currentTerm->term_name }}</small>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Quick Links -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('student.enrollments') }}" class="btn btn-primary">
                        <i class="fas fa-clipboard-list"></i> My Enrollments
                    </a>
                    <a href="{{ route('student.schedule') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-alt"></i> My Schedule
                    </a>
                    <a href="{{ route('student.transcript') }}" class="btn btn-primary">
                        <i class="fas fa-graduation-cap"></i> Transcript
                    </a>
                    <a href="{{ route('student.courses') }}" class="btn btn-success">
                        <i class="fas fa-book"></i> Browse Courses
                    </a>
                    <a href="{{ route('student.progress') }}" class="btn btn-info">
                        <i class="fas fa-chart-line"></i> Progress
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Current Enrollments -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Current Enrollments</h5>
                <a href="{{ route('student.enrollments') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                @if($currentEnrollments->count() > 0)
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Instructor</th>
                            <th>Term</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($currentEnrollments as $enrollment)
                        <tr>
                            <td><strong>{{ $enrollment->section?->course?->course_code ?? 'N/A' }}</strong></td>
                            <td>{{ $enrollment->section?->course?->course_title ?? 'N/A' }}</td>
                            <td>{{ $enrollment->section?->instructor?->first_name ?? 'N/A' }} {{ $enrollment->section?->instructor?->last_name ?? '' }}</td>
                            <td>{{ $enrollment->section?->term?->term_name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-success">{{ $enrollment->status }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center text-muted py-4">
                    <p>No current enrollments found.</p>
                    <a href="{{ route('student.courses') }}" class="btn btn-success">
                        <i class="fas fa-book"></i> Enroll in Courses
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection

