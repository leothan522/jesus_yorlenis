<?php

namespace App\Filament\Resources\ControlPrenatalResource\Pages;

use App\Filament\Resources\ControlPrenatalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListControlPrenatal extends ListRecords
{
    protected static string $resource = ControlPrenatalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
