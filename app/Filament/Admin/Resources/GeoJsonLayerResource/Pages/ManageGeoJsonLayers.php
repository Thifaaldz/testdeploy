<?php

namespace App\Filament\Admin\Resources\GeoJsonLayerResource\Pages;

use App\Filament\Admin\Resources\GeoJsonLayerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGeoJsonLayers extends ManageRecords
{
    protected static string $resource = GeoJsonLayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
