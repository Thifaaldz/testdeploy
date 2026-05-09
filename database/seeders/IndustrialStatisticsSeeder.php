<?php

namespace Database\Seeders;

use App\Enums\StatisticCategoryCode;
use App\Models\DataSource;
use App\Models\GeoJsonLayer;
use App\Models\StatisticCategory;
use App\Models\StatisticPeriod;
use App\Models\StatisticPoint;
use App\Models\StatisticSeries;
use App\Models\SurveyProgress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class IndustrialStatisticsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = StatisticCategory::query()->pluck('id', 'code');

        $periods = [
            ['label' => 'Q1 2022', 'year' => 2022, 'quarter' => 1],
            ['label' => 'Q2 2022', 'year' => 2022, 'quarter' => 2],
            ['label' => 'Q3 2022', 'year' => 2022, 'quarter' => 3],
            ['label' => 'Q4 2022', 'year' => 2022, 'quarter' => 4],
            ['label' => 'Q1 2023', 'year' => 2023, 'quarter' => 1],
            ['label' => 'Q2 2023', 'year' => 2023, 'quarter' => 2],
            ['label' => 'Q3 2023', 'year' => 2023, 'quarter' => 3],
            ['label' => 'Q4 2023', 'year' => 2023, 'quarter' => 4],
            ['label' => 'Q1 2024', 'year' => 2024, 'quarter' => 1],
            ['label' => 'Q2 2024', 'year' => 2024, 'quarter' => 2],
            ['label' => 'Q3 2024', 'year' => 2024, 'quarter' => 3],
            ['label' => 'Q4 2024', 'year' => 2024, 'quarter' => 4],
            ['label' => 'Q1 2025', 'year' => 2025, 'quarter' => 1],
            ['label' => 'Q2 2025', 'year' => 2025, 'quarter' => 2],
            ['label' => 'Q3 2025', 'year' => 2025, 'quarter' => 3],
            ['label' => 'Q4 2025', 'year' => 2025, 'quarter' => 4],
            ['label' => 'Q1 2026', 'year' => 2026, 'quarter' => 1],
            ['label' => 'Q2 2026', 'year' => 2026, 'quarter' => 2],
            ['label' => 'Q3 2026', 'year' => 2026, 'quarter' => 3],
            ['label' => 'Q4 2026', 'year' => 2026, 'quarter' => 4],
        ];

        usort($periods, fn (array $left, array $right) => [$left['year'], $left['quarter']] <=> [$right['year'], $right['quarter']]);

        foreach ($periods as $index => $period) {
            StatisticPeriod::updateOrCreate(
                ['label' => $period['label']],
                $period + ['sort_order' => $index + 1],
            );
        }

        $periodIds = StatisticPeriod::query()->pluck('id', 'label');

        $seriesDefinitions = [
            [
                'slug' => 'ibs-index',
                'name' => 'Indeks IBS',
                'category' => StatisticCategoryCode::IBS->value,
                'group_key' => 'production-index',
                'chart_type' => 'line',
                'unit' => 'index',
                'precision' => 2,
                'color' => '#f97316',
                'is_featured' => true,
            ],
            [
                'slug' => 'ibs-growth',
                'name' => 'Pertumbuhan IBS',
                'category' => StatisticCategoryCode::IBS->value,
                'group_key' => 'production-trend',
                'chart_type' => 'line',
                'unit' => 'percent',
                'precision' => 2,
                'color' => '#fb923c',
                'is_featured' => true,
            ],
            [
                'slug' => 'imk-index',
                'name' => 'Indeks IMK',
                'category' => StatisticCategoryCode::IMK->value,
                'group_key' => 'production-index',
                'chart_type' => 'line',
                'unit' => 'index',
                'precision' => 2,
                'color' => '#7c3aed',
                'is_featured' => true,
            ],
            [
                'slug' => 'imk-growth',
                'name' => 'Pertumbuhan IMK',
                'category' => StatisticCategoryCode::IMK->value,
                'group_key' => 'production-trend',
                'chart_type' => 'line',
                'unit' => 'percent',
                'precision' => 2,
                'color' => '#a855f7',
                'is_featured' => true,
            ],
            [
                'slug' => 'ikbm-index',
                'name' => 'IKBM',
                'category' => StatisticCategoryCode::KEK_KI->value,
                'group_key' => 'production-index',
                'chart_type' => 'line',
                'unit' => 'index',
                'precision' => 0,
                'color' => '#f59e0b',
                'is_featured' => true,
            ],
            [
                'slug' => 'pdb-industrial-adhb-growth',
                'name' => 'Pert. PDB Industri ADHB',
                'category' => StatisticCategoryCode::DSI->value,
                'group_key' => 'production-trend',
                'chart_type' => 'line',
                'unit' => 'percent',
                'precision' => 2,
                'color' => '#2563eb',
                'is_featured' => false,
            ],
            [
                'slug' => 'pdb-industrial-adhk-growth',
                'name' => 'Pert. PDB Industri ADHK',
                'category' => StatisticCategoryCode::DSI->value,
                'group_key' => 'production-trend',
                'chart_type' => 'line',
                'unit' => 'percent',
                'precision' => 2,
                'color' => '#84cc16',
                'is_featured' => false,
            ],
            [
                'slug' => 'kekki-workforce-share',
                'name' => 'Share Tenaga Kerja KEK/KI',
                'category' => StatisticCategoryCode::KEK_KI->value,
                'group_key' => 'kekki-share',
                'chart_type' => 'line',
                'unit' => 'percent',
                'precision' => 2,
                'color' => '#dc2626',
                'is_featured' => true,
            ],
            [
                'slug' => 'kekki-investment-share',
                'name' => 'Share Investasi KEK/KI',
                'category' => StatisticCategoryCode::KEK_KI->value,
                'group_key' => 'kekki-share',
                'chart_type' => 'line',
                'unit' => 'percent',
                'precision' => 2,
                'color' => '#ea580c',
                'is_featured' => true,
            ],
            [
                'slug' => 'kekki-output-share',
                'name' => 'Share Output KEK/KI',
                'category' => StatisticCategoryCode::KEK_KI->value,
                'group_key' => 'kekki-share',
                'chart_type' => 'line',
                'unit' => 'percent',
                'precision' => 2,
                'color' => '#f59e0b',
                'is_featured' => true,
            ],
            [
                'slug' => 'industry-role-distribution',
                'name' => 'Peran Industri Pengolahan dalam Perekonomian',
                'category' => StatisticCategoryCode::DSI->value,
                'group_key' => 'industry-role',
                'chart_type' => 'doughnut',
                'unit' => 'percent',
                'precision' => 2,
                'color' => '#fb923c',
                'is_featured' => false,
            ],
        ];

        foreach ($seriesDefinitions as $index => $seriesDefinition) {
            StatisticSeries::updateOrCreate(
                ['slug' => $seriesDefinition['slug']],
                [
                    'statistic_category_id' => $categories[$seriesDefinition['category']] ?? null,
                    'name' => $seriesDefinition['name'],
                    'group_key' => $seriesDefinition['group_key'],
                    'chart_type' => $seriesDefinition['chart_type'],
                    'unit' => $seriesDefinition['unit'],
                    'precision' => $seriesDefinition['precision'],
                    'color' => $seriesDefinition['color'],
                    'description' => $seriesDefinition['name'],
                    'is_featured' => $seriesDefinition['is_featured'],
                    'sort_order' => $index + 1,
                ],
            );
        }

        $seriesIds = StatisticSeries::query()->pluck('id', 'slug');

        $lineQtoq = [
            ['period' => 'Q1 2022', 'pdb_adhb' => 4508662.5, 'pdb_adhk' => 2819331.8, 'ibs_index' => 143.52, 'ibs_growth' => null, 'imk_index' => 137.05, 'imk_growth' => null],
            ['period' => 'Q2 2022', 'pdb_adhb' => 4898031.8, 'pdb_adhk' => 2924444.0, 'ibs_index' => 144.27, 'ibs_growth' => 0.5225752508, 'imk_index' => 139.56, 'imk_growth' => 1.8314483770],
            ['period' => 'Q3 2022', 'pdb_adhb' => 5066968.2, 'pdb_adhk' => 2977920.0, 'ibs_index' => 155.61, 'ibs_growth' => 7.8602620090, 'imk_index' => 136.91, 'imk_growth' => -1.8988248780],
            ['period' => 'Q4 2022', 'pdb_adhb' => 5114797.4, 'pdb_adhk' => 2988527.4, 'ibs_index' => 154.28, 'ibs_growth' => -0.8547008547, 'imk_index' => 135.57, 'imk_growth' => -0.9787451611],
            ['period' => 'Q1 2023', 'pdb_adhb' => 5071497.2, 'pdb_adhk' => 2961564.4, 'ibs_index' => 148.60, 'ibs_growth' => -3.6816178380, 'imk_index' => 139.13, 'imk_growth' => 2.6259496940],
            ['period' => 'Q2 2023', 'pdb_adhb' => 5223290.3, 'pdb_adhk' => 3075781.9, 'ibs_index' => 146.35, 'ibs_growth' => -1.5141318980, 'imk_index' => 140.60, 'imk_growth' => 1.0565658020],
            ['period' => 'Q3 2023', 'pdb_adhb' => 5294903.3, 'pdb_adhk' => 3124968.2, 'ibs_index' => 159.73, 'ibs_growth' => 9.1424666890, 'imk_index' => 140.45, 'imk_growth' => -0.1066856330],
            ['period' => 'Q4 2023', 'pdb_adhb' => 5302657.7, 'pdb_adhk' => 3139160.6, 'ibs_index' => 157.40, 'ibs_growth' => -1.4587115760, 'imk_index' => 142.92, 'imk_growth' => 1.7586329650],
            ['period' => 'Q1 2024', 'pdb_adhb' => 5288576.6, 'pdb_adhk' => 3113039.9, 'ibs_index' => 152.79, 'ibs_growth' => -2.9288437100, 'imk_index' => 143.90, 'imk_growth' => 0.6856982928],
            ['period' => 'Q2 2024', 'pdb_adhb' => 5536588.5, 'pdb_adhk' => 3230990.3, 'ibs_index' => 150.54, 'ibs_growth' => -1.4726094640, 'imk_index' => 142.58, 'imk_growth' => -0.9173036831],
            ['period' => 'Q3 2024', 'pdb_adhb' => 5638869.1, 'pdb_adhk' => 3279509.8, 'ibs_index' => 161.18, 'ibs_growth' => 7.0678889330, 'imk_index' => 144.80, 'imk_growth' => 1.5570206200],
            ['period' => 'Q4 2024', 'pdb_adhb' => 5674929.8, 'pdb_adhk' => 3296741.7, 'ibs_index' => 163.53, 'ibs_growth' => 1.4579972700, 'imk_index' => 142.98, 'imk_growth' => -1.2569060770],
            ['period' => 'Q1 2025', 'pdb_adhb' => 5665930.2, 'pdb_adhk' => 3264533.7, 'ibs_index' => null, 'ibs_growth' => null, 'imk_index' => null, 'imk_growth' => null],
            ['period' => 'Q2 2025', 'pdb_adhb' => 5947005.4, 'pdb_adhk' => 3396302.6, 'ibs_index' => null, 'ibs_growth' => null, 'imk_index' => null, 'imk_growth' => null],
        ];

        $pdbAdhbGrowth = $this->calculateQuarterGrowth(Arr::pluck($lineQtoq, 'pdb_adhb'));
        $pdbAdhkGrowth = $this->calculateQuarterGrowth(Arr::pluck($lineQtoq, 'pdb_adhk'));

        $this->syncTimeSeries(
            $seriesIds['ibs-index'],
            collect($lineQtoq)->map(fn (array $row) => ['period' => $row['period'], 'value' => $row['ibs_index']])->all(),
            $periodIds,
        );

        $this->syncTimeSeries(
            $seriesIds['ibs-growth'],
            collect($lineQtoq)->map(fn (array $row) => ['period' => $row['period'], 'value' => $row['ibs_growth']])->all(),
            $periodIds,
        );

        $this->syncTimeSeries(
            $seriesIds['imk-index'],
            collect($lineQtoq)->map(fn (array $row) => ['period' => $row['period'], 'value' => $row['imk_index']])->all(),
            $periodIds,
        );

        $this->syncTimeSeries(
            $seriesIds['imk-growth'],
            collect($lineQtoq)->map(fn (array $row) => ['period' => $row['period'], 'value' => $row['imk_growth']])->all(),
            $periodIds,
        );

        $this->syncTimeSeries(
            $seriesIds['pdb-industrial-adhb-growth'],
            collect($lineQtoq)->map(function (array $row, int $index) use ($pdbAdhbGrowth) {
                return ['period' => $row['period'], 'value' => $pdbAdhbGrowth[$index] ?? null];
            })->all(),
            $periodIds,
        );

        $this->syncTimeSeries(
            $seriesIds['pdb-industrial-adhk-growth'],
            collect($lineQtoq)->map(function (array $row, int $index) use ($pdbAdhkGrowth) {
                return ['period' => $row['period'], 'value' => $pdbAdhkGrowth[$index] ?? null];
            })->all(),
            $periodIds,
        );

        $ikbmSeries = [
            ['period' => 'Q1 2022', 'value' => 1],
            ['period' => 'Q2 2022', 'value' => 2],
            ['period' => 'Q3 2022', 'value' => 3],
            ['period' => 'Q4 2022', 'value' => 4],
            ['period' => 'Q1 2023', 'value' => 5],
            ['period' => 'Q2 2023', 'value' => 6],
            ['period' => 'Q3 2023', 'value' => 7],
            ['period' => 'Q4 2023', 'value' => 8],
            ['period' => 'Q1 2024', 'value' => 9],
            ['period' => 'Q2 2024', 'value' => 10],
            ['period' => 'Q3 2024', 'value' => 11],
            ['period' => 'Q4 2024', 'value' => 12],
            ['period' => 'Q1 2025', 'value' => 13],
            ['period' => 'Q2 2025', 'value' => 14],
            ['period' => 'Q3 2025', 'value' => 15],
            ['period' => 'Q4 2025', 'value' => 16],
        ];

        $this->syncTimeSeries($seriesIds['ikbm-index'], $ikbmSeries, $periodIds);

        $kekkiShareSeries = [
            'kekki-workforce-share' => [
                ['period' => 'Q1 2025', 'value' => 1.00],
                ['period' => 'Q2 2025', 'value' => 1.98],
                ['period' => 'Q3 2025', 'value' => 2.94],
                ['period' => 'Q4 2025', 'value' => 3.88],
                ['period' => 'Q1 2026', 'value' => 4.81],
                ['period' => 'Q2 2026', 'value' => 5.71],
                ['period' => 'Q3 2026', 'value' => 6.60],
                ['period' => 'Q4 2026', 'value' => 7.48],
            ],
            'kekki-investment-share' => [
                ['period' => 'Q1 2025', 'value' => 1.96],
                ['period' => 'Q2 2025', 'value' => 2.91],
                ['period' => 'Q3 2025', 'value' => 3.85],
                ['period' => 'Q4 2025', 'value' => 4.76],
                ['period' => 'Q1 2026', 'value' => 5.66],
                ['period' => 'Q2 2026', 'value' => 6.54],
                ['period' => 'Q3 2026', 'value' => 7.41],
                ['period' => 'Q4 2026', 'value' => 8.26],
            ],
            'kekki-output-share' => [
                ['period' => 'Q1 2025', 'value' => 2.88],
                ['period' => 'Q2 2025', 'value' => 3.81],
                ['period' => 'Q3 2025', 'value' => 4.72],
                ['period' => 'Q4 2025', 'value' => 5.61],
                ['period' => 'Q1 2026', 'value' => 6.48],
                ['period' => 'Q2 2026', 'value' => 7.34],
                ['period' => 'Q3 2026', 'value' => 8.18],
                ['period' => 'Q4 2026', 'value' => 9.01],
            ],
        ];

        foreach ($kekkiShareSeries as $slug => $payload) {
            $this->syncTimeSeries($seriesIds[$slug], $payload, $periodIds);
        }

        $industryRolePoints = [
            ['label' => 'C', 'value' => 19.07, 'sort_order' => 1],
            ['label' => 'G', 'value' => 13.17, 'sort_order' => 2],
            ['label' => 'A', 'value' => 13.10, 'sort_order' => 3],
            ['label' => 'F', 'value' => 9.83, 'sort_order' => 4],
            ['label' => 'Others', 'value' => 40.58, 'sort_order' => 5],
        ];

        $this->syncLabelSeries($seriesIds['industry-role-distribution'], $industryRolePoints);

        $progressRecords = [
            [
                'category' => StatisticCategoryCode::DSI->value,
                'activity_name' => 'SIBSTR',
                'target_awal' => 5,
                'selesai_dicacah' => 1,
                'sisa_target' => 1,
                'eligible' => 1,
                'sedang_dicacah' => 1,
                'condition_label' => '7 April 2026 10.00 WIB',
                'sort_order' => 1,
            ],
            [
                'category' => StatisticCategoryCode::KEK_KI->value,
                'activity_name' => 'STPU KEK-KI',
                'target_awal' => 5,
                'selesai_dicacah' => 2,
                'sisa_target' => 2,
                'eligible' => 2,
                'sedang_dicacah' => 2,
                'condition_label' => '7 April 2026 10.00 WIB',
                'sort_order' => 2,
            ],
            [
                'category' => StatisticCategoryCode::DSI->value,
                'activity_name' => 'STPIM',
                'target_awal' => 5,
                'selesai_dicacah' => 3,
                'sisa_target' => 3,
                'eligible' => 3,
                'sedang_dicacah' => 3,
                'condition_label' => '7 April 2026 10.00 WIB',
                'sort_order' => 3,
            ],
            [
                'category' => StatisticCategoryCode::IMK->value,
                'activity_name' => 'VIMK tahunan',
                'target_awal' => 5,
                'selesai_dicacah' => 4,
                'sisa_target' => 4,
                'eligible' => 4,
                'sedang_dicacah' => 4,
                'condition_label' => '7 April 2026 10.00 WIB',
                'sort_order' => 4,
            ],
            [
                'category' => StatisticCategoryCode::IMK->value,
                'activity_name' => 'VIMK triwulanan',
                'target_awal' => 5,
                'selesai_dicacah' => 5,
                'sisa_target' => 5,
                'eligible' => 5,
                'sedang_dicacah' => 5,
                'condition_label' => '7 April 2026 10.00 WIB',
                'sort_order' => 5,
            ],
        ];

        foreach ($progressRecords as $record) {
            SurveyProgress::updateOrCreate(
                ['activity_name' => $record['activity_name']],
                [
                    'statistic_category_id' => $categories[$record['category']] ?? null,
                    'target_awal' => $record['target_awal'],
                    'selesai_dicacah' => $record['selesai_dicacah'],
                    'sisa_target' => $record['sisa_target'],
                    'eligible' => $record['eligible'],
                    'sedang_dicacah' => $record['sedang_dicacah'],
                    'condition_label' => $record['condition_label'],
                    'sort_order' => $record['sort_order'],
                ],
            );
        }

        DataSource::updateOrCreate(
            ['slug' => 'dsi-seed-excel'],
            [
                'statistic_category_id' => $categories[StatisticCategoryCode::DSI->value] ?? null,
                'name' => 'Seed DSI dari Industri_cleaned.xlsx',
                'source_type' => 'excel_upload',
                'parser_key' => 'dsi_excel_v1',
                'storage_disk' => 'public',
                'status' => 'seeded',
                'notes' => 'Data awal dashboard diambil dari workbook Industri_cleaned.xlsx dan disalin sebagai seed aplikasi.',
                'meta' => [
                    'seeded_from' => 'datasource/Industri_cleaned.xlsx',
                    'supports_upload' => true,
                    'supports_spreadsheet_link' => true,
                ],
            ],
        );

        GeoJsonLayer::updateOrCreate(
            ['slug' => 'zona-industri-dummy'],
            [
                'statistic_category_id' => null,
                'name' => 'Zona Industri Dummy',
                'source_file' => 'dummy-seeded.geojson',
                'geojson' => json_encode([
                    'type' => 'FeatureCollection',
                    'features' => [
                        [
                            'type' => 'Feature',
                            'properties' => ['name' => 'Zona Industri Barat'],
                            'geometry' => [
                                'type' => 'Polygon',
                                'coordinates' => [[
                                    [106.95, -6.44],
                                    [107.25, -6.44],
                                    [107.25, -6.12],
                                    [106.95, -6.12],
                                    [106.95, -6.44],
                                ]],
                            ],
                        ],
                        [
                            'type' => 'Feature',
                            'properties' => ['name' => 'Zona Industri Tengah'],
                            'geometry' => [
                                'type' => 'Polygon',
                                'coordinates' => [[
                                    [110.15, -7.15],
                                    [110.55, -7.15],
                                    [110.55, -6.85],
                                    [110.15, -6.85],
                                    [110.15, -7.15],
                                ]],
                            ],
                        ],
                    ],
                ], JSON_PRETTY_PRINT),
                'style' => [
                    'color' => '#f59e0b',
                    'fillColor' => '#fbbf24',
                    'fillOpacity' => 0.18,
                    'weight' => 2,
                ],
                'is_active' => true,
                'notes' => 'Layer dummy untuk contoh tampilan peta. Bisa diganti nanti dengan upload GeoJSON resmi.',
            ],
        );
    }

    private function calculateQuarterGrowth(array $values): array
    {
        $growth = [];

        foreach ($values as $index => $value) {
            $previous = $values[$index - 1] ?? null;

            if ($index === 0 || ! $previous || ! $value) {
                $growth[] = null;
                continue;
            }

            $growth[] = (($value / $previous) - 1) * 100;
        }

        return $growth;
    }

    private function syncLabelSeries(int $seriesId, array $points): void
    {
        StatisticPoint::query()->where('statistic_series_id', $seriesId)->delete();

        foreach ($points as $point) {
            StatisticPoint::create([
                'statistic_series_id' => $seriesId,
                'label' => $point['label'],
                'value' => $point['value'],
                'sort_order' => $point['sort_order'] ?? 0,
            ]);
        }
    }

    private function syncTimeSeries(int $seriesId, array $points, $periodIds): void
    {
        StatisticPoint::query()->where('statistic_series_id', $seriesId)->delete();

        foreach ($points as $index => $point) {
            if ($point['value'] === null || ! isset($periodIds[$point['period']])) {
                continue;
            }

            StatisticPoint::create([
                'statistic_series_id' => $seriesId,
                'statistic_period_id' => $periodIds[$point['period']],
                'value' => $point['value'],
                'sort_order' => $index + 1,
                'meta' => ['period_label' => $point['period']],
            ]);
        }
    }
}
