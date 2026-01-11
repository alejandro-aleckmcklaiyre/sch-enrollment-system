@extends('layouts.app')
@section('title', 'Faculty Courses')
@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
    <h1 style="margin: 0; font-size: 1.5em;">My Courses</h1>
</div>

@if($courses->isEmpty())
    <div style="background-color: #e2e3e5; border: 1px solid #d3d3d3; color: #383d41; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
        <strong>No courses assigned.</strong> Your courses will appear here once sections are assigned.
    </div>
@else
    <div style="background: white; border-radius: 4px; box-shadow: 0 1px 0 rgba(0,0,0,0.03); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f8f9fa; border-bottom: 2px solid #c4b59f;">
                    <th style="padding: 12px; text-align: left; font-weight: 600; border-left: 1px solid #c4b59f;">Course Code</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600; border-left: 1px solid #c4b59f;">Course Title</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600; border-left: 1px solid #c4b59f;">Description</th>
                    <th style="padding: 12px; text-align: center; font-weight: 600; border-left: 1px solid #c4b59f;">Units</th>
                    <th style="padding: 12px; text-align: center; font-weight: 600; border-left: 1px solid #c4b59f;">Hours</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                <tr style="border-bottom: 1px solid #e8e1d6;">
                    <td style="padding: 12px; border-left: 1px solid #c4b59f;">
                        <strong style="color: #7a6a4f;">{{ $course->course_code ?? 'N/A' }}</strong>
                    </td>
                    <td style="padding: 12px; border-left: 1px solid #c4b59f;">
                        {{ $course->course_title ?? 'N/A' }}
                    </td>
                    <td style="padding: 12px; border-left: 1px solid #c4b59f; font-size: 0.9em;">
                        <small style="color: #666;">
                            {{ Str::limit($course->course_description ?? 'N/A', 50) }}
                        </small>
                    </td>
                    <td style="padding: 12px; border-left: 1px solid #c4b59f; text-align: center;">
                        {{ $course->units ?? 'N/A' }}
                    </td>
                    <td style="padding: 12px; border-left: 1px solid #c4b59f; text-align: center;">
                        {{ $course->hours ?? 'N/A' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
