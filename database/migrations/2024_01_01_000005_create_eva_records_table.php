<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('eva_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('period_label', 10);
            $table->date('report_date');
            $table->decimal('bac', 18, 2);
            $table->decimal('planned_value', 18, 2);
            $table->decimal('earned_value', 18, 2);
            $table->decimal('actual_cost', 18, 2);
            $table->decimal('cost_variance', 18, 2);
            $table->decimal('schedule_variance', 18, 2);
            $table->decimal('cpi', 8, 4);
            $table->decimal('spi', 8, 4);
            $table->decimal('eac', 18, 2);
            $table->decimal('etc', 18, 2);
            $table->decimal('vac', 18, 2);
            $table->decimal('tcpi', 8, 4)->nullable();
            $table->decimal('physical_progress_percent', 5, 2)->default(0);
            $table->decimal('planned_progress_percent', 5, 2)->default(0);
            $table->enum('status_cost', ['under_budget', 'on_budget', 'over_budget'])->default('on_budget');
            $table->enum('status_schedule', ['ahead_of_schedule', 'on_schedule', 'behind_schedule'])->default('on_schedule');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'period_label'], 'unique_eva_per_period');
            $table->index(['project_id', 'report_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('eva_records'); }
};
