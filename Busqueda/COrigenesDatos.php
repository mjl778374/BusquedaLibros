<?php
include_once "CSQL.php";
include_once "COrigenDatos.php";

class COrigenesDatos extends CSQL
{        
    public function DemeTodosOrigenesDatos()
    {
        $Retorno = [];
        $Retorno[] = array(COrigenDatos::ID_ORIGEN_DATOS_TITULO, "Título");
        $Retorno[] = array(COrigenDatos::ID_ORIGEN_DATOS_AUTOR, "Autor");
        $Retorno[] = array(COrigenDatos::ID_ORIGEN_DATOS_TEMA, "Tema");
        return $Retorno;
    } // public function DemeTodosOrigenesDatos()
    
    public function ConsultarEnOrigenesDatos($PalabrasBusqueda, $IdOrigenDatos)
    {   
        $ResultadosXRetornar = [];
            
        $PrimeraTabla = "";
        $OtrasTablas = [];
        
        for($i = 1; $i <= 3; $i++)
        {
            $Tabla = "";
            
            if ($i == COrigenDatos::ID_ORIGEN_DATOS_TITULO && ($IdOrigenDatos == COrigenDatos::ID_ORIGEN_DATOS_TITULO || $IdOrigenDatos == COrigenDatos::ID_TODOS_ORIGENES_DATOS))
                $Tabla = "PalabrasXTituloLibro";

            else if ($i == COrigenDatos::ID_ORIGEN_DATOS_AUTOR && ($IdOrigenDatos == COrigenDatos::ID_ORIGEN_DATOS_AUTOR || $IdOrigenDatos == COrigenDatos::ID_TODOS_ORIGENES_DATOS))
                $Tabla = "PalabrasXAutorLibro";

            else if ($i == COrigenDatos::ID_ORIGEN_DATOS_TEMA && ($IdOrigenDatos == COrigenDatos::ID_ORIGEN_DATOS_TEMA || $IdOrigenDatos == COrigenDatos::ID_TODOS_ORIGENES_DATOS))
                $Tabla = "PalabrasXTemaLibro";
                
            if ($Tabla != "")
            {
                if ($PrimeraTabla == "")
                    $PrimeraTabla = $Tabla;
                    
                else
                    $OtrasTablas[] = $Tabla;
            } // if ($Tabla != "")
        } // for($i = 1; $i <= 3; $i++)
        
        if ($PrimeraTabla != "")
        {
            include_once "CPalabrasSemejantes.php";

            $PalabrasSemejantes = new CPalabrasSemejantes();
            $PalabrasMasParecidas = $PalabrasSemejantes->DemePalabrasMasParecidas($PalabrasBusqueda, $PrimeraTabla, $OtrasTablas);        

            $Consulta = "";
            $Consulta = $Consulta . "(";
            $Consulta = $Consulta . "     SELECT e.IdPalabra";
            $Consulta = $Consulta . "     FROM Palabras e";
            $Consulta = $Consulta . "     WHERE (1 = 0";
        
            $TiposParametros = "";
            $ArregloParametros = [];

            for($NumConsultasPalabras = 1; $NumConsultasPalabras <= 3; $NumConsultasPalabras++)
            {
                for($i = 0; $i < count($PalabrasMasParecidas); $i++)
                {
                    if ($NumConsultasPalabras == COrigenDatos::ID_ORIGEN_DATOS_TITULO && $IdOrigenDatos == COrigenDatos::ID_ORIGEN_DATOS_TITULO 
                    || $NumConsultasPalabras == COrigenDatos::ID_ORIGEN_DATOS_AUTOR && $IdOrigenDatos == COrigenDatos::ID_ORIGEN_DATOS_AUTOR 
                    || $NumConsultasPalabras == COrigenDatos::ID_ORIGEN_DATOS_TEMA && $IdOrigenDatos == COrigenDatos::ID_ORIGEN_DATOS_TEMA 
                    || $IdOrigenDatos == COrigenDatos::ID_TODOS_ORIGENES_DATOS)
                    {
                        $ArregloParametros[] = $PalabrasMasParecidas[$i];
                        $TiposParametros = $TiposParametros . "i";
                    } // if ($NumConsultasPalabras == COrigenDatos::ID_ORIGEN_DATOS_TITULO && $IdOrigenDatos == COrigenDatos::ID_ORIGEN_DATOS_TITULO ...
                    
                    if ($NumConsultasPalabras == 1)
                        $Consulta = $Consulta . " OR e.IdPalabraSemejante = ?";
                } // for($i = 0; $i < count($PalabrasMasParecidas); $i++)
            } // for($NumConsultasPalabras = 1; $NumConsultasPalabras <= 3; $NumConsultasPalabras++)

            $Consulta = $Consulta . ")";
            $Consulta = $Consulta . ")";

            $ConsultaPalabras = $Consulta;
            $Consulta = "";
            $TextoUnion = "";
        
            if ($IdOrigenDatos == COrigenDatos::ID_ORIGEN_DATOS_TITULO || $IdOrigenDatos == COrigenDatos::ID_TODOS_ORIGENES_DATOS)
            {
                $Consulta = $Consulta . $TextoUnion;            
                $TextoUnion = " UNION ";
                $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdTitulo as Id, a.Titulo as Dato, " . COrigenDatos::ID_ORIGEN_DATOS_TITULO . " as Origen";
                $Consulta = $Consulta . " FROM TitulosLibros a, PalabrasXTituloLibro b";
                $Consulta = $Consulta . " WHERE a.IdTitulo = b.IdTitulo";
                $Consulta = $Consulta . " AND a.EstaLibre = 0";
                $Consulta = $Consulta . " AND b.IdPalabra IN " . $ConsultaPalabras;
                $Consulta = $Consulta . " GROUP BY a.IdTitulo, a.Titulo, Origen";
            } // if ($IdOrigenDatos == COrigenDatos::ID_ORIGEN_DATOS_TITULO || $IdOrigenDatos == COrigenDatos::ID_TODOS_ORIGENES_DATOS)

            if ($IdOrigenDatos == COrigenDatos::ID_ORIGEN_DATOS_AUTOR || $IdOrigenDatos == COrigenDatos::ID_TODOS_ORIGENES_DATOS)
            {
                $Consulta = $Consulta . $TextoUnion;            
                $TextoUnion = " UNION ";
                $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdAutor as Id, a.Autor as Dato, " . COrigenDatos::ID_ORIGEN_DATOS_AUTOR . " as Origen";
                $Consulta = $Consulta . " FROM AutoresLibros a, PalabrasXAutorLibro b";
                $Consulta = $Consulta . " WHERE a.IdAutor = b.IdAutor";
                $Consulta = $Consulta . " AND a.EstaLibre = 0";
                $Consulta = $Consulta . " AND b.IdPalabra IN " . $ConsultaPalabras;
                $Consulta = $Consulta . " GROUP BY a.IdAutor, a.Autor, Origen";
            } // if ($IdOrigenDatos == COrigenDatos::ID_ORIGEN_DATOS_AUTOR || $IdOrigenDatos == COrigenDatos::ID_TODOS_ORIGENES_DATOS)

            if ($IdOrigenDatos == COrigenDatos::ID_ORIGEN_DATOS_TEMA || $IdOrigenDatos == COrigenDatos::ID_TODOS_ORIGENES_DATOS)
            {
                $Consulta = $Consulta . $TextoUnion;            
                $TextoUnion = " UNION ";
                $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdTema as Id, a.Tema as Dato, " . COrigenDatos::ID_ORIGEN_DATOS_TEMA . " as Origen";
                $Consulta = $Consulta . " FROM TemasLibros a, PalabrasXTemaLibro b";
                $Consulta = $Consulta . " WHERE a.IdTema = b.IdTema";
                $Consulta = $Consulta . " AND a.EstaLibre = 0";
                $Consulta = $Consulta . " AND b.IdPalabra IN " . $ConsultaPalabras;
                $Consulta = $Consulta . " GROUP BY a.IdTema, a.Tema, Origen";
            } // if ($IdOrigenDatos == COrigenDatos::ID_ORIGEN_DATOS_TEMA || $IdOrigenDatos == COrigenDatos::ID_TODOS_ORIGENES_DATOS)

            $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);

            if ($ConsultaEjecutadaExitosamente)
            {     
                include_once "CGroupByCantidad.php";
                
                $GroupBy = new CGroupByCantidad();        
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
                $MaximoTamanoDato = 0;
                $MaximoTamanoOrigen = 0;
            
                while ($ResultadoConsulta != NULL)
                {
                    $NumAciertos = $ResultadoConsulta[0];
                    $Id = $ResultadoConsulta[1];
                    $Dato = $ResultadoConsulta[2];
                    $Origen = $ResultadoConsulta[3];
                    
                    if (strlen($Dato) > $MaximoTamanoDato)
                        $MaximoTamanoDato = strlen($Dato);
                        
                    if (strlen($Origen) > $MaximoTamanoOrigen)
                        $MaximoTamanoOrigen = strlen($Origen);
                        
                    $GroupBy->AgregarTupla(array($Id, $Dato, $Origen), array(0,2), array(0,1,2), $NumAciertos);
                    $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
                } // while ($ResultadoConsulta != NULL)

                $MaximaCantidad = $GroupBy->DemeMaximaCantidad();
                $TamanoCampoCantidad = strlen($MaximaCantidad);

                $ResultadosOrdenados = $GroupBy->OrdenarTuplas(array(array(3,'i',$TamanoCampoCantidad,'desc',$MaximaCantidad), array(1, 's', $MaximoTamanoDato), array(2,'i',$MaximoTamanoOrigen,'asc')));

                $ResultadosXRetornar = [];
                foreach ($ResultadosOrdenados as $Clave => $CamposOrigen)
                    $ResultadosXRetornar[] = new COrigenDatos($CamposOrigen[0], $CamposOrigen[1], $CamposOrigen[2]);
            } // if ($ConsultaEjecutadaExitosamente)
        } // if ($PrimeraTabla != "")
        
        return $ResultadosXRetornar;
    } // public function ConsultarEnOrigenesDatos($PalabrasBusqueda, $IdOrigenDatos)
} // class COrigenesDatos extends CSQL
?>
