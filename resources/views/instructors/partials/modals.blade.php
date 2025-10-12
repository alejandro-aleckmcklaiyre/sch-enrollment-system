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
