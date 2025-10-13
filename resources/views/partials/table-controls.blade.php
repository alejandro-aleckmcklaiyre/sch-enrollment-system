@php
    $listUrl = $listUrl ?? url()->current();
    $sortFields = $sortFields ?? [];
    $filterModalId = $filterModalId ?? 'filterModal';
@endphp

<div class="table-header-root">
    <div class="controls-bar" style="display:flex; justify-content:space-between; gap:12px; align-items:center; padding:8px 0; border-top:1px solid transparent">
        <div style="display:flex; gap:8px; align-items:center">
            <form method="GET" action="{{ $listUrl }}" style="display:flex; gap:8px; align-items:center">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="form-control" style="width:320px" />
                <button type="button" class="btn btn-secondary" onclick="openModal('{{ $filterModalId }}')" style="margin-left:6px">Filter</button>
            </form>
        </div>

        <div style="display:flex; gap:8px; align-items:center">
            <form method="GET" action="{{ $listUrl }}" style="display:flex; gap:8px; align-items:center">
                @foreach(request()->except(['page','sort_by','sort_dir']) as $k => $v)
                    @if(is_array($v))
                        @foreach($v as $item)
                            <input type="hidden" name="{{ $k }}[]" value="{{ $item }}" />
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}" />
                    @endif
                @endforeach

                <select name="sort_by" class="form-control" style="width:180px">
                    @foreach($sortFields as $sf)
                        <option value="{{ $sf['value'] }}" {{ request('sort_by') == $sf['value'] ? 'selected' : '' }}>{{ $sf['label'] }}</option>
                    @endforeach
                </select>

                <select name="sort_dir" class="form-control" style="width:120px">
                    <option value="desc" {{ request('sort_dir','desc')=='desc' ? 'selected' : '' }}>DESC</option>
                    <option value="asc" {{ request('sort_dir')=='asc' ? 'selected' : '' }}>ASC</option>
                </select>
                <button type="submit" class="btn btn-secondary">Sort</button>
            </form>
        </div>
    </div>
</div>
