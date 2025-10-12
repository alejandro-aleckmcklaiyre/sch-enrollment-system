@extends('layouts.app')

@section('title','Rooms')

@section('toolbar')
    <div class="toolbar">
        <button onclick="openModal('createRoomModal')">Manage</button>
        <form method="GET" action="{{ url('rooms') }}" style="display:flex; gap:8px; align-items:center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search...">
            <button type="submit" class="btn-secondary">Filter</button>
        </form>
        <form method="POST" action="{{ url('rooms/export-excel') }}" style="display:inline">
            @csrf
            <input type="hidden" name="filtered" value="1">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button>Export CSV</button>
        </form>
        <form method="GET" action="{{ url('rooms/export-pdf') }}" style="display:inline">
            <input type="hidden" name="filtered" value="1">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button type="submit" class="btn-secondary">Export PDF</button>
        </form>
    </div>
@endsection

@section('content')
    <table>
        <thead>
            <tr>
                <th>Building</th>
                <th>Room Code</th>
                <th>Capacity</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rooms as $r)
                <tr>
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
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('createRoomModal'); location.reload(); } else console.error(resp);});
    });

    document.getElementById('editRoomForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.room_id.value;
        fetch('/rooms/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'PUT'}, body: new FormData(e.target)})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('editRoomModal'); location.reload(); } else console.error(resp);});
    });

    document.getElementById('deleteRoomForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = e.target.room_id.value;
        fetch('/rooms/' + id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-HTTP-Method-Override':'DELETE'}})
        .then(r=>r.json()).then(resp=>{ if(resp && resp.message){ closeModal('deleteRoomModal'); location.reload(); } else console.error(resp);});
    });
</script>
@endpush
