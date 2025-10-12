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
