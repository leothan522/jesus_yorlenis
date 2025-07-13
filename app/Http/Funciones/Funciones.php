<?php
//Funciones Personalizadas para el Proyecto

function cerosIzquierda($cantidad, $cantCeros = 2): int|string
{
    if ($cantidad == 0) {
        return 0;
    }
    return str_pad($cantidad, $cantCeros, "0", STR_PAD_LEFT);
}

function numSizeCodigo(): int
{
    $num = 6;
    $parametro = \App\Models\Parametro::where('nombre', 'size_codigo')->first();
    if ($parametro){
        if (!empty($parametro->valor_id) && $parametro->valor_id >= 1){
            $num = intval($parametro->valor_id);
        }
    }
    return $num;
}

function formatoMillares($cantidad, $decimal = 2): string
{
    if (!is_numeric($cantidad)){
        $cantidad = 0;
    }
    return number_format($cantidad, $decimal, ',', '.');
}

function verImagen($path): string
{
    $response  = 'img/placeholder.jpg';
    if (!empty($path)){
        $existe = file_exists(public_path('storage/'.$path));
        if ($existe){
            $response = storage_path('app/public/'.$path);
        }
    }
    return $response;
}
