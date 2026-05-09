<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\IndustryLocationResource\Pages\ManageIndustryLocations;
use App\Models\IndustryLocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class IndustryLocationResource extends Resource
{
    protected static ?string $model = IndustryLocation::class;

    protected static ?string $navigationGroup = 'Peta Industri';

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?int $navigationSort = 1;

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
                        Forms\Components\TextInput::make('status')
                            ->required()
                            ->default('active'),
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
                        Forms\Components\TextInput::make('industry_sector')
                            ->label('Sektor industri')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_dummy')
                            ->label('Data dummy')
                            ->default(true),
                        Forms\Components\TextInput::make('province')
                            ->label('Provinsi')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->label('Kab/Kota')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('latitude')
                            ->numeric()
                            ->required()
                            ->step(0.0000001),
                        Forms\Components\TextInput::make('longitude')
                            ->numeric()
                            ->required()
                            ->step(0.0000001),
                        Forms\Components\TextInput::make('workforce')
                            ->label('Tenaga kerja')
                            ->numeric(),
                        Forms\Components\TextInput::make('investment_value')
                            ->label('Nilai investasi')
                            ->numeric(),
                        Forms\Components\TextInput::make('output_value')
                            ->label('Nilai output')
                            ->numeric(),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('map_preview')
                            ->label('Preview OpenStreetMap')
                            ->content(function (Get $get): HtmlString {
                                $latitude = $get('latitude');
                                $longitude = $get('longitude');

                                if (! is_numeric($latitude) || ! is_numeric($longitude)) {
                                    return new HtmlString('<span class="text-sm text-gray-500">Isi koordinat latitude dan longitude untuk menampilkan preview.</span>');
                                }

                                $bbox = sprintf(
                                    '%s,%s,%s,%s',
                                    $longitude - 0.25,
                                    $latitude - 0.18,
                                    $longitude + 0.25,
                                    $latitude + 0.18,
                                );

                                $src = 'https://www.openstreetmap.org/export/embed.html?bbox=' . rawurlencode($bbox) . '&layer=mapnik&marker=' . rawurlencode("{$latitude},{$longitude}");

                                return new HtmlString('<iframe title="OpenStreetMap preview" src="' . $src . '" class="h-72 w-full rounded-2xl border border-gray-200"></iframe>');
                            })
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
                Tables\Columns\TextColumn::make('province')
                    ->label('Provinsi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Kab/Kota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('industry_sector')
                    ->label('Sektor')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_dummy')
                    ->boolean()
                    ->label('Dummy'),
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
            'index' => ManageIndustryLocations::route('/'),
        ];
    }
}
