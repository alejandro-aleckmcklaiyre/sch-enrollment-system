@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Student Dashboard</h1>
    @if($currentTerm)
        <span class="badge bg-info fs-6">{{ $currentTerm->term_name }}</span>
    @endif
</div>

@if ($needsProfileSetup)
<!-- Welcome Section for new students -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Welcome to Your Student Portal!</h5>
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
            <div class="card-header bg-light">
                <h5 class="card-title mb-0"><i class="fas fa-user-check"></i> Welcome, {{ $student->first_name }} {{ $student->last_name }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Student ID:</strong> {{ $student->student_id }}</p>
                        <p class="mb-2"><strong>Email:</strong> {{ $student->email ?? 'Not provided' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Program:</strong> {{ $student->program->program_name ?? 'Not assigned' }}</p>
                        @if($student->year_level)
                            <p class="mb-2"><strong>Year Level:</strong> <span class="badge bg-secondary">{{ $student->year_level }}</span></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <i class="fas fa-clipboard-list text-primary" style="font-size: 2rem;"></i>
                <h6 class="card-title text-muted mt-2 mb-1">Current Courses</h6>
                <h3 class="text-primary mb-0">{{ $currentEnrollments->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <i class="fas fa-graduation-cap text-success" style="font-size: 2rem;"></i>
                <h6 class="card-title text-muted mt-2 mb-1">Completed Courses</h6>
                <h3 class="text-success mb-0">{{ $completedEnrollments->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <i class="fas fa-book text-warning" style="font-size: 2rem;"></i>
                <h6 class="card-title text-muted mt-2 mb-1">Credits (Current)</h6>
                <h3 class="text-warning mb-0">{{ $totalCreditsEnrolled }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <i class="fas fa-star text-info" style="font-size: 2rem;"></i>
                <h6 class="card-title text-muted mt-2 mb-1">Credits (Completed)</h6>
                <h3 class="text-info mb-0">{{ $totalCreditsCompleted }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0"><i class="fas fa-lightning-bolt"></i> Quick Actions</h5>
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
                    <a href="{{ route('student.announcements') }}" class="btn btn-warning">
                        <i class="fas fa-bell"></i> Announcements
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Two Column Layout -->
<div class="row">
    <!-- Current Enrollments -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h5 class="card-title mb-0"><i class="fas fa-hourglass-half"></i> Current Courses</h5>
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
                            <th>Credits</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($currentEnrollments as $enrollment)
                        <tr>
                            <td><strong>{{ $enrollment->section?->course?->course_code ?? 'N/A' }}</strong></td>
                            <td>{{ $enrollment->section?->course?->course_title ?? 'N/A' }}</td>
                            <td>
                                <small>{{ $enrollment->section?->instructor?->first_name ?? 'N/A' }} {{ $enrollment->section?->instructor?->last_name ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $enrollment->section?->course?->units ?? '0' }}</span>
                            </td>
                            <td>
                                @if($enrollment->letter_grade)
                                    <span class="badge bg-info">{{ $enrollment->letter_grade }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="mt-3 mb-3">No current enrollments found.</p>
                    <a href="{{ route('student.courses') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-book"></i> Enroll in Courses
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Announcements -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-bell"></i> Latest Announcements</h5>
                <a href="{{ route('student.announcements') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="list-group list-group-flush">
                @if($announcements->count() > 0)
                    @foreach($announcements as $announcement)
                    <div class="list-group-item border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $announcement['title'] }}</h6>
                                <p class="text-muted small mb-2">{{ Str::limit($announcement['body'], 80) }}</p>
                                <div class="d-flex gap-2 align-items-center">
                                    @if($announcement['type'] === 'system')
                                        <span class="badge bg-primary">System</span>
                                    @elseif($announcement['type'] === 'academic')
                                        <span class="badge bg-info">Academic</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($announcement['type']) }}</span>
                                    @endif
                                    <small class="text-muted">{{ $announcement['date']->format('M d') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-bell-slash" style="font-size: 2rem; opacity: 0.3;"></i>
                    <p class="mt-3">No announcements at this time.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity / Upcoming Classes -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0"><i class="fas fa-calendar-check"></i> Course Rooms & Locations</h5>
            </div>
            <div class="table-responsive">
                @if($currentEnrollments->count() > 0)
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Course</th>
                            <th>Section</th>
                            <th>Room</th>
                            <th>Building</th>
                            <th>Instructor</th>
                            <th>Term</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($currentEnrollments as $enrollment)
                        <tr>
                            <td>
                                <strong>{{ $enrollment->section?->course?->course_code ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $enrollment->section?->course?->course_title ?? '' }}</small>
                            </td>
                            <td>{{ $enrollment->section?->section_code ?? 'N/A' }}</td>
                            <td>
                                @if($enrollment->section?->room)
                                    <span class="badge bg-secondary">{{ $enrollment->section->room->room_number }}</span>
                                @else
                                    <span class="text-muted">TBA</span>
                                @endif
                            </td>
                            <td>
                                {{ $enrollment->section?->room?->building ?? '-' }}
                            </td>
                            <td>
                                {{ $enrollment->section?->instructor?->first_name ?? 'N/A' }} 
                                {{ $enrollment->section?->instructor?->last_name ?? '' }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $enrollment->section?->term?->term_code ?? 'N/A' }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center text-muted py-4">
                    <p>No courses available to display room information.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endif
@endsection

