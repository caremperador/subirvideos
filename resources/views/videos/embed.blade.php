{{-- videos/embed.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Embed - {{ $video->title }}</title>
    <style>
        .video-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
        }
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>

<div class="video-container">
    <video controls autoplay>
        {{-- Asegúrate de que la URL aquí sea accesible y correcta --}}
        <source src="{{ asset('storage/' . $video->path) }}" type="video/mp4">
        Tu navegador no soporta la etiqueta video.
    </video>
</div>

</body>
</html>
