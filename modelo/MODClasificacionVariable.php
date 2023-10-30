<?php
/**
*@package pXP
*@file gen-MODClasificacionVariable.php
*@author  (admin)
*@date 27-06-2017 09:34:29
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODClasificacionVariable extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarClasificacionVariable(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='kaf.ft_clasificacion_variable_sel';
		$this->transaccion='SKA_CLAVAR_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_clasificacion_variable','int4');
		$this->captura('id_clasificacion','int4');
		$this->captura('nombre','varchar');
		$this->captura('tipo_dato','varchar');
		$this->captura('descripcion','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('obligatorio','varchar');
		$this->captura('orden_var','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
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
			
	function insertarClasificacionVariable(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_clasificacion_variable_ime';
		$this->transaccion='SKA_CLAVAR_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_clasificacion','id_clasificacion','int4');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('tipo_dato','tipo_dato','varchar');
		$this->setParametro('descripcion','descripcion','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('obligatorio','obligatorio','varchar');
		$this->setParametro('orden_var','orden_var','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarClasificacionVariable(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_clasificacion_variable_ime';
		$this->transaccion='SKA_CLAVAR_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_clasificacion_variable','id_clasificacion_variable','int4');
		$this->setParametro('id_clasificacion','id_clasificacion','int4');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('tipo_dato','tipo_dato','varchar');
		$this->setParametro('descripcion','descripcion','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('obligatorio','obligatorio','varchar');
		$this->setParametro('orden_var','orden_var','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarClasificacionVariable(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_clasificacion_variable_ime';
		$this->transaccion='SKA_CLAVAR_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_clasificacion_variable','id_clasificacion_variable','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	
	function listarClasificacionPartida(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='kaf.ft_clasificacion_variable_sel';
		$this->transaccion='SKA_CLASIPAR_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		
		$this->setParametro('id_clasificacion','id_clasificacion','int4');		
		//Definicion de la lista del resultado del query
		$this->captura('id_clasificacion_partida','int4');
		$this->captura('id_clasificacion','int4');
		$this->captura('id_partida','int4');
		$this->captura('id_gestion','int4');
		$this->captura('gestion','int4');		
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('dec_par','text');		
		$this->captura('tipo_reg','text');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;		
	}
	
	function insertarClasificacionPartida(){
		$this->procedimiento='kaf.ft_clasificacion_variable_ime';
		$this->transaccion='SKA_CLASIPAR_INS';
		$this->tipo_procedimiento='IME';
		$this->setParametro('id_clasificacion','id_clasificacion','int4');
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('id_gestion','id_gestion','int4');			

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;				
	}
	
	function modificarClasificacionPartida(){
		$this->procedimiento='kaf.ft_clasificacion_variable_ime';
		$this->transaccion='SKA_CLASIPAR_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_clasificacion_partida','id_clasificacion_partida','int4');
		$this->setParametro('id_clasificacion','id_clasificacion','int4');
		$this->setParametro('id_partida','id_partida','int4');
		$this->setParametro('id_gestion','id_gestion','int4');		

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;		
		
	}
	
	function eliminarClasificacionPartida(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_clasificacion_variable_ime';
		$this->transaccion='SKA_CLASIPAR_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_clasificacion_partida','id_clasificacion_partida','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	
	function listarPartidas(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_clasificacion_variable_sel';
        $this->transaccion='SKA_PARTID_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //Definicion de la lista del resultado del query
        //$this->setParametro('id_gestion','id_gestion','int4');
		       
        $this->captura('id_partida','int4');
        $this->captura('tipo','varchar');
		$this->captura('codigo','varchar');
		$this->captura('nombre_partida','varchar');
		$this->captura('id_gestion','int4');
		$this->captura('sw_movimiento','varchar');
		$this->captura('gestion','int4');		       
        //Ejecuta la instruccion
        $this->armarConsulta();        
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;		
	}
	
	function clonarClasificacionPartidaGestion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='kaf.ft_clasificacion_variable_ime';
		$this->transaccion='KAF_CLONAR_IME';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_gestion','id_gestion','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;		
	}

    function listarClasificacionPartidas(){//fRnk: nuevo reporte
        $this->procedimiento='kaf.ft_clasificacion_variable_sel';
        $this->transaccion='SKA_CLASIFPAR_SEL';
        $this->tipo_procedimiento='SEL';
        $this->captura('clasif_codigo','varchar');
        $this->captura('clasif_detalle','varchar');
        $this->captura('clasif_operacion','varchar');
        $this->captura('gestion','varchar');
        $this->captura('partida','varchar');
        $this->captura('usr_reg','varchar');
        $this->captura('fecha_registro','varchar');
        $this->armarConsulta();
        $this->ejecutarConsulta();
        return $this->respuesta;
    }
}
?>