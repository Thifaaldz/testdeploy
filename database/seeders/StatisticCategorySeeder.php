<?php

namespace Database\Seeders;

use App\Enums\StatisticCategoryCode;
use App\Models\StatisticCategory;
use Illuminate\Database\Seeder;

class StatisticCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'code' => StatisticCategoryCode::DSI->value,
                'name' => 'Dashboard Statistik Industri',
                'description' => 'Kategori ringkasan utama yang menggabungkan indikator industri, progres survei, dan kontribusi ekonomi.',
                'accent_color' => '#f59e0b',
                'icon' => 'heroicon-o-squares-2x2',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'code' => StatisticCategoryCode::IBS->value,
                'name' => 'Industri Besar dan Sedang',
                'description' => 'Kategori untuk memantau indeks dan pertumbuhan industri besar dan sedang.',
                'accent_color' => '#2563eb',
                'icon' => 'heroicon-o-building-office-2',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'code' => StatisticCategoryCode::IMK->value,
                'name' => 'Industri Mikro dan Kecil',
                'description' => 'Kategori untuk melihat perkembangan industri mikro dan kecil secara triwulanan.',
                'accent_color' => '#7c3aed',
                'icon' => 'heroicon-o-chart-bar',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'code' => StatisticCategoryCode::KEK_KI->value,
                'name' => 'KEK dan Kawasan Industri',
                'description' => 'Kategori kontribusi kawasan industri terhadap tenaga kerja, investasi, output, dan peta lokasi.',
                'accent_color' => '#dc2626',
                'icon' => 'heroicon-o-map',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            StatisticCategory::updateOrCreate(
                ['code' => $category['code']],
                $category,
            );
        }
    }
}
