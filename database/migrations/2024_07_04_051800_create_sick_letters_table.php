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
        Schema::create('sick_letters', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('nomor');
            $table->date('date');
            $table->string('patient_name');
            $table->string('patient_email');
            $table->double('age');
            $table->char('gender');
            $table->string('profession')->nullable();
            $table->string('address');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('diagnosis')->nullable();
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sick_letters');
    }
};
