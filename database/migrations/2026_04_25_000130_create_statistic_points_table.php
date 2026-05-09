<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statistic_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statistic_series_id')->constrained()->cascadeOnDelete();
            $table->foreignId('statistic_period_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label')->nullable();
            $table->decimal('value', 18, 6);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['statistic_series_id', 'statistic_period_id'], 'series_period_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistic_points');
    }
};
