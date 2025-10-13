<div id="createRoomModal" class="modal">
    <div class="box">
        <h3>Create Room</h3>
        <form id="createRoomForm" action="{{ url('rooms') }}" method="POST">
            @csrf
            <div style="display:flex; gap:8px; flex-direction:column;">
                <label>Building</label>
                <input name="building">
                <label>Room Code</label>
                <input name="room_code" required>
                <label>Capacity</label>
                <input name="capacity" type="number">
                <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:8px;">
                    <button type="button" onclick="closeModal('createRoomModal')" class="btn-secondary">Cancel</button>
                    <button type="submit">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="editRoomModal" class="modal">
    <div class="box">
        <h3>Edit Room</h3>
        <form id="editRoomForm" action="{{ url('rooms') }}" method="POST">
            @csrf
            <input type="hidden" name="room_id">
            <div style="display:flex; gap:8px; flex-direction:column;">
                <label>Building</label>
                <input name="building">
                <label>Room Code</label>
                <input name="room_code" required>
                <label>Capacity</label>
                <input name="capacity" type="number">
                <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:8px;">
                    <button type="button" onclick="closeModal('editRoomModal')" class="btn-secondary">Cancel</button>
                    <button type="submit">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="deleteRoomModal" class="modal">
    <div class="box">
        <h3>Delete Room</h3>
        <form id="deleteRoomForm" action="" method="POST">
            @csrf
            <input type="hidden" name="room_id">
            <p>Are you sure you want to delete this room?</p>
            <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:8px;">
                <button type="button" onclick="closeModal('deleteRoomModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Delete</button>
            </div>
        </form>
    </div>
</div>

<div id="filterRoomModal" class="modal">
    <div class="box">
        <h3>Filter Rooms</h3>
        <form method="GET" action="{{ url('rooms') }}">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <!-- placeholder for future room filters -->
            </div>
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('filterRoomModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Apply</button>
            </div>
        </form>
    </div>
</div>
