<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Instructors</title>
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

    <h2>Instructors</h2>

    @include('components.export_table', [
        'headers' => ['ID', 'Last Name', 'First Name', 'Email', 'Department'],
        'rows' => $instructors,
        'columns' => [
            'instructor_id',
            'last_name',
            'first_name',
            'email',
            ['callback' => function($i) { return optional($i->department)->dept_name ?? $i->dept_id; }]
        ]
    ])

    @include('exports.partials.footer')
</body>
</html>
