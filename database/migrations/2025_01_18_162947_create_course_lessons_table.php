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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('thumbnail')->nullable();
            $table->integer('sort');
            $table->string('title');
            $table->text('link')->nullable();
            $table->integer('duration')->default(0);
            $table->text('description');
            $table->foreignId('course_id')->constrained('courses','id')->cascadeOnDelete();
            $table->boolean('active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
    }
};
