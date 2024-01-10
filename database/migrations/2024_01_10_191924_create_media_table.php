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
        // 'name',
        // 'alter text',
        // 'image'
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->tinyText('name')->nullable();
            $table->mediumText('alter_text')->nullable();
            $table->tinyText('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
