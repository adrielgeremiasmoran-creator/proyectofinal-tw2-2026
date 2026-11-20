<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('calificaciones', function (Blueprint $table) {
			$table->id();
			$table->foreignId('materias_x_usuarios_id')->constrained('materias_x_usuarios')->onDelete('cascade');
			$table->decimal('calificacion', 4, 2);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('calificaciones');
	}
};