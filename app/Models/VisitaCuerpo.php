<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitaCuerpo extends Model
{
    protected $table = 'visita_cuerpos';

    const CREATED_AT = null;

    protected $fillable = [
        'visita_cabecera_id',
        'nombre',
        'cedula',
        'correo',
        'estado',
    ];

    public function cabecera()
    {
        return $this->belongsTo(VisitaCabecera::class, 'visita_cabecera_id');
    }
}
