<!-- Create Student Modal -->
<div id="createStudentModal" class="modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:white; padding:30px; border-radius:8px; max-width:600px; width:90%; max-height:90vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0;">Create Student</h2>
            <button onclick="closeModal('createStudentModal')" style="background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
        </div>
        <form id="createStudentForm" method="POST" action="{{ route('admin.students.store') }}">
            @csrf
            <div style="margin-bottom:15px;">
                <label for="student_no" style="display:block; margin-bottom:5px; font-weight:500;">Student No</label>
                <input type="text" name="student_no" id="student_no" class="form-control" required>
            </div>
            <div style="margin-bottom:15px;">
                <label for="first_name" style="display:block; margin-bottom:5px; font-weight:500;">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control" required>
            </div>
            <div style="margin-bottom:15px;">
                <label for="last_name" style="display:block; margin-bottom:5px; font-weight:500;">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control" required>
            </div>
            <div style="margin-bottom:15px;">
                <label for="email" style="display:block; margin-bottom:5px; font-weight:500;">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div style="margin-bottom:15px;">
                <label for="year_level" style="display:block; margin-bottom:5px; font-weight:500;">Year Level</label>
                <select name="year_level" id="year_level" class="form-control" required>
                    <option value="">Select Year Level</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label for="program_id" style="display:block; margin-bottom:5px; font-weight:500;">Program</label>
                <select name="program_id" id="program_id" class="form-control" required>
                    <option value="">Select Program</option>
                    @foreach($programs ?? [] as $program)
                        @if(is_object($program) && isset($program->program_id) && isset($program->program_name))
                            <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:25px;">
                <button type="button" onclick="closeModal('createStudentModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Student Modal -->
<div id="editStudentModal" class="modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:white; padding:30px; border-radius:8px; max-width:600px; width:90%; max-height:90vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0;">Edit Student</h2>
            <button onclick="closeModal('editStudentModal')" style="background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
        </div>
        <form id="editStudentForm" method="POST" action="{{ route('admin.students.update', 0) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="student_id">
            <div style="margin-bottom:15px;">
                <label for="edit_student_no" style="display:block; margin-bottom:5px; font-weight:500;">Student No</label>
                <input type="text" name="student_no" id="edit_student_no" class="form-control" required>
            </div>
            <div style="margin-bottom:15px;">
                <label for="edit_first_name" style="display:block; margin-bottom:5px; font-weight:500;">First Name</label>
                <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
            </div>
            <div style="margin-bottom:15px;">
                <label for="edit_last_name" style="display:block; margin-bottom:5px; font-weight:500;">Last Name</label>
                <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
            </div>
            <div style="margin-bottom:15px;">
                <label for="edit_email" style="display:block; margin-bottom:5px; font-weight:500;">Email</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            <div style="margin-bottom:15px;">
                <label for="edit_year_level" style="display:block; margin-bottom:5px; font-weight:500;">Year Level</label>
                <select name="year_level" id="edit_year_level" class="form-control" required>
                    <option value="">Select Year Level</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label for="edit_program_id" style="display:block; margin-bottom:5px; font-weight:500;">Program</label>
                <select name="program_id" id="edit_program_id" class="form-control" required>
                    <option value="">Select Program</option>
                    @foreach($programs ?? [] as $program)
                        @if(is_object($program) && isset($program->program_id) && isset($program->program_name))
                            <option value="{{ $program->program_id }}">{{ $program->program_name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:25px;">
                <button type="button" onclick="closeModal('editStudentModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Student Modal -->
<div id="deleteStudentModal" class="modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:white; padding:30px; border-radius:8px; max-width:500px; width:90%;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0;">Delete Student</h2>
            <button onclick="closeModal('deleteStudentModal')" style="background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
        </div>
        <p style="margin-bottom:20px; color:#666;">Are you sure you want to delete this student? This action cannot be undone.</p>
        <form id="deleteStudentForm" method="POST" action="{{ route('admin.students.destroy', 0) }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="student_id">
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:25px;">
                <button type="button" onclick="closeModal('deleteStudentModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<!-- Backup Modal -->
<div id="backupModal" class="modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:white; padding:30px; border-radius:8px; max-width:500px; width:90%;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0;">Backup Students</h2>
            <button onclick="closeModal('backupModal')" style="background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
        </div>
        <p style="margin-bottom:20px; color:#666;">Create a backup of all student data.</p>
        <form id="backupForm" method="POST" action="{{ route('admin.students.backup') }}">
            @csrf
            <input type="hidden" name="table" value="students">
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:25px;">
                <button type="button" onclick="closeModal('backupModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Backup</button>
            </div>
        </form>
    </div>
</div>

<!-- Restore Modal -->
<div id="restoreModal" class="modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:white; padding:30px; border-radius:8px; max-width:500px; width:90%;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0;">Restore Students</h2>
            <button onclick="closeModal('restoreModal')" style="background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
        </div>
        <form id="restoreForm" method="POST" action="{{ route('admin.students.restore') }}" enctype="multipart/form-data">
            @csrf
            <div style="display:flex; flex-direction:column; gap:12px;">
                <div>
                    <label for="backup_file">Select Backup File (JSON):</label>
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
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:15px;">
                <button type="button" onclick="closeModal('restoreModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Restore</button>
            </div>
        </form>
    </div>
</div>

<!-- Filter Student Modal -->
<div id="filterStudentModal" class="modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:white; padding:30px; border-radius:8px; max-width:500px; width:90%;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0;">Filter Students</h2>
            <button onclick="closeModal('filterStudentModal')" style="background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
        </div>
        <form method="GET" action="{{ route('admin.students.index') }}">
            <div style="margin-bottom:15px;">
                <label for="filter_year_level" style="display:block; margin-bottom:5px; font-weight:500;">Year Level</label>
                <select name="year_level" id="filter_year_level" class="form-control">
                    <option value="">All Years</option>
                    <option value="1" {{ request('year_level') == '1' ? 'selected' : '' }}>1st Year</option>
                    <option value="2" {{ request('year_level') == '2' ? 'selected' : '' }}>2nd Year</option>
                    <option value="3" {{ request('year_level') == '3' ? 'selected' : '' }}>3rd Year</option>
                    <option value="4" {{ request('year_level') == '4' ? 'selected' : '' }}>4th Year</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label for="filter_program_id" style="display:block; margin-bottom:5px; font-weight:500;">Program</label>
                <select name="program_id" id="filter_program_id" class="form-control">
                    <option value="">All Programs</option>
                    @foreach($programs ?? [] as $program)
                        @if(is_object($program) && isset($program->program_id) && isset($program->program_name))
                            <option value="{{ $program->program_id }}" {{ request('program_id') == $program->program_id ? 'selected' : '' }}>{{ $program->program_name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:25px;">
                <button type="button" onclick="closeModal('filterStudentModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Apply</button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    
    function handleResponse(resp, modalId) {
        if (resp.success) {
            showAlert('success', { title: 'Success', detail: resp.message });
            closeModal(modalId);
            setTimeout(() => location.reload(), 2000);
        } else {
            showAlert('error', { title: 'Error', detail: resp.message });
        }
    }
</script>
