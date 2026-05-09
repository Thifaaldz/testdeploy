<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('industry_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statistic_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('industry_sector')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('workforce')->nullable();
            $table->decimal('investment_value', 18, 2)->nullable();
            $table->decimal('output_value', 18, 2)->nullable();
            $table->string('status')->default('active');
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_dummy')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('industry_locations');
    }
};
