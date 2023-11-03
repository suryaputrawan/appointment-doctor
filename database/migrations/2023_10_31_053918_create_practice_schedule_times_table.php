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
        Schema::create('practice_schedule_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_schedule_id')->constrained('practice_schedules')
                ->cascadeOnDelete();
            $table->time('start_time');
            $table->time('end_time');
            $table->tinyInteger('booking_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_schedule_times');
    }
};
