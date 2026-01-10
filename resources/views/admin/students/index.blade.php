@extends('layouts.app')

@section('title','Students')

@section('toolbar')
    @include('partials.table-toolbar', [
        'listUrl' => route('admin.students.index'),
        'exportExcelUrl' => route('admin.students.export-excel'),
        'exportPdfUrl' => route('admin.students.export-pdf'),
        'manageModalId' => 'createStudentModal',
        'filterModalId' => 'filterStudentModal'
    ])
@endsection

@section('content')
    @include('partials.table-controls', [
        'listUrl' => route('admin.students.index'),
        'sortFields' => [
            ['value' => 'student_id', 'label' => 'ID'],
            ['value' => 'student_no', 'label' => 'Student No'],
            ['value' => 'last_name', 'label' => 'Last Name'],
            ['value' => 'first_name', 'label' => 'First Name'],
        ],
        'filterModalId' => 'filterStudentModal'
    ])

    <table>
        <thead>
            <tr>
                <th>@include('partials._sortable_header', ['label'=>'ID','field'=>'student_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Student No','field'=>'student_no'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Name','field'=>'last_name'])</th>
                <th>Email</th>
                <th>@include('partials._sortable_header', ['label'=>'Year Level','field'=>'year_level'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Program','field'=>'program_id'])</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->student_id }}</td>
                    <td>{{ $student->student_no }}</td>
                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->year_level }}</td>
                    <td>{{ optional($student->program)->program_name ?? 'N/A' }}</td>
                    <td style="display:flex; gap:6px; justify-content:flex-start; align-items:center; min-width:140px;">
                        <button style="min-width:64px; padding:8px 10px;" onclick='openStudentEdit({{ $student->student_id }}, @json($student))'>Edit</button>
                        <button style="min-width:64px; padding:8px 10px;" onclick='openStudentDelete({{ $student->student_id }})' class="btn-secondary">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:12px;">@include('partials.pagination', ['paginator' => $students])</div>

@endsection

@push('modals')
    @include('students.partials.modals')
@endpush

@push('scripts')
<script>


    console.log('Student admin script loaded');

    function openStudentEdit(id, data){
        console.log('openStudentEdit', id, data);
        const modal = document.getElementById('editStudentModal');
        if (!modal) return console.warn('editStudentModal not found');
        modal.style.display='flex';
        modal.querySelector('[name="student_id"]').value = data.student_id;
        modal.querySelector('#edit_student_no').value = data.student_no || '';
        modal.querySelector('#edit_first_name').value = data.first_name || '';
        modal.querySelector('#edit_last_name').value = data.last_name || '';
        modal.querySelector('#edit_email').value = data.email || '';
        modal.querySelector('#edit_year_level').value = data.year_level || '';
        modal.querySelector('#edit_program_id').value = data.program_id || '';
        // Set the form action to the correct student ID
        const form = modal.querySelector('form');
        if (form) form.action = '/admin/students/' + id;
    }

    function openStudentDelete(id){
        console.log('openStudentDelete', id);
        const modal = document.getElementById('deleteStudentModal');
        if (!modal) return console.warn('deleteStudentModal not found');
        modal.style.display='flex';
        modal.querySelector('[name="student_id"]').value = id;
        // Set the form action to the correct student ID
        const form = modal.querySelector('form');
        if (form) form.action = '/admin/students/' + id;
    }

    // Helper to safely attach submit handlers
    function attachFormHandler(formId, handler) {
        const el = document.getElementById(formId);
        if (!el) return;
        el.addEventListener('submit', handler);
    }

    attachFormHandler('createStudentForm', function(e){
        e.preventDefault();
        const form = e.target;
        const fd = new FormData(form);
        fetch(form.action, {method:'POST', headers:{'Accept':'application/json'}, body: fd})
            .then(r => r.json())
            .then(resp => { handleResponse(resp,'createStudentModal'); })
            .catch(err => { console.error(err); showAlert('error', { title: 'Error', detail: 'Request failed' }); });
    });

    attachFormHandler('editStudentForm', function(e){
        e.preventDefault();
        const form = e.target;
        const id = form.student_id.value;
        const fd = new FormData(form);
        fd.append('_method','PUT');
        // use the form action if set, otherwise fallback to admin path
        const url = form.action && form.action.length ? form.action : ('/admin/students/' + id);
        fetch(url, {method:'POST', headers:{'Accept':'application/json'}, body: fd})
            .then(r => r.json())
            .then(resp => { handleResponse(resp,'editStudentModal'); })
            .catch(err => { console.error(err); showAlert('error', { title: 'Error', detail: 'Request failed' }); });
    });

    attachFormHandler('deleteStudentForm', function(e){
        e.preventDefault();
        const form = e.target;
        const id = form.student_id.value;
        const fd = new FormData(form);
        fd.append('_method','DELETE');
        const url = form.action && form.action.length ? form.action : ('/admin/students/' + id);
        fetch(url, {method:'POST', headers:{'Accept':'application/json'}, body: fd})
            .then(r => r.json())
            .then(resp => { handleResponse(resp,'deleteStudentModal'); })
            .catch(err => { console.error(err); showAlert('error', { title: 'Error', detail: 'Request failed' }); });
    });

    attachFormHandler('restoreForm', function(e){
        e.preventDefault();
        const form = e.target;
        const fd = new FormData(form);
        fetch(form.action, {method:'POST', headers:{'Accept':'application/json'}, body: fd})
            .then(r => r.json().then(data => { data.httpStatus = r.status; return data }))
            .then(resp=>{
                if(resp.success){
                    showAlert('success', { title: 'Restore Completed', detail: `Processed: ${resp.results.processed}, Created: ${resp.results.created}, Updated: ${resp.results.updated}, Skipped: ${resp.results.skipped}` });
                    closeModal('restoreModal');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showAlert('error', { title: 'Restore Failed', detail: resp.message });
                }
            })
            .catch(err => { console.error(err); showAlert('error', { title: 'Error', detail: 'Request failed' }); });
    });
</script>
@endpush