<div id="createProgramModal" class="modal">
    <div class="box">
        <h3>Create Program</h3>
        <form id="createProgramForm" method="POST" action="{{ url('programs') }}">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <input name="program_code" placeholder="Program Code" required>
                <input name="program_name" placeholder="Program Name" required>
                <select name="dept_id">
                    <option value="">Select Department</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->dept_id }}">{{ $d->dept_name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('createProgramModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Create</button>
            </div>
        </form>
    </div>
</div>

<div id="editProgramModal" class="modal">
    <div class="box">
        <h3>Edit Program</h3>
        <form id="editProgramForm" method="POST" action="{{ url('programs') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="program_id">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <input name="program_code" placeholder="Program Code" required>
                <input name="program_name" placeholder="Program Name" required>
                <select name="dept_id">
                    <option value="">Select Department</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->dept_id }}">{{ $d->dept_name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('editProgramModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteProgramModal" class="modal">
    <div class="box">
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this program?</p>
        <form id="deleteProgramForm" method="POST" action="{{ url('programs') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="program_id">
            <div style="display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('deleteProgramModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Delete</button>
            </div>
        </form>
    </div>
</div>

<div id="filterProgramModal" class="modal">
    <div class="box">
        <h3>Filter Programs</h3>
        <form method="GET" action="{{ url('programs') }}">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <select name="dept_id">
                    <option value="">All Departments</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->dept_id }}" {{ request('dept_id') == $d->dept_id ? 'selected' : '' }}>{{ $d->dept_name }}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('filterProgramModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Apply</button>
            </div>
        </form>
    </div>
</div>

<div id="restoreModal" class="modal">
    <div class="box">
        <h3>Restore Programs</h3>
        <form id="restoreForm" method="POST" action="{{ url('programs/restore') }}" enctype="multipart/form-data">
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
