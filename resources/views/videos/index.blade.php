<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Videos</title>
    <!-- Incluir Tailwind CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Lista de Videos</h1>
    <div class="flex flex-col">
        @foreach ($videos as $video)
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">{{ $video->title }}</h2>
                        <p class="text-gray-600">Subido el: {{ $video->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="flex items-center">
                        <a href="{{ route('videos.embed', $video) }}" target="_blank" class="text-white bg-blue-500 hover:bg-blue-600 font-bold py-2 px-4 rounded-lg mr-2">
                            Ver Embed
                        </a>
                        <form action="{{ route('videos.destroy', $video) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-white bg-red-500 hover:bg-red-600 font-bold py-2 px-4 rounded-lg">
                                Eliminar
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
</body>
</html>
