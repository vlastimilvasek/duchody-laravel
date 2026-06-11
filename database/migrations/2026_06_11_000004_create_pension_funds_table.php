<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pension_funds', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('slug')->unique();
            $table->string('name', 200);
            $table->string('company', 200);
            $table->string('fund_type', 50);
            $table->decimal('fee_management', 4, 2);
            $table->decimal('fee_performance', 4, 2);
            $table->decimal('return_1y', 5, 2)->nullable();
            $table->decimal('return_3y', 5, 2)->nullable();
            $table->decimal('return_5y', 5, 2)->nullable();
            $table->integer('total_assets_mil')->nullable();
            $table->integer('participants_count')->nullable();
            $table->string('affiliate_url')->nullable();
            $table->string('partner_id')->nullable();
            $table->timestamps();

            $table->index('fund_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pension_funds');
    }
};
