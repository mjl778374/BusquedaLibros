<?php
include_once "ValidarIngresoApp.php"; // Aquí dentro se hace el redireccionamiento a la página de ingreso, en caso de fallar la validación
// La anterior debe ser la primera instrucción por ejecutar en el archivo web

try
{    
    $URLFormulario = "temasLibro.php";
    $NombreCampoId = "IdLibro";
    $IncorporarParametroModo = 0;
    // Los anteriores tres son parámetros que recibe "ValidarParametroId.php"
    include_once "ValidarParametroId.php";
    $IdLibro = $ValorCampoId; // Este es un dato que se obtiene en "ValidarParametroId.php"    
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)

// A continuación el código fuente de la implementación
try
{
    if ($NumError == 0)
    {
        include_once "CTemasLibros.php";
        $TemasLibros = new CTemasLibros();
        $ListadoTemasLibros = $TemasLibros->ConsultarXTodosTemasLibro($IdLibro);
    } // if ($NumError == 0)
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)
// El anterior fue el código fuente de la implementación


$URLFormulario = "temasLibro.php";  // Este es un parámetro que recibe "AfinarParametrosListado.php"
$ParametrosURL = "?IdLibro=" . $IdLibro; // Este es un parámetro que recibe "AfinarParametrosListado.php"
$ListadoDatos = $ListadoTemasLibros; // Este es un parámetro que reciben "AfinarParametrosListado.php" y "DesglosarTablaDatos.php"
include_once "AfinarParametrosListado.php";
include_once "DesglosarTablaDatos.php";

include_once "CFormateadorMensajes.php";
?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<?php
if ($NumError != 0)
{
    if ($NumError == 1)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);
} // if ($NumError != 0)
?>

<?php
if ($NumError == 0) {
?>
<div class="container mt-4 mb-4">
<?php
include_once "constantesApp.php";
?>
<a href="temaLibro.php?Modo=<?php echo $MODO_ALTA;?>&IdLibro=<?php echo $IdLibro;?>" class="btn btn-primary" role="button" aria-pressed="true">Agregar Tema</a>
</div>
<?php
} // if ($NumError == 0) {
?>

<?php
$URL = "temasLibro.php";
$ParametrosURL = "?IdLibro=" . $IdLibro;
// Los anteriores son parámetros que recibe "componentePaginacion.php"

if ($NumPaginas > 0)
    include "componentePaginacion.php";
?>
<?php

if ($MensajeXDesglosar != "")
    echo $MensajeXDesglosar;
?>
</body>
</html>
