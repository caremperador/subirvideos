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

        // Obtener la suma del tamaño de los archivos por disco
        $sumasPorDisco = Video::groupBy('disk')
            ->selectRaw('disk, sum(size) as totalSize')
            ->pluck('totalSize', 'disk');

        // Convertir a GB
        $sumasPorDiscoGB = $sumasPorDisco->map(function ($size) {
            return number_format($size / 1024 / 1024 / 1024, 2) . ' GB';
        });

        return view('videos.create', ['sumasPorDiscoGB' => $sumasPorDiscoGB]);
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
        // Lógica para seleccionar un disco. Puede ser tan simple o compleja como necesites.
        // Por ejemplo, devolver un disco fijo o basarse en algún criterio:
        return 'volume-ams3-01'; // o cualquier otro disco
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
      /*   // Verifica el referente de la solicitud
        $referer = request()->headers->get('referer');
        $allowedReferers = ['http://localhost', 'http://134.209.87.255', 'https://134.209.87.255'];

        // Verificar si el referente está en la lista de URL permitidas
        $isAllowedReferer = false;
        foreach ($allowedReferers as $allowedReferer) {
            if (strpos($referer, $allowedReferer) !== false) {
                $isAllowedReferer = true;
                break;
            }
        }

        // Si el referente no es permitido, redirige a una URL externa con un código aleatorio
        if (!$isAllowedReferer) {
            $randomCode = mt_rand(100000000000, 999999999999); // Genera un número aleatorio de 12 dígitos
            return redirect()->away("https://ok.ru/video/{$randomCode}");
        }
 */
        // Si el referente es válido, muestra la vista de embed
        return view('videos.embed', compact('video'));
    }




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
