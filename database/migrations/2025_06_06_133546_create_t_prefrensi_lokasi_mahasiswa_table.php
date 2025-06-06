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
        Schema::create('t_prefrensi_lokasi_mahasiswa', function (Blueprint $table) {
    $table->id();

    $table->string('mhs_nim', 20);

    $table->unsignedBigInteger('negara_id');
    $table->unsignedBigInteger('provinsi_id')->nullable();
    $table->unsignedBigInteger('kabupaten_id')->nullable();
    $table->unsignedBigInteger('kecamatan_id')->nullable();
    $table->unsignedBigInteger('desa_id')->nullable();

    $table->string('nama_tampilan', 255);
    $table->timestamps();

    // Foreign key manual karena mhs_nim bertipe string
    $table->foreign('mhs_nim')->references('mhs_nim')->on('m_mahasiswa')->onDelete('cascade');
    $table->foreign('negara_id')->references('id')->on('m_negara')->onDelete('cascade');
    $table->foreign('provinsi_id')->references('id')->on('m_provinsi')->onDelete('cascade');
    $table->foreign('kabupaten_id')->references('id')->on('m_kabupaten')->onDelete('cascade');
    $table->foreign('kecamatan_id')->references('id')->on('m_kecamatan')->onDelete('cascade');
    $table->foreign('desa_id')->references('id')->on('m_desa')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_prefrensi_lokasi_mahasiswa');
    }
};
