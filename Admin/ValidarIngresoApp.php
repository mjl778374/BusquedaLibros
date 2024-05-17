<?php
include_once "constantesApp.php";
include_once "CSession.php";

try
{
    $UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
    // La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
    // transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.

    $ObjUsuario = NULL;
    $UsuarioSesionEsAdmin = false;

    if ($UsuarioSesionIngresoOKApp)
        $ObjUsuario = CSession::DemeObjUsuarioSesion();

    if ($ObjUsuario != NULL && $ValidarUsuarioSesionEsAdmin)
        $UsuarioSesionEsAdmin = $ObjUsuario->DemeEsAdministrador();
} // try
catch (Exception $e)
{}

$RedireccionarAPaginaIngreso = 0;

if (!$UsuarioSesionIngresoOKApp || $ObjUsuario == NULL)
    $RedireccionarAPaginaIngreso = 1;
    
if ($ValidarUsuarioSesionEsAdmin && !$UsuarioSesionEsAdmin)
    $RedireccionarAPaginaIngreso = 1;
    
if ($RedireccionarAPaginaIngreso)
{
    echo "<script>window.top.location.href = '" . $URL_PAGINA_INGRESO . "';</script>"; // Se redirecciona a la página de ingreso a la aplicación
    exit;
} // if ($RedireccionarAPaginaIngreso)
