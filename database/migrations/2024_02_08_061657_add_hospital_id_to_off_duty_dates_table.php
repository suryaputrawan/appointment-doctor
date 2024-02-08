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
        Schema::table('off_duty_dates', function (Blueprint $table) {
            $table->foreignId('hospital_id')->nullable()->after('doctor_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('off_duty_dates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hospital_id');
        });
    }
};
