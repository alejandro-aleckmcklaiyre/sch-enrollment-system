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
<h2>Sections</h2>
<table>
    <thead>
    <tr><th>Section Code</th><th>Course</th><th>Term</th><th>Instructor</th><th>Room</th><th>Max Capacity</th></tr>
    </thead>
    <tbody>
    @foreach($sections as $s)
        <tr>
            <td>{{ $s->section_code }}</td>
            <td>{{ optional($s->course)->course_code }}</td>
            <td>{{ optional($s->term)->term_code }}</td>
            <td>{{ optional($s->instructor)->last_name }}</td>
            <td>{{ optional($s->room)->room_code }}</td>
            <td>{{ $s->max_capacity }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
