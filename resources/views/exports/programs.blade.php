@extends('layouts.export')

@section('title', 'Program Records')

@section('content')
    @include('exports.export_table', [
        'headers' => ['ID', 'Program Code', 'Program Name', 'Department'],
        'data' => $records->map(function($item) {
            return [
                $item->program_id,
                $item->program_code,
                $item->program_name,
                $item->department->dept_name ?? 'N/A'
            ];
        }),
        'summary' => [
            ['Total Programs:', $totals['count']]
        ],
        'filters' => [
            'Search' => $search ?? 'None',
            'Sort By' => $sort_by ?? 'program_id',
            'Sort Direction' => $sort_dir ?? 'asc',
            'Filtered' => $filtered ? 'Yes' : 'No'
        ]
    ])
@endsection