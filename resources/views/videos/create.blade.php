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
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Subir Videos!</h1>
        <a href="{{ route('videos.index') }}" target="_blank"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg mb-4">
            <i class="fas fa-list-ul pr-1"></i> ir Lista de Videos
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


    <!-- Contenedor de videos subidos -->
    <div id="videos-subidos" class="mt-4 space-y-4">
        <!-- Aquí se agregarán los videos subidos dinámicamente -->
    </div>


    <div class="bg-white shadow overflow-hidden sm:rounded-lg">

        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
          <h2 class="text-lg leading-6 font-medium text-gray-900">
            Uso del Espacio en Discos
          </h2>
        </div>
      
        <div class="max-w-full overflow-hidden bg-white shadow">
      
          <table class="min-w-full text-sm divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-4 py-2 font-medium text-left text-gray-900 whitespace-nowrap">
                  Servidor
                </th>
                <th class="px-4 py-2 font-medium text-right text-gray-900 whitespace-nowrap">
                  Espacio Usado
                </th>
                <th class="px-4 py-2 font-medium text-right text-gray-900 whitespace-nowrap">
                  Espacio Libre
                </th>
              </tr>
            </thead>
      
            <tbody class="divide-y divide-gray-100">

                @foreach ($sumasPorDiscoGB as $disco => $tamaño)
              
                  <tr class="hover:bg-gray-50">
                    
                    <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap">
                      {{ $disco }}  
                    </td>
              
                    <td class="px-4 py-2 text-right text-gray-500 whitespace-nowrap hover:text-purple-500">
                      {{ $tamaño }} 
                    </td>
              
                    <td class="px-4 py-2 text-right text-gray-500 whitespace-nowrap">
                      {{ $espacioLibreDiscoGB[$disco] }} 
                    </td>
              
                  </tr>
              
                @endforeach
              
              </tbody>
              
              </table>
      
        </div>
      
      </div>

      
    
      <script>
        let rows = document.querySelectorAll('tbody tr');
      
        rows.forEach(row => {
          row.addEventListener('mouseover', () => {
            row.classList.add('bg-purple-50');
          });
          
          row.addEventListener('mouseout', () => {  
            row.classList.remove('bg-purple-50');
          });
        });
      </script>

    {{-- modificado --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadForm = document.getElementById('upload-form');
            const videosSubidosContainer = document.getElementById('videos-subidos');
        
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const xhr = new XMLHttpRequest();
        
                xhr.open('POST', this.action, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('input[name="_token"]').value);
        
                const videoContainer = document.createElement('div');
                videoContainer.className = 'bg-white p-4 rounded-lg shadow-md mb-4';
        
                const videoInfo = document.createElement('p');
                videoInfo.textContent = `Nombre: ${document.getElementById('nombre').value} - Idioma: ${document.getElementById('idioma').options[document.getElementById('idioma').selectedIndex].text}`;
                videoContainer.appendChild(videoInfo);
        
                const progressBar = document.createElement('div');
                progressBar.className = 'bg-blue-500 text-xs leading-none py-1 text-center text-white';
                progressBar.style.width = '0%';
                videoContainer.appendChild(progressBar);
        
                const cancelButton = document.createElement('button');
                cancelButton.textContent = 'Cancelar';
                cancelButton.className = 'bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg mt-2';
                cancelButton.type = 'button';
                videoContainer.appendChild(cancelButton);
        
                videosSubidosContainer.appendChild(videoContainer);
        
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        const percentage = (e.loaded / e.total) * 100;
                        progressBar.style.width = percentage + '%';
                        progressBar.textContent = `${percentage.toFixed(0)}%`;
                    }
                };
        
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            videoInfo.innerHTML += ' - <span class="text-green-500">Subido con éxito.</span>';
                            progressBar.remove();
                            cancelButton.remove();
                            // Procesa la respuesta exitosa aquí
                        } catch (error) {
                            // Si la respuesta no es JSON válido
                            videoInfo.innerHTML += ' - <span class="text-red-500">Error al procesar la respuesta</span>';
                            progressBar.className = 'bg-red-500';
                        }
                    } else {
                        // Manejo de errores no relacionados con el JSON
                        videoInfo.innerHTML += ` - <span class="text-red-500">Error en la carga: ${xhr.status} ${xhr.statusText}</span>`;
                        progressBar.className = 'bg-red-500';
                    }
                };
        
                xhr.onerror = function() {
                    // Manejo de errores de red
                    videoInfo.innerHTML += ' - <span class="text-red-500">Error en la red o la solicitud fue abortada</span>';
                    progressBar.className = 'bg-red-500';
                };
        
                cancelButton.addEventListener('click', function() {
                    xhr.abort();
                    videoContainer.remove();
                });
        
                xhr.send(formData);
            });
        });
        </script>





<footer>
    {{-- Verifica si hay un usuario autenticado --}}
    @auth
        {{-- Verifica si el usuario tiene el rol 'admin' --}}
        @if(auth()->user()->hasRole('admin'))
            <p>Bienvenido, administrador. Estás en el panel de control de admin.</p>
        {{-- Verifica si el usuario tiene el rol 'uploader' --}}
        @elseif(auth()->user()->hasRole('uploader'))
            <p>Bienvenido, uploader. Puedes subir y gestionar tus videos aquí.</p>
        @else
            <p>Bienvenido a nuestro sitio.</p>
        @endif
    @else
        <p>Bienvenido, por favor inicia sesión o regístrate.</p>
    @endauth
    {{-- Otros elementos del footer --}}
</footer>

</body>

</html>
