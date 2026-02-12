<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkplacesRelationManager extends RelationManager
{
    protected static string $relationship = 'workplaces';

    protected static ?string $navigationLabel = 'Locuri de Munca';

    protected static ?string $label            = 'Loc de Munca';
    protected static ?string $pluralModelLabel = 'Locuri de Munca';

    protected static ?string $title = 'Loc de Munca';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employer')
                    ->required(),
                Forms\Components\TextInput::make('CUI'),
                Forms\Components\TextInput::make('reg_com'),

                Forms\Components\TextInput::make('adresa')
                    ->required(),
                Forms\Components\TextInput::make('oras')
                    ->required(),
                Forms\Components\TextInput::make('judet')
                    ->required(),
                //REPEATER CONTACT and INFO
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('employer')
            ->columns([
                Tables\Columns\TextColumn::make('employer'),
                Tables\Columns\TextColumn::make('CUI'),
                Tables\Columns\TextColumn::make('reg_com'),
                Tables\Columns\TextColumn::make('adresa'),
                Tables\Columns\TextColumn::make('oras'),
                Tables\Columns\TextColumn::make('judet'),
                Tables\Columns\TextColumn::make('contact.tel'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date(),
            ])
            ->persistSortInSession()
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn(AttachAction $action): array=> [
                        $action->getRecordSelect(),
                        //Forms\Components\TextInput::make('workplace')->required(),
                    ]),
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
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
