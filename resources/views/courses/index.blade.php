@extends('layouts.app')

@section('title','Courses')

@section('toolbar')
    <div class="toolbar">
        <button onclick="openModal('createCourseModal')">Manage</button>
        <form method="GET" action="{{ url('courses') }}" style="display:flex; gap:8px; align-items:center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search...">
            <select name="dept_id">
                <option value="">All Departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->dept_id }}" {{ request('dept_id') == $d->dept_id ? 'selected' : '' }}>{{ $d->dept_name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary">Filter</button>
        </form>
        <form method="POST" action="{{ url('courses/export-excel') }}" style="display:inline">
            @csrf
            <input type="hidden" name="filtered" value="1">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="dept_id" value="{{ request('dept_id') }}">
            <button>Export CSV</button>
        </form>
        <form method="GET" action="{{ url('courses/export-pdf') }}" style="display:inline">
            <input type="hidden" name="filtered" value="1">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="dept_id" value="{{ request('dept_id') }}">
            <button type="submit" class="btn-secondary">Export PDF</button>
        </form>
    </div>
@endsection

@section('content')
    <table>
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Units</th>
                <th>Lecture Hrs</th>
                <th>Lab Hrs</th>
                <th>Department</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses as $c)
                <tr>
                    <td>{{ $c->course_code }}</td>
                    <td>{{ $c->course_title }}</td>
                    <td>{{ $c->units }}</td>
                    <td>{{ $c->lecture_hours }}</td>
                    <td>{{ $c->lab_hours }}</td>
                    <td>{{ optional($c->department)->dept_name ?? $c->dept_id }}</td>
                    <td style="display:flex; gap:6px; justify-content:flex-start; align-items:center; min-width:140px;">
                        <button style="min-width:64px; padding:8px 10px;" onclick="openCourseEdit({{ $c->course_id }}, {{ json_encode($c) }})">Edit</button>
                        <button style="min-width:64px; padding:8px 10px;" onclick="openCourseDelete({{ $c->course_id }})" class="btn-secondary">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:12px;">@include('partials.pagination', ['paginator' => $courses])</div>

@endsection

@push('modals')
    @include('courses.partials.modals')
@endpush

@push('scripts')
<script>
    function openCourseEdit(id, data){
        const modal = document.getElementById('editCourseModal');
        modal.style.display='flex';
        modal.querySelector('[name="course_id"]').value = data.course_id;
        modal.querySelector('[name="course_code"]').value = data.course_code || '';
        modal.querySelector('[name="course_title"]').value = data.course_title || '';
        modal.querySelector('[name="units"]').value = data.units || '';
        modal.querySelector('[name="lecture_hours"]').value = data.lecture_hours || '';
        modal.querySelector('[name="lab_hours"]').value = data.lab_hours || '';
        const deptSelect = modal.querySelector('[name="dept_id"]'); if(deptSelect) deptSelect.value = data.dept_id || '';
    }

    function openCourseDelete(id){
        const modal = document.getElementById('deleteCourseModal');
        modal.style.display='flex';
        modal.querySelector('[name="course_id"]').value = id;
    }

    document.getElementById('createCourseForm').addEventListener('submit', function(e){
        e.preventDefault();
        const form = e.target;
        fetch(form.action, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: new FormData(form)})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('createCourseModal'); location.reload(); } else console.error(resp);});
    });

    document.getElementById('editCourseForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.course_id.value;
        fetch('/courses/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'PUT'}, body: new FormData(e.target)})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('editCourseModal'); location.reload(); } else console.error(resp);});
    });

    document.getElementById('deleteCourseForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.course_id.value;
        fetch('/courses/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('deleteCourseModal'); location.reload(); } else console.error(resp);});
    });
</script>
@endpush
