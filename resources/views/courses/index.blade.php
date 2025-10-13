@extends('layouts.app')

@section('title','Courses')

@section('toolbar')
    @include('partials.table-toolbar', [
        'listUrl' => url('courses'),
        'exportExcelUrl' => url('courses/export-excel'),
        'exportPdfUrl' => url('courses/export-pdf'),
        'manageModalId' => 'createCourseModal',
        'filterModalId' => 'filterCourseModal'
    ])
@endsection

@section('content')
    @include('partials.table-controls', [
        'listUrl' => url('courses'),
        'sortFields' => [
            ['value' => 'course_id', 'label' => 'ID'],
            ['value' => 'course_code', 'label' => 'Course Code'],
            ['value' => 'course_title', 'label' => 'Course Title'],
        ],
        'filterModalId' => 'filterCourseModal'
    ])

    <table>
        <thead>
            <tr>
                <th>@include('partials._sortable_header', ['label'=>'ID','field'=>'course_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Course Code','field'=>'course_code'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Course Title','field'=>'course_title'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Units','field'=>'units'])</th>
                <th>Lecture Hrs</th>
                <th>Lab Hrs</th>
                <th>@include('partials._sortable_header', ['label'=>'Department','field'=>'dept_id'])</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses as $c)
                <tr>
                    <td>{{ $c->course_id }}</td>
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
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'createCourseModal'); });
    });

    document.getElementById('editCourseForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.course_id.value;
        fetch('/courses/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'PUT'}, body: new FormData(e.target)})
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'editCourseModal'); });
    });

    document.getElementById('deleteCourseForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.course_id.value;
        fetch('/courses/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}})
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'deleteCourseModal'); });
    });
</script>
@endpush
