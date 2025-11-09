<div id="createTermModal" class="modal" style="display:none">
    <div class="box">
        <h3>Create Term</h3>
        <form id="createForm" method="POST" action="{{ url('terms') }}">
            @csrf
            <div style="display:flex; flex-direction:column; gap:12px">
                <div class="form-group">
                    <label for="term_code">Term Code</label>
                    <input id="term_code" name="term_code" required 
                           placeholder="e.g., 2025-1ST">
                </div>
                
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input id="start_date" name="start_date" type="date" required>
                </div>
                
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input id="end_date" name="end_date" type="date" required>
                    <div class="help-text">Must be after or equal to start date</div>
                </div>
            </div>
            <div style="margin-top:16px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('createTermModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="editTermModal" class="modal" style="display:none">
    <div class="box">
        <h3>Edit Term</h3>
        <form id="editForm" method="POST" action="{{ url('terms') }}">
            @csrf
            <input type="hidden" id="edit_term_id" name="term_id">
            <div style="display:flex; flex-direction:column; gap:12px">
                <div class="form-group">
                    <label for="edit_term_code">Term Code</label>
                    <input id="edit_term_code" name="term_code" required 
                           placeholder="e.g., 2025-1ST">
                </div>
                
                <div class="form-group">
                    <label for="edit_start_date">Start Date</label>
                    <input id="edit_start_date" name="start_date" type="date" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_end_date">End Date</label>
                    <input id="edit_end_date" name="end_date" type="date" required>
                    <div class="help-text">Must be after or equal to start date</div>
                </div>
            </div>
            <div style="margin-top:16px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('editTermModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Update</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteTermModal" class="modal" style="display:none">
    <div class="box">
        <h3>Delete Term</h3>
        <p>Are you sure?</p>
        <form id="deleteForm" method="POST" action="{{ url('terms') }}">@csrf
            <input type="hidden" id="delete_term_id" name="term_id">
            <div style="display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('deleteTermModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('createForm').addEventListener('submit', function(e){
    e.preventDefault(); submitFormWithStatus(fetch(this.action, { method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('input[name=_token]').value}, body: new FormData(this)}), 'createTermModal');
});

document.getElementById('editForm').addEventListener('submit', function(e){
    e.preventDefault(); var id = document.getElementById('edit_term_id').value; submitFormWithStatus(fetch('/terms/' + id, { method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('input[name=_token]').value,'X-HTTP-Method-Override':'PUT'}, body: new FormData(this)}), 'editTermModal');
});

document.getElementById('deleteForm').addEventListener('submit', function(e){
    e.preventDefault(); var id = document.getElementById('delete_term_id').value; submitFormWithStatus(fetch('/terms/' + id, { method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('input[name=_token]').value,'X-HTTP-Method-Override':'DELETE'}}), 'deleteTermModal');
});
</script>
