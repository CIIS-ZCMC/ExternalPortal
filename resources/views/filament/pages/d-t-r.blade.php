<x-filament-panels::page>
    <script>
        document.addEventListener('open-new-tab', event => {
            const url = event.detail[0].url;
            const width = window.screen.width * 0.45;
            const height = window.screen.height * 1;

            const popup = window.open('', '_blank', 'width=' + width + ',height=' + height + ',scrollbars=yes,resizable=yes');
            popup.document.title = 'DTR Report';
            popup.document.body.style.margin = '0';
            popup.document.body.style.padding = '0';
            popup.document.body.style.overflow = 'hidden';
            const iframe = popup.document.createElement('iframe');
            iframe.src = url;
            iframe.style.width = '100%';
            iframe.style.height = '100vh';
            iframe.style.border = 'none';
            popup.document.body.appendChild(iframe);
        });
    </script>

</x-filament-panels::page>
