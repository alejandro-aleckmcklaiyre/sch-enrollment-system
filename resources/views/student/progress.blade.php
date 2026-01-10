@extends('layouts.app')

@section('page-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Academic Progress</h1>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">Total Completed</h6>
                <h3 class="text-success">{{ $completed }}/{{ $totalEnrolled }}</h3>
                <small class="text-muted">courses</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">Currently Enrolled</h6>
                <h3 class="text-warning">{{ $enrolled }}</h3>
                <small class="text-muted">courses</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">Progress</h6>
                <h3 class="text-primary">{{ $progressPercentage }}%</h3>
                <small class="text-muted">of total enrolled</small>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Progress Overview</h5>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-2">
                <strong>Completion Rate</strong>
                <span>{{ $progressPercentage }}%</span>
            </div>
            <div class="progress" style="height: 25px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercentage }}%;" 
                     aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100">
                    {{ $progressPercentage }}%
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h6>Status Breakdown</h6>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i> 
                            <strong>Completed:</strong> {{ $completed }} courses
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-hourglass-half text-warning"></i> 
                            <strong>In Progress:</strong> {{ $enrolled }} courses
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-times-circle text-danger"></i> 
                            <strong>Dropped:</strong> {{ $dropped }} courses
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@if($currentTermEnrollments->isNotEmpty())
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Current Term Courses</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Instructor</th>
                    <th>Status</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach($currentTermEnrollments as $enrollment)
                <tr>
                    <td><strong>{{ $enrollment->section->course->course_code ?? 'N/A' }}</strong></td>
                    <td>{{ $enrollment->section->course->course_title ?? 'N/A' }}</td>
                    <td>{{ $enrollment->section->instructor->first_name ?? 'N/A' }} {{ $enrollment->section->instructor->last_name ?? '' }}</td>
                    <td>
                        @if($enrollment->status === 'ENROLLED')
                            <span class="badge bg-warning">{{ $enrollment->status }}</span>
                        @elseif($enrollment->status === 'COMPLETED')
                            <span class="badge bg-success">{{ $enrollment->status }}</span>
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
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
