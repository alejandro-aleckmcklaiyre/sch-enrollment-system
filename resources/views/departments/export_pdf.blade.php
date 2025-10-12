<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Departments</title>
    <style>
        body{font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222}
        h2{font-weight:700; margin:0 0 12px 0}
        table{width:100%; border-collapse:collapse}
        th,td{padding:8px; border:1px solid #ddd; text-align:left}
        th{background:#f5f0ea}
    </style>
</head>
<body>
    <h2>Departments</h2>
    <table>
        <thead>
            <tr><th>ID</th><th>Code</th><th>Name</th></tr>
        </thead>
        <tbody>
        @foreach($items as $i)
            <tr>
                <td>{{ $i->dept_id }}</td>
                <td>{{ $i->dept_code }}</td>
                <td>{{ $i->dept_name }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
