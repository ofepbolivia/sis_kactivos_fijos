<?php
/**
*@package pXP
*@file gen-MODDetalleSigep.php
*@author  (ivaldivia)
*@date 25-10-2018 15:35:31
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODDetalleSigep extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarDetalleSigep(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='kaf.ft_detalle_sigep_sel';
		$this->transaccion='KAF_DETSIG_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->capturaCount('total_sigep','numeric');
		//Definicion de la lista del resultado del query
		$this->captura('id_detalle_sigep','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('nro_partida','varchar');
		$this->captura('c31','varchar');
		$this->captura('monto_sigep','numeric');
		$this->captura('id_periodo_anexo','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarDetalleSigep(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_detalle_sigep_ime';
		$this->transaccion='KAF_DETSIG_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nro_partida','nro_partida','varchar');
		$this->setParametro('c31','c31','varchar');
		$this->setParametro('monto_sigep','monto_sigep','numeric');
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarDetalleSigep(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_detalle_sigep_ime';
		$this->transaccion='KAF_DETSIG_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_detalle_sigep','id_detalle_sigep','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nro_partida','nro_partida','varchar');
		$this->setParametro('c31','c31','varchar');
		$this->setParametro('monto_sigep','monto_sigep','numeric');
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarDetalleSigep(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_detalle_sigep_ime';
		$this->transaccion='KAF_DETSIG_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_detalle_sigep','id_detalle_sigep','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	
	function repDetalleSigep(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_detalle_sigep_sel';
		$this->transaccion='KAF_REPDETSI_SEL';
		$this->tipo_procedimiento='SEL';
		$this->setCount(false);

		//Define los parametros para la funcion
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');
		
		//Definicion de la lista del resultado del query			
		$this->captura('nro_partida','varchar');
		$this->captura('c31','varchar');		
		$this->captura('monto_sigep','numeric');		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;		
	}

}
?>
