<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuppImage extends Model
{
    protected $table = 'supp_images';

    public $timestamps = false;

    protected $fillable = [
        'publicacion_id',
        'directorio',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function publicacion()
    {
        return $this->belongsTo(Publicacion::class);
    }
}
