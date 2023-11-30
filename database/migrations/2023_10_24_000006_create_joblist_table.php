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
        Schema::create('joblist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('VehicleId')->constrained('vehicles');
            $table->string('WO');
            $table->timestamp('ServiceDate');
            $table->timestamp('ServiceEndDate')->nullable();
            $table->timestamp('ReleaseDate')->nullable();
            $table->unsignedBigInteger('isPaid')->nullable();
            $table->unsignedBigInteger('isCanceled')->nullable();
            $table->foreignId('UserId')->constrained('users');
            $table->foreignId('ProgressId')->constrained('master_progress');
            $table->foreignId('JobTypeId')->constrained('master_job');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('joblist');
    }
};
