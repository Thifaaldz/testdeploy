<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.admin.pages.dashboard';
    
    public $activeTab = 'DSI';
    
    #[\Livewire\Attributes\On('tabChanged')]
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\TimelineWidget::class,
            \App\Filament\Admin\Widgets\ProgressDataWidget::class,
            \App\Filament\Admin\Widgets\DataIndustriChartWidget::class,
            \App\Filament\Admin\Widgets\PertumbuhanProduksiWidget::class,
            \App\Filament\Admin\Widgets\PeranIndustriChartWidget::class,
        ];
    }
}
