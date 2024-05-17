<?php
include_once "CParametrosGet.php";

$ValorCampoId = CParametrosGet::ValidarIdEntero($NombreCampoId, $NumError);
    
if ($NumError == 1)
    throw new Exception("Debe incorporar el parámetro '" . $NombreCampoId . "'.");
elseif ($NumError == 2)
    throw new Exception("'" . $NombreCampoId . "' debe ser un número entero mayor o igual que 0.");
elseif ($NumError != 0)
    throw new Exception("No se manejó el error número " . $NumError . " en el parámetro '" . $NombreCampoId . "'.");

if (strcmp($_GET[$NombreCampoId], $ValorCampoId) != 0)
{
    if ($VentanaRedireccion == "")
        $VentanaRedireccion = "window";
    
    if (strlen($IncorporarParametroModo) == 0)
        $IncorporarParametroModo = 1;
        
    $Parametros = "?";
    
    if ($IncorporarParametroModo == 1)
        $Parametros = $Parametros . "Modo=" . $Modo . "&";
        
    $Parametros = $Parametros . $NombreCampoId . "=" . $ValorCampoId;

    for ($i = 0; $i < count($ArregloOtrosParametros); $i+=2)
        $Parametros = $Parametros . "&" . $ArregloOtrosParametros[$i] . "=" . $ArregloOtrosParametros[$i+1];
        
    echo "<script>" . $VentanaRedireccion . ".location.href = '" . $URLFormulario . $Parametros . "';</script>";
    exit;
    // header("Location: " . $URLFormulario . "?Modo=" . $Modo . "&" . $NombreCampoId . "=" . $ValorCampoId);
} // if (strcmp($_GET[$NombreCampoId], $ValorCampoId) != 0)
