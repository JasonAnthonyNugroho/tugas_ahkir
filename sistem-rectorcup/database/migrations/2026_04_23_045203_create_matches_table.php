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
        Schema::table('pertandingans', function (Blueprint $table) {
            $table->foreignId('sport_id')->constrained('sports')->onDelete('cascade');
            $table->string('babak')->nullable(); // Contoh: Quarter Final, Final [cite: 117]
            $table->string('format_tanding')->default('Knockout'); // BO3 atau BO5 [cite: 118]
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
