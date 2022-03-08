<?php
/**
 *@package pXP
 *@file gen-MODClasificacion.php
 *@author  (admin)
 *@date 08-10-2013 14:41:56
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODReporte extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function reporteDepreciacion(){
        //Definicion de variables para ejecucion del procedimientp
        $this->setCount(false);
        $this->setTipoRetorno('record');
        $this->procedimiento='af.f_reportes_sel';
        $this->transaccion='AF_REP_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion


        $this->setParametro('id_gestion','id_gestion','integer');
        $this->setParametro('id_periodo','id_periodo','integer');
        $this->setParametro('clasificacion','clasificacion','varchar');
        $this->setParametro('revalorizaciones','revalorizaciones','varchar');
        $this->setParametro('regionales','regionales','varchar');



        //Definicion de la lista del resultado del query
        //$this->captura('datos', 'record');

        $this->captura('id_activo_fijo', 'integer');
        $this->captura('codigo_tipo', 'varchar');

        $this->captura('ajuste_reva_gestion', 'numeric');
        $this->captura('compra_gestion', 'numeric');


        $this->captura('tipo', 'varchar');
        $this->captura('codigo_subtipo', 'varchar');
        $this->captura('subtipo', 'varchar');
        $this->captura('codigo_rama', 'varchar');
        $this->captura('rama', 'varchar');
        $this->captura('codigo', 'varchar');
        $this->captura('descripcion', 'varchar');
        $this->captura('descripcion_larga', 'varchar');
        $this->captura('fecha_ini_dep', 'date');
        $this->captura('importe_100', 'numeric');
        $this->captura('monto_compra', 'numeric');
        $this->captura('monto_vigente', 'numeric');
        $this->captura('actualizacion_gestion_anterior', 'numeric');
        $this->captura('actualizacion_gestion_actual', 'numeric');
        $this->captura('actualizacion_periodo', 'numeric');
        $this->captura('monto_actualiz', 'numeric');
        $this->captura('vida_usada', 'integer');
        $this->captura('vida_util', 'integer');
        $this->captura('depreciacion_acum_gestion_anterior', 'numeric');
        $this->captura('depre_actu_gestion_anterior', 'numeric');
        $this->captura('depreciacion_gestion', 'numeric');
        $this->captura('depreciacion_periodo', 'numeric');
        $this->captura('depreciacion_acum', 'numeric');
        $this->captura('valor_residual', 'numeric');
        

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();
        
        //var_dump($this->respuesta); exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function reporteBajaRevalorizacion(){
        //Definicion de variables para ejecucion del procedimientp
        $this->setCount(false);
        $this->setTipoRetorno('record');
        $this->procedimiento='af.f_reportes_sel';
        $this->transaccion='AF_REPBAJREV_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion


        $this->setParametro('id_gestion','id_gestion','integer');
        $this->setParametro('gestion','gestion','varchar');



        //Definicion de la lista del resultado del query
        //$this->captura('datos', 'record');


        $this->captura('codigo_tipo', 'varchar');
        $this->captura('tipo', 'varchar');
        $this->captura('codigo', 'varchar');
        $this->captura('descripcion', 'varchar');
        $this->captura('fecha_baja', 'text');
        $this->captura('monto_actualiz', 'numeric');
        $this->captura('depreciacion_acum', 'numeric');
        $this->captura('valor_residual', 'numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //var_dump($this->respuesta); exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function reporteDepreciacionPDF(){
        //Definicion de variables para ejecucion del procedimientp
        $this->setCount(false);
        $this->setTipoRetorno('record');
        $this->procedimiento='af.f_reportes_sel';
        $this->transaccion='AF_REPDF_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion


        $this->setParametro('id_gestion','id_gestion','integer');
        $this->setParametro('id_periodo','id_periodo','integer');
        $this->setParametro('clasificacion','clasificacion','varchar');
        $this->setParametro('revalorizaciones','revalorizaciones','varchar');
        $this->setParametro('nombre_desc','nombre_desc','varchar');
        $this->setParametro('regionales','regionales','varchar');

        //Definicion de la lista del resultado del query
        //$this->captura('datos', 'record');

        $this->captura('codigo_tipo', 'varchar');
        $this->captura('tipo', 'varchar');
        $this->captura('codigo_subtipo', 'varchar');
        $this->captura('subtipo', 'varchar');
        $this->captura('codigo_rama', 'varchar');
        $this->captura('rama', 'varchar');
        $this->captura('codigo', 'varchar');
        $this->captura('descripcion', 'varchar');
        $this->captura('fecha_ini_dep', 'date');
        $this->captura('importe_100', 'numeric');
        $this->captura('monto_compra', 'numeric');
        $this->captura('actualizacion', 'numeric');
        $this->captura('monto_actualiz', 'numeric');
        $this->captura('vida_usada', 'integer');
        $this->captura('vida_util', 'integer');
        $this->captura('depreciacion_acum_gestion_anterior', 'numeric');
        $this->captura('depre_actu_gestion_anterior', 'numeric');
        $this->captura('depreciacion_gestion', 'numeric');
        $this->captura('depreciacion_acum', 'numeric');
        $this->captura('valor_residual', 'numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //var_dump($this->respuesta); exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
    
    function listarClasificacion(){

        $this->setCount(false);
        $this->procedimiento='af.ft_clasificacion_sel';
        $this->transaccion='AF_CLAS_SEL';
        $this->tipo_procedimiento='SEL';

        $this->captura('codigo_tipo', ' varchar');
        $this->captura('tipo', 'varchar');
        $this->captura('codigo_subtipo', 'varchar');
        $this->captura('subtipo', 'varchar');
        $this->captura('codigo_rama', 'varchar');
        $this->captura('rama', 'varchar');

        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
