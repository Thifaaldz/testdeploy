<?php

namespace App\Support;

use App\Enums\StatisticCategoryCode;

class StatisticsDashboardConfig
{
    public static function categories(): array
    {
        return [
            StatisticCategoryCode::DSI->value => [
                'headline' => 'Dashboard Statistik Industri',
                'description' => 'Ringkasan statistik industri pengolahan dari data DSI, lengkap dengan progres survei, tren triwulanan, dan peta lokasi industri.',
                'accent' => '#f59e0b',
                'kpi_slugs' => ['ibs-index', 'imk-growth', 'ikbm-index'],
                'share_slugs' => ['kekki-workforce-share', 'kekki-investment-share', 'kekki-output-share'],
                'trend_slugs' => [
                    'pdb-industrial-adhb-growth',
                    'pdb-industrial-adhk-growth',
                    'ibs-growth',
                    'imk-growth',
                ],
                'distribution_mode' => 'industry-role',
            ],
            StatisticCategoryCode::IBS->value => [
                'headline' => 'Statistik IBS',
                'description' => 'Pemantauan indeks dan pertumbuhan industri besar dan sedang dengan fokus pada dinamika produksi triwulanan.',
                'accent' => '#2563eb',
                'kpi_slugs' => ['ibs-index', 'ibs-growth', 'pdb-industrial-adhb-growth'],
                'share_slugs' => [],
                'trend_slugs' => ['ibs-index', 'ibs-growth'],
                'distribution_mode' => 'industry-role',
            ],
            StatisticCategoryCode::IMK->value => [
                'headline' => 'Statistik IMK',
                'description' => 'Sorotan perkembangan industri mikro dan kecil dengan indikator indeks, pertumbuhan, dan pemerataan lokasi produksi.',
                'accent' => '#7c3aed',
                'kpi_slugs' => ['imk-index', 'imk-growth', 'pdb-industrial-adhk-growth'],
                'share_slugs' => [],
                'trend_slugs' => ['imk-index', 'imk-growth'],
                'distribution_mode' => 'industry-role',
            ],
            StatisticCategoryCode::KEK_KI->value => [
                'headline' => 'Statistik KEK/KI',
                'description' => 'Kinerja kawasan ekonomi khusus dan kawasan industri, termasuk share tenaga kerja, investasi, output, dan layer peta industri.',
                'accent' => '#dc2626',
                'kpi_slugs' => ['kekki-workforce-share', 'kekki-investment-share', 'kekki-output-share'],
                'share_slugs' => ['kekki-workforce-share', 'kekki-investment-share', 'kekki-output-share'],
                'trend_slugs' => ['kekki-workforce-share', 'kekki-investment-share', 'kekki-output-share'],
                'distribution_mode' => 'current-share',
            ],
        ];
    }

    public static function category(string $code): array
    {
        return static::categories()[$code] ?? static::categories()[StatisticCategoryCode::DSI->value];
    }
}
