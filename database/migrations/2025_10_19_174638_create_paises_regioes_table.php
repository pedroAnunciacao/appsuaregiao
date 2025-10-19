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
        Schema::create('paises_regioes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 60)->nullable();
            $table->foreignId('pais_id')->nullable()->constrained('paises')->onDelete('cascade');
            $table->char('ativo', 1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paises_regioes');
    }
};
