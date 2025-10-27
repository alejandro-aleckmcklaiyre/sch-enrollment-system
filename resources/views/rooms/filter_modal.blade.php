<!-- Filter Modal -->
<div id="filterRoomModal" class="modal">
    <div class="box">
        <h3>Filter Rooms</h3>
        <form id="filterRoomForm" action="{{ url('rooms') }}" method="GET">
            <div style="display:grid; grid-template-columns:1fr; gap:8px">
                <label for="building">Building</label>
                <select name="building" id="building">
                    <option value="">All Buildings</option>
                    @foreach($buildings as $building)
                        <option value="{{ $building }}" {{ request('building') == $building ? 'selected' : '' }}>{{ $building }}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
            <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end">
                <button type="button" onclick="closeModal('filterRoomModal')" class="btn-secondary">Cancel</button>
                <button type="button" onclick="clearRoomFilters()" class="btn-secondary">Clear</button>
                <button type="submit">Apply</button>
            </div>
        </form>
    </div>
</div>

<script>
function clearRoomFilters() {
    const sel = document.querySelector('#building');
    if(sel) sel.value = '';
    document.querySelector('#filterRoomForm').submit();
}
</script>