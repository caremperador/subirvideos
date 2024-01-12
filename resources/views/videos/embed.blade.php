{{-- videos/embed.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Embed - {{ $video->title }}</title>
    <!-- Incluir Tailwind CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden; /* Elimina la barra de desplazamiento */
        }
        .video-container {
            position: fixed; /* Cambiado de relative a fixed */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Asegura que el video cubra todo el espacio */
        }
    </style>
</head>
<body class="h-full">
    <div class="video-container">
        <video controls autoplay>
            <source src="{{ route('videos.play', ['video' => $video->id]) }}" type="video/mp4">
            Tu navegador no soporta la etiqueta video.
        </video>
    </div>
</body>
</html>

