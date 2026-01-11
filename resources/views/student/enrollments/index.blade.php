@extends('layouts.app')

@section('content')
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
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Section</th>
                                <th>Instructor</th>
                                <th>Term</th>
                                <th>Room</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($current as $enrollment)
                            <tr>
                                <td><strong>{{ $enrollment->section?->course?->course_code ?? 'N/A' }}</strong></td>
                                <td>{{ $enrollment->section?->course?->course_title ?? 'N/A' }}</td>
                                <td>{{ $enrollment->section?->section_code ?? 'N/A' }}</td>
                                <td>{{ $enrollment->section?->instructor?->first_name ?? 'N/A' }} {{ $enrollment->section?->instructor?->last_name ?? '' }}</td>
                                <td>{{ $enrollment->section?->term?->term_name ?? 'N/A' }}</td>
                                <td>{{ $enrollment->section?->room?->room_number ?? 'TBA' }}</td>
                                <td>
                                    <span class="badge bg-success">{{ $enrollment->status }}</span>
                                    @if($enrollment->letter_grade)
                                        <br><span class="badge bg-info mt-1">Grade: {{ $enrollment->letter_grade }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('student.courses.show', $enrollment->section->course->course_id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Section</th>
                                <th>Term</th>
                                <th>Grade</th>
                                <th>Credits</th>
                                <th>Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completed as $enrollment)
                            <tr>
                                <td><strong>{{ $enrollment->section?->course?->course_code ?? 'N/A' }}</strong></td>
                                <td>{{ $enrollment->section?->course?->course_title ?? 'N/A' }}</td>
                                <td>{{ $enrollment->section?->section_code ?? 'N/A' }}</td>
                                <td>{{ $enrollment->section?->term?->term_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $enrollment->letter_grade ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $enrollment->section?->course?->units ?? '0' }}</span>
                                </td>
                                <td>{{ $enrollment->date_enrolled->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- All Enrollments Tab -->
    <div class="tab-pane fade" id="all">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Section</th>
                            <th>Term</th>
                            <th>Status</th>
                            <th>Grade</th>
                            <th>Credits</th>
                            <th>Date Enrolled</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                        <tr>
                            <td><strong>{{ $enrollment->section->course->course_code ?? 'N/A' }}</strong></td>
                            <td>{{ $enrollment->section->course->course_title ?? 'N/A' }}</td>
                            <td>{{ $enrollment->section->section_code ?? 'N/A' }}</td>
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
                            <td>
                                @if($enrollment->letter_grade)
                                    <span class="badge bg-info">{{ $enrollment->letter_grade }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $enrollment->section->course->units ?? '0' }}</span>
                            </td>
                            <td>{{ $enrollment->date_enrolled->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No enrollments found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endif

@endsection
