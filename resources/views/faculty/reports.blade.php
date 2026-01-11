@extends('layouts.app')
@section('title', 'Faculty Reports')
@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
    <h1 style="margin: 0; font-size: 1.5em;">Reports</h1>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <!-- Class Roster Report -->
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 0 rgba(0,0,0,0.03); border-left: 4px solid #7a6a4f;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
            <div style="width: 40px; height: 40px; background: #e8e1d6; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-list" style="color: #7a6a4f; font-size: 1.2em;"></i>
            </div>
            <h3 style="margin: 0; font-size: 1.1em; color: #2e2a26;">Class Roster</h3>
        </div>
        <p style="margin: 8px 0; color: #666; font-size: 0.95em;">Generate a list of all students enrolled in your sections.</p>
        <a href="#" style="display: inline-block; background: #7a6a4f; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 0.9em; margin-top: 12px; cursor: pointer;">Generate Report</a>
    </div>

    <!-- Grade Report -->
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 0 rgba(0,0,0,0.03); border-left: 4px solid #8a8073;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
            <div style="width: 40px; height: 40px; background: #e8e1d6; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-bar" style="color: #8a8073; font-size: 1.2em;"></i>
            </div>
            <h3 style="margin: 0; font-size: 1.1em; color: #2e2a26;">Grade Report</h3>
        </div>
        <p style="margin: 8px 0; color: #666; font-size: 0.95em;">View grades summary and statistics for your sections.</p>
        <a href="#" style="display: inline-block; background: #8a8073; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 0.9em; margin-top: 12px; cursor: pointer;">Generate Report</a>
    </div>

    <!-- Attendance Report -->
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 0 rgba(0,0,0,0.03); border-left: 4px solid #9a9083;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
            <div style="width: 40px; height: 40px; background: #e8e1d6; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-calendar-check" style="color: #9a9083; font-size: 1.2em;"></i>
            </div>
            <h3 style="margin: 0; font-size: 1.1em; color: #2e2a26;">Attendance Report</h3>
        </div>
        <p style="margin: 8px 0; color: #666; font-size: 0.95em;">Track attendance and participation of your students.</p>
        <a href="#" style="display: inline-block; background: #9a9083; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 0.9em; margin-top: 12px; cursor: pointer;">Generate Report</a>
    </div>
</div>

@if($instructor)
<div style="background: #f5f0ea; border: 1px solid #c4b59f; padding: 16px; border-radius: 4px;">
    <h3 style="margin: 0 0 12px 0; color: #2e2a26;">Quick Info</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
        <div>
            <strong style="color: #7a6a4f;">Name:</strong> {{ $instructor->first_name ?? 'N/A' }} {{ $instructor->last_name ?? '' }}
        </div>
        <div>
            <strong style="color: #7a6a4f;">Email:</strong> {{ $instructor->email ?? 'N/A' }}
        </div>
        <div>
            <strong style="color: #7a6a4f;">Department:</strong> {{ $instructor->department->dept_name ?? 'Not assigned' }}
        </div>
    </div>
</div>
@else
<div style="background-color: #e2e3e5; border: 1px solid #d3d3d3; color: #383d41; padding: 12px; border-radius: 4px;">
    <strong>Note:</strong> Complete your profile information to see additional report details and statistics.
</div>
@endif
@endsection
