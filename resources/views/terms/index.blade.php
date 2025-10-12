@extends('layouts.app')

@section('title','Terms')

@section('toolbar')
    <div class="toolbar">
        <button onclick="openModal('createTermModal')">Manage</button>
        <form method="GET" action="{{ url('terms') }}" style="display:flex; gap:8px; align-items:center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search...">
            <button type="submit" class="btn-secondary">Filter</button>
        </form>
        <form method="POST" action="{{ url('terms/export-excel') }}" style="display:inline">@csrf
            <input type="hidden" name="search" value="{{ request('search') }}"><button>Export CSV</button>
        </form>
        <form method="GET" action="{{ url('terms/export-pdf') }}" style="display:inline">
            <input type="hidden" name="search" value="{{ request('search') }}"><button type="submit" class="btn-secondary">Export PDF</button>
        </form>
    </div>
@endsection

@section('content')
    <table>
        <thead>
            <tr><th>Term Code</th><th>Start</th><th>End</th><th style="text-align:left">Actions</th></tr>
        </thead>
        <tbody>
            @foreach($terms as $t)
                <tr>
                    <td>{{ $t->term_code }}</td>
                    <td>{{ $t->start_date }}</td>
                    <td>{{ $t->end_date }}</td>
                    <td style="display:flex; gap:8px; justify-content:flex-start; align-items:center;">
                        <button onclick="openTermEdit({{ $t->term_id }}, {{ json_encode($t) }})">Edit</button>
                        <button onclick="openTermDelete({{ $t->term_id }})" class="btn-secondary">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:12px">@include('partials.pagination',['paginator'=>$terms])</div>
@endsection

@push('modals')@include('terms.partials.modals')@endpush

@push('scripts')
<script>
function openTermEdit(id,data){ const modal=document.getElementById('editTermModal'); modal.style.display='flex'; modal.querySelector('[name="term_id"]').value=data.term_id; }
function openTermDelete(id){ const modal=document.getElementById('deleteTermModal'); modal.style.display='flex'; modal.querySelector('[name="term_id"]').value=id; }
</script>
@endpush
