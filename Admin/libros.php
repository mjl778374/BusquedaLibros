<?php
include_once "ValidarIngresoApp.php"; // Aquí dentro se hace el redireccionamiento a la página de ingreso, en caso de fallar la validación
// La anterior debe ser la primera instrucción por ejecutar en el archivo web

session_start();
$TextoXBuscar = "";

if (isset($_GET["TextoXBuscar"]))
    $TextoXBuscar = $_GET["TextoXBuscar"];
elseif (isset($_SESSION["Libros_TextoXBuscar"]))
    $TextoXBuscar = $_SESSION["Libros_TextoXBuscar"];

$_SESSION["Libros_TextoXBuscar"] = $TextoXBuscar;

// A continuación el código fuente de la implementación
try
{
    include_once "CLibros.php";
    $Libros = new CLibros();
    $ListadoLibros = $Libros->ConsultarXTodosLibros($TextoXBuscar);
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)
// El anterior fue el código fuente de la implementación
?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<?php
$FormularioActivo = "Libros";
$URLFormularioActivo = "libros.php";
// Los anteriores son parámetros que recibe "menuApp.php"
include "menuApp.php";

$URLFormulario = "libros.php";  // Este es un parámetro que recibe "AfinarParametrosListado.php"
$ListadoDatos = $ListadoLibros; // Este es un parámetro que reciben "AfinarParametrosListado.php" y "DesglosarTablaDatos.php"
CLibro::FijarVistaDatos(1); // Este es un dato que necesita "DesglosarTablaDatos.php"
include_once "AfinarParametrosListado.php";
include_once "DesglosarTablaDatos.php";

include_once "CFormateadorMensajes.php";
$MensajeXDesglosar = "";

if ($NumError == 1)
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);
?>
<div class="container mt-4 mb-4">
<?php
include_once "constantesApp.php";
?>
<a href="libro.php?Modo=<?php echo $MODO_ALTA;?>" class="btn btn-primary" role="button" aria-pressed="true">Agregar Libro</a>
</div>

<?php
$URL = "libros.php";
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
