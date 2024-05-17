<?php
try
{
    include_once "CFormateadorMensajes.php"; // Esto es para desglosar mensajes de error si ocurren (como los relativos a la base de datos)
    $MensajeXDesglosar = "";
 
    include_once "constantesApp.php";
    include_once "CParametrosGet.php";

    $IdListaOrigenesDatos = "IdOrigenesDatos";
    $NombreListaOrigenesDatos = "IdOrigenesDatos";
        
    $IdTitulo = CParametrosGet::ValidarIdEntero("IdTitulo", $NumError);
    if ($NumError != 0)
        $IdTitulo = $ID_ITEM_NULO;

    $IdAutor = CParametrosGet::ValidarIdEntero("IdAutor", $NumError);
    if ($NumError != 0)
        $IdAutor = $ID_ITEM_NULO;

    $IdTema = CParametrosGet::ValidarIdEntero("IdTema", $NumError);
    if ($NumError != 0)
        $IdTema = $ID_ITEM_NULO;

    include_once "COrigenDatos.php";    
    include_once "CLibro.php";        

    // A continuación el código fuente de la implementación
    include_once "CLibros.php";
    $Libros = new CLibros();
    $ListadoLibros = $Libros->ConsultarXTodosLibros2($IdTitulo, $IdAutor, $IdTema);
    
    $Libros->ConsultarXTitulo($IdTitulo, $Titulo);
    $Libros->ConsultarXAutor($IdAutor, $Autor);
    
    include_once "CTemasLibros.php";    
    $TemasLibros = new CTemasLibros();
    $TemasLibros->ConsultarXTema($IdTema, $Tema);

    include_once "COrigenesDatos.php";
    $OrigenesDatos = new COrigenesDatos();
    $ListadoOrigenesDatos = $OrigenesDatos->DemeTodosOrigenesDatos();    
    // El anterior fue el código fuente de la implementación    
} // try
catch (Exception $e)
{
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($e->getMessage());
} // catch (Exception $e)
?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <a class="navbar-brand" href="">Menú</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoOrigenesDatos;
$PrimerItemListaSeleccion[] = array(COrigenDatos::ID_TODOS_ORIGENES_DATOS, "Origen de Datos");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = COrigenDatos::ID_TODOS_ORIGENES_DATOS;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
<?php $IdListaSeleccion=$IdListaOrigenesDatos; $NombreListaSeleccion=$NombreListaOrigenesDatos; include "componenteListaSeleccion.php" ?>

      </li>
    </ul>
<form class="form-inline my-2 my-lg-0" method="post">
      <input class="form-control col-12" type="search" placeholder="Título, Autor o Tema" aria-label="Buscar" name="DatoXBuscar" onkeyup="CargarListaDatosXBuscar(this.value);">
</form>
  </div>
</nav>
<div id="ListaDatosXBuscar" class="list-group">
</div>
<?php
    if ($Titulo != "")
        echo "<h3>Título: " . htmlspecialchars($Titulo) . "</h3>";

    if ($Autor != "")
        echo "<h3>Autor: " . htmlspecialchars($Autor) . "</h3>";

    if ($Tema != "")
        echo "<h3>Tema: " . htmlspecialchars($Tema) . "</h3>";

    include "CListaGrupos.php";

    $ListaGrupos = new CListaGrupos("lista-libros", "item-libro", $ListadoLibros, "CargarLibro");
    CLibro::FijarVistaDatos(2);
    $ListaGrupos->Desglosar();
    $EventoOnLoad = $ListaGrupos->DemeEventoOnLoad();

    if ($MensajeXDesglosar != "")
        echo $MensajeXDesglosar;
?>
<script>
<?php
if ($EventoOnLoad != "")
    echo $EventoOnLoad; // Requiere que el evento termine con ";"
?>

function CargarLibro(IdItemLibro, Codigo, Titulo, Autor)
{
    var ItemLibro = document.getElementById(IdItemLibro);
        
    ItemLibro.innerHTML = EnvolverDentroParrafo("Código del Libro: " + Codigo)
        + EnvolverDentroParrafo("Título: " + Titulo)
        + EnvolverDentroParrafo("Autor: " + Autor);
} // function CargarLibro(IdItemLibro, Codigo, Titulo, Autor)

function EnvolverDentroParrafo(Texto)
{
    return "<br>" + Texto + "</br>";
} // function EnvolverDentroParrafo(Texto)

function VaciarListaDatosXBuscar()
{
    document.getElementById("ListaDatosXBuscar").innerHTML = "";
} // function VaciarListaDatosXBuscar()

function AgregarDatoAListaDatosXBuscar(iIdDato, sDescDato, iOrigen)
{
    var m_sHTML = document.getElementById("ListaDatosXBuscar").innerHTML;
    m_sHTML = m_sHTML + '<button type="button" class="list-group-item list-group-item-action" onclick="SeleccionarDatoXBuscar(' + iIdDato + ", " + iOrigen + ');">' + sDescDato + '</button>';
    document.getElementById("ListaDatosXBuscar").innerHTML = m_sHTML;
} // function AgregarDatoAListaDatosXBuscar(iIdDato, sDescDato, iOrigen)

function SeleccionarDatoXBuscar(iIdDato, iOrigen)
{
    var m_sHref = "<?php echo $URL_PAGINA_INGRESO; ?>";
    var m_bCargarVentana = true;
    
    if (iOrigen == "<?php echo COrigenDatos::ID_ORIGEN_DATOS_TITULO;?>")
        m_sHref = m_sHref + "?IdTitulo=" + iIdDato;

    else if (iOrigen == "<?php echo COrigenDatos::ID_ORIGEN_DATOS_AUTOR;?>")
        m_sHref = m_sHref + "?IdAutor=" + iIdDato;

    else if (iOrigen == "<?php echo COrigenDatos::ID_ORIGEN_DATOS_TEMA;?>")
        m_sHref = m_sHref + "?IdTema=" + iIdDato;

    else
        m_bCargarVentana = false;
        
    if (m_bCargarVentana)
        window.location.href = m_sHref;
} // function SeleccionarDatoXBuscar(iIdDato, iOrigen)

function CargarListaDatosXBuscar(sTextoXBuscar)
{
    sTextoXBuscar = ReemplazarTodo(sTextoXBuscar, "\\", "+"); // La función "ReemplazarTodo" se encuentra en el archivo "FuncionesUtiles.js" que se carga desde "encabezados.php"
    sTextoXBuscar = ReemplazarTodo(sTextoXBuscar, "?", "+");
    sTextoXBuscar = ReemplazarTodo(sTextoXBuscar, "&", "+");
    sTextoXBuscar = ReemplazarTodo(sTextoXBuscar, " ", "+");

    var m_sOrigen = OpcionSeleccionada("<?php echo $IdListaOrigenesDatos;?>");
    var m_sHref = 'cargarListaDatosXBuscar.php?TextoXBuscar=' + sTextoXBuscar + "&IdOrigen=" + m_sOrigen;
    window.fraProcesar.location.href = m_sHref;
} // function CargarListaDatosXBuscar(sTextoXBuscar)

function OpcionSeleccionada(IdListaSeleccion)
{
    var ListaSeleccion = document.getElementById(IdListaSeleccion);
    return ListaSeleccion.options[ListaSeleccion.selectedIndex].value;
} // function OpcionSeleccionada(IdListaSeleccion)

</script>
<?php
$AnchoFrame = 0;
$AltoFrame = 0;

if ($MOSTRAR_CONSULTAS_SQL)
{
    $AnchoFrame = "100%";
    $AltoFrame = 400;
} // if ($MOSTRAR_CONSULTAS_SQL)
?>
<iframe name="fraProcesar" width="<?php echo $AnchoFrame;?>" height="<?php echo $AltoFrame;?>"></iframe>
</html>
