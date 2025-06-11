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
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            $table->unsignedBigInteger('bidang_keahlian_id')->nullable()->after('ipk');

            $table->foreign('bidang_keahlian_id')
                ->references('id')
                ->on('m_bidang_keahlian')
                ->onDelete('set null'); // Optional: sesuaikan jika ingin hapus ke null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['bidang_keahlian_id']);
            $table->dropColumn('bidang_keahlian_id');
        });
    }
};
