<?php

namespace App\Filament\Resources\WorkplaceResource\Pages;

use App\Filament\Resources\WorkplaceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWorkplace extends ViewRecord
{
    protected static string $resource = WorkplaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
