@php
// Usage: @include('partials._sortable_header', ['label'=>'Student No','field'=>'student_no'])
    $label = $label ?? '';
    $field = $field ?? null;

    // Build query preserving current filters/search but removing pagination
    $query = request()->except('page');

    // Current sort state
    $currentSort = request()->query('sort_by', request()->input('sort_by'));
    $currentDir = request()->query('sort_dir', request()->input('sort_dir', 'asc'));
    $isActive = ($currentSort === $field);

    // Toggle direction when clicking the same active column
    $nextDir = ($isActive && strtolower($currentDir) === 'asc') ? 'desc' : 'asc';

    // Set requested sort into the query
    if ($field) {
        $query['sort_by'] = $field;
        $query['sort_dir'] = $nextDir;
    }

    $url = url()->current() . (count($query) ? ('?' . http_build_query($query)) : '');
@endphp

<a href="{{ $url }}" class="sortable-link">
    <span>{{ $label }}</span>
    @if($isActive)
        <small>{{ strtolower($currentDir) === 'asc' ? '▲' : '▼' }}</small>
    @endif
</a>
