<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pension_parameters', function (Blueprint $table) {
            $table->integer('year')->primary();
            $table->integer('basic_amount');
            $table->integer('average_wage');
            $table->integer('reduction_boundary_1');
            $table->integer('reduction_boundary_2');
            $table->integer('reduction_boundary_3');
            $table->decimal('percentage_rate_per_year', 6, 4);
            $table->decimal('reduction_rate_1', 5, 4);
            $table->decimal('reduction_rate_2', 5, 4);
            $table->decimal('reduction_rate_3', 5, 4);
            $table->integer('child_bonus');
            $table->integer('min_percentage_amount');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pension_parameters');
    }
};
