@extends('layouts.app')

@section('title','Course Prerequisites')

@section('toolbar')
                    @endif
                @endforeach
                @foreach(request()->except(['page','sort_by','sort_dir']) as $k => $v)
                    @if(is_array($v))
                        @foreach($v as $item)
                <select name="sort_by" class="form-control">
                    <option value="course_id" {{ request('sort_by')=='course_id' ? 'selected' : '' }}>Course</option>
                    <option value="prereq_course_id" {{ request('sort_by')=='prereq_course_id' ? 'selected' : '' }}>Prerequisite</option>
                </select>
                <select name="sort_dir" class="form-control">
                    <option value="desc" {{ request('sort_dir','desc')=='desc' ? 'selected' : '' }}>DESC</option>
                    <option value="asc" {{ request('sort_dir')=='asc' ? 'selected' : '' }}>ASC</option>
                </select>
                <button type="submit" class="btn btn-secondary">Sort</button>
            </form>

            <form method="POST" action="{{ url('course-prerequisites/export-excel') }}" style="display:inline">@csrf
                <input type="hidden" name="search" value="{{ request('search') }}"><button>Export CSV</button>
            </form>
            <form method="GET" action="{{ url('course-prerequisites/export-pdf') }}" style="display:inline">
                <input type="hidden" name="search" value="{{ request('search') }}"><button type="submit" class="btn-secondary">Export PDF</button>
            </form>
        </div>
    </div>
    @include('partials.table-header', [
        'listUrl' => url('course-prerequisites'),
        'exportExcelUrl' => url('course-prerequisites/export-excel'),
        'exportPdfUrl' => url('course-prerequisites/export-pdf'),
        'manageModalId' => 'createPrereqModal',
        'filterModalId' => 'filterPrereqModal',
        'sortFields' => [
            ['value' => 'course_id', 'label' => 'Course'],
            ['value' => 'prereq_course_id', 'label' => 'Prerequisite'],
        ]
    ])
    @endsection
@endsection

@section('content')
    <table>
        <thead>
            <tr>
                <th>@include('partials._sortable_header', ['label'=>'Course','field'=>'course_id'])</th>
                <th>@include('partials._sortable_header', ['label'=>'Prerequisite','field'=>'prereq_course_id'])</th>
                <th style="text-align:left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prereqs as $p)
                <tr>
                    <td>{{ optional($p->course)->course_code }}</td>
                    <td>{{ optional($p->prereq)->course_code }}</td>
                    <td style="display:flex; gap:8px; justify-content:flex-start; align-items:center;">
                        <button onclick="openPrereqDelete('{{ $p->course_id }}:{{ $p->prereq_course_id }}')" class="btn-secondary">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:12px">@include('partials.pagination',['paginator'=>$prereqs])</div>
@endsection

@push('modals')@include('course_prerequisites.partials.modals')@endpush

@push('scripts')
<script>
function openPrereqDelete(composite){ const modal=document.getElementById('deletePrereqModal'); modal.style.display='flex'; modal.querySelector('[name="composite_id"]').value=composite; }
</script>
@endpush
