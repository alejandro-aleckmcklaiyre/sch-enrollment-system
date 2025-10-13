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

<div id="deletePrereqModal" class="modal" style="display:none">
    <div class="box">
        <h3>Delete Prerequisite</h3>
        <p>Are you sure?</p>
        <form id="deleteForm" method="POST" action="{{ url('course-prerequisites') }}">@csrf @method('DELETE')
            <input type="hidden" name="composite_id">
            <div style="display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('deletePrereqModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Delete</button>
            </div>
        </form>
    </div>

<script>
document.getElementById('deleteForm').addEventListener('submit', function(e){
    e.preventDefault();
    var composite = this.querySelector('[name=composite_id]').value;
    if(!composite) return;
    // composite is 'course:prereq'
    fetch('/course-prerequisites/' + encodeURIComponent(composite), {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value, 'X-HTTP-Method-Override': 'DELETE' }
    }).then(r=>r.json()).then(resp=>{ handleResponse(resp,'deletePrereqModal'); });
});
</script>
</div>
