<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTRiwayatMagangTable extends Migration
{
    public function up()
    {
        Schema::create('t_riwayat_magang', function (Blueprint $table) {
            $table->bigIncrements('riwayat_id');
            $table->unsignedBigInteger('lamaran_id');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->float('nilai')->nullable();

            $table->foreign('lamaran_id')
                  ->references('lamaran_id')->on('t_lamaran_magang');
        });
    }

    public function down()
    {
        Schema::table('t_riwayat_magang', function (Blueprint $table) {
            $table->dropForeign(['lamaran_id']);
        });
        Schema::dropIfExists('t_riwayat_magang');
    }
}
