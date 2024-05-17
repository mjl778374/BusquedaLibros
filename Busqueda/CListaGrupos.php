<?php
class CListaGrupos
{
    private $IdListadoHTML = "";
    private $IdItemHTML = "";    

    private $ListadoPorDesglosar = [];
    private $FuncionEventoOnClick = "";
        
    private $EventoOnLoad = "";
        
    function __construct($IdListadoHTML, $IdItemHTML, $ListadoPorDesglosar, $FuncionEventoOnClick)
    {
        $this->IdListadoHTML = $IdListadoHTML;
        $this->IdItemHTML = $IdItemHTML;
        $this->ListadoPorDesglosar = $ListadoPorDesglosar;
        $this->FuncionEventoOnClick = $FuncionEventoOnClick;
    } // function __construct($IdLibro, $Codigo, $Titulo, $Autor)
    
    public function DemeEventoOnLoad()
    {
        return $this->EventoOnLoad;
    } // public function DemeEventoOnLoad()
    
    public function Desglosar()
    {
?>
<div class="container mt-12">
    <div class="row">
        <div class="col-4">
            <div class="list-group" id="list-tab" role="tablist">
<?php
        include "FuncionesUtiles.php";

        $this->EventoOnLoad = "";

        for($i = 0; $i < count($this->ListadoPorDesglosar); $i++)
        {
            $ItemListado = $this->ListadoPorDesglosar[$i];
            $ArregloDatos = $ItemListado->DemeArregloDatos();

            $TituloItem = htmlspecialchars($ItemListado->DemeIdentificador());
            $EventoOnClick = $this->FuncionEventoOnClick . "('" . $this->IdItemHTML . "'";
        
            for($j = 0; $j < count($ArregloDatos); $j++)
            {
                $Dato = htmlspecialchars(FormatearTextoJS($ArregloDatos[$j]));
                $EventoOnClick = $EventoOnClick . ", '" . $Dato . "'";
            } // for($j = 0; $j < count($ArregloDatos); $j++)
        
            $EventoOnClick = $EventoOnClick . ");";
        
            $ItemActivo = "";
            if ($i == 0)
            {
                $ItemActivo = "active";
                $this->EventoOnLoad = $EventoOnClick;
            } // if ($i == 0)
?>
                <a class="list-group-item list-group-item-action <?php echo $ItemActivo; ?>" id="<?php echo $this->IdListadoHTML;?>" data-toggle="list" href="#<?php echo $this->IdItemHTML;?>" role="tab" aria-controls="home" onclick="<?php echo $EventoOnClick;?>"><?php echo $TituloItem;?></a>
<?php
        } // for($i = 0; $i < count($this->ListadoPorDesglosar); $i++)
?>
        </div>
    </div>
    <div class="col-8">
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="<?php echo $this->IdItemHTML;?>" role="tabpanel" aria-labelledby="<?php echo $this->IdListadoHTML;?>"></div>
            </div>
        </div>
    </div>
</div>
<?php
    } // public function Desglosar()
} // class CListaGrupos
?>
