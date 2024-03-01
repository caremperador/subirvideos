{{-- videos/embed.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Embed - {{ $video->title }}</title>
    <!-- Incluir Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      /* Estilos adicionales si necesitas mayor control sobre el video */
      video {
        /* Eliminar la altura máxima si es necesario para permitir el centrado vertical */
        max-height: none;
      }
      @media (min-aspect-ratio: 16/9) {
        video {
          height: 100vh; /* Altura completa en pantallas anchas */
          width: auto;  /* Permitir que el ancho sea automático */
        }
      }
      @media (max-aspect-ratio: 16/9) {
        video {
          width: 100vw; /* Ancho completo en pantallas altas */
          height: auto; /* Permitir que la altura sea automática */
        }
      }
    </style>
</head>
<body class="bg-black flex justify-center items-center h-screen overflow-hidden">
    <!-- El contenedor flex centra el video en la pantalla -->
    <div class="flex justify-center items-center w-full h-full">
      <video controls autoplay playsinline class="max-w-full max-h-full">
        <source src="{{ route('videos.play', ['video' => $video->id]) }}" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    </div>
  </body>
</html>



