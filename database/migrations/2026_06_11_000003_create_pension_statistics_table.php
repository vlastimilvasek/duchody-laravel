<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pension_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('period');
            $table->string('region_code', 10);
            $table->string('pension_type', 20);
            $table->integer('count');
            $table->integer('average_amount');
            $table->string('source', 50)->nullable();
            $table->timestamps();

            $table->index(['region_code', 'pension_type', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pension_statistics');
    }
};
