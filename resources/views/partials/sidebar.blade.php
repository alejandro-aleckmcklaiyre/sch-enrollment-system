<div style="display:flex; flex-direction:column; gap:12px;">
    @auth
        @if(auth()->user()->isAdmin())
            <div style="font-weight:700; font-size:18px; margin-bottom:8px">ADMIN PANEL</div>
            <nav style="display:flex; flex-direction:column; gap:6px">
                <a href="{{ route('admin.dashboard') }}" style="color:var(--text); text-decoration:none; padding:8px 6px; display:block; font-weight:600">Dashboard</a>
                <a href="/admin/students" style="color:var(--text); text-decoration:none; padding:8px 6px; display:block">Students</a>
                <a href="/admin/programs" style="color:var(--text); text-decoration:none; padding:8px 6px; display:block">Programs</a>
                <a href="/admin/courses" style="color:var(--text); text-decoration:none; padding:8px 6px; display:block">Courses</a>
                <a href="/admin/instructors" style="color:var(--text); text-decoration:none; padding:8px 6px; display:block">Instructors</a>
                <a href="/admin/rooms" style="color:var(--text); text-decoration:none; padding:8px 6px; display:block">Rooms</a>
                <a href="/admin/departments" style="color:var(--text); text-decoration:none; padding:8px 6px; display:block">Departments</a>
                <a href="/admin/enrollments" style="color:var(--text); text-decoration:none; padding:8px 6px; display:block">Enrollments</a>
                <a href="/admin/sections" style="color:var(--text); text-decoration:none; padding:8px 6px; display:block">Sections</a>
                <a href="/admin/terms" style="color:var(--text); text-decoration:none; padding:8px 6px; display:block">Terms</a>
                <a href="/admin/course-prerequisites" style="color:var(--text); text-decoration:none; padding:8px 6px; display:block">Course Prerequisites</a>
            </nav>
        @elseif(auth()->user()->isFaculty())
            @include('layouts.faculty-sidebar')
        @elseif(auth()->user()->isStudent())
            @include('layouts.student-sidebar')
        @endif
    @endauth
</div>
