<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MiControlador extends Controller
{
    public function mostrarVista()
{
    // Lógica para preparar datos si es necesario

    return view('mostrar-vista'); // Reemplaza 'nombre-de-la-vista' con el nombre real de tu vista
}

}
