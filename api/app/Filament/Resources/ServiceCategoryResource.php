<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceCategoryResource\Pages\CreateServiceCategory;
use App\Filament\Resources\ServiceCategoryResource\Pages\EditServiceCategory;
use App\Filament\Resources\ServiceCategoryResource\Pages\ListServiceCategories;
use App\Models\ServiceCategory;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServiceCategoryResource extends Resource
{
    protected static ?string $model = ServiceCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Kategori Layanan';

    protected static ?string $pluralModelLabel = 'Kategori Layanan';

    protected static ?string $modelLabel = 'Kategori Layanan';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('sort_order')
                ->label('Urutan')
                ->numeric()
                ->default(0),
            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),

            Tabs::make('Bahasa')
                ->tabs([
                    Tab::make('Bahasa Indonesia')
                        ->icon('heroicon-o-language')
                        ->schema([
                            TextInput::make('name_id')
                                ->label('Nama Layanan (Bahasa Indonesia)')
                                ->required()
                                ->maxLength(255),
                            Textarea::make('description_id')
                                ->label('Deskripsi Layanan (Bahasa Indonesia)')
                                ->rows(3),
                            TextInput::make('cta_text_id')
                                ->label('Teks Tombol Diskusi / CTA (Bahasa Indonesia)')
                                ->placeholder('e.g. DISKUSIKAN LAYANAN PAJAK'),
                            TagsInput::make('features_id')
                                ->label('Daftar Fitur / Poin Layanan (Bahasa Indonesia)')
                                ->placeholder('Ketik poin lalu tekan Enter'),
                        ]),
                    Tab::make('English')
                        ->icon('heroicon-o-globe-alt')
                        ->schema([
                            TextInput::make('name_en')
                                ->label('Service Name (English)')
                                ->required()
                                ->maxLength(255),
                            Textarea::make('description_en')
                                ->label('Service Description (English)')
                                ->rows(3),
                            TextInput::make('cta_text_en')
                                ->label('CTA Button Text (English)')
                                ->placeholder('e.g. DISCUSS TAX SUPPORT'),
                            TagsInput::make('features_en')
                                ->label('Features / Point List (English)')
                                ->placeholder('Type item and press Enter'),
                        ]),
                ])
                ->columnSpan(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100])
            ->columns([
                TextColumn::make('name_id')
                    ->label('Nama (ID)')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_en')
                    ->label('Name (EN)')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceCategories::route('/'),
            'create' => CreateServiceCategory::route('/create'),
            'edit' => EditServiceCategory::route('/{record}/edit'),
        ];
    }
}
