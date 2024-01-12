<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuarioProhibidoController extends Controller
{
    public function index()
    {
        return view('videos.usuarioProhibido');
    }
}
