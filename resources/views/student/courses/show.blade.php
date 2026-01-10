@extends('layouts.app')

@section('page-content')
<div class="mb-4">
    <a href="{{ route('student.courses') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Catalog
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h1 class="h3 mb-0">{{ $course->course_code ?? 'N/A' }} - {{ $course->course_title ?? 'Course' }}</h1>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-2"><strong>Department:</strong> {{ $course->department->dept_name ?? 'N/A' }}</p>
                <p class="mb-2"><strong>Credit Units:</strong> {{ $course->credit_units ?? '0' }}</p>
                <p class="mb-2"><strong>Course Level:</strong> {{ $course->course_level ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                @if($isEnrolled)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> You are already enrolled in this course.
                    </div>
                @endif
            </div>
        </div>
        
        @if($course->course_description)
        <div class="mt-3">
            <h5>Course Description</h5>
            <p>{{ $course->course_description }}</p>
        </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Available Sections</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Section</th>
                    <th>Instructor</th>
                    <th>Term</th>
                    <th>Room</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sections as $section)
                <tr>
                    <td>{{ $section->section_code ?? 'Section ' . $section->section_id }}</td>
                    <td>
                        {{ $section->instructor->first_name ?? 'N/A' }} 
                        {{ $section->instructor->last_name ?? '' }}
                    </td>
                    <td>{{ $section->term->term_name ?? 'N/A' }}</td>
                    <td>
                        {{ $section->room?->room_number ?? 'TBA' }}
                        @if($section->day_pattern)
                            <br><small class="text-muted">{{ $section->day_pattern }} {{ $section->start_time }} - {{ $section->end_time }}</small>
                        @endif
                    </td>
                    <td>
                        @if(!$isEnrolled)
                        <form action="{{ route('student.courses.enroll', $course->course_id) }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="section_id" value="{{ $section->section_id }}">
                            <button type="submit" class="btn btn-sm btn-primary" title="Enroll in this section">
                                <i class="fas fa-plus"></i> Enroll
                            </button>
                        </form>
                        @else
                        <span class="badge bg-success">Enrolled</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        No sections available for this course
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
