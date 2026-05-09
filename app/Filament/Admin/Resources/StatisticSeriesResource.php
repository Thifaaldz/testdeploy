<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StatisticSeriesResource\Pages\ManageStatisticSeries;
use App\Models\StatisticSeries;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class StatisticSeriesResource extends Resource
{
    protected static ?string $model = StatisticSeries::class;

    protected static ?string $navigationGroup = 'Statistik Industri';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

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
                        Forms\Components\TextInput::make('group_key')
                            ->required()
                            ->maxLength(255),
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
                        Forms\Components\Select::make('chart_type')
                            ->options([
                                'line' => 'Line',
                                'doughnut' => 'Donut',
                                'stat' => 'Stat',
                            ])
                            ->required(),
                        Forms\Components\Select::make('unit')
                            ->options([
                                'index' => 'Index',
                                'percent' => 'Persen',
                                'count' => 'Jumlah',
                                'currency' => 'Rupiah',
                            ]),
                        Forms\Components\TextInput::make('color')
                            ->placeholder('#f59e0b'),
                        Forms\Components\TextInput::make('precision')
                            ->numeric()
                            ->default(2)
                            ->minValue(0)
                            ->maxValue(6),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Tampilkan sebagai KPI')
                            ->default(false),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Repeater::make('points')
                    ->relationship()
                    ->label('Titik data')
                    ->defaultItems(0)
                    ->columns(4)
                    ->collapsible()
                    ->orderColumn('sort_order')
                    ->itemLabel(function (array $state): ?string {
                        return $state['label'] ?? null;
                    })
                    ->schema([
                        Forms\Components\Select::make('statistic_period_id')
                            ->label('Periode')
                            ->relationship('period', 'label')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('label')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('value')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('group_key')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('chart_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('points_count')
                    ->counts('points')
                    ->label('Titik'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->since()
                    ->sortable(),
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
            'index' => ManageStatisticSeries::route('/'),
        ];
    }
}
