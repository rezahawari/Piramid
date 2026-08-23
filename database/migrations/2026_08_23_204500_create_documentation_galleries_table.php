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
        Schema::create('documentation_galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->default('image'); // image, video
            $table->string('file_url');
            $table->string('youtube_url')->nullable();
            $table->text('description')->nullable();
            $table->string('category')->default('qurban'); // qurban, aqiqah, edukasi, distribusi
            $table->integer('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentation_galleries');
    }
};
