@extends('layouts.app')

@section('title', 'Faculty Dashboard')

@section('content')

<!-- Welcome & Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 0 rgba(0,0,0,0.03);">
        <div style="text-align: center;">
            <div style="font-size: 2.5em; font-weight: bold; color: #7a6a4f;">{{ $totalSections ?? 0 }}</div>
            <div style="font-size: 0.9em; color: #8a8073; margin-top: 8px;">Assigned Sections</div>
        </div>
    </div>
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 0 rgba(0,0,0,0.03);">
        <div style="text-align: center;">
            <div style="font-size: 2.5em; font-weight: bold; color: #7a6a4f;">{{ $totalStudents ?? 0 }}</div>
            <div style="font-size: 0.9em; color: #8a8073; margin-top: 8px;">Total Students</div>
        </div>
    </div>
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 0 rgba(0,0,0,0.03);">
        <div style="text-align: center;">
            <div style="font-size: 2.5em; font-weight: bold; color: #7a6a4f;">{{ $totalEnrollments ?? 0 }}</div>
            <div style="font-size: 0.9em; color: #8a8073; margin-top: 8px;">Total Enrollments</div>
        </div>
    </div>
</div>

<!-- Welcome Message -->
<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 0 rgba(0,0,0,0.03); margin-bottom: 24px;">
    @if($instructor)
        <h2 style="margin: 0 0 8px 0; font-size: 1.2em; color: #2e2a26;">Welcome, {{ $instructor->first_name }} {{ $instructor->last_name }}</h2>
        <p style="margin: 0; color: #666; font-size: 0.95em;">
            <strong>Department:</strong> {{ $instructor->department->dept_name ?? 'N/A' }} | 
            <strong>Email:</strong> {{ $instructor->email }}
        </p>
    @else
        <h2 style="margin: 0 0 8px 0; font-size: 1.2em; color: #2e2a26;">Welcome to Faculty Portal</h2>
        <p style="margin: 0; color: #666; font-size: 0.95em;">
            You can start managing your sections and courses. Your profile information can be updated anytime.
        </p>
    @endif
</div>

<!-- My Assigned Sections -->
    <div style="background: white; padding: 0; border-radius: 8px; box-shadow: 0 1px 0 rgba(0,0,0,0.03); margin-bottom: 24px;">
        <div style="padding: 16px; border-bottom: 1px solid #c4b59f;">
            <h3 style="margin: 0; font-size: 1.1em; color: #2e2a26;">My Assigned Sections</h3>
        </div>
        
        @if($sectionsWithStats->isEmpty())
            <div style="padding: 40px; text-align: center; color: #8a8073;">
                <p style="margin: 0;">No sections assigned</p>
            </div>
        @else
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #c4b59f;">
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Course</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Section</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Term</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Schedule</th>
                        <th style="padding: 12px; text-align: center; font-weight: 600;">Room</th>
                        <th style="padding: 12px; text-align: center; font-weight: 600;">Enrolled</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sectionsWithStats as $section)
                    <tr style="border-bottom: 1px solid #c4b59f;">
                        <td style="padding: 12px; border-left: 1px solid #c4b59f;">
                            <strong>{{ $section['course_code'] }}</strong><br>
                            <small style="color: #666;">{{ $section['course_title'] }}</small>
                        </td>
                        <td style="padding: 12px; border-left: 1px solid #c4b59f;">{{ $section['section_code'] }}</td>
                        <td style="padding: 12px; border-left: 1px solid #c4b59f;">{{ $section['term'] }}</td>
                        <td style="padding: 12px; border-left: 1px solid #c4b59f;">
                            <small>{{ $section['schedule_days'] ?? 'TBD' }}<br>{{ $section['schedule_time'] ?? '' }}</small>
                        </td>
                        <td style="padding: 12px; border-left: 1px solid #c4b59f; text-align: center;">{{ $section['room'] }}</td>
                        <td style="padding: 12px; border-left: 1px solid #c4b59f; text-align: center;">
                            <strong>{{ $section['enrolled_count'] }}</strong> / {{ $section['total_count'] }}
                        </td>
                        <td style="padding: 12px; border-left: 1px solid #c4b59f; display: flex; gap: 6px;">
                            <a href="#" style="min-width: 70px; padding: 6px 10px; background: #7a6a4f; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em; text-align: center; cursor: pointer;">Grades</a>
                            <a href="#" style="min-width: 70px; padding: 6px 10px; background: #8a8073; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em; text-align: center; cursor: pointer;">Students</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Recent Enrollments Activity -->
    @if($recentEnrollments->isNotEmpty())
    <div style="background: white; padding: 0; border-radius: 8px; box-shadow: 0 1px 0 rgba(0,0,0,0.03);">
        <div style="padding: 16px; border-bottom: 1px solid #c4b59f;">
            <h3 style="margin: 0; font-size: 1.1em; color: #2e2a26;">Recent Activity</h3>
        </div>
        
        <div style="padding: 16px;">
            @foreach($recentEnrollments->take(5) as $enrollment)
            <div style="padding: 12px 0; border-bottom: 1px solid #e8e1d6; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</strong>
                    <small style="display: block; color: #666;">{{ $enrollment->section->course->course_code ?? 'N/A' }} - {{ $enrollment->section->course->course_title ?? 'N/A' }}</small>
                </div>
                <div style="text-align: right;">
                    <span style="background: #e8e1d6; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; color: #7a6a4f;">
                        {{ $enrollment->status ?? 'ENROLLED' }}
                    </span>
                    <small style="display: block; color: #999; margin-top: 4px;">{{ $enrollment->date_enrolled->format('M d, Y') ?? 'N/A' }}</small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

@endsection
