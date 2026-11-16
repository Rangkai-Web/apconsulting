<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages\CreateFaq;
use App\Filament\Resources\FaqResource\Pages\EditFaq;
use App\Filament\Resources\FaqResource\Pages\ListFaqs;
use App\Models\Faq;
use App\Models\ServiceCategory;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'FAQ';

    protected static ?string $modelLabel = 'FAQ';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'lg' => 3,
            ])
            ->schema([
                // ==========================================
                // KOLOM KIRI (Konten FAQ - 2 Kolom Lebar)
                // ==========================================
                Group::make()
                    ->schema([
                        Tabs::make('Konten FAQ')
                            ->tabs([
                                // TAB 1: BAHASA INDONESIA
                                Tab::make('Bahasa Indonesia')
                                    ->icon('heroicon-o-language')
                                    ->badge('ID')
                                    ->schema([
                                        Select::make('category_id')
                                            ->label('Kategori Layanan (ID)')
                                            ->placeholder('Pilih kategori terkait (Opsional)...')
                                            ->options(fn () => ServiceCategory::where('is_active', true)->pluck('name_id', 'name_id')->toArray())
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                                if ($state) {
                                                    $cat = ServiceCategory::where('name_id', $state)->first();
                                                    if ($cat) {
                                                        $set('category_en', $cat->name_en);
                                                    }
                                                }
                                            }),

                                        TextInput::make('question_id')
                                            ->label('Pertanyaan (ID)')
                                            ->placeholder('Contoh: Kapan batas waktu pelaporan SPT Tahunan Badan & Orang Pribadi?')
                                            ->required()
                                            ->maxLength(255),

                                        Textarea::make('answer_id')
                                            ->label('Jawaban (ID)')
                                            ->placeholder('Tuliskan penjelasan jawaban secara jelas, solutif, dan profesional...')
                                            ->required()
                                            ->rows(6)
                                            ->helperText('Gunakan baris baru (Enter) untuk memisahkan poin-poin penjelasan agar mudah dibaca.'),
                                    ]),

                                // TAB 2: ENGLISH TRANSLATION
                                Tab::make('English Translation')
                                    ->icon('heroicon-o-globe-alt')
                                    ->badge('EN')
                                    ->schema([
                                        Select::make('category_en')
                                            ->label('Service Category (EN)')
                                            ->placeholder('Select English category...')
                                            ->options(fn () => ServiceCategory::where('is_active', true)->pluck('name_en', 'name_en')->toArray())
                                            ->searchable()
                                            ->preload(),

                                        TextInput::make('question_en')
                                            ->label('Question (EN)')
                                            ->placeholder('Example: What is the deadline for annual corporate tax filing?')
                                            ->maxLength(255),

                                        Textarea::make('answer_en')
                                            ->label('Answer (EN)')
                                            ->placeholder('Write clear English explanation for international/English clients...')
                                            ->rows(6)
                                            ->helperText('Terjemahan jawaban untuk pengunjung website dalam mode bahasa Inggris.'),
                                    ]),
                            ]),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),

                // ==========================================
                // KOLOM KANAN (Sidebar Form - 1 Kolom)
                // ==========================================
                Group::make()
                    ->schema([
                        Section::make('Status & Tampilan')
                            ->description('Pengaturan visibilitas di website')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Tampilkan di Website')
                                    ->helperText('Jika nonaktif, FAQ disembunyikan dari halaman publik.')
                                    ->default(true),

                                TextInput::make('sort_order')
                                    ->label('Urutan Tampil')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(fn () => (Faq::max('sort_order') ?? 0) + 1)
                                    ->required()
                                    ->helperText('Nomor urutan pertanyaan saat ditampilkan di website.'),
                            ]),

                        Section::make('Panduan FAQ')
                            ->description('Tips penulisan FAQ efektif')
                            ->icon('heroicon-o-light-bulb')
                            ->schema([
                                Placeholder::make('faq_tips')
                                    ->hiddenLabel()
                                    ->content(new HtmlString('
                                        <div style="font-size: 0.8rem; line-height: 1.5; color: #94a3b8;">
                                            💡 <strong>Tips Efektif:</strong><br/>
                                            • Gunakan pertanyaan singkat yang langsung menyasar kebutuhan klien.<br/>
                                            • Jawaban sebaiknya langsung memberikan solusi praktis.<br/>
                                            • Anda juga dapat mengatur urutan dengan <em>drag &amp; drop</em> di tabel utama.
                                        </div>
                                    ')),
                            ]),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 1,
                    ]),
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
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('question_id')
                    ->label('Pertanyaan & Jawaban')
                    ->searchable(['question_id', 'answer_id'])
                    ->sortable()
                    ->limit(60)
                    ->tooltip(fn (Faq $record): string => $record->question_id)
                    ->description(fn (Faq $record): ?string => $record->answer_id ? Str::limit($record->answer_id, 80) : null)
                    ->wrap(),

                TextColumn::make('category_id')
                    ->label('Kategori')
                    ->placeholder('Umum')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('question_en')
                    ->label('Versi EN')
                    ->badge()
                    ->state(fn (Faq $record): string => !empty($record->question_en) ? 'Tersedia' : 'Belum')
                    ->color(fn (string $state): string => $state === 'Tersedia' ? 'success' : 'gray')
                    ->alignCenter(),

                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->onColor('success')
                    ->offColor('gray')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Hanya yang Aktif')
                    ->falseLabel('Hanya Nonaktif'),

                SelectFilter::make('category_id')
                    ->label('Kategori Layanan')
                    ->options(fn () => ServiceCategory::where('is_active', true)->pluck('name_id', 'name_id')->toArray()),
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
