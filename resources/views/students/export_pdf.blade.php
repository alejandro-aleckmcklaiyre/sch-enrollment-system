<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Students</title>
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
    {{-- DEBUG: logoDataUri present? {{ isset($logoDataUri) && $logoDataUri ? 'yes' : 'no' }} --}}

    <h2>Students</h2>

    @include('components.export_table', [
        'headers' => ['ID', 'Student No', 'Name', 'Email', 'Gender', 'Birthdate', 'Year'],
        'rows' => $students,
        'columns' => [
            'student_id',
            'student_no',
            ['callback' => function($s) { return $s->last_name . ', ' . $s->first_name; }],
            'email',
            'gender',
            'birthdate',
            'year_level'
        ]
    ])

    @include('exports.partials.footer')
</body>
</html>
