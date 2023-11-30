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
        Schema::create('history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('JobId')->constrained('joblist');
            $table->foreignId('ProgressId')->constrained('master_progress');
            $table->boolean('isPaused')->nullable();
            $table->timestamp('PausedAt')->nullable();
            $table->timestamp('PausedOff')->nullable();
            $table->string('Notes')->nullable();
            $table->timestamp('ClockOnAt')->nullable();
            $table->timestamp('ClockOffAt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history');
    }
};
