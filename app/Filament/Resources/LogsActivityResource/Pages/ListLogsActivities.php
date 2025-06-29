<?php

namespace App\Filament\Resources\LogsActivityResource\Pages;

use App\Filament\Resources\LogsActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLogsActivities extends ListRecords
{
    protected static string $resource = LogsActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
