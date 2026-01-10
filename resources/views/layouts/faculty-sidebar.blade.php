<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('faculty.dashboard')) active @endif" href="{{ route('faculty.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('faculty.sections*')) active @endif" href="{{ route('faculty.sections') }}">
            <i class="fas fa-layer-group"></i> My Sections
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('faculty.schedule')) active @endif" href="{{ route('faculty.schedule') }}">
            <i class="fas fa-calendar-alt"></i> Schedule
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('faculty.courses')) active @endif" href="{{ route('faculty.courses') }}">
            <i class="fas fa-book"></i> Courses
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('faculty.reports')) active @endif" href="{{ route('faculty.reports') }}">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link @if(request()->routeIs('faculty.profile')) active @endif" href="{{ route('faculty.profile') }}">
            <i class="fas fa-user"></i> Profile
        </a>
    </li>
</ul>
