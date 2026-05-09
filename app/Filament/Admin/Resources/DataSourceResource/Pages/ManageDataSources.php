<?php

namespace App\Filament\Admin\Resources\DataSourceResource\Pages;

use App\Filament\Admin\Resources\DataSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDataSources extends ManageRecords
{
    protected static string $resource = DataSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
