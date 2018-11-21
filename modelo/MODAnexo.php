<?php
/**
*@package pXP
*@file gen-MODAnexo.php
*@author  (ivaldivia)
*@date 22-10-2018 13:08:18
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODAnexo extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarAnexo(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='kaf.ft_anexo_sel';
		$this->transaccion='KAF_ANEX_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->capturaCount('total_contrato','numeric');
		$this->capturaCount('total_transito','numeric');
		$this->capturaCount('total_pagado','numeric');
		$this->capturaCount('total_erp','numeric');
		$this->capturaCount('total_tercer','numeric');


		//Definicion de la lista del resultado del query
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
		$this->captura('monto_erp','numeric');
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
		$this->captura('monto_tercer','numeric');
		$this->captura('nombre_unidad','varchar');
		$this->captura('control','varchar');
		$this->captura('seleccionado','varchar');
		$this->captura('monto_alta','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();		
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	function listarAnexo1(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='kaf.ft_anexo_sel';
		$this->transaccion='KAF_ANEX1_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->capturaCount('total_sigep','numeric');
		$this->capturaCount('total_erp','numeric');
		$this->capturaCount('total_diferencia','numeric');

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
		return $this->respuesta;
	}
	function MoverAnexo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_anexo_ime';
		$this->transaccion='KAF_MOVER_MOD';
		$this->tipo_procedimiento='IME';


		//Define los parametros para la funcion
		$this->setParametro('id_anexo','id_anexo','int4');
		$this->setParametro('tipo_anexo','tipo_anexo','int4');
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('monto_contrato','monto_contrato','numeric');
		$this->setParametro('monto_tercer','monto_tercer','numeric');
		$this->setParametro('monto_alta','monto_alta','numeric');
		$this->setParametro('monto_erp','monto_erp','numeric');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarAnexo2(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='kaf.ft_anexo_sel';
		$this->transaccion='KAF_ANEX2_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->capturaCount('total_erp','numeric');

		$this->captura('id_anexo','int4');
		$this->captura('id_partida','int4');
		$this->captura('tipo_anexo','int4');
		$this->captura('id_periodo_anexo','int4');
		$this->captura('monto_sigep','numeric');
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
		$this->captura('monto_erp','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarAnexo3(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='kaf.ft_anexo_sel';
		$this->transaccion='KAF_ANEX3_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->capturaCount('total_sigep','numeric');
		$this->capturaCount('total_erp','numeric');
		$this->capturaCount('total_diferencia','numeric');


		//Definicion de la lista del resultado del query
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
		return $this->respuesta;
	}



	function insertarAnexo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_anexo_ime';
		$this->transaccion='KAF_ANEX_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('tipo_anexo','tipo_anexo','int4');
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');
		$this->setParametro('monto_contrato','monto_contrato','numeric');
		$this->setParametro('observaciones','observaciones','text');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('c31','c31','varchar');
		$this->setParametro('monto_transito','monto_transito','numeric');
		$this->setParametro('monto_pagado','monto_pagado','numeric');
		$this->setParametro('detalle_c31','detalle_c31','text');
		$this->setParametro('monto_alta','monto_alta','numeric');
		$this->setParametro('id_uo','id_uo','int4');
		$this->setParametro('monto_tercer','monto_tercer','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	function insertarAnexo1(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_anexo_ime';
		$this->transaccion='KAF_ANEX1_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('tipo_anexo','tipo_anexo','int4');
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');
		$this->setParametro('monto_sigep','monto_sigep','numeric');
		$this->setParametro('observaciones','observaciones','text');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('diferencia','diferencia','numeric');
		$this->setParametro('c31','c31','varchar');
		$this->setParametro('monto_erp','monto_erp','numeric');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarAnexo2(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_anexo_ime';
		$this->transaccion='KAF_ANEX2_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('tipo_anexo','tipo_anexo','int4');
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');
		$this->setParametro('monto_erp','monto_erp','numeric');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('c31','c31','varchar');
		$this->setParametro('detalle_c31','detalle_c31','text');
		$this->setParametro('id_uo','id_uo','int4');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}
	function insertarAnexo3(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_anexo_ime';
		$this->transaccion='KAF_ANEX3_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('tipo_anexo','tipo_anexo','int4');
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');
		$this->setParametro('monto_sigep','monto_sigep','numeric');
		$this->setParametro('observaciones','observaciones','text');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('diferencia','diferencia','numeric');
		$this->setParametro('c31','c31','varchar');
		$this->setParametro('monto_erp','monto_erp','numeric');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarAnexo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_anexo_ime';
		$this->transaccion='KAF_ANEX_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_anexo','id_anexo','int4');
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('tipo_anexo','tipo_anexo','int4');
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');
		$this->setParametro('monto_contrato','monto_contrato','numeric');
		$this->setParametro('observaciones','observaciones','text');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('c31','c31','varchar');
		$this->setParametro('monto_transito','monto_transito','numeric');
		$this->setParametro('monto_pagado','monto_pagado','numeric');
		$this->setParametro('detalle_c31','detalle_c31','text');
		$this->setParametro('monto_alta','monto_alta','numeric');
		$this->setParametro('id_uo','id_uo','int4');
		$this->setParametro('monto_tercer','monto_tercer','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}
	function modificarAnexo1(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_anexo_ime';
		$this->transaccion='KAF_ANEX1_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->procedimiento='kaf.ft_anexo_ime';
		$this->transaccion='KAF_ANEX1_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_anexo','id_anexo','int4');
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('tipo_anexo','tipo_anexo','int4');
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');
		$this->setParametro('monto_sigep','monto_sigep','numeric');
		$this->setParametro('observaciones','observaciones','text');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('diferencia','diferencia','numeric');
		$this->setParametro('c31','c31','varchar');
		$this->setParametro('monto_erp','monto_erp','numeric');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarAnexo2(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_anexo_ime';
		$this->transaccion='KAF_ANEX2_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->procedimiento='kaf.ft_anexo_ime';
		$this->transaccion='KAF_ANEX2_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_anexo','id_anexo','int4');
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('tipo_anexo','tipo_anexo','int4');
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');
		$this->setParametro('monto_erp','monto_erp','numeric');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('c31','c31','varchar');
		$this->setParametro('detalle_c31','detalle_c31','text');
		$this->setParametro('id_uo','id_uo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	function modificarAnexo3(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_anexo_ime';
		$this->transaccion='KAF_ANEX3_MOD';
		$this->tipo_procedimiento='IME';


		//Define los parametros para la funcion
		$this->setParametro('id_anexo','id_anexo','int4');
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('tipo_anexo','tipo_anexo','int4');
		$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');
		$this->setParametro('monto_sigep','monto_sigep','numeric');
		$this->setParametro('observaciones','observaciones','text');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('diferencia','diferencia','numeric');
		$this->setParametro('c31','c31','varchar');
		$this->setParametro('monto_erp','monto_erp','numeric');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}


	function eliminarAnexo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_anexo_ime';
		$this->transaccion='KAF_ANEX_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_anexo','id_anexo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	function insertarAnexos	(){
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento='kaf.ft_anexo_ime';
			$this->transaccion='KAF_GENANEXOS_INS';
			$this->tipo_procedimiento='IME';

			$this->setParametro('id_periodo_anexo','id_periodo_anexo','int4');
			$this->setParametro('fecha_ini','fecha_ini','date');
			$this->setParametro('fecha_fin','fecha_fin','date');
			$this->setParametro('id_gestion','id_gestion','int4');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}
	function controlSeleccionado()
	{
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento = 'kaf.ft_anexo_ime';
			$this->transaccion = 'KAF_CONTROL_CON';
			$this->tipo_procedimiento = 'IME';

			//Define los parametros para la funcion
			$this->setParametro('id_anexo', 'id_anexo', 'int4');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}
	function agruparAnexo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_anexo_ime';
		$this->transaccion='KAFF_AGRUP_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_anexo','id_anexo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

}
?>
