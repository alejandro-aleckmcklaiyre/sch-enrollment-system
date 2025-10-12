@extends('layouts.app')

@section('title','Instructors')

@section('toolbar')
    <div class="toolbar">
        <button onclick="openModal('createInstructorModal')">Manage</button>
        <form method="GET" action="{{ url('instructors') }}" style="display:flex; gap:8px; align-items:center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search...">
            <select name="dept_id">
                <option value="">All Departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->dept_id }}" {{ request('dept_id') == $d->dept_id ? 'selected' : '' }}>{{ $d->dept_name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary">Filter</button>
        </form>
        <form method="POST" action="{{ url('instructors/export-excel') }}" style="display:inline">
            @csrf
            <input type="hidden" name="filtered" value="1">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="dept_id" value="{{ request('dept_id') }}">
            <button>Export CSV</button>
        </form>
        <form method="GET" action="{{ url('instructors/export-pdf') }}" style="display:inline">
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
                <th>Last Name</th>
                <th>First Name</th>
                <th>Email</th>
                <th>Department</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($instructors as $i)
                <tr>
                    <td>{{ $i->last_name }}</td>
                    <td>{{ $i->first_name }}</td>
                    <td>{{ $i->email }}</td>
                    <td>{{ optional($i->department)->dept_name ?? $i->dept_id }}</td>
                    <td style="display:flex; gap:8px; justify-content:flex-start; align-items:center; min-width:160px;">
                        <button style="min-width:64px; padding:8px 10px; height:34px;" onclick="openInstructorEdit({{ $i->instructor_id }}, {{ json_encode($i) }})">Edit</button>
                        <button style="min-width:64px; padding:8px 10px; height:34px;" onclick="openInstructorDelete({{ $i->instructor_id }})" class="btn-secondary">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:12px">@include('partials.pagination', ['paginator' => $instructors])</div>

@endsection

@push('modals')
    @include('instructors.partials.modals')
@endpush

@push('scripts')
<script>
    function openInstructorEdit(id, data){
        const modal = document.getElementById('editInstructorModal');
        modal.style.display='flex';
        modal.querySelector('[name="instructor_id"]').value = data.instructor_id;
        modal.querySelector('[name="last_name"]').value = data.last_name || '';
        modal.querySelector('[name="first_name"]').value = data.first_name || '';
        modal.querySelector('[name="email"]').value = data.email || '';
        const deptSelect = modal.querySelector('[name="dept_id"]'); if(deptSelect) deptSelect.value = data.dept_id || '';
    }

    function openInstructorDelete(id){
        const modal = document.getElementById('deleteInstructorModal');
        modal.style.display='flex';
        modal.querySelector('[name="instructor_id"]').value = id;
    }

    document.getElementById('createInstructorForm').addEventListener('submit', function(e){
        e.preventDefault();
        const form = e.target;
        fetch(form.action, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: new FormData(form)})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('createInstructorModal'); location.reload(); } else console.error(resp);});
    });

    document.getElementById('editInstructorForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.instructor_id.value;
        fetch('/instructors/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'PUT'}, body: new FormData(e.target)})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('editInstructorModal'); location.reload(); } else console.error(resp);});
    });

    document.getElementById('deleteInstructorForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.instructor_id.value;
        fetch('/instructors/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('deleteInstructorModal'); location.reload(); } else console.error(resp);});
    });
</script>
@endpush
