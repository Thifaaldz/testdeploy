<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statistic_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statistic_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('group_key')->index();
            $table->string('chart_type')->default('line');
            $table->string('unit')->nullable();
            $table->unsignedTinyInteger('precision')->default(2);
            $table->string('color', 32)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistic_series');
    }
};
