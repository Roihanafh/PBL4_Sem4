<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTFeedbackTable extends Migration
{
    public function up()
    {
        Schema::create('t_feedback', function (Blueprint $table) {
            $table->bigIncrements('feedback_id');
            $table->string('mhs_nim', 20);
            $table->enum('target_type', ['riwayat','perusahaan','lowongan']);
            $table->unsignedBigInteger('target_id');
            $table->tinyInteger('rating')->nullable();
            $table->text('komentar');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('mhs_nim')
                  ->references('mhs_nim')->on('m_mahasiswa');
        });
    }

    public function down()
    {
        Schema::table('t_feedback', function (Blueprint $table) {
            $table->dropForeign(['mhs_nim']);
        });
        Schema::dropIfExists('t_feedback');
    }
}
