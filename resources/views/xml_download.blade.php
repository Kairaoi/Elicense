<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Downloading XML</title>
</head>
<body>
    <noscript>
        <p>This page requires JavaScript to download the XML file automatically. Please enable JavaScript and try again.</p>
    </noscript>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            try {
                // XML content from the server
                var xmlContent = {!! json_encode($xml) !!};
                
                // Create a Blob from the XML content
                var blob = new Blob([xmlContent], { type: 'application/xml' });

                // Create a temporary link element
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'applicant_details.xml';

                // Append to the DOM, trigger the download, and remove the link
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Revoke the Object URL to free memory
                URL.revokeObjectURL(link.href);

                // Redirect after a short delay
                setTimeout(function () {
                    window.location.href = '{{ route('license.licenses.create') }}';
                }, 1000);
            } catch (error) {
                console.error("Error during XML download:", error);
            }
        });
    </script>
</body>
</html>
