<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannedWordsTable extends Migration
{
    public function up()
    {
        // Schema::create('banned_words', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('word')->unique();
        //     $table->timestamps();
        // });
    }

    public function down()
    {
        // Schema::dropIfExists('banned_words');
    }
}
