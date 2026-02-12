<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;

    // Override the default create method to save relationship data

    // protected function handleRecordCreation(array $data): Model
    // {
    //     $record =  static::getModel()::create($data);
    //     $record->detail()->create($data['detail']);

    //     return $record;
    // }
}
