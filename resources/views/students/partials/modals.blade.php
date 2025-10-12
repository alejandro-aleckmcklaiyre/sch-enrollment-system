<div id="createModal" class="modal">
    <div class="box">
        <h3>Create Student</h3>
        <form id="createForm" method="POST" action="{{ url('students') }}">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px">
                <input name="student_no" placeholder="Student No" required>
                <input name="last_name" placeholder="Last Name" required>
                <input name="first_name" placeholder="First Name" required>
                <input name="middle_name" placeholder="Middle Name">
                <input name="email" placeholder="Email">
                <select name="gender">
                    <option value="">Gender</option>
                    <option value="M">M</option>
                    <option value="F">F</option>
                </select>
                <input name="birthdate" type="date">
                <select name="year_level">
                    <option value="">Year Level</option>
                    @for($i=1;$i<=5;$i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                <select name="program_id" style="grid-column:1 / -1">
                    <option value="">Select Program</option>
                    @foreach($programs as $prog)
                        <option value="{{ $prog->program_id }}">{{ $prog->program_name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('createModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Create</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="box">
        <h3>Edit Student</h3>
        <form id="editForm" method="POST" action="{{ url('students') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="student_id">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px">
                <input name="student_no" placeholder="Student No" required>
                <input name="last_name" placeholder="Last Name" required>
                <input name="first_name" placeholder="First Name" required>
                <input name="middle_name" placeholder="Middle Name">
                <input name="email" placeholder="Email">
                <select name="gender">
                    <option value="">Gender</option>
                    <option value="M">M</option>
                    <option value="F">F</option>
                </select>
                <input name="birthdate" type="date">
                <select name="year_level">
                    <option value="">Year Level</option>
                    @for($i=1;$i<=5;$i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                <select name="program_id" style="grid-column:1 / -1">
                    <option value="">Select Program</option>
                    @foreach($programs as $prog)
                        <option value="{{ $prog->program_id }}">{{ $prog->program_name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('editModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteModal" class="modal">
    <div class="box">
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this student?</p>
        <form id="deleteForm" method="POST" action="{{ url('students') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="student_id">
            <div style="display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('deleteModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Delete</button>
            </div>
        </form>
    </div>
</div>
