<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Video</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Subir Video</h1>
        <a href="{{ route('videos.index') }}" target="_blank" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg mb-4">
            Ver Lista de Videos
        </a>
        
        <div id="success-message" class="hidden bg-green-500 p-4 rounded-lg mb-6 text-white"></div>

        <div class="max-w-md w-full bg-white p-6 rounded-lg shadow-lg">
            <form id="upload-form" action="{{ route('videos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="video" class="block text-gray-700 text-sm font-bold mb-2">Video:</label>
                    <input type="file" name="video" id="video" class="border rounded p-2 w-full">
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg w-full">Subir Video</button>
            </form>

            <div id="progress-container" class="hidden mt-4">
                <div id="progress-bar" class="bg-blue-500 text-xs leading-none py-1 text-center text-white" style="width: 0%"></div>
            </div>
        </div>

        <div id="video-buttons" class="hidden mt-4">
            <a id="view-video-btn" href="#" target="_blank" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg mr-2">
                Ver Video Embed
            </a>
        </div>
    </div>

    <script>
        document.getElementById('upload-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const progressBarContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const successMessage = document.getElementById('success-message');
            const videoButtons = document.getElementById('video-buttons');
            const viewVideoBtn = document.getElementById('view-video-btn');

            const xhr = new XMLHttpRequest();
            xhr.open('POST', this.action, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percentage = (e.loaded / e.total) * 100;
                    progressBar.style.width = percentage + '%';
                    progressBar.textContent = percentage.toFixed(0) + '%';
                }
            };

            xhr.onloadstart = function(e) {
                progressBarContainer.classList.remove('hidden');
            };

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    successMessage.textContent = 'Video subido con éxito. ID del Video: ' + response.id;
                    successMessage.classList.remove('hidden');
                    progressBarContainer.classList.add('hidden');
                    videoButtons.classList.remove('hidden');
                    viewVideoBtn.href = '/videos/' + response.id + '/embed';
                } else {
                    console.error('Error en la carga');
                }
            };

            xhr.send(formData);
        });
    </script>
</body>
</html>
