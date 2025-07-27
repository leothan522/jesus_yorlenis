<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait ReportesFpdf
{

    // Cabecera de página
    function Header(): void
    {
        // Logo
        $this->Image(asset('img/logo_yorlenis_mini.png'), 10, 0);
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
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(0);
        //footer
        $this->Cell(160, 10, verUtf8(env('APP_NAME', 'Morros Devops').' - '.$_SESSION['footerTitle']));
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, verUtf8('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    protected function getCedula($participante): string
    {
        $cedula = $participante->cedula;
        if (is_numeric($participante->cedula)){
            $cedula = formatoMillares($participante->cedula, 0);
        }
        return Str::limit(Str::padLeft(Str::upper($cedula), 10), 12, preserveWords: true);
    }

    protected function getNombres($participante): string
    {
        return verUtf8(Str::limit(Str::upper($participante->primer_nombre.' '.$participante->segundo_nombre),20));
    }

    protected function getPrimerNombre($participante): string
    {
        return verUtf8(Str::limit(Str::upper($participante->primer_nombre),20));
    }

    protected function getSegundoNombre($participante): string
    {
        return verUtf8(Str::limit(Str::upper($participante->segundo_nombre ?? ''),20));
    }

    protected function getApellidos($participante): string
    {
        return verUtf8(Str::limit(Str::upper($participante->primer_apellido.' '.$participante->segundo_apellido),20));
    }

    protected function getPrimerApellido($participante): string
    {
        return verUtf8(Str::limit(Str::upper($participante->primer_apellido),20));
    }

    protected function getSegundoApellido($participante): string
    {
        return verUtf8(Str::limit(Str::upper($participante->segundo_apellido ?? ''),20));
    }

    protected function getFechaNac($participante): string
    {
        if (!empty($participante->fecha_nacimiento)){
            return getFecha($participante->fecha_nacimiento);
        }
        return '';
    }

    protected function getSexo($paticipante): string
    {
        $opciones =[
            0 => 'Masculino',
            1 => 'Femenino'
        ];
        return verUtf8(Str::upper($opciones[$paticipante->sexo] ?? ''));
    }

    protected function getEmail($participante): string
    {
        return verUtf8(Str::limit(Str::upper($participante->email ?? ''),20));
    }

    protected function getTelefono($participante): string
    {
        return verUtf8(Str::limit(Str::upper($participante->telefono ?? ''),20));
    }

}
