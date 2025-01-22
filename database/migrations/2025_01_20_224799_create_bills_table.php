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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('bill_type_id')->constrained('bill_types','id')->cascadeOnDelete();
            $table->foreignId('added_by')->constrained('users','id')->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained('payments','id')->cascadeOnDelete();
            $table->integer('amount');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
