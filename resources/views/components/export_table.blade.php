{{-- 
    Shared export table component
    Usage: @include('components.export_table', [
        'headers' => ['ID', 'Name', ...],
        'rows' => $records,
        'columns' => ['id', 'name', ...] 
    ])
--}}
<style>
    .export-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
        font-family: DejaVu Sans, Arial, sans-serif;
    }
    .export-table th {
        background: #f5f0ea;
        font-weight: bold;
        text-align: left;
        padding: 8px;
        border: 1px solid #ddd;
        font-size: 11px;
    }
    .export-table td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: left;
        font-size: 11px;
    }
    .export-table thead {
        display: table-header-group;
    }
</style>

<table class="export-table">
    <thead>
        <tr>
            @foreach($headers as $header)
                <th>{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            <tr>
                @foreach($columns as $column)
                    <td>
                        @if(is_array($column))
                            @if(isset($column['relation']))
                                {{ optional($row->{$column['relation']})->{$column['field']} }}
                            @elseif(isset($column['callback']))
                                {{ $column['callback']($row) }}
                            @endif
                        @else
                            @if(in_array($column, ['start_date', 'end_date']) && $row->{$column})
                                {{ Carbon\Carbon::parse($row->{$column})->format('m/d/Y') }}
                            @else
                                {{ $row->{$column} }}
                            @endif
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>