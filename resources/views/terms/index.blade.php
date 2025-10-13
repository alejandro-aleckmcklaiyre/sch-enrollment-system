@extends('layouts.app')

@section('title','Terms')

@section('toolbar')
    @include('partials.table-toolbar', [
        'listUrl' => url('terms'),
        'exportExcelUrl' => url('terms/export-excel'),
        'exportPdfUrl' => url('terms/export-pdf'),
        'manageModalId' => 'createTermModal',
        'filterModalId' => 'filterTermModal'
    ])
@endsection

@section('content')
    @include('partials.table-controls', [
        'listUrl' => url('terms'),
        'sortFields' => [
            ['value' => 'term_id', 'label' => 'ID'],
            ['value' => 'term_code', 'label' => 'Term Code'],
            ['value' => 'start_date', 'label' => 'Start'],
        ],
        'filterModalId' => 'filterTermModal'
    ])

    <table>
        <thead>
            <tr>
                <th>@include('partials._sortable_header', ['label'=>'ID','field'=>'term_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Term Code','field'=>'term_code'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Start','field'=>'start_date'])</th>
                <th>@include('partials._sortable_header', ['label'=>'End','field'=>'end_date'])</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($terms as $t)
                <tr>
                    <td>{{ $t->term_id }}</td>
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
function openTermEdit(id,data){ const modal=document.getElementById('editTermModal'); modal.style.display='flex'; document.getElementById('edit_term_id').value = data.term_id || id; modal.querySelector('[name="term_code"]').value = data.term_code || ''; modal.querySelector('[name="start_date"]').value = data.start_date || ''; modal.querySelector('[name="end_date"]').value = data.end_date || ''; }
function openTermDelete(id){ const modal=document.getElementById('deleteTermModal'); modal.style.display='flex'; document.getElementById('delete_term_id').value = id; }
</script>
@endpush
