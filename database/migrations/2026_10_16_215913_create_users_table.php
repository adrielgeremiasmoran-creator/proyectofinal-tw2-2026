<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('tipos_id');
			$table->string('username', 255);
			$table->string('email', 255);
			$table->string('password', 255);
			$table->string('remember_token', 255)->nullable();

			$table->foreign('tipos_id')
			->references('id')
			->on('tipos')
			->onUpdate('cascade')
			->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('users');
	}
};