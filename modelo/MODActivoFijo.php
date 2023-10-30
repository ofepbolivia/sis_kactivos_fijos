<?php
/**
 *@package pXP
 *@file gen-MODActivoFijo.php
 *@author  (admin)
 *@date 29-10-2015 03:18:45
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODActivoFijo extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarActivoFijo(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='SKA_AFIJ_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('por_usuario','por_usuario','varchar');

        //Definicion de la lista del resultado del query
        $this->captura('id_activo_fijo','int4');
        $this->captura('id_persona','int4');
        $this->captura('cantidad_revaloriz','int4');
        $this->captura('foto','varchar');
        $this->captura('id_proveedor','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('fecha_compra','varchar');
        $this->captura('monto_vigente','numeric');
        $this->captura('id_cat_estado_fun','int4');
        $this->captura('ubicacion','varchar');
        $this->captura('vida_util','int4');
        $this->captura('documento','varchar');
        $this->captura('observaciones','varchar');
        $this->captura('fecha_ult_dep','date');
        $this->captura('monto_rescate','numeric');
        $this->captura('denominacion','varchar');
        $this->captura('id_funcionario','int4');
        $this->captura('id_deposito','int4');
        $this->captura('monto_compra','numeric');
        $this->captura('id_moneda','int4');
        $this->captura('depreciacion_mes','numeric');
        $this->captura('codigo','varchar');
        $this->captura('descripcion','varchar');
        $this->captura('id_moneda_orig','int4');
        $this->captura('fecha_ini_dep','date');
        $this->captura('id_cat_estado_compra','int4');
        $this->captura('depreciacion_per','numeric');
        $this->captura('vida_util_original','int4');
        $this->captura('depreciacion_acum','numeric');
        $this->captura('estado','varchar');
        $this->captura('id_clasificacion','int4');
        $this->captura('id_centro_costo','int4');
        $this->captura('id_oficina','int4');
        $this->captura('id_depto','int4');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_ai','int4');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('persona','text');
        $this->captura('desc_proveedor','varchar');
        $this->captura('estado_fun','varchar');
        $this->captura('estado_compra','varchar');
        $this->captura('clasificacion','text');
        $this->captura('centro_costo','text');
        $this->captura('oficina','text');
        $this->captura('depto','text');
        $this->captura('funcionario','text');
        $this->captura('deposito','varchar');
        $this->captura('deposito_cod','varchar');
        $this->captura('desc_moneda_orig','varchar');
        $this->captura('en_deposito','varchar');
        $this->captura('extension','varchar');
        $this->captura('codigo_ant','varchar');
        $this->captura('marca','varchar');
        $this->captura('nro_serie','varchar');
        $this->captura('caracteristicas','text');
        $this->captura('monto_vigente_real_af','numeric');
        $this->captura('vida_util_real_af','int4');
        $this->captura('fecha_ult_dep_real_af','date');
        $this->captura('depreciacion_acum_real_af','numeric');
        $this->captura('depreciacion_per_real_af','numeric');
        $this->captura('tipo_activo','varchar');
        $this->captura('depreciable','varchar');
        $this->captura('monto_compra_orig','numeric');
        $this->captura('id_proyecto','int4');
        $this->captura('desc_proyecto','varchar');
        $this->captura('cantidad_af','integer');
        $this->captura('id_unidad_medida','integer');
        $this->captura('codigo_unmed','varchar');
        $this->captura('descripcion_unmed','varchar');
        $this->captura('monto_compra_orig_100','numeric');
        $this->captura('nro_cbte_asociado','varchar');
        $this->captura('fecha_cbte_asociado','date');
        $this->captura('vida_util_original_anios','numeric');
        $this->captura('nombre_cargo','varchar');
        $this->captura('fecha_asignacion','date');
        $this->captura('prestamo','varchar');
        $this->captura('fecha_dev_prestamo','date');
        $this->captura('tramite_compra','varchar');
        $this->captura('id_proceso_wf','int4');
        $this->captura('subtipo','varchar');
        $this->captura('nombre_unidad','varchar');
        $this->captura('id_uo','int4');
        $this->captura('desc_denominacion','varchar');
        $this->captura('departamento','varchar');

        $this->captura('fecha_inicio','date');
        $this->captura('fecha_fin','date');
        $this->captura('resp_deposito','text');

        //fRnk: campos adicionados HR1163
        $this->captura('clasif_codigo','varchar');
        $this->captura('clasif_nombre','varchar');
        $this->captura('ofi_ubicacion','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarActivoFijo(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='kaf.ft_activo_fijo_ime';
        $this->transaccion='SKA_AFIJ_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_persona','id_persona','int4');
        $this->setParametro('cantidad_revaloriz','cantidad_revaloriz','int4');
        $this->setParametro('foto','foto','varchar');
        $this->setParametro('id_proveedor','id_proveedor','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('fecha_compra','fecha_compra','date');
        $this->setParametro('monto_vigente','monto_vigente','numeric');
        $this->setParametro('id_cat_estado_fun','id_cat_estado_fun','int4');
        $this->setParametro('ubicacion','ubicacion','varchar');
        $this->setParametro('vida_util','vida_util','int4');
        $this->setParametro('documento','documento','varchar');
        $this->setParametro('observaciones','observaciones','varchar');
        $this->setParametro('fecha_ult_dep','fecha_ult_dep','date');
        $this->setParametro('monto_rescate','monto_rescate','numeric');
        $this->setParametro('denominacion','denominacion','varchar');
        $this->setParametro('id_funcionario','id_funcionario','int4');
        $this->setParametro('id_deposito','id_deposito','int4');
        $this->setParametro('monto_compra','monto_compra','numeric');
        $this->setParametro('id_moneda','id_moneda','int4');
        $this->setParametro('depreciacion_mes','depreciacion_mes','numeric');
        $this->setParametro('codigo','codigo','varchar');
        $this->setParametro('descripcion','descripcion','varchar');
        $this->setParametro('id_moneda_orig','id_moneda_orig','int4');
        $this->setParametro('fecha_ini_dep','fecha_ini_dep','date');
        $this->setParametro('id_cat_estado_compra','id_cat_estado_compra','int4');
        $this->setParametro('depreciacion_per','depreciacion_per','numeric');
        $this->setParametro('vida_util_original','vida_util_original','int4');
        $this->setParametro('depreciacion_acum','depreciacion_acum','numeric');
        $this->setParametro('estado','estado','varchar');
        $this->setParametro('id_clasificacion','id_clasificacion','int4');
        $this->setParametro('id_centro_costo','id_centro_costo','int4');
        $this->setParametro('id_oficina','id_oficina','int4');
        $this->setParametro('id_depto','id_depto','int4');
        $this->setParametro('codigo_ant','codigo_ant','varchar');
        $this->setParametro('marca','marca','varchar');
        $this->setParametro('nro_serie','nro_serie','varchar');
        //$this->setParametro('caracteristicas','caracteristicas','text');
        $this->setParametro('monto_compra_orig','monto_compra_orig','numeric');

        $this->setParametro('id_proyecto','id_proyecto','int4');
        $this->setParametro('cantidad_af','cantidad_af','int4');
        $this->setParametro('id_unidad_medida','id_unidad_medida','int4');
        $this->setParametro('monto_compra_orig_100','monto_compra_orig_100','numeric');
        $this->setParametro('nro_cbte_asociado','nro_cbte_asociado','varchar');
        $this->setParametro('fecha_cbte_asociado','fecha_cbte_asociado','date');
        $this->setParametro('tramite_compra','tramite_compra','varchar');
        $this->setParametro('subtipo','subtipo','varchar');
        $this->setParametro('nombre_unidad','nombre_unidad','varchar');
        $this->setParametro('id_uo','id_uo','int4');

        $this->setParametro('fecha_inicio','fecha_inicio','date');
        $this->setParametro('fecha_fin','fecha_fin','date');



        //Ejecuta la instruccion
        $this->armarConsulta();

        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarActivoFijo(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='kaf.ft_activo_fijo_ime';
        $this->transaccion='SKA_AFIJ_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_activo_fijo','id_activo_fijo','int4');
        $this->setParametro('id_persona','id_persona','int4');
        $this->setParametro('cantidad_revaloriz','cantidad_revaloriz','int4');
        $this->setParametro('foto','foto','varchar');
        $this->setParametro('id_proveedor','id_proveedor','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('fecha_compra','fecha_compra','date');
        $this->setParametro('monto_vigente','monto_vigente','numeric');
        $this->setParametro('id_cat_estado_fun','id_cat_estado_fun','int4');
        $this->setParametro('ubicacion','ubicacion','varchar');
        $this->setParametro('vida_util','vida_util','int4');
        $this->setParametro('documento','documento','varchar');
        $this->setParametro('observaciones','observaciones','varchar');
        $this->setParametro('fecha_ult_dep','fecha_ult_dep','date');
        $this->setParametro('monto_rescate','monto_rescate','numeric');
        $this->setParametro('denominacion','denominacion','varchar');
        $this->setParametro('id_funcionario','id_funcionario','int4');
        $this->setParametro('id_deposito','id_deposito','int4');
        $this->setParametro('monto_compra','monto_compra','numeric');
        $this->setParametro('id_moneda','id_moneda','int4');
        $this->setParametro('depreciacion_mes','depreciacion_mes','numeric');
        $this->setParametro('codigo','codigo','varchar');
        $this->setParametro('descripcion','descripcion','varchar');
        $this->setParametro('id_moneda_orig','id_moneda_orig','int4');
        $this->setParametro('fecha_ini_dep','fecha_ini_dep','date');
        $this->setParametro('id_cat_estado_compra','id_cat_estado_compra','int4');
        $this->setParametro('depreciacion_per','depreciacion_per','numeric');
        $this->setParametro('vida_util_original','vida_util_original','int4');
        $this->setParametro('depreciacion_acum','depreciacion_acum','numeric');
        $this->setParametro('estado','estado','varchar');
        $this->setParametro('id_clasificacion','id_clasificacion','int4');
        $this->setParametro('id_centro_costo','id_centro_costo','int4');
        $this->setParametro('id_oficina','id_oficina','int4');
        $this->setParametro('id_depto','id_depto','int4');
        $this->setParametro('codigo_ant','codigo_ant','varchar');
        $this->setParametro('marca','marca','varchar');
        $this->setParametro('nro_serie','nro_serie','varchar');
        //$this->setParametro('caracteristicas','caracteristicas','text');
        $this->setParametro('monto_compra_orig','monto_compra_orig','numeric');
        $this->setParametro('id_proyecto','id_proyecto','int4');
        $this->setParametro('cantidad_af','cantidad_af','int4');
        $this->setParametro('id_unidad_medida','id_unidad_medida','int4');
        $this->setParametro('monto_compra_orig_100','monto_compra_orig_100','numeric');
        $this->setParametro('nro_cbte_asociado','nro_cbte_asociado','varchar');
        $this->setParametro('fecha_cbte_asociado','fecha_cbte_asociado','date');
        $this->setParametro('tramite_compra','tramite_compra','varchar');
        $this->setParametro('subtipo','subtipo','varchar');
        $this->setParametro('nombre_unidad','nombre_unidad','varchar');
        $this->setParametro('id_uo','id_uo','int4');

        $this->setParametro('fecha_inicio','fecha_inicio','date');
        $this->setParametro('fecha_fin','fecha_fin','date');

        $this->setParametro('fecha_cbte_asociado_hist','fecha_cbte_asociado','date');
        $this->setParametro('renova','renova','numeric');



        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarActivoFijo(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='kaf.ft_activo_fijo_ime';
        $this->transaccion='SKA_AFIJ_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_activo_fijo','id_activo_fijo','int4');
        $this->setParametro('motivo','motivo','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function codificarActivoFijo(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='kaf.ft_activo_fijo_ime';
        $this->transaccion='SKA_AFCOD_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_activo_fijo','id_activo_fijo','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function seleccionarActivosFijos(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='SKA_IDAF_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

        //Definicion de la lista del resultado del query
        $this->captura('ids','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }



    function recuperarCodigoQR(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='kaf.ft_activo_fijo_ime';
        $this->transaccion='SKA_GETQR_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_activo_fijo','id_activo_fijo','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function recuperarListadoCodigosQR(){
        //Definicion de variables para ejecucion del procedimiento
        $this -> procedimiento='kaf.ft_activo_fijo_sel';
        $this -> transaccion='SKA_GEVARTQR_SEL';
        $this -> tipo_procedimiento='SEL';
        $this -> setCount(false);

        //Define los parametros para la funcion
        $this->setParametro('id_clasificacion','id_clasificacion','int4');
        $this->setParametro('desde','desde','date');
        $this->setParametro('hasta','hasta','date');

        $this->captura('id_activo_fijo','int4');
        $this->captura('codigo','varchar');
        $this->captura('codigo_ant','varchar');
        $this->captura('denominacion','varchar');
        $this->captura('nombre_depto','varchar');
        $this->captura('nombre_entidad','varchar');




        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }




    function subirFoto(){

        $cone = new conexion();
        $link = $cone->conectarpdo();
        $copiado = false;
        try {

            $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $link->beginTransaction();

            if ($this->arregloFiles['archivo']['name'] == "") {
                throw new Exception("El archivo no puede estar vacio");
            }

            $this->procedimiento='kaf.ft_activo_fijo_ime';
            $this->transaccion='SKA_PHOTO_UPL';
            $this->tipo_procedimiento='IME';

            $ext = pathinfo($this->arregloFiles['archivo']['name']);
            $this->arreglo['extension'] = strtolower($ext['extension']);

            //validar que no sea un arhvio en blanco
            $file_name = $this->getFileName2('archivo', 'id_activo_fijo', '', false);

            //Define los parametros para la funcion
            $this->setParametro('id_activo_fijo','id_activo_fijo','integer');
            $this->setParametro('extension','extension','varchar');

            //manda como parametro la url completa del archivo
            $this->aParam->addParametro('file_name', $file_name[2]);
            $this->arreglo['file_name'] = $file_name[2];
            $this->setParametro('file_name','file_name','varchar');

            //manda como parametro el folder del arhivo
            $this->aParam->addParametro('folder', $file_name[1]);
            $this->arreglo['folder'] = $file_name[1];
            $this->setParametro('folder','folder','varchar');

            //manda como parametro el solo el nombre del arhivo  sin extencion
            $this->aParam->addParametro('only_file', $file_name[0]);
            $this->arreglo['only_file'] = $file_name[0];
            $this->setParametro('only_file','only_file','varchar');


            //Ejecuta la instruccion
            $this->armarConsulta();
            $stmt = $link->prepare($this->consulta);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);

            if ($resp_procedimiento['tipo_respuesta']=='ERROR') {
                throw new Exception("Error al ejecutar en la bd", 3);
            }


            if($resp_procedimiento['tipo_respuesta'] == 'EXITO'){

                //revisamos si ya existe el archivo la verison anterior sera mayor a cero
                $respuesta = $resp_procedimiento['datos'];
                //var_dump($respuesta);
                if($respuesta['max_version'] != '0' && $respuesta['url_destino'] != ''){

                    $this->copyFile($respuesta['url_origen'], $respuesta['url_destino'],  $folder = 'historico');
                    $copiado = true;
                }

                //cipiamos el nuevo archivo
                $this->setFile('archivo','id_activo_fijo', false,100000 ,array('jpg','jpeg','bmp','gif','png','JPG','JPEG','BMP','GIF','PNG'));
            }

            $link->commit();
            $this->respuesta=new Mensaje();
            $this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'],$this->nombre_archivo,$resp_procedimiento['mensaje'],$resp_procedimiento['mensaje_tec'],'base',$this->procedimiento,$this->transaccion,$this->tipo_procedimiento,$this->consulta);
            $this->respuesta->setDatos($respuesta);
        }

        catch (Exception $e) {
            $link->rollBack();

            if($copiado){
                $this->copyFile($respuesta['url_origen'], $respuesta['url_destino'],  $folder = 'historico', true);
            }
            $this->respuesta=new Mensaje();
            if ($e->getCode() == 3) {//es un error de un procedimiento almacenado de pxp
                $this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'],$this->nombre_archivo,$resp_procedimiento['mensaje'],$resp_procedimiento['mensaje_tec'],'base',$this->procedimiento,$this->transaccion,$this->tipo_procedimiento,$this->consulta);
            } else if ($e->getCode() == 2) {//es un error en bd de una consulta
                $this->respuesta->setMensaje('ERROR',$this->nombre_archivo,$e->getMessage(),$e->getMessage(),'modelo','','','','');
            } else {//es un error lanzado con throw exception
                throw new Exception($e->getMessage(), 2);
            }
        }

        return $this->respuesta;

    }

    function clonarActivoFijo(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='kaf.ft_activo_fijo_ime';
        $this->transaccion='SKA_AFIJ_CLO';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_activo_fijo','id_activo_fijo','int4');
        $this->setParametro('cantidad_clon','cantidad_clon','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarActivoFijoFecha(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='SKA_AFFECH_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('fecha_mov','fecha_mov','date');
        $this->setParametro('no_asignado','no_asignado','varchar');
        $this->setParametro('cod_mov','cod_mov','varchar');

        //Definicion de la lista del resultado del query
        $this->captura('id_activo_fijo','int4');
        $this->captura('id_persona','int4');
        $this->captura('cantidad_revaloriz','int4');
        $this->captura('foto','varchar');
        $this->captura('id_proveedor','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('fecha_compra','date');
        $this->captura('monto_vigente','numeric');
        $this->captura('id_cat_estado_fun','int4');
        $this->captura('ubicacion','varchar');
        $this->captura('vida_util','int4');
        $this->captura('documento','varchar');
        $this->captura('observaciones','varchar');
        $this->captura('fecha_ult_dep','date');
        $this->captura('monto_rescate','numeric');
        $this->captura('denominacion','varchar');
        $this->captura('id_funcionario','int4');
        $this->captura('id_deposito','int4');
        $this->captura('monto_compra','numeric');
        $this->captura('id_moneda','int4');
        $this->captura('depreciacion_mes','numeric');
        $this->captura('codigo','varchar');
        $this->captura('descripcion','varchar');
        $this->captura('id_moneda_orig','int4');
        $this->captura('fecha_ini_dep','date');
        $this->captura('id_cat_estado_compra','int4');
        $this->captura('depreciacion_per','numeric');
        $this->captura('vida_util_original','int4');
        $this->captura('depreciacion_acum','numeric');
        $this->captura('estado','varchar');
        $this->captura('id_clasificacion','int4');
        $this->captura('id_centro_costo','int4');
        $this->captura('id_oficina','int4');
        $this->captura('id_depto','int4');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_ai','int4');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('persona','text');
        $this->captura('desc_proveedor','varchar');
        $this->captura('estado_fun','varchar');
        $this->captura('estado_compra','varchar');
        $this->captura('clasificacion','text');
        $this->captura('centro_costo','text');
        $this->captura('oficina','text');
        $this->captura('depto','text');
        $this->captura('funcionario','text');
        $this->captura('deposito','varchar');
        $this->captura('deposito_cod','varchar');
        $this->captura('desc_moneda_orig','varchar');
        $this->captura('en_deposito','varchar');
        $this->captura('extension','varchar');
        $this->captura('codigo_ant','varchar');
        $this->captura('marca','varchar');
        $this->captura('nro_serie','varchar');
        $this->captura('caracteristicas','text');
        $this->captura('monto_vigente_real_af','numeric');
        $this->captura('vida_util_real_af','int4');
        $this->captura('fecha_ult_dep_real_af','date');
        $this->captura('depreciacion_acum_real_af','numeric');
        $this->captura('depreciacion_per_real_af','numeric');
        $this->captura('tipo_activo','varchar');
        $this->captura('depreciable','varchar');
        $this->captura('monto_compra_orig','numeric');
        $this->captura('id_proyecto','int4');
        $this->captura('desc_proyecto','varchar');
        $this->captura('cantidad_af','integer');
        $this->captura('id_unidad_medida','integer');
        $this->captura('codigo_unmed','varchar');
        $this->captura('descripcion_unmed','varchar');
        $this->captura('monto_compra_orig_100','numeric');
        $this->captura('nro_cbte_asociado','varchar');
        $this->captura('fecha_cbte_asociado','date');
        $this->captura('nombre_cargo','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
       // echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarActivosNoAsignados(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='SKA_NO_ASIGNADO_SEL';
        $this->tipo_procedimiento='SEL';

        //Define los parametros para la funcion
        $this->captura('id_activo_fijo','int4');
        $this->captura('codigo','varchar');
        $this->captura('descripcion','varchar');
        $this->captura('denominacion','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function consultaQR(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='kaf.ft_activo_fijo_ime';
        $this->transaccion='SKA_AFQR_DAT';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_activo_fijo','id_activo_fijo','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarCodigoQRVarios(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='SKA_QRVARIOS_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

        //Definicion de la lista del resultado del query
        $this->captura('id_activo_fijo','int4');
        $this->captura('codigo','varchar');
        $this->captura('codigo_ant','varchar');
        $this->captura('denominacion','varchar');
        $this->captura('nombre_depto','varchar');
        $this->captura('nombre_entidad','varchar');
        $this->captura('descripcion','varchar');
        $this->captura('clase_rep','varchar');
        $this->captura('clasif','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function reportesAFGlobal(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='SKA_COMPRAS_GEST_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('desc_nombre','desc_nombre','varchar');
        $this->setParametro('id_clasificacion','id_clasificacion','varchar');
        $this->setParametro('ubicacion','ubicacion','INT4');



        //Definicion de la lista del resultado del query
        $this->captura('id_clasificacion','int4');
        $this->captura('id_clasificacion_fk','int4');
        $this->captura('codigo','varchar');
        $this->captura('codigo_completo','varchar');
        $this->captura('nivel','int4');
        $this->captura('nombre','varchar');
        $this->captura('camino','text');
        $this->captura('codigo_af','varchar');
        $this->captura('denominacion','varchar');
        $this->captura('fecha_compra','varchar');
        $this->captura('nro_cbte_asociado','varchar');
        $this->captura('fecha_cbte_asociado','varchar');
        $this->captura('fecha_ini_dep','varchar');
        $this->captura('vida_util_original','int4');
        $this->captura('monto_compra_orig_100','numeric');
        $this->captura('monto_compra_orig','numeric');
        $this->captura('tipo_activo','varchar');
        $this->captura('ubicacion','varchar');
        $this->captura('responsable','varchar');
        $this->captura('monto_compra','numeric');
        $this->captura('estado','varchar');
        $this->captura('nombre_unidad','varchar');
        $this->captura('estado_fun','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
/////////////////////////////////////////////////


    function getActivosFijosFuncionarioBoa(){
        //Definicion de variables para ejecucion del procedimiento
        $this -> procedimiento='kaf.ft_activo_fijo_sel';
        $this -> transaccion='SKA_GET_AF_BOA_SEL';
        $this -> tipo_procedimiento='SEL';
        $this -> setCount(false);

        //Define los parametros para la funcion
        $this->setParametro('id_funcionario','id_funcionario','int4');
        $this->setParametro('busca','busca','text');
        $this->setParametro('orden','orden','varchar');

        $this->captura('responsable','varchar');
        $this->captura('codigo','varchar');
        $this->captura('denominacion','varchar');
        $this->captura('descripcion','varchar');
        $this->captura('fecha_asignacion','text');
        $this->captura('oficina','varchar');
        $this->captura('ubicacion','varchar');



        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }


    function ListaDetActivo(){

        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='SKA_LI_ACLIDE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

        //Ejecuta la instruccion
        $this->captura('id_clasificacion','int4');
        $this->captura('codigo','varchar');
        $this->captura('nombre','varchar');
        $this->captura('nivel','int4');

        $this->armarConsulta();
        //var_dump($this->consulta);exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;

    }
    function ReporteDetalleActivos(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='SKA_REP_DETAF_SEL'; // texto < 20
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //$this->setCount(false);

        $this->setParametro('id_clasificacion','id_clasificacion','varchar');

        //Definicion de la lista del resultado del query
        $this->captura('id_clasificacion','int4');
        $this->captura('id_clasificacion_fk','int4');
        $this->captura('codigo_completo_tmp','varchar');
        $this->captura('nombre','varchar');
        $this->captura('nivel','int4');
        $this->captura('hijos','varchar');
        $this->captura('sw_transaccional', 'varchar'); //fRnk: añadido HR915
        //Ejecuta la instruccion
        $this->armarConsulta();
        //var_dump($this->consulta);exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;

    }
    function ReporteActivoEnDetalle(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='SKA_REP_ACTEDET_SEL'; // texto < 20
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('id_clasificacion','id_clasificacion','varchar');
        //$this->setParametro('id_clasificacion','id_clasificacion','int4'); cambio para seleccion multiple

        //Definicion de la lista del resultado del query

        $this->captura('tipo','varchar');
        $this->captura('marca','varchar');
        $this->captura('subtipo','varchar');
        $this->captura('codigo','varchar');
        $this->captura('descripcion','varchar');
        $this->captura('clasificacion','varchar');
        $this->captura('denominacion','varchar');
        $this->captura('estado','varchar');
        $this->captura('estado_funcional','varchar');
        $this->captura('fecha_compra','varchar');
        $this->captura('c31','varchar');
        $this->captura('ubicacion','varchar');
        $this->captura('responsable','varchar');
		$this->captura('nro_serie','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        //var_dump($this->consulta);exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;

    }
    function lecturaQRAP(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='kaf.ft_activo_fijo_ime';
        $this->transaccion='SKA_AFQR_DET';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('code','code','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function proveedorActivo(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='SKA_PROV_AC_SEL';
        $this->tipo_procedimiento='SEL';

        $this->captura('provee','varchar');
        $this->captura('id_proveedor','int4');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function proveedorActivoRep(){
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->setCount(false);
        $this->transaccion='SKA_PROV_REP_SEL';
        $this->tipo_procedimiento='SEL';

        $this->setParametro('id_proveedor','id_proveedor','int4');
        $this->captura('desc_proveedor','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;

    }
    function listaLug(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this-> setCount(false);
        $this->transaccion='SKA_ACTLUG_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('id_lugar','id_lugar','int4');
        //Definicion de la lista del resultado del query
        $this->captura('id_lugar','int4');
        $this->captura('codigo','varchar');
        $this->captura('nombre','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function listarAF(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='KA_AFIJOS_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //Definicion de la lista del resultado del query
        $this->captura('id_activo_fijo','int4');
        $this->captura('denominacion','varchar');
        $this->captura('codigo','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function listarAFUnidSol(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='KA_AFUNSOL_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //Definicion de la lista del resultado del query
        $this->captura('id_uo','int4');
        $this->captura('nombre_unidad','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarActivoFijoHistorico(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='KA_AFHISLIS_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        // $this->setParametro('por_usuario','por_usuario','varchar');

        //Definicion de la lista del resultado del query
        $this->captura('id_activo_fijo_hist','int4');
        $this->captura('codigo_hist','varchar');
        $this->captura('denominacion_hist','varchar');
        $this->captura('descripcion_hist','varchar');
        $this->captura('cantidad_af_hist','integer');
        $this->captura('documento_hist','varchar');
        $this->captura('fecha_compra_hist','date');
        $this->captura('fecha_ini_dep_hist','date');
        $this->captura('monto_compra_hist','varchar');
        $this->captura('monto_compra_orig_hist','varchar');
        $this->captura('monto_compra_orig_100_hist','varchar');
        $this->captura('observaciones_hist','varchar');
        $this->captura('tipo_reg_hist','varchar');
        $this->captura('tramite_compra_hist','varchar');
        $this->captura('ubicacion_hist','varchar');
        $this->captura('vida_util_original_hist','integer');
        $this->captura('id_activo_fijo','int4');

        $this->captura('depto','varchar');
        $this->captura('clasificacion','varchar');
        $this->captura('estado_hist','varchar');
        $this->captura('oficina','varchar');
        $this->captura('funcionario','varchar');
        $this->captura('desc_proveedor','varchar');
        $this->captura('nro_cbte_asociado_hist','varchar');
        $this->captura('fecha_cbte_asociado_hist','date');
        $this->captura('nombre_unidad','varchar');
        $this->captura('desc_proyecto','varchar');
        $this->captura('desc_moneda_orig','varchar');
        $this->captura('fecha_inicio','date');
        $this->captura('fecha_fin','date');

        $this->captura('fecha_mod','timestamp');
        $this->captura('fecha_reg','timestamp');
        $this->captura('estado_reg','varchar');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        // echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function reporteHistoricoAF()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'kaf.ft_activo_fijo_sel';
        $this->transaccion = 'KA_REPHISTAF_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion
        $this->setCount(false);

//        $this->setParametro('id_proceso_wf', 'id_proceso_wf', 'int4');
        //Definicion de la lista del resultado del query

        $this->captura('codigo_hist', 'varchar');
        $this->captura('descripcion_hist', 'varchar');
        $this->captura('fecha_inicio', 'varchar');
        $this->captura('fecha_fin', 'varchar');
        $this->captura('monto_compra_orig_hist', 'varchar');
        $this->captura('monto_compra_orig_100_hist', 'varchar');
        $this->captura('monto_compra_hist', 'varchar');
        $this->captura('nro_cbte_asociado_hist', 'varchar');
        $this->captura('fecha_cbte_asociado_hist', 'varchar');
        $this->captura('tramite_compra_hist', 'varchar');
        $this->captura('funcionario_responsable', 'varchar');
        $this->captura('nombre_unidad', 'varchar');
        $this->captura('denominacion_hist', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
//       echo($this->consulta);exit;
        $this->ejecutarConsulta();
//       var_dump($this->respuesta); exit;

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function reportesPendientesAprob(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='SKA_REPPENAPROB_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);


        //Definicion de la lista del resultado del query
        $this->captura('nro_tramite','varchar');
        $this->captura('fecha_ini','date');
        $this->captura('glosa','varchar');
        $this->captura('funcionario','varchar');
        $this->captura('depto','varchar');
        $this->captura('num_tramite','varchar');



        //Ejecuta la instruccion
        $this->armarConsulta();
//        echo $this->consulta;exit;
        $this->ejecutarConsulta();
//        var_dump($this->respuesta); exit;

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function reportesSinAsignacion(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='SKA_REPSINASIG_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);


        //Definicion de la lista del resultado del query
        $this->captura('codigo','varchar');
        $this->captura('descripcion','varchar');
        $this->captura('fecha_ini_dep','date');
        $this->captura('monto_compra_orig_100','numeric');
        $this->captura('monto_compra_orig','numeric');
        $this->captura('nombre_unidad','varchar');
        $this->captura('tramite_compra','varchar');
        $this->captura('nro_cbte_asociado','varchar');



        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;

        $this->ejecutarConsulta();
//        var_dump($this->respuesta); exit;

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function reporteActiDepoFuncio(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='SKA_ACDEPXFUN_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

		$this->setParametro('id_funcionario','id_funcionario','int4');
		$this->setParametro('id_deposito','id_deposito','int4');

        //Definicion de la lista del resultado del query
        $this->captura('codigo','varchar');
        $this->captura('denominacion','varchar');
        $this->captura('descripcion','varchar');
		$this->captura('ubicacion','varchar');
        $this->captura('cat_desc','varchar');
        $this->captura('almacen','varchar');
		$this->captura('fecha_mov','date');
		$this->captura('encargado','text');
        //Ejecuta la instruccion
        $this->armarConsulta();
        // echo($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function verificarNoTramiteCompra(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_activo_fijo_ime';
        $this->transaccion='KA_VERTRCOM_IME';
        $this->tipo_procedimiento='IME';//tipo de transaccion
        //Definicion de la lista del resultado del query
        $this->setParametro('tramite_compra','tramite_compra','varchar');
        $this->setParametro('id_preingreso','id_preingreso','int4');
        $this->setParametro('id_activo_fijo','id_activo_fijo','int4');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarFuncionarioUltCargo(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='kaf.ft_activo_fijo_sel';
        $this->transaccion='SKA_ACFUNCAR_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_uo_funcionario','integer');
        $this->captura('id_funcionario','integer');
        $this->captura('desc_funcionario1','text');
        $this->captura('desc_funcionario2','text');
        $this->captura('id_uo','integer');
        $this->captura('nombre_cargo','varchar');
        $this->captura('fecha_asignacion','date');
        $this->captura('fecha_finalizacion','date');
        $this->captura('num_doc','integer');
        $this->captura('ci','varchar');
        $this->captura('codigo','varchar');
        $this->captura('email_empresa','varchar');
        $this->captura('estado_reg_fun','varchar');
        $this->captura('estado_reg_asi','varchar');
        $this->captura('id_cargo','integer');
        $this->captura('descripcion_cargo','varchar');
        $this->captura('cargo_codigo','varchar');
        $this->captura('id_lugar','integer');
        $this->captura('id_oficina','integer');
        $this->captura('lugar_nombre','varchar');
        $this->captura('oficina_nombre','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
}
?>
