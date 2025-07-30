<?php

namespace App\Traits;

use App\Models\AntecedentesFamiliar;
use App\Models\AntecedentesOtro;
use App\Models\AntecedentesPersonal;
use App\Models\ControlPrenatalItem;
use App\Models\PacienteAntFamiliar;
use App\Models\PacienteAntOtro;
use App\Models\PacienteAntPersonal;
use App\Models\PacienteVacuna;
use App\Models\Parametro;
use App\Models\Vacuna;
use Carbon\Carbon;
use Illuminate\Support\Str;
use function Pest\Laravel\get;

trait ReportesFpdf
{
    public $pacientes_id;

    // Cabecera de página
    function Header(): void
    {
        // Logo
        $this->Image(asset('img/logo_yorlenis_mini.png'), 10, 0);
        $this->Image(qrCodeGenerateFPDF($_SESSION['headerQR']), 170, 5, 30, 30);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        $this->SetY(12);
        $this->Cell(0, 7, verUtf8('DRA.YORLENIS UZCÁTEGUI'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, verUtf8('GINECOLOGO-OBSTETRA-ULA'), 0, 1, 'C');
        $this->Cell(0, 5, verUtf8('MPPS 107368-  CM3468-  CI: 15.031.268'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 7);

        $this->Cell(0, 5, verUtf8('PLANIFICACIÓN FAMILIAR, CONTROL PRENATAL, DOPPLER, ATENCIÓN DEL PARTO Y'), 0, 1, 'C');
        $this->Cell(0, 5, verUtf8('CESAREA, BIOPSIA, MENOPAUSIA, CIRUGIA GINECOLOGICA.'), 0, 1, 'C');
        $x = $this->GetX();
        $y = $this->GetY();
        $this->Line($x, $y, 200, $y);
        $this->Ln(2);
        // Título
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, verUtf8($_SESSION['headerTitle']), 0, 1, 'C');
        // Salto de línea
        $this->Ln(2);
    }

    // Pie de página
    function Footer(): void
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'BI', 10);
        $this->SetTextColor(0);
        //footer
        //$this->Cell(160, 10, verUtf8(env('APP_NAME', 'Morros Devops') . ' - ' . $_SESSION['footerTitle']));
        $this->Cell(160, 10,  $this->getMensjeFooter());
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, verUtf8('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    protected function mostarTitle($texto): string
    {
        return verUtf8(Str::upper($texto . ':'));
    }

    protected function getCodigo($control): string
    {
        return verUtf8(Str::limit(Str::upper($control->codigo), 12, preserveWords: true));
    }

    protected function getCedula($control): string
    {
        $cedula = $control->paciente->cedula;
        if (is_numeric($cedula)) {
            $cedula = formatoMillares($cedula, 0);
        }
        return verUtf8(Str::limit(Str::upper($cedula), 12));
    }

    protected function getNombre($control): string
    {
        return verUtf8(Str::limit(Str::upper($control->paciente->nombre), 43));
    }

    protected function getFechaNac($control): string
    {
        $fecha = '';
        if (!empty($control->paciente->fecha_nacimiento)) {
            $fecha = getFecha($control->paciente->fecha_nacimiento);
        }
        return Str::limit(Str::upper($fecha), 12);
    }

    protected function getEdad($control): string
    {
        $edad = $control->paciente->edad;
        if (!empty($control->paciente->fecha_nacimiento)) {
            $edad = Carbon::parse($control->paciente->fecha_nacimiento)->age;
        }
        return Str::limit(Str::upper(formatoMillares($edad, 0)), 12);
    }

    protected function getTelefono($control): string
    {
        return verUtf8(Str::limit(Str::upper($control->paciente->telefono ?? ''), 30));
    }

    protected function getDireccion($control): string
    {
        return verUtf8(Str::limit(Str::upper($control->paciente->direccion ?? ''), 73));
    }

    protected function getFUR($control): string
    {
        $fecha = '';
        if (!empty($control->paciente->fur)) {
            $fecha = getFecha($control->paciente->fur);
        }
        return Str::limit(Str::upper($fecha), 12);
    }

    protected function getFPP($control): string
    {
        $fecha = '';
        if (!empty($control->paciente->fpp)) {
            $fecha = getFecha($control->paciente->fpp);
        }
        return Str::limit(Str::upper($fecha), 12);
    }

    protected function getGestas($control): string
    {
        $numero = '';
        if (!empty($control->paciente->gestas)) {
            $numero = $this->setNumero($control->paciente->gestas);
        }
        return $numero;
    }

    protected function getPartos($control): string
    {
        $numero = '';
        if (!empty($control->paciente->partos)) {
            $numero = $this->setNumero($control->paciente->partos);
        }
        return $numero;
    }

    protected function getCesareas($control): string
    {
        $numero = '';
        if (!empty($control->paciente->cesareas)) {
            $numero = $this->setNumero($control->paciente->cesareas);
        }
        return $numero;
    }

    protected function getAbortos($control): string
    {
        $numero = '';
        if (!empty($control->paciente->abortos)) {
            $numero = $this->setNumero($control->paciente->abortos);
        }
        return $numero;
    }

    protected function setNumero($numero): string
    {
        return '' . cerosIzquierda(formatoMillares($numero, 0));
    }

    protected function getAntFamiliares()
    {
        $antecedentes = AntecedentesFamiliar::all();
        $antecedentes->each(function ($antecedente) {
            $existe = PacienteAntFamiliar::where('pacientes_id', $this->pacientes_id)
                ->where('antecedentes_id', $antecedente->id)
                ->first();
            if ($existe) {
                $antecedente->SI = 'X';
                $antecedente->NO = '';
                $antecedente->otro = $existe->texto ?? '';
            } else {
                $antecedente->SI = '';
                $antecedente->NO = 'X';
                $antecedente->otro = '';
            }
        });
        return $antecedentes;
    }

    protected function getAntPersonales()
    {
        $antecedentes = AntecedentesPersonal::all();
        $antecedentes->each(function ($antecedente) {
            $existe = PacienteAntPersonal::where('pacientes_id', $this->pacientes_id)
                ->where('antecedentes_id', $antecedente->id)
                ->first();
            if ($existe) {
                $antecedente->SI = 'X';
                $antecedente->NO = '';
                $antecedente->otro = $existe->texto ?? '';
            } else {
                $antecedente->SI = '';
                $antecedente->NO = 'X';
                $antecedente->otro = '';
            }
        });
        return $antecedentes;
    }

    protected function getAntOtros()
    {
        $antecedentes = AntecedentesOtro::all();
        $antecedentes->each(function ($antecedente) {
            $existe = PacienteAntOtro::where('pacientes_id', $this->pacientes_id)
                ->where('antecedentes_id', $antecedente->id)
                ->first();
            if ($existe) {
                $antecedente->SI = 'X';
                $antecedente->NO = '';
                $antecedente->otro = $existe->texto ?? '';
            } else {
                $antecedente->SI = '';
                $antecedente->NO = 'X';
                $antecedente->otro = '';
            }
        });
        return $antecedentes;
    }

    protected function getVacunas()
    {
        $vacunas = Vacuna::all();
        $vacunas->each(function ($vacuna) {
            $existe = PacienteVacuna::where('pacientes_id', $this->pacientes_id)
                ->where('vacunas_id', $vacuna->id)->first();
            if ($existe) {
                $vacuna->dosis_1 = $existe->dosis_1 ? getFecha($existe->dosis_1) : '';
                $vacuna->dosis_2 = $existe->dosis_2 ? getFecha($existe->dosis_2) : '';
                $vacuna->refuerzo = $existe->refuerzo ? getFecha($existe->refuerzo) : '';
            } else {
                $vacuna->dosis_1 = '';
                $vacuna->dosis_2 = '';
                $vacuna->refuerzo = '';
            }
        });
        return $vacunas;
    }

    protected function getAntenecente($antecedente, $limit = null): string
    {
        return verUtf8(Str::limit(Str::upper($antecedente), $limit ?? 12));
    }

    protected function getTipajeMadre($control): string
    {
        return verUtf8(Str::limit(Str::upper($control->paciente->tipaje->madre ?? ''), 12));
    }

    protected function getTipajePadre($control): string
    {
        return verUtf8(Str::limit(Str::upper($control->paciente->tipaje->padre ?? ''), 12));
    }

    protected function getTipajeSensibilidad($control): string
    {
        return verUtf8(Str::limit(Str::upper($control->paciente->tipaje->sensibilidad ?? ''), 9));
    }

    protected function getControlItems($control)
    {
        return ControlPrenatalItem::where('control_id', $control->id)->get();
    }

    protected function getItemFecha($fecha): string
    {
        return getFecha($fecha);
    }

    protected function getitemNumero($numero, $decimales = 0): string
    {
        return Str::padLeft(formatoMillares($numero, $decimales), 10);
    }

    protected function getItemTexto($texto, $limit = 10): string
    {
        return verUtf8(Str::limit(Str::upper($texto), $limit));
    }

    protected function getItemBool($bool): string
    {
        $response = 'NO';
        if ($bool){
            $response = 'SI';
        }
        return $response;
    }

    protected function getMensajeFinal(): string
    {
        $texto = 'GRACIAS POR CONFIAR SU CUIDADO Y EL DE SU HIJO. MI OBJETIVO ES MANTENER UNA MADRE SANA Y LOGRAR UN NIÑO SALUDABLE, ESTO DEPENDE DE LOS CUIDADOS QUE USTED Y YO REALICEMOS';
        $parametro = Parametro::where('nombre', 'mensaje_final')->first();
        if ($parametro && !empty($parametro->valor_texto)){
            $texto = $parametro->valor_texto;
        }
        return verUtf8(Str::upper($texto));
    }

    protected function getMensajeEnCasoDe(): string
    {
        $texto = 'SANGRADO VAGINAL, PERDIDA DE LIQUIDO, DISMINUCIÓN DE MOVIMIENTOS FETALES, VISIÓN BORROSA, DOLOR DE CABEZA, CAÍDAS, CONTRACCIONES O CÓLICOS, FIEBRE.';
        $parametro = Parametro::where('nombre', 'mensaje_en_caso_de')->first();
        if ($parametro && !empty($parametro->valor_texto)){
            $texto = $parametro->valor_texto;
        }
        return verUtf8(Str::upper($texto));
    }

    protected function getMensjeFooter(): string
    {
        $texto = 'ASISTE A TU CONTROL CON REGULARIDAD, AGENDA TU CITA: 0424-7125267';
        $parametro = Parametro::where('nombre', 'mensaje_footer')->first();
        if ($parametro && !empty($parametro->valor_texto)){
            $texto = $parametro->valor_texto;
        }
        return verUtf8(Str::upper($texto));
    }

}
