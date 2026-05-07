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
        Schema::create('student_skill_behaviours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_behaviour_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete(); // your students table
            $table->timestamps();

            $table->unique(['skill_behaviour_id', 'student_id'], 'usb_unique');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_skill_behaviours');
    }
};
