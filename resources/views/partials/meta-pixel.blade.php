{{-- Meta Pixel — Pixel ID diatur dari halaman Admin › Pengaturan --}}
@php
    $metaPixelId = \App\Models\Setting::metaPixelId();
    $metaAdvancedMatching = [];
    if ($metaPixelId && auth()->check()) {
        $metaAdvancedMatching = array_filter([
            'em' => strtolower(trim((string) auth()->user()->email)),
            'ph' => preg_replace('/\D/', '', (string) auth()->user()->no_tlp),
            'external_id' => (string) auth()->id(),
        ]);
    }
@endphp
@if ($metaPixelId)
    <script>
        // Domain *.my.id: Pixel kadang gagal menulis cookie _fbp/_fbc sendiri.
        // Buat cookie first-party di domain ini (format resmi Meta) sebelum Pixel dimuat;
        // Pixel akan membaca cookie yang sudah ada, jadi browser & server memakai ID yang sama.
        (function () {
            try {
                var maxAge = '; path=/; max-age=7776000; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : '');
                if (!/(?:^|; )_fbp=/.test(document.cookie)) {
                    document.cookie = '_fbp=fb.1.' + Date.now() + '.' + Math.floor(Math.random() * 1e10) + maxAge;
                }
                var fbclid = new URLSearchParams(location.search).get('fbclid');
                if (fbclid) {
                    var current = document.cookie.match(/(?:^|; )_fbc=([^;]*)/);
                    if (!current || decodeURIComponent(current[1]).split('.').slice(3).join('.') !== fbclid) {
                        document.cookie = '_fbc=fb.1.' + Date.now() + '.' + fbclid + maxAge;
                    }
                }
            } catch (e) {}
        })();

        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', @json($metaPixelId), @json((object) $metaAdvancedMatching));
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" alt=""
        src="https://www.facebook.com/tr?id={{ $metaPixelId }}&ev=PageView&noscript=1"></noscript>
@endif
