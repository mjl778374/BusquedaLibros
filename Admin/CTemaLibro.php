<?php
class CTemaLibro
{
    private $IdLibro = NULL;
    private $IdTema = NULL;
    private $Tema = NULL;

    function __construct($IdLibro, $IdTema, $Tema)
    {
        $this->IdLibro = $IdLibro;
        $this->IdTema = $IdTema;
        $this->Tema = $Tema;
    } // function __construct($IdLibro, $IdTema, $Tema)
    
    public function DemeIdLibro()
    {
        return $this->IdLibro;
    } // public function DemeIdLibro()

    public function DemeIdTema()
    {
        return $this->IdTema;
    } // public function DemeIdTema()

    public function DemeTema()
    {
        return $this->Tema;
    } // public function DemeTema()
  
    public static function DemeTitulares()
    {
      return array("Tema");
    } // public static function DemeTitulares()
    
    public function DemeArregloDatos()
    {
        include "constantesApp.php";
        return array("temaLibro.php?Modo=" . $MODO_CAMBIO . "&IdLibro=" . $this->DemeIdLibro() . "&IdTema=" . $this->DemeIdTema(), $this->DemeTema());
    } // public function DemeArregloDatos()
} // class CTemaLibro
?>
