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

        $videos = $query->orderBy('id', 'desc')->paginate(20);

        return view('videos.index', compact('videos'));
    }
    // Muestra el formulario para subir videos
    public function create()
    {
        $volumes = config('filesystems.disks');
        $sumasPorDiscoGB = [];
        $espacioLibreDiscoGB = [];

        foreach ($volumes as $name => $config) {
            if (strpos($name, 'volume-ams3-') === 0) { // Verifica que el disco sea un volumen
                $path = $config['root'];
                $totalSize = Video::where('disk', $name)->sum('size'); // Suma el tamaño de los videos por disco
                $freeSpace = disk_free_space($path);

                $sumasPorDiscoGB[$name] = number_format($totalSize / (1024 ** 3), 2) . ' GB';
                $espacioLibreDiscoGB[$name] = number_format($freeSpace / (1024 ** 3), 2) . ' GB';
            }
        }

        return view('videos.create', compact('sumasPorDiscoGB', 'espacioLibreDiscoGB'));
    }



    // Guarda el video subido
    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'nombre' => 'required|string|max:255',
            'idioma' => 'required|in:es_es,es_lat,in,in_sub',
            'video' => 'required|file'
        ]);

        // Selecciona un disco automáticamente o según la lógica de negocio
        $disk = $this->selectDisk();

        // Procesa la subida del archivo
        $videoFile = $request->file('video');
        $path = $videoFile->store('videos', $disk);
        $size = $videoFile->getSize();

        // Guarda la información del video en la base de datos
        $video = Video::create([
            'nombre' => $request->nombre,
            'idioma' => $request->idioma,
            'title' => $videoFile->getClientOriginalName(),
            'path' => $path,
            'disk' => $disk,
            'size' => $size,
        ]);

        return response()->json(['id' => $video->id]);
    }

    private function selectDisk()
    {
        $volumes = config('filesystems.disks');
        $freeSpaces = [];

        foreach ($volumes as $name => $config) {
            if (strpos($name, 'volume-ams3-') === 0) { // Verifica que el disco sea un volumen
                $path = $config['root'];
                $freeSpaces[$name] = disk_free_space($path);
            }
        }

        asort($freeSpaces); // Ordena por espacio libre de menor a mayor
        return array_key_last($freeSpaces); // Retorna el nombre del disco con más espacio libre
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


    public function embed(Video $video, Request $request)
    {
        // Verifica el referente de la solicitud
        $referer = $request->headers->get('referer');
        $allowedReferers = ['http://localhost', 'http://134.209.87.255', 'https://yaske.ru', 'http://yaske.ru','http://68.183.13.79'];

        // Verificar si el referente está en la lista de URL permitidas o si no hay referente
        $isAllowedReferer = false;
        foreach ($allowedReferers as $allowedReferer) {
            if (strpos($referer, $allowedReferer) !== false) {
                $isAllowedReferer = true;
                break;
            }
        }

        // Verificar el User-Agent para identificar si la solicitud proviene de una WebView de Android
        $userAgent = $request->header('User-Agent');
        $isWebView = strpos($userAgent, 'wv') !== false;

        // Permitir el acceso si no hay referente, si el referente está permitido o si es una WebView
        if (empty($referer) || $isAllowedReferer || $isWebView) {
            return view('videos.embed', compact('video'));
        }

        // Si el referente no es permitido y no es una WebView, redirige a una URL externa con un código aleatorio
        $randomCode = mt_rand(100000000000, 999999999999); // Genera un número aleatorio de 12 dígitos
        return redirect()->away("https://ok.ru/video/{$randomCode}");
    }


    /*  public function embed(Video $video)
    {
        // Sin verificar el referente, permitimos el acceso a la vista de embed directamente
        return view('videos.embed', compact('video'));
    } */



    public function play(Request $request, Video $video)
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
