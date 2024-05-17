<?php
class CUsuario
{
    private $IdUsuario = NULL;
    private $Usuario = NULL;
    private $Cedula = NULL;
    private $Nombre = NULL;
    private $EsAdministrador = NULL;

    function __construct($IdUsuario, $Usuario, $Cedula, $Nombre, $EsAdministrador)
    {
        $this->IdUsuario = $IdUsuario;
        $this->Usuario = $Usuario;
        $this->Cedula = $Cedula;
        $this->Nombre = $Nombre;
        $this->EsAdministrador = $EsAdministrador;
    } // function __construct($IdUsuario, $Usuario, $Cedula, $Nombre, $EsAdministrador)

    public function DemeIdUsuario()
    {
        return $this->IdUsuario;
    } // public function DemeIdUsuario()

    public function DemeUsuario()
    {
        return $this->Usuario;
    } // public function DemeUsuario()

    public function DemeCedula()
    {
        return $this->Cedula;
    } // public function DemeCedula()

    public function DemeNombre()
    {
        return $this->Nombre;
    } // public function DemeNombre()

    public function DemeEsAdministrador()
    {
        return $this->EsAdministrador;
    } // public function DemeEsAdministrador()
    
    public static function DemeTitulares()
    {
        return array("Usuario", "Cédula", "Nombre", "Es Administrador");
    } // public static function DemeTitulares()
    
    public function DemeArregloDatos()
    {
        include "constantesApp.php";
        $EsAdministrador = "No";

        if ($this->DemeEsAdministrador())
            $EsAdministrador = "Sí";

        return array("usuario.php?Modo=" . $MODO_CAMBIO . "&IdUsuario=" . $this->DemeIdUsuario(), $this->DemeUsuario(), $this->DemeCedula(), $this->DemeNombre(), $EsAdministrador);
    } // public function DemeArregloDatos()
} // class CUsuario
?>
