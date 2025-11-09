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
                <div>
                    <label for="is_irregular" style="display:block"><input type="checkbox" id="is_irregular" name="is_irregular" value="1"> Irregular Enrollment</label>
                    <select name="section_code" id="section_code_create">
                        <option value="">Select Section</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec->section_code }}">{{ $sec->section_code }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="date" name="date_enrolled" />
                <select name="status">
                    @foreach($statuses as $st)
                        <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
                <input name="letter_grade" placeholder="Letter Grade" />
                <div></div>
            </div>
            <div class="form-group course-selection" style="display: none; margin-top:12px">
                <div class="course-selection-header">
                    <label style="font-weight:500">Select Courses for Irregular Enrollment</label>
                    <span class="course-selection-count" id="selected_course_count">No courses selected</span>
                    <button type="button" class="select-all-courses" onclick="toggleAllCourses()">Select All</button>
                </div>
                <div id="course_checklist" class="course-checklist">
                    <!-- Course checkboxes will be dynamically added here -->
                </div>
                <div class="help-text" style="margin-top:8px">Click anywhere on a course card to select/deselect it.</div>
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
                <select id="edit_section" name="section_id">
                    <option value="">Select Section</option>
                    @foreach($sectionRows as $sec)
                        <option value="{{ $sec->section_id }}">{{ $sec->section_code }} - {{ optional($sec->course)->course_code }}</option>
                    @endforeach
                </select>
                <input id="edit_date" type="date" name="date_enrolled" />
                <select id="edit_status" name="status">
                    @foreach($statuses as $st)
                        <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
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
// Create
document.getElementById('createForm').addEventListener('submit', function(e){
    e.preventDefault();
    
    // Validate irregular enrollment
    const isIrregular = document.getElementById('is_irregular').checked;
    const selectedCourses = document.querySelectorAll('#course_checklist input[type="checkbox"]:checked');
    
    if (isIrregular && selectedCourses.length === 0) {
        alert('Please select at least one course for irregular enrollment');
        return;
    }
    
    var data = new FormData(this);
    
    // Debug log the form data
    console.log('Submitting enrollment with data:');
    for (var pair of data.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    fetch(this.action, {
        method: 'POST',
        headers: { 
            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
            'Accept': 'application/json'
        },
        body: data
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || 'Server error');
        }
        return data;
    })
    .then(resp => { 
        handleResponse(resp, 'createEnrollmentModal'); 
    })
    .catch(error => {
        console.error('Enrollment error:', error);
        alert(error.message || 'Failed to create enrollment. Please try again.');
    });
});

// Handle status changes and irregular course selection
document.getElementById('is_irregular').addEventListener('change', function(){
    const checked = this.checked;
    document.querySelector('.course-selection').style.display = checked ? 'block' : 'none';
    document.getElementById('course_ids_create').required = checked;
    // Also set status to irregular when checkbox is checked
    if (checked) {
        document.querySelector('select[name="status"]').value = 'irregular';
    } else {
        document.querySelector('select[name="status"]').value = 'enrolled';
    }
});

// Update selected course count
function updateSelectedCount() {
    const count = document.querySelectorAll('#course_checklist input[type="checkbox"]:checked').length;
    const countText = count === 0 ? 'No courses selected' : 
                     count === 1 ? '1 course selected' : 
                     `${count} courses selected`;
    document.getElementById('selected_course_count').textContent = countText;
}

// Toggle all courses
function toggleAllCourses() {
    const checkboxes = document.querySelectorAll('#course_checklist input[type="checkbox"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
        cb.closest('.course-checklist-item').classList.toggle('selected', !allChecked);
    });
    updateSelectedCount();
}

// Load courses for create form when section_code changes
document.getElementById('section_code_create').addEventListener('change', function(){
    const sectionCode = this.value;
    const courseChecklist = document.getElementById('course_checklist');
    courseChecklist.innerHTML = '';
    if (!sectionCode) return;
    
    // Show loading indicator
    courseChecklist.innerHTML = '<div class="course-details" style="text-align:center;padding:12px;">Loading courses...</div>';
    
    fetch(`/enrollments/sections/${encodeURIComponent(sectionCode)}/courses`)
        .then(r => {
            if (!r.ok) throw new Error(r.statusText);
            return r.json();
        })
        .then(resp => {
            courseChecklist.innerHTML = ''; // Clear loading message
            
            if (!resp.courses || !resp.courses.length) {
                courseChecklist.innerHTML = '<div class="course-details" style="text-align:center;padding:12px;">No courses found for this section</div>';
                return;
            }
            
            resp.courses.forEach(function(item){
                const div = document.createElement('div');
                div.className = 'course-checklist-item';
                
                const input = document.createElement('input');
                input.type = 'checkbox';
                input.name = 'course_section_row_ids[]';
                input.value = item.section_id;
                input.id = `course_${item.section_id}`;
                
                // Add change handler to update UI
                input.addEventListener('change', function() {
                    div.classList.toggle('selected', this.checked);
                    updateSelectedCount();
                });
                
                const info = document.createElement('div');
                info.className = 'course-info';
                
                const title = document.createElement('div');
                title.className = 'course-title';
                title.textContent = `${item.course_code} - ${item.course_title}`;
                
                const details = document.createElement('div');
                details.className = 'course-details';
                details.textContent = [
                    item.instructor_name,
                    item.room_code,
                    `${item.day_pattern || ''} ${item.start_time || ''}-${item.end_time || ''}`
                ].filter(Boolean).join(' | ');
                
                info.appendChild(title);
                info.appendChild(details);
                div.appendChild(input);
                div.appendChild(info);
                
                // Make the whole card clickable
                div.addEventListener('click', function(e) {
                    if (e.target !== input) { // Don't trigger if checkbox itself was clicked
                        input.checked = !input.checked;
                        input.dispatchEvent(new Event('change')); // Trigger change event for UI update
                    }
                });
                
                courseChecklist.appendChild(div);
            });
            updateSelectedCount();
        })
        .catch(error => {
            console.error('Error loading courses:', error);
            courseChecklist.innerHTML = '<div class="course-details" style="text-align:center;padding:12px;color:#ef4444;">Error loading courses. Please try again.</div>';
        });
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
    }).then(r=>r.json()).then(resp=> { handleResponse(resp,'editEnrollmentModal'); });
});

// Delete
document.getElementById('deleteForm').addEventListener('submit', function(e){
    e.preventDefault();
    var id = document.getElementById('delete_id').value;
    fetch('/enrollments/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value, 'X-HTTP-Method-Override': 'DELETE' }
    }).then(r=>r.json()).then(resp=> { handleResponse(resp,'deleteEnrollmentModal'); });
});
</script>
