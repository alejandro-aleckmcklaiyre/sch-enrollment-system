@extends('layouts.app')

@section('title','Students')

@section('toolbar')
    <div class="toolbar">
        <button onclick="openModal('createModal')">Manage</button>
        <form method="GET" action="{{ url('students') }}" style="display:flex; gap:8px; align-items:center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search...">
            <select name="year_level">
                <option value="">All Years</option>
                @for($i=1;$i<=5;$i++)
                    <option value="{{ $i }}" {{ request('year_level') == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            <button type="submit" class="btn-secondary">Filter</button>
        </form>
        <form method="POST" action="{{ url('students/export-excel') }}" style="display:inline">
            @csrf
            <input type="hidden" name="filtered" value="1">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="year_level" value="{{ request('year_level') }}">
            <button>Export CSV</button>
        </form>
        <form method="GET" action="{{ url('students/export-pdf') }}" style="display:inline">
            <input type="hidden" name="filtered" value="1">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="year_level" value="{{ request('year_level') }}">
            <button type="submit" class="btn-secondary">Export PDF</button>
        </form>
    </div>
@endsection

@section('content')
    <table>
        <thead>
            <tr>
                <th>Student No</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Birthdate</th>
                <th>Year</th>
                <th>Program</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $s)
                <tr>
                    <td>{{ $s->student_no }}</td>
                    <td>{{ $s->last_name }}</td>
                    <td>{{ $s->first_name }}</td>
                    <td>{{ $s->middle_name }}</td>
                    <td>{{ $s->email }}</td>
                    <td>{{ $s->gender }}</td>
                    <td>{{ $s->birthdate }}</td>
                    <td>{{ $s->year_level }}</td>
                    <td>{{ optional($s->program)->program_name }}</td>
                    <td style="display:flex; gap:6px; justify-content:flex-start; align-items:center; min-width:140px;">
                        <button style="min-width:64px; padding:8px 10px;" onclick="openEdit({{ $s->student_id }}, {{ json_encode($s) }})">Edit</button>
                        <button style="min-width:64px; padding:8px 10px;" onclick="openDelete({{ $s->student_id }})" class="btn-secondary">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:12px">@include('partials.pagination', ['paginator' => $students])</div>

@endsection

@push('modals')
    @include('students.partials.modals')
@endpush

@push('scripts')
<script>
    function openEdit(id, data){
        const modal = document.getElementById('editModal');
        modal.style.display='flex';
        // populate fields
        modal.querySelector('[name="student_id"]').value = data.student_id;
        modal.querySelector('[name="student_no"]').value = data.student_no || '';
        modal.querySelector('[name="last_name"]').value = data.last_name || '';
        modal.querySelector('[name="first_name"]').value = data.first_name || '';
        modal.querySelector('[name="middle_name"]').value = data.middle_name || '';
        modal.querySelector('[name="email"]').value = data.email || '';
        modal.querySelector('[name="gender"]').value = data.gender || '';
        modal.querySelector('[name="birthdate"]').value = data.birthdate || '';
    modal.querySelector('[name="year_level"]').value = data.year_level || '';
    const progSelect = modal.querySelector('[name="program_id"]');
    if(progSelect){ progSelect.value = data.program_id || ''; }
    }

    function openDelete(id){
        const modal = document.getElementById('deleteModal');
        modal.style.display='flex';
        modal.querySelector('[name="student_id"]').value = id;
    }

    document.getElementById('createForm').addEventListener('submit', function(e){
        e.preventDefault();
        const form = e.target;
        fetch(form.action, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: new FormData(form)})
        .then(r=>r.json()).then(resp=>handleResponse(resp,'createModal'));
    });

    document.getElementById('editForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.student_id.value;
        fetch('/students/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'PUT'}, body: new FormData(e.target)})
        .then(r=>r.json()).then(resp=>handleResponse(resp,'editModal'));
    });

    document.getElementById('deleteForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.student_id.value;
        fetch('/students/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}})
        .then(r=>r.json()).then(resp=>handleResponse(resp,'deleteModal'));
    });
</script>
@endpush
