<div id="createCourseModal" class="modal">
    <div class="box">
        <h3>Create Course</h3>
        <form id="createCourseForm" method="POST" action="{{ url('courses') }}">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <input name="course_code" placeholder="Course Code" required>
                <input name="course_title" placeholder="Course Title" required>
                <input name="units" type="number" placeholder="Units">
                <input name="lecture_hours" type="number" placeholder="Lecture Hours">
                <input name="lab_hours" type="number" placeholder="Lab Hours">
                <select name="dept_id">
                    <option value="">Select Department</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->dept_id }}">{{ $d->dept_name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('createCourseModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Create</button>
            </div>
        </form>
    </div>
</div>

<div id="editCourseModal" class="modal">
    <div class="box">
        <h3>Edit Course</h3>
        <form id="editCourseForm" method="POST" action="{{ url('courses') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="course_id">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <input name="course_code" placeholder="Course Code" required>
                <input name="course_title" placeholder="Course Title" required>
                <input name="units" type="number" placeholder="Units">
                <input name="lecture_hours" type="number" placeholder="Lecture Hours">
                <input name="lab_hours" type="number" placeholder="Lab Hours">
                <select name="dept_id">
                    <option value="">Select Department</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->dept_id }}">{{ $d->dept_name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('editCourseModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteCourseModal" class="modal">
    <div class="box">
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this course?</p>
        <form id="deleteCourseForm" method="POST" action="{{ url('courses') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="course_id">
            <div style="display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('deleteCourseModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Delete</button>
            </div>
        </form>
    </div>
</div>

<div id="filterCourseModal" class="modal">
    <div class="box">
        <h3>Filter Courses</h3>
        <form method="GET" action="{{ url('courses') }}">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <select name="dept_id">
                    <option value="">All Departments</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->dept_id }}" {{ request('dept_id') == $d->dept_id ? 'selected' : '' }}>{{ $d->dept_name }}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('filterCourseModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Apply</button>
            </div>
        </form>
    </div>
</div>

<div id="restoreModal" class="modal">
    <div class="box">
        <h3>Restore Courses</h3>
        <form id="restoreForm" method="POST" action="{{ url('courses/restore') }}" enctype="multipart/form-data">
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
