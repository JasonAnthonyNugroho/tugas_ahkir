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
        Schema::create('pertandingans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained('sports')->onDelete('cascade');
            $table->foreignId('team_a_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('team_b_id')->constrained('teams')->onDelete('cascade');
            $table->integer('score_a')->default(0);
            $table->integer('score_b')->default(0);
            $table->dateTime('waktu_tanding');
            $table->string('lokasi');
            $table->enum('status', ['scheduled', 'live', 'finished'])->default('scheduled');
            $table->timestamp('selesai_pada')->nullable();
            $table->string('babak')->nullable(); // Contoh: Quarter Final, Final
            $table->string('format_tanding')->default('Knockout'); // BO3 atau BO5
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertandingans');
    }
};
