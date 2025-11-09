@extends('layouts.app')

@section('title','Enrollments')

@section('toolbar')
    @include('partials.table-toolbar', [
        'listUrl' => url('enrollments'),
        'exportExcelUrl' => url('enrollments/export-excel'),
        'exportPdfUrl' => url('enrollments/export-pdf'),
        'manageModalId' => 'createEnrollmentModal',
        'filterModalId' => 'filterEnrollmentModal'
    ])
@endsection

@section('content')
    @include('partials.table-controls', [
        'listUrl' => url('enrollments'),
        'sortFields' => [
            ['value' => 'enrollment_id', 'label' => 'ID'],
            ['value' => 'student_id', 'label' => 'Student'],
            ['value' => 'date_enrolled', 'label' => 'Date Enrolled'],
        ],
        'filterModalId' => 'filterEnrollmentModal'
    ])

    <table>
        <thead>
            <tr>
                <th>@include('partials._sortable_header', ['label'=>'ID','field'=>'enrollment_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Student','field'=>'student_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Section','field'=>'section_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Date Enrolled','field'=>'date_enrolled'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Status','field'=>'status'])</th>
                <th>Grade</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enrollments as $e)
                <tr>
                    <td>{{ $e->enrollment_id }}</td>
                    <td>{{ optional($e->student)->student_no }} - {{ optional($e->student)->last_name }}</td>
                    <td>{{ $e->section_id }}</td>
                    <td>{{ $e->date_enrolled }}</td>
                    <td class="status-{{ strtolower($e->status ?? 'enrolled') }}">{{ ucfirst($e->status ?? 'Enrolled') }}</td>
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
        const submitBtn = form.querySelector('[type="submit"]');
        if(form.dataset.submitting === 'true') return; // Prevent double submit
        
        form.dataset.submitting = 'true';
        submitBtn.disabled = true;
        
        fetch(form.action || '/enrollments', {
            method:'POST', 
            headers:{
                'X-CSRF-TOKEN':'{{ csrf_token() }}',
                'Accept':'application/json'
            }, 
            body: new FormData(form)
        })
        .then(async response => {
            const data = await response.json();
            // Add HTTP status to response for proper error handling
            data.httpStatus = response.status;
            return data;
        })
        .then(resp => {
            handleResponse(resp, 'createEnrollmentModal');
        })
        .catch(error => {
            showAlert('error', {
                title: 'Error',
                detail: 'Failed to create enrollment. Please try again.'
            });
        })
        .finally(() => {
            form.dataset.submitting = 'false';
            submitBtn.disabled = false;
        });
    });

    document.getElementById('editForm').addEventListener('submit', function(e){
        e.preventDefault();
        const form = this;
        const submitBtn = form.querySelector('[type="submit"]');
        if(form.dataset.submitting === 'true') return;
        
        const id = form.querySelector('[name="id"]').value || document.getElementById('edit_id')?.value;
        
        form.dataset.submitting = 'true';
        submitBtn.disabled = true;
        
        fetch('/enrollments/' + id, {
            method:'POST', 
            headers:{
                'X-CSRF-TOKEN':'{{ csrf_token() }}',
                'X-HTTP-Method-Override':'PUT'
            }, 
            body: new FormData(form)
        })
        .then(async response => {
            const data = await response.json();
            data.httpStatus = response.status;
            return data;
        })
        .then(resp => {
            handleResponse(resp, 'editEnrollmentModal');
        })
        .catch(error => {
            showAlert('error', {
                title: 'Error',
                detail: 'Failed to update enrollment. Please try again.'
            });
        })
        .finally(() => {
            form.dataset.submitting = 'false';
            submitBtn.disabled = false;
        });
    });

    document.getElementById('deleteForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.id.value;
        fetch('/enrollments/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}})
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'deleteEnrollmentModal'); });
    });
</script>
@endpush

