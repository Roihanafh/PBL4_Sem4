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
        Schema::table('t_notifikasi', function (Blueprint $table) {
            $table->dropColumn('penerima_id', 'tipe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_notifikasi', function (Blueprint $table) {
            $table->unsignedBigInteger('penerima_id')->nullable(); 
            $table->string('tipe')->nullable();
        });
    }
};
