<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Department;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // If no explicit roles were chosen but a department is set,
        // assign the department's default roles to this user.
        if (
            (! isset($data['roles']) || empty($data['roles']))
            && isset($data['department_id'])
            && $data['department_id']
        ) {
            $department = Department::with('roles')->find($data['department_id']);

            if ($department) {
                $data['roles'] = $department->roles->pluck('id')->all();
            }
        }

        return $data;
    }
}
