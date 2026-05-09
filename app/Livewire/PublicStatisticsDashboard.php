<?php

namespace App\Livewire;

use App\Enums\StatisticCategoryCode;
use App\Models\DataSource;
use App\Models\GeoJsonLayer;
use App\Models\IndustryLocation;
use App\Models\StatisticCategory;
use App\Models\StatisticPeriod;
use App\Models\StatisticPoint;
use App\Models\StatisticSeries;
use App\Models\SurveyProgress;
use App\Support\StatisticsDashboardConfig;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

class PublicStatisticsDashboard extends Component
{
    #[Url(as: 'category')]
    public string $selectedCategory = 'DSI';

    #[Url(as: 'year')]
    public string $selectedYear = 'all';

    #[Url(as: 'quarter')]
    public string $selectedQuarter = 'latest';

    public ?string $selectedActivity = null;

    public function mount(): void
    {
        $this->selectedCategory = $this->normalizeCategory($this->selectedCategory);
        $this->selectedActivity = $this->progressItems()->first()?->activity_name;
    }

    public function setCategory(string $code): void
    {
        $this->selectedCategory = $this->normalizeCategory($code);
        $this->selectedActivity = $this->progressItems()->first()?->activity_name;
    }

    public function updatedSelectedYear(): void
    {
        if ($this->selectedYear === 'all') {
            $this->selectedQuarter = 'latest';
        }
    }

    public function updatedSelectedCategory(): void
    {
        $this->selectedActivity = $this->progressItems()->first()?->activity_name;
    }

    public function render()
    {
        $category = $this->categoryRecord();
        $config = StatisticsDashboardConfig::category($this->selectedCategory);
        $kpis = $this->metricsFromSlugs($config['kpi_slugs'] ?? []);
        $shareCards = $this->metricsFromSlugs($config['share_slugs'] ?? []);
        $progressItems = $this->progressItems();
        $selectedProgress = $progressItems->firstWhere('activity_name', $this->selectedActivity) ?? $progressItems->first();
        $insightStats = $this->insightStats();

        return view('livewire.public-statistics-dashboard', [
            'availableYears' => $this->availableYears(),
            'category' => $category,
            'categories' => StatisticCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'distributionChart' => $this->distributionChart($config['distribution_mode'] ?? 'industry-role', $shareCards),
            'insightStats' => $insightStats,
            'kpis' => $kpis,
            'latestSource' => $this->latestSource(),
            'mapPayload' => $this->mapPayload(),
            'progressItems' => $progressItems,
            'selectedProgress' => $selectedProgress,
            'shareCards' => $shareCards,
            'trendChart' => $this->trendChart($config['trend_slugs'] ?? []),
            'viewConfig' => $config,
        ]);
    }

    private function availablePeriods(): Collection
    {
        return StatisticPeriod::query()
            ->when($this->selectedYear !== 'all', fn ($query) => $query->where('year', (int) $this->selectedYear))
            ->when($this->selectedQuarter !== 'latest', fn ($query) => $query->where('quarter', (int) $this->selectedQuarter))
            ->orderBy('sort_order')
            ->get();
    }

    private function availableYears(): Collection
    {
        return StatisticPeriod::query()
            ->select('year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year');
    }

    private function categoryRecord(): StatisticCategory
    {
        return StatisticCategory::query()
            ->where('code', $this->selectedCategory)
            ->firstOrFail();
    }

    private function distributionChart(string $mode, array $shareCards): array
    {
        if ($mode === 'current-share') {
            return [
                'labels' => array_column($shareCards, 'label'),
                'title' => 'Komposisi Share KEK/KI',
                'subtitle' => 'Periode terbaru',
                'values' => array_column($shareCards, 'value'),
                'highlight' => null,
            ];
        }

        $series = StatisticSeries::query()->where('slug', 'industry-role-distribution')->first();
        $points = $series?->points()->orderBy('sort_order')->get() ?? collect();

        return [
            'labels' => $points->pluck('label')->all(),
            'title' => 'Peran Industri Pengolahan dalam Perekonomian',
            'subtitle' => 'Distribusi PDB ADHB 2025',
            'values' => $points->pluck('value')->all(),
            'highlight' => $points->firstWhere('label', 'C')?->value,
        ];
    }

    private function formatMetric(StatisticSeries $series, float $value): string
    {
        $formatted = number_format($value, $series->precision ?? 2, ',', '.');

        return match ($series->unit) {
            'percent' => $formatted . '%',
            'currency' => 'Rp ' . $formatted,
            default => $formatted,
        };
    }

    private function insightStats(): array
    {
        $sourcesQuery = DataSource::query();

        if ($this->selectedCategory !== StatisticCategoryCode::DSI->value) {
            $sourcesQuery->whereHas('category', fn ($query) => $query->where('code', $this->selectedCategory));
        }

        $locationsQuery = IndustryLocation::query();

        if ($this->selectedCategory !== StatisticCategoryCode::DSI->value) {
            $locationsQuery->whereHas('category', fn ($query) => $query->where('code', $this->selectedCategory));
        }

        return [
            [
                'label' => 'Sumber aktif',
                'value' => $sourcesQuery->whereIn('status', ['imported', 'seeded'])->count(),
            ],
            [
                'label' => 'Lokasi industri',
                'value' => $locationsQuery->count(),
            ],
            [
                'label' => 'Layer GeoJSON',
                'value' => GeoJsonLayer::query()->where('is_active', true)->count(),
            ],
            [
                'label' => 'Update terakhir',
                'value' => optional(DataSource::query()->latest('last_imported_at')->first()?->last_imported_at)->diffForHumans() ?? 'Seed awal',
            ],
        ];
    }

    private function mapPayload(): array
    {
        $locations = IndustryLocation::query()
            ->with('category')
            ->when($this->selectedCategory !== StatisticCategoryCode::DSI->value, function ($query) {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('code', $this->selectedCategory));
            })
            ->orderBy('sort_order')
            ->get();

        $layers = GeoJsonLayer::query()
            ->where('is_active', true)
            ->with('category')
            ->get()
            ->filter(function (GeoJsonLayer $layer): bool {
                if (! filled($layer->geojson)) {
                    return false;
                }

                if (! $layer->category) {
                    return true;
                }

                return $this->selectedCategory === StatisticCategoryCode::DSI->value
                    || $layer->category->code === $this->selectedCategory;
            })
            ->values();

        return [
            'geoJsonLayers' => $layers->map(fn (GeoJsonLayer $layer) => [
                'name' => $layer->name,
                'style' => $layer->style ?: [],
                'geojson' => $layer->geojsonPayload,
            ])->all(),
            'locations' => $locations->map(fn (IndustryLocation $location) => [
                'category' => $location->category?->code,
                'city' => $location->city,
                'is_dummy' => $location->is_dummy,
                'label' => $location->name,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'output' => $location->output_value,
                'province' => $location->province,
                'sector' => $location->industry_sector,
                'status' => $location->status,
                'workforce' => $location->workforce,
            ])->all(),
        ];
    }

    private function latestSource(): ?DataSource
    {
        $query = DataSource::query()->with('category');

        if ($this->selectedCategory !== StatisticCategoryCode::DSI->value) {
            $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('code', $this->selectedCategory));
        }

        $source = $query
            ->orderByDesc('last_imported_at')
            ->orderByDesc('updated_at')
            ->first();

        if ($source) {
            return $source;
        }

        return DataSource::query()
            ->with('category')
            ->orderByDesc('last_imported_at')
            ->orderByDesc('updated_at')
            ->first();
    }

    private function metricsFromSlugs(array $slugs): array
    {
        $metrics = [];

        foreach ($slugs as $slug) {
            $series = StatisticSeries::query()->where('slug', $slug)->first();

            if (! $series) {
                continue;
            }

            $point = $this->resolvePointForSeries($series);

            if (! $point) {
                continue;
            }

            $metrics[] = [
                'formatted' => $this->formatMetric($series, $point->value),
                'label' => $series->name,
                'period' => $point->period?->label,
                'value' => $point->value,
            ];
        }

        return $metrics;
    }

    private function normalizeCategory(string $code): string
    {
        $availableCodes = StatisticCategory::query()->pluck('code')->all();

        return in_array($code, $availableCodes, true)
            ? $code
            : StatisticCategoryCode::DSI->value;
    }

    private function progressItems(): Collection
    {
        $query = SurveyProgress::query()->with('category')->orderBy('sort_order');

        if ($this->selectedCategory !== StatisticCategoryCode::DSI->value) {
            $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('code', $this->selectedCategory));
        }

        $items = $query->get();

        return $items->isNotEmpty()
            ? $items
            : SurveyProgress::query()->with('category')->orderBy('sort_order')->get();
    }

    private function resolvePointForSeries(StatisticSeries $series): ?StatisticPoint
    {
        $candidatePeriods = $this->availablePeriods()->pluck('id')->all();
        $query = $series->points()->with('period')->orderByDesc('sort_order');

        if ($candidatePeriods !== []) {
            $point = (clone $query)->whereIn('statistic_period_id', $candidatePeriods)->first();

            if ($point) {
                return $point;
            }
        }

        return $query->first();
    }

    private function trendChart(array $slugs): array
    {
        $periods = $this->availablePeriods();

        if ($periods->count() < 2) {
            $periods = StatisticPeriod::query()->orderBy('sort_order')->get();
        }

        $datasets = [];

        foreach ($slugs as $slug) {
            $series = StatisticSeries::query()->where('slug', $slug)->first();

            if (! $series) {
                continue;
            }

            $valuesByPeriod = $series->points()->pluck('value', 'statistic_period_id');

            $datasets[] = [
                'borderColor' => $series->color,
                'data' => $periods->map(fn (StatisticPeriod $period) => $valuesByPeriod[$period->id] ?? null)->all(),
                'label' => $series->name,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $periods->pluck('label')->all(),
            'title' => $this->selectedCategory === StatisticCategoryCode::KEK_KI->value
                ? 'Perkembangan Share KEK/KI'
                : 'Perkembangan Data Produksi',
        ];
    }
}
