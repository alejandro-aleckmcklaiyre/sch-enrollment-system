@extends('layouts.app')

@section('title','Programs')

@section('toolbar')
    @include('partials.table-toolbar', [
        'listUrl' => url('programs'),
        'exportExcelUrl' => url('programs/export-excel'),
        'exportPdfUrl' => url('programs/export-pdf'),
        'manageModalId' => 'createProgramModal',
        'filterModalId' => 'filterProgramModal'
    ])
@endsection

@section('content')
    @include('partials.table-controls', [
        'listUrl' => url('programs'),
        'sortFields' => [
            ['value' => 'program_id', 'label' => 'ID'],
            ['value' => 'program_code', 'label' => 'Program Code'],
            ['value' => 'program_name', 'label' => 'Program Name'],
        ],
        'filterModalId' => 'filterProgramModal'
    ])

    <table>
        <thead>
            <tr>
                <th>@include('partials._sortable_header', ['label'=>'ID','field'=>'program_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Program Code','field'=>'program_code'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Program Name','field'=>'program_name'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Department','field'=>'dept_id'])</th>
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
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'createProgramModal'); });
    });

    document.getElementById('editProgramForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.program_id.value;
        fetch('/programs/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'PUT'}, body: new FormData(e.target)})
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'editProgramModal'); });
    });

    document.getElementById('deleteProgramForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.program_id.value;
        fetch('/programs/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}})
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'deleteProgramModal'); });
    });

    document.getElementById('restoreForm').addEventListener('submit', function(e){
        e.preventDefault();
        const form = e.target;
        fetch(form.action, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: new FormData(form)})
    .then(r => r.json().then(data => { data.httpStatus = r.status; return data }))
    .then(resp=>{
        if(resp.success){
            showAlert('success', { title: 'Restore Completed', detail: `Processed: ${resp.results.processed}, Created: ${resp.results.created}, Updated: ${resp.results.updated}, Skipped: ${resp.results.skipped}` });
            closeModal('restoreModal');
            // Reload the page to show updated data
            setTimeout(() => location.reload(), 2000);
        } else {
            showAlert('error', { title: 'Restore Failed', detail: resp.message });
        }
    })
    .catch(err => { console.error(err); showAlert('error', { title: 'Error', detail: 'Request failed' }); });
    });
</script>
@endpush
