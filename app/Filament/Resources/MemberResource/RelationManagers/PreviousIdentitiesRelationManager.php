<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Fieldset;

class PreviousIdentitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'previous_identities';
    protected static string $model = 'PreviousIdentity';

    protected static ?string $navigationLabel = 'CI Anterioare';

    protected static ?string $label            = 'CI Anterior';
    protected static ?string $pluralModelLabel = 'CI Anterioare';

    protected static ?string $title = 'Carti Identitate Anterioare';

    public function infolist(Infolist $infolist): Infolist
    {


        return $infolist
            ->schema([

                Fieldset::make('Carte de Identitate')
                ->schema([

                    TextEntry::make('full_name'),
                    TextEntry::make('CI')
                        ->label('Carte identitate'),
                    TextEntry::make('data_emitere')
                        ->date(),
                    TextEntry::make('data_expirare')
                        ->date(),

                    TextEntry::make('CIValid')
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
                    Fieldset::make('Adresa Actuala')
                        ->schema([
                            TextEntry::make('domiciliu'),
                            TextEntry::make('oras'),
                            TextEntry::make('judet'),

                        ])->columns(3),

                    Fieldset::make('Loc Nastere')
                        ->schema([
                            TextEntry::make('oras_nastere'),
                            TextEntry::make('judet_nastere'),

                        ])->columns(2),

                ])->columns(2),


            ]);

    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ci_serie')
                    ->required(),
                Forms\Components\TextInput::make('ci_numar')
                    ->required(),
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
                Forms\Components\TextInput::make('oras'),
                Forms\Components\TextInput::make('judet'),
                Forms\Components\TextInput::make('domiciliu'),




                Forms\Components\TextInput::make('user_id')
                    ->hidden()
                    ->required()
                    ->default(auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('data_emitere')
            ->columns([
                Tables\Columns\TextColumn::make('data_emitere')
                ->date(),
                Tables\Columns\TextColumn::make('ci')
                ->label('S/N CI'),
                Tables\Columns\TextColumn::make('full_name')
                ->label('Nume'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
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
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
