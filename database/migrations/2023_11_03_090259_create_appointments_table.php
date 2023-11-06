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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->string('patient_name');
            $table->date('patient_dob');
            $table->char('patient_sex');
            $table->string('patient_address');
            $table->string('patient_email');
            $table->string('patient_telp');
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
