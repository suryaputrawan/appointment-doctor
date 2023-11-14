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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('name');
            $table->string('email')->unique();
            $table->char('gender');
            $table->string('specialization');
            $table->foreignId('speciality_id')->constrained('specialities')->cascadeOnDelete();
            $table->text('about_me');
            $table->string('picture')->nullable();
            $table->tinyInteger('isAktif')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
