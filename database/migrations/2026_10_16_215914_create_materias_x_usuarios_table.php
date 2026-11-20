<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('materias_x_usuarios', function (Blueprint $table) {
			$table->id();
			$table->foreignId('users_id')->constrained('users')->onDelete('cascade');
			$table->foreignId('materias_id')->constrained('materias')->onDelete('cascade');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('materias_x_usuarios');
	}
};