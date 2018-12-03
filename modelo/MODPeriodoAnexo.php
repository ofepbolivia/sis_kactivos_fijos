<?php
/**
*@package pXP
*@file gen-MODPeriodoAnexo.php
*@author  (ivaldivia)
*@date 19-10-2018 13:39:03
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODPeriodoAnexo extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarPeriodoAnexo(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='kaf.ft_periodo_anexo_sel';
		$this->transaccion='KAF_PERANE_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion



		//Definicion de la lista del resultado del query
		$this->captura('id_periodo_anexo','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('nombre_periodo','varchar');
		$this->captura('fecha_ini','date');
		$this->captura('fecha_fin','date');
		$this->captura('id_gestion','int4');
		$this->captura('observaciones','text');
		$this->captura('estado','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_gestion','int4');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarPeriodoAnexo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_periodo_anexo_ime';
		$this->transaccion='KAF_PERANE_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nombre_periodo','nombre_periodo','varchar');
		$this->setParametro('fecha_ini','fecha_ini','date');
		$this->setParametro('fecha_fin','fecha_fin','date');
		$this->setParametro('id_gestion','id_gestion','int4');
		$this->setParametro('observaciones','observaciones','text');		

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarPeriodoAnexo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_periodo_anexo_ime';
		$this->transaccion='KAF_PERANE_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nombre_periodo','nombre_periodo','varchar');
		$this->setParametro('fecha_ini','fecha_ini','date');
		$this->setParametro('fecha_fin','fecha_fin','date');
		$this->setParametro('id_gestion','id_gestion','int4');
		$this->setParametro('observaciones','observaciones','text');
		


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarPeriodoAnexo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_periodo_anexo_ime';
		$this->transaccion='KAF_PERANE_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarArchivoExcel(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_periodo_anexo_ime';
		$this->transaccion='KAFF_EXCEL_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function Finalizar(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_periodo_anexo_ime';
		$this->transaccion='KAFF_FINAL_IME';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarPartidaPeriodo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_periodo_anexo_ime';
		$this->transaccion='KAFF_PARPER_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function reporteAnexo1(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='kaf.ft_periodo_anexo_sel';
		$this->transaccion='KAF_REPORT1_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');

		$this->captura('id_anexo','int4');
		$this->captura('id_partida','int4');
		$this->captura('tipo_anexo','int4');
		$this->captura('id_periodo_anexo','int4');
		$this->captura('monto_contrato','numeric');
		$this->captura('observaciones','text');
		$this->captura('estado_reg','varchar');
		$this->captura('c31','varchar');
		$this->captura('monto_transito','numeric');
		$this->captura('monto_pagado','numeric');
		$this->captura('detalle_c31','text');
		$this->captura('monto_alta','numeric');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_uo','int4');
		$this->captura('desc_codigo','varchar');
		$this->captura('desc_nombre','varchar');
		$this->captura('monto_tercer','numeric');
		$this->captura('nombre_unidad','varchar');
		$this->captura('control','varchar');
		$this->captura('seleccionado','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		//var_dump( $this->respuesta);exit;
		return $this->respuesta;

	}

	function reporteAnexo2(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='kaf.ft_periodo_anexo_sel';
		$this->transaccion='KAF_REPORT2_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');

		$this->captura('id_anexo','int4');
		$this->captura('id_partida','int4');
		$this->captura('tipo_anexo','int4');
		$this->captura('id_periodo_anexo','int4');
		$this->captura('monto_sigep','numeric');
		$this->captura('observaciones','text');
		$this->captura('estado_reg','varchar');
		$this->captura('diferencia','numeric');
		$this->captura('c31','varchar');
		$this->captura('monto_erp','numeric');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_codigo','varchar');
		$this->captura('desc_nombre','varchar');
		$this->captura('control','varchar');
		$this->captura('seleccionado','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		//var_dump( $this->respuesta);exit;
		return $this->respuesta;

	}

	function reporteAnexo3(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='kaf.ft_periodo_anexo_sel';
		$this->transaccion='KAF_REPORT3_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');

		$this->captura('id_anexo','int4');
		$this->captura('id_partida','int4');
		$this->captura('tipo_anexo','int4');
		$this->captura('id_periodo_anexo','int4');
		$this->captura('monto_erp','numeric');
		$this->captura('estado_reg','varchar');
		$this->captura('c31','varchar');
		$this->captura('detalle_c31','text');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('id_uo','int4');
		$this->captura('desc_codigo','varchar');
		$this->captura('desc_nombre','varchar');
		$this->captura('nombre_unidad','varchar');
		$this->captura('control','varchar');
		$this->captura('seleccionado','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		//var_dump( $this->respuesta);exit;
		return $this->respuesta;

	}

	function reporteAnexo4(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='kaf.ft_periodo_anexo_sel';
		$this->transaccion='KAF_REPORT4_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');

		$this->captura('id_anexo','int4');
		$this->captura('id_partida','int4');
		$this->captura('tipo_anexo','int4');
		$this->captura('id_periodo_anexo','int4');
		$this->captura('monto_sigep','numeric');
		$this->captura('observaciones','text');
		$this->captura('estado_reg','varchar');
		$this->captura('diferencia','numeric');
		$this->captura('c31','varchar');
		$this->captura('monto_erp','numeric');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_codigo','varchar');
		$this->captura('desc_nombre','varchar');
		$this->captura('control','varchar');
		$this->captura('seleccionado','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		//var_dump( $this->respuesta);exit;
		return $this->respuesta;

	}

	function reporteGeneral(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='kaf.ft_periodo_anexo_sel';
		$this->transaccion='KAF_REPORTGE_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');


		$this->captura('id_partida_periodo','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_periodo_anexo','int4');
		$this->captura('id_partida','int4');
		$this->captura('importe_sigep','numeric');
		$this->captura('importe_anexo1','numeric');
		$this->captura('importe_anexo2','numeric');
		$this->captura('importe_anexo3','numeric');
		$this->captura('importe_anexo4','numeric');
		$this->captura('importe_anexo5','numeric');
		$this->captura('importe_total','numeric');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_partida','varchar');
		$this->captura('desc_codigo','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		//var_dump( $this->respuesta);exit;
		return $this->respuesta;

	}

}
?>
