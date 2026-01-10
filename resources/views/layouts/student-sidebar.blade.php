<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('student.dashboard')) active @endif" href="{{ route('student.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('student.enrollments')) active @endif" href="{{ route('student.enrollments') }}">
            <i class="fas fa-clipboard-list"></i> My Enrollments
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('student.schedule')) active @endif" href="{{ route('student.schedule') }}">
            <i class="fas fa-calendar-alt"></i> Schedule
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('student.courses*')) active @endif" href="{{ route('student.courses') }}">
            <i class="fas fa-book"></i> Course Catalog
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('student.transcript')) active @endif" href="{{ route('student.transcript') }}">
            <i class="fas fa-graduation-cap"></i> Transcript
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('student.progress')) active @endif" href="{{ route('student.progress') }}">
            <i class="fas fa-chart-line"></i> Progress
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('student.announcements')) active @endif" href="{{ route('student.announcements') }}">
            <i class="fas fa-bell"></i> Announcements
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('student.profile')) active @endif" href="{{ route('student.profile') }}">
            <i class="fas fa-user"></i> Profile
        </a>
    </li>
</ul>
