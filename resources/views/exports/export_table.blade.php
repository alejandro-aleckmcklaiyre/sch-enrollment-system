<div class="content">
    @if(!empty($filters))
    <div class="filters mb-4">
        <h4>Filter Settings</h4>
        <table class="table table-sm">
            <tbody>
                @foreach($filters as $label => $value)
                <tr>
                    <td style="width: 150px;"><strong>{{ $label }}:</strong></td>
                    <td>{{ $value }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(!empty($summary))
    <div class="summary mt-4">
        <table class="table table-sm">
            <tbody>
                @foreach($summary as $row)
                <tr>
                    <td style="width: 150px;"><strong>{{ $row[0] }}</strong></td>
                    <td>{{ $row[1] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<style>
    .content {
        padding: 20px;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
    }
    
    .table-bordered {
        border: 1px solid #dee2e6;
    }
    
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
        padding: 8px;
    }
    
    .table-sm th,
    .table-sm td {
        padding: 4px;
    }
    
    th {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: left;
    }
    
    .filters,
    .summary {
        margin: 20px 0;
    }
    
    .filters h4,
    .summary h4 {
        margin-bottom: 10px;
        font-size: 16px;
    }
    
    .mb-4 {
        margin-bottom: 1.5rem;
    }
    
    .mt-4 {
        margin-top: 1.5rem;
    }
</style>