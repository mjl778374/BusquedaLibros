<?php
$TextoXBuscar = $_GET['TextoXBuscar'];
$IdOrigen = $_GET['IdOrigen'];

include_once "COrigenesDatos.php";
$OrigenesDatos = new COrigenesDatos();
$ListadoDatosXBuscar = $OrigenesDatos->ConsultarEnOrigenesDatos($TextoXBuscar, $IdOrigen);
?>

<script>
window.parent.VaciarListaDatosXBuscar();

<?php

include "FuncionesUtiles.php";

for($i = 0; $i < count($ListadoDatosXBuscar); $i++)
{
    $DatoXBuscar = $ListadoDatosXBuscar[$i];

    $IdDato = $DatoXBuscar->DemeIdDato();
    $DescDato = FormatearTextoJS($DatoXBuscar->DemeDescOrigen() . ": " . $DatoXBuscar->DemeDato());
    $Origen = $DatoXBuscar->DemeOrigen();

    $Alias = htmlspecialchars(FormatearTextoJS($Alias));
?>
window.parent.AgregarDatoAListaDatosXBuscar(<?php echo $IdDato;?>, '<?php echo $DescDato;?>', "<?php echo $Origen; ?>");
<?php
} // for($i = 0; $i < count($ListadoDatosXBuscar); $i++)
?>
</script>
