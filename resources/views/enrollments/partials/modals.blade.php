<!-- Create Modal -->
<div id="createEnrollmentModal" class="modal" style="display:none">
    <div class="box">
        <h3>Create Enrollment</h3>
        <form id="createForm" method="POST" action="{{ url('enrollments') }}">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <select name="student_id">
                    @foreach($students as $s)
                        <option value="{{ $s->student_id }}">{{ $s->student_no }} - {{ $s->last_name }}, {{ $s->first_name }}</option>
                    @endforeach
                </select>
                <input name="section_id" placeholder="Section ID" />
                <input type="date" name="date_enrolled" />
                <input name="status" placeholder="Status" />
                <input name="letter_grade" placeholder="Letter Grade" />
                <div></div>
            </div>
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('createEnrollmentModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editEnrollmentModal" class="modal" style="display:none">
    <div class="box">
        <h3>Edit Enrollment</h3>
        <form id="editForm" method="POST" action="{{ url('enrollments') }}">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_id" name="id" />
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <select id="edit_student" name="student_id">
                    @foreach($students as $s)
                        <option value="{{ $s->student_id }}">{{ $s->student_no }} - {{ $s->last_name }}, {{ $s->first_name }}</option>
                    @endforeach
                </select>
                <input id="edit_section" name="section_id" placeholder="Section ID" />
                <input id="edit_date" type="date" name="date_enrolled" />
                <input id="edit_status" name="status" placeholder="Status" />
                <input id="edit_grade" name="letter_grade" placeholder="Letter Grade" />
                <div></div>
            </div>
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('editEnrollmentModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteEnrollmentModal" class="modal" style="display:none">
    <div class="box">
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this enrollment?</p>
        <form id="deleteForm" method="POST" action="{{ url('enrollments') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" id="delete_id" name="id" />
            <div style="display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('deleteEnrollmentModal')" class="btn-secondary">Cancel</button>
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
    }).then(r=>r.json()).then(resp=> { if(resp && resp.message){ closeModal('createEnrollmentModal'); location.reload(); } else console.error(resp); });
});

// Edit
document.getElementById('editForm').addEventListener('submit', function(e){
    e.preventDefault();
    var id = document.getElementById('edit_id').value;
    var data = new FormData(this);
    fetch('/enrollments/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value, 'X-HTTP-Method-Override': 'PUT' },
        body: data
    }).then(r=>r.json()).then(resp=> { if(resp && resp.message){ closeModal('editEnrollmentModal'); location.reload(); } else console.error(resp); });
});

// Delete
document.getElementById('deleteForm').addEventListener('submit', function(e){
    e.preventDefault();
    var id = document.getElementById('delete_id').value;
    fetch('/enrollments/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value, 'X-HTTP-Method-Override': 'DELETE' }
    }).then(r=>r.json()).then(resp=> { if(resp && resp.message){ closeModal('deleteEnrollmentModal'); location.reload(); } else console.error(resp); });
});
</script>
