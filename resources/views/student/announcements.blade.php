@extends('layouts.app')

@section('page-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Announcements</h1>
</div>

@if($announcements->isEmpty())
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> No announcements at this time.
</div>
@else

@foreach($announcements as $announcement)
<div class="card mb-3">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5 class="card-title mb-1">{{ $announcement['title'] }}</h5>
                <small class="text-muted">
                    <i class="fas fa-calendar"></i> {{ $announcement['date']->format('M d, Y') }}
                    @if($announcement['type'] === 'system')
                        <span class="badge bg-primary ms-2">System</span>
                    @elseif($announcement['type'] === 'academic')
                        <span class="badge bg-info ms-2">Academic</span>
                    @else
                        <span class="badge bg-secondary ms-2">{{ ucfirst($announcement['type']) }}</span>
                    @endif
                </small>
            </div>
        </div>
    </div>
    <div class="card-body">
        <p class="card-text">{{ $announcement['body'] }}</p>
    </div>
</div>
@endforeach

@endif

@endsection
