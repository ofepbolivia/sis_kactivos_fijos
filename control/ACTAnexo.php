<?php
/**
*@package pXP
*@file gen-ACTAnexo.php
*@author  (ivaldivia)
*@date 22-10-2018 13:08:18
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__).'/../reportes/RReporteAnexo1.php');
include_once(dirname(__FILE__).'/../../lib/lib_general/ExcelInput.php');
class ACTAnexo extends ACTbase{

	function listarAnexo(){
		//$this->objParam->defecto('ordenacion','codigo');

		$this->objParam->defecto('dir_ordenacion','asc');

		if ($this->objParam->getParametro('an1') == 'especifico') {
	 $this->objParam->addFiltro("anex.tipo_anexo=1 and anex.id_periodo_anexo = ". $this->objParam->getParametro('id_periodo_anexo'));
	}


		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODAnexo','listarAnexo');
		}  else{
			$this->objFunc=$this->create('MODAnexo');
			if ($this->objParam->getParametro('id_periodo_anexo') != '') {
					$this->res=$this->objFunc->listarAnexo($this->objParam);
					$temp = Array();
					$temp['total_transito'] = $this->res->extraData['total_transito'];
					$temp['total_pagado'] = $this->res->extraData['total_pagado'];
					$temp['total_erp'] = $this->res->extraData['total_erp'];
					$temp['total_tercer'] = $this->res->extraData['total_tercer'];
					$temp['total_contrato'] = $this->res->extraData['total_contrato'];
					$temp['tipo_reg'] = 'summary';
					//$temp['id_deposito'] = 0;
					$this->res->total++;
					$this->res->addLastRecDatos($temp);

			}else{
						$this->res=$this->objFunc->listarAnexo($this->objParam);

			}

		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function MoverAnexo(){
		$this->objFunc=$this->create('MODAnexo');
		if($this->objParam->insertar('id_anexo')){
			$this->res=$this->objFunc->MoverAnexo($this->objParam);
		} else{
			$this->res=$this->objFunc->MoverAnexo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}


	function listarAnexo1(){
		//$this->objParam->defecto('ordenacion','id_anexo');

		$this->objParam->defecto('dir_ordenacion','asc');

		if ($this->objParam->getParametro('an2') == 'especifico') {
	 $this->objParam->addFiltro("anex.tipo_anexo=2 and anex.id_periodo_anexo = ". $this->objParam->getParametro('id_periodo_anexo'));
	}
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODAnexo','listarAnexo1');
		} else{
			$this->objFunc=$this->create('MODAnexo');
			if ($this->objParam->getParametro('id_periodo_anexo') != '') {
					$this->res=$this->objFunc->listarAnexo1($this->objParam);
					$temp = Array();
					$temp['total_sigep'] = $this->res->extraData['total_sigep'];
					$temp['total_erp'] = $this->res->extraData['total_erp'];
					$temp['total_diferencia'] = $this->res->extraData['total_diferencia'];
					$temp['tipo_reg'] = 'summary';
					//$temp['id_deposito'] = 0;
					$this->res->total++;
					$this->res->addLastRecDatos($temp);

			}else{
						$this->res=$this->objFunc->listarAnexo1($this->objParam);

			}

		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function listarAnexo2(){
		//$this->objParam->defecto('ordenacion','id_anexo');

		$this->objParam->defecto('dir_ordenacion','asc');

		if ($this->objParam->getParametro('an3') == 'especifico') {
	 $this->objParam->addFiltro("anex.tipo_anexo=3 and anex.id_periodo_anexo = ". $this->objParam->getParametro('id_periodo_anexo'));
	}

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODAnexo','listarAnexo2');
		} else{
			$this->objFunc=$this->create('MODAnexo');
			if ($this->objParam->getParametro('id_periodo_anexo') != '') {
					$this->res=$this->objFunc->listarAnexo2($this->objParam);
					$temp = Array();
					$temp['total_erp'] = $this->res->extraData['total_erp'];
					$temp['tipo_reg'] = 'summary';
					//$temp['id_deposito'] = 0;
					$this->res->total++;
					$this->res->addLastRecDatos($temp);

			}else{
						$this->res=$this->objFunc->listarAnexo2($this->objParam);

			}

		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function listarAnexo3(){
		//$this->objParam->defecto('ordenacion','id_anexo');

		$this->objParam->defecto('dir_ordenacion','asc');

		if ($this->objParam->getParametro('an4') == 'especifico') {
	 $this->objParam->addFiltro("anex.tipo_anexo=4 and anex.id_periodo_anexo = ". $this->objParam->getParametro('id_periodo_anexo'));
	}

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODAnexo','listarAnexo3');
		} else{
			$this->objFunc=$this->create('MODAnexo');
			if ($this->objParam->getParametro('id_periodo_anexo') != '') {
					$this->res=$this->objFunc->listarAnexo3($this->objParam);
					$temp = Array();
					$temp['total_sigep'] = $this->res->extraData['total_sigep'];
					$temp['total_erp'] = $this->res->extraData['total_erp'];
					$temp['total_diferencia'] = $this->res->extraData['total_diferencia'];
					$temp['tipo_reg'] = 'summary';
					//$temp['id_deposito'] = 0;
					$this->res->total++;
					$this->res->addLastRecDatos($temp);

			}else{
						$this->res=$this->objFunc->listarAnexo3($this->objParam);

			}

		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}


	function insertarAnexo(){
		$this->objFunc=$this->create('MODAnexo');
		if($this->objParam->insertar('id_anexo')){
			$this->res=$this->objFunc->insertarAnexo($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarAnexo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarAnexo1(){
		$this->objFunc=$this->create('MODAnexo');
		if($this->objParam->insertar('id_anexo')){
			$this->res=$this->objFunc->insertarAnexo1($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarAnexo1($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarAnexo2(){
		$this->objFunc=$this->create('MODAnexo');
		if($this->objParam->insertar('id_anexo')){
			$this->res=$this->objFunc->insertarAnexo2($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarAnexo2($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarAnexo3(){
			$this->objFunc=$this->create('MODAnexo');
			if($this->objParam->insertar('id_anexo')){
				$this->res=$this->objFunc->insertarAnexo3($this->objParam);
			} else{
				$this->res=$this->objFunc->modificarAnexo3($this->objParam);
			}
			$this->res->imprimirRespuesta($this->res->generarJson());
		}

	function eliminarAnexo(){
			$this->objFunc=$this->create('MODAnexo');
		$this->res=$this->objFunc->eliminarAnexo($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	function generarAnexos(){
		$this->objFunc=$this->create('MODAnexo');
		$this->res=$this->objFunc->insertarAnexos($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function controlSeleccionado()	{
			$this->objFunc=$this->create('MODAnexo');
			$this->res=$this->objFunc->controlSeleccionado($this->objParam);
			$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function agruparAnexo(){
		//var_dump($this->objParam->getParametro('id_archivo_acm'));
			$this->objFunc=$this->create('MODAnexo');
			$this->res=$this->objFunc->agruparAnexo($this->objParam);
			$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	function generaAnexoGeneral(){
		$this->objFunc=$this->create('MODAnexo');
		$this->res=$this->objFunc->generaAnexoGeneral($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}	

}

?>
