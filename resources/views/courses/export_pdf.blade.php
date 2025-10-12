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
    <h2>Courses</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Units</th>
                <th>Lecture Hrs</th>
                <th>Lab Hrs</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses as $c)
                <tr>
                    <td>{{ $c->course_id }}</td>
                    <td>{{ $c->course_code }}</td>
                    <td>{{ $c->course_title }}</td>
                    <td>{{ $c->units }}</td>
                    <td>{{ $c->lecture_hours }}</td>
                    <td>{{ $c->lab_hours }}</td>
                    <td>{{ optional($c->department)->dept_name ?? $c->dept_id }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
