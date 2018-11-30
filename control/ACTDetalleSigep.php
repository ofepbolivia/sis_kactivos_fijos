<?php
/**
*@package pXP
*@file gen-ACTDetalleSigep.php
*@author  (ivaldivia)
*@date 25-10-2018 15:35:31
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTDetalleSigep extends ACTbase{

	function listarDetalleSigep(){
		$this->objParam->defecto('ordenacion','id_detalle_sigep');

		$this->objParam->defecto('dir_ordenacion','asc');

		//var_dump($this->objParam->getParametro('excel'));
   	if ($this->objParam->getParametro('excel') == 'especifico') {
		 $this->objParam->addFiltro("detsig.id_periodo_anexo = ". $this->objParam->getParametro('id_periodo_anexo'));
	 	}


		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODDetalleSigep','listarDetalleSigep');
		} else{
			$this->objFunc=$this->create('MODDetalleSigep');
			if ($this->objParam->getParametro('id_periodo_anexo') != '') {
					$this->res=$this->objFunc->listarDetalleSigep($this->objParam);
					$temp = Array();
					$temp['total_sigep'] = $this->res->extraData['total_sigep'];
					$temp['tipo_reg'] = 'summary';
					//$temp['id_deposito'] = 0;
					$this->res->total++;
					$this->res->addLastRecDatos($temp);

			}else{
						$this->res=$this->objFunc->listarDetalleSigep($this->objParam);

			}

		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarDetalleSigep(){
		$this->objFunc=$this->create('MODDetalleSigep');
		if($this->objParam->insertar('id_detalle_sigep')){
			$this->res=$this->objFunc->insertarDetalleSigep($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarDetalleSigep($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarDetalleSigep(){
			$this->objFunc=$this->create('MODDetalleSigep');
		$this->res=$this->objFunc->eliminarDetalleSigep($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

}

?>
