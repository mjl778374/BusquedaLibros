<?php
if ($NumPaginaActual > 0)
{
    $Filas = [];
    $IndiceInicial = ($NumPaginaActual - 1) * $MAX_NUM_RESULTADOS_X_PAGINA;
    $IndiceFinal = $IndiceInicial + $MAX_NUM_RESULTADOS_X_PAGINA - 1;

    if ($IndiceFinal >= count($ListadoDatos))
        $IndiceFinal = count($ListadoDatos) - 1;

    for ($i = $IndiceInicial; $i <= $IndiceFinal; $i++)
    {
        $ObjDatos = $ListadoDatos[$i];
        
        if ($i == $IndiceInicial)
            $EncabezadoTabla = $ObjDatos->DemeTitulares();
            
        $Filas[] = $ObjDatos->DemeArregloDatos();
        // $EncabezadoTabla y $Filas son parámetros que recibe "componenteTabla.php"
    } // for ($i = $IndiceInicial; $i <= $IndiceFinal; $i++)

    if (count($Filas) > 0)
        include "componenteTabla.php";
} // if ($NumPaginaActual > 0)

