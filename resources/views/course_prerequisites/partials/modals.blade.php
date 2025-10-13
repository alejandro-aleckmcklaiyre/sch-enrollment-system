<div id="createPrereqModal" class="modal" style="display:none">
    <div class="box">
        <h3>Add Prerequisite</h3>
        <form id="createForm" method="POST" action="{{ url('course-prerequisites') }}">@csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <select name="course_id" required style="min-width:0; padding:8px">
                    <option value="">Select Course</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->course_id }}">{{ $c->course_code }} - {{ $c->course_title }}</option>
                    @endforeach
                </select>
                <select name="prereq_course_id" required style="min-width:0; padding:8px">
                    <option value="">Select Prerequisite</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->course_id }}">{{ $c->course_code }} - {{ $c->course_title }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('createPrereqModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="editPrereqModal" class="modal" style="display:none">
    <div class="box">
        <h3>Edit Prerequisite</h3>
        <form id="editForm" method="POST" action="{{ url('course-prerequisites') }}">@csrf @method('PUT')
            <input type="hidden" id="edit_composite_id" name="composite_id">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <select id="edit_course_id" name="course_id" required style="min-width:0; padding:8px">
                    <option value="">Select Course</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->course_id }}">{{ $c->course_code }} - {{ $c->course_title }}</option>
                    @endforeach
                </select>
                <select id="edit_prereq_course_id" name="prereq_course_id" required style="min-width:0; padding:8px">
                    <option value="">Select Prerequisite</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->course_id }}">{{ $c->course_code }} - {{ $c->course_title }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('editPrereqModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="deletePrereqModal" class="modal" style="display:none">
    <div class="box">
        <h3>Delete Prerequisite</h3>
        <p>Are you sure?</p>
        <form id="deleteForm" method="POST" action="{{ url('course-prerequisites') }}">@csrf @method('DELETE')
            <input type="hidden" id="delete_composite_id" name="composite_id">
            <div style="display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('deletePrereqModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Delete</button>
            </div>
        </form>
    </div>

<script>
// helper used elsewhere
function submitFormWithStatus(fetchPromise, modalId){
    fetchPromise.then(async (res)=>{
        let body = {};
        try{ body = await res.json(); } catch(e){ body = { message: 'No response body' }; }
        body.httpStatus = res.status; handleResponse(body, modalId);
    }).catch(e=>{ console.error(e); handleResponse({ message: 'Network error', success:false, error:true, httpStatus:0 }, modalId); });
}

// Create
document.getElementById('createForm').addEventListener('submit', function(e){
    e.preventDefault();
    submitFormWithStatus(fetch(this.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value },
        body: new FormData(this)
    }), 'createPrereqModal');
});

// Edit open helper
function openPrereqEdit(composite, currentCourseId, currentPrereqId){
    const modal = document.getElementById('editPrereqModal');
    modal.style.display = 'flex';
    document.getElementById('edit_composite_id').value = composite;
    document.getElementById('edit_course_id').value = currentCourseId || '';
    document.getElementById('edit_prereq_course_id').value = currentPrereqId || '';
}

// Edit submit
document.getElementById('editForm').addEventListener('submit', function(e){
    e.preventDefault();
    var composite = document.getElementById('edit_composite_id').value;
    if(!composite) return;
    var id = encodeURIComponent(composite);
    submitFormWithStatus(fetch('/course-prerequisites/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value, 'X-HTTP-Method-Override': 'PUT' },
        body: new FormData(this)
    }), 'editPrereqModal');
});

// Delete
document.getElementById('deleteForm').addEventListener('submit', function(e){
    e.preventDefault();
    var composite = document.getElementById('delete_composite_id').value || this.querySelector('[name=composite_id]')?.value;
    if(!composite) return;
    submitFormWithStatus(fetch('/course-prerequisites/' + encodeURIComponent(composite), {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value, 'X-HTTP-Method-Override': 'DELETE' }
    }), 'deletePrereqModal');
});
</script>
</div>
