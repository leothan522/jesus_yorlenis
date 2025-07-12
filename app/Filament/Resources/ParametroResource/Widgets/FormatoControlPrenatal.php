<?php

namespace App\Filament\Resources\ParametroResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FormatoControlPrenatal extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('size_codigo', 'Valor id = Int')
                ->description('Valor texto = null'),
            Stat::make('codigo_control_prenatal', 'Valor id = Int')
                ->description('Valor texto = string'),
        ];
    }

    protected function getHeading(): ?string
    {
        return 'Parametros Iniciales';
    }
}
