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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->tinyText('heading')->nullable();
            $table->mediumText('presummary')->nullable();
            // $table->foreignId('category_id')->constrained('categories');
             $table->foreignId('category_id')->references('id')->on('categories');
            $table->foreignId('writer_id')->constrained('writers');
            $table->foreignId('sub_categories_id')->constrained('sub_categories');
            $table->string('read_time')->nullable();
            $table->tinyText('main_image')->nullable();
            $table->tinyText('keypoint')->nullable();
            $table->tinyText('thumbnail')->nullable();
            $table->mediumText('summary')->nullable();
            $table->longText("body")->nullable();
            $table->tinyText('no_time_viewed')->nullable();
            $table->timestamp('schedule_story_time')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
