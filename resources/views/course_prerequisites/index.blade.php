@extends('layouts.app')

@section('title','Course Prerequisites')

@section('toolbar')
    <div class="toolbar">
        <button onclick="openModal('createPrereqModal')">Manage</button>
        <form method="GET" action="{{ url('course-prerequisites') }}" style="display:flex; gap:8px; align-items:center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search...">
            <button type="submit" class="btn-secondary">Filter</button>
        </form>
        <form method="POST" action="{{ url('course-prerequisites/export-excel') }}" style="display:inline">@csrf
            <input type="hidden" name="search" value="{{ request('search') }}"><button>Export CSV</button>
        </form>
        <form method="GET" action="{{ url('course-prerequisites/export-pdf') }}" style="display:inline">
            <input type="hidden" name="search" value="{{ request('search') }}"><button type="submit" class="btn-secondary">Export PDF</button>
        </form>
    </div>
@endsection

@section('content')
    <table>
        <thead>
            <tr><th>Course</th><th>Prerequisite</th><th style="text-align:left">Actions</th></tr>
        </thead>
        <tbody>
            @foreach($prereqs as $p)
                <tr>
                    <td>{{ optional($p->course)->course_code }}</td>
                    <td>{{ optional($p->prereq)->course_code }}</td>
                    <td style="display:flex; gap:8px; justify-content:flex-start; align-items:center;">
                        <button onclick="openPrereqDelete('{{ $p->course_id }}:{{ $p->prereq_course_id }}')" class="btn-secondary">Delete</button>
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
function openPrereqDelete(composite){ const modal=document.getElementById('deletePrereqModal'); modal.style.display='flex'; modal.querySelector('[name="composite_id"]').value=composite; }
</script>
@endpush
