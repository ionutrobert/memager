<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactInfosRelationManager extends RelationManager
{
    protected static string $relationship = 'contact_infos';

    protected static ?string $navigationLabel = 'Contact';

    protected static ?string $label            = 'Informatie Contact';
    protected static ?string $pluralModelLabel = 'Informatii de Contact';

    protected static ?string $title = 'Informatii de Contact';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tip_info')
                    ->options([
                        'Telefon'      => 'Telefon',
                        'Email'        => 'Email',
                        'Fax'          => 'Fax',
                        'Adresa'       => 'Adresa Corespondenta',
                        'Social Media' => 'Link retea Social Media',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('info')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tip_info')
            ->columns([
                Tables\Columns\TextColumn::make('tip_info')
                    ->searchable()
                    ->sortable()
                    ->label('Tip Contact'),
                Tables\Columns\TextColumn::make('info')
                    ->searchable()
                    ->label('Informatie'),
                Tables\Columns\TextColumn::make('created_at')
                    ->visibleFrom('md')
                    ->since()
                    ->sortable()
                    ->label('Data Crearii')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->visibleFrom('md')
                    ->since()
                    ->sortable()
                    ->label('Data Actializarii')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->visibleFrom('md')
                    ->sortable()
                    ->label('Utilizator')
                    ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->filters([
                //
                SelectFilter::make('tip_info')
                    ->label('Tip Informatie')
                    ->multiple()
                    ->options([
                        'Telefon'      => 'Telefon',
                        'Email'        => 'Email',
                        'Fax'          => 'Fax',
                        'Adresa'       => 'Adresa Corespondenta',
                        'Social Media' => 'Link retea Social Media',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
                    ->successNotificationTitle('Informatie contact creata cu succes!'),
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
