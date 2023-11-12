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
        // 'twitter',
        // 'instagram',
        // 'linkedin',
        // 'user_id',
        Schema::create('sociallinks', function (Blueprint $table) {
            $table->id();
            $table->tinyText('twitter')->nullable();
            $table->tinyText('instagram')->nullable();
            $table->tinyText('linkedin')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sociallinks');
    }
};
