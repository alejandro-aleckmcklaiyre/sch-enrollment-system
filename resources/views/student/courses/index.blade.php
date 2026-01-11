@extends('layouts.app')

@section('content')
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

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Department</th>
                    <th>Credits</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                <tr>
                    <td>
                        <strong>{{ $course->course_code ?? 'N/A' }}</strong>
                    </td>
                    <td>{{ $course->course_title ?? 'N/A' }}</td>
                    <td>{{ $course->department->dept_name ?? 'N/A' }}</td>
                    <td>
                        <span class="badge bg-secondary">{{ $course->units ?? '0' }}</span>
                    </td>
                    <td>
                        <small>{{ strlen($course->course_description ?? '') > 60 ? substr($course->course_description, 0, 60) . '...' : $course->course_description }}</small>
                    </td>
                    <td>
                        @if(in_array($course->course_id, $enrolledCourseIds))
                            <span class="badge bg-success"><i class="fas fa-check"></i> Enrolled</span>
                        @else
                            <span class="badge bg-warning text-dark">Available</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('student.courses.show', $course->course_id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $courses->links() }}
</div>

@endif

@endsection
