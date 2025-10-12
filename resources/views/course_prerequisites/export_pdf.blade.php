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
<h2>Course Prerequisites</h2>
<table>
    <thead><tr><th>Course</th><th>Prerequisite</th></tr></thead>
    <tbody>
    @foreach($prereqs as $p)
        <tr><td>{{ optional($p->course)->course_code }}</td><td>{{ optional($p->prereq)->course_code }}</td></tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
