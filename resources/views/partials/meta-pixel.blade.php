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
