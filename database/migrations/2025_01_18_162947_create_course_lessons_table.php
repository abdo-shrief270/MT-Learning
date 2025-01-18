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
        Schema::create('course_lessons', function (Blueprint $table) {
            //'title','description','course_id','video_link','active'
            $table->id();
            $table->string('title');
            $table->text('image');
            $table->text('description');
            $table->foreignId('course_id')->constrained('courses','id')->cascadeOnDelete();
            $table->string('video_link');
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
