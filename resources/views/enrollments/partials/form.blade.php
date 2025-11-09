<!-- Enrollment Form Modal Content -->
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">{{ isset($enrollment) ? 'Edit Enrollment' : 'Create Enrollment' }}</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
        <form id="{{ isset($enrollment) ? 'editEnrollmentForm' : 'createEnrollmentForm' }}" 
              action="{{ isset($enrollment) ? route('enrollments.update', $enrollment->id) : route('enrollments.store') }}" 
              method="POST">
            @csrf
            @if(isset($enrollment))
                @method('PUT')
            @endif

            <div class="form-group">
                <label for="student_id">Student</label>
                <select name="student_id" class="form-control" required {{ isset($enrollment) ? 'disabled' : '' }}>
                    <option value="">Select Student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->student_id }}">
                            {{ $student->student_no }} - {{ $student->last_name }}, {{ $student->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="is_irregular" name="is_irregular" value="1">
                <label class="form-check-label" for="is_irregular">Irregular Enrollment</label>
            </div>

            <div class="form-group">
                <label for="section_code">Section</label>
                <select name="section_code" id="section_code" class="form-control" required>
                    <option value="">Select Section</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->section_code }}">{{ $section->section_code }}</option>
                    @endforeach
                </select>
            </div>

                <div class="form-group course-selection" style="display: none;">
                <label for="course_ids">Courses (select specific courses for irregular enrollment)</label>
                <select name="course_section_row_ids[]" id="course_ids" class="form-control" multiple>
                    <!-- Courses will be loaded dynamically; values are section row ids -->
                </select>
            </div>

            @if(isset($enrollment))
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control" required>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ $enrollment->status == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Handle irregular enrollment checkbox
    $('#is_irregular').change(function() {
        $('.course-selection').toggle(this.checked);
        $('#course_ids').prop('required', this.checked);
    });

    // Handle section change
    $('#section_code').change(function() {
        const sectionCode = $(this).val();
        if (sectionCode) {
            $.get(`/enrollments/sections/${encodeURIComponent(sectionCode)}/courses`, function(response) {
                const courseSelect = $('#course_ids');
                courseSelect.empty();
                
                response.courses.forEach(function(item) {
                    // item should contain: section_id, course_id, course_code, course_title, instructor_name, room_code, day_pattern, start_time, end_time
                    const label = `${item.course_code} - ${item.course_title} | ${item.instructor_name || ''} | ${item.day_pattern || ''} ${item.start_time || ''}-${item.end_time || ''} | ${item.room_code || ''}`;
                    courseSelect.append(new Option(label, item.section_id));
                });
            });
        }
    });

    // Handle section code selection/input
    $('#section_code').change(function() {
        const newCodeInput = $('#new_section_code');
        if ($(this).val()) {
            newCodeInput.hide().val('');
        } else {
            newCodeInput.show();
        }
    });
});
</script>
@endpush