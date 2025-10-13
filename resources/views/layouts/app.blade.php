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
    .modal .box{background:var(--panel); padding:14px; width:640px; max-width:92vw; max-height:78vh; overflow:auto; box-shadow:0 8px 24px rgba(0,0,0,0.2); border-radius:8px}
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
        if(resp){
            try{ closeModal(modalId); }catch(e){}
            // Determine operation: prefer explicit fields from server
            let op = resp.op || resp.action || resp.type || null;
            const msg = (resp.message || '') + '';
            const lower = msg.toLowerCase();
            if(!op){
                if(/add|create|added|created/.test(lower)) op = 'add';
                else if(/update|updated|edit/.test(lower)) op = 'update';
                else if(/delete|removed|archive|archived/.test(lower)) op = 'delete';
            }
            const success = !(resp.error || resp.status === 'error' || (resp.status && resp.status.toString().toLowerCase()==='fail'));

            // Default standardized messages per user request
            const messages = {
                add: {
                    success: { title: 'Add Record', detail: '✅ Success: Record added successfully.' },
                    error:   { title: 'Add Record', detail: '❌ Failed: Unable to add record. Please try again.' }
                },
                update: {
                    success: { title: 'Edit / Update Record', detail: '✅ Success: Record updated successfully.' },
                    error:   { title: 'Edit / Update Record', detail: '❌ Failed: Unable to update record. Please try again.' }
                },
                delete: {
                    success: { title: 'Delete Record', detail: '✅ Success: Record has been removed successfully.' },
                    error:   { title: 'Delete Record', detail: '❌ Failed: Unable to delete record. Please try again.' }
                }
            };

            let payload = null;
            if(op && messages[op]){
                payload = success ? messages[op].success : messages[op].error;
            } else {
                // fallback: use server message but format
                payload = success ? { title: 'Success', detail: '✅ ' + (resp.message || 'Operation completed.') } : { title: 'Failed', detail: '❌ ' + (resp.message || 'Operation failed.') };
            }

            // Do not surface backend DB details in the UI. Use server-provided 'message' only.

            showAlert(success ? 'success' : 'error', payload);
        }
        if(resp && (resp.force_reload === true || resp.reload === true)){
            setTimeout(()=> location.reload(), 450);
        }
    }
</script>

@stack('scripts')
</body>
</html>
