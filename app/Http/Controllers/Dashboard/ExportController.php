<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ControlPrenatal;
use App\Traits\ReportesFpdf;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExportController extends Fpdf
{
    use ReportesFpdf;

    public function exportControlPrenatal($id): mixed
    {
        $control = ControlPrenatal::find($id);
        if (!$control) {
            sweetAlert2([
                'icon' => 'info',
                'text' => 'Control Prenatal No Encontrado',
                'timer' => null,
                'showCloseButton' => true
            ]);
            return redirect()->route('web.index');
        }

        $this->pacientes_id = $id;
        $_SESSION['headerTitle'] = "TARJETA DE CONTROL PRENATAL";
        $_SESSION['footerTitle'] = "TARJETA DE CONTROL PRENATAL";
        $name = "Control_Prenatal_".$control->codigo;

        $pdf = new ExportController();
        $pdf->SetTitle('viewPDF');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->SetFillColor(250, 152, 135);

        //Datos Básicos
        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(160, 7, verUtf8(Str::upper('Datos Básicos')), 1, 0, 'C', 1);
        $pdf->Cell(0, 7, $this->getCodigo($control), 1, 1, 'C', 1);

        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(18, 7, $this->mostarTitle('Cédula'), 1);
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(31, 7, $this->getCedula($control), 1, 0, 'C');
        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(42, 7, $this->mostarTitle('Nombre y Apellido'), 1);
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(0, 7, $this->getNombre($control), 1, 1);

        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(24, 7, $this->mostarTitle('Fecha Nac'), 1);
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(25, 7, $this->getFechaNac($control), 1, 0, 'C');
        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(13, 7, $this->mostarTitle('Edad'), 1);
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(29, 7, $this->getEdad($control), 1, 0, 'C');
        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(23, 7, $this->mostarTitle('Teléfono'), 1);
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(0, 7, $this->getTelefono($control), 1, 1);

        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(24, 7, $this->mostarTitle('Dirección'), 1);
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(0, 7, $this->getDireccion($control), 1, 1);

        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(10, 7, $this->mostarTitle('FUR'), 1);
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(20, 7, $this->getFUR($control), 1, 0, 'C');
        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(10, 7, $this->mostarTitle('FPP'), 1);
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(20, 7, $this->getFPP($control), 1, 0, 'C');
        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(21, 7, $this->mostarTitle('Gestas'), 1);
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(10, 7, $this->getGestas($control), 1, 0, 'C');
        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(23, 7, $this->mostarTitle('Partos'), 1);
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(10, 7, $this->getPartos($control), 1, 0, 'C');
        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(23, 7, $this->mostarTitle('Cesareas'), 1);
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(10, 7, $this->getCesareas($control), 1, 0, 'C');
        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(23, 7, $this->mostarTitle('Abortos'), 1);
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(10, 7, $this->getAbortos($control), 1, 1, 'C');
        $pdf->Ln(7);

        //Antecedentes
        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(0, 7, verUtf8(Str::upper('Antecedentes')), 1, 1, 'C', 1);

        $y = $pdf->GetY();
        $pdf->Cell(30, 7, $this->mostarTitle('familiares'), 1, 0, 'C', 1);
        $pdf->Cell(10, 7, 'SI', 1, 0, 'C', 1);
        $pdf->Cell(10, 7, 'NO', 1, 0, 'C', 1);
        $x = $pdf->GetX();
        $pdf->Cell(0, 7, '', 0, 1);

        $pdf->SetFont('Times', '', 10);
        $ultima = $y;
        foreach ($this->getAntFamiliares() as $antecedente) {

            if ($antecedente->is_bool){
                $pdf->Cell(30, 7, $this->getAntenecente($antecedente->nombre), 1);
                $pdf->Cell(10, 7, $antecedente->SI, 1, 0, 'C');
                $pdf->Cell(10, 7, $antecedente->NO, 1, 0, 'C');
                $pdf->Cell(0, 7, '', 0, 1);
            }else{
                $pdf->Cell(14, 7, $this->getAntenecente($antecedente->nombre), 1);
                $pdf->Cell(36, 7, $this->getAntenecente($antecedente->otro), 1, 0, 'C');
                $pdf->Cell(0, 7, '', 0, 1);
            }

            $yy = $pdf->GetY();
            if ($yy > $ultima){
                $ultima = $yy;
            }
        }


        $pdf->SetXY($x, $y);

        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(30, 7, $this->mostarTitle('Personales'), 1, 0, 'C', 1);
        $pdf->Cell(10, 7, 'SI', 1, 0, 'C', 1);
        $pdf->Cell(10, 7, 'NO', 1, 0, 'C', 1);
        $x2 = $pdf->GetX();
        $pdf->Cell(0, 7, '', 0, 1);

        $pdf->SetFont('Times', '', 10);
        foreach ($this->getAntPersonales() as $antecedente) {
            $pdf->SetX($x);
            if ($antecedente->is_bool){
                $pdf->Cell(30, 7, $this->getAntenecente($antecedente->nombre), 1);
                $pdf->Cell(10, 7, $antecedente->SI, 1, 0, 'C');
                $pdf->Cell(10, 7, $antecedente->NO, 1, 0, 'C');
                $pdf->Cell(0, 7, '', 0, 1);
            }else{
                $pdf->Cell(14, 7, $this->getAntenecente($antecedente->nombre), 1);
                $pdf->Cell(36, 7, $this->getAntenecente($antecedente->otro), 1, 0, 'C');
                $pdf->Cell(0, 7, '', 0, 1);
            }

            $yy = $pdf->GetY();
            if ($yy > $ultima){
                $ultima = $yy;
            }
        }

        $pdf->SetXY($x2, $y);

        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(45, 7, $this->mostarTitle('otros'), 1, 0, 'C', 1);
        $pdf->Cell(45, 7, '', 1, 1, 'C', 1);

        $pdf->SetFont('Times', '', 10);
        foreach ($this->getAntOtros() as $antecedente) {
            $pdf->SetX($x2);

            $pdf->Cell(45, 7, $this->getAntenecente($antecedente->nombre, 21), 1);
            $pdf->Cell(45, 7, $this->getAntenecente($antecedente->otro ?? $antecedente->SI, 21), 1, 1, 'C');

            $yy = $pdf->GetY();
            if ($yy > $ultima){
                $ultima = $yy;
            }
        }

        $pdf->SetY($ultima);
        $pdf->Cell(0, 7, '', 0, 1);

        //Vacunas
        $pdf->SetFont('Times', 'B', 10);
        $y = $pdf->GetY();
        $pdf->Cell(34, 7, 'VACUNAS', 1, 0, 'C', 1);
        $pdf->Cell(22, 7, '1', 1, 0, 'C', 1);
        $pdf->Cell(22, 7, '2', 1, 0, 'C', 1);
        $pdf->Cell(22, 7, 'REFUERZO', 1, 0, 'C', 1);
        $x = $pdf->GetX();
        $pdf->Cell(0, 7, '', 0, 1);

        $pdf->SetFont('Times', '', 10);

        $ultima = $y;
        foreach ($this->getVacunas() as $vacuna) {

            $pdf->Cell(34, 7, $this->getAntenecente($vacuna->nombre, 14), 1);
            $pdf->Cell(22, 7, $vacuna->dosis_1, 1, 0, 'C');
            $pdf->Cell(22, 7, $vacuna->dosis_2, 1, 0, 'C');
            $pdf->Cell(22, 7, $vacuna->refuerzo, 1, 0, 'C');
            $pdf->Cell(0, 7, '', 0, 1);

            $yy = $pdf->GetY();
            if ($yy > $ultima){
                $ultima = $yy;
            }
        }

        //TIPAJE
        $pdf->SetXY($x, $y);

        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(50, 7, $this->mostarTitle('TIPAJE'), 1, 0, 'C', 1);
        $pdf->Cell(0, 7, '', 0, 1);

        $pdf->SetFont('Times', '', 10);
        $pdf->SetX($x);
        $pdf->Cell(20, 7, $this->mostarTitle('Madre'), 1);
        $pdf->Cell(30, 7, $this->getTipajeMadre($control), 1, 0, 'C');
        $pdf->Cell(0, 7, '', 0, 1);
        $pdf->SetX($x);
        $pdf->Cell(20, 7, $this->mostarTitle('Padre'), 1);
        $pdf->Cell(30, 7, $this->getTipajePadre($control), 1, 0, 'C');
        $pdf->Cell(0, 7, '', 0, 1);
        $pdf->SetX($x);
        $pdf->Cell(30, 7, $this->mostarTitle('Sensibilidad'), 1);
        $pdf->Cell(20, 7, $this->getTipajeSensibilidad($control), 1, 'C');
        $pdf->Cell(0, 7, '', 0, 1);

        $pdf->SetY($ultima);
        $pdf->Ln(7);

        //CONTROL ITEMS
        $pdf->SetFont('Times', 'B', 9);
        $pdf->Cell(27, 7, 'FECHA', 1, 0, 'C', 1);
        $pdf->Cell(22, 7, 'EDAD GEST.', 1, 0, 'C', 1);
        $pdf->Cell(15, 7, 'PESO', 1, 0, 'C', 1);
        $pdf->Cell(15, 7, 'TA', 1, 0, 'C', 1);
        $pdf->Cell(15, 7, 'AU', 1, 0, 'C', 1);
        $pdf->Cell(24, 7, 'PRES', 1, 0, 'C', 1);
        $pdf->Cell(15, 7, 'FCF', 1, 0, 'C', 1);
        $pdf->Cell(27, 7, 'MOV FETALES', 1, 0, 'C', 1);
        $pdf->Cell(15, 7, 'DU', 1, 0, 'C', 1);
        $pdf->Cell(15, 7, 'EDEMA', 1, 1, 'C', 1);

        $pdf->SetFont('Times', '', 9);

        $pdf->Cell(27, 7, '21/02/1989', 1, 0, 'C');
        $pdf->Cell(22, 7, '999.999', 1, 0, 'C');
        $pdf->Cell(15, 7, '999.99', 1, 0, 'C');
        $pdf->Cell(15, 7, '999', 1, 0, 'C');
        $pdf->Cell(15, 7, '999', 1, 0, 'C');
        $pdf->Cell(24, 7, 'TEXTO', 1, 0, 'C');
        $pdf->Cell(15, 7, '999', 1, 0, 'C');
        $pdf->Cell(27, 7, 'SI', 1, 0, 'C');
        $pdf->Cell(15, 7, 'SI', 1, 0, 'C');
        $pdf->Cell(15, 7, 'NO', 1, 1, 'C');

        $pdf->Cell(0, 7, $this->getCodigo($control), 1, 1);
        $pdf->Cell(0, 7, $this->getCodigo($control), 1, 1);

        $pdf->Output('I', $name . ".pdf");
        return $pdf;
    }
}
