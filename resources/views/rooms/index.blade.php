@extends('layouts.app')

@section('title','Rooms')

@section('toolbar')
    @include('partials.table-toolbar', [
        'listUrl' => url('rooms'),
        'exportExcelUrl' => url('rooms/export-excel'),
        'exportPdfUrl' => url('rooms/export-pdf'),
        'manageModalId' => 'createRoomModal',
        'filterModalId' => 'filterRoomModal'
    ])
@endsection

@section('content')
    @include('partials.table-controls', [
        'listUrl' => url('rooms'),
        'sortFields' => [
            ['value' => 'room_id', 'label' => 'ID'],
            ['value' => 'building', 'label' => 'Building'],
            ['value' => 'room_code', 'label' => 'Room Code'],
        ],
        'filterModalId' => 'filterRoomModal'
    ])

    @include('rooms.filter_modal')

    <table>
        <thead>
            <tr>
                <th>@include('partials._sortable_header', ['label'=>'ID','field'=>'room_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Building','field'=>'building'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Room Code','field'=>'room_code'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Capacity','field'=>'capacity'])</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rooms as $r)
                <tr>
                    <td>{{ $r->room_id }}</td>
                    <td>{{ $r->building }}</td>
                    <td>{{ $r->room_code }}</td>
                    <td>{{ $r->capacity }}</td>
                    <td style="display:flex; gap:8px; justify-content:flex-start; align-items:center; min-width:160px;">
                        <button style="min-width:64px; padding:8px 10px; height:34px;" onclick="openRoomEdit({{ $r->room_id }}, {{ json_encode($r) }})">Edit</button>
                        <button style="min-width:64px; padding:8px 10px; height:34px;" onclick="openRoomDelete({{ $r->room_id }})" class="btn-secondary">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:12px">@include('partials.pagination', ['paginator' => $rooms])</div>

@endsection

@push('modals')
    @include('rooms.partials.modals')
@endpush

@push('scripts')
<script>
    function openRoomEdit(id, data){
        const modal = document.getElementById('editRoomModal');
        modal.style.display='flex';
        modal.querySelector('[name="room_id"]').value = data.room_id;
        modal.querySelector('[name="building"]').value = data.building || '';
        modal.querySelector('[name="room_code"]').value = data.room_code || '';
        modal.querySelector('[name="capacity"]').value = data.capacity || '';
    }

    function openRoomDelete(id){
        const modal = document.getElementById('deleteRoomModal');
        modal.style.display='flex';
        modal.querySelector('[name="room_id"]').value = id;
    }

    document.getElementById('createRoomForm').addEventListener('submit', function(e){
        e.preventDefault();
        const form = e.target;
        fetch(form.action, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: new FormData(form)})
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'createRoomModal'); });
    });

    document.getElementById('editRoomForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.room_id.value;
        fetch('/rooms/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'PUT'}, body: new FormData(e.target)})
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'editRoomModal'); });
    });

    document.getElementById('deleteRoomForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.room_id.value;
        fetch('/rooms/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}})
    .then(r=>r.json()).then(resp=>{ handleResponse(resp,'deleteRoomModal'); });
    });
</script>
@endpush
