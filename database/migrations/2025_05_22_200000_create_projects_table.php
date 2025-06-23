<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type');
            $table->string('image')->nullable();
            $table->string('author');
            $table->string('date');
            $table->string('gradient')->nullable();
            $table->text('description')->nullable();
            $table->json('technologies')->nullable();
            $table->json('features')->nullable();
            $table->string('status');
            $table->string('liveUrl')->nullable();
            $table->string('githubUrl')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
}; 