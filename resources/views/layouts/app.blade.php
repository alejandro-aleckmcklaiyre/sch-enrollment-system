<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Students')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/modern-normalize/modern-normalize.css">
    <style>
        /* Minimalist retro earth-tone palette */
        :root{
            --bg: #f5f0ea; /* off-white */
            --sidebar: #cdb79e; /* beige/tan */
            --panel: #e8e1d6; /* lighter tan */
            --accent: #7a6a4f; /* muted olive/brown */
            --text: #2e2a26; /* dark brown */
            --line: #c4b59f; /* table line color - medium brown / dark beige */
            --muted: #8a8073;
        }
        body{font-family: Inter, Arial, sans-serif; background:var(--bg); color:var(--text);}
        .app{display:flex; min-height:100vh}
        .sidebar{width:220px; background:var(--sidebar); padding:24px 16px; box-shadow:2px 0 0 rgba(0,0,0,0.03)}
        .content{flex:1; padding:20px}
        .topbar{display:flex; gap:12px; align-items:center; margin-bottom:16px}
        .card{background:var(--panel); padding:16px; box-shadow:0 1px 0 rgba(0,0,0,0.03)}
    table{width:100%; border-collapse:collapse}
    th,td{padding:10px; border-bottom:1px solid var(--line); text-align:left}
    /* add subtle vertical separators between columns for better readability */
    /* using adjacent sibling selector to avoid double borders */
    td + td, th + th { border-left:1px solid var(--line); }
    /* stronger header bottom border */
    th { border-bottom:2px solid var(--line); text-align:left; }
        .toolbar{display:flex; gap:8px}
        button{background:var(--accent); color:var(--bg); border:0; padding:8px 12px; cursor:pointer}
        .btn-secondary{background:var(--muted);}
        input,select{padding:8px; border:1px solid rgba(0,0,0,0.08); background:white}
        /* simple modal styles */
        .modal{position:fixed; inset:0; display:none; align-items:center; justify-content:center; background:rgba(0,0,0,0.4)}
    .modal .box{background:var(--panel); padding:20px; width:640px; max-width:92vw; max-height:78vh; overflow:auto; box-shadow:0 8px 24px rgba(0,0,0,0.2); border-radius:8px}
        .form-group { display:flex; flex-direction:column; gap:4px; }
        .form-group label { font-weight:500; color:var(--text); }
        .form-group input, .form-group select { width:100%; box-sizing:border-box; }
        .help-text { font-size:0.9em; color:var(--muted); margin-top:2px; }
    /* Constrain SVGs used in UI (pagination/icons) so they don't scale unexpectedly */
    .card .pagination, .content .pagination { display:flex; gap:8px; align-items:center; }
    .card .pagination svg, .content .pagination svg { width:1em !important; height:1em !important; max-width:24px !important; max-height:24px !important; }
    /* Ensure inline icons don't become block-level oversized elements */
    img, svg { max-width:100%; height:auto; display:inline-block; vertical-align:middle; }
    /* Strong safeguard: force any svg inside main content to remain small so decorative svgs don't overflow */
    .content svg { width:auto !important; height:auto !important; max-width:48px !important; max-height:48px !important; }
     nav[aria-label="Pagination Navigation"] svg { width:16px !important; height:16px !important; }
     /* Laravel's default paginator outputs two blocks: a simple prev/next (for small screens)
         and a full numbered paginator. If Tailwind responsive utilities aren't present,
         both blocks show. Hide the simple prev/next block and keep the numbered one. */
     nav[aria-label="Pagination Navigation"] > div:first-child { display:none; }
    /* Sortable header link styling - keep inline, centered and small arrow */
    .sortable-link{ display:inline-flex; gap:6px; align-items:center; color:inherit; text-decoration:none; }
    .sortable-link small{ font-size:0.78em; line-height:1; }
    th .sortable-link{ display:inline-flex; }
    /* Alert styles (earth-tone, dismissible) */
    .alert-root{ position:relative; }
    .alert {
        position:absolute;
        left:24px;
        right:24px;
        top:12px;
        z-index:40;
        display:flex;
        align-items:center;
        gap:12px;
        padding:12px 16px;
        border-radius:8px;
        box-shadow:0 6px 18px rgba(0,0,0,0.08);
        color:var(--text);
        pointer-events:auto;
        min-height:56px;
    }
    .alert .icon{flex:0 0 44px; display:flex; align-items:center; justify-content:center}
    .alert .icon svg{width:28px; height:28px}
    .alert .content{flex:1; display:flex; flex-direction:column; gap:4px}
    .alert .title{font-weight:700; font-size:0.95rem}
    .alert .detail{font-size:0.9rem; opacity:0.95}
    .alert .close{background:transparent;border:0;color:var(--text);cursor:pointer;padding:6px}
    .alert-success{ background: linear-gradient(90deg, rgba(243,236,226,1) 0%, rgba(233,221,205,1) 100%); border:1px solid var(--line) }
    .alert-info{ background: linear-gradient(90deg, rgba(247,245,242,1) 0%, rgba(238,233,224,1) 100%); border:1px solid var(--line) }
    .alert-error{ background: linear-gradient(90deg, rgba(245,230,230,1) 0%, rgba(235,210,208,1) 100%); border:1px solid rgba(160,120,110,0.18) }
    /* Table header controls - flat retro earth-tone look */
    .table-header-root { padding: 10px 0 6px; }
    .table-header-root .toolbar { margin-bottom:6px }
    .table-header-root .controls-bar { background: transparent; padding:8px 0; }
    .table-header-root input.form-control, .table-header-root select.form-control { border-radius:0; }
    .table-header-root button { border-radius:0; }
    .table-header-root .btn-secondary { background: var(--muted); color:var(--bg); }
    /* ensure alignment with table width inside .card */
    .card > .table-header-root, .card .table-header-root { width:100%; box-sizing:border-box }
    </style>
    <style>
        /* Course checklist styles */
        .course-checklist {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: 400px;
            overflow-y: auto;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 4px;
            background: white;
            margin-top: 8px;
        }

        .course-checklist-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 4px;
            background: var(--bg);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .course-checklist-item:hover {
            background: var(--panel);
            border-color: var(--accent);
        }

        /* Custom checkbox styling */
        .course-checklist-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin: 0;
            cursor: pointer;
            position: relative;
            border: 2px solid var(--accent);
            border-radius: 3px;
            background: white;
            flex-shrink: 0;
        }

        .course-checklist-item input[type="checkbox"]:checked {
            background: var(--accent);
        }

        .course-checklist-item input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 14px;
            line-height: 1;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .course-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 4px 0;
        }

        .course-title {
            font-weight: 600;
            color: var(--text);
            font-size: 1.05em;
        }

        .course-details {
            color: var(--muted);
            font-size: 0.9em;
            line-height: 1.4;
        }

        /* Selected state */
        .course-checklist-item.selected {
            background: var(--panel);
            border-color: var(--accent);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* Course selection header */
        .course-selection-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--line);
            margin-bottom: 8px;
        }

        .course-selection-count {
            font-size: 0.9em;
            color: var(--muted);
        }

        .select-all-courses {
            font-size: 0.9em;
            color: var(--accent);
            text-decoration: underline;
            cursor: pointer;
            border: none;
            background: none;
            padding: 4px 8px;
        }

        .select-all-courses:hover {
            color: var(--text);
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="app">
    <aside class="sidebar">
        @include('partials.sidebar')
    </aside>
    <main class="content">
        <div class="topbar" style="display:flex; align-items:center; gap:12px;">
            <div style="flex:1; display:flex; align-items:center;">
                <h1 style="margin:0;">@yield('title', 'Students')</h1>
            </div>
            <div style="display:flex; align-items:center;">
                @yield('toolbar')
            </div>
        </div>

        <div class="card">
            <div class="alert-root" id="alert-root" aria-live="polite" aria-atomic="true"></div>
            @yield('content')
        </div>
    </main>
</div>

@stack('modals')

<script>
    function openModal(id){document.getElementById(id).style.display='flex'}
    function closeModal(id){document.getElementById(id).style.display='none'}
    function postJson(url,data, cb){
        fetch(url,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}, body: JSON.stringify(data)})
        .then(r=>r.json()).then(cb).catch(e=>console.error(e));
    }
    // Helper used by many modals to run fetch and forward http status + json to handleResponse
    function submitFormWithStatus(fetchPromise, modalId){
        fetchPromise.then(async (res)=>{
            let body = {};
            try{ body = await res.json(); } catch(e){ body = { message: 'No response body' }; }
            body.httpStatus = res.status; handleResponse(body, modalId);
        }).catch(e=>{ console.error(e); handleResponse({ message: 'Network error', success:false, error:true, httpStatus:0 }, modalId); });
    }
    // Alerts: show dismissible message near top of content without forcing a full reload
    function showAlert(type, payload, opts={}){
        // payload: { title: '', detail: '' }
        const root = document.getElementById('alert-root');
        if(!root) return;
        const el = document.createElement('div');
        el.className = 'alert alert-' + (type || 'info');
        const iconHtml = {
            success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M16 11l-4.5 4.5L8 13"/></svg>',
            error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>',
            info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8h.01M11 12h1v4h1"/></svg>'
        };
        const icon = iconHtml[type==='error' ? 'error' : (type==='success' ? 'success' : 'info')];
        el.innerHTML = '<div class="icon">' + icon + '</div>' +
            '<div class="content"><div class="title">' + (payload.title || '') + '</div>' +
            '<div class="detail">' + (payload.detail || '') + '</div></div>' +
            '<button class="close" aria-label="Dismiss">&times;</button>';
        // attach close
        el.querySelector('.close').addEventListener('click', ()=>{ dismissAlert(el); });
        // insert and animate
        root.appendChild(el);
        el.style.opacity = 0; el.style.transform = 'translateY(-6px)';
        requestAnimationFrame(()=>{ el.style.transition='opacity 240ms, transform 240ms'; el.style.opacity=1; el.style.transform='translateY(0)'; });
        // auto-dismiss unless disabled
        if(!opts.sticky){ setTimeout(()=>{ dismissAlert(el); }, opts.timeout || 5000); }
        return el;
    }
    function dismissAlert(el){ if(!el) return; el.style.transition='opacity 240ms, transform 240ms'; el.style.opacity=0; el.style.transform='translateY(-6px)'; setTimeout(()=>{ el.remove(); }, 260); }

    // handleResponse: closable and non-reloading by default. If server sends force_reload=true, do a reload.
    function handleResponse(resp, modalId){
        // New enhanced response handler: will inspect httpStatus (if provided), resp.op, and resp.data
        // Close modal by default only on success responses. Validation (422) and Duplicate (409) keep modal open.
        if(!resp) return;
        // allow server to provide an explicit httpStatus property (helpers below will pass it through)
        const httpStatus = resp.httpStatus || resp.statusCode || null;
        // Infer op and success
        let op = resp.op || resp.action || resp.type || null;
        const message = (resp.message || '') + '';
        const lower = message.toLowerCase();
        if(!op){
            if(/add|create|added|created/.test(lower)) op = 'add';
            else if(/update|updated|edit/.test(lower)) op = 'update';
            else if(/delete|removed|archive|archived/.test(lower)) op = 'delete';
        }

        const success = (httpStatus ? (httpStatus >= 200 && httpStatus < 300) : !!resp.success);

        // Handle validation errors (422)
        if(httpStatus === 422 || (resp.errors && !success)){
            // keep modal open, show inline field errors if possible
            try{ openModal(modalId); }catch(e){}
            if(typeof showFormErrors === 'function') showFormErrors(modalId, resp.errors || {});
            // show a small alert summarizing the problem
            showAlert('error', { title: 'Validation error', detail: resp.message || 'Please correct the highlighted fields.' }, { sticky: false });
            return;
        }

        // Handle duplicate (409)
        if(httpStatus === 409){
            // open a standardized duplicate modal with server message
            if(typeof openDuplicateModal === 'function'){
                openDuplicateModal(resp.message || 'This record already exists in the database.');
            } else {
                showAlert('error', { title: 'Duplicate record', detail: resp.message || 'This record already exists in the database.' });
            }
            return;
        }

        // Success path: close modal, insert row if provided, and show success alert
        if(success){
            try{ closeModal(modalId); }catch(e){}
            // Insert row_html or try to build a row from resp.data
            if(resp.row_html && typeof resp.row_html === 'string'){
                if(typeof insertRowHtml === 'function') insertRowHtml(resp.row_html, resp.op || op);
            } else if(resp.data){
                if(typeof insertRowData === 'function') insertRowData(resp.data, resp.op || op);
            }
            // show success toast
            const title = op ? (op === 'add' ? 'Add Record' : (op === 'update' ? 'Update' : 'Success')) : 'Success';
            showAlert('success', { title: title, detail: resp.message || '✅ Record added successfully.' });
        } else {
            // fallback error
            showAlert('error', { title: 'Error', detail: resp.message || '❌ Unable to complete the operation. Please try again.' });
        }

        // Always reload after successful add/update/delete to refresh UI state (short delay for toast)
        if(success && (op === 'add' || op === 'update' || op === 'delete')){
            setTimeout(()=> location.reload(), 450);
        }
    }

    // Show inline form validation errors inside modal. Expects errors in Laravel format { field: [messages] }
    function showFormErrors(modalId, errors){
        try{
            const modal = document.getElementById(modalId);
            if(!modal) return;
            // Clear prior
            clearFormErrors(modalId);
            Object.keys(errors || {}).forEach(field => {
                const input = modal.querySelector('[name="' + field + '"]');
                const container = input ? input.parentElement : modal.querySelector('.box');
                if(input){
                    input.classList.add('has-error');
                    const el = document.createElement('div'); el.className='field-error'; el.style.color='#8a2b2b'; el.style.marginTop='6px'; el.innerText = errors[field].join(' ');
                    container.appendChild(el);
                }
            });
        }catch(e){ console.error(e); }
    }

    function clearFormErrors(modalId){
        try{
            const modal = document.getElementById(modalId);
            if(!modal) return;
            modal.querySelectorAll('.field-error').forEach(n=>n.remove());
            modal.querySelectorAll('.has-error').forEach(i=>i.classList.remove('has-error'));
        }catch(e){console.error(e)}
    }

    // Insert raw row HTML into first table body on the page
    function insertRowHtml(rowHtml, op){
        try{
            const tbody = document.querySelector('table tbody');
            if(!tbody) return;
            const temp = document.createElement('tbody'); temp.innerHTML = rowHtml.trim();
            // If the rowHtml contains multiple rows use them all
            const rows = Array.from(temp.children);
            rows.forEach(r=> tbody.insertBefore(r, tbody.firstChild));
        }catch(e){ console.error(e); }
    }

    // Build a simple table row from resp.data using data-* attributes on the header cells as guidance would be complex.
    // We'll try to find a related resource by examining the current page title and create columns in the same order as the first existing row.
    function insertRowData(data, op){
        try{
            const tbody = document.querySelector('table tbody');
            if(!tbody) return;
            const firstRow = tbody.querySelector('tr');
            if(!firstRow){
                // no rows exist yet, create a row with the values of data
                const tr = document.createElement('tr');
                Object.keys(data).forEach(k=>{ const td = document.createElement('td'); td.innerText = data[k]; tr.appendChild(td); });
                tbody.appendChild(tr); return;
            }
            // Heuristic: clone first row, then replace textContent for each cell with matching data fields where possible
            const tr = firstRow.cloneNode(true);
            const cells = tr.querySelectorAll('td');
            const keys = Object.keys(data);
            for(let i=0;i<cells.length;i++){
                const key = keys[i] || null;
                if(key){ cells[i].innerText = data[key] ?? cells[i].innerText; }
            }
            tbody.insertBefore(tr, firstRow);
        }catch(e){ console.error(e); }
    }

    // Duplicate modal helper
    function openDuplicateModal(message){
        // Reuse a generic modal id to display duplicates. Create it if missing
        let id = 'duplicateAlertModal';
        let modal = document.getElementById(id);
        if(!modal){
            modal = document.createElement('div'); modal.id = id; modal.className='modal'; modal.style.display='none';
            const box = document.createElement('div'); box.className='box';
            box.innerHTML = '<h3>Duplicate Record</h3><p id="duplicateMessage"></p><div style="display:flex; gap:8px; justify-content:flex-end; margin-top:8px;"><button type="button" onclick="closeModal(\'duplicateAlertModal\')" class="btn-secondary">Close</button></div>';
            modal.appendChild(box); document.body.appendChild(modal);
        }
        modal.querySelector('#duplicateMessage').innerText = message;
        openModal(id);
    }
</script>

@stack('scripts')
</body>
</html>
