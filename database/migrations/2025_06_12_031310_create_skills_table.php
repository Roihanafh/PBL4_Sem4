<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillsTable extends Migration
{
    public function up()
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();                  // skill ID
            $table->string('nama')->unique();
            $table->timestamps();         // optional
        });
    }

    public function down()
    {
        Schema::dropIfExists('skills');
    }
}
