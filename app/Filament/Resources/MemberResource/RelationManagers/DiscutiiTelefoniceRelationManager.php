<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use App\Models\ContactInfo;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DiscutiiTelefoniceRelationManager extends RelationManager
{
    protected static string $relationship = 'discutii_telefonice';

    protected static ?string $navigationLabel = 'Discutii Telefonice';

    protected static ?string $label            = 'Discutie Telefonica';
    protected static ?string $pluralModelLabel = 'Discutii Telefonice';

    protected static ?string $title = 'Discutii Telefonice';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('participant_discutie')
                    ->required()
                    ->label('Participant Discutie')
                    ->default('member.full_name'),

                Select::make('contact_info_id')
                    ->label('Numar Telefon')
                    ->native(false)
                    ->options([

                        null => 'Selecteaza',

                        'Telefoane Asociate' => ContactInfo::all()
                            ->where('member_id', $this->getOwnerRecord()?->id)
                            ->where('tip_info', 'Telefon')
                            ->pluck('info', 'id')
                            ->toArray(),
            ]),

                RichEditor::make('rezumat'),

                DatePicker::make('data_discutie')
                    ->default(now()),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('participant_discutie')
            ->columns([
                Tables\Columns\TextColumn::make('data_discutie'),
                Tables\Columns\TextColumn::make('participant_discutie'),
                Tables\Columns\TextColumn::make('member.full_name'),
                Tables\Columns\TextColumn::make('contact_info.info'),
            ])
            ->defaultSort('data_discutie', 'desc')
            ->persistSortInSession()
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
                    ->successNotificationTitle('Discutie telefonica creata cu succes!'),
            ])
            ->actions([
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
