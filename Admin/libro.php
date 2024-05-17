<?php
include_once "ValidarIngresoApp.php"; // Aquí dentro se hace el redireccionamiento a la página de ingreso, en caso de fallar la validación
// La anterior debe ser la primera instrucción por ejecutar en el archivo web

$Codigo = "";
$Titulo = "";
$Autor = "";
$SePretendeGuardarInformacion = false;
$SeGuardoInformacionExitosamente = false;

if (isset($_POST["Codigo"]))
{
    $SePretendeGuardarInformacion = true;
    $Codigo = $_POST["Codigo"];
    $Titulo = $_POST["Titulo"];
    $Autor = $_POST["Autor"];
} // if (isset($_POST["Codigo"]))

try
{
    include_once "ValidarParametroModo.php";
    
    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $URLFormulario = "mainLibro.php";
        $NombreCampoId = "IdLibro";
        $VentanaRedireccion = "window.top";
        // Los anteriores tres son parámetros que recibe "ValidarParametroId.php"
        include_once "ValidarParametroId.php";
        $IdLibro = $ValorCampoId; // Este es un dato que se obtiene en "ValidarParametroId.php"
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)

// A continuación el código fuente de la implementación
try
{
    $ObjLibro = NULL;
    include_once "CLibros.php";

    if ($NumError == 0 && $SePretendeGuardarInformacion)
    {
        $Libros = new CLibros();

        if (strcmp($Modo, $MODO_ALTA) == 0)
            $Libros->AltaLibro($Codigo, $Titulo, $Autor, $NumError, $ObjLibro);

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
            $Libros->CambioLibro($IdLibro, $Codigo, $Titulo, $Autor, $NumError, $ObjLibro);

        if ($NumError == 0)
            $SeGuardoInformacionExitosamente = true;
    } // if ($NumError == 0 && $SePretendeGuardarInformacion)

    if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $Libros = new CLibros();
        $Libros->ConsultarXLibro($IdLibro, $Existe, $ObjLibro);

        if (!$Existe)
            $NumError = 2;
    } // if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)
// El anterior fue el código fuente de la implementación

if ($ObjLibro != NULL)
{
    $IdLibro = $ObjLibro->DemeIdLibro();
    $Codigo = $ObjLibro->DemeCodigo();
    $Titulo = $ObjLibro->DemeTitulo();
    $Autor = $ObjLibro->DemeAutor();
} // if ($ObjLibro != NULL)

if ($SeGuardoInformacionExitosamente && strcmp($Modo, $MODO_ALTA) == 0)
{
    echo "<script>window.top.location.href = 'mainLibro.php?Modo=" . $MODO_CAMBIO . "&IdLibro=" . $IdLibro . "';</script>"; // Se carga el libro guardado.
    exit;
} // if ($SeGuardoInformacionExitosamente && strcmp($Modo, $MODO_ALTA) == 0)

include_once "CFormateadorMensajes.php";
include_once "CPalabras.php";

$ErrorNoExisteLibroConIdEspecificado = "No existe el libro con el id " . $IdLibro . ".";
$ErrorCodigoInvalido = "El código debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();
$ErrorTituloInvalido = "El título debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();
$ErrorAutorInvalido = "El autor debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();

if ($NumError != 0)
{
    if ($NumError == 1)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);
    elseif ($NumError == 2)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteLibroConIdEspecificado);
    else
    {
        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("Ya existe un libro con el código " . $Codigo . ". No se puede insertar nuevamente.");
            elseif ($NumError == 2001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorCodigoInvalido);
            elseif ($NumError == 2002)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorTituloInvalido);
            elseif ($NumError == 2003)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorAutorInvalido);
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'AltaLibro'.");
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("Ya existe el código " . $Codigo . " con otro id.");
            elseif ($NumError == 2001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteLibroConIdEspecificado);
            elseif ($NumError == 3001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorCodigoInvalido);
            elseif ($NumError == 3002)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorTituloInvalido);
            elseif ($NumError == 3003)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorAutorInvalido);
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'CambioLibro'.");
        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
    } // else
} // if ($NumError != 0)
elseif ($SeGuardoInformacionExitosamente)
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeOK("Se guardó el libro exitosamente.");

$Codigo = htmlspecialchars($Codigo);
$Titulo = htmlspecialchars($Titulo);
$Autor = htmlspecialchars($Autor);

$MaximoTamanoCampoCodigo = CLibros::MAXIMO_TAMANO_CAMPO_CODIGO;
$MaximoTamanoCampoTitulo = CLibros::MAXIMO_TAMANO_CAMPO_TITULO;
$MaximoTamanoCampoAutor = CLibros::MAXIMO_TAMANO_CAMPO_AUTOR;

?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<?php
$FormularioActivo = "Libro"; // Este es un parámetro que recibe "menuApp.php"
include "menuApp.php";
?>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Codigo">Código</label>
                <input type="text" class="form-control" id="Codigo" name="Codigo" placeholder="Ingrese el código del libro" value="<?php echo $Codigo; ?>" maxlength="<?php echo $MaximoTamanoCampoCodigo;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Titulo">Título</label>
                <input type="text" class="form-control" id="Titulo" name="Titulo" placeholder="Ingrese el título del libro" value="<?php echo $Titulo; ?>" maxlength="<?php echo $MaximoTamanoCampoTitulo;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Autor">Autor</label>
                <input type="text" class="form-control" id="Autor" name="Autor" placeholder="Ingrese el autor del libro" value="<?php echo $Autor; ?>" maxlength="<?php echo $MaximoTamanoCampoAutor;?>">
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="window.top.location.href='libros.php';">Regresar</button>
            </div>
        </div>
    </div>
<?php
if ($MensajeXDesglosar != "")
    echo $MensajeXDesglosar;
?>
</form>
</body>
</html>
