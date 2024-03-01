<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Embed - {{ $video->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black h-screen overflow-hidden flex justify-center items-center">
    <video id="myVideo" class="w-full max-h-screen" controls>
        <source src="{{ route('videos.play', ['video' => $video->id]) }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
  
<div id="resumeDialog" class="hidden absolute top-0 left-0 right-0 bottom-0 bg-black bg-opacity-50 flex justify-center items-center">
    <div class="bg-black text-white p-4 rounded-lg text-center border border-gray-600 border-shadow-white">
        <p class="mb-4">¿Deseas retomar donde te quedaste (<span id="timePosition"></span>) o empezar de nuevo?</p>
        <button id="resume" class="bg-green-800 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">Retomar</button>
        <button id="restart" class="bg-red-800 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Empezar de nuevo</button>
    </div>
</div>

   <script>
  document.addEventListener('DOMContentLoaded', function () {
    const video = document.getElementById('myVideo');
    const resumeDialog = document.getElementById('resumeDialog');
    const timePosition = document.getElementById('timePosition'); // Elemento para mostrar la posición
    const resumeButton = document.getElementById('resume');
    const restartButton = document.getElementById('restart');

    // Formatear el tiempo en HH:MM:SS
    function formatTime(time) {
      const hours = Math.floor(time / 3600).toString().padStart(2, '0');
      const minutes = Math.floor((time % 3600) / 60).toString().padStart(2, '0');
      const seconds = Math.floor(time % 60).toString().padStart(2, '0');
      return `${hours}:${minutes}:${seconds}`;
    }

    // Intentar recuperar la última posición del video
    const videoPosition = localStorage.getItem('videoPosition');
    if (videoPosition) {
      timePosition.textContent = formatTime(videoPosition); // Actualizar el texto con el tiempo formateado
      resumeDialog.classList.remove('hidden'); // Mostrar el diálogo
    }

    resumeButton.addEventListener('click', function() {
      video.currentTime = parseFloat(videoPosition);
      video.play();
      resumeDialog.classList.add('hidden'); // Ocultar el diálogo
    });

    restartButton.addEventListener('click', function() {
      localStorage.removeItem('videoPosition'); // Eliminar la posición guardada
      video.currentTime = 0;
      video.play();
      resumeDialog.classList.add('hidden'); // Ocultar el diálogo
    });

    video.addEventListener('timeupdate', function () {
      localStorage.setItem('videoPosition', video.currentTime);
    });
  });
</script>


</body>
</html>
