@extends('layouts.app')

@section('title','Departments')

@section('toolbar')
    @include('partials.table-toolbar', [
        'listUrl' => url('departments'),
        'exportExcelUrl' => url('departments/export-excel'),
        'exportPdfUrl' => url('departments/export-pdf'),
        'manageModalId' => 'createDepartmentModal',
        'filterModalId' => 'filterDepartmentModal'
    ])
@endsection

@section('content')
    @include('partials.table-controls', [
        'listUrl' => url('departments'),
        'sortFields' => [
            ['value' => 'dept_id', 'label' => 'ID'],
            ['value' => 'dept_code', 'label' => 'Dept Code'],
            ['value' => 'dept_name', 'label' => 'Dept Name'],
        ],
        'filterModalId' => 'filterDepartmentModal'
    ])

    <table>
        <thead>
            <tr>
                 <th>@include('partials._sortable_header', ['label'=>'ID','field'=>'dept_id'])</th>
                 <th>@include('partials._sortable_header', ['label'=>'Dept Code','field'=>'dept_code'])</th>
                 <th>@include('partials._sortable_header', ['label'=>'Dept Name','field'=>'dept_name'])</th>
                 <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departments as $d)
                <tr>
                    <td>{{ $d->dept_code }}</td>
                    <td>{{ $d->dept_name }}</td>
                        <td>{{ $d->dept_id }}</td>
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
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'createDepartmentModal'); });
    });

    document.getElementById('editForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.id.value;
        fetch('/departments/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'PUT'}, body: new FormData(e.target)})
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'editDepartmentModal'); });
    });

    document.getElementById('deleteForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.id.value;
        fetch('/departments/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}})
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'deleteDepartmentModal'); });
    });
</script>
@endpush

