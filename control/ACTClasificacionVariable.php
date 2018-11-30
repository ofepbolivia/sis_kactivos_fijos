<?php
/**
*@package pXP
*@file gen-ACTClasificacionVariable.php
*@author  (admin)
*@date 27-06-2017 09:34:29
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

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
			
}

?>