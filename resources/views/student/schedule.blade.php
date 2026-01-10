@extends('layouts.app')

@section('page-content')
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

<div class="row">
    @foreach($enrollments as $enrollment)
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    {{ $enrollment->section->course->course_code ?? 'N/A' }}
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong class="text-primary" style="font-size: 1.1em;">
                        {{ $enrollment->section->course->course_title ?? 'Course' }}
                    </strong>
                </p>
                <hr>
                <p class="mb-2">
                    <i class="fas fa-user"></i> <strong>Instructor:</strong><br>
                    {{ $enrollment->section->instructor->first_name ?? 'N/A' }} 
                    {{ $enrollment->section->instructor->last_name ?? '' }}
                </p>
                <p class="mb-2">
                    <i class="fas fa-map-marker-alt"></i> <strong>Room:</strong><br>
                    {{ $enrollment->section?->room?->room_number ?? 'TBA' }}
                    @if($enrollment->section?->room?->building)
                        ({{ $enrollment->section?->room?->building }})
                    @endif
                </p>
                <p class="mb-2">
                    <i class="fas fa-calendar"></i> <strong>Schedule:</strong><br>
                    <small>Contact instructor for specific meeting times</small>
                </p>
                <p class="mb-0">
                    <i class="fas fa-book"></i> <strong>Credits:</strong> 
                    {{ $enrollment->section->course->credit_units ?? '0' }}
                </p>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endif

@endsection
