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
        // "username",
        // 'phone',
        // 'user_id'
        Schema::create('userprofiles', function (Blueprint $table) {
            $table->id();
            $table->tinyText('username')->nullable();
            $table->tinytext('phone')->nullable();
            $table->foreignId('user_id')->references('id')->on('users');

             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userprofiles');
    }
};
