<?php
$ParametrosGet = "";
$comodin = "?";

if (isset($_GET["Modo"]))
{
    $ParametrosGet = $ParametrosGet . $comodin . 'Modo=' . $_GET["Modo"];
    $comodin = "&";
} // if (isset($_GET["Modo"]))

if (isset($_GET["IdLibro"]))
{
    $ParametrosGet = $ParametrosGet . $comodin . 'IdLibro=' . $_GET["IdLibro"];
    $comodin = "&";
} // if (isset($_GET["IdLibro"]))
?>
<frameset rows="50%,*">
   <frame src="libro.php<?php echo $ParametrosGet;?>">
   <frame src="temasLibro.php<?php echo $ParametrosGet;?>">
</frameset>
