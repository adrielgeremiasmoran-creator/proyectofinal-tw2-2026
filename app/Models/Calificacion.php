<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
	public $timestamps = false;

	protected $table = 'calificaciones';

	protected $fillable = [
		'calificacion',
		'materias_x_usuarios_id'
	];

	// Relación: La calificación pertenece a una asignación materias_x_usuarios
	public function materiasXUsuario(){
		return $this->belongsTo(MateriasXUsuario::class, 'materias_x_usuarios_id');
	}
}