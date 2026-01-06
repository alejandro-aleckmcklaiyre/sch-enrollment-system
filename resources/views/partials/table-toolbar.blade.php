@php
    $listUrl = $listUrl ?? url()->current();
    $exportExcelUrl = $exportExcelUrl ?? $listUrl . '/export-excel';
    $exportPdfUrl = $exportPdfUrl ?? $listUrl . '/export-pdf';
    $backupUrl = $backupUrl ?? $listUrl . '/backup';
    $restoreUrl = $restoreUrl ?? $listUrl . '/restore';
    $manageModalId = $manageModalId ?? 'createModal';
    $filterModalId = $filterModalId ?? 'filterModal';
    $restoreModalId = $restoreModalId ?? 'restoreModal';
@endphp

<div class="toolbar" style="justify-content:flex-end; display:flex; gap:8px; align-items:center;">
    <button type="button" onclick="openModal('{{ $manageModalId }}')">Manage</button>
    <form method="POST" action="{{ $backupUrl }}" style="display:inline">
        @csrf
        <button type="submit" class="btn-secondary">Backup</button>
    </form>
    <button type="button" onclick="openModal('{{ $restoreModalId }}')" class="btn-secondary">Restore</button>
    <form method="POST" action="{{ $exportExcelUrl }}" style="display:inline">
        @csrf
        <input type="hidden" name="filtered" value="1">
        @foreach(request()->except(['page']) as $k => $v)
            @if(is_array($v))
                @foreach($v as $item)
                    <input type="hidden" name="{{ $k }}[]" value="{{ $item }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endif
        @endforeach
        <button>Export CSV</button>
    </form>
    <form method="GET" action="{{ $exportPdfUrl }}" style="display:inline">
        <input type="hidden" name="filtered" value="1">
        @foreach(request()->except(['page']) as $k => $v)
            @if(is_array($v))
                @foreach($v as $item)
                    <input type="hidden" name="{{ $k }}[]" value="{{ $item }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endif
        @endforeach
        <button type="submit" class="btn-secondary">Export PDF</button>
    </form>
</div>
