@extends('layouts.app')
@section('title', 'Faculty Schedule')
@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
    <h1 style="margin: 0; font-size: 1.5em;">Class Schedule</h1>
</div>

@if($sections->isEmpty())
    <div style="background-color: #e2e3e5; border: 1px solid #d3d3d3; color: #383d41; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
        <strong>No sections assigned.</strong> Your class schedule will appear here once sections are assigned.
    </div>
@else
    <div style="background: white; border-radius: 4px; box-shadow: 0 1px 0 rgba(0,0,0,0.03); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f8f9fa; border-bottom: 2px solid #c4b59f;">
                    <th style="padding: 12px; text-align: left; font-weight: 600; border-left: 1px solid #c4b59f;">Course Code</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600; border-left: 1px solid #c4b59f;">Course Title</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600; border-left: 1px solid #c4b59f;">Section</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600; border-left: 1px solid #c4b59f;">Term</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600; border-left: 1px solid #c4b59f;">Schedule</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600; border-left: 1px solid #c4b59f;">Room</th>
                    <th style="padding: 12px; text-align: center; font-weight: 600; border-left: 1px solid #c4b59f;">Students</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sections as $section)
                <tr style="border-bottom: 1px solid #e8e1d6;">
                    <td style="padding: 12px; border-left: 1px solid #c4b59f;">
                        <strong style="color: #7a6a4f;">{{ $section->course->course_code ?? 'N/A' }}</strong>
                    </td>
                    <td style="padding: 12px; border-left: 1px solid #c4b59f;">
                        {{ $section->course->course_title ?? 'N/A' }}
                    </td>
                    <td style="padding: 12px; border-left: 1px solid #c4b59f;">
                        {{ $section->section_code ?? 'N/A' }}
                    </td>
                    <td style="padding: 12px; border-left: 1px solid #c4b59f;">
                        {{ $section->term->term_name ?? 'N/A' }}
                    </td>
                    <td style="padding: 12px; border-left: 1px solid #c4b59f; font-size: 0.9em;">
                        <small style="color: #666;">
                            @if($section->schedule_days)
                                {{ $section->schedule_days }}<br>{{ $section->schedule_time ?? '' }}
                            @else
                                TBD
                            @endif
                        </small>
                    </td>
                    <td style="padding: 12px; border-left: 1px solid #c4b59f;">
                        {{ $section->room->room_code ?? 'TBA' }}
                    </td>
                    <td style="padding: 12px; border-left: 1px solid #c4b59f; text-align: center;">
                        <span style="background: #e8e1d6; padding: 4px 8px; border-radius: 4px; font-size: 0.9em;">
                            {{ $section->enrollments->where('status', '!=', 'DROPPED')->count() }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
