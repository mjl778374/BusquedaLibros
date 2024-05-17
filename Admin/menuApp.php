<?php
include_once "constantesApp.php";
include_once "CSession.php";

try
{
    $UsuarioSesionEsAdmin = false;
    $ObjUsuarioSesion = CSession::DemeObjUsuarioSesion();

    if ($ObjUsuarioSesion != NULL)
        $UsuarioSesionEsAdmin = $ObjUsuarioSesion->DemeEsAdministrador();
} // try
catch (Exception $e)
{}

$MostrarMenuBuscar = false;

if ($FormularioActivo == "Usuarios" || $FormularioActivo == "Usuario")
{
    $MenuUsuariosActivo = "active";

    if ($FormularioActivo == "Usuarios")
    {
        $MostrarMenuBuscar = true;
        $NombreMenuBuscar = "TextoXBuscar_Usuarios";
    } // if ($FormularioActivo == "Usuarios")
} // if ($FormularioActivo == "Usuarios" || $FormularioActivo == "Usuario")
else if ($FormularioActivo == "Libros" || $FormularioActivo == "Libro")
{
    $MenuLibrosActivo = "active";

    if ($FormularioActivo == "Libros")
    {
        $MostrarMenuBuscar = true;
        $NombreMenuBuscar = "TextoXBuscar_Libros";
    } // if ($FormularioActivo == "Libros")
} // else if ($FormularioActivo == "Libros" || $FormularioActivo == "Libro")
else if($FormularioActivo == "CambiarContrasena")
    $MenuCambiarContrasenaActivo = "active";
else if($FormularioActivo == "IndexarTodo")
    $MenuIndexarTodoActivo = "active";
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <a class="navbar-brand" href="main.php" target="_top">Menú</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item <?php echo $MenuCambiarContrasenaActivo ?>">
        <a class="nav-link" href="cambiarContrasena.php" target="_top">Cambiar Contraseña</a>
      </li>
<?php if ($UsuarioSesionEsAdmin) { ?>
      <li class="nav-item <?php echo $MenuUsuariosActivo ?>">
        <a class="nav-link" href="usuarios.php" target="_top">Usuarios</a>
      </li>
<?php } // if ($UsuarioSesionEsAdmin) ?>
      <li class="nav-item <?php echo $MenuLibrosActivo ?>">
        <a class="nav-link" href="libros.php" target="_top">Libros</a>
      </li>
<?php if ($UsuarioSesionEsAdmin) { ?>
      <li class="nav-item <?php echo $MenuIndexarTodoActivo ?>">
        <a class="nav-link" href="indexarTodo.php" target="_top">Indexar Todo</a>
      </li>
<?php } // if ($UsuarioSesionEsAdmin) ?>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $URL_PAGINA_INGRESO; ?>" target="_top">Salir</a>
      </li>
    </ul>
<?php if ($MostrarMenuBuscar) { ?>
    <form class="form-inline my-2 my-lg-0" method="post" onsubmit="form_onsubmit(this, this.<?php echo $NombreMenuBuscar; ?>.value, '<?php echo $URLFormularioActivo; ?>');">
      <input class="form-control mr-sm-2" type="search" placeholder="Buscar" aria-label="Buscar" name="<?php echo $NombreMenuBuscar; ?>">
      <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Buscar</button>
    </form>
<?php } // if ($MostrarMenuBuscar) ?>
  </div>
</nav>
<?php if ($MostrarMenuBuscar) { ?>
<script>
function form_onsubmit(unForm, ValorCampoBusqueda, URLRedireccionar)
{
    var TextoXBuscar = ValorCampoBusqueda;
    TextoXBuscar = ReemplazarTodo(TextoXBuscar, '?', ' '); // La función "ReemplazarTodo" se encuentra en el archivo "FuncionesUtiles.js" que se carga desde "encabezados.php"
    TextoXBuscar = ReemplazarTodo(TextoXBuscar, '&', ' ');
    TextoXBuscar = ReemplazarTodo(TextoXBuscar, '  ', ' ');
    TextoXBuscar = TextoXBuscar.trim();
    TextoXBuscar = ReemplazarTodo(TextoXBuscar, ' ', '+');
    unForm.action = URLRedireccionar + '?TextoXBuscar=' + TextoXBuscar;
} // function form_onsubmit(unForm, ValorCampoBusqueda, URLRedireccionar)
</script>
<?php } // if ($MostrarMenuBuscar) ?>
