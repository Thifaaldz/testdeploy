<?php

namespace App\Filament\Admin\Widgets;

use App\Models\StatisticSeries;
use App\Models\StatisticPeriod;
use Filament\Widgets\ChartWidget;

class DataIndustriChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Pertumbuhan Data Industri';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;

    public int $selectedYear;
    public array $availableYears = [];

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
        // Fetch all series related to group 'produksi' or all featured series
        $allSeries = StatisticSeries::whereHas('points.period', function ($q) {
                $q->where('year', $this->selectedYear);
            })
            ->with(['points' => function ($q) {
                $q->whereHas('period', function ($pq) {
                    $pq->where('year', $this->selectedYear);
                })->with('period')->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->take(5) // limit to 5 series max for readability
            ->get();

        // Build labels from periods
        $labels = StatisticPeriod::where('year', $this->selectedYear)
            ->orderBy('sort_order')
            ->pluck('label')
            ->toArray();

        if (empty($labels)) {
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        }

        $colors = ['#E07B2A', '#2563EB', '#16A34A', '#DC2626', '#7C3AED'];

        $datasets = [];
        if ($allSeries->isEmpty()) {
            // Fallback dummy data
            $datasets = [
                [
                    'label'       => 'IBS',
                    'data'        => [98, 102, 105, 110, 108, 112, 115, 118, 120, 123, 125, 128],
                    'borderColor' => '#E07B2A',
                    'borderWidth' => 2,
                    'tension'     => 0.4,
                    'fill'        => false,
                ],
                [
                    'label'       => 'IMK',
                    'data'        => [85, 87, 90, 92, 95, 97, 98, 100, 102, 105, 107, 110],
                    'borderColor' => '#2563EB',
                    'borderWidth' => 2,
                    'tension'     => 0.4,
                    'fill'        => false,
                ],
                [
                    'label'       => 'KEK-KI',
                    'data'        => [60, 62, 63, 65, 66, 68, 70, 72, 74, 75, 77, 79],
                    'borderColor' => '#16A34A',
                    'borderWidth' => 2,
                    'tension'     => 0.4,
                    'fill'        => false,
                ],
            ];
        } else {
            foreach ($allSeries as $index => $series) {
                $data = [];
                foreach ($labels as $label) {
                    $point = $series->points->first(fn($p) => $p->period?->label === $label);
                    $data[] = $point ? $point->value : null;
                }
                $datasets[] = [
                    'label'       => $series->name,
                    'data'        => $data,
                    'borderColor' => $series->color ?? ($colors[$index % count($colors)]),
                    'borderWidth' => 2,
                    'tension'     => 0.4,
                    'fill'        => false,
                ];
            }
        }

        return [
            'datasets' => $datasets,
            'labels'   => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels'   => ['boxWidth' => 12, 'font' => ['size' => 10]],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => false,
                    'grid'        => ['color' => 'rgba(0,0,0,0.05)'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
