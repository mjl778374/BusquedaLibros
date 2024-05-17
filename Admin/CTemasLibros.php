<?php
include_once "CSQL.php";
include_once "CTemaLibro.php";

class CTemasLibros extends CSQL
{
    public const MAXIMO_TAMANO_CAMPO_TEMA = 100;
    
    function __construct()
    {
        parent::__construct();
    } // function __construct()

    public function ConsultarXTemaLibro($IdLibro, $IdTema, &$Existe, &$ObjTemaLibro)
    {
        $Consulta = "SELECT a.IdLibro, a.IdTema, b.Tema";
        $Consulta = $Consulta . " FROM TemasXLibro a, TemasLibros b";
        $Consulta = $Consulta . " WHERE a.IdTema = b.IdTema";
        $Consulta = $Consulta . " AND a.IdLibro = ?";
        $Consulta = $Consulta . " AND a.IdTema = ?";
        
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'ii', array($IdLibro, $IdTema));
        $Existe = false;
        $ObjTemaLibro = NULL;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
                $IdLibro = $ResultadoConsulta[0];
                $IdTema = $ResultadoConsulta[1];
                $Tema = $ResultadoConsulta[2];
                $ObjTemaLibro = new CTemaLibro($IdLibro, $IdTema, $Tema);
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXTemaLibro($IdLibro, $IdTema, &$Existe, &$ObjTemaLibro)

    public function ConsultarXTema($IdTema, &$Tema)
    {
        $Consulta = "SELECT Tema FROM TemasLibros WHERE IdTema = ? AND EstaLibre = 0";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdTema));
        $Tema = "";
        
        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $Tema = $ResultadoConsulta[0];

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXTema($IdTema, &$Tema)

    public function ConsultarXTodosTemasLibro($IdLibro)
    {
        $Retorno = [];

        $Consulta = "SELECT a.IdLibro, a.IdTema, b.Tema";
        $Consulta = $Consulta . " FROM TemasXLibro a, TemasLibros b";
        $Consulta = $Consulta . " WHERE a.IdTema = b.IdTema";
        $Consulta = $Consulta . " AND a.IdLibro = ?";
        $Consulta = $Consulta . " ORDER BY b.Tema ASC";        

        $TiposParametros = "i";
        $ArregloParametros = array($IdLibro);

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $IdLibro = $ResultadoConsulta[0];
                $IdTema = $ResultadoConsulta[1];
                $Tema = $ResultadoConsulta[2];
                $ObjTemaLibro = new CTemaLibro($IdLibro, $IdTema, $Tema);
                $Retorno[] = $ObjTemaLibro;
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function ConsultarXTodosTemasLibro($IdLibro)

    public function AltaTemaLibro($IdLibro, $Tema, &$NumError, &$ObjTemaLibro)
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL AltaTemaLibro(?, ?, ?, ?, ?, ?, ?, 1);";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'issssss', array($IdLibro, $Tema, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        $ObjTemaLibro = NULL;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $IdTema = $ResultadoConsulta[1];
            } // if ($ResultadoConsulta != NULL)
            
            $ObjTemaLibro = new CTemaLibro($IdLibro, $IdTema, $Tema);
            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaTemaLibro($IdLibro, $Tema, &$NumError, &$ObjTemaLibro)

    public function CambioTemaLibro($IdLibro, $IdTema, $Tema, &$NumError, &$ObjTemaLibro)
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL CambioTemaLibro(?, ?, ?, ?, ?, ?, ?, ?, 1);";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'iissssss', array($IdLibro, $IdTema, $Tema, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        $ObjTemaLibro = NULL;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $NuevoIdTema = $ResultadoConsulta[1];
            } // if ($ResultadoConsulta != NULL)
            
            $ObjTemaLibro = new CTemaLibro($IdLibro, $NuevoIdTema, $Tema);
            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambioTemaLibro($IdLibro, $IdTema, $Tema, &$NumError, &$ObjTemaLibro)

    public function IndexarTodo()
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL IndexarTodosTemasLibros(?, ?, ?, ?, ?, 0);";
        $this->EjecutarConsulta($Consulta, 'sssss', array(CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));
    } // public function IndexarTodo()

    function __destruct()
    {
        parent::__destruct();
    } // function __destruct()
} // class CLibros extends CSQL
?>
