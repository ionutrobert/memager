<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Livewire\ViewImprumut;
use App\Models\ContactInfo;
use App\Models\Member;
use App\Models\PreviousIdentity;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filipac\Cnp\Cnp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;


class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Baza Date';
    protected static ?int $navigationSort     = -99;
    protected static ?string $navigationLabel = 'Membri';

    protected static ?string $label            = 'Membru';
    protected static ?string $pluralModelLabel = 'Membri';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function infolist(Infolist $infolist): Infolist
    {


        return $infolist
            ->schema([

                Section::make('Identitate')
                    ->schema([
                        Infolists\Components\TextEntry::make('CNP')
                            ->label('CNP'),
                        Infolists\Components\TextEntry::make('CNPValid')
                            ->label('Validitate CNP')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'Valid'                           => 'success',
                                'Invalid'                         => 'danger',
                            })
                            ->default(CNP::valid($infolist->getRecord()->CNP) ? 'Valid' : 'Invalid'),


                        Infolists\Components\Fieldset::make('Carte de Identitate')
                            ->schema([

                                Infolists\Components\TextEntry::make('full_name'),
                                Infolists\Components\TextEntry::make('CI')
                                    ->label('Carte identitate'),
                                Infolists\Components\TextEntry::make('data_emitere')
                                    ->date(),
                                Infolists\Components\TextEntry::make('data_expirare')
                                    ->date(),

                                Infolists\Components\TextEntry::make('CIValid')
                                    ->label('Valabilitate Carte identitate')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'Valid'                           => 'success',
                                        'Expirat'                         => 'danger',
                                    })
                                    ->default(Carbon::createFromDate($infolist->getRecord()->data_expirare)->gt(Carbon::today()) ? 'Valid' : 'Expirat')
                                    ->prefix(
                                        fn(string $state): string => match ($state) {
                                            'Valid'                   => 'Expira ' . Carbon::createFromDate($infolist->getRecord()->data_expirare)->diffForHumans() . ' - ',
                                            'Expirat'                 => ' ',
                                        }
                                    )
                                    ->suffix(
                                        fn(string $state): string => match ($state) {
                                            'Valid'                   => ' ',
                                            'Expirat'                 => ' cu ' . Carbon::createFromDate($infolist->getRecord()->data_expirare)->diffForHumans(),
                                        }
                                    ),
                                Infolists\Components\Fieldset::make('Adresa Actuala')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('domiciliu'),
                                        Infolists\Components\TextEntry::make('oras'),
                                        Infolists\Components\TextEntry::make('judet'),

                                    ])->columns(3),

                                Infolists\Components\Fieldset::make('Loc Nastere')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('oras_nastere'),
                                        Infolists\Components\TextEntry::make('judet_nastere'),

                                    ])->columns(2),

                            ])->columns(2),

                    ])
                    ->columns(2)
                    ->columnSpan(2),

                Section::make('Informatii')
                    ->schema([
                        Infolists\Components\Group::make()
                            ->label('Informatii de contact')
                            ->schema([
                                Infolists\Components\TextEntry::make('contact_phone')
                                    ->label('Telefon')
                                    ->state(fn(Member $record) => $record->contact_infos()->where('tip_info', 'telefon')->latest('created_at')->first()?->info ?? 'N/A'),
                                Infolists\Components\TextEntry::make('contact_email')
                                    ->label('Email')
                                    ->state(fn(Member $record) => $record->contact_infos()->where('tip_info', 'email')->latest('created_at')->first()?->info ?? 'N/A'),
                                Infolists\Components\TextEntry::make('contact_address')
                                    ->label('Adresa Corespondenta')
                                    ->state(fn(Member $record) => $record->contact_infos()->where('tip_info', 'adresa_corespondenta')->latest('created_at')->first()?->info ?? 'N/A'),
                            ])->columns(3)
                            ->hidden(fn(Member $record) => $record->contact_infos()->count() === 0),

                        Infolists\Components\Group::make()
                            ->label('Informatii angajare')
                            ->schema([
                                Infolists\Components\TextEntry::make('workplace_employer')
                                    ->label('Angajat la')
                                    ->state(fn(Member $record) => $record->workplaces()->latest('member_workplace.updated_at')->first()?->employer ?? 'N/A'),
                                Infolists\Components\TextEntry::make('workplace_contact')
                                    ->label('Angajator Contact')
                                    ->state(fn(Member $record) => $record->workplaces()->latest('member_workplace.updated_at')->first()?->contact ?? 'N/A'),
                                Infolists\Components\TextEntry::make('workplace_since')
                                    ->label('De la data')
                                    ->state(fn(Member $record) => $record->workplaces()->latest('member_workplace.updated_at')->first()?->pivot?->data_cim ?? 'N/A'),
                            ])->columns(3)
                            ->hidden(fn(Member $record) => $record->workplaces()->count() === 0),

                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\TextEntry::make('member_joined_date')
                                    ->label('Data cand a devenit membru')
                                    ->state(fn(Member $record) => $record->created_at?->toDateString() ?? 'N/A')
                                    ->since(),
                                Infolists\Components\TextEntry::make('last_payment_date')
                                    ->label('Data ultimei plati')
                                    ->state(fn(Member $record) => $record->debts()->with('payment')->get()->flatMap(fn($debt) => $debt->payment)->sortByDesc('data')->first()?->data)
                                    ->since(),
                            ])->columns(2),

                    ])

                    ->columnSpan(1),

                // Section::make('Debts')
                // ->hidden(
                //     fn(Member $record) => $record->debts()->count() == 0)
                //     ->schema([
                //         Infolists\Components\RepeatableEntry::make('debts')
                //             ->label('Debts')

                //             ->schema([

                //                 Infolists\Components\TextEntry::make('suma'),
                //                 Infolists\Components\TextEntry::make('data_acordare'),

                //             ])
                //             ->columns(2),
                //     ]),

                Section::make('Imprumuturi')
                    ->hidden(
                        fn(Member $record) => $record->debts()->count() == 0)
                    ->schema([
                        Livewire::make(ViewImprumut::class)
                            ->columnSpanFull(),
                    ]),

            ])->columns(3);

    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('CNP')
                    ->required()
                    ->autofocus()
                    ->unique(ignoreRecord: true)
                    ->minLength(13)
                    ->maxLength(13)
                    ->rules('cnp')
                    ->extraAttributes(['oninput' => "window.cnpCheck && window.cnpCheck(this.value)"]),
                Forms\Components\Placeholder::make('cnp_check')
                    ->content(new \Illuminate\Support\HtmlString('<div id="cnp-check-wrapper"><div id="cnp-check">Enter CNP to validate</div></div>')),
                Forms\Components\TextInput::make('ci_serie')
                    ->required()
                    ->minLength(2)
                    ->maxLength(2)
                    ->rule('regex:/^[A-Z]{2}$/')
                    ->helperText('Exact 2 litere majuscule, ex. VX'),
                Forms\Components\TextInput::make('ci_numar')
                    ->required()
                    ->numeric()
                    ->minLength(6)
                    ->maxLength(6)
                    ->rule('regex:/^[0-9]{6}$/')
                    ->helperText('Exact 6 cifre, ex. 123456'),
                Forms\Components\TextInput::make('emis_de')
                    ->required(),
                Forms\Components\DatePicker::make('data_emitere')
                    ->required(),
                Forms\Components\DatePicker::make('data_expirare')
                    ->required(),
                Forms\Components\TextInput::make('nume')
                    ->required(),
                Forms\Components\TextInput::make('prenume')
                    ->required(),
                Forms\Components\TextInput::make('cetatenie')
                    ->required(),
                Forms\Components\TextInput::make('nationalitate')
                    ->required(),
                Forms\Components\TextInput::make('domiciliu')
                    ->required(),
                Forms\Components\TextInput::make('oras_nastere')
                    ->required(),

                Forms\Components\Section::make('contact_info')
                    ->label('Contact Info')

                    ->schema([
                        Repeater::make('contact_info')
                            ->label('Informatii de contact')
                            ->addActionLabel('Adaugare Contact Info')
                            ->schema([

                                Forms\Components\Select::make('contact_info.tip')
                                    ->label('Selectati tipul de informatie de contact')
                                    ->options([
                                        'telefon' => 'Telefon',
                                        'email'   => 'Adresa Email',
                                        'adresa'  => 'Adresa Corespondenta',
                                    ])
                                    ->default('telefon'),
                                Forms\Components\TextInput::make('contact_info.info')
                                    ->label('Introduceti datele de contact corespondente tipului de contact')
                                    ->required(),
                                Forms\Components\TextInput::make('contact_info.data_info')->default(now())->hidden(),
                                Forms\Components\TextInput::make('contact_info.adaugat_de')->default(auth()->user()->id)->hidden(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn(array $state): ?string => $state['contact_info.info'] ?? null)
                            ->deleteAction(
                                fn(Action $action) => $action->requiresConfirmation(),
                            ),
                    ]),

                // Change into relationship
                Forms\Components\Section::make('Informatii Loc de Munca')
                    ->label('Informatii despre Locul de Munca')

                    ->schema([

                        // Forms\Components\Select::make('workplaces')
                        // ->relationship('workplaces','employer')
                        // ->multiple(),
                        Repeater::make('workplace')

                            ->label('')
                            ->addActionLabel('Informatii Loc de Munca')
                            ->schema([

                                Forms\Components\Select::make('loc_munca_info.salariat_sau_pensionar')
                                    ->label('Selectati tipul de informatie de contact')
                                    ->options([
                                        'Salariat' => 'Salariat',
                                        'Pensionar'   => 'Pensionar',
                                    ])
                                    ->default('Salariat'),

                                    Forms\Components\DatePicker::make('loc_munca_info.data_revisal')
                                    ->label('Data Raportului per Salariat'),


                                Forms\Components\DatePicker::make('loc_munca_info.data_cim')
                                    ->label('Data contractului individual de munca'),

                                Forms\Components\Select::make('loc_munca_info.tip_durata_cim')
                                    ->label('Tip durata contractului individual de munca')
                                    ->options([
                                        'Nedeterminata' => 'Nedeterminata',
                                        'Determinata'   => 'Determinata',
                                    ]),

                                    Forms\Components\Select::make('loc_munca_info.tip_norma_cim')
                                    ->label('Tip durata contractului individual de munca')
                                    ->options([
                                        'Norma intreaga' => 'Norma intreaga',
                                        'Norma partiala'   => 'Norma partiala',
                                    ]),

                                Forms\Components\TextInput::make('loc_munca_info.functie'),
                                Forms\Components\TextInput::make('loc_munca_info.data_sfarsit_activitate'),
                                Forms\Components\TextInput::make('loc_munca_info.salariu_baza_lunar_brut')
                                ->numeric()
                                ->suffix(' Lei'),
                                Forms\Components\TextInput::make('loc_munca_info.sporuri_indemnizatii_adaosuri')
                                ->label('Valoare Totala Sporuri, Indemnizatii, Adaosuri')
                                ->numeric()
                                ->suffix(' Lei'),
                            ])
                            ->columns(2)
                            //->itemLabel(fn(array $state): ?string => $state['loc_munca_info'] ?? null)
                            ->deleteAction(
                                fn(Action $action) => $action->requiresConfirmation(),
                            ),
                    ]),

                Forms\Components\TextInput::make('scan_carte_identitate'),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->default(auth()->id())
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nume Complet')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $lowerSearch = strtolower($search);
                        return $query->where(function ($q) use ($lowerSearch) {
                            $q->where('nume', 'like', "%{$lowerSearch}%")
                              ->orWhere('prenume', 'like', "%{$lowerSearch}%");
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('nume', $direction)->orderBy('prenume', $direction);
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('col_cnp')
                    ->label('CNP')
                    ->state(fn(Member $record) => $record->CNP)
                    ->searchable(query: fn(Builder $query, string $search) => $query->where('CNP', 'like', "%{$search}%"))
                    ->sortable(query: fn(Builder $query, string $direction) => $query->orderBy('CNP', $direction))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('col_validitate_cnp')
                    ->label('Validitate CNP')
                    ->state(fn(Member $record) => Cnp::valid($record->CNP) ? 'Valid' : 'Invalid')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Valid' => 'success',
                        'Invalid' => 'danger',
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_outstanding_loans')
                    ->label('Total Imprumuturi Ramase')
                    ->state(function (Member $record): string {
                        $totalOutstanding = 0;
                        foreach ($record->debts as $debt) {
                            $totalOutstanding += $debt->remainingDebt($debt->id)->balance;
                        }
                        return number_format($totalOutstanding, 2) . ' RON';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Telefon')
                    ->state(function (Member $record): ?string {
                        return $record->contact_infos()
                            ->where('tip_info', 'telefon')
                            ->latest('created_at')
                            ->first()
                            ?->info;
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('contact_infos', function ($q) use ($search) {
                            $q->where('tip_info', 'telefon')
                              ->where('info', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            ContactInfo::select('info')
                                ->whereColumn('contact_infos.member_id', 'members.id')
                                ->where('tip_info', 'telefon')
                                ->latest('created_at')
                                ->limit(1),
                            $direction
                        );
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('col_ci')
                    ->label('CI')
                    ->state(fn(Member $record) => $record->CI)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('ci_serie', 'like', "%{$search}%")
                                     ->orWhere('ci_numar', 'like', "%{$search}%");
                    })
                    ->sortable(query: fn(Builder $query, string $direction) => $query->orderBy('ci_serie', $direction)->orderBy('ci_numar', $direction))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('emis_de')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('data_emitere')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('data_expirare')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('loc_nastere')
                    ->label('Loc Naștere')
                    ->state(function (Member $record): ?string {
                        $latestIdentity = $record->previous_identities()
                            ->latest('data_emitere')
                            ->first();
                        
                        if ($latestIdentity) {
                            $parts = array_filter([
                                $latestIdentity->oras_nastere,
                                $latestIdentity->judet_nastere
                            ]);
                            return implode(', ', $parts);
                        }
                        
                        // Fallback to member's own data if no previous identities
                        $parts = array_filter([
                            $record->oras_nastere,
                            $record->judet_nastere
                        ]);
                        return implode(', ', $parts) ?: null;
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('previous_identities', function ($q) use ($search) {
                            $q->where('oras_nastere', 'like', "%{$search}%")
                              ->orWhere('judet_nastere', 'like', "%{$search}%");
                        })
                        ->orWhere('oras_nastere', 'like', "%{$search}%")
                        ->orWhere('judet_nastere', 'like', "%{$search}%");
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cetatenie')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nationalitate')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('domiciliu')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('scan_carte_identitate')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->reorderableColumns('members');
    }

    public static function getRelations(): array
    {
        return [
            //
            MemberResource\RelationManagers\ContactInfosRelationManager::class,
            MemberResource\RelationManagers\DiscutiiTelefoniceRelationManager::class,
            MemberResource\RelationManagers\NoteRelationManager::class,
            MemberResource\RelationManagers\DebtsRelationManager::class,
            DebtResource\RelationManagers\PaymentsRelationManager::class,
            MemberResource\RelationManagers\PreviousIdentitiesRelationManager::class,
            MemberResource\RelationManagers\WorkplacesRelationManager::class,
            MemberResource\RelationManagers\MemberWorkplaceDetailsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'view'   => Pages\ViewMember::route('/{record}'),
            'edit'   => Pages\EditMember::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
