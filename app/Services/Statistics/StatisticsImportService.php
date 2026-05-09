<?php

namespace App\Services\Statistics;

use App\Enums\StatisticCategoryCode;
use App\Models\DataSource;
use App\Models\StatisticCategory;
use App\Models\StatisticPeriod;
use App\Models\StatisticPoint;
use App\Models\StatisticSeries;
use App\Models\SurveyProgress;
use App\Services\Spreadsheets\RemoteSpreadsheetDownloader;
use App\Services\Spreadsheets\XlsxValueReader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class StatisticsImportService
{
    public function __construct(
        private readonly RemoteSpreadsheetDownloader $downloader,
        private readonly XlsxValueReader $reader,
    ) {
    }

    public function import(DataSource $dataSource): array
    {
        $temporaryPath = null;

        try {
            $path = match ($dataSource->source_type) {
                'excel_upload' => $this->resolveUploadPath($dataSource),
                'spreadsheet_link' => $temporaryPath = $this->downloader->download((string) $dataSource->spreadsheet_url),
                default => throw new RuntimeException('Tipe sumber data belum didukung.'),
            };

            if (str_ends_with(strtolower($path), '.csv')) {
                throw new RuntimeException('Link spreadsheet perlu mengarah ke file XLSX atau Google Sheets export.');
            }

            $workbook = $this->reader->read($path);
            $summary = DB::transaction(fn (): array => $this->persistWorkbook($workbook));

            $dataSource->update([
                'last_imported_at' => now(),
                'last_error' => null,
                'status' => 'imported',
                'meta' => array_merge($dataSource->meta ?? [], ['last_summary' => $summary]),
            ]);

            return $summary;
        } catch (Throwable $exception) {
            $dataSource->update([
                'status' => 'failed',
                'last_error' => $exception->getMessage(),
            ]);

            throw $exception;
        } finally {
            if ($temporaryPath && is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }
        }
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

    private function determineCategoryForProgress(string $activityName): string
    {
        return match (true) {
            str_contains($activityName, 'KEK') => StatisticCategoryCode::KEK_KI->value,
            str_contains($activityName, 'VIMK') => StatisticCategoryCode::IMK->value,
            default => StatisticCategoryCode::DSI->value,
        };
    }

    private function normalizeQuarter(int|string|null $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = strtoupper(trim((string) $value));

        return match ($normalized) {
            'Q1', 'I', '1' => 1,
            'Q2', 'II', '2' => 2,
            'Q3', 'III', '3' => 3,
            'Q4', 'IV', '4' => 4,
            default => null,
        };
    }

    private function persistWorkbook(array $workbook): array
    {
        $categories = StatisticCategory::query()->pluck('id', 'code');

        if ($categories->isEmpty()) {
            throw new RuntimeException('Kategori statistik belum tersedia. Jalankan seeder dasar terlebih dahulu.');
        }

        $lineRows = $this->reader->rowsByHeading($workbook['Line QtoQ'] ?? []);
        $ikbmRows = $this->reader->rowsByHeading($workbook['IBS IMK IKBM'] ?? []);
        $progressRows = $this->reader->rowsByHeading($workbook['Pemasukan data'] ?? []);
        $roleRows = $this->reader->rowsByHeading($workbook['Peran industri'] ?? []);
        $kekShareRows = $this->reader->rowsByHeading($workbook['Peran kawasan thd industri'] ?? []);

        if ($lineRows === [] || $ikbmRows === []) {
            throw new RuntimeException('Workbook tidak memiliki sheet utama DSI yang dibutuhkan.');
        }

        $periodPayloads = [];

        foreach ($lineRows as $row) {
            if (! isset($row['Periode'])) {
                continue;
            }

            preg_match('/Q(\d)\s+(\d{4})/i', (string) $row['Periode'], $matches);

            if (! $matches) {
                continue;
            }

            $periodPayloads[(string) $row['Periode']] = [
                'label' => (string) $row['Periode'],
                'year' => (int) $matches[2],
                'quarter' => (int) $matches[1],
            ];
        }

        foreach ($ikbmRows as $row) {
            $label = (string) ($row['Periode'] ?? '');

            if ($label === '') {
                continue;
            }

            preg_match('/Q(\d)\s+(\d{4})/i', $label, $matches);

            if (! $matches) {
                continue;
            }

            $periodPayloads[$label] = [
                'label' => $label,
                'year' => (int) $matches[2],
                'quarter' => (int) $matches[1],
            ];
        }

        foreach ($kekShareRows as $row) {
            $year = $this->toInteger($row['Tahun'] ?? null);
            $quarter = $this->normalizeQuarter($row['Triwulan'] ?? null);

            if (! $year || ! $quarter) {
                continue;
            }

            $label = "Q{$quarter} {$year}";

            $periodPayloads[$label] = [
                'label' => $label,
                'year' => $year,
                'quarter' => $quarter,
            ];
        }

        $sortedPeriods = array_values($periodPayloads);
        usort($sortedPeriods, fn (array $left, array $right) => [$left['year'], $left['quarter']] <=> [$right['year'], $right['quarter']]);

        foreach ($sortedPeriods as $index => $period) {
            StatisticPeriod::updateOrCreate(
                ['label' => $period['label']],
                $period + ['sort_order' => $index + 1],
            );
        }

        $periodIds = StatisticPeriod::query()->pluck('id', 'label');
        $seriesIds = $this->upsertSeriesDefinitions($categories);

        $adhbValues = [];
        $adhkValues = [];

        foreach ($lineRows as $row) {
            $adhbValues[] = $this->toFloat($row['PDB ADHB'] ?? null);
            $adhkValues[] = $this->toFloat($row['PDB ADHK'] ?? null);
        }

        $adhbGrowth = $this->calculateQuarterGrowth($adhbValues);
        $adhkGrowth = $this->calculateQuarterGrowth($adhkValues);

        $this->syncTimeSeries(
            $seriesIds['ibs-index'],
            array_map(fn (array $row) => [
                'period' => (string) ($row['Periode'] ?? ''),
                'value' => $this->toFloat($row['Indeks IBS'] ?? null),
            ], $lineRows),
            $periodIds,
        );

        $this->syncTimeSeries(
            $seriesIds['ibs-growth'],
            array_map(fn (array $row) => [
                'period' => (string) ($row['Periode'] ?? ''),
                'value' => $this->toFloat($row['Pert. Ind. IBS'] ?? null),
            ], $lineRows),
            $periodIds,
        );

        $this->syncTimeSeries(
            $seriesIds['imk-index'],
            array_map(fn (array $row) => [
                'period' => (string) ($row['Periode'] ?? ''),
                'value' => $this->toFloat($row['Indeks IMK'] ?? null),
            ], $lineRows),
            $periodIds,
        );

        $this->syncTimeSeries(
            $seriesIds['imk-growth'],
            array_map(fn (array $row) => [
                'period' => (string) ($row['Periode'] ?? ''),
                'value' => $this->toFloat($row['Pert. Ind. IMK'] ?? null),
            ], $lineRows),
            $periodIds,
        );

        $this->syncTimeSeries(
            $seriesIds['pdb-industrial-adhb-growth'],
            array_map(fn (array $row, int $index) => [
                'period' => (string) ($row['Periode'] ?? ''),
                'value' => $adhbGrowth[$index] ?? null,
            ], $lineRows, array_keys($lineRows)),
            $periodIds,
        );

        $this->syncTimeSeries(
            $seriesIds['pdb-industrial-adhk-growth'],
            array_map(fn (array $row, int $index) => [
                'period' => (string) ($row['Periode'] ?? ''),
                'value' => $adhkGrowth[$index] ?? null,
            ], $lineRows, array_keys($lineRows)),
            $periodIds,
        );

        $this->syncTimeSeries(
            $seriesIds['ikbm-index'],
            array_map(fn (array $row) => [
                'period' => (string) ($row['Periode'] ?? ''),
                'value' => $this->toFloat($row['IKBM'] ?? null),
            ], $ikbmRows),
            $periodIds,
        );

        $this->syncTimeSeries(
            $seriesIds['kekki-workforce-share'],
            array_map(fn (array $row) => [
                'period' => 'Q' . $this->normalizeQuarter($row['Triwulan'] ?? null) . ' ' . $this->toInteger($row['Tahun'] ?? null),
                'value' => $this->toFloat($row['Share Tenaga Kerja KEK-KI'] ?? null, true),
            ], $kekShareRows),
            $periodIds,
        );

        $this->syncTimeSeries(
            $seriesIds['kekki-investment-share'],
            array_map(fn (array $row) => [
                'period' => 'Q' . $this->normalizeQuarter($row['Triwulan'] ?? null) . ' ' . $this->toInteger($row['Tahun'] ?? null),
                'value' => $this->toFloat($row['Share Investasi KEK-KI'] ?? null, true),
            ], $kekShareRows),
            $periodIds,
        );

        $this->syncTimeSeries(
            $seriesIds['kekki-output-share'],
            array_map(fn (array $row) => [
                'period' => 'Q' . $this->normalizeQuarter($row['Triwulan'] ?? null) . ' ' . $this->toInteger($row['Tahun'] ?? null),
                'value' => $this->toFloat($row['Share Output KEK-KI'] ?? null, true),
            ], $kekShareRows),
            $periodIds,
        );

        $industryRolePoints = $this->aggregateIndustryRolePoints($roleRows);
        $this->syncLabelSeries($seriesIds['industry-role-distribution'], $industryRolePoints);

        foreach ($progressRows as $index => $row) {
            $activityName = (string) ($row['Kegiatan'] ?? '');

            if ($activityName === '') {
                continue;
            }

            SurveyProgress::updateOrCreate(
                ['activity_name' => $activityName],
                [
                    'statistic_category_id' => $categories[$this->determineCategoryForProgress($activityName)] ?? null,
                    'target_awal' => $this->toInteger($row['Target Awal'] ?? 0) ?? 0,
                    'selesai_dicacah' => $this->toInteger($row['Selesai dicacah'] ?? 0) ?? 0,
                    'sisa_target' => $this->toInteger($row['Sisa Target'] ?? 0) ?? 0,
                    'eligible' => $this->toInteger($row['Eligible'] ?? 0) ?? 0,
                    'sedang_dicacah' => $this->toInteger($row['Sedang Dicacah'] ?? 0) ?? 0,
                    'condition_label' => (string) ($row['Kondisi Tanggal'] ?? ''),
                    'sort_order' => $index + 1,
                ],
            );
        }

        return [
            'periods' => count($sortedPeriods),
            'series' => count($seriesIds),
            'role_points' => count($industryRolePoints),
            'progress_items' => count($progressRows),
        ];
    }

    private function aggregateIndustryRolePoints(array $rows): array
    {
        $values = [];

        foreach ($rows as $row) {
            $label = trim((string) ($row['Lapangan Usaha'] ?? ''));
            $value = $this->toFloat($row['Distribusi PDB ADHB (%) 2025'] ?? null);

            if ($label === '' || $value === null) {
                continue;
            }

            $values[$label] = $value;
        }

        $mainLabels = ['C', 'G', 'A', 'F'];
        $points = [];
        $others = 0.0;
        $sortOrder = 1;

        foreach ($values as $label => $value) {
            if (in_array($label, $mainLabels, true)) {
                $points[] = ['label' => $label, 'value' => $value, 'sort_order' => $sortOrder++];
            } else {
                $others += $value;
            }
        }

        $points[] = ['label' => 'Others', 'value' => round($others, 2), 'sort_order' => $sortOrder];

        usort($points, fn (array $left, array $right) => $left['sort_order'] <=> $right['sort_order']);

        return $points;
    }

    private function resolveUploadPath(DataSource $dataSource): string
    {
        if (! $dataSource->file_path) {
            throw new RuntimeException('File spreadsheet belum diunggah.');
        }

        return Storage::disk($dataSource->storage_disk ?: 'public')->path($dataSource->file_path);
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
            if (($point['period'] ?? '') === '' || $point['value'] === null || ! isset($periodIds[$point['period']])) {
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

    private function toFloat(mixed $value, bool $multiplyByHundred = false): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = is_string($value)
            ? str_replace(',', '.', preg_replace('/[^0-9,\.\-]/', '', $value) ?: '')
            : $value;

        if (! is_numeric($normalized)) {
            return null;
        }

        $floatValue = (float) $normalized;

        return $multiplyByHundred ? $floatValue * 100 : $floatValue;
    }

    private function toInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) round((float) $value);
    }

    private function upsertSeriesDefinitions($categories): array
    {
        $definitions = [
            'ibs-index' => ['category' => StatisticCategoryCode::IBS->value, 'name' => 'Indeks IBS', 'group_key' => 'production-index', 'chart_type' => 'line', 'unit' => 'index', 'precision' => 2, 'color' => '#f97316', 'is_featured' => true],
            'ibs-growth' => ['category' => StatisticCategoryCode::IBS->value, 'name' => 'Pertumbuhan IBS', 'group_key' => 'production-trend', 'chart_type' => 'line', 'unit' => 'percent', 'precision' => 2, 'color' => '#fb923c', 'is_featured' => true],
            'imk-index' => ['category' => StatisticCategoryCode::IMK->value, 'name' => 'Indeks IMK', 'group_key' => 'production-index', 'chart_type' => 'line', 'unit' => 'index', 'precision' => 2, 'color' => '#7c3aed', 'is_featured' => true],
            'imk-growth' => ['category' => StatisticCategoryCode::IMK->value, 'name' => 'Pertumbuhan IMK', 'group_key' => 'production-trend', 'chart_type' => 'line', 'unit' => 'percent', 'precision' => 2, 'color' => '#a855f7', 'is_featured' => true],
            'ikbm-index' => ['category' => StatisticCategoryCode::KEK_KI->value, 'name' => 'IKBM', 'group_key' => 'production-index', 'chart_type' => 'line', 'unit' => 'index', 'precision' => 0, 'color' => '#f59e0b', 'is_featured' => true],
            'pdb-industrial-adhb-growth' => ['category' => StatisticCategoryCode::DSI->value, 'name' => 'Pert. PDB Industri ADHB', 'group_key' => 'production-trend', 'chart_type' => 'line', 'unit' => 'percent', 'precision' => 2, 'color' => '#2563eb', 'is_featured' => false],
            'pdb-industrial-adhk-growth' => ['category' => StatisticCategoryCode::DSI->value, 'name' => 'Pert. PDB Industri ADHK', 'group_key' => 'production-trend', 'chart_type' => 'line', 'unit' => 'percent', 'precision' => 2, 'color' => '#84cc16', 'is_featured' => false],
            'kekki-workforce-share' => ['category' => StatisticCategoryCode::KEK_KI->value, 'name' => 'Share Tenaga Kerja KEK/KI', 'group_key' => 'kekki-share', 'chart_type' => 'line', 'unit' => 'percent', 'precision' => 2, 'color' => '#dc2626', 'is_featured' => true],
            'kekki-investment-share' => ['category' => StatisticCategoryCode::KEK_KI->value, 'name' => 'Share Investasi KEK/KI', 'group_key' => 'kekki-share', 'chart_type' => 'line', 'unit' => 'percent', 'precision' => 2, 'color' => '#ea580c', 'is_featured' => true],
            'kekki-output-share' => ['category' => StatisticCategoryCode::KEK_KI->value, 'name' => 'Share Output KEK/KI', 'group_key' => 'kekki-share', 'chart_type' => 'line', 'unit' => 'percent', 'precision' => 2, 'color' => '#f59e0b', 'is_featured' => true],
            'industry-role-distribution' => ['category' => StatisticCategoryCode::DSI->value, 'name' => 'Peran Industri Pengolahan dalam Perekonomian', 'group_key' => 'industry-role', 'chart_type' => 'doughnut', 'unit' => 'percent', 'precision' => 2, 'color' => '#fb923c', 'is_featured' => false],
        ];

        foreach ($definitions as $slug => $definition) {
            StatisticSeries::updateOrCreate(
                ['slug' => $slug],
                [
                    'statistic_category_id' => $categories[$definition['category']] ?? null,
                    'name' => $definition['name'],
                    'group_key' => $definition['group_key'],
                    'chart_type' => $definition['chart_type'],
                    'unit' => $definition['unit'],
                    'precision' => $definition['precision'],
                    'color' => $definition['color'],
                    'description' => $definition['name'],
                    'is_featured' => $definition['is_featured'],
                ],
            );
        }

        return StatisticSeries::query()
            ->whereIn('slug', array_keys($definitions))
            ->pluck('id', 'slug')
            ->all();
    }
}
