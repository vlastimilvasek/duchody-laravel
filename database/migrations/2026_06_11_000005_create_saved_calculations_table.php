<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_calculations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 64)->nullable();
            $table->string('calc_type', 50);
            $table->json('input_params');
            $table->json('result');
            $table->timestamps();

            $table->index('session_id');
            $table->index(['user_id', 'calc_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_calculations');
    }
};
