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
    <h2>Instructors</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Email</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody>
            @foreach($instructors as $i)
                <tr>
                    <td>{{ $i->instructor_id }}</td>
                    <td>{{ $i->last_name }}</td>
                    <td>{{ $i->first_name }}</td>
                    <td>{{ $i->email }}</td>
                    <td>{{ optional($i->department)->dept_name ?? $i->dept_id }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
