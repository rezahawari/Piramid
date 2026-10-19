<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legalities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('document_number')->nullable();
            $table->string('issuer')->nullable();
            $table->date('issued_date')->nullable();
            $table->string('file_path'); // URL atau local storage path
            $table->string('file_type', 20)->default('pdf'); // pdf, png, jpg, jpeg
            $table->string('file_size')->nullable(); // misal "1.2 MB"
            $table->text('description')->nullable();
            $table->integer('order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legalities');
    }
};
