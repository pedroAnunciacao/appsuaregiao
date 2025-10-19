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
        Schema::create('pais_regiao_cidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pais_id')->constrained('paises')->onDelete('cascade');
            $table->foreignId('regiao_id')->constrained('paises_regioes')->onDelete('cascade');
            $table->string('nome', 50)->nullable();
            $table->string('uf', 50)->nullable();
            $table->char('ativo', 1)->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pais_regiao_cidades');
    }
};
