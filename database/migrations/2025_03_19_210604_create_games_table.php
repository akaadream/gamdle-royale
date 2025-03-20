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
        Schema::create('games', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('parent_name')->nullable();
            $table->string('game_modes');
            $table->string('platforms');
            $table->string('genres');
            $table->string('themes');
            $table->text('developers');
            $table->string('perspectives');
            $table->string('first_release_date');
            $table->string('screenshot');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
