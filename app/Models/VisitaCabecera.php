<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitaCabecera extends Model
{
    protected $table = 'visita_cabeceras';

    protected $fillable = [
        'proyecto_id',
        'user_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'datetime',
            'fecha_fin'    => 'datetime',
        ];
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cuerpos()
    {
        return $this->hasMany(VisitaCuerpo::class, 'visita_cabecera_id');
    }
}
