{{-- videos/embed.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Embed - {{ $video->title }}</title>
    <!-- Incluir Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-black h-screen overflow-hidden">
    <video class="w-full max-h-screen" controls autoplay>
        <source src="{{ route('videos.play', ['video' => $video->id]) }}" type="video/mp4">
      Your browser does not support the video tag.
      </video>
  </body>
</html>
