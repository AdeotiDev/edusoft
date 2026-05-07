<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_behaviour_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('score'); // 1–5 scale
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_scores');
    }
};
