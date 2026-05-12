<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('boq_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('name');
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('weight_percent', 8, 4)->default(0);
            $table->unsignedSmallInteger('order_no')->default(0);
            $table->timestamps();
            $table->unique(['project_id', 'code']);
        });

        Schema::create('boq_sub_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boq_item_id')->constrained('boq_items')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('code', 30);
            $table->string('name');
            $table->text('specification')->nullable();
            $table->decimal('volume', 15, 3);
            $table->string('unit', 20);
            $table->decimal('unit_price', 18, 2);
            $table->decimal('total_price', 18, 2);
            $table->decimal('weight_percent', 8, 4)->default(0);
            $table->date('planned_start');
            $table->date('planned_end');
            $table->unsignedSmallInteger('order_no')->default(0);
            $table->timestamps();
            $table->index('project_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('boq_sub_items');
        Schema::dropIfExists('boq_items');
    }
};
