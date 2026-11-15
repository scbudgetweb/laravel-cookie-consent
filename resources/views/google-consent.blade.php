<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('consent', 'default', @json($state));
    @if ($adsDenied)
    gtag('set', 'ads_data_redaction', true);
    @endif
</script>
