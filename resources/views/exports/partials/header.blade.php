@php
    $title = config('export.header_title');
    $created = date(config('export.date_format'));
    // Controller may supply a prepared $logoDataUri; if not, attempt several fallbacks:
    // 1) env('EXPORT_LOGO_BASE64') - paste raw base64 or a full data: URI
    // 2) env('EXPORT_LOGO_URL') - absolute URL to an image (http(s) or data: URI)
    // 3) public/images/pup_logo.jpg or pup_logo.png
    // 4) inline SVG fallback
    if (empty($logoDataUri)) {
        $logoDataUri = null;

        // 1) allow providing the logo via environment variable (base64 or full data URI)
        try {
            $envBase64 = env('EXPORT_LOGO_BASE64');
        } catch (\Throwable $e) {
            $envBase64 = null;
        }
        try {
            $envUrl = env('EXPORT_LOGO_URL');
        } catch (\Throwable $e) {
            $envUrl = null;
        }

        if (!empty($envBase64)) {
            // if user pasted a full data: URI, use it; otherwise assume PNG
            if (strpos(trim($envBase64), 'data:') === 0) {
                $logoDataUri = trim($envBase64);
            } else {
                $logoDataUri = 'data:image/png;base64,' . trim($envBase64);
            }
        } elseif (!empty($envUrl)) {
            // use URL as-is (can be a remote http(s) url or a data: URI)
            $logoDataUri = trim($envUrl);
        } else {
            // 2) fall back to local files (try jpg then png). Support both 'images' and legacy 'image' folders.
            $candidates = [
                public_path('images/pup_logo.jpg'),
                public_path('images/pup_logo.png'),
                public_path('image/pup_logo.jpg'),
                public_path('image/pup_logo.png'),
            ];
            $tryPath = null;
            foreach ($candidates as $p) {
                if (file_exists($p)) {
                    try {
                        if (@getimagesize($p) !== false && filesize($p) > 512) {
                            $tryPath = $p;
                            break;
                        }
                    } catch (\Throwable $e) {
                        // ignore and continue
                    }
                }
            }

            if ($tryPath) {
                try {
                    $mime = mime_content_type($tryPath) ?: 'image/jpeg';
                    $data = base64_encode(file_get_contents($tryPath));
                    $logoDataUri = "data:{$mime};base64,{$data}";
                } catch (\Throwable $e) {
                    $logoDataUri = null;
                }
            }
        }
    }
@endphp

<div style="text-align:center; margin-bottom:10px; font-family: Helvetica, Arial, sans-serif;">
    <div style="display:inline-block; text-align:center;">
        @if($logoDataUri)
            <img src="{{ $logoDataUri }}" alt="PUP Logo" width="64" style="display:block; margin:0 auto 6px auto;">
        @else
            {{-- Inline SVG fallback to avoid broken image when the file is missing. --}}
            <svg width="64" height="64" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg" style="display:block; margin:0 auto 6px auto;">
                <circle cx="60" cy="60" r="58" fill="#8b1e2b" stroke="#fff" stroke-width="4" />
                <polygon points="60,18 70,52 106,52 76,72 86,106 60,86 34,106 44,72 14,52 50,52" fill="#ffd24a" />
                <circle cx="60" cy="60" r="20" fill="rgba(255,255,255,0.08)" />
            </svg>
        @endif
        <div style="font-weight:700; font-size:14px">{{ $title }}</div>
        <div style="font-size:11px; color:#333">Date Created: {{ $created }}</div>
    </div>
</div>
<hr style="border:none; border-top:1px solid #ddd; margin:6px 0 12px 0">
