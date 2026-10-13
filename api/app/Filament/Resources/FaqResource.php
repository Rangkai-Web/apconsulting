<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages\CreateFaq;
use App\Filament\Resources\FaqResource\Pages\EditFaq;
use App\Filament\Resources\FaqResource\Pages\ListFaqs;
use App\Models\Faq;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'FAQ';

    protected static ?string $modelLabel = 'FAQ';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('sort_order')
                ->label('Urutan')
                ->numeric()
                ->minValue(1)
                ->default(fn () => (Faq::max('sort_order') ?? 0) + 1)
                ->unique(table: Faq::class, column: 'sort_order', ignoreRecord: true)
                ->validationMessages([
                    'unique' => 'Nomor urutan ini sudah digunakan oleh FAQ lain.',
                ])
                ->helperText('Otomatis diisi nomor urutan berikutnya. Nomor urutan harus unik.'),
            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),

            Tabs::make('Bahasa')
                ->tabs([
                    Tab::make('Bahasa Indonesia')
                        ->icon('heroicon-o-language')
                        ->schema([
                            TextInput::make('question_id')
                                ->label('Pertanyaan (Bahasa Indonesia)')
                                ->required()
                                ->maxLength(255),
                            Textarea::make('answer_id')
                                ->label('Jawaban (Bahasa Indonesia)')
                                ->required()
                                ->rows(4),
                        ]),
                    Tab::make('English')
                        ->icon('heroicon-o-globe-alt')
                        ->schema([
                            TextInput::make('question_en')
                                ->label('Question (English)')
                                ->placeholder('English question translation...')
                                ->maxLength(255),
                            Textarea::make('answer_en')
                                ->label('Answer (English)')
                                ->placeholder('English answer translation...')
                                ->rows(4),
                        ]),
                ])
                ->columnSpan(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100])
            ->columns([
                TextColumn::make('question_id')
                    ->label('Pertanyaan (ID)')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('question_en')
                    ->label('Question (EN)')
                    ->searchable()
                    ->placeholder('-')
                    ->limit(50),
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
            'index' => ListFaqs::route('/'),
            'create' => CreateFaq::route('/create'),
            'edit' => EditFaq::route('/{record}/edit'),
        ];
    }
}
