<?php

namespace App\Filament\Admin\Widgets;

use App\Models\StatisticSeries;
use App\Models\StatisticPeriod;
use Filament\Widgets\ChartWidget;

class PeranIndustriChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Peran Industri Pengolahan dalam Ekonomi';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 1;

    public int $selectedYear;
    public array $availableYears = [];

    public function getHeading(): string
    {
        return 'Peran Industri Pengolahan dalam Ekonomi';
    }

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->availableYears = StatisticPeriod::selectRaw('DISTINCT year')
            ->orderByDesc('year')
            ->take(10)
            ->pluck('year')
            ->toArray();

        if (empty($this->availableYears)) {
            $this->availableYears = range(now()->year, now()->year - 5);
        }
    }

    protected function getData(): array
    {
        // Look for distribution/PDB series grouped by category
        $series = StatisticSeries::where('group_key', 'distribusi_pdb')
            ->orWhere('group_key', 'share_kategori')
            ->with(['points' => function ($q) {
                $q->whereHas('period', fn($p) => $p->where('year', $this->selectedYear))
                  ->orderBy('sort_order');
            }, 'category'])
            ->get();

        $fallbackColors = ['#DC2626', '#78350F', '#E07B2A', '#FDE047', '#FB923C'];

        if ($series->isEmpty()) {
            return [
                'datasets' => [[
                    'label'           => 'Distribusi PDB ADHB',
                    'data'            => [40, 20, 15, 10, 15],
                    'backgroundColor' => $fallbackColors,
                    'borderWidth'     => 0,
                ]],
                'labels' => ['C', 'G', 'A', 'F', 'Lainnya'],
            ];
        }

        $labels = [];
        $data   = [];
        $colors = [];

        foreach ($series as $index => $s) {
            $value = $s->points->sum('value');
            if ($value > 0) {
                $labels[] = $s->name;
                $data[]   = round($value, 2);
                $colors[] = $s->color ?? $fallbackColors[$index % count($fallbackColors)];
            }
        }

        return [
            'datasets' => [[
                'label'           => 'Distribusi PDB ADHB',
                'data'            => $data,
                'backgroundColor' => $colors,
                'borderWidth'     => 0,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels'   => ['boxWidth' => 10, 'font' => ['size' => 11]],
                ],
            ],
            'cutout' => '60%',
        ];
    }
}
