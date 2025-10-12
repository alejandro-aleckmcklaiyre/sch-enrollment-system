@extends('layouts.app')

@section('title','Departments')

@section('toolbar')
    <div class="toolbar">
        <button onclick="openModal('createDepartmentModal')">Manage</button>
        <form method="GET" action="{{ url('departments') }}" style="display:flex; gap:8px; align-items:center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search...">
            <button type="submit" class="btn-secondary">Filter</button>
        </form>
        <form method="POST" action="{{ url('departments/export-excel') }}" style="display:inline">
            @csrf
            <input type="hidden" name="filtered" value="1">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button>Export CSV</button>
        </form>
        <form method="GET" action="{{ url('departments/export-pdf') }}" style="display:inline">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button type="submit" class="btn-secondary">Export PDF</button>
        </form>
    </div>
@endsection

@section('content')
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departments as $d)
                <tr>
                    <td>{{ $d->dept_code }}</td>
                    <td>{{ $d->dept_name }}</td>
                    <td style="display:flex; gap:8px; justify-content:flex-start; align-items:center;">
                        <button onclick="openDepartmentEdit({{ $d->dept_id }}, {{ json_encode($d) }})">Edit</button>
                        <button onclick="openDepartmentDelete({{ $d->dept_id }})" class="btn-secondary">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:12px">@include('partials.pagination', ['paginator' => $departments])</div>

@endsection

@push('modals')
    @include('departments.partials.modals')
@endpush

@push('scripts')
<script>
    function openDepartmentEdit(id, data){
        const modal = document.getElementById('editDepartmentModal');
        modal.style.display='flex';
        modal.querySelector('[name="id"]').value = data.dept_id;
        modal.querySelector('[name="dept_code"]').value = data.dept_code || '';
        modal.querySelector('[name="dept_name"]').value = data.dept_name || '';
    }

    function openDepartmentDelete(id){
        const modal = document.getElementById('deleteDepartmentModal');
        modal.style.display='flex';
        modal.querySelector('[name="id"]').value = id;
    }

    document.getElementById('createForm').addEventListener('submit', function(e){
        e.preventDefault();
        const form = e.target;
        fetch(form.action || '/departments', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: new FormData(form)})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('createDepartmentModal'); location.reload(); } else console.error(resp);});
    });

    document.getElementById('editForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.id.value;
        fetch('/departments/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'PUT'}, body: new FormData(e.target)})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('editDepartmentModal'); location.reload(); } else console.error(resp);});
    });

    document.getElementById('deleteForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.id.value;
        fetch('/departments/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('deleteDepartmentModal'); location.reload(); } else console.error(resp);});
    });
</script>
@endpush

