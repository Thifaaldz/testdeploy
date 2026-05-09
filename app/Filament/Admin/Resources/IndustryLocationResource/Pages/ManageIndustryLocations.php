<?php

namespace App\Filament\Admin\Resources\IndustryLocationResource\Pages;

use App\Filament\Admin\Resources\IndustryLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageIndustryLocations extends ManageRecords
{
    protected static string $resource = IndustryLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
