@extends('layouts.app')

@section('title','Sections')

@section('toolbar')
    <div class="toolbar">
        <button onclick="openModal('createSectionModal')">Manage</button>
        <form method="GET" action="{{ url('sections') }}" style="display:flex; gap:8px; align-items:center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search...">
            <button type="submit" class="btn-secondary">Filter</button>
        </form>
        <form method="POST" action="{{ url('sections/export-excel') }}" style="display:inline">@csrf
            <input type="hidden" name="search" value="{{ request('search') }}"><button>Export CSV</button>
        </form>
        <form method="GET" action="{{ url('sections/export-pdf') }}" style="display:inline">
            <input type="hidden" name="search" value="{{ request('search') }}"><button type="submit" class="btn-secondary">Export PDF</button>
        </form>
    </div>
@endsection

@section('content')
    <table>
        <thead>
            <tr>
                <th>Section Code</th>
                <th>Course</th>
                <th>Term</th>
                <th>Instructor</th>
                <th>Room</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sections as $s)
                <tr>
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
document.getElementById('createForm')?.addEventListener('submit', function(e){ e.preventDefault(); fetch(this.action,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:new FormData(this)}).then(r=>r.json()).then(()=>location.reload());});
</script>
@endpush
