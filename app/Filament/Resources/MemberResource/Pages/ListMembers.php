<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Member;
use Bostos\ReorderableColumns\Concerns\HasReorderableColumns;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Konnco\FilamentImport\Actions\ImportAction;
use Konnco\FilamentImport\Actions\ImportField;
use Illuminate\Support\Stringable;

class ListMembers extends ListRecords
{
    use HasReorderableColumns;

    protected static string $resource = MemberResource::class;
    protected static string $view = 'filament.resources.members.pages.list-members';

    protected function getHeaderActions(): array
    {
        ini_set('memory_limit', '2000M');
        ini_set('max_execution_time', '300'); //300 seconds = 5 minutes

        return [
            Actions\CreateAction::make(),
            ImportAction::make()
            ->handleBlankRows(true)
            ->uniqueField('CNP')
                ->fields([
                    ImportField::make('CNP')
                        ->label('Câmp CNP')
                        ->helperText('CNP-urile trebuie sa fie valide')
                        ->required(),
                    ImportField::make('nume_complet')
                        ->label('Câmp Nume Complet')
                        ->required(),
                    ImportField::make('CI')
                        ->label('Câmp CI')
                        ->required(),
                    ImportField::make('emis_de'),
                    ImportField::make('data_emitere'),
                    ImportField::make('data_expirare'),
                    Select::make('cetatenie')
                    ->options([
                        'RO' => 'RO',
                        'Strain' => 'Strain',
                    ])
                    ->default('RO'),
                    Select::make('nationalitate')
                    ->options([
                        'RO' => 'RO',
                        'Strain' => 'Strain',
                    ])
                    ->default('RO'),
                    ImportField::make('domiciliu'),
                    ImportField::make('loc_nastere'),

                    ], columns:2)
                    ->handleRecordCreation(function($data){

                        $name_parts = explode(" ", $data['nume_complet']);

                        $data['nume'] = ucfirst(trim($name_parts[0]));

                        $data['prenume'] = ucfirst(substr($data['nume_complet'], strlen($data['nume']) + 1));

                        $data['ci_serie'] = strtoupper(substr(trim($data['CI']),0,2));
                        $data['ci_numar'] = substr(trim($data['CI']),2);

                        $data['scan_carte_identitate'] = null;
                        $data['user_id'] = auth()->id();

                        // Handle validation TODO

                        return Member::create($data);
                    })
        ];
    }
}
