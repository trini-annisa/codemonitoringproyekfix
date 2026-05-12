<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('progress_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('boq_sub_item_id')->constrained('boq_sub_items')->cascadeOnDelete();
            $table->foreignId('reported_by')->constrained('users');
            $table->string('period_label', 10);
            $table->date('report_date');
            $table->decimal('physical_progress', 5, 2)->default(0);
            $table->decimal('actual_cost', 18, 2)->default(0);
            $table->decimal('earned_value', 18, 2)->default(0);
            $table->decimal('planned_value', 18, 2)->default(0);
            $table->string('attachment_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['boq_sub_item_id', 'period_label'], 'unique_progress_per_period');
            $table->index(['project_id', 'period_label']);
        });
    }
    public function down(): void { Schema::dropIfExists('progress_entries'); }
};
