<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Programs</title>
    <style>
        body{font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222}
        h2{font-weight:700; margin:0 0 12px 0}
        table{width:100%; border-collapse:collapse}
        th,td{padding:8px; border:1px solid #ddd; text-align:left}
        th{background:#f5f0ea}
    </style>
</head>
<body>
    <h2>Programs</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Program Code</th>
                <th>Program Name</th>
                <th>Dept ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach($programs as $p)
                <tr>
                    <td>{{ $p->program_id }}</td>
                    <td>{{ $p->program_code }}</td>
                    <td>{{ $p->program_name }}</td>
                    <td>{{ $p->dept_id }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
