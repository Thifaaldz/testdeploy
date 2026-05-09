<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GeoJsonLayerResource\Pages\ManageGeoJsonLayers;
use App\Models\GeoJsonLayer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GeoJsonLayerResource extends Resource
{
    protected static ?string $model = GeoJsonLayer::class;

    protected static ?string $navigationGroup = 'Peta Industri';

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('statistic_category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if (! filled($get('slug')) && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\FileUpload::make('source_file')
                            ->label('Upload GeoJSON')
                            ->disk('public')
                            ->directory('geojson-layers')
                            ->acceptedFileTypes([
                                'application/geo+json',
                                'application/json',
                                '.geojson',
                                '.json',
                            ])
                            ->downloadable()
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if (! $state) {
                                    return;
                                }

                                $set('geojson', Storage::disk('public')->get($state));
                            })
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('geojson')
                            ->label('Isi GeoJSON')
                            ->rows(12)
                            ->rules(['nullable', 'json'])
                            ->helperText('Otomatis terisi saat upload file, atau bisa ditempel manual.')
                            ->columnSpanFull(),
                        Forms\Components\KeyValue::make('style')
                            ->label('Style layer')
                            ->default([
                                'color' => '#f59e0b',
                                'fillColor' => '#fbbf24',
                                'fillOpacity' => 0.2,
                                'weight' => 2,
                            ])
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('category.code')
                    ->label('Kategori')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Aktif'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGeoJsonLayers::route('/'),
        ];
    }
}
