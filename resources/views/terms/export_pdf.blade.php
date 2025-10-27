<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Terms</title>
    <style>
        body{font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222}
        h2{font-weight:700; margin:0 0 12px 0}
        table{width:100%; border-collapse:collapse}
        th,td{padding:8px; border:1px solid #ddd; text-align:left}
        th{background:#f5f0ea}
        .export-header{ margin-bottom:8px }
    </style>
</head>
<body>
    @include('exports.partials.header', ['logoDataUri' => $logoDataUri ?? null])

    <h2>Term Records</h2>

    @include('components.export_table', [
        'headers' => ['Term Code', 'Start Date', 'End Date'],
        'rows' => $terms,
        'columns' => [
            'term_code',
            'start_date',
            'end_date'
        ]
    ])

    @include('exports.partials.footer')
</body>
</html>
