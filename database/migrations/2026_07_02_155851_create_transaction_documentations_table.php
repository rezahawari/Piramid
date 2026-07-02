<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_documentations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->string('stage'); // same values as transactions.status
            $table->string('type'); // photo | video
            $table->string('file_url');
            $table->string('cloudinary_public_id')->nullable();
            $table->string('caption')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();

            $table->index(['transaction_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_documentations');
    }
};
