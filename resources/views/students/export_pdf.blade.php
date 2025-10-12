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
    <h2>Students</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Student No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Birthdate</th>
                <th>Year</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $s)
                <tr>
                    <td>{{ $s->student_id }}</td>
                    <td>{{ $s->student_no }}</td>
                    <td>{{ $s->last_name }}, {{ $s->first_name }}</td>
                    <td>{{ $s->email }}</td>
                    <td>{{ $s->gender }}</td>
                    <td>{{ $s->birthdate }}</td>
                    <td>{{ $s->year_level }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
