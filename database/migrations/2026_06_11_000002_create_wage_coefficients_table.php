<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wage_coefficients', function (Blueprint $table) {
            $table->integer('reference_year');
            $table->integer('income_year');
            $table->decimal('coefficient', 10, 4);
            $table->primary(['reference_year', 'income_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wage_coefficients');
    }
};
