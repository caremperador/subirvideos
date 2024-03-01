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
            position: fixed; /* Se asegura de que el contenedor sea fijo y cubra toda la pantalla */
            top: 0;
            left: 0;
            width: 100vw; /* 100% del ancho de la ventana gráfica */
            height: 100vh; /* 100% del alto de la ventana gráfica */
            display: flex; /* Usa flexbox para centrar el contenido */
            justify-content: center; /* Centra horizontalmente */
            align-items: center; /* Centra verticalmente */
            background-color: black; /* Fondo negro para el contenedor, proporciona contraste */
        }
        .video-container video {
            /* El máximo tamaño del video es el 100% del contenedor pero manteniendo su aspecto original */
            max-width: 100%;
            max-height: 100vh; /* Esto asegura que los subtítulos sean visibles en la pantalla */
            object-fit: contain; /* Mantiene el aspecto completo del video */
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
