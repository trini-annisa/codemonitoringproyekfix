<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('boq_items', function (Blueprint $table) {
            $table->decimal('weight_percent', 10, 4)->default(0)->change();
        });

        Schema::table('boq_sub_items', function (Blueprint $table) {
            $table->decimal('weight_percent', 10, 4)->default(0)->change();
        });
    }

    public function down(): void {
        Schema::table('boq_items', function (Blueprint $table) {
            $table->decimal('weight_percent', 8, 4)->default(0)->change();
        });

        Schema::table('boq_sub_items', function (Blueprint $table) {
            $table->decimal('weight_percent', 8, 4)->default(0)->change();
        });
    }
};
