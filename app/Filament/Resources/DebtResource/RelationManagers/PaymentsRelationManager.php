<?php

namespace App\Filament\Resources\DebtResource\RelationManagers;

use App\Models\Debt;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payment';

    protected static ?string $navigationLabel = 'Plati la Imprumuturi';

    protected static ?string $label            = 'Plata';
    protected static ?string $pluralModelLabel = 'Plati';

    protected static ?string $title = 'Plati la Imprumuturi';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('act')
                    ->required(),
                Forms\Components\DatePicker::make('data')
                ->required()
                ->label('Data Plata')
                ->displayFormat('d/m/Y')
                ->native(false)
                ->format('d/m/Y')
                ->maxDate(now()),
                Forms\Components\TextInput::make('suma')
                    ->required(),
                Forms\Components\Hidden::make('debt_id')
                    ->default($this->getOwnerRecord()->id),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->user()->id),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('act')
            ->columns([
                Tables\Columns\TextColumn::make('data')

                    ->sortable(),
                Tables\Columns\TextColumn::make('act'),

                Tables\Columns\TextColumn::make('suma')
                    ->suffix(' Lei'),
                // Tables\Columns\TextColumn::make('remainingDebt')
                //     ->numeric()
                //     ->label('Sold ramas')
                //     ->getStateUsing(function ($record) {
                //         $debt = Debt::where('id', $this->getOwnerRecord()->id)->first();
                //         return $debt->remainingDebt($this->getOwnerRecord()->id)->balance . ' LEI';
                //     })
                //     ->alignment(Alignment::End)
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: false),

                // Tables\Columns\TextColumn::make('remainingInterest')
                //     ->numeric()
                //     ->label('Dobanda ramasa')
                //     ->getStateUsing(function ($record) {
                //         $debt = Debt::where('id', $this->getOwnerRecord()->id)->first();

                //         return $debt->remainingDebt($this->getOwnerRecord()->id)->interest . ' LEI';

                //     })
                //     ->alignment(Alignment::End)
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('debt.suma')
                    ->suffix(' Lei')
                    ->label('Suma Imprumutata')
                    ->alignment(Alignment::End)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('debt_id')
                    ->label('Imprumut')
                    ->alignment(Alignment::End)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                ->alignment(Alignment::End)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('data', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
