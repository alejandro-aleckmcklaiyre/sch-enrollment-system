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

<div id="restoreModal" class="modal">
    <div class="box">
        <h3>Restore Rooms</h3>
        <form id="restoreForm" method="POST" action="{{ url('rooms/restore') }}" enctype="multipart/form-data">
            @csrf
            <div style="display:flex; flex-direction:column; gap:12px">
                <div>
                    <label for="backup_file">Select Backup File:</label>
                    <input type="file" name="file" id="backup_file" accept=".json" required>
                </div>
                <div>
                    <label for="restore_mode">Restore Mode:</label>
                    <select name="mode" id="restore_mode" required>
                        <option value="skip">Skip existing records</option>
                        <option value="update">Update existing records</option>
                        <option value="replace">Replace all data</option>
                    </select>
                </div>
                <div style="font-size:0.9em; color:#666;">
                    <strong>Skip:</strong> Only import new records, ignore existing ones<br>
                    <strong>Update:</strong> Update existing records, add new ones<br>
                    <strong>Replace:</strong> Delete all current data and import from backup
                </div>
            </div>
            <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('restoreModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Restore</button>
            </div>
        </form>
    </div>
</div>


