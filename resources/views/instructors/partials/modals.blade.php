<div id="createInstructorModal" class="modal">
    <div class="box">
        <h3>Create Instructor</h3>
        <form id="createInstructorForm" action="{{ url('instructors') }}" method="POST">
            @csrf
            <div style="display:flex; gap:8px; flex-direction:column;">
                <label>Last Name</label>
                <input name="last_name" required>
                <label>First Name</label>
                <input name="first_name" required>
                <label>Email</label>
                <input name="email" type="email">
                <label>Department</label>
                <select name="dept_id">
                    <option value="">-- choose --</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->dept_id }}">{{ $d->dept_name }}</option>
                    @endforeach
                </select>
                <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:8px;">
                    <button type="button" onclick="closeModal('createInstructorModal')" class="btn-secondary">Cancel</button>
                    <button type="submit">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="editInstructorModal" class="modal">
    <div class="box">
        <h3>Edit Instructor</h3>
        <form id="editInstructorForm" action="{{ url('instructors') }}" method="POST">
            @csrf
            <input type="hidden" name="instructor_id">
            <div style="display:flex; gap:8px; flex-direction:column;">
                <label>Last Name</label>
                <input name="last_name" required>
                <label>First Name</label>
                <input name="first_name" required>
                <label>Email</label>
                <input name="email" type="email">
                <label>Department</label>
                <select name="dept_id">
                    <option value="">-- choose --</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->dept_id }}">{{ $d->dept_name }}</option>
                    @endforeach
                </select>
                <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:8px;">
                    <button type="button" onclick="closeModal('editInstructorModal')" class="btn-secondary">Cancel</button>
                    <button type="submit">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="deleteInstructorModal" class="modal">
    <div class="box">
        <h3>Delete Instructor</h3>
        <form id="deleteInstructorForm" action="" method="POST">
            @csrf
            <input type="hidden" name="instructor_id">
            <p>Are you sure you want to delete this instructor?</p>
            <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:8px;">
                <button type="button" onclick="closeModal('deleteInstructorModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Delete</button>
            </div>
        </form>
    </div>
</div>

<div id="filterInstructorModal" class="modal">
    <div class="box">
        <h3>Filter Instructors</h3>
        <form method="GET" action="{{ url('instructors') }}">
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
                <button type="button" onclick="closeModal('filterInstructorModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Apply</button>
            </div>
        </form>
    </div>
</div>

<div id="restoreModal" class="modal">
    <div class="box">
        <h3>Restore Instructors</h3>
        <form id="restoreForm" method="POST" action="{{ url('instructors/restore') }}" enctype="multipart/form-data">
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
