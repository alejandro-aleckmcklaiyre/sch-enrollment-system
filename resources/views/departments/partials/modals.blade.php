<!-- Create Modal -->
<div id="createDepartmentModal" class="modal" style="display:none">
    <div class="box">
        <h3>Create Department</h3>
        <form id="createForm" method="POST" action="{{ url('departments') }}">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <input name="dept_code" placeholder="Code" required>
                <input name="dept_name" placeholder="Name" required>
            </div>
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('createDepartmentModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editDepartmentModal" class="modal" style="display:none">
    <div class="box">
        <h3>Edit Department</h3>
        <form id="editForm" method="POST" action="{{ url('departments') }}">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_id" name="id" />
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <input id="edit_code" name="dept_code" placeholder="Code" required>
                <input id="edit_name" name="dept_name" placeholder="Name" required>
            </div>
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('editDepartmentModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteDepartmentModal" class="modal" style="display:none">
    <div class="box">
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this department?</p>
        <form id="deleteForm" method="POST" action="{{ url('departments') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" id="delete_id" name="id" />
            <div style="display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('deleteDepartmentModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Delete</button>
            </div>
        </form>
    </div>
</div>

<div id="restoreModal" class="modal">
    <div class="box">
        <h3>Restore Departments</h3>
        <form id="restoreForm" method="POST" action="{{ url('departments/restore') }}" enctype="multipart/form-data">
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

<script>
// Unified fetch helper to pass HTTP status and JSON body to handleResponse
function submitFormWithStatus(fetchPromise, modalId){
    fetchPromise.then(async (res)=>{
        let body = {};
        try{ body = await res.json(); } catch(e) { body = { message: 'No response body' }; }
        // attach http status for better handling
        body.httpStatus = res.status;
        handleResponse(body, modalId);
    }).catch(e=>{
        console.error(e);
        handleResponse({ message: 'Network error', success:false, error:true, httpStatus:0 }, modalId);
    });
}

// Create
document.getElementById('createForm').addEventListener('submit', function(e){
    e.preventDefault();
    var data = new FormData(this);
    submitFormWithStatus(fetch(this.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value },
        body: data
    }), 'createDepartmentModal');
});

// Edit
document.getElementById('editForm').addEventListener('submit', function(e){
    e.preventDefault();
    var id = document.getElementById('edit_id').value;
    var data = new FormData(this);
    submitFormWithStatus(fetch('/departments/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value, 'X-HTTP-Method-Override': 'PUT' },
        body: data
    }), 'editDepartmentModal');
});

// Delete
document.getElementById('deleteForm').addEventListener('submit', function(e){
    e.preventDefault();
    var id = document.getElementById('delete_id').value;
    submitFormWithStatus(fetch('/departments/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value, 'X-HTTP-Method-Override': 'DELETE' }
    }), 'deleteDepartmentModal');
});
</script>
