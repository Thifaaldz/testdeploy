<?php

namespace App\Filament\Admin\Resources\SurveyProgressResource\Pages;

use App\Filament\Admin\Resources\SurveyProgressResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSurveyProgresses extends ManageRecords
{
    protected static string $resource = SurveyProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
