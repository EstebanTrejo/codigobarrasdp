<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoDeBarras extends Model
{
    protected $table = 'codigos_de_barras'; // Nombre de la tabla en la base de datos
    public $timestamps = false; // Deshabilitar los timestamps automáticos

    protected $fillable = [
        'fecha',
        'datos_del_codigo',
        'impreso',
    ];

    // Agrega otros métodos, relaciones y configuraciones según tus necesidades
}
