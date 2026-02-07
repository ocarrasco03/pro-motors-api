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
        Schema::create('scheduled_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('job_class');
            $table->string('command')->nullable();
            $table->string('cron_expression', 100);
            $table->json('payload')->nullable();
            $table->string('timezone')->default('UTC');
            $table->boolean('is_active')->default(true);
            $table->boolean('without_overlapping')->default(true);
            $table->boolean('run_in_background')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamps();
        });

        Schema::create('scheduled_task_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_task_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->enum('status', ['running', 'completed', 'failed']);
            $table->text('error')->nullable();
            $table->string('started_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_task_runs');
        Schema::dropIfExists('scheduled_tasks');
    }
};
