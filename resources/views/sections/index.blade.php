@extends('layouts.app')

@section('title','Sections')

@section('toolbar')
    @include('partials.table-toolbar', [
        'listUrl' => url('sections'),
        'exportExcelUrl' => url('sections/export-excel'),
        'exportPdfUrl' => url('sections/export-pdf'),
        'manageModalId' => 'createSectionModal',
        'filterModalId' => 'filterSectionModal'
    ])
@endsection

@section('content')
    @include('partials.table-controls', [
        'listUrl' => url('sections'),
        'sortFields' => [
            ['value' => 'section_id', 'label' => 'ID'],
            ['value' => 'section_code', 'label' => 'Section Code'],
            ['value' => 'course_id', 'label' => 'Course'],
        ],
        'filterModalId' => 'filterSectionModal'
    ])

    <table>
        <thead>
            <tr>
                <th>@include('partials._sortable_header', ['label'=>'ID','field'=>'section_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Section Code','field'=>'section_code'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Course','field'=>'course_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Term','field'=>'term_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Instructor','field'=>'instructor_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Room','field'=>'room_id'])</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sections as $s)
                <tr>
                    <td>{{ $s->section_id }}</td>
                    <td>{{ $s->section_code }}</td>
                    <td>{{ optional($s->course)->course_code }}</td>
                    <td>{{ optional($s->term)->term_code }}</td>
                    <td>{{ optional($s->instructor)->last_name }}</td>
                    <td>{{ optional($s->room)->room_code }}</td>
                    <td style="display:flex; gap:8px; justify-content:flex-start; align-items:center;">
                        <button onclick="openSectionEdit({{ $s->section_id }}, {{ json_encode($s) }})">Edit</button>
                        <button onclick="openSectionDelete({{ $s->section_id }})" class="btn-secondary">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:12px">@include('partials.pagination',['paginator'=>$sections])</div>
@endsection

@push('modals')@include('sections.partials.modals')@endpush

@push('scripts')
<script>
function openSectionEdit(id,data){ const modal=document.getElementById('editSectionModal'); modal.style.display='flex'; modal.querySelector('[name="section_id"]').value=data.section_id; }
function openSectionDelete(id){ const modal=document.getElementById('deleteSectionModal'); modal.style.display='flex'; modal.querySelector('[name="section_id"]').value=id; }
document.getElementById('createForm')?.addEventListener('submit', function(e){ e.preventDefault(); const form=this; fetch(this.action,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:new FormData(this)}).then(r=>r.json()).then(resp=>{ handleResponse(resp,'createSectionModal'); });});
</script>
@endpush
