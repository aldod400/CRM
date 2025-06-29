<?php

namespace App\Filament\Resources\LogsActivityResource\Pages;

use App\Filament\Resources\LogsActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLogsActivity extends EditRecord
{
    protected static string $resource = LogsActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
