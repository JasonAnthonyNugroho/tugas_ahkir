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
        // Update Tournaments table
        Schema::table('tournaments', function (Blueprint $table) {
            $table->foreignId('sport_id')->nullable()->constrained('sports')->onDelete('cascade');
            $table->integer('year')->default(date('Y'));
        });

        // Create pivot table for Tournament Teams
        Schema::create('tournament_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->timestamps();
        });

        // Update Pertandingans table for bracket support
        Schema::table('pertandingans', function (Blueprint $table) {
            $table->foreignId('tournament_id')->nullable()->constrained('tournaments')->onDelete('set null');
            $table->integer('round')->nullable(); // 1, 2, 3 (Final)
            $table->integer('match_number')->nullable(); // Urutan dalam babak tersebut
            $table->foreignId('next_match_id')->nullable()->constrained('pertandingans')->onDelete('set null');
            $table->foreignId('winner_id')->nullable()->constrained('teams')->onDelete('set null');
            
            // Allow team_a_id and team_b_id to be nullable for bracket placeholders
            $table->unsignedBigInteger('team_a_id')->nullable()->change();
            $table->unsignedBigInteger('team_b_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pertandingans', function (Blueprint $table) {
            $table->dropForeign(['tournament_id']);
            $table->dropForeign(['next_match_id']);
            $table->dropForeign(['winner_id']);
            $table->dropColumn(['tournament_id', 'round', 'match_number', 'next_match_id', 'winner_id']);
            
            $table->unsignedBigInteger('team_a_id')->nullable(false)->change();
            $table->unsignedBigInteger('team_b_id')->nullable(false)->change();
        });

        Schema::dropIfExists('tournament_teams');

        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropForeign(['sport_id']);
            $table->dropColumn(['sport_id', 'year']);
        });
    }
};
