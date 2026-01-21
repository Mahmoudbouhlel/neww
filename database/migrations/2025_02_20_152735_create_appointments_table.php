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
            $table->foreignId('personnel_id');
            $table->foreignId('service_id');
            $table->foreignId('task_id');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'canceled'])->default('pending');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable(); // New column to track completion time

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
