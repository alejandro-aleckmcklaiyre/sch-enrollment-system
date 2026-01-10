@extends('layouts.app')

@section('page-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Course Catalog</h1>
    @if($currentTerm)
    <span class="badge bg-info">{{ $currentTerm->term_name }}</span>
    @endif
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('student.courses') }}" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search by course code or title" value="{{ request('search') }}">
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

@if($courses->isEmpty())
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> No courses available.
</div>
@else

<div class="row">
    @foreach($courses as $course)
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ $course->course_code ?? 'N/A' }}</h5>
                <small class="text-muted">{{ $course->department->dept_name ?? 'N/A' }}</small>
            </div>
            <div class="card-body">
                <p class="card-text">{{ $course->course_title ?? 'Course' }}</p>
                <p class="text-muted small">
                    <i class="fas fa-book"></i> {{ $course->units ?? '0' }} credits
                </p>
                <p class="text-muted small">
                    {{ strlen($course->course_description ?? '') > 100 ? substr($course->course_description, 0, 100) . '...' : $course->course_description }}
                </p>
            </div>
            <div class="card-footer bg-white">
                @if(in_array($course->course_id, $enrolledCourseIds))
                    <span class="badge bg-success">Enrolled</span>
                @else
                    <a href="{{ route('student.courses.show', $course->course_id) }}" class="btn btn-sm btn-primary">
                        View Details
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $courses->links() }}
</div>

@endif

@endsection
