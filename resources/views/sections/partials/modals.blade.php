<!-- Create Section Modal -->
<div id="createSectionModal" class="modal" style="display:none">
    <div class="box">
        <h3>Create Section</h3>
        <form id="createForm" method="POST" action="{{ url('sections') }}">
            @csrf
            <div style="display:grid; gap:12px">
                <div class="form-group">
                    <label for="create_section_code">Section Code</label>
                    <input id="create_section_code" name="section_code" required>
                </div>

                <div class="form-group">
                    <label for="create_course_id">Course</label>
                    <select id="create_course_id" name="course_id" required>
                        @foreach($courses as $c)
                            <option value="{{ $c->course_id }}">{{ $c->course_code }} - {{ $c->course_title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="create_term_id">Term</label>
                    <select id="create_term_id" name="term_id" required>
                        @foreach($terms as $t)
                            <option value="{{ $t->term_id }}">{{ $t->term_code }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="create_instructor_id">Instructor</label>
                    <select id="create_instructor_id" name="instructor_id">
                        <option value="">-- Select Instructor --</option>
                        @foreach($instructors as $i)
                            <option value="{{ $i->instructor_id }}">{{ $i->last_name }}, {{ $i->first_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="create_room_id">Room</label>
                    <select id="create_room_id" name="room_id">
                        <option value="">-- Select Room --</option>
                        @foreach($rooms as $r)
                            <option value="{{ $r->room_id }}">{{ $r->room_code }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="create_day_pattern">Day Pattern</label>
                    <input id="create_day_pattern" name="day_pattern">
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                    <div class="form-group">
                        <label for="create_start_time">Start Time</label>
                        <input id="create_start_time" name="start_time" type="time">
                    </div>

                    <div class="form-group">
                        <label for="create_end_time">End Time</label>
                        <input id="create_end_time" name="end_time" type="time">
                    </div>
                </div>

                <div class="form-group">
                    <label for="create_max_capacity">Maximum Capacity</label>
                    <input id="create_max_capacity" name="max_capacity" type="number" min="0">
                </div>
            </div>
            <div style="margin-top:16px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('createSectionModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Section Modal -->
<div id="editSectionModal" class="modal" style="display:none">
    <div class="box">
        <h3>Edit Section</h3>
        <form method="POST" action="{{ url('sections') }}" id="editForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="section_id">
            <div style="display:grid; gap:12px">
                <div class="form-group">
                    <label for="edit_section_code">Section Code</label>
                    <input id="edit_section_code" name="section_code" required>
                </div>

                <div class="form-group">
                    <label for="edit_course_id">Course</label>
                    <select id="edit_course_id" name="course_id" required>
                        @foreach($courses as $c)
                            <option value="{{ $c->course_id }}">{{ $c->course_code }} - {{ $c->course_title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_term_id">Term</label>
                    <select id="edit_term_id" name="term_id" required>
                        @foreach($terms as $t)
                            <option value="{{ $t->term_id }}">{{ $t->term_code }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_instructor_id">Instructor</label>
                    <select id="edit_instructor_id" name="instructor_id">
                        <option value="">-- Select Instructor --</option>
                        @foreach($instructors as $i)
                            <option value="{{ $i->instructor_id }}">{{ $i->last_name }}, {{ $i->first_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_room_id">Room</label>
                    <select id="edit_room_id" name="room_id">
                        <option value="">-- Select Room --</option>
                        @foreach($rooms as $r)
                            <option value="{{ $r->room_id }}">{{ $r->room_code }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_day_pattern">Day Pattern</label>
                    <input id="edit_day_pattern" name="day_pattern">
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                    <div class="form-group">
                        <label for="edit_start_time">Start Time</label>
                        <input id="edit_start_time" name="start_time" type="time">
                    </div>

                    <div class="form-group">
                        <label for="edit_end_time">End Time</label>
                        <input id="edit_end_time" name="end_time" type="time">
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_max_capacity">Maximum Capacity</label>
                    <input id="edit_max_capacity" name="max_capacity" type="number" min="0">
                </div>
            </div>
            <div style="margin-top:16px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('editSectionModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Section Modal -->
<div id="deleteSectionModal" class="modal" style="display:none">
    <div class="box">
        <h3>Delete Section</h3>
        <p>Are you sure you want to delete this section?</p>
        <form id="deleteSectionForm" method="POST" action="{{ url('sections') }}">
            @csrf
            <input type="hidden" id="delete_section_id" name="section_id">
            <div style="display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('deleteSectionModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Delete</button>
            </div>
        </form>
    </div>
</div>