<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Konnco\FilamentImport\Actions\ImportAction;
use Konnco\FilamentImport\Actions\ImportField;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
            ->uniqueField('email')
                ->fields([
                    ImportField::make('name')
                        ->label('Câmp Nume')
                        ->required(),
                    ImportField::make('email')
                        ->label('Câmp Email')
                        ->required(),
                    ImportField::make('password')
                        ->label('Câmp Parola')
                        ->mutateBeforeCreate(fn ($value) =>
                            bcrypt($value)
                        )
                        ->required(),
                    Select::make('roles')
                        ->relationship('roles', 'name')
                        ->label('Selectare Roluri')

                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->required(),
                    ])

// https://github.com/konnco/filament-import?tab=readme-ov-file#usage


        ];
    }
}
