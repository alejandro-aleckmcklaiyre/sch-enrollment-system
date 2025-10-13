@extends('layouts.app')

@section('title','Course Prerequisites')

@section('toolbar')
    @include('partials.table-toolbar', [
        'listUrl' => url('course-prerequisites'),
        'exportExcelUrl' => url('course-prerequisites/export-excel'),
        'exportPdfUrl' => url('course-prerequisites/export-pdf'),
        'manageModalId' => 'createPrereqModal',
        'filterModalId' => 'filterPrereqModal'
    ])
@endsection

@section('content')
    @include('partials.table-controls', [
        'listUrl' => url('course-prerequisites'),
        'sortFields' => [
            ['value' => 'course_id', 'label' => 'Course'],
            ['value' => 'prereq_course_id', 'label' => 'Prerequisite'],
        ],
        'filterModalId' => 'filterPrereqModal'
    ])
    <table>
        <thead>
            <tr>
                <th>@include('partials._sortable_header', ['label'=>'Course','field'=>'course_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Prerequisite','field'=>'prereq_course_id'])</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prereqs as $p)
                <tr>
                    <td>{{ optional($p->course)->course_code }}</td>
                    <td>{{ optional($p->prereq)->course_code }}</td>
                    <td style="display:flex; gap:8px; justify-content:flex-start; align-items:center;">
                        <button onclick="openPrereqEdit('{{ $p->course_id }}:{{ $p->prereq_course_id }}', '{{ $p->course_id }}', '{{ $p->prereq_course_id }}')">Edit</button>
                        <button onclick="(function(){ const modal=document.getElementById('deletePrereqModal'); modal.style.display='flex'; document.getElementById('delete_composite_id').value='{{ $p->course_id }}:{{ $p->prereq_course_id }}'; })()" class="btn-secondary">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:12px">@include('partials.pagination',['paginator'=>$prereqs])</div>
@endsection

@push('modals')@include('course_prerequisites.partials.modals')@endpush

@push('scripts')
<script>
function openPrereqDelete(composite){ const modal=document.getElementById('deletePrereqModal'); modal.style.display='flex'; document.getElementById('delete_composite_id').value = composite; }
// fallback if partial's openPrereqEdit isn't available
function openPrereqEditFallback(composite, courseId, prereqId){ if(typeof openPrereqEdit === 'function'){ openPrereqEdit(composite, courseId, prereqId); } }
</script>
@endpush
