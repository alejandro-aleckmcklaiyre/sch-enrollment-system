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
    @include('exports.partials.header')

    <h2>Terms</h2>
    <table>
        <thead><tr><th>Term Code</th><th>Start Date</th><th>End Date</th></tr></thead>
        <tbody>
        @foreach($terms as $t)
            <tr><td>{{ $t->term_code }}</td><td>{{ $t->start_date }}</td><td>{{ $t->end_date }}</td></tr>
        @endforeach
        </tbody>
    </table>

    @include('exports.partials.footer')
</body>
</html>
