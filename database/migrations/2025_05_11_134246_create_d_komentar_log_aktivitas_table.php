<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDKomentarLogAktivitasTable extends Migration
{
    public function up()
    {
        Schema::create('d_komentar_log_aktivitas', function (Blueprint $table) {
            $table->bigIncrements('komentar_id');
            $table->unsignedBigInteger('aktivitas_id');
            $table->unsignedBigInteger('pengirim_id');
            $table->text('komentar');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('aktivitas_id')
                  ->references('aktivitas_id')->on('t_log_aktivitas_mhs');
        });
    }

    public function down()
    {
        Schema::table('d_komentar_log_aktivitas', function (Blueprint $table) {
            $table->dropForeign(['aktivitas_id']);
        });
        Schema::dropIfExists('d_komentar_log_aktivitas');
    }
}
