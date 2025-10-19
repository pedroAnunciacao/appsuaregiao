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
        Schema::create('paises', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 60)->nullable();
            $table->string('nome_pt', 60)->nullable();
            $table->string('sigla', 2)->nullable();
            $table->foreignId('continente_id')->nullable()->constrained('paises_continentes')->onDelete('set null');
            $table->integer('ativo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paises');
    }
};
