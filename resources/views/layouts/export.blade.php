<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('title')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .university-name {
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .date-created {
            color: #006400;  /* Dark green color */
            font-size: 11px;
            margin: 4px 0;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
        }

        th {
            background-color: #f5f5f5;
        }

        @page {
            margin: 25px 25px 60px 25px;
        }
    </style>
</head>
<body>
    <div class="university-name">
        Polytechnic University of the Philippines - Taguig Campus
    </div>
    <div class="date-created">
        Date Created: {{ date(config('export.date_format', 'F j, Y')) }}
    </div>
    <div class="report-title">
        @yield('title')
    </div>

    @yield('content')

    @yield('content')
</body>
</html>