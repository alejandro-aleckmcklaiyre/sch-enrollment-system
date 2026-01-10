@extends('layouts.app')

@section('page-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">My Transcript</h1>
    <button class="btn btn-primary" onclick="window.print()">
        <i class="fas fa-print"></i> Print Transcript
    </button>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">Total Courses</h6>
                <h3 class="text-primary">{{ $completedCourses }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">Total Credits</h6>
                <h3 class="text-success">{{ $totalCredits }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">In Progress</h6>
                <h3 class="text-warning">{{ $enrollments->where('status', 'ENROLLED')->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">Dropped</h6>
                <h3 class="text-danger">{{ $enrollments->where('status', 'DROPPED')->count() }}</h3>
            </div>
        </div>
    </div>
</div>

@if($enrollments->isEmpty())
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> No enrollment records found.
</div>
@else

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Academic Record</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Term</th>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Credits</th>
                    <th>Grade</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollments->groupBy('section.term_id') as $termId => $termEnrollments)
                    @php
                        $term = $termEnrollments->first()->section->term;
                    @endphp
                    <tr class="table-active">
                        <td colspan="6">
                            <strong>{{ $term->term_name ?? 'Unknown Term' }}</strong>
                        </td>
                    </tr>
                    @foreach($termEnrollments as $enrollment)
                    <tr>
                        <td></td>
                        <td><strong>{{ $enrollment->section->course->course_code ?? 'N/A' }}</strong></td>
                        <td>{{ $enrollment->section->course->course_title ?? 'N/A' }}</td>
                        <td>{{ $enrollment->section->course->credit_units ?? '0' }}</td>
                        <td>
                            @if($enrollment->letter_grade)
                                <span class="badge bg-info">{{ $enrollment->letter_grade }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($enrollment->status === 'COMPLETED')
                                <span class="badge bg-success">{{ $enrollment->status }}</span>
                            @elseif($enrollment->status === 'ENROLLED')
                                <span class="badge bg-warning">{{ $enrollment->status }}</span>
                            @elseif($enrollment->status === 'DROPPED')
                                <span class="badge bg-danger">{{ $enrollment->status }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $enrollment->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endif

@endsection
