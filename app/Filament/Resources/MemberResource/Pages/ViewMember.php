<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Member;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMember extends ViewRecord
{
    protected static string $resource = MemberResource::class;

    public function getTitle(): string
    {
        $record = $this->getRecord();

        // Get full name from latest previous identity (ID card) or fallback to member's name
        $latestIdentity = $record->previous_identities()
            ->latest('data_emitere')
            ->first();

        if ($latestIdentity) {
            return $latestIdentity->full_name;
        }

        return $record->full_name ?? 'Membru';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
