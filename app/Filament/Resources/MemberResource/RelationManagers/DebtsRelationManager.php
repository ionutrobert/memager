<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use App\Models\Debt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;

class DebtsRelationManager extends RelationManager
{
    protected static string $relationship = 'debts';


    protected static ?string $navigationLabel = 'Imprumuturi';

    protected static ?string $label            = 'Imprumut';
    protected static ?string $pluralModelLabel = 'Imprumuturi';

    protected static ?string $title = 'Imprumuturi';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                ->default(auth()->user()->id),
            Forms\Components\DatePicker::make('data_acordare')
            ->required()
            ->label('Data Plata')
            ->displayFormat('d/m/Y')
            ->native(false)
            ->format('d/m/Y')
            ->maxDate(now()),
            Forms\Components\TextInput::make('suma')
                ->label('Suma împrumutată')
                ->required()
                ->numeric()
                ->suffix(' Lei'),
            Forms\Components\TextInput::make('procent')
                ->label('Procent dobândă')
                ->required()
                ->numeric()
                ->suffix(' %'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('suma')
            ->columns([
                Tables\Columns\TextColumn::make('data_acordare')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('suma')
                    ->numeric()
                    ->money('Lei')
                    ->alignment(Alignment::End)
                    ->sortable(),

                Tables\Columns\TextColumn::make('procent')
                    ->numeric()
                    ->suffix(' %')
                    ->alignment(Alignment::End)
                    ->sortable(),

                Tables\Columns\TextColumn::make('remainingDebt')
                    ->numeric()
                    ->label('Sold ramas')
                    ->getStateUsing(function ($record) {
                        $debt = Debt::where('id', $record->id)->first();
                        return $debt->remainingDebt($record->id)->balance . ' LEI';
                    })
                    ->alignment(Alignment::End)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('remainingInterest')
                    ->numeric()
                    ->label('Dobanda ramasa')
                    ->getStateUsing(function ($record) {
                        $debt = Debt::where('id', $record->id)->first();
                        return $debt->remainingDebt($record->id)->interest . ' LEI';
                    })
                    ->alignment(Alignment::End)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('payment_count')
                    ->counts('payment')
                    ->alignment(Alignment::End)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->alignment(Alignment::End)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->alignment(Alignment::End)
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
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
               // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
