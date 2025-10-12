<html>
<head>
    <meta charset="utf-8">
    <title>Rooms</title>
    <style>
        body{font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222}
        h2{font-weight:700; margin:0 0 12px 0}
        table{width:100%; border-collapse:collapse}
        th,td{padding:8px; border:1px solid #ddd; text-align:left}
        th{background:#f5f0ea}
    </style>
</head>
<body>
    <h2>Rooms</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Building</th>
                <th>Room Code</th>
                <th>Capacity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rooms as $r)
                <tr>
                    <td>{{ $r->room_id }}</td>
                    <td>{{ $r->building }}</td>
                    <td>{{ $r->room_code }}</td>
                    <td>{{ $r->capacity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
