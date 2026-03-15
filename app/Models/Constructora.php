<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Constructora extends Model
{
    use HasFactory;

    protected $table = 'constructoras';

    protected $fillable = [
        'nombre',
        'nit',
        'direccion',
        'ciudad',
        'telefono',
        'email',
        'representante_legal',
        'fecha_creacion',
        'estado',
    ];

    protected $casts = [
        'fecha_creacion' => 'date',
    ];

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }
}

