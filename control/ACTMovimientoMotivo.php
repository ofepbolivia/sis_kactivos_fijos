<?php
/**
*@package pXP
*@file gen-ACTMovimientoMotivo.php
*@author  (admin)
*@date 18-03-2016 07:25:59
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__) . '/../reportes/RMovimientoMotivos.php');

class ACTMovimientoMotivo extends ACTbase{    
			
	function listarMovimientoMotivo(){
		$this->objParam->defecto('ordenacion','id_movimiento_motivo');
		$this->objParam->defecto('dir_ordenacion','asc');

		if($this->objParam->getParametro('id_cat_movimiento')!=''){
			$this->objParam->addFiltro("mmot.id_cat_movimiento = ".$this->objParam->getParametro('id_cat_movimiento'));	
		}

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODMovimientoMotivo','listarMovimientoMotivo');
		} else{
			$this->objFunc=$this->create('MODMovimientoMotivo');
			
			$this->res=$this->objFunc->listarMovimientoMotivo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarMovimientoMotivo(){
		$this->objFunc=$this->create('MODMovimientoMotivo');	
		if($this->objParam->insertar('id_movimiento_motivo')){
			$this->res=$this->objFunc->insertarMovimientoMotivo($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarMovimientoMotivo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarMovimientoMotivo(){
		$this->objFunc=$this->create('MODMovimientoMotivo');	
		$this->res=$this->objFunc->eliminarMovimientoMotivo($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    function generaReporteMotivos(){//fRnk: nuevo reporte Tipos de movimientos HR0341
        $nombreArchivo = 'Motivos' . uniqid(md5(session_id())) . '.pdf';
        $this->objFunc = $this->create('MODMovimientoMotivo');
        $datos = $this->objFunc->listarReporteMovimientoMotivo();
        $tamano = 'LETTER';
        $orientacion = 'P';
        $titulo = 'Consolidado';
        $this->objParam->addParametro('orientacion', $orientacion);
        $this->objParam->addParametro('tamano', $tamano);
        $this->objParam->addParametro('titulo_archivo', $titulo);
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        $reporte = new RMovimientoMotivos($this->objParam);
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