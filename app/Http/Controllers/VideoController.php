<?php

namespace App\Http\Controllers;

use App\Models\Video; // Importa el modelo Video
use Illuminate\Http\Request; // Importa la clase Request para manejar solicitudes HTTP
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage; // Importa la fachada Storage para operaciones de archivos
use Symfony\Component\HttpFoundation\StreamedResponse;

// Clase VideoController que extiende de Controller
class VideoController extends Controller
{

    public function index(Request $request)
    {
        $query = Video::query();

        // Si se recibe un término de búsqueda, filtrar los resultados
        if ($search = $request->get('q')) {
            $query->where('nombre', 'LIKE', '%' . $search . '%')
                ->orWhere('title', 'LIKE', '%' . $search . '%'); // Puedes añadir más campos aquí
        }

        $videos = $query->orderBy('id', 'desc')->paginate(5);

        return view('videos.index', compact('videos'));
    }
    // Muestra el formulario para subir videos
    public function create()
    {
        // Retorna una vista llamada 'videos.create'
        return view('videos.create');
    }


    // Guarda el video subido
    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'nombre' => 'required|string|max:255',
            'idioma' => 'required|in:es_es,es_lat,in,in_sub',
            'video' => 'required|file|mimes:mp4'
        ]);

        // Selecciona un disco automáticamente o según la lógica de negocio
        $disk = $this->selectDisk();

        // Procesa la subida del archivo
        $videoFile = $request->file('video');
        $path = $videoFile->store('videos', $disk);

        // Guarda la información del video en la base de datos
        $video = Video::create([
            'nombre' => $request->nombre,
            'idioma' => $request->idioma,
            'title' => $videoFile->getClientOriginalName(),
            'path' => $path,
            'disk' => $disk,
        ]);

        return response()->json(['id' => $video->id]);
    }

    private function selectDisk()
    {
        // Lógica para seleccionar un disco. Puede ser tan simple o compleja como necesites.
        // Por ejemplo, devolver un disco fijo o basarse en algún criterio:
        return 'volume_ams3_01'; // o cualquier otro disco
    }


    // Método para eliminar un video
    public function destroy(Video $video)
    {
        if (Storage::disk($video->disk)->exists($video->path)) {
            Storage::disk($video->disk)->delete($video->path);
        }

        $video->delete();
        return redirect()->route('videos.index')->with('success', 'Video eliminado con éxito.');
    }


    public function embed(Video $video)
    {
        // Verifica el referente de la solicitud
        $referer = request()->headers->get('referer');
        $allowedReferer = 'http://localhost'; // URL de tu sitio permitido

        // // Verificar si el referente contiene la URL permitida
        // if (strpos($referer, $allowedReferer) === false) {
        //     // Si el referente no es el permitido, muestra una página de error o niega el acceso
        //     return view('error.no-permission');
        // }

        // Si el referente es válido, muestra la vista de embed
        return view('videos.embed', compact('video'));
    }

    public function play(Request $request, Video $video, $token)
    {
        // Verificar que el token proporcionado coincida con el token del video
        // if ($token !== $video->embed_token) {
        //     abort(403);
        // }

        // Opcionalmente, puedes limpiar el token aquí si solo debe ser usado una vez
        // $video->embed_token = null;
        // $video->save();

        /** @var mixed */
        $storage = Storage::disk($video->disk);
        $videoPath = $storage->path($video->path);

        if (!file_exists($videoPath)) {
            abort(404, "El video no se encontró.");
        }
        $size = filesize($videoPath);
        $stream = fopen($videoPath, 'r');

        // Tamaño máximo de segmento
        $maxChunkSize = 1024 * 1024; // 1MB

        // Encabezado Range de la solicitud
        $range = $request->header('Range', null);
        if ($range !== null) {
            list(, $range) = explode('=', $range, 2);
            list($start, $end) = explode('-', $range, 2);

            if ($end === '') {
                $end = min($start + $maxChunkSize - 1, $size - 1);
            }

            $length = $end - $start + 1;
            fseek($stream, $start);
            $statusCode = 206; // Parcial content
            $headers = [
                'Content-Range' => sprintf('bytes %s-%s/%s', $start, $end, $size),
                'Accept-Ranges' => 'bytes',
                'Content-Length' => $length,
            ];
        } else {
            $start = 0;
            $end = $maxChunkSize - 1;
            $statusCode = 200;
            $headers = [
                'Content-Length' => $size,
            ];
        }

        return new StreamedResponse(function () use ($stream, $start, $end) {
            fseek($stream, $start);
            while (!feof($stream) && ftell($stream) <= $end) {
                echo fread($stream, 2048);
                flush();
            }
            fclose($stream);
        }, $statusCode, $headers + [
            'Content-Type' => 'video/mp4',
            'Content-Disposition' => 'inline; filename="' . basename($videoPath) . '"',
        ]);
    }
}
