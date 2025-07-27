<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ControlPrenatal;
use App\Traits\ReportesFpdf;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;

class ExportController extends Fpdf
{
    use ReportesFpdf;
    public function exportControlPrenatal($id): mixed
    {
        $control = ControlPrenatal::find($id);
        if (!$control){
            sweetAlert2([
                'icon' => 'info',
                'text' => 'Control Prenatal No Encontrado',
                'timer' => null,
                'showCloseButton' => true
            ]);
            return redirect()->route('web.index');
        }

        $_SESSION['headerTitle'] = "TARJETA DE CONTROL PRENATAL";
        $_SESSION['footerTitle'] = "TARJETA DE CONTROL PRENATAL";
        $name = "Control_Prenatal";

        $pdf = new ExportController();
        $pdf->SetTitle('viewPDF');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        //$pdf->Cell(0, 7, 'kkkkkk', 1);

        $pdf->Output('I', $name . ".pdf");
        return $pdf;
    }
}
