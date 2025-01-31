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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('thumbnail')->nullable();
            $table->string('title');
            $table->text('description');
            $table->foreignId('branch_id')->nullable()->constrained('branches','id')->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained('users','id')->cascadeOnDelete();
            $table->integer('price');
            $table->enum('discount_type', ['percentage', 'amount']);
            $table->integer('discount_amount');
            $table->date('started_at')->nullable();
            $table->enum('type',['online','recorded','offline'])->default('online');
            $table->integer('max_students')->default(0);
            $table->boolean('active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
