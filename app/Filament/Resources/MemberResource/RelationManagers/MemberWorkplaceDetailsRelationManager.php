<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use App\Models\Workplace;
use Carbon\Carbon;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MemberWorkplaceDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'member_workplace_details';

    protected static ?string $navigationLabel = 'Detalii Loc de Munca';

    protected static ?string $label            = 'Detalii Loc de Munca';
    protected static ?string $pluralModelLabel = 'Detalii Loc de Munca';

    protected static ?string $title = 'Detalii Loc de Munca';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([



                DatePicker::make('data_informatie')
                    ->required()
                    ->label('Data Adeverinta / Revisal')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->format('d/m/Y')
                    ->maxDate(now()),

                //dd($this->getOwnerRecord()->id),
                Select::make('workplace_id')
                    ->options(Workplace::whereHas('members', function ($q) {
                        $q->where('member_id', $this->getOwnerRecord()?->id);
                    })

                            ->pluck('employer', 'id')
                )
                            ->default(
                                Workplace::whereHas('members', function ($q) {
                                    $q->where('member_id', $this->getOwnerRecord()?->id);
                                })
                                ->pluck('id', 'id')
                                ->last()
                            ),



                Hidden::make('user_id')
                    ->default(auth()->user()->id),
                Select::make('tip_informatie')
                    ->options([
                        'Revisal'    => 'Revisal',
                        'Adeverinta' => 'Adeverinta',
                    ])
                    ->live()
                    ->afterStateUpdated(fn(Select $component) => $component
                            ->getContainer()
                            ->getComponent('dynamicTypeFields')
                            ->getChildComponentContainer()
                            ->fill()),

                Grid::make(2)
                    ->schema(fn(Get $get): array=> match ($get('tip_informatie')) {
                        'Revisal'                   => [
                            Select::make('tip_durata_cim')
                                ->options([
                                    'Nedeterminata' => 'Nedeterminata',
                                    'Determinata'   => 'Determinata',
                                ])
                                ->live(),

                                Select::make('tip_norma_cim')
                                ->options([
                                    'Norma Intreaga' => 'Norma Intreaga',
                                    'Norma Partiala'   => 'Norma Partiala',
                                ]),
                            TextInput::make('functie'),

                            TextInput::make('salariu_de_baza_lunar_brut')
                                ->numeric()
                                ->suffix(' Lei'),
                            TextInput::make('sporuri_indemnizatii_adaosuri')
                                ->suffix(' Lei')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if (str_contains($state, '+')) {
                                        $set('sporuri_indemnizatii_adaosuri', array_sum(explode('+', trim($state))));
                                    }

                                    if (str_contains($state, '-')) {
                                        $set('sporuri_indemnizatii_adaosuri', array_diff(explode('-', trim($state))));
                                    }

                                }),

                            DatePicker::make('data_incepere_cim')
                                ->required()
                                ->format('d/m/Y')
                                ->displayFormat('d/m/Y'),
                            DatePicker::make('data_incetare_cim')
                                ->visible(fn(Get $get) => $get('tip_durata_cim') == 'Determinata')
                                ->format('d/m/Y')
                                ->displayFormat('d/m/Y')
                                ->required(),
                        ],

                        'Adeverinta'                => [
                            TextInput::make('hourly_rate')
                                ->numeric()
                                ->required()
                                ->prefix('€'),
                            FileUpload::make('contract')
                                ->required(),
                        ],
                        default                     => [],
                    })
                    ->key('dynamicTypeFields')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('data_informatie')
            ->columns([
                Tables\Columns\TextColumn::make('data_informatie'),
                Tables\Columns\TextColumn::make('workplace.employer')
                    ->label('Loc de munca'),
                Tables\Columns\TextColumn::make('tip_informatie')
                    ->label('Tip'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
