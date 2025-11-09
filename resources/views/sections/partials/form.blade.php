<!-- Section Form Modal Content -->
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">{{ isset($section) ? 'Edit Section' : 'Create Section' }}</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
        <form id="{{ isset($section) ? 'editSectionForm' : 'createSectionForm' }}" 
              action="{{ isset($section) ? route('sections.update', $section->id) : route('sections.store') }}" 
              method="POST">
            @csrf
            @if(isset($section))
                @method('PUT')
            @endif

            <div class="form-group">
                <label for="section_code">Section Code</label>
                <select name="section_code" id="section_code" class="form-control">
                    <option value="">Create New Code</option>
                    @foreach($existingSectionCodes as $code)
                        <option value="{{ $code }}">{{ $code }}</option>
                    @endforeach
                </select>
                <input type="text" class="form-control mt-2" id="new_section_code" name="new_section_code" placeholder="Enter new section code">
            </div>

            <div class="form-group">
                <label for="course_ids">Courses</label>
                <select name="course_ids[]" id="course_ids" class="form-control" multiple required>
                    @foreach($courses as $course)
                        <option value="{{ $course->course_id }}">
                            {{ $course->course_code }} - {{ $course->course_title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="term_id">Term</label>
                <select name="term_id" class="form-control" required>
                    <option value="">Select Term</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->term_id }}">{{ $term->term_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="instructor_id">Instructor</label>
                <select name="instructor_id" class="form-control" required>
                    <option value="">Select Instructor</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->instructor_id }}">
                            {{ $instructor->last_name }}, {{ $instructor->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="day_pattern">Schedule Pattern</label>
                <input type="text" name="day_pattern" class="form-control" required>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="room_id">Room</label>
                <select name="room_id" class="form-control" required>
                    <option value="">Select Room</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->room_id }}">{{ $room->room_code }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="max_capacity">Maximum Capacity</label>
                <input type="number" name="max_capacity" class="form-control" required min="1">
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </form>
    </div>
</div>