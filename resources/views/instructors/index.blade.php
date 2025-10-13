@extends('layouts.app')

@section('title','Instructors')

@section('toolbar')
    @include('partials.table-toolbar', [
        'listUrl' => url('instructors'),
        'exportExcelUrl' => url('instructors/export-excel'),
        'exportPdfUrl' => url('instructors/export-pdf'),
        'manageModalId' => 'createInstructorModal',
        'filterModalId' => 'filterInstructorModal'
    ])
@endsection

@section('content')
    @include('partials.table-controls', [
        'listUrl' => url('instructors'),
        'sortFields' => [
            ['value' => 'instructor_id', 'label' => 'ID'],
            ['value' => 'last_name', 'label' => 'Last Name'],
            ['value' => 'first_name', 'label' => 'First Name'],
        ],
        'filterModalId' => 'filterInstructorModal'
    ])

    <table>
        <thead>
            <tr>
                <th>@include('partials._sortable_header', ['label'=>'ID','field'=>'instructor_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Last Name','field'=>'last_name'])</th>
                <th>@include('partials._sortable_header', ['label'=>'First Name','field'=>'first_name'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Email','field'=>'email'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Department','field'=>'dept_id'])</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($instructors as $i)
                <tr>
                    <td>{{ $i->instructor_id }}</td>
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
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'createInstructorModal'); });
    });

    document.getElementById('editInstructorForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.instructor_id.value;
        fetch('/instructors/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'PUT'}, body: new FormData(e.target)})
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'editInstructorModal'); });
    });

    document.getElementById('deleteInstructorForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.instructor_id.value;
        fetch('/instructors/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}})
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'deleteInstructorModal'); });
    });
</script>
@endpush
