<?php

namespace App\Filament\Admin\Widgets;

use App\Models\StatisticCategory;
use App\Models\SurveyProgress;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class ProgressDataWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.progress-data-widget';
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 2;

    public int $selectedYear;
    public array $availableYears = [];

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->availableYears = range(now()->year, now()->year - 5);
    }

    public function updatedSelectedYear(): void
    {
        // Livewire will re-render automatically
    }

    public function getProgress(): SurveyProgress|null
    {
        // Get the DSI category and its latest survey progress for the selected year
        return SurveyProgress::whereHas('category', function ($q) {
                $q->where('code', 'DSI');
            })
            ->whereYear('created_at', $this->selectedYear)
            ->latest()
            ->first();
    }

    public function getTotals(): array
    {
        $records = SurveyProgress::whereHas('category', function ($q) {
                $q->where('code', 'DSI');
            })
            ->whereYear('created_at', $this->selectedYear)
            ->get();

        return [
            'selesai_cacah'  => $records->sum('selesai_dicacah'),
            'sisa_target'    => $records->sum('sisa_target'),
            'eligible'       => $records->sum('eligible'),
            'sedang_cacah'   => $records->sum('sedang_dicacah'),
            'kondisi_data'   => $records->count(),
        ];
    }
}
