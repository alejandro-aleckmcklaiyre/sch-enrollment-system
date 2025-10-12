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
        .modal .box{background:var(--panel); padding:16px; width:720px; box-shadow:0 8px 24px rgba(0,0,0,0.2)}
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
    </style>
    @stack('styles')
</head>
<body>
<div class="app">
    <aside class="sidebar">
        @include('partials.sidebar')
    </aside>
    <main class="content">
        <div class="topbar">
            <h1>@yield('title', 'Students')</h1>
            <div style="flex:1"></div>
            @yield('toolbar')
        </div>

        <div class="card">
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
    // simple helper to close modals after action
    function handleResponse(resp, modalId){ if(resp && resp.message){ closeModal(modalId); location.reload(); } }
</script>

@stack('scripts')
</body>
</html>
