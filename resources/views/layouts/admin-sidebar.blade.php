<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('admin.dashboard')) active @endif" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('admin.students.*')) active @endif" href="{{ route('admin.students.index') }}">
            <i class="fas fa-graduation-cap"></i> Students
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('admin.instructors.*')) active @endif" href="{{ route('admin.instructors.index') }}">
            <i class="fas fa-chalkboard-user"></i> Faculty
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('admin.courses.*')) active @endif" href="{{ route('admin.courses.index') }}">
            <i class="fas fa-book"></i> Courses
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('admin.departments.*')) active @endif" href="{{ route('admin.departments.index') }}">
            <i class="fas fa-building"></i> Departments
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('admin.programs.*')) active @endif" href="{{ route('admin.programs.index') }}">
            <i class="fas fa-list"></i> Programs
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('admin.sections.*')) active @endif" href="{{ route('admin.sections.index') }}">
            <i class="fas fa-layer-group"></i> Sections
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('admin.enrollments.*')) active @endif" href="{{ route('admin.enrollments.index') }}">
            <i class="fas fa-clipboard-list"></i> Enrollments
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('admin.terms.*')) active @endif" href="{{ route('admin.terms.index') }}">
            <i class="fas fa-calendar-alt"></i> Terms
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('admin.rooms.*')) active @endif" href="{{ route('admin.rooms.index') }}">
            <i class="fas fa-door-open"></i> Rooms
        </a>
    </li>
</ul>
