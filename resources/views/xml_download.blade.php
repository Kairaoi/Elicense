<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Downloading XML</title>
</head>
<body>
    <script>
        // Create a Blob from the XML content
        var xmlContent = {!! json_encode($xml) !!};
        var blob = new Blob([xmlContent], { type: 'application/xml' });

        // Create a link element to trigger the download
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'applicant_details.xml';
        link.click();

        // Redirect after download
        window.location.href = '{{ route('license.licenses.create') }}';
    </script>
</body>
</html>
