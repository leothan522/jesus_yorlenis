<?php

namespace App\Filament\Resources\ControlprenatalResource\Pages;

use App\Filament\Resources\ControlPrenatalResource;
use App\Models\Parametro;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateControlPrenatal extends CreateRecord
{
    protected static string $resource = ControlPrenatalResource::class;
    protected static bool $canCreateAnother = false;

    protected function afterCreate(): void
    {
        // Runs after the form fields are saved to the database.
        $parametro = Parametro::where('nombre', 'codigo_control_prenatal')->first();
        if ($parametro){
            $parametro->valor_id = ++$parametro->valor_id;
            $parametro->save();
        }else{
            $parametro = new Parametro();
            $parametro->nombre = 'codigo_control_prenatal';
            $parametro->valor_id = 2;
            $parametro->save();
        }
    }
}
