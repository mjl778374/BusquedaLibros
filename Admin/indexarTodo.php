<?php
$ValidarUsuarioSesionEsAdmin = 1; // Este es un parámetro que recibe "ValidarIngresoApp.php"
include_once "ValidarIngresoApp.php"; // Aquí dentro se hace el redireccionamiento a la página de ingreso, en caso de fallar la validación
// La anterior debe ser la primera instrucción por ejecutar en el archivo web

$SeDebeIndexarTodo = false;
$MensajesXDesglosar = [];
$MENSAJE_OK = 1;
$MENSAJE_ERROR = 2;

$IdMensajeSeVanIndexarTodosUsuarios = 1;
$IdMensajeSeIndexaronTodosUsuarios = 2;

$IdMensajeSeVanIndexarTodosLibros = 3;
$IdMensajeSeIndexaronTodosLibros = 4;

$IdMensajeSeVanIndexarTodosTemasLibros = 5;
$IdMensajeSeIndexaronTodosTemasLibros = 6;

$IdMensajeSeIndexoTodoExitosamente = 0;

if (isset($_POST["IndexarTodo"]))
{
    $SeDebeIndexarTodo = true;
} // if (isset($_POST["IndexarTodo"]))

// A continuación el código fuente de la implementación
try
{
    if ($SeDebeIndexarTodo)
    {
        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeVanIndexarTodosUsuarios);
        include_once "CUsuarios.php";
        $Usuarios = new CUsuarios();
        $Usuarios->IndexarTodo();
        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeIndexaronTodosUsuarios);

        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeVanIndexarTodosLibros);
        include_once "CLibros.php";
        $Libros = new CLibros();
        $Libros->IndexarTodo();
        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeIndexaronTodosLibros);

        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeVanIndexarTodosTemasLibros);
        include_once "CTemasLibros.php";
        $TemasLibros = new CTemasLibros();
        $TemasLibros->IndexarTodo();
        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeIndexaronTodosTemasLibros);

        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeIndexoTodoExitosamente);
    } // if ($SeDebeIndexarTodo)
} // try
catch (Exception $e)
{
    $MensajesXDesglosar[] = array($MENSAJE_ERROR, $e->getMessage());
} // catch (Exception $e)
// El anterior fue el código fuente de la implementación

include_once "CFormateadorMensajes.php";
$MensajeXDesglosar = "";

for($i = 0; $i < count($MensajesXDesglosar); $i++)
{
    $MensajeActual = $MensajesXDesglosar[$i];

    if ($MensajeActual[0] == $MENSAJE_OK)
    {
        if ($MensajeActual[1] == $IdMensajeSeVanIndexarTodosUsuarios)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se van a indexar los usuarios...");

        elseif ($MensajeActual[1] == $IdMensajeSeIndexaronTodosUsuarios)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se indexaron los usuarios exitosamente.");

        elseif ($MensajeActual[1] == $IdMensajeSeVanIndexarTodosLibros)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se van a indexar los libros...");

        elseif ($MensajeActual[1] == $IdMensajeSeIndexaronTodosLibros)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se indexaron los libros exitosamente.");

        elseif ($MensajeActual[1] == $IdMensajeSeVanIndexarTodosTemasLibros)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se van a indexar los temas de los libros...");

        elseif ($MensajeActual[1] == $IdMensajeSeIndexaronTodosTemasLibros)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se indexaron los temas de los libros exitosamente.");

        elseif ($MensajeActual[1] == $IdMensajeSeIndexoTodoExitosamente)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se indexó todo exitosamente.");
    } // if ($MensajeActual[0] == $MENSAJE_OK)
    
    elseif ($MensajeActual[0] == $MENSAJE_ERROR)
        $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeError($MensajeActual[1]);
} // for($i = 0; $i < count($MensajesXDesglosar); $i++)

?>
<!DOCTYPE html>
<html>
<?php
    include "encabezados.php";
?>
<body>
<?php
    $FormularioActivo = "IndexarTodo"; // Este es un parámetro que recibe "menuApp.php"
    include "menuApp.php";
?>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary btn-lg btn-block" name="IndexarTodo">Indexar Todo</button>
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
