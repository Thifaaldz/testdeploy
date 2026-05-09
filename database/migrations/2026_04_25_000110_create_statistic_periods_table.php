<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statistic_periods', function (Blueprint $table) {
            $table->id();
            $table->string('label')->unique();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('quarter')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['year', 'quarter']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistic_periods');
    }
};
