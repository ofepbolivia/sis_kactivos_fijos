<?php
/**
*@package pXP
*@file gen-MODPartidaPeriodo.php
*@author  (ivaldivia)
*@date 19-10-2018 14:37:17
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODPartidaPeriodo extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarPartidaPeriodo(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='kaf.ft_partida_periodo_sel';
		$this->transaccion='KAF_PARPER_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->capturaCount('total_sigep','numeric');
		$this->capturaCount('total_anex1','numeric');
		$this->capturaCount('total_anex2','numeric');
		$this->capturaCount('total_anex3','numeric');
		$this->capturaCount('total_anex4','numeric');
		$this->capturaCount('total_anex5','numeric');
		$this->capturaCount('total_importe','numeric');
		//Definicion de la lista del resultado del query
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
		$this->captura('nombre_partida','varchar');
		$this->captura('desc_codigo','varchar');
		$this->captura('nombre_periodo','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarPartidaPeriodo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_partida_periodo_ime';
		$this->transaccion='KAF_PARPER_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('importe_sigep','importe_sigep','numeric');
		$this->setParametro('importe_anexo1','importe_anexo1','numeric');
		$this->setParametro('importe_anexo2','importe_anexo2','numeric');
		$this->setParametro('importe_anexo3','importe_anexo3','numeric');
		$this->setParametro('importe_anexo4','importe_anexo4','numeric');
		$this->setParametro('importe_anexo5','importe_anexo5','numeric');
		$this->setParametro('importe_total','importe_total','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarPartidaPeriodo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_partida_periodo_ime';
		$this->transaccion='KAF_PARPER_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_partida_periodo','id_partida_periodo','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('importe_sigep','importe_sigep','numeric');
		$this->setParametro('importe_anexo1','importe_anexo1','numeric');
		$this->setParametro('importe_anexo2','importe_anexo2','numeric');
		$this->setParametro('importe_anexo3','importe_anexo3','numeric');
		$this->setParametro('importe_anexo4','importe_anexo4','numeric');
		$this->setParametro('importe_anexo5','importe_anexo5','numeric');
		$this->setParametro('importe_total','importe_total','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarPartidaPeriodo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_partida_periodo_ime';
		$this->transaccion='KAF_PARPER_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_partida_periodo','id_partida_periodo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

}
?>
