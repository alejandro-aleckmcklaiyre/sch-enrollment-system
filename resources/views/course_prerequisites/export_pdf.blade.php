<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Course Prerequisites</title>
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

    <h2>Course Prerequisites</h2>

    @include('components.export_table', [
        'headers' => ['Course', 'Prerequisite'],
        'rows' => $prereqs,
        'columns' => [
            ['relation' => 'course', 'field' => 'course_code'],
            ['relation' => 'prereq', 'field' => 'course_code']
        ]
    ])

    @include('exports.partials.footer')
</body>
</html>
