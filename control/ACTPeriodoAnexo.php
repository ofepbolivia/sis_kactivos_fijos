<?php
/**
*@package pXP
*@file gen-ACTPeriodoAnexo.php
*@author  (ivaldivia)
*@date 19-10-2018 13:39:03
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__).'/../reportes/RReporteAnexo1.php');
require_once(dirname(__FILE__).'/../reportes/RReporteAnexo2.php');
require_once(dirname(__FILE__).'/../reportes/RReporteAnexo3.php');
require_once(dirname(__FILE__).'/../reportes/RReporteAnexo4.php');
require_once(dirname(__FILE__).'/../reportes/RReporteAnexoGeneral.php');
require_once(dirname(__FILE__).'/../reportes/RReporteAnexoGeneralPDF.php');
include_once(dirname(__FILE__).'/../../lib/lib_general/ExcelInput.php');
class ACTPeriodoAnexo extends ACTbase{

	function listarPeriodoAnexo(){
		$this->objParam->defecto('ordenacion','id_periodo_anexo');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPeriodoAnexo','listarPeriodoAnexo');
		} else{
			$this->objFunc=$this->create('MODPeriodoAnexo');

			$this->res=$this->objFunc->listarPeriodoAnexo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function listarPlantillaArchivoExcel(){
			$this->objParam->defecto('ordenacion','id_plantilla_archivo_excel');

			$this->objParam->defecto('dir_ordenacion','asc');

			if($this->objParam->getParametro('archivoPer') == 'SIGEPAF'){
					$this->objParam->addFiltro(" arxls.codigo in(''SIGEPAF'')");
			}
			if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
					$this->objReporte = new Reporte($this->objParam,$this);
					$this->res = $this->objReporte->generarReporteListado('sis_parametros/MODPlantillaArchivoExcel','listarPlantillaArchivoExcel');
			} else{
					$this->objFunc=$this->create('sis_parametros/MODPlantillaArchivoExcel');

					$this->res=$this->objFunc->listarPlantillaArchivoExcel($this->objParam);
			}

			$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarPeriodoAnexo(){
		$this->objFunc=$this->create('MODPeriodoAnexo');
		if($this->objParam->insertar('id_periodo_anexo')){
			$this->res=$this->objFunc->insertarPeriodoAnexo($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarPeriodoAnexo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarPeriodoAnexo(){
			$this->objFunc=$this->create('MODPeriodoAnexo');
		$this->res=$this->objFunc->eliminarPeriodoAnexo($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarArchivoExcel(){
		//var_dump($this->objParam->getParametro('id_archivo_acm'));
			$this->objFunc=$this->create('MODPeriodoAnexo');
			$this->res=$this->objFunc->eliminarArchivoExcel($this->objParam);
			$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function Finalizar(){
		//var_dump($this->objParam->getParametro('id_archivo_acm'));
			$this->objFunc=$this->create('MODPeriodoAnexo');
			$this->res=$this->objFunc->Finalizar($this->objParam);
			$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarPartidaPeriodo(){
		//var_dump($this->objParam->getParametro('id_archivo_acm'));
			$this->objFunc=$this->create('MODPeriodoAnexo');
			$this->res=$this->objFunc->insertarPartidaPeriodo($this->objParam);
			$this->res->imprimirRespuesta($this->res->generarJson());
	}


	function cargarArchivoPERIODOExcel(){
			//validar extnsion del archivo
			$id_periodo_anexo = $this->objParam->getParametro('id_periodo_anexo');

			$codigoArchivo = $this->objParam->getParametro('codigo');
			//echo "llega";

	//        echo "que es: $id_archivo_acm";

			$arregloFiles = $this->objParam->getArregloFiles();
			$ext = pathinfo($arregloFiles['archivo']['name']);
			$extension = $ext['extension'];

			$error = 'no';
			$mensaje_completo = '';
			//validar errores unicos del archivo: existencia, copia y extension
			if(isset($arregloFiles['archivo']) && is_uploaded_file($arregloFiles['archivo']['tmp_name'])){
					/*
											if (!in_array($extension, array('xls','xlsx','XLS','XLSX'))){
													$mensaje_completo = "La extensión del archivo debe ser XLS o XLSX";
													$error = 'error_fatal';
											}else {*/
					//procesa Archivo
					$archivoExcel = new ExcelInput($arregloFiles['archivo']['tmp_name'], $codigoArchivo);
					$archivoExcel->recuperarColumnasExcel();

					$arrayArchivo = $archivoExcel->leerColumnasArchivoExcel();
					//var_dump($arrayArchivo);
					foreach ($arrayArchivo as $fila) {
						$this->objParam->addParametro('estado_reg', '');
						$this->objParam->addParametro('nro_partida', $fila['nro_partida'] == NULL ? '' : $fila['nro_partida']);
						$this->objParam->addParametro('c31', $fila['c31'] == NULL ? '' : $fila['c31']);
						$this->objParam->addParametro('monto_sigep', $fila['monto_sigep'] == NULL ? '' : $fila['monto_sigep']);
						$this->objParam->addParametro('id_periodo_anexo', $id_periodo_anexo);
						$this->objFunc = $this->create('sis_kactivos_fijos/MODDetalleSigep');
						$this->res = $this->objFunc->insertarDetalleSigep($this->objParam);

							if($this->res->getTipo()=='ERROR'){
									$error = 'error';
									$mensaje_completo = "Error al guardar el fila en tabla ". $this->res->getMensajeTec();
							}
					}

					//upload directory
					$upload_dir = "/tmp/";
					//create file name
					$file_path = $upload_dir . $arregloFiles['archivo']['name'];

					//move uploaded file to upload dir
					if (!move_uploaded_file($arregloFiles['archivo']['tmp_name'], $file_path)) {
							//error moving upload file
							$mensaje_completo = "Error al guardar el archivo PERIODO ANEXO en disco";
							$error = 'error_fatal';
					}
					// }
			} else {
					$mensaje_completo = "No se subio el archivo";
					$error = 'error_fatal';
			}
			//armar respuesta en error fatal
			if ($error == 'error_fatal') {

					$this->mensajeRes=new Mensaje();
					$this->mensajeRes->setMensaje('ERROR','ACTColumnaCalor.php',$mensaje_completo, $mensaje_completo,'control');
					//si no es error fatal proceso el archivo
			} else {
					$lines = file($file_path);
					/*
											foreach ($lines as $line_num => $line) {
													$arr_temp = explode('|', $line);

													if (count($arr_temp) != 2) {
															$error = 'error';
															$mensaje_completo .= "No se proceso la linea: $line_num, por un error en el formato \n";

													} else {
															$this->objParam->addParametro('numero',$arr_temp[0]);
															$this->objParam->addParametro('monto',$arr_temp[1]);
															$this->objFunc=$this->create('MODConsumo');
															$this->res=$this->objFunc->modificarConsumoCsv($this->objParam);

															if ($this->res->getTipo() == 'ERROR') {
																	$error = 'error';
																	$mensaje_completo .= $this->res->getMensaje() . " \n";
															}
													}
											}*/
			}
			//armar respuesta en caso de exito o error en algunas tuplas
			if ($error == 'error') {
					$this->mensajeRes=new Mensaje();
					$this->mensajeRes->setMensaje('ERROR','ACTPartidaPeriodo.php','Ocurrieron los siguientes errores : ' . $mensaje_completo,	$mensaje_completo,'control');
					/*
					$this->mensajeRes=new Mensaje();
					$this->mensajeRes->setMensaje($this->res);
					$this->mensajeRes->setMensaje($mensaje_completo,$this->nombre_archivo,$this->res->getMensaje(),$this->res->getMensajeTecnico(),'base',$this->res->getProcedimiento(),$this->res->getTransaccion(),$this->res->getTipoProcedimiento,$respuesta['consulta']);
					$this->mensajeRes->setDatos($respuesta);
					$this->res->imprimirRespuesta($this->respuesta->generarJson());
					*/
			} else if ($error == 'no') {
					$this->mensajeRes=new Mensaje();
					$this->mensajeRes->setMensaje('EXITO','ACTPartidaPeriodo.php','El archivo fue ejecutado con éxito','El archivo fue ejecutado con éxito','control');
			}

			//devolver respuesta
			$this->mensajeRes->imprimirRespuesta($this->mensajeRes->generarJson());
			//return $this->respuesta;
	}

  function reporteAnexo1(){
        if($this->objParam->getParametro('id_periodo_anexo') != ''){
            $this->objParam->addFiltro("anex.id_periodo_anexo = ". $this->objParam->getParametro('id_periodo_anexo'));
//            var_dump($this->objParam->getParametro('id_archivo_acm'));exit;
        }


        $this->objFunc=$this->create('MODPeriodoAnexo');
        $this->res=$this->objFunc->reporteAnexo1($this->objParam);
        //obtener titulo de reporte
         //var_dump($this->res);exit;
        $titulo ='ANEXO 1';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo=uniqid(md5(session_id()).$titulo);
        $nombreArchivo.='.xls';
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('datos',$this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato=new RReporteAnexo1($this->objParam);
        $this->objReporteFormato->generarDatos();
        $this->objReporteFormato->generarReporte();

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

		function reporteAnexo2(){
	        if($this->objParam->getParametro('id_periodo_anexo') != ''){
	            $this->objParam->addFiltro("anex.id_periodo_anexo = ". $this->objParam->getParametro('id_periodo_anexo'));
	//            var_dump($this->objParam->getParametro('id_archivo_acm'));exit;
	        }


	        $this->objFunc=$this->create('MODPeriodoAnexo');
	        $this->res=$this->objFunc->reporteAnexo2($this->objParam);
	        //obtener titulo de reporte
	         //var_dump($this->res);exit;
	        $titulo ='ANEXO 2';
	        //Genera el nombre del archivo (aleatorio + titulo)
	        $nombreArchivo=uniqid(md5(session_id()).$titulo);
	        $nombreArchivo.='.xls';
	        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
	        $this->objParam->addParametro('datos',$this->res->datos);
	        //Instancia la clase de excel
	        $this->objReporteFormato=new RReporteAnexo2($this->objParam);
	        $this->objReporteFormato->generarDatos();
	        $this->objReporteFormato->generarReporte();

	        $this->mensajeExito=new Mensaje();
	        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
	            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
	        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
	        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

	    }

			function reporteAnexo3(){
		        if($this->objParam->getParametro('id_periodo_anexo') != ''){
		            $this->objParam->addFiltro("anex.id_periodo_anexo = ". $this->objParam->getParametro('id_periodo_anexo'));
		//            var_dump($this->objParam->getParametro('id_archivo_acm'));exit;
		        }

		        $this->objFunc=$this->create('MODPeriodoAnexo');
		        $this->res=$this->objFunc->reporteAnexo3($this->objParam);
		        //obtener titulo de reporte
		         //var_dump($this->res);exit;
		        $titulo ='ANEXO 3';
		        //Genera el nombre del archivo (aleatorio + titulo)
		        $nombreArchivo=uniqid(md5(session_id()).$titulo);
		        $nombreArchivo.='.xls';
		        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
		        $this->objParam->addParametro('datos',$this->res->datos);
		        //Instancia la clase de excel
		        $this->objReporteFormato=new RReporteAnexo3($this->objParam);
		        $this->objReporteFormato->generarDatos();
		        $this->objReporteFormato->generarReporte();

		        $this->mensajeExito=new Mensaje();
		        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
		            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
		        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
		        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

		    }

				function reporteAnexo4(){
			        if($this->objParam->getParametro('id_periodo_anexo') != ''){
			            $this->objParam->addFiltro("anex.id_periodo_anexo = ". $this->objParam->getParametro('id_periodo_anexo'));
			//            var_dump($this->objParam->getParametro('id_archivo_acm'));exit;
			        }

			        $this->objFunc=$this->create('MODPeriodoAnexo');
			        $this->res=$this->objFunc->reporteAnexo4($this->objParam);
			        //obtener titulo de reporte
			         //var_dump($this->res);exit;
			        $titulo ='ANEXO 4';
			        //Genera el nombre del archivo (aleatorio + titulo)
			        $nombreArchivo=uniqid(md5(session_id()).$titulo);
			        $nombreArchivo.='.xls';
			        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
			        $this->objParam->addParametro('datos',$this->res->datos);
			        //Instancia la clase de excel
			        $this->objReporteFormato=new RReporteAnexo4($this->objParam);
			        $this->objReporteFormato->generarDatos();
			        $this->objReporteFormato->generarReporte();

			        $this->mensajeExito=new Mensaje();
			        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
			            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
			        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
			        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

			    }

					function reporteGeneral(){
						$this->objParam->addParametro('tipo', 'detalle');
						$this->objFunc = $this->create('MODPeriodoAnexo');
						$this->res = $this->objFunc->reporteGeneral($this->objParam);
						$this->objParam->addParametro('informe', $this->res->datos);						


						$this->objParam->addParametro('tipo', 'resumen');
						$this->objFunc = $this->create('MODPeriodoAnexo');
						$this->res = $this->objFunc->reporteAnexo1($this->objParam);
						$this->objParam->addParametro('anexo1', $this->res->datos);


						$this->objParam->addParametro('tipo', 'anexo');
						$this->objFunc = $this->create('MODPeriodoAnexo');
						$this->res = $this->objFunc->reporteAnexo2($this->objParam);
						$this->objParam->addParametro('anexo2', $this->res->datos);

						$this->objParam->addParametro('tipo', 'anexo2');
						$this->objFunc = $this->create('MODPeriodoAnexo');
						$this->res = $this->objFunc->reporteAnexo3($this->objParam);
						$this->objParam->addParametro('anexo3', $this->res->datos);

						$this->objParam->addParametro('tipo', 'anexo2');
						$this->objFunc = $this->create('MODPeriodoAnexo');
						$this->res = $this->objFunc->reporteAnexo4($this->objParam);
						$this->objParam->addParametro('anexo4', $this->res->datos);

						//obtener titulo del reporte
						$titulo = 'RepRporteGeneralAnexoSigep';

						//Genera el nombre del archivo (aleatorio + titulo)
						$nombreArchivo = uniqid(md5(session_id()) . $titulo);
												
						if ($this->objParam->getParametro('def')=='pdf'){
							$nombreArchivo .= '.pdf';
							$this->objParam->addParametro('nombre_archivo', $nombreArchivo);
							$this->objParam->addParametro('tamano','LETTER');
							$this->objParam->addParametro('orientacion','L');							
	
							$this->objReporteFormato = new RReporteAnexoGeneralPDF($this->objParam);
							$this->objReporteFormato->imprimeInforme();
							$this->objReporteFormato->imprimeAnexo1();
							$this->objReporteFormato->imprimeAnexo2();
							$this->objReporteFormato->imprimeAnexo3();
							$this->objReporteFormato->imprimeAnexo4();							
						}else{
							$nombreArchivo .= '.xls';
							$this->objParam->addParametro('nombre_archivo', $nombreArchivo);
	
							$this->objReporteFormato = new RReporteAnexoGeneral($this->objParam);
							$this->objReporteFormato->imprimeInforme();
							$this->objReporteFormato->imprimeAnexo1();
							$this->objReporteFormato->imprimeAnexo2();
							$this->objReporteFormato->imprimeAnexo3();
							$this->objReporteFormato->imprimeAnexo4();
						}

						$this->objReporteFormato->generarReporte();
						$this->mensajeExito = new Mensaje();
						$this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
								'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');

						$this->mensajeExito->setArchivoGenerado($nombreArchivo);
						$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
				}

}

?>
