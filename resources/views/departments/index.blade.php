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
                @include('departments._row', ['dept' => $d])
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
        // try to read data from the table row if not passed
        let rowData = data || null;
        if(!rowData){
            const row = document.querySelector('tr[data-dept-id="' + id + '"]');
            if(row && row.dataset && row.dataset.dept){
                try{ rowData = JSON.parse(row.dataset.dept); }catch(e){ rowData = null; }
            }
        }
        modal.style.display='flex';
        modal.querySelector('[name="id"]').value = rowData?.dept_id || id;
        modal.querySelector('[name="dept_code"]').value = rowData?.dept_code || '';
        modal.querySelector('[name="dept_name"]').value = rowData?.dept_name || '';
    }

    function openDepartmentDelete(id){
        const modal = document.getElementById('deleteDepartmentModal');
        modal.style.display='flex';
        modal.querySelector('[name="id"]').value = id;
    }

    // Create/Edit/Delete form handlers are bound inside the modals partial to ensure a single source of truth.
    // Attach click handlers to action buttons after DOM ready to ensure each button reliably opens the modals
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('button[data-action="edit"]').forEach(function(btn){
            btn.addEventListener('click', function(e){
                const id = btn.getAttribute('data-id');
                const row = document.querySelector('tr[data-dept-id="' + id + '"]');
                let rowData = null;
                if(row && row.dataset && row.dataset.dept){
                    try{ rowData = JSON.parse(row.dataset.dept); }catch(e){ rowData = null; }
                }
                const modal = document.getElementById('editDepartmentModal');
                modal.style.display='flex';
                modal.querySelector('[name="id"]').value = rowData?.dept_id || id;
                modal.querySelector('[name="dept_code"]').value = rowData?.dept_code || '';
                modal.querySelector('[name="dept_name"]').value = rowData?.dept_name || '';
            });
        });
        document.querySelectorAll('button[data-action="delete"]').forEach(function(btn){
            btn.addEventListener('click', function(e){
                const id = btn.getAttribute('data-id');
                const modal = document.getElementById('deleteDepartmentModal');
                modal.style.display='flex';
                modal.querySelector('[name="id"]').value = id;
            });
        });
    });
</script>
@endpush

