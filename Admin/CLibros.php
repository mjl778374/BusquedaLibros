<?php
include_once "CSQL.php";
include_once "CLibro.php";

class CLibros extends CSQL
{
    public const MAXIMO_TAMANO_CAMPO_CODIGO = 50;
    public const MAXIMO_TAMANO_CAMPO_TITULO = 100;
    public const MAXIMO_TAMANO_CAMPO_AUTOR = 100;

    function __construct()
    {
        parent::__construct();
    } // function __construct()

    public function ConsultarXLibro($IdLibro, &$Existe, &$ObjLibro)
    {
        $Consulta = "SELECT a.Codigo, b.Titulo, c.Autor FROM Libros a, TitulosLibros b, AutoresLibros c WHERE a.IdLibro = ? AND a.IdTitulo = b.IdTitulo AND a.IdAutor = c.IdAutor";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdLibro));
        $Existe = false;
        $ObjLibro = NULL;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
                $Codigo = $ResultadoConsulta[0];
                $Titulo = $ResultadoConsulta[1];
                $Autor = $ResultadoConsulta[2];
                $ObjLibro = new CLibro($IdLibro, $Codigo, $Titulo, $Autor);
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXLibro($IdLibro, &$Existe, &$ObjLibro)

    public function ConsultarXTitulo($IdTitulo, &$Titulo)
    {
        $Consulta = "SELECT Titulo FROM TitulosLibros WHERE IdTitulo = ? AND EstaLibre = 0";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdTitulo));
        $Titulo = "";
        
        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $Titulo = $ResultadoConsulta[0];

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXTitulo($IdTitulo, &$Titulo)

    public function ConsultarXAutor($IdAutor, &$Autor)
    {
        $Consulta = "SELECT Autor FROM AutoresLibros WHERE IdAutor = ? AND EstaLibre = 0";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdAutor));
        $Autor = "";
        
        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $Autor = $ResultadoConsulta[0];

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXAutor($IdAutor, &$Autor)

    private function DemePalabrasMasParecidas($PalabrasBusqueda)
    {
        include_once "CPalabrasSemejantes.php";

        $PalabrasSemejantes = new CPalabrasSemejantes();
        $Retorno = $PalabrasSemejantes->DemePalabrasMasParecidas($PalabrasBusqueda, "PalabrasXLibro", array("PalabrasXTituloLibro", "PalabrasXAutorLibro"));

        return $Retorno;
    } // private function DemePalabrasMasParecidas($PalabrasBusqueda)

    public function ConsultarXTodosLibros($PalabrasBusqueda)
    {    
        $ResultadosXRetornar = [];
        $PalabrasMasParecidas = $this->DemePalabrasMasParecidas($PalabrasBusqueda);

        $Consulta = "";
        $Consulta = $Consulta . "(";
        $Consulta = $Consulta . "     SELECT e.IdPalabra";
        $Consulta = $Consulta . "     FROM Palabras e";
        $Consulta = $Consulta . "     WHERE (1 = 0";
        
        $TiposParametros = "";
        $ArregloParametros = [];

        for($NumConsultasPalabras = 0; $NumConsultasPalabras < 3; $NumConsultasPalabras++)
        {
            for($i = 0; $i < count($PalabrasMasParecidas); $i++)
            {
                $ArregloParametros[] = $PalabrasMasParecidas[$i];
                $TiposParametros = $TiposParametros . "i";

                if ($NumConsultasPalabras == 0)
                    $Consulta = $Consulta . " OR e.IdPalabraSemejante = ?";
            } // for($i = 0; $i < count($PalabrasMasParecidas); $i++)
        } // for($NumConsultasPalabras = 0; $NumConsultasPalabras < 3; $NumConsultasPalabras++)

        $Consulta = $Consulta . ")";
        $Consulta = $Consulta . ")";

        $ConsultaPalabras = $Consulta;
        $Consulta = "";
        
        $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdLibro, a.Codigo, b.Titulo, c.Autor, 'L' as Tipo";
        $Consulta = $Consulta . " FROM Libros a, TitulosLibros b, AutoresLibros c, PalabrasXLibro d";
        $Consulta = $Consulta . " WHERE a.IdLibro = d.IdLibro";
        $Consulta = $Consulta . " AND a.IdTitulo = b.IdTitulo";
        $Consulta = $Consulta . " AND a.IdAutor = c.IdAutor";
        $Consulta = $Consulta . " AND d.IdPalabra IN " . $ConsultaPalabras;
        $Consulta = $Consulta . " GROUP BY a.IdLibro, a.Codigo, b.Titulo, c.Autor, Tipo";

        $Consulta = $Consulta . " UNION ";

        $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdLibro, a.Codigo, b.Titulo, c.Autor, 'T' as Tipo";
        $Consulta = $Consulta . " FROM Libros a, TitulosLibros b, AutoresLibros c, PalabrasXTituloLibro d";
        $Consulta = $Consulta . " WHERE a.IdTitulo = d.IdTitulo";
        $Consulta = $Consulta . " AND a.IdTitulo = b.IdTitulo";
        $Consulta = $Consulta . " AND a.IdAutor = c.IdAutor";
        $Consulta = $Consulta . " AND d.IdPalabra IN " . $ConsultaPalabras;
        $Consulta = $Consulta . " GROUP BY a.IdLibro, a.Codigo, b.Titulo, c.Autor, Tipo";

        $Consulta = $Consulta . " UNION ";

        $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdLibro, a.Codigo, b.Titulo, c.Autor, 'A' as Tipo";
        $Consulta = $Consulta . " FROM Libros a, TitulosLibros b, AutoresLibros c, PalabrasXAutorLibro d";
        $Consulta = $Consulta . " WHERE a.IdAutor = d.IdAutor";
        $Consulta = $Consulta . " AND a.IdTitulo = b.IdTitulo";
        $Consulta = $Consulta . " AND a.IdAutor = c.IdAutor";
        $Consulta = $Consulta . " AND d.IdPalabra IN " . $ConsultaPalabras;
        $Consulta = $Consulta . " GROUP BY a.IdLibro, a.Codigo, b.Titulo, c.Autor, Tipo";
        
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);

        if ($ConsultaEjecutadaExitosamente)
        {
            include_once "CGroupByCantidad.php";
            $GroupBy = new CGroupByCantidad();        
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            
            while ($ResultadoConsulta != NULL)
            {
                $NumAciertos = $ResultadoConsulta[0];
                $IdLibro = $ResultadoConsulta[1];
                $Codigo = $ResultadoConsulta[2];
                $Titulo = $ResultadoConsulta[3];
                $Autor = $ResultadoConsulta[4];
                    
                $GroupBy->AgregarTupla(array($IdLibro, $Codigo, $Titulo, $Autor), array(0), array(0,1,2,3), $NumAciertos);
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $MaximaCantidad = $GroupBy->DemeMaximaCantidad();
            $TamanoCampoCantidad = strlen($MaximaCantidad);

            $ResultadosOrdenados = $GroupBy->OrdenarTuplas(array(array(4,'i',$TamanoCampoCantidad,'desc',$MaximaCantidad), array(1, 's', self::MAXIMO_TAMANO_CAMPO_CODIGO)));

            $ResultadosXRetornar = [];
            foreach ($ResultadosOrdenados as $Clave => $CamposLibro)
                $ResultadosXRetornar[] = new CLibro($CamposLibro[0], $CamposLibro[1], $CamposLibro[2], $CamposLibro[3]);
        } // if ($ConsultaEjecutadaExitosamente)

        return $ResultadosXRetornar;
    } // public function ConsultarXTodosLibros($PalabrasBusqueda)

    public function ConsultarXTodosLibros2($IdTitulo, $IdAutor, $IdTema)
    {
        $Retorno = [];
        
        $Consulta = "";
        
        $Consulta = $Consulta . "SELECT a.IdLibro, a.Codigo, b.Titulo, c.Autor";
        $Consulta = $Consulta . " FROM Libros a, TitulosLibros b, AutoresLibros c";
        $Consulta = $Consulta . " WHERE a.IdTitulo = b.IdTitulo";
        $Consulta = $Consulta . " AND a.IdAutor = c.IdAutor";
        $Consulta = $Consulta . " AND (1=0 ";

        $TiposParametros = "";
        $ArregloParametros = [];
        
        if ($IdTitulo != $ID_ITEM_NULO)
        {
            $Consulta = $Consulta . " OR a.IdTitulo = ?";
            $ArregloParametros[] = $IdTitulo;
            $TiposParametros = $TiposParametros . "i";
        } // if ($IdTitulo != $ID_ITEM_NULO)
        
        if ($IdAutor != $ID_ITEM_NULO)
        {
            $Consulta = $Consulta . " OR a.IdAutor = ?";
            $ArregloParametros[] = $IdAutor;
            $TiposParametros = $TiposParametros . "i";
        } // if ($IdAutor != $ID_ITEM_NULO)
        
        if ($IdTema != $ID_ITEM_NULO)
        {
            $Consulta = $Consulta . " OR a.IdLibro IN (SELECT d.IdLibro FROM TemasXLibro d WHERE d.IdTema = ?)";
            $ArregloParametros[] = $IdTema;
            $TiposParametros = $TiposParametros . "i";
        } // if ($IdTema != $ID_ITEM_NULO)

        $Consulta = $Consulta . " )";
        $Consulta = $Consulta . " ORDER BY b.Titulo ASC";
        
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);        

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            
            while ($ResultadoConsulta != NULL)
            {
                $IdLibro = $ResultadoConsulta[0];
                $Codigo = $ResultadoConsulta[1];
                $Titulo = $ResultadoConsulta[2];
                $Autor = $ResultadoConsulta[3];
                $ObjLibro = new CLibro($IdLibro, $Codigo, $Titulo, $Autor);
                $Retorno[] = $ObjLibro;
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)
        } // if ($ConsultaEjecutadaExitosamente)
        
        return $Retorno;
    } // public function ConsultarXTodosLibros2($IdTitulo, $IdAutor, $IdTema)
    
    public function AltaLibro($Codigo, $Titulo, $Autor, &$NumError, &$ObjLibro)
    {

        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $Consulta = "CALL AltaLibro(?, ?, ?, ?, ?, ?, ?, ?, 1);";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'ssssssss', array($Codigo, $Titulo, $Autor, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        $ObjLibro = NULL;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $IdLibro = $ResultadoConsulta[1];
                $ObjLibro = new CLibro($IdLibro, $Codigo, $Titulo, $Autor);
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaLibro($Codigo, $Titulo, $Autor, &$NumError, &$ObjLibro)

    public function CambioLibro($IdLibro, $Codigo, $Titulo, $Autor, &$NumError, &$ObjLibro)
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL CambioLibro(?, ?, ?, ?, ?, ?, ?, ?, ?, 1);";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'issssssss', array($IdLibro, $Codigo, $Titulo, $Autor, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        $ObjLibro = NULL;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $NumError = $ResultadoConsulta[0];

            $ObjLibro = new CLibro($IdLibro, $Codigo, $Titulo, $Autor);
            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambioLibro($IdLibro, $Codigo, $Titulo, $Autor, &$NumError, &$ObjLibro)

    public function IndexarTodo()
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL IndexarTodosLibros(?, ?, ?, ?, ?, 0);";
        $this->EjecutarConsulta($Consulta, 'sssss', array(CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));
    } // public function IndexarTodo()

    function __destruct()
    {
        parent::__destruct();
    } // function __destruct()
} // class CLibros extends CSQL
?>
