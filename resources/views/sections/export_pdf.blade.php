<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sections</title>
    <style>
        body{font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222}
        h2{font-weight:700; margin:0 0 12px 0}
        table{width:100%; border-collapse:collapse}
        th,td{padding:8px; border:1px solid #ddd; text-align:left}
        th{background:#f5f0ea}
    </style>
</head>
<body>
    @include('exports.partials.header', ['logoDataUri' => $logoDataUri ?? null])

    <h2>Sections</h2>

    @include('components.export_table', [
        'headers' => ['Section Code', 'Course', 'Term', 'Instructor', 'Room', 'Max Capacity'],
        'rows' => $sections,
        'columns' => [
            'section_code',
            ['relation' => 'course', 'field' => 'course_code'],
            ['relation' => 'term', 'field' => 'term_code'],
            ['relation' => 'instructor', 'field' => 'last_name'],
            ['relation' => 'room', 'field' => 'room_code'],
            'max_capacity'
        ]
    ])

    @include('exports.partials.footer')
</body>
</html>
