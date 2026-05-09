<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SurveyProgressResource\Pages\ManageSurveyProgresses;
use App\Models\SurveyProgress;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SurveyProgressResource extends Resource
{
    protected static ?string $model = SurveyProgress::class;

    protected static ?string $navigationGroup = 'Statistik Industri';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 3;

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
                        Forms\Components\TextInput::make('activity_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('target_awal')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('selesai_dicacah')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('sisa_target')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('eligible')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('sedang_dicacah')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('condition_label')
                            ->label('Kondisi data')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('activity_name')
                    ->label('Kegiatan')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('category.code')
                    ->label('Kategori')
                    ->badge(),
                Tables\Columns\TextColumn::make('target_awal')
                    ->numeric(),
                Tables\Columns\TextColumn::make('selesai_dicacah')
                    ->numeric(),
                Tables\Columns\TextColumn::make('completion_percentage')
                    ->label('Selesai')
                    ->formatStateUsing(fn (float $state): string => number_format($state, 2) . '%'),
                Tables\Columns\TextColumn::make('condition_label')
                    ->label('Kondisi')
                    ->wrap(),
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
            'index' => ManageSurveyProgresses::route('/'),
        ];
    }
}
