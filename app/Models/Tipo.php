<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
	public $timestamps = false;
	protected $table = 'tipos';
	protected $fillable = [
		'tipo'
	];

	// Relación: Un tipo puede tener muchos usuarios
	public function users(){
		return $this->hasMany(User::class, 'tipos_id');
	}
}