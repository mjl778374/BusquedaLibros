<?php
include_once "constantesApp.php";
$NumPaginas = 0;

if (count($ListadoDatos) > 0 && $MAX_NUM_RESULTADOS_X_PAGINA > 0)
    $NumPaginas = ceil(count($ListadoDatos) / $MAX_NUM_RESULTADOS_X_PAGINA);

include_once "CParametrosGet.php";
$NumPaginaActual = CParametrosGet::DemeNumPagina("NumPagina", $NumPaginas);
// Los anteriores son parámetros que manipulan "componenteTabla.php" y "componentePaginacion.php"

if (isset($_GET["NumPagina"]) && strcmp($_GET["NumPagina"], $NumPaginaActual) != 0)
{
    if ($ParametrosURL != "")
        $ParametrosURL = $ParametrosURL . "&";
    else
        $ParametrosURL = "?";
        
    $ParametrosURL = $ParametrosURL . "NumPagina=" . $NumPaginaActual;
    
    echo "<script>window.location.href = '" . $URLFormulario . $ParametrosURL . "';</script>";
    exit;
    // header("Location: " . "usuarios.php?NumPagina=" . $NumPaginaActual);
 } // if (isset($_GET["NumPagina"]) && strcmp($_GET["NumPagina"], $NumPaginaActual) != 0)

