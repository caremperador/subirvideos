<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Videos</title>
    <!-- Incluir Tailwind CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Lista de Videos</h1>
        <a href="{{ route('videos.create') }}" target="_blank"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg mb-4">
            <i class='fa fa-upload mr-1'> </i>Subir Videos
        </a>
        <!-- Formulario de búsqueda -->
        <form action="{{ route('videos.index') }}" method="GET" class="mb-6">
            <div class="flex mt-4">
                <input type="text" name="q" class="mr-3 p-2 border rounded w-full"
                    placeholder="Buscar videos...">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class='fa fa-search mr-1'></i>Buscar
                </button>
            </div>
        </form>
        <div class="flex flex-col">
            @foreach ($videos as $video)
                <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-blue-700">{{ $video->nombre }}</h2>
                            <p class="text-gray-600"><b>Idioma:</b>
                                @switch($video->idioma)
                                    @case('es_es')
                                        Español (España)
                                    @break

                                    @case('es_lat')
                                        Español (Latinoamérica)
                                    @break

                                    @case('in')
                                        Inglés
                                    @break

                                    @case('in_sub')
                                        Inglés (Subtitulado)
                                    @break

                                    @default
                                        Desconocido
                                @endswitch
                            </p>
                            <p class="text-gray-600"><b>Título del Archivo:</b> {{ $video->title }}</p>
                            <p class="text-gray-600"><b>Subido el:</b> {{ $video->created_at->format('d/m/Y H:i') }}</p>
                            <p class="text-gray-600"><b>Almacenado en: </b>{{ $video->disk }}</p> 
                            <p class="text-gray-600"><b>Tamaño del Archivo: </b>{{ number_format($video->size / 1024 / 1024 / 1024, 2) }} GB</p>
                        </div>
                        <div class="flex items-center">
                            <a href="{{ route('videos.embed', $video) }}" target="_blank"
                                class="text-white bg-blue-500 hover:bg-blue-600 font-bold py-2 px-4 rounded-lg mr-2">
                                <i class='fas fa-eye mr-1'></i>Ver Embed
                            </a>
                            <button onclick="copiarEnlace('{{ route('videos.embed', $video) }}', this)"
                                class="text-white bg-blue-500 hover:bg-blue-600 font-bold py-2 px-4 rounded-lg mr-2">
                                <i class="fas fa-clipboard"></i> Copiar Enlace
                            </button>
                            <form action="{{ route('videos.destroy', $video) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-white bg-red-500 hover:bg-red-600 font-bold py-2 px-4 rounded-lg">
                                    <i class='fa fa-trash mr-1'></i>Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
            {{ $videos->links() }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.js"></script>
    <script>
        function copiarEnlace(enlace, boton) {
            navigator.clipboard.writeText(enlace).then(() => {
                // Cambia el texto del botón y añade un ícono de verificación
                boton.innerHTML = '<i class="fas fa-check"></i> Enlace Copiado!';
                // Opcionalmente, vuelve al texto original después de 2 segundos
                setTimeout(() => {
                    boton.innerHTML = '<i class="fas fa-clipboard"></i> Copiar Enlace';
                }, 2000);
            }).catch(err => {
                console.error('Error al copiar el enlace: ', err);
            });
        }
    </script>

</body>

</html>
