<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Courses</title>
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

    <h2>Courses</h2>

    @include('components.export_table', [
        'headers' => ['ID', 'Course Code', 'Course Title', 'Units', 'Lecture Hrs', 'Lab Hrs', 'Department'],
        'rows' => $courses,
        'columns' => [
            'course_id',
            'course_code',
            'course_title',
            'units',
            'lecture_hours',
            'lab_hours',
            ['callback' => function($c) { return optional($c->department)->dept_name ?? $c->dept_id; }]
        ]
    ])

    @include('exports.partials.footer')
</body>
</html>
