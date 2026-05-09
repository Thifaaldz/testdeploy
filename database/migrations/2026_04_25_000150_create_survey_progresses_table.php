<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statistic_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('activity_name')->unique();
            $table->unsignedInteger('target_awal')->default(0);
            $table->unsignedInteger('selesai_dicacah')->default(0);
            $table->unsignedInteger('sisa_target')->default(0);
            $table->unsignedInteger('eligible')->default(0);
            $table->unsignedInteger('sedang_dicacah')->default(0);
            $table->string('condition_label')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_progresses');
    }
};
