<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DebtResource\Pages;
use App\Livewire\ViewImprumut;
use App\Models\Debt;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DebtResource extends Resource
{
    protected static ?string $model = Debt::class;

    protected static ?string $navigationGroup = 'Baza Date';

    protected static ?string $modelLabel       = 'Împrumut';
    protected static ?string $pluralModelLabel = 'Împrumuturi';

    protected static ?string $navigationLabel = 'Împrumuturi';
    protected static ?int $navigationSort     = 2;
    protected static ?string $navigationIcon  = 'heroicon-o-credit-card';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                Livewire::make(ViewImprumut::class, ['id' => 'member_id'])
                    ->columnSpanFull(),
            ]);

    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('member_id')
                    ->label('Membru')
                    ->options(Member::all()->pluck('full_name', 'id'))
                    ->searchable(['full_name', 'CNP'])
                    ->required(),

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

    public static function table(Table $table): Table
    {

        return $table

            ->columns([
                Tables\Columns\TextColumn::make('member.full_name')
                    ->label('Membru')
                    ->sortable('members.full_name')
                    ->searchable(['members.nume', 'members.prenume']),

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

                TextColumn::make('payment_count')
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

                // ...\EightyNine\Approvals\Tables\Columns\ApprovalStatusColumn::make("approvalStatus.status")->label('Aprobare'),

            ])
            ->persistSortInSession()
            ->searchPlaceholder('Cautare (ID, Nume etc)')

            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                // ApprovalActions::make(
                //     Action::make('Approved')
                //  ),
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
            DebtResource\RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDebts::route('/'),
            'create' => Pages\CreateDebt::route('/create'),
            'edit'   => Pages\EditDebt::route('/{record}/edit'),

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
