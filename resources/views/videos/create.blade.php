<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Video</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Subir Video</h1>
        <a href="{{ route('videos.index') }}" target="_blank"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg mb-4">
            <i class="fas fa-list-ul pr-1"></i> Lista de Videos
        </a>

        <div id="success-message" class="hidden bg-green-500 p-4 rounded-lg mb-6 text-white"></div>

        <div class="max-w-md w-full bg-white p-6 rounded-lg shadow-lg">
            <form id="upload-form" action="{{ route('videos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" class="border rounded p-2 w-full" required>
                </div>
                <div class="mb-4">
                    <label for="idioma" class="block text-gray-700 text-sm font-bold mb-2">Idioma:</label>
                    <select name="idioma" id="idioma" class="border rounded p-2 w-full" required>
                        <option value="es_es">Español (España)</option>
                        <option value="es_lat">Español (Latinoamérica)</option>
                        <option value="in">Inglés</option>
                        <option value="in_sub">Inglés (Subtitulado)</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="video" class="block text-gray-700 text-sm font-bold mb-2">Video:</label>
                    <input type="file" name="video" id="video" class="border rounded p-2 w-full">
                </div>
                <div class="mb-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg w-full"
                        id="submit-button"><i class="fa fa-upload pr-1"></i>Subir Video</button>
                    <button type="button"
                        class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded-lg w-full mt-2 hidden"
                        id="cancel-button"><i class="fa fa-ban pr-1"></i>Cancelar Subida</button>
                </div>

            </form>

            <div id="progress-container" class="hidden mt-4">
                <div id="progress-bar" class="bg-blue-500 text-xs leading-none py-1 text-center text-white"
                    style="width: 0%"></div>
            </div>
        </div>

        <div id="video-buttons" class="hidden mt-4">
            <a id="view-video-btn" href="#" target="_blank"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg mr-2">
                Ver Video Embed
            </a>
        </div>
    </div>

    {{-- lista de servidores --}}
    <div class="pt-4">
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Uso del Espacio en Discos</h2>
            <ul class="list-disc pl-5 space-y-2">
                @foreach ($sumasPorDiscoGB as $disco => $tamaño)
                    <li class="text-gray-700">{{ $disco }}: <span
                            class="font-semibold">{{ $tamaño }}</span></li>
                @endforeach
            </ul>
        </div>
    </div>

  {{-- modificado --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const uploadForm = document.getElementById('upload-form');
        const submitButton = document.getElementById('submit-button');
        const progressList = document.getElementById('progress-list');

        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const xhr = new XMLHttpRequest();
            const progressBarId = 'progress-' + Date.now();
            const viewVideoBtnId = 'view-video-' + Date.now();

            const progressBarContainer = document.createElement('div');
            progressBarContainer.innerHTML = `
                <div class="bg-gray-200 rounded-full mb-2">
                    <div id="${progressBarId}" class="bg-blue-500 text-xs leading-none py-1 text-center text-white" style="width: 0%"></div>
                </div>
                <button type="button" class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded-lg mt-2 cancel-upload">Cancelar</button>
                <a id="${viewVideoBtnId}" href="#" target="_blank" class="hidden">Ver Video</a>
            `;
            progressList.appendChild(progressBarContainer);

            const progressBar = document.getElementById(progressBarId);
            const cancelButton = progressBarContainer.querySelector('.cancel-upload');
            const viewVideoBtn = document.getElementById(viewVideoBtnId);

            xhr.open('POST', uploadForm.action, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percentage = (e.loaded / e.total) * 100;
                    progressBar.style.width = percentage + '%';
                    progressBar.textContent = percentage.toFixed(0) + '%';
                }
            };

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    viewVideoBtn.href = '/videos/' + response.id + '/embed';
                    viewVideoBtn.classList.remove('hidden');
                } else {
                    console.error('Error en la carga');
                }
                cancelButton.classList.add('hidden');
                uploadForm.reset();
                submitButton.disabled = false;
            };

            cancelButton.addEventListener('click', function() {
                xhr.abort();
                progressBarContainer.remove();
            });

            xhr.send(formData);
            submitButton.disabled = true;
        });
    });
</script>


</body>

</html>
