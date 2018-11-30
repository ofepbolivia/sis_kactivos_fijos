<?php
/**
*@package pXP
*@file gen-ACTPartidaPeriodo.php
*@author  (ivaldivia)
*@date 19-10-2018 14:37:17
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTPartidaPeriodo extends ACTbase{

	function listarPartidaPeriodo(){
		$this->objParam->defecto('ordenacion','id_partida_periodo');

		$this->objParam->defecto('dir_ordenacion','asc');

		if($this->objParam->getParametro('id_periodo_anexo')!=''){
		 $this->objParam->addFiltro("parper.id_periodo_anexo = ".$this->objParam->getParametro('id_periodo_anexo'));
	 }


		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPartidaPeriodo','listarPartidaPeriodo');
		} else{
			$this->objFunc=$this->create('MODPartidaPeriodo');
			if ($this->objParam->getParametro('id_periodo_anexo') != '') {
					$this->res=$this->objFunc->listarPartidaPeriodo($this->objParam);
					$temp = Array();
					$temp['total_sigep'] = $this->res->extraData['total_sigep'];
					$temp['total_anex1'] = $this->res->extraData['total_anex1'];
					$temp['total_anex2'] = $this->res->extraData['total_anex2'];
					$temp['total_anex3'] = $this->res->extraData['total_anex3'];
					$temp['total_anex4'] = $this->res->extraData['total_anex4'];
					$temp['total_anex5'] = $this->res->extraData['total_anex5'];
					$temp['total_importe'] = $this->res->extraData['total_importe'];
					$temp['tipo_reg'] = 'summary';
					//$temp['id_deposito'] = 0;
					$this->res->total++;
					$this->res->addLastRecDatos($temp);

			}else{
						$this->res=$this->objFunc->listarPartidaPeriodo($this->objParam);

			}

		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarPartidaPeriodo(){
		$this->objFunc=$this->create('MODPartidaPeriodo');

		if($this->objParam->insertar('id_partida_periodo')){
			$this->res=$this->objFunc->insertarPartidaPeriodo($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarPartidaPeriodo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarPartidaPeriodo(){
			$this->objFunc=$this->create('MODPartidaPeriodo');
		$this->res=$this->objFunc->eliminarPartidaPeriodo($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

}

?>
