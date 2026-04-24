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
        Schema::create('pertandingans', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel teams (Prodi A vs Prodi B)
            $table->foreignId('team_a_id')->constrained('teams');
            $table->foreignId('team_b_id')->constrained('teams');

            // Skor Pertandingan
            $table->integer('score_a')->default(0);
            $table->integer('score_b')->default(0);

            // Detail Jadwal & Lokasi manual sesuai sketsa Kamu
            $table->dateTime('waktu_tanding');
            $table->string('lokasi'); // Contoh: "GOR UKDW"

            // Status untuk indikator LIVE merah
            $table->enum('status', ['scheduled', 'live', 'finished'])->default('scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
