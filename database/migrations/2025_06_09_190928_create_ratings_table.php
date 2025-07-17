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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Utilisateur qui donne la note');
            $table->foreignId('rated_user_id')->constrained('users')->onDelete('cascade')->comment('Utilisateur qui reçoit la note');
            $table->foreignId('user_match_id')->constrained()->onDelete('cascade');
            $table->integer('score')->comment('Note de 1 à 5');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
