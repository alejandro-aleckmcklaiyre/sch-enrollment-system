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

<script>
// Create
document.getElementById('createForm').addEventListener('submit', function(e){
    e.preventDefault();
    var data = new FormData(this);
    fetch(this.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value },
        body: data
    }).then(r=>r.json()).then(resp=> { handleResponse(resp,'createDepartmentModal'); });
});

// Edit
document.getElementById('editForm').addEventListener('submit', function(e){
    e.preventDefault();
    var id = document.getElementById('edit_id').value;
    var data = new FormData(this);
    fetch('/departments/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value, 'X-HTTP-Method-Override': 'PUT' },
        body: data
    }).then(r=>r.json()).then(resp=> { handleResponse(resp,'editDepartmentModal'); });
});

// Delete
document.getElementById('deleteForm').addEventListener('submit', function(e){
    e.preventDefault();
    var id = document.getElementById('delete_id').value;
    fetch('/departments/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value, 'X-HTTP-Method-Override': 'DELETE' }
    }).then(r=>r.json()).then(resp=> { handleResponse(resp,'deleteDepartmentModal'); });
});
</script>
