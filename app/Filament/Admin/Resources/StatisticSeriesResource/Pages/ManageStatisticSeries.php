<?php

namespace App\Filament\Admin\Resources\StatisticSeriesResource\Pages;

use App\Filament\Admin\Resources\StatisticSeriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStatisticSeries extends ManageRecords
{
    protected static string $resource = StatisticSeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
