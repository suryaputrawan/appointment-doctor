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
        Schema::create('m_params', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('auto_no_surat');
            $table->string('format_surat');
            $table->foreignId('hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_params');
    }
};
