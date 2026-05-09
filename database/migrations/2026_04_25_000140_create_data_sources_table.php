<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statistic_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('source_type');
            $table->string('parser_key')->default('dsi_excel_v1');
            $table->string('storage_disk')->default('public');
            $table->string('file_path')->nullable();
            $table->text('spreadsheet_url')->nullable();
            $table->timestamp('last_imported_at')->nullable();
            $table->string('status')->default('draft');
            $table->text('last_error')->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_sources');
    }
};
