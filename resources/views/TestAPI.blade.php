<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>API Test Page</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- @vite(['resources/js/app.js', 'resources/css/app.css']) --}}
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        textarea { width: 100%; height: 150px; }
        input[type="text"] { width: 300px; }
        .logs { background: #f4f4f4; padding: 10px; margin-top: 20px; white-space: pre-wrap; font-family: monospace; max-height: 200px; overflow-y: auto; }
        .response { background: #e8f0fe; padding: 10px; margin-top: 10px; white-space: pre-wrap; font-family: monospace; }
    </style>
</head>
<body>
    <h1>API Test Page</h1>
    <form id="apiTestForm">
        <label for="bboxInput">Enter BBox (format: minX,minY,maxX,maxY):</label><br>
        <input type="text" id="bboxInput" name="bbox" placeholder="e.g. 2.29,48.85,2.35,48.89" required>
        <button type="submit">Submit</button>
    </form>

    <h2>API Response</h2>
    <div id="apiResponse" class="response">No data yet.</div>

    <h2>Last 10 Laravel Log Lines</h2>
    <div class="logs" id="logLines">
        @foreach ($logs as $line)
            {{ $line }}<br>
        @endforeach
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('apiTestForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    console.log('Form submitted!');
                    e.preventDefault();
                    const bbox = document.getElementById('bboxInput').value;
                    const apiResponseDiv = document.getElementById('apiResponse');
    
                    fetch('/test/fetch-data', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ bbox: bbox })
                    })
                    .then(response => {
                        if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`Error ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                    })
                    .then(data => {
                        apiResponseDiv.textContent = JSON.stringify(data, null, 2);
                    })
                    .catch(error => {
                        apiResponseDiv.textContent = 'Error: ' + error.message;
                    });
                    console.log('Request body:', JSON.stringify({ bbox: bbox }));
                    
                });
            }
        });
    </script>
    
</body>
</html>
