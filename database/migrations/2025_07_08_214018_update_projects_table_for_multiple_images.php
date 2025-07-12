<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop the gradient column
            $table->dropColumn('gradient');
            
            // Rename image to images and change to JSON
            $table->json('images')->nullable()->after('type');
        });
        
        // Drop the old image column
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Recreate the gradient column
            $table->string('gradient')->nullable()->after('date');
            
            // Recreate the image column
            $table->string('image')->nullable()->after('type');
            
            // Drop the images column
            $table->dropColumn('images');
        });
    }
};
