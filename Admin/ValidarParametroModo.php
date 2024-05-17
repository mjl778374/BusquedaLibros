<?php
include_once "CParametrosGet.php";
$Modo = CParametrosGet::ValidarModo("Modo", $NumError);

if ($NumError == 1)
    throw new Exception("Debe incorporar el parámetro 'Modo'.");
elseif ($NumError == 2)
    throw new Exception("'Modo' inválido.");
elseif ($NumError != 0)
    throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'Modo'.");

