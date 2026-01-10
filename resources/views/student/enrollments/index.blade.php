@extends('layouts.app')

@section('page-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">My Enrollments</h1>
    <a href="{{ route('student.courses') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Browse Courses
    </a>
</div>

@if($enrollments->isEmpty())
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> You are not currently enrolled in any courses. 
    <a href="{{ route('student.courses') }}">Browse the course catalog</a> to enroll.
</div>
@else

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active" href="#current" data-bs-toggle="tab">Current Enrollments</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#completed" data-bs-toggle="tab">Completed</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#all" data-bs-toggle="tab">All</a>
    </li>
</ul>

<div class="tab-content">
    <!-- Current Enrollments Tab -->
    <div class="tab-pane fade show active" id="current">
        @php
            $current = collect($enrollments)->where('status', 'ENROLLED');
        @endphp
        
        @if($current->isEmpty())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> No current enrollments.
            </div>
        @else
            <div class="row">
                @foreach($current as $enrollment)
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                {{ $enrollment->section?->course?->course_code ?? 'N/A' }}
                                - {{ $enrollment->section?->course?->course_title ?? 'Course' }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Instructor:</strong> {{ $enrollment->section?->instructor?->first_name ?? 'N/A' }} {{ $enrollment->section?->instructor?->last_name ?? '' }}</p>
                            <p class="mb-2"><strong>Term:</strong> {{ $enrollment->section?->term?->term_name ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>Room:</strong> {{ $enrollment->section?->room?->room_number ?? 'TBA' }}</p>
                            <p class="mb-2"><strong>Enrolled:</strong> {{ $enrollment->date_enrolled->format('M d, Y') }}</p>
                            <p class="mb-0">
                                <span class="badge bg-success">{{ $enrollment->status }}</span>
                                @if($enrollment->letter_grade)
                                    <span class="badge bg-info">Grade: {{ $enrollment->letter_grade }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Completed Tab -->
    <div class="tab-pane fade" id="completed">
        @php
            $completed = collect($enrollments)->where('status', 'COMPLETED');
        @endphp
        
        @if($completed->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> You have not completed any courses yet.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Term</th>
                            <th>Grade</th>
                            <th>Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completed as $enrollment)
                        <tr>
                            <td><strong>{{ $enrollment->section?->course?->course_code ?? 'N/A' }}</strong></td>
                            <td>{{ $enrollment->section?->course?->course_title ?? 'N/A' }}</td>
                            <td>{{ $enrollment->section?->term?->term_name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $enrollment->letter_grade ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $enrollment->date_enrolled->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- All Enrollments Tab -->
    <div class="tab-pane fade" id="all">
        @php
            $dropped = $enrollments->where('status', 'DROPPED');
        @endphp
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Term</th>
                        <th>Status</th>
                        <th>Grade</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $enrollment)
                    <tr>
                        <td><strong>{{ $enrollment->section->course->course_code ?? 'N/A' }}</strong></td>
                        <td>{{ $enrollment->section->course->course_title ?? 'N/A' }}</td>
                        <td>{{ $enrollment->section->term->term_name ?? 'N/A' }}</td>
                        <td>
                            @if($enrollment->status === 'ENROLLED')
                                <span class="badge bg-success">{{ $enrollment->status }}</span>
                            @elseif($enrollment->status === 'COMPLETED')
                                <span class="badge bg-primary">{{ $enrollment->status }}</span>
                            @elseif($enrollment->status === 'DROPPED')
                                <span class="badge bg-danger">{{ $enrollment->status }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $enrollment->status }}</span>
                            @endif
                        </td>
                        <td>{{ $enrollment->letter_grade ?? '-' }}</td>
                        <td>{{ $enrollment->date_enrolled->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No enrollments found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endif

@endsection
