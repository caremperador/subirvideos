<?php

namespace App\Http\Controllers;

use App\Models\Video; // Importa el modelo Video
use Illuminate\Http\Request; // Importa la clase Request para manejar solicitudes HTTP
use Illuminate\Support\Facades\Storage; // Importa la fachada Storage para operaciones de archivos

// Clase VideoController que extiende de Controller
class VideoController extends Controller
{
    // Muestra el formulario para subir videos
    public function create()
    {
        // Retorna una vista llamada 'videos.create'
        return view('videos.create');
    }

    // Guarda el video subido
    public function store(Request $request)
    {
        // Valida la solicitud asegurando que se haya subido un archivo de tipo 'mp4'
        // y validando los campos 'nombre' e 'idioma'
        $request->validate([
            'nombre' => 'required|string|max:255',
            'idioma' => 'required|in:es_es,es_lat,in,in_sub',
            'video' => 'required|file|mimes:mp4'
        ]);

        // Obtiene el archivo de video de la solicitud
        $videoFile = $request->file('video');
        // Almacena el archivo en el disco 'public' en la carpeta 'videos'
        $path = $videoFile->store('videos', 'public');

        // Crea un nuevo registro de video con los datos proporcionados
        $video = Video::create([
            'nombre' => $request->nombre, // Almacena el nombre del video
            'idioma' => $request->idioma, // Almacena el idioma del video
            'title' => $videoFile->getClientOriginalName(), // Almacena el título original del archivo
            'path' => $path // Almacena la ruta del archivo
        ]);

        return response()->json(['id' => $video->id]);
    }


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


    // Método para eliminar un video
    public function destroy(Video $video)
    {
        // Verifica si el archivo existe en el disco 'public'
        if (Storage::disk('public')->exists($video->path)) {
            // Elimina el archivo del disco 'public'
            Storage::disk('public')->delete($video->path);
        }

        // Elimina el registro del video de la base de datos
        $video->delete();

        // Redirige a la ruta 'videos.index' con un mensaje de éxito
        return redirect()->route('videos.index')->with('success', 'Video eliminado con éxito.');
    }
    public function embed(Video $video)
    {
        // Verifica el referente de la solicitud
        $referer = request()->headers->get('referer');
        $allowedReferer = 'http://yaske.test:8004'; // URL de tu sitio permitido

        // Verificar si el referente contiene la URL permitida
        if (strpos($referer, $allowedReferer) === false) {
            // Si el referente no es el permitido, muestra una página de error o niega el acceso
            return view('error.no-permission');
        }

        // Si el referente es válido, muestra la vista de embed
        return view('videos.embed', compact('video'));
    }

    public function play(Request $request, Video $video, $token)
    {
        // Verificar que el token proporcionado coincida con el token del video
        if ($token !== $video->embed_token) {
            abort(403);
        }

        // Opcionalmente, puedes limpiar el token aquí si solo debe ser usado una vez
        // $video->embed_token = null;
        // $video->save();

        $videoPath = storage_path('app/public/' . $video->path);

        if (!file_exists($videoPath)) {
            abort(404, "El video no se encontró.");
        }

        // Servir el video directamente desde la ruta
        return response()->file($videoPath);
    }
}
