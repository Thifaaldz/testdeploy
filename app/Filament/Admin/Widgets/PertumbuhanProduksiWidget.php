<?php

namespace App\Filament\Admin\Widgets;

use App\Models\StatisticSeries;
use App\Models\StatisticPeriod;
use Filament\Widgets\Widget;

class PertumbuhanProduksiWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.pertumbuhan-produksi-widget';
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 3;

    public int $selectedYear;
    public int $selectedQuarter;
    public array $availableYears    = [];
    public array $availableQuarters = [1, 2, 3, 4];

    public function mount(): void
    {
        $this->selectedYear    = now()->year;
        $this->selectedQuarter = (int) ceil(now()->month / 3);

        $this->availableYears = StatisticPeriod::selectRaw('DISTINCT year')
            ->orderByDesc('year')
            ->take(10)
            ->pluck('year')
            ->toArray();

        if (empty($this->availableYears)) {
            $this->availableYears = range(now()->year, now()->year - 5);
        }
    }

    public function getGrowthData(): array
    {
        // Lookup series by slug or name for IBS, IMK, IKBM
        $slugs = ['ibs-qtq', 'imk-qtq', 'ikbm-qtq'];

        $results = [];
        foreach ($slugs as $slug) {
            $series = StatisticSeries::where('slug', $slug)->first();
            if ($series) {
                $period = StatisticPeriod::where('year', $this->selectedYear)
                    ->where('quarter', $this->selectedQuarter)
                    ->first();

                $value = $period
                    ? $series->points()->where('statistic_period_id', $period->id)->value('value')
                    : null;

                $results[] = [
                    'label'    => strtoupper(str_replace('-qtq', '', $slug)),
                    'subtitle' => 'QtoQ',
                    'value'    => $value !== null ? number_format($value, 2) . ' %' : '– %',
                ];
            } else {
                // Fallback if slug not found
                $results[] = [
                    'label'    => strtoupper(str_replace('-qtq', '', $slug)),
                    'subtitle' => 'QtoQ',
                    'value'    => '– %',
                ];
            }
        }

        return $results;
    }
}
