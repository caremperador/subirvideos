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
        $request->validate([
            'video' => 'required|file|mimes:mp4'
        ]);

        // Obtiene el archivo de video de la solicitud
        $video = $request->file('video');
        // Almacena el archivo en el disco 'public' en la carpeta 'videos'
        $path = $video->store('videos', 'public');

        // Tu validación y lógica de almacenamiento

        $video = Video::create([
            'title' => $video->getClientOriginalName(),
            'path' => $path
        ]);

        return response()->json(['id' => $video->id]);
    }

    public function index()
    {
        // Ordenar videos por id y paginar
        $videos = Video::orderBy('id', 'desc')->paginate(5);

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
        return view('videos.embed', compact('video'));
    }
}
