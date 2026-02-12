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
                // CNP Section with validation badge
                Section::make(__('lang.cnp.section_title'))
                    ->schema([
                        Infolists\Components\TextEntry::make('CNP')
                            ->label(__('lang.cnp.label'))
                            ->inlineLabel()
                            ->icon(fn(Member $record) => CNP::valid($record->CNP) ? 'heroicon-o-shield-check' : 'heroicon-o-shield-exclamation')
                            ->iconColor(fn(Member $record) => CNP::valid($record->CNP) ? 'success' : 'danger'),
Infolists\Components\TextEntry::make('CIValid')
                            ->label(__('lang.identity.ci_status'))
                            ->inlineLabel()
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'Valid' => 'success',
                                'Expirat' => 'danger',
                            })
                            ->default(fn(Member $record) => Carbon::createFromDate($record->data_expirare)->gt(Carbon::today()) ? 'Valid' : 'Expirat'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Identity Card Section
                Section::make(__('lang.identity.card_title'))
                    ->schema([
                        Infolists\Components\Fieldset::make(__('lang.identity.latest_id_card'))
                            ->schema([
                        Infolists\Components\TextEntry::make('latest_identity_name')
                            ->label(__('lang.identity.full_name'))
                            ->state(fn(Member $record) => $record->previous_identities()->latest('data_emitere')->first()?->full_name ?? $record->full_name),

                        Infolists\Components\TextEntry::make('CI')
                            ->label(__('lang.identity.ci_number')),

                        Infolists\Components\TextEntry::make('data_emitere')
                            ->label(__('lang.identity.issue_date'))
                            ->date(),

                        Infolists\Components\TextEntry::make('data_expirare')
                            ->label(__('lang.identity.expiry_date'))
                            ->date(),
                            ]),


                        // Current Address
                        Infolists\Components\Fieldset::make(__('lang.identity.current_address'))
                            ->schema([
                                Infolists\Components\TextEntry::make('domiciliu')
                                    ->label(__('lang.identity.domicile'))
                                    ->hidden(fn(Member $record) => empty($record->domiciliu)),
                                Infolists\Components\TextEntry::make('oras')
                                    ->label(__('lang.identity.city'))
                                    ->hidden(fn(Member $record) => empty($record->oras)),
                                Infolists\Components\TextEntry::make('judet')
                                    ->label(__('lang.identity.county'))
                                    ->hidden(fn(Member $record) => empty($record->judet)),
                            ])
                            ->columns(3)
                            ->hidden(fn(Member $record) => empty($record->domiciliu) && empty($record->oras) && empty($record->judet)),

                        // Birth Place
                        Infolists\Components\Fieldset::make(__('lang.identity.birth_place'))
                            ->schema([
                                Infolists\Components\TextEntry::make('oras_nastere')
                                    ->label(__('lang.identity.birth_city'))
                                    ->hidden(fn(Member $record) => empty($record->oras_nastere)),
                                Infolists\Components\TextEntry::make('judet_nastere')
                                    ->label(__('lang.identity.birth_county'))
                                    ->hidden(fn(Member $record) => empty($record->judet_nastere)),
                            ])
                            ->columns(2)
                            ->hidden(fn(Member $record) => empty($record->oras_nastere) && empty($record->judet_nastere)),
                    ])
                    ->columns(2)
                    ->columnSpan(2),

                // Contact Information Section
                Section::make(__('lang.contact.section_title'))
                    ->schema([
                        Infolists\Components\TextEntry::make('contact_phone')
                            ->label(__('lang.contact.phone'))
                            ->state(fn(Member $record) => $record->contact_infos()->where('tip_info', 'telefon')->latest('created_at')->first()?->info)
                            ->tooltip(fn(Member $record) => $record->contact_infos()->where('tip_info', 'telefon')->latest('created_at')->first()?->created_at?->format('d.m.Y'))
                            ->hidden(fn(Member $record) => empty($record->contact_infos()->where('tip_info', 'telefon')->latest('created_at')->first()?->info)),

                        Infolists\Components\TextEntry::make('contact_email')
                            ->label(__('lang.contact.email'))
                            ->state(fn(Member $record) => $record->contact_infos()->where('tip_info', 'email')->latest('created_at')->first()?->info)
                            ->tooltip(fn(Member $record) => $record->contact_infos()->where('tip_info', 'email')->latest('created_at')->first()?->created_at?->format('d.m.Y'))
                            ->hidden(fn(Member $record) => empty($record->contact_infos()->where('tip_info', 'email')->latest('created_at')->first()?->info)),

                        Infolists\Components\TextEntry::make('contact_address')
                            ->label(__('lang.contact.address'))
                            ->state(fn(Member $record) => $record->contact_infos()->where('tip_info', 'adresa_corespondenta')->latest('created_at')->first()?->info)
                            ->tooltip(fn(Member $record) => $record->contact_infos()->where('tip_info', 'adresa_corespondenta')->latest('created_at')->first()?->created_at?->format('d.m.Y'))
                            ->hidden(fn(Member $record) => empty($record->contact_infos()->where('tip_info', 'adresa_corespondenta')->latest('created_at')->first()?->info)),
                    ])
                    ->columns(1)
                    ->columnSpan(1)
                    ->hidden(fn(Member $record) => $record->contact_infos()->count() === 0),

                // Employment Information Section
                Section::make(__('lang.employment.section_title'))
                    ->schema([
                        Infolists\Components\TextEntry::make('workplace_employer')
                            ->label(__('lang.employment.employer'))
                            ->state(fn(Member $record) => $record->workplaces()->latest('member_workplace.updated_at')->first()?->employer)
                            ->hidden(fn(Member $record) => empty($record->workplaces()->latest('member_workplace.updated_at')->first()?->employer)),

                        Infolists\Components\TextEntry::make('workplace_contact')
                            ->label(__('lang.employment.contact'))
                            ->state(function (Member $record) {
                                $contact = $record->workplaces()->latest('member_workplace.updated_at')->first()?->contact;
                                if (empty($contact)) {
                                    return null;
                                }
                                // Try to parse JSON if it looks like JSON
                                if (str_starts_with($contact, '{') && str_ends_with($contact, '}')) {
                                    $decoded = json_decode($contact, true);
                                    if (json_last_error() === JSON_ERROR_NONE && isset($decoded['contact'])) {
                                        return $decoded['contact'];
                                    }
                                }
                                return $contact;
                            })
                            ->hidden(fn(Member $record) => empty($record->workplaces()->latest('member_workplace.updated_at')->first()?->contact)),

                        Infolists\Components\TextEntry::make('workplace_since')
                            ->label(__('lang.employment.since'))
                            ->state(fn(Member $record) => $record->workplaces()->latest('member_workplace.updated_at')->first()?->pivot?->data_cim)
                            ->date()
                            ->hidden(fn(Member $record) => empty($record->workplaces()->latest('member_workplace.updated_at')->first()?->pivot?->data_cim)),
                    ])
                    ->columns(1)
                    ->columnSpan(1)
                    ->hidden(fn(Member $record) => $record->workplaces()->count() === 0),

                // Membership Info Section
                Section::make(__('lang.membership.section_title'))
                    ->schema([
                        Infolists\Components\TextEntry::make('member_joined_date')
                            ->label(__('lang.membership.joined_date'))
                            ->state(fn(Member $record) => $record->created_at)
                            ->since(),

                        Infolists\Components\TextEntry::make('last_payment_date')
                            ->label(__('lang.membership.last_payment'))
                            ->state(fn(Member $record) => $record->debts()->with('payment')->get()->flatMap(fn($debt) => $debt->payment)->sortByDesc('data')->first()?->data)
                            ->since()
                            ->hidden(fn(Member $record) => empty($record->debts()->with('payment')->get()->flatMap(fn($debt) => $debt->payment)->sortByDesc('data')->first()?->data)),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Loans Section
                Section::make(__('lang.loans.section_title'))
                    ->hidden(fn(Member $record) => $record->debts()->count() == 0)
                    ->schema([
                        Livewire::make(ViewImprumut::class)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

            ])
            ->columns(3);
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
