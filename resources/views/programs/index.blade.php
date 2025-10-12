@extends('layouts.app')

@section('title','Programs')

@section('toolbar')
    <div class="toolbar">
        <button onclick="openModal('createProgramModal')">Manage</button>
        <form method="GET" action="{{ url('programs') }}" style="display:flex; gap:8px; align-items:center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search...">
            <button type="submit" class="btn-secondary">Filter</button>
        </form>
        <form method="POST" action="{{ url('programs/export-excel') }}" style="display:inline">
            @csrf
            <input type="hidden" name="filtered" value="1">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button>Export CSV</button>
        </form>
        <form method="GET" action="{{ url('programs/export-pdf') }}" style="display:inline">
            <input type="hidden" name="filtered" value="1">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button type="submit" class="btn-secondary">Export PDF</button>
        </form>
    </div>
@endsection

@section('content')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Program Code</th>
                <th>Program Name</th>
                <th>Department</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($programs as $p)
                <tr>
                    <td>{{ $p->program_id }}</td>
                    <td>{{ $p->program_code }}</td>
                    <td>{{ $p->program_name }}</td>
                    <td>{{ optional($p->department)->dept_name ?? $p->dept_id }}</td>
                    <td style="display:flex; gap:8px; justify-content:flex-start; align-items:center;">
                        <button onclick="openProgramEdit({{ $p->program_id }}, {{ json_encode($p) }})">Edit</button>
                        <button onclick="openProgramDelete({{ $p->program_id }})" class="btn-secondary">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:12px">@include('partials.pagination', ['paginator' => $programs])</div>

@endsection

@push('modals')
    @include('programs.partials.modals')
@endpush

@push('scripts')
<script>
    function openProgramEdit(id, data){
        const modal = document.getElementById('editProgramModal');
        modal.style.display='flex';
        modal.querySelector('[name="program_id"]').value = data.program_id;
    modal.querySelector('[name="program_code"]').value = data.program_code || '';
    modal.querySelector('[name="program_name"]').value = data.program_name || '';
    // set the dept select
    const deptSelect = modal.querySelector('[name="dept_id"]');
    if(deptSelect){ deptSelect.value = data.dept_id || ''; }
    }

    function openProgramDelete(id){
        const modal = document.getElementById('deleteProgramModal');
        modal.style.display='flex';
        modal.querySelector('[name="program_id"]').value = id;
    }

    document.getElementById('createProgramForm').addEventListener('submit', function(e){
        e.preventDefault();
        const form = e.target;
        fetch(form.action, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: new FormData(form)})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('createProgramModal'); location.reload(); } else console.error(resp);});
    });

    document.getElementById('editProgramForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.program_id.value;
        fetch('/programs/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'PUT'}, body: new FormData(e.target)})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('editProgramModal'); location.reload(); } else console.error(resp);});
    });

    document.getElementById('deleteProgramForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.program_id.value;
        fetch('/programs/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('deleteProgramModal'); location.reload(); } else console.error(resp);});
    });
</script>
@endpush
