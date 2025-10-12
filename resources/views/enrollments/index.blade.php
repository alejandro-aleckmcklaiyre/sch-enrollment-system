@extends('layouts.app')

@section('title','Enrollments')

@section('toolbar')
    <div class="toolbar">
        <button onclick="openModal('createEnrollmentModal')">Manage</button>
        <form method="GET" action="{{ url('enrollments') }}" style="display:flex; gap:8px; align-items:center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search...">
            <select name="status">
                <option value="">All Statuses</option>
                <option value="enrolled" {{ request('status')=='enrolled'? 'selected':'' }}>Enrolled</option>
                <option value="dropped" {{ request('status')=='dropped'? 'selected':'' }}>Dropped</option>
            </select>
            <button type="submit" class="btn-secondary">Filter</button>
        </form>
        <form method="POST" action="{{ url('enrollments/export-excel') }}" style="display:inline">
            @csrf
            <input type="hidden" name="filtered" value="1">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <button>Export CSV</button>
        </form>
        <form method="GET" action="{{ url('enrollments/export-pdf') }}" style="display:inline">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <button type="submit" class="btn-secondary">Export PDF</button>
        </form>
    </div>
@endsection

@section('content')
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Section</th>
                <th>Date Enrolled</th>
                <th>Status</th>
                <th>Grade</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enrollments as $e)
                <tr>
                    <td>{{ optional($e->student)->student_no }} - {{ optional($e->student)->last_name }}</td>
                    <td>{{ $e->section_id }}</td>
                    <td>{{ $e->date_enrolled }}</td>
                    <td>{{ $e->status }}</td>
                    <td>{{ $e->letter_grade }}</td>
                    <td style="display:flex; gap:8px; justify-content:flex-start; align-items:center;">
                        <button onclick="openEnrollmentEdit({{ $e->enrollment_id }}, {{ json_encode($e) }})">Edit</button>
                        <button onclick="openEnrollmentDelete({{ $e->enrollment_id }})" class="btn-secondary">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:12px">@include('partials.pagination', ['paginator' => $enrollments])</div>

@endsection

@push('modals')
    @include('enrollments.partials.modals')
@endpush

@push('scripts')
<script>
    function openEnrollmentEdit(id, data){
        const modal = document.getElementById('editEnrollmentModal');
        modal.style.display='flex';
        modal.querySelector('[name="id"]').value = data.enrollment_id;
        modal.querySelector('[name="student_id"]').value = data.student_id || '';
        modal.querySelector('[name="section_id"]').value = data.section_id || '';
        modal.querySelector('[name="date_enrolled"]').value = data.date_enrolled || '';
        modal.querySelector('[name="status"]').value = data.status || '';
        modal.querySelector('[name="letter_grade"]').value = data.letter_grade || '';
    }

    function openEnrollmentDelete(id){
        const modal = document.getElementById('deleteEnrollmentModal');
        modal.style.display='flex';
        modal.querySelector('[name="id"]').value = id;
    }

    document.getElementById('createForm').addEventListener('submit', function(e){
        e.preventDefault();
        const form = e.target;
        fetch(form.action || '/enrollments', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: new FormData(form)})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('createEnrollmentModal'); location.reload(); } else console.error(resp);});
    });

    document.getElementById('editForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.id.value;
        fetch('/enrollments/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'PUT'}, body: new FormData(e.target)})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('editEnrollmentModal'); location.reload(); } else console.error(resp);});
    });

    document.getElementById('deleteForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.id.value;
        fetch('/enrollments/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('deleteEnrollmentModal'); location.reload(); } else console.error(resp);});
    });
</script>
@endpush

