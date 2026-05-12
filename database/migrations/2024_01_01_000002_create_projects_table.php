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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            // Identitas proyek
            $table->string('project_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('owner')->nullable();

            // Nilai proyek
            $table->bigInteger('contract_value')->nullable();
            $table->bigInteger('bac')->nullable();

            // Waktu proyek
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Status proyek
            $table->enum('status', ['active', 'completed', 'delayed'])
                  ->default('active');

            // Dokumen kontrak
            $table->string('contract_number')->nullable();

            // Relasi ke user (Project Manager & Creator)
            $table->foreignId('pm_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Timestamp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
