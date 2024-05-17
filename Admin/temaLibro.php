<?php
include_once "ValidarIngresoApp.php"; // Aquí dentro se hace el redireccionamiento a la página de ingreso, en caso de fallar la validación
// La anterior debe ser la primera instrucción por ejecutar en el archivo web

$Tema = "";
$SePretendeGuardarInformacion = false;
$SeGuardoInformacionExitosamente = false;

if (isset($_POST["Tema"]))
{
    $SePretendeGuardarInformacion = true;
    $Tema = $_POST["Tema"];
} // if (isset($_POST["Tema"]))

try
{
    include_once "ValidarParametroModo.php";

    $ArregloOtrosParametros = [];
    $URLFormulario = "temaLibro.php";
    $NombreCampoId = "IdLibro";
    if (isset($_GET["IdTema"])) {$ArregloOtrosParametros[] = "IdTema"; $ArregloOtrosParametros[] = $_GET["IdTema"];}
    // Los anteriores dos son parámetros que recibe "ValidarParametroId.php"
    include "ValidarParametroId.php";
    $IdLibro = $ValorCampoId; // Este es un dato que se obtiene en "ValidarParametroId.php"
    
    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $ArregloOtrosParametros = [];
        $URLFormulario = "temaLibro.php";
        $NombreCampoId = "IdTema";
        $ArregloOtrosParametros[] = "IdLibro"; $ArregloOtrosParametros[] = $IdLibro;
        // Los anteriores dos son parámetros que recibe "ValidarParametroId.php"
        include "ValidarParametroId.php";
        $IdTema = $ValorCampoId; // Este es un dato que se obtiene en "ValidarParametroId.php"
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
    $NuevoIdTema = $IdTema;
    $ObjTemaLibro = NULL;
    include_once "CTemasLibros.php";

    if ($NumError == 0 && $SePretendeGuardarInformacion)
    {
        $TemasLibros = new CTemasLibros();

        if (strcmp($Modo, $MODO_ALTA) == 0)
            $TemasLibros->AltaTemaLibro($IdLibro, $Tema, $NumError, $ObjTemaLibro);

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
            $TemasLibros->CambioTemaLibro($IdLibro, $IdTema, $Tema, $NumError, $ObjTemaLibro);

        if ($NumError == 0)
            $SeGuardoInformacionExitosamente = true;
            
        if ($SeGuardoInformacionExitosamente)
            $NuevoIdTema = $ObjTemaLibro->DemeIdTema();
    } // if ($NumError == 0 && $SePretendeGuardarInformacion)

    if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $TemasLibros = new CTemasLibros();
        $TemasLibros->ConsultarXTemaLibro($IdLibro, $NuevoIdTema, $Existe, $ObjTemaLibro);

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

if ($ObjTemaLibro != NULL)
{
    $IdLibro = $ObjTemaLibro->DemeIdLibro();
    
    if ($NumError == 0)
        $NuevoIdTema = $ObjTemaLibro->DemeIdTema();
    else
        $NuevoIdTema = $IdTema;
        
    $Tema = $ObjTemaLibro->DemeTema();
} // if ($ObjTemaLibro != NULL)

if ($SeGuardoInformacionExitosamente && 
   (
       strcmp($Modo, $MODO_ALTA) == 0 
       || 
       (strcmp($Modo, $MODO_CAMBIO) == 0 && $IdTema != $NuevoIdTema)
   )
)
{
    echo "<script>window.location.href = 'temaLibro.php?Modo=" . $MODO_CAMBIO . "&IdLibro=" . $IdLibro . "&IdTema=" . $NuevoIdTema . "';</script>"; // Se carga el tema guardado.
    exit;
} // if ($SeGuardoInformacionExitosamente && ...

include_once "CFormateadorMensajes.php";
include_once "CPalabras.php";

$ErrorNoExisteLibroConIdEspecificado = "No existe el libro con el id " . $IdLibro . ".";
$ErrorNoExisteTemaLibroConIdEspecificado = "No existe el tema con el id " . $IdTema . " en el libro con el id " . $IdLibro . ".";
$ErrorTemaInvalido = "El tema debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();

if ($NumError != 0)
{
    if ($NumError == 1)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);
    elseif ($NumError == 2)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteTemaLibroConIdEspecificado);
    else
    {
        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteLibroConIdEspecificado);
            elseif ($NumError == 2001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorTemaInvalido);
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'AltaTemaLibro'.");
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteLibroConIdEspecificado);
            elseif ($NumError == 2001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorTemaInvalido);
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'CambioTemaLibro'.");
        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
    } // else
} // if ($NumError != 0)
elseif ($SeGuardoInformacionExitosamente)
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeOK("Se guardó el tema exitosamente.");

$Tema = htmlspecialchars($Tema);
$MaximoTamanoCampoTema = CTemasLibros::MAXIMO_TAMANO_CAMPO_TEMA;

?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Tema">Tema</label>
                <input type="text" class="form-control" id="Tema" name="Tema" placeholder="Ingrese el tema del libro" value="<?php echo $Tema; ?>" maxlength="<?php echo $MaximoTamanoCampoTema;?>">
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='temasLibro.php?IdLibro=<?php echo $IdLibro; ?>';">Regresar</button>
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
