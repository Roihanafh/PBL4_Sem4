<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTLogAktivitasMhsTable extends Migration
{
    public function up()
    {
        Schema::create('t_log_aktivitas_mhs', function (Blueprint $table) {
            $table->bigIncrements('aktivitas_id');
            $table->string('mhs_nim', 20);
            $table->text('keterangan');
            $table->timestamp('waktu')->useCurrent();

            $table->foreign('mhs_nim')
                  ->references('mhs_nim')->on('m_mahasiswa');
        });
    }

    public function down()
    {
        Schema::table('t_log_aktivitas_mhs', function (Blueprint $table) {
            $table->dropForeign(['mhs_nim']);
        });
        Schema::dropIfExists('t_log_aktivitas_mhs');
    }
}
