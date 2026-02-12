<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkplaceResource\Pages;
use App\Filament\Resources\WorkplaceResource\RelationManagers;
use App\Models\Workplace;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkplaceResource extends Resource
{
    protected static ?string $model = Workplace::class;
    protected static ?string $navigationGroup = 'Informatii Asociate';
    protected static ?int $navigationSort     = 99;
    protected static ?string $navigationLabel = 'Angajatori';

    protected static ?string $label            = 'Angajator';
    protected static ?string $pluralModelLabel = 'Angajatori';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
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
                Forms\Components\Textarea::make('contact')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('info')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('CUI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reg_com')
                    ->searchable(),
                Tables\Columns\TextColumn::make('adresa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('oras')
                    ->searchable(),
                Tables\Columns\TextColumn::make('judet')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
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
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            WorkplaceResource\RelationManagers\MembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkplaces::route('/'),
            'create' => Pages\CreateWorkplace::route('/create'),
            'view' => Pages\ViewWorkplace::route('/{record}'),
            'edit' => Pages\EditWorkplace::route('/{record}/edit'),
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
