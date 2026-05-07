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
        Schema::create('student_category_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_skill_behaviour_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score'); // e.g. 1–5
            $table->timestamps();

            $table->unique(['student_skill_behaviour_id', 'category_id'], 'scs_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_category_scores');
    }
};
