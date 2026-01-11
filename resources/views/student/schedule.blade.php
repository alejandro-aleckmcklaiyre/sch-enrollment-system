@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">My Schedule</h1>
    @if($currentTerm)
    <span class="badge bg-info">{{ $currentTerm->term_name }}</span>
    @endif
</div>

@if($enrollments->isEmpty())
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> You have no classes scheduled for the current term.
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
                    <th>Room</th>
                    <th>Term</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollments as $enrollment)
                <tr>
                    <td>
                        <strong>{{ $enrollment->section->course->course_code ?? 'N/A' }}</strong>
                    </td>
                    <td>{{ $enrollment->section->course->course_title ?? 'N/A' }}</td>
                    <td>{{ $enrollment->section->section_code ?? 'N/A' }}</td>
                    <td>
                        @if($enrollment->section->instructor)
                            {{ $enrollment->section->instructor->first_name ?? 'N/A' }} 
                            {{ $enrollment->section->instructor->last_name ?? '' }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        {{ $enrollment->section?->room?->room_number ?? 'TBA' }}
                        @if($enrollment->section?->room?->building)
                            <br><small class="text-muted">({{ $enrollment->section?->room?->building }})</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-info">{{ $enrollment->section->term->term_name ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <a href="{{ route('student.courses.show', $enrollment->section->course->course_id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> View Course
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endif

@endsection
