<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Enrollments</title>
    <style>
        body{font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222}
        h2{font-weight:700; margin:0 0 12px 0}
        table{width:100%; border-collapse:collapse}
        th,td{padding:8px; border:1px solid #ddd; text-align:left}
        th{background:#f5f0ea}
    </style>
</head>
<body>
    <h2>Enrollments</h2>
    <table>
        <thead>
            <tr><th>ID</th><th>Student</th><th>Section</th><th>Date</th><th>Status</th><th>Grade</th></tr>
        </thead>
        <tbody>
        @foreach($items as $i)
            <tr>
                <td>{{ $i->enrollment_id }}</td>
                <td>{{ optional($i->student)->student_no }} - {{ optional($i->student)->last_name }}</td>
                <td>{{ $i->section_id }}</td>
                <td>{{ $i->date_enrolled }}</td>
                <td>{{ $i->status }}</td>
                <td>{{ $i->letter_grade }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
