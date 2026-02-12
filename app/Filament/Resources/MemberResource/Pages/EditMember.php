<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Member;
use App\Models\PreviousIdentity;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMember extends EditRecord
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function beforeSave(): void
    {

        // Handles Insert into Prev Ident
        // Needs validation

        $record = $this->record;
        // $data = $this->data;
        $array =$record->toArray();
        $array['member_id'] = $record->id;

        $previousIdentity = new PreviousIdentity();


        $previousIdentity::create($array);

    }
}
