<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;
    protected array $assignedToPivotData = [];
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->assignedToPivotData = $data['assigned_to'] ?? [];
        unset($data['assigned_to']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->assignedTo()->sync([]);

        foreach ($this->assignedToPivotData as $item) {
            $this->record->assignedTo()->attach($item['id'], [
                'role' => $item['pivot']['role'] ?? null,
                'joined_at' => $item['pivot']['joined_at'] ?? now(),
                'is_active' => $item['pivot']['is_active'] ?? false,
            ]);
        }
    }
}
