<?php
class CLibro
{
    private $IdLibro = NULL;
    private $Codigo = NULL;
    private $Titulo = NULL;
    private $Autor = NULL;
    private static $VistaDatos = NULL;

    function __construct($IdLibro, $Codigo, $Titulo, $Autor)
    {
        $this->IdLibro = $IdLibro;
        $this->Codigo = $Codigo;
        $this->Titulo = $Titulo;
        $this->Autor = $Autor;
    } // function __construct($IdLibro, $Codigo, $Titulo, $Autor)

    public static function FijarVistaDatos($VistaDatos)
    {
        self::$VistaDatos = $VistaDatos;
    } //  public static function FijarVistaDatos($VistaDatos)
    
    public function DemeIdLibro()
    {
        return $this->IdLibro;
    } // public function DemeIdLibro()

    public function DemeCodigo()
    {
        return $this->Codigo;
    } // public function DemeCodigo()

    public function DemeTitulo()
    {
        return $this->Titulo;
    } // public function DemeTitulo()

    public function DemeAutor()
    {
        return $this->Autor;
    } // public function DemeAutor()
  
    public static function DemeTitulares()
    {
        if (self::$VistaDatos == 1)
            return array("Código", "Título", "Autor");
    } // public static function DemeTitulares()
    
    public function DemeArregloDatos()
    {
        include "constantesApp.php";
        
        if (self::$VistaDatos == 1)
            return array("mainLibro.php?Modo=" . $MODO_CAMBIO . "&IdLibro=" . $this->DemeIdLibro(), $this->DemeCodigo(), $this->DemeTitulo(), $this->DemeAutor());
        else if (self::$VistaDatos == 2)
            return array($this->DemeCodigo(), $this->DemeTitulo(), $this->DemeAutor());
    } // public function DemeArregloDatos()

    public function DemeIdentificador()
    {
        if (self::$VistaDatos == 1)
            return $this->DemeCodigo();
        else if (self::$VistaDatos == 2)
            return $this->DemeTitulo();
    } // public function DemeIdentificador()

    public function EsLink($IndiceDato)
    {
        $Links = array();
        $Resultado = in_array($IndiceDato, $Links);
        return $Resultado;
    } // public function EsLink($IndiceDato)

    public function DemeTextoLink($IndiceDato)
    {
        return "Abrir";
    } // public function DemeTextoLink($IndiceDato)
    
    public function DemeTargetLink($IndiceDato)
    {
        return "_blank";
    } // public function DemeTargetLink($IndiceDato)
} // class CLibro
?>
