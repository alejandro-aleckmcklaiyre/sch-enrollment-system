<div id="createTermModal" class="modal" style="display:none">
    <div class="box">
        <h3>Create Term</h3>
        <form id="createForm" method="POST" action="{{ url('terms') }}">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <input name="term_code" placeholder="Term Code" required>
                <input name="start_date" type="date" required>
                <input name="end_date" type="date" required>
                <div></div>
            </div>
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('createTermModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="editTermModal" class="modal" style="display:none">
    <div class="box">
        <h3>Edit Term</h3>
        <form id="editForm" method="POST" action="{{ url('terms') }}">@csrf
            <input type="hidden" id="edit_term_id" name="term_id">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <input name="term_code" placeholder="Term Code" required>
                <input name="start_date" type="date" required>
                <input name="end_date" type="date" required>
                <div></div>
            </div>
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
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
