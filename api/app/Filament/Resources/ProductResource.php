<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Produk';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('category_id')
                ->label('Kategori')
                ->options(fn () => Category::query()->pluck('name', 'id'))
                ->searchable()
                ->columnSpan(2)
                ->required(),
            TextInput::make('name')
                ->label('Nama Produk')
                ->required()
                ->maxLength(255)
                ->live(debounce: 500)
                ->afterStateUpdated(function (string $operation, Set $set, ?string $state) {
                    if ($operation === 'create') {
                        $set('slug', Str::slug($state ?? ''));
                    }
                }),
            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            RichEditor::make('description')
                ->label('Deskripsi')
                ->columnSpan(2)
                ->formatStateUsing(fn ($state) => empty($state) ? '' : (str_starts_with($state, '<') ? $state : '<p>' . e($state) . '</p>'))
                ->toolbarButtons([
                    'bold',
                    'italic',
                    'strike',
                    'underline',
                    'bulletList',
                    'orderedList',
                    'h2',
                    'h3',
                    'redo',
                    'undo',
                ]),
            FileUpload::make('images')
                ->label('Gambar Produk')
                ->disk('public_uploads')
                ->directory('products')
                ->visibility('public')
                ->multiple()
                ->maxFiles(5)
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->reorderable()
                ->helperText('Maksimal 5 gambar, masing-masing maks. 2MB, format JPG/PNG/WEBP.'),
            Group::make([
                Placeholder::make('status_placeholder')
                    ->label('Status'),
                Toggle::make('is_active')
                    ->label(fn ($state) => $state ? 'Aktif' : 'Tidak Aktif')
                    ->live()
                    ->default(true),
            ])->gap(1),
            Repeater::make('variants')
                ->relationship('variants')
                ->label('Daftar Varian Produk')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Varian')
                        ->required()
                        ->placeholder('Contoh: 50ml')
                        ->maxLength(255)
                        ->columnSpan(2),
                    TextInput::make('sku')
                        ->label('SKU')
                        ->required()
                        ->placeholder('AMC-50')
                        ->unique(ignoreRecord: true)
                        ->columnSpan(2),
                    TextInput::make('price')
                        ->label('Harga (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->required()
                        ->minValue(0)
                        ->columnSpan(2),
                    TextInput::make('stock')
                        ->label('Stok')
                        ->numeric()
                        ->default(0)
                        ->required()
                        ->minValue(0)
                        ->columnSpan(2),
                    TextInput::make('weight')
                        ->label('Berat (gr)')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->placeholder('Gram')
                        ->columnSpan(2),
                    FileUpload::make('image')
                        ->label('Gambar Varian')
                        ->disk('public_uploads')
                        ->directory('variants')
                        ->visibility('public')
                        ->image()
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->columnSpan(2),
                ])
                ->columns(12)
                ->defaultItems(1)
                ->required()
                ->addActionLabel('Tambah Varian')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images')
                    ->label('Gambar')
                    ->disk('public_uploads')
                    ->getStateUsing(fn (Product $record) => $record->images[0] ?? null)
                    ->circular(),
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Kategori'),
                TextColumn::make('variants_count')->label('Varian')->counts('variants'),
                TextColumn::make('rating_avg')->label('Rating')->formatStateUsing(
                    fn (?string $state) => $state ? number_format((float) $state, 1) : '—'
                ),
                ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(10)
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
