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
        <form id="editForm" method="POST" action="{{ url('terms') }}">@csrf @method('PUT')
            <input type="hidden" name="term_id">
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
        <form id="deleteForm" method="POST" action="{{ url('terms') }}">@csrf @method('DELETE')
            <input type="hidden" name="term_id">
            <div style="display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('deleteTermModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Delete</button>
            </div>
        </form>
    </div>
</div>
