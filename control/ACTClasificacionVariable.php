<?php
/**
*@package pXP
*@file gen-ACTClasificacionVariable.php
*@author  (admin)
*@date 27-06-2017 09:34:29
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__) . '/../reportes/RClasificacionActivos.php');

class ACTClasificacionVariable extends ACTbase{    
			
	function listarClasificacionVariable(){
		$this->objParam->defecto('ordenacion','id_clasificacion_variable');
		$this->objParam->defecto('dir_ordenacion','asc');

		if($this->objParam->getParametro('id_clasificacion')!=''){
			$this->objParam->addFiltro("clavar.id_clasificacion = ".$this->objParam->getParametro('id_clasificacion'));
		}

		if($this->objParam->getParametro('id_activo_fijo')!=''){
			$this->objParam->addFiltro("clavar.id_clasificacion_variable not in (
    				select id_clasificacion_variable
    				from kaf.tactivo_fijo_caract
    				where id_activo_fijo = ".$this->objParam->getParametro('id_activo_fijo').")
					");
		}

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODClasificacionVariable','listarClasificacionVariable');
		} else{
			$this->objFunc=$this->create('MODClasificacionVariable');
			
			$this->res=$this->objFunc->listarClasificacionVariable($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarClasificacionVariable(){
		$this->objFunc=$this->create('MODClasificacionVariable');	
		if($this->objParam->insertar('id_clasificacion_variable')){
			$this->res=$this->objFunc->insertarClasificacionVariable($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarClasificacionVariable($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarClasificacionVariable(){
			$this->objFunc=$this->create('MODClasificacionVariable');	
		$this->res=$this->objFunc->eliminarClasificacionVariable($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	function listarClasificacionPartida(){
		$this->objParam->defecto('ordenacion','id_clasificacion_partida');		
		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('id_gestion')!=''){
			$this->objParam->addFiltro("clapa.id_gestion = ".$this->objParam->getParametro('id_gestion'));
		}		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODClasificacionVariable','listarClasificacionPartida');
		} else{
			$this->objFunc=$this->create('MODClasificacionVariable');
			
			$this->res=$this->objFunc->listarClasificacionPartida($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());		
				
	}
	
	function insertarClasificacionPartida(){
		$this->objFunc=$this->create('MODClasificacionVariable');
		if($this->objParam->insertar('id_clasificacion_partida')){
			$this->res=$this->objFunc->insertarClasificacionPartida($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarClasificacionPartida($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());		
	}

	function eliminarClasificacionPartida(){
			$this->objFunc=$this->create('MODClasificacionVariable');	
		$this->res=$this->objFunc->eliminarClasificacionPartida($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());		
	}
	
	function listarPartidas(){
		$this->objParam->defecto('ordenacion','id_partida');
		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('id_gestion')!=''){
			$this->objParam->addFiltro("par.id_gestion = ".$this->objParam->getParametro('id_gestion'));
		}							
		$this->objFunc=$this->create('MODClasificacionVariable');
		$this->res=$this->objFunc->listarPartidas($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());					
	}
	
	function clonarClasificacionPartidaGestion(){
		$this->objFunc=$this->create('MODClasificacionVariable');	
		$this->res=$this->objFunc->clonarClasificacionPartidaGestion($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());		
	}

    function listarClasificacionPartidas(){//fRnk: nuevo reporte Clasificación de Activos Fijos HR01341
        $nombreArchivo = 'ClasificacionAF' . uniqid(md5(session_id())) . '.pdf';
        $this->objFunc = $this->create('MODClasificacionVariable');
        $datos = $this->objFunc->listarClasificacionPartidas();//var_dump($datos);exit();
        $tamano = 'LETTER';
        $orientacion = 'P';
        $titulo = 'Consolidado';
        $this->objParam->addParametro('orientacion', $orientacion);
        $this->objParam->addParametro('tamano', $tamano);
        $this->objParam->addParametro('titulo_archivo', $titulo);
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        $reporte = new RClasificacionActivos($this->objParam);
        $reporte->datosHeader($datos->getDatos());
        $reporte->generarReporte();
        $reporte->output($reporte->url_archivo, 'F');
        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }
}

?>