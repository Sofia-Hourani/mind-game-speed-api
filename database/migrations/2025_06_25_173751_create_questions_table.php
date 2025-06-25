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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id');
            $table->string('question');
            $table->float('answer');
            $table->time('asked_at')->nullable();
            $table->time('answered_at')->nullable();
            $table->time('time_taken')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->string('score')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
