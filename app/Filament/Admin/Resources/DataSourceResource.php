<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DataSourceResource\Pages\ManageDataSources;
use App\Models\DataSource;
use App\Services\Statistics\StatisticsImportService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Throwable;

class DataSourceResource extends Resource
{
    protected static ?string $model = DataSource::class;

    protected static ?string $navigationGroup = 'Statistik Industri';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?int $navigationSort = 1;

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
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('source_type')
                            ->label('Tipe Sumber')
                            ->options([
                                'excel_upload' => 'Upload Excel (.xlsx)',
                                'spreadsheet_link' => 'Link Spreadsheet',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama sumber data')
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
                        Forms\Components\Select::make('parser_key')
                            ->label('Parser')
                            ->options([
                                'dsi_excel_v1' => 'Parser DSI Excel v1',
                            ])
                            ->default('dsi_excel_v1')
                            ->required(),
                        Forms\Components\TextInput::make('status')
                            ->disabled()
                            ->dehydrated(false)
                            ->default('draft'),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('File spreadsheet')
                            ->disk('public')
                            ->directory('statistic-sources')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                '.xlsx',
                            ])
                            ->downloadable()
                            ->openable()
                            ->visible(fn (Get $get): bool => $get('source_type') === 'excel_upload')
                            ->required(fn (Get $get): bool => $get('source_type') === 'excel_upload'),
                        Forms\Components\TextInput::make('spreadsheet_url')
                            ->label('URL spreadsheet publik')
                            ->placeholder('https://docs.google.com/spreadsheets/d/.../edit')
                            ->url()
                            ->visible(fn (Get $get): bool => $get('source_type') === 'spreadsheet_link')
                            ->required(fn (Get $get): bool => $get('source_type') === 'spreadsheet_link'),
                        Forms\Components\DateTimePicker::make('last_imported_at')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Textarea::make('last_error')
                            ->label('Error terakhir')
                            ->rows(3)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(4)
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
                Tables\Columns\TextColumn::make('source_type')
                    ->label('Sumber')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'excel_upload' ? 'Excel' : 'Spreadsheet'),
                Tables\Columns\TextColumn::make('parser_key')
                    ->label('Parser')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'imported', 'seeded' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('last_imported_at')
                    ->label('Import terakhir')
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('import')
                    ->label('Import')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (DataSource $record): void {
                        try {
                            $summary = app(StatisticsImportService::class)->import($record);

                            Notification::make()
                                ->title('Import selesai')
                                ->body("Periode: {$summary['periods']}, series: {$summary['series']}, progres: {$summary['progress_items']}.")
                                ->success()
                                ->send();
                        } catch (Throwable $exception) {
                            Notification::make()
                                ->title('Import gagal')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
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
            'index' => ManageDataSources::route('/'),
        ];
    }
}
