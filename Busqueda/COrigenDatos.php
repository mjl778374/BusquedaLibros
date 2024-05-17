<?php
class COrigenDatos
{
    public const ID_ORIGEN_DATOS_TITULO = 1;
    public const ID_ORIGEN_DATOS_AUTOR = 2;
    public const ID_ORIGEN_DATOS_TEMA = 3;
    public const ID_TODOS_ORIGENES_DATOS = 4;
    
    private $IdDato = NULL;
    private $Dato = NULL;
    private $Origen = NULL;
    
    function __construct($IdDato, $Dato, $Origen)
    {
        $this->IdDato = $IdDato;
        $this->Dato = $Dato;
        $this->Origen = $Origen;
    } // function __construct($IdDato, $Dato, $Origen)s
    
    public function DemeIdDato()
    {
        return $this->IdDato;
    } // public function DemeIdDato()

    public function DemeDato()
    {
        return $this->Dato;
    } // public function DemeDato()

    public function DemeOrigen()
    {
        return $this->Origen;
    } // public function DemeOrigen()

    public function DemeDescOrigen()
    {
        if ($this->Origen == self::ID_ORIGEN_DATOS_TITULO)
            return "Título";

        else if ($this->Origen == self::ID_ORIGEN_DATOS_AUTOR)
            return "Autor";

        else if ($this->Origen == self::ID_ORIGEN_DATOS_TEMA)
            return "Tema";
    } // public function DemeDescOrigen()
 } // class COrigenDatos
?>
