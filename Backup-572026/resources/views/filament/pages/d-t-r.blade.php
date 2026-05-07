<x-filament-panels::page>
    <script>
        document.addEventListener('open-new-tab', event => {
            const url = event.detail[0].url;
            const width = window.screen.width * 0.45;
            const height = window.screen.height * 1;
            const left = "500px";
            const top = "500px";
            window.open(url, "_blank");
            // window.open(
            //     url,
            //     "_blank",
            //     `width=${width},height=${height},top=${top},left=${left},scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no`
            // );
        });
    </script>

</x-filament-panels::page>
