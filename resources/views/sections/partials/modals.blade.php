<!-- Create Section Modal -->
<div id="createSectionModal" class="modal" style="display:none">
    <div class="box">
        <h3>Create Section</h3>
        <form id="createForm" method="POST" action="{{ url('sections') }}">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <input name="section_code" placeholder="Section Code" required>
                <select name="course_id" required>
                    @foreach($courses as $c)
                        <option value="{{ $c->course_id }}">{{ $c->course_code }} - {{ $c->course_title }}</option>
                    @endforeach
                </select>
                <select name="term_id" required>
                    @foreach($terms as $t)
                        <option value="{{ $t->term_id }}">{{ $t->term_code }}</option>
                    @endforeach
                </select>
                <select name="instructor_id">
                    <option value="">--</option>
                    @foreach($instructors as $i)
                        <option value="{{ $i->instructor_id }}">{{ $i->last_name }}, {{ $i->first_name }}</option>
                    @endforeach
                </select>
                <select name="room_id">
                    <option value="">--</option>
                    @foreach($rooms as $r)
                        <option value="{{ $r->room_id }}">{{ $r->room_code }}</option>
                    @endforeach
                </select>
                <input name="day_pattern" placeholder="Day Pattern">
                <input name="start_time" type="time">
                <input name="end_time" type="time">
                <input name="max_capacity" type="number" min="0" placeholder="Max Capacity">
            </div>
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
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
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                <input name="section_code" placeholder="Section Code" required>
                <select name="course_id" required>
                    @foreach($courses as $c)
                        <option value="{{ $c->course_id }}">{{ $c->course_code }} - {{ $c->course_title }}</option>
                    @endforeach
                </select>
                <select name="term_id" required>
                    @foreach($terms as $t)
                        <option value="{{ $t->term_id }}">{{ $t->term_code }}</option>
                    @endforeach
                </select>
                <select name="instructor_id">
                    <option value="">--</option>
                    @foreach($instructors as $i)
                        <option value="{{ $i->instructor_id }}">{{ $i->last_name }}, {{ $i->first_name }}</option>
                    @endforeach
                </select>
                <select name="room_id">
                    <option value="">--</option>
                    @foreach($rooms as $r)
                        <option value="{{ $r->room_id }}">{{ $r->room_code }}</option>
                    @endforeach
                </select>
                <input name="day_pattern" placeholder="Day Pattern">
                <input name="start_time" type="time">
                <input name="end_time" type="time">
                <input name="max_capacity" type="number" min="0" placeholder="Max Capacity">
            </div>
            <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end">
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
        <form method="POST" action="{{ url('sections') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="section_id">
            <div style="display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('deleteSectionModal')" class="btn-secondary">Cancel</button>
                <button type="submit">Delete</button>
            </div>
        </form>
    </div>
</div>
