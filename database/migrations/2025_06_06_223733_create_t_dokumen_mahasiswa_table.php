<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_dokumen_mahasiswa', function (Blueprint $table) {
            $table->unsignedBigInteger('mhs_nim')->constrained('m_mahasiswa')->onDelete('cascade');
            $table->unsignedBigInteger('jenis_dokumen_id');
            $table->foreign('jenis_dokumen_id')
                    ->references('jenis_dokumen_id')
                    ->on('m_jenis_dokumen')
                    ->onDelete('cascade');

            $table->string('label')->nullable();
            $table->string('nama')->nullable();
            $table->string('path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_dokumen_mahasiswa');
    }
};
