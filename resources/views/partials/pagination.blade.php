<div class="pagination-bar" style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 8px; background:var(--panel);">
    {{-- Left: per-page selector --}}
    <div style="display:flex; align-items:center; gap:8px; min-width:160px;">
        <label style="color:var(--muted);">Show</label>
        <form id="perPageForm" method="GET" style="margin:0">
            {{-- Preserve existing query params except per_page --}}
            @foreach(request()->except('per_page') as $k => $v)
                @if(is_array($v))
                    @foreach($v as $vv)
                        <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endif
            @endforeach
            <select name="per_page" onchange="document.getElementById('perPageForm').submit()" style="padding:6px; border:1px solid rgba(0,0,0,0.08); background:white">
                @foreach([10,15,20,30,50] as $n)
                    <option value="{{ $n }}" {{ request('per_page', 15) == $n ? 'selected' : '' }}>{{ $n }} per page</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Center: page info (centered) --}}
    <div style="flex:1; display:flex; justify-content:center;">
        <div style="color:var(--muted); font-size:0.95em; text-align:center">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }} &middot; Showing {{ $paginator->firstItem() ?: 0 }} to {{ $paginator->lastItem() ?: 0 }} of {{ $paginator->total() }} results</div>
    </div>

    {{-- Right: pagination controls (prev, numbers, next) aligned right --}}
    <div style="min-width:260px; display:flex; justify-content:flex-end;">
        <nav class="numbered-pagination" aria-label="Pagination Navigation" style="display:flex; gap:6px; align-items:center;">
            @php
                $last = $paginator->lastPage();
                $current = $paginator->currentPage();
                $start = 1;
                $end = $last;
                $showLeading = false;
                $showTrailing = false;
                if($last > 9){
                    $start = max(1, $current - 3);
                    $end = min($last, $current + 3);
                    if($start > 1) $showLeading = true;
                    if($end < $last) $showTrailing = true;
                }
            @endphp

            {{-- Previous arrow --}}
            @if($paginator->onFirstPage())
                <button disabled style="padding:6px 10px; background:transparent; color:var(--muted); border:1px solid rgba(0,0,0,0.04);">‹</button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" style="text-decoration:none"><button style="padding:6px 10px;">‹</button></a>
            @endif

            {{-- Leading --}}
            @if($showLeading)
                <a href="{{ $paginator->url(1) }}" style="text-decoration:none"><button style="padding:6px 10px;">1</button></a>
                <span style="padding:6px 6px; color:var(--muted);">…</span>
            @endif

            {{-- Page numbers --}}
            @for($i = $start; $i <= $end; $i++)
                @if($i == $current)
                    <button aria-current="page" style="padding:6px 10px; background:var(--accent); color:var(--bg); border:0;">{{ $i }}</button>
                @else
                    <a href="{{ $paginator->url($i) }}" style="text-decoration:none"><button style="padding:6px 10px;">{{ $i }}</button></a>
                @endif
            @endfor

            {{-- Trailing --}}
            @if($showTrailing)
                <span style="padding:6px 6px; color:var(--muted);">…</span>
                <a href="{{ $paginator->url($last) }}" style="text-decoration:none"><button style="padding:6px 10px;">{{ $last }}</button></a>
            @endif

            {{-- Next arrow --}}
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" style="text-decoration:none"><button style="padding:6px 10px;">›</button></a>
            @else
                <button disabled style="padding:6px 10px; background:transparent; color:var(--muted); border:1px solid rgba(0,0,0,0.04);">›</button>
            @endif
        </nav>
    </div>

</div>

@once
    @push('styles')
    <style>
        /* Retro/minimal pagination styles: strictly flat, no rounded corners */
        .pagination-bar button{ background:var(--accent); color:var(--bg); border:0; box-shadow:0 1px 0 rgba(0,0,0,0.03); border-radius:0; }
        .pagination-bar a button{ background:var(--accent); color:var(--bg); border:0; border-radius:0; }
        .pagination-bar button[disabled]{ background:transparent; color:var(--muted); border:1px solid rgba(0,0,0,0.04); }
        .pagination-bar select{ border-radius:0; }
    </style>
    @endpush
@endonce
