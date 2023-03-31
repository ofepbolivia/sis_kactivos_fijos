<?php
/**
 *@package pXP
 *@file gen-ACTActivoFijo.php
 *@author  (admin)
 *@date 29-10-2015 03:18:45
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */
require_once(dirname(__FILE__).'/../reportes/RCodigoQRAF.php');
require_once(dirname(__FILE__).'/../reportes/RCodigoQRAF_v1.php');
require_once(dirname(__FILE__).'/../reportes/RCompraGestionPDF.php');
require_once(dirname(__FILE__).'/../reportes/RCompraGestionXls.php');
require_once(dirname(__FILE__).'/../reportes/RDetalleAFPDF.php');
require_once(dirname(__FILE__).'/../reportes/RActivoDetallePDF.php');
require_once(dirname(__FILE__).'/../reportes/RDetalleAFXls.php');
require_once(dirname(__FILE__).'/../reportes/RActivoFijoPDF.php');
require_once(dirname(__FILE__).'/../reportes/RActivoFijoXls.php');
require_once(dirname(__FILE__).'/../reportes/RActivoFijoDetalleXls.php');

require_once(dirname(__FILE__).'/../reportes/RHistoricoAF.php');
require_once(dirname(__FILE__).'/../reportes/RPendientesAprobAFPDF.php');
require_once(dirname(__FILE__).'/../reportes/RPendientesAprobAFXls.php');
require_once(dirname(__FILE__).'/../reportes/RSinAsignacionAFPDF.php');
require_once(dirname(__FILE__).'/../reportes/RSinAsignacionAFXls.php');
require_once(dirname(__FILE__).'/../reportes/RActiDepaPFunFPDF.php');
require_once(dirname(__FILE__).'/../reportes/RActiDepaPFunAFXls.php');


class ACTActivoFijo extends ACTbase{

    function listarActivoFijo(){
        $this->objParam->defecto('ordenacion','id_activo_fijo');
        $this->objParam->defecto('dir_ordenacion','asc');

        //General filter by: depto, clasificacion, oficina, organigrama
        if($this->objParam->getParametro('col_filter_panel')!=''){
            $colFilter = $this->objParam->getParametro('col_filter_panel');
            if($colFilter=='id_depto'){
                $this->objParam->addFiltro("afij.id_depto = ".$this->objParam->getParametro('id_filter_panel'));
            } else if($colFilter=='id_clasificacion'){
                $this->objParam->addFiltro("afij.id_clasificacion in (
					WITH RECURSIVE t(id,id_fk,nombre,n) AS (
    				SELECT l.id_clasificacion,l.id_clasificacion_fk, l.nombre,1
    				FROM kaf.tclasificacion l
    				WHERE l.id_clasificacion = ".$this->objParam->getParametro('id_filter_panel')."
    				UNION ALL
    				SELECT l.id_clasificacion,l.id_clasificacion_fk, l.nombre,n+1
    				FROM kaf.tclasificacion l, t
    				WHERE l.id_clasificacion_fk = t.id
					)
					SELECT id
					FROM t)");

            } else if($colFilter=='id_oficina'){
                $this->objParam->addFiltro("afij.id_oficina = ".$this->objParam->getParametro('id_filter_panel'));
            } else if($colFilter=='id_uo'){
                $this->objParam->addFiltro("uo.id_uo in (
					WITH RECURSIVE t(id,id_fk,n) AS (
					SELECT l.id_uo_hijo,l.id_uo_padre,1
					FROM orga.testructura_uo l
					WHERE l.id_uo_hijo = ".$this->objParam->getParametro('id_filter_panel')."
					UNION ALL
					SELECT l.id_uo_hijo,l.id_uo_padre,n+1
					FROM orga.testructura_uo l, t
					WHERE l.id_uo_padre = t.id
					)
					SELECT id
					FROM t)");

            }

            //Por caracteristicas
            if($this->objParam->getParametro('caractFilter')!=''&&$this->objParam->getParametro('caractValue')!=''){
                $this->objParam->addFiltro("afij.id_activo_fijo in (select id_activo_fijo from kaf.tactivo_fijo_caract acar where acar.clave like ''%".$this->objParam->getParametro('caractFilter')."%'' and acar.valor like ''%".$this->objParam->getParametro('caractValue')."%'')");
            }
        }

        if($this->objParam->getParametro('id_depto')!=''){
            $this->objParam->addFiltro("afij.id_depto = ".$this->objParam->getParametro('id_depto'));
        }
        if($this->objParam->getParametro('estado')!=''){
            $this->objParam->addFiltro("afij.estado = ''".$this->objParam->getParametro('estado')."''");
        }

        if($this->objParam->getParametro('depreciable')!=''){
            $this->objParam->addFiltro("cla.depreciable = ''".$this->objParam->getParametro('depreciable')."''");
        }

        if($this->objParam->getParametro('en_deposito')!=''){
            $this->objParam->addFiltro("afij.en_deposito = ''".$this->objParam->getParametro('en_deposito')."''");
        }
        if($this->objParam->getParametro('id_funcionario')!=''){
            $this->objParam->addFiltro("afij.id_funcionario = ".$this->objParam->getParametro('id_funcionario'));
        }

        //Por caracteristicas
        if($this->objParam->getParametro('caractFilter')!=''&&$this->objParam->getParametro('caractValue')!=''){
            $this->objParam->addFiltro("afij.id_activo_fijo in (select id_activo_fijo from kaf.tactivo_fijo_caract acar where acar.clave like ''%".$this->objParam->getParametro('caractFilter')."%'' and acar.valor like ''%".$this->objParam->getParametro('caractValue')."%'')");
        }

        //Si es abierto desde link de otra grilla
        if($this->objParam->getParametro('id_activo_fijo')!=''){
            $this->objParam->addFiltro("afij.id_activo_fijo = ".$this->objParam->getParametro('id_activo_fijo'));
        }

        //Filtro por movimientos
        //Transferencia, Devolucion
        if($this->objParam->getParametro('codMov')=='transf'||$this->objParam->getParametro('codMov')=='devol'){
            $this->objParam->addFiltro("afij.id_funcionario = ".$this->objParam->getParametro('id_funcionario_mov'));
        }
        //Alta
        if($this->objParam->getParametro('codMov')=='alta'|| $this->objParam->getParametro('codMov')=='baja'|| $this->objParam->getParametro('codMov')=='reval'|| $this->objParam->getParametro('codMov')=='deprec'|| $this->objParam->getParametro('codMov')=='actua'||$this->objParam->getParametro('codMov')=='desuso'||$this->objParam->getParametro('codMov')=='incdec'||$this->objParam->getParametro('codMov')=='tranfdep'){
            $this->objParam->addFiltro("afij.id_depto = ".$this->objParam->getParametro('id_depto_mov'));
            $this->objParam->addFiltro("afij.estado = "."''".$this->objParam->getParametro('estado_mov')."''");
        }
        if($this->objParam->getParametro('codMov')=='asig'){
            $this->objParam->addFiltro("afij.en_deposito = ''".$this->objParam->getParametro('en_deposito_mov')."''");
            $this->objParam->addFiltro("afij.id_depto = ".$this->objParam->getParametro('id_depto_mov'));
        }
        if($this->objParam->getParametro('tipo_activo')!=''){
            $this->objParam->addFiltro("cla.tipo_activo = ''intangible'' ");
        }


        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODActivoFijo','listarActivoFijo');
        } else{
            $this->objFunc=$this->create('MODActivoFijo');

            $this->res=$this->objFunc->listarActivoFijo($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarActivoFijo(){
        $this->objFunc=$this->create('MODActivoFijo');
        if($this->objParam->insertar('id_activo_fijo')){
            $this->res=$this->objFunc->insertarActivoFijo($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarActivoFijo($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarActivoFijo(){
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->eliminarActivoFijo($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function codificarActivoFijo(){
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->codificarActivoFijo($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function seleccionarActivosFijos(){
        $this->objParam->defecto('ordenacion','id_activo_fijo');
        $this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('col_filter_panel')!=''){
            $colFilter = $this->objParam->getParametro('col_filter_panel');
            if($colFilter=='id_depto'){
                $this->objParam->addFiltro("afij.id_depto = ".$this->objParam->getParametro('id_filter_panel'));
            } else if($colFilter=='id_clasificacion'){
                $this->objParam->addFiltro("afij.id_clasificacion in (
					WITH RECURSIVE t(id,id_fk,nombre,n) AS (
    				SELECT l.id_clasificacion,l.id_clasificacion_fk, l.nombre,1
    				FROM alm.tclasificacion l
    				WHERE l.id_clasificacion = ".$this->objParam->getParametro('id_filter_panel')."
    				UNION ALL
    				SELECT l.id_clasificacion,l.id_clasificacion_fk, l.nombre,n+1
    				FROM alm.tclasificacion l, t
    				WHERE l.id_clasificacion_fk = t.id
					)
					SELECT id
					FROM t)");

            } else if($colFilter=='id_oficina'){
                $this->objParam->addFiltro("afij.id_oficina = ".$this->objParam->getParametro('id_filter_panel'));
            }
        }

        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->seleccionarActivosFijos($this->objParam);

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function subirFoto(){
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->SubirFoto();
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    /*
     *
     * Autor: RAC
     * Fecha: 16/03/2017
     * Descrip:  Imprime codigo de activo fijos de  uno en uno
     *
     *
     * */

    function recuperarCodigoQR(){
        $this->objFunc = $this->create('MODActivoFijo');
        $cbteHeader = $this->objFunc->recuperarCodigoQR($this->objParam);
        if($cbteHeader->getTipo() == 'EXITO'){
            return $cbteHeader;
        }
        else{
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }

    }




    function impCodigoActivoFijo(){

        $nombreArchivo = 'CodigoAF'.uniqid(md5(session_id())).'.pdf';
        $dataSource = $this->recuperarCodigoQR();



        //parametros basicos

        $orientacion = 'L';
        $titulo = 'Códigos Activos Fijos';

        //$width = 40;
        //$height = 20;

        $width = 160;
        $height = 80;


        $this->objParam->addParametro('orientacion',$orientacion);
        $this->objParam->addParametro('tamano',array($width, $height));
        $this->objParam->addParametro('titulo_archivo',$titulo);
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        //var_dump($dataSource->getDatos());
        //exit;
        $clsRep = $dataSource->getDatos();

        //$reporte = new RCodigoQRAF($this->objParam);

        eval('$reporte = new '.$clsRep['v_clase_reporte'].'($this->objParam);');



        $reporte->datosHeader( 'unico', $dataSource->getDatos());
        $reporte->generarReporte();
        $reporte->output($reporte->url_archivo,'F');


        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

    /*
     *
     * Autor: RAC
     * Fecha: 16/03/2017
     * Descrip:  Imprime codigos de activo fijos dsegun elc riterio de filtro, varios a la vez
     *
     *
     * */

    function recuperarListadoCodigosQR(){
        $this->objFunc = $this->create('MODActivoFijo');
        $cbteHeader = $this->objFunc->recuperarListadoCodigosQR($this->objParam);
        if($cbteHeader->getTipo() == 'EXITO'){
            return $cbteHeader;
        }
        else{
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }

    }

    function obtenerClaseReporteCodigoQRAF(){
        //crea el objetoFunSeguridad que contiene todos los metodos del sistema de seguridad

        $this->objParam->addParametro('codigo','kaf_clase_reporte_codigo');
        $this->objFunSeguridad=$this->create('sis_seguridad/MODSubsistema');



        $cbteHeader=$this->objFunSeguridad->obtenerVariableGlobal($this->objParam);

        if($cbteHeader->getTipo() == 'EXITO'){
            return $cbteHeader;
        }
        else{
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }

    }


    function impVariosCodigoActivoFijo(){

        $nombreArchivo = 'CodigoAF'.uniqid(md5(session_id())).'.pdf';
        $dataSource = $this->recuperarListadoCodigosQR();

        //recuperar variable global kaf_clase_reporte_codigo
        $clsQr = $this->obtenerClaseReporteCodigoQRAF();
        //parametros basicos

        $orientacion = 'L';
        $titulo = 'Código';

        //$width = 40;
        //$height = 20;

        $width = 160;
        $height = 80;

        $this->objParam->addParametro('orientacion',$orientacion);
        $this->objParam->addParametro('tamano',array($width, $height));
        $this->objParam->addParametro('titulo_archivo',$titulo);
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        //var_dump($dataSource->getDatos());
        //exit;
        $cls = $clsQr->getDatos();

        //$reporte = new RCodigoQRAF($this->objParam);

        eval('$reporte = new '.$cls['valor'].'($this->objParam);');



        $reporte->datosHeader( 'varios', $dataSource->getDatos());
        $reporte->generarReporte();
        $reporte->output($reporte->url_archivo,'F');


        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

    function clonarActivoFijo(){
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->clonarActivoFijo($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarActivoFijoFecha(){
        $this->objParam->defecto('ordenacion','id_activo_fijo');
        $this->objParam->defecto('dir_ordenacion','asc');

        //General filter by: depto, clasificacion, oficina, organigrama
        if($this->objParam->getParametro('col_filter_panel')!=''){
            $colFilter = $this->objParam->getParametro('col_filter_panel');
            if($colFilter=='id_depto'){
                $this->objParam->addFiltro("afij.id_depto = ".$this->objParam->getParametro('id_filter_panel'));
            } else if($colFilter=='id_clasificacion'){
                $this->objParam->addFiltro("afij.id_clasificacion in (
					WITH RECURSIVE t(id,id_fk,nombre,n) AS (
    				SELECT l.id_clasificacion,l.id_clasificacion_fk, l.nombre,1
    				FROM kaf.tclasificacion l
    				WHERE l.id_clasificacion = ".$this->objParam->getParametro('id_filter_panel')."
    				UNION ALL
    				SELECT l.id_clasificacion,l.id_clasificacion_fk, l.nombre,n+1
    				FROM kaf.tclasificacion l, t
    				WHERE l.id_clasificacion_fk = t.id
					)
					SELECT id
					FROM t)");

            } else if($colFilter=='id_oficina'){
                $this->objParam->addFiltro("afij.id_oficina = ".$this->objParam->getParametro('id_filter_panel'));
            }

            //Por caracteristicas
            if($this->objParam->getParametro('caractFilter')!=''&&$this->objParam->getParametro('caractValue')!=''){
                $this->objParam->addFiltro("afij.id_activo_fijo in (select id_activo_fijo from kaf.tactivo_fijo_caract acar where acar.clave like ''%".$this->objParam->getParametro('caractFilter')."%'' and acar.valor like ''%".$this->objParam->getParametro('caractValue')."%'')");
            }
        }

        if($this->objParam->getParametro('id_depto')!=''){
            $this->objParam->addFiltro("afij.id_depto = ".$this->objParam->getParametro('id_depto'));
        }
        if($this->objParam->getParametro('estado')!=''){
            $this->objParam->addFiltro("afij.estado = ''".$this->objParam->getParametro('estado')."''");
        }

        if($this->objParam->getParametro('depreciable')!=''){
            $this->objParam->addFiltro("cla.depreciable = ''".$this->objParam->getParametro('depreciable')."''");
        }

        if($this->objParam->getParametro('en_deposito')!=''){
            $this->objParam->addFiltro("afij.en_deposito = ''".$this->objParam->getParametro('en_deposito')."''");
        }
        if($this->objParam->getParametro('id_funcionario')!=''){
            $this->objParam->addFiltro("afij.id_funcionario = ".$this->objParam->getParametro('id_funcionario'));
        }

        //Por caracteristicas
        if($this->objParam->getParametro('caractFilter')!=''&&$this->objParam->getParametro('caractValue')!=''){
            $this->objParam->addFiltro("afij.id_activo_fijo in (select id_activo_fijo from kaf.tactivo_fijo_caract acar where acar.clave like ''%".$this->objParam->getParametro('caractFilter')."%'' and acar.valor like ''%".$this->objParam->getParametro('caractValue')."%'')");
        }

        //Filtro por movimientos
        //Transferencia, Devolucion
        if($this->objParam->getParametro('codMov')=='transf'||$this->objParam->getParametro('codMov')=='devol'){
            $this->objParam->addFiltro("afij.id_funcionario = ".$this->objParam->getParametro('id_funcionario_mov'));
        }
        //Alta
        if($this->objParam->getParametro('codMov')=='alta'|| $this->objParam->getParametro('codMov')=='baja'|| $this->objParam->getParametro('codMov')=='reval'|| $this->objParam->getParametro('codMov')=='deprec'|| $this->objParam->getParametro('codMov')=='actua'||$this->objParam->getParametro('codMov')=='desuso'||$this->objParam->getParametro('codMov')=='incdec'||$this->objParam->getParametro('codMov')=='tranfdep'){
            $this->objParam->addFiltro("afij.id_depto = ".$this->objParam->getParametro('id_depto_mov'));
            $this->objParam->addFiltro("afij.estado = "."''".$this->objParam->getParametro('estado_mov')."''");
        }
        if($this->objParam->getParametro('codMov')=='asig'){
            $this->objParam->addFiltro("afij.en_deposito = ''".$this->objParam->getParametro('en_deposito_mov')."''");
            $this->objParam->addFiltro("afij.id_depto = ".$this->objParam->getParametro('id_depto_mov'));
        }



        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODActivoFijo','listarActivoFijoFecha');
        } else{
            $this->objFunc=$this->create('MODActivoFijo');

            $this->res=$this->objFunc->listarActivoFijoFecha($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function consultaQR(){
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->consultaQR($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarActivosNoAsignados(){
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->listarActivosNoAsignados($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function repCodigoQRVarios(){
        $nombreArchivo = 'CodigoAF'.uniqid(md5(session_id())).'.pdf';

        if($this->objParam->getParametro('id_activo_fijo')!=''){
            $this->objParam->addFiltro("kaf.id_activo_fijo in (".$this->objParam->getParametro('id_activo_fijo').")");
        }

        if($this->objParam->getParametro('id_clasificacion')!=''){
            $this->objParam->addFiltro("kaf.id_clasificacion in (
					WITH RECURSIVE t(id,id_fk,nombre,n) AS (
    				SELECT l.id_clasificacion,l.id_clasificacion_fk, l.nombre,1
    				FROM kaf.tclasificacion l
    				WHERE l.id_clasificacion = ".$this->objParam->getParametro('id_clasificacion')."
    				UNION ALL
    				SELECT l.id_clasificacion,l.id_clasificacion_fk, l.nombre,n+1
    				FROM kaf.tclasificacion l, t
    				WHERE l.id_clasificacion_fk = t.id
					)
					SELECT id
					FROM t)");
        }
        //BVP
        if($this->objParam->getParametro('id_clasificacion_multi')!=''){
            $this->objParam->addFiltro("kaf.id_clasificacion in (
					WITH RECURSIVE t(id,id_fk,nombre,n) AS (
    				SELECT l.id_clasificacion,l.id_clasificacion_fk, l.nombre,1
    				FROM kaf.tclasificacion l
    				WHERE l.id_clasificacion in (".$this->objParam->getParametro('id_clasificacion_multi').")
    				UNION ALL
    				SELECT l.id_clasificacion,l.id_clasificacion_fk, l.nombre,n+1
    				FROM kaf.tclasificacion l, t
    				WHERE l.id_clasificacion_fk = t.id
					)
					SELECT id
					FROM t)");
        }//
        $dataSource = $this->listarCodigoQRVarios();

        //parametros basicos
        $orientacion = 'L';
        $titulo = 'Códigos Activos Fijos';

        //$width = 40;
        //$height = 20;

        $width = 160;
        $height = 80;

        $this->objParam->addParametro('orientacion',$orientacion);
        $this->objParam->addParametro('tamano',array($width, $height));
        $this->objParam->addParametro('titulo_archivo',$titulo);
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        //var_dump($dataSource->getDatos());
        //exit;
        $clsRep = $dataSource->getDatos();

        $reporte = new RCodigoQRAF_v1($this->objParam);


        //eval('$reporte = new '.$clsRep[0]['v_clase_reporte'].'($this->objParam);');

        $reporte->datosHeader( 'varios', $dataSource->getDatos());
        $reporte->generarReporte();
        $reporte->output($reporte->url_archivo,'F');

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

    function listarCodigoQRVarios(){
        $this->objFunc = $this->create('MODActivoFijo');

        $datos = $this->objFunc->listarCodigoQRVarios($this->objParam);

        if($datos->getTipo() == 'EXITO'){
            return $datos;
        } else
        {
            $datos->imprimirRespuesta($datos->generarJson());
            exit;
        }
    }

    function reportesAFGlobal(){

        //Filtros Reporte
        if($this->objParam->getParametro('estado')!= ''){
            $this->objParam->addFiltro("(taf.estado = ''".$this->objParam->getParametro('estado')."'' OR taf.estado is null)");
        }
        if($this->objParam->getParametro('id_oficina')!=''){
            $this->objParam->addFiltro("tof.id_oficina in (".$this->objParam->getParametro('id_oficina').")");
        }
        if($this->objParam->getParametro('id_clasificacion')!=''){
            $this->objParam->addFiltro("niv.id_clasificacion in (
			
					WITH RECURSIVE t(id,id_fk,nombre,n) AS (
    				SELECT l.id_clasificacion,l.id_clasificacion_fk, l.nombre,1
    				FROM kaf.tclasificacion l
    				WHERE l.id_clasificacion in (".$this->objParam->getParametro('id_clasificacion').")
                    
    				UNION ALL
                    
    				SELECT l.id_clasificacion,l.id_clasificacion_fk, l.nombre,n+1
    				FROM kaf.tclasificacion l, t
    				WHERE l.id_clasificacion_fk = t.id
					)
					SELECT id
					FROM t)");
        }
        if($this->objParam->getParametro('id_lugar')!=''){
            $this->objParam->addFiltro("tlug.id_lugar =".$this->objParam->getParametro('id_lugar'));
        }
        if($this->objParam->getParametro('nro_cbte_asociado')!=''){
            $this->objParam->addFiltro("taf.nro_cbte_asociado = "."''".$this->objParam->getParametro('nro_cbte_asociado')."''");
        }
        if($this->objParam->getParametro('id_cat_estado_fun')!=''){
            if($this->objParam->getParametro('id_cat_estado_fun')==411){
            }else{
                $this->objParam->addFiltro("taf.id_cat_estado_fun =".$this->objParam->getParametro('id_cat_estado_fun'));
            }
        }
        /*para motos de monto_compra kaf.activo_fijo*/
        if($this->objParam->getParametro('column_busque')!=''){
            if($this->objParam->getParametro('column_busque')=='1'){
                if($this->objParam->getParametro('txtMontoSup')!=''){
                    $this->objParam->addFiltro("taf.monto_compra_orig >= "."''".$this->objParam->getParametro('txtMontoSup')."''");
                }
                if($this->objParam->getParametro('txtMontoInf')!=''){
                    $this->objParam->addFiltro("taf.monto_compra_orig <= "."''".$this->objParam->getParametro('txtMontoInf')."''");
                }
            }elseif($this->objParam->getParametro('column_busque')=='2'){
                if($this->objParam->getParametro('txtMontoSup')!=''){
                    $this->objParam->addFiltro("taf.monto_compra_orig_100 >= "."''".$this->objParam->getParametro('txtMontoSup')."''");
                }
                if($this->objParam->getParametro('txtMontoInf')!=''){
                    $this->objParam->addFiltro("taf.monto_compra_orig_100 <= "."''".$this->objParam->getParametro('txtMontoInf')."''");
                }
            }else{
                if($this->objParam->getParametro('txtMontoSup')!=''){
                    $this->objParam->addFiltro("taf.monto_compra >= "."''".$this->objParam->getParametro('txtMontoSup')."''");
                }
                if($this->objParam->getParametro('txtMontoInf')!=''){
                    $this->objParam->addFiltro("taf.monto_compra <= "."''".$this->objParam->getParametro('txtMontoInf')."''");
                }
            }
        }
        if($this->objParam->getParametro('configuracion_reporte') != 'pendientes_aprobacion') {
            if($this->objParam->getParametro('id_depto')!=''){
                if($this->objParam->getParametro('id_depto')==3){
                    if($this->objParam->getParametro('configuracion_reporte') != 'sin_asignacion'){
                        $this->objParam->addFiltro("taf.id_depto in (7,47)");
                    }
                }
                else{
                    if($this->objParam->getParametro('configuracion_reporte') != 'sin_asignacion'){
                        $this->objParam->addFiltro("taf.id_depto = ".$this->objParam->getParametro('id_depto'));
                    }
                    
                }
            }
        }
        if($this->objParam->getParametro('nr_factura')!=''){
            $this->objParam->addFiltro("taf.documento = ''".$this->objParam->getParametro('nr_factura')."''");
        }
        if($this->objParam->getParametro('id_proveedor')!=''){
            $this->objParam->addFiltro("taf.id_proveedor = ".$this->objParam->getParametro('id_proveedor'));
        }
        if($this->objParam->getParametro('tramite_compra')!=''){
            $this->objParam->addFiltro("taf.tramite_compra = ''".$this->objParam->getParametro('tramite_compra')."''");
        }
        if($this->objParam->getParametro('nro_serie')!=''){
            $this->objParam->addFiltro("taf.nro_serie = ''".$this->objParam->getParametro('nro_serie')."''");
        }
        ///
        if($this->objParam->getParametro('tipo_activo')== 1){
            $this->objParam->addFiltro("niv.tipo_activo  = ''tangible''");
        }else if($this->objParam->getParametro('tipo_activo')== 2){
            $this->objParam->addFiltro("niv.tipo_activo = ''intangible''");
        }
//        else
//        $this->objParam->addFiltro("niv.tipo_activo in  (''tangible'', ''intangible'')");
//        }


        if($this->objParam->getParametro('configuracion_reporte') == 'pendientes_aprobacion') {
            //fecha
//          
            if ($this->objParam->getParametro('fecha_ini') != '' && $this->objParam->getParametro('fecha_fin') != '') {
                $this->objParam->addFiltro("(mo.fecha_mov::date  BETWEEN ''%" . $this->objParam->getParametro('fecha_ini') . "%''::date  and ''%" . $this->objParam->getParametro('fecha_fin') . "%''::date)");
            }

            if ($this->objParam->getParametro('fecha_ini') != '' && $this->objParam->getParametro('fecha_fin') == '') {
                $this->objParam->addFiltro("(mo.fecha_mov  >= ''%" . $this->objParam->getParametro('fecha_ini') . "%''::date)");
            }

            if ($this->objParam->getParametro('fecha_ini') == '' && $this->objParam->getParametro('fecha_fin') != '') {
                $this->objParam->addFiltro("(mo.fecha_mov  <= ''%" . $this->objParam->getParametro('fecha_fin') . "%''::date)");
            }

            if($this->objParam->getParametro('id_depto')!=''){
                if($this->objParam->getParametro('id_depto')==3){
                    $this->objParam->addFiltro("mo.id_depto in (7,47)");
                }
                else{
                    $this->objParam->addFiltro("mo.id_depto = ".$this->objParam->getParametro('id_depto'));
                }
            }
            //para el estado pre
            if($this->objParam->getParametro('estado_mo')== 1){
                $this->objParam->addFiltro("mo.estado  = ''borrador''");
            }else if($this->objParam->getParametro('estado_mo')== 2){
                $this->objParam->addFiltro("mo.estado = ''vbaf''");
            }else if($this->objParam->getParametro('estado_mo')== 3){
                $this->objParam->addFiltro("mo.estado = ''finalizado''");
            }

            else {
                $this->objParam->addFiltro("mo.estado in  (''borrador'', ''vbaf'', ''finalizado'')");
            }


        }
        if($this->objParam->getParametro('configuracion_reporte') == 'sin_asignacion') {
            //fecha
            if ($this->objParam->getParametro('fecha_ini') != '' && $this->objParam->getParametro('fecha_fin') != '') {
                $this->objParam->addFiltro("(afij.fecha_ini_dep::date  BETWEEN ''%" . $this->objParam->getParametro('fecha_ini') . "%''::date  and ''%" . $this->objParam->getParametro('fecha_fin') . "%''::date)");
            }

            if ($this->objParam->getParametro('fecha_ini') != '' && $this->objParam->getParametro('fecha_fin') == '') {
                $this->objParam->addFiltro("(afij.fecha_ini_dep  >= ''%" . $this->objParam->getParametro('fecha_ini') . "%''::date)");
            }

            if ($this->objParam->getParametro('fecha_ini') == '' && $this->objParam->getParametro('fecha_fin') != '') {
                $this->objParam->addFiltro("(afij.fecha_ini_dep  <= ''%" . $this->objParam->getParametro('fecha_fin') . "%''::date)");
            }
            if($this->objParam->getParametro('id_depto')!=''){
                if($this->objParam->getParametro('id_depto')==3){
                    $this->objParam->addFiltro("mov.id_depto in (7,47)");
                }
                else{
                    $this->objParam->addFiltro("mov.id_depto = ".$this->objParam->getParametro('id_depto'));
                }
            }


        }

        //

        //Llamada al Modelo, consulta BD
        $this->objFunc = $this->create('MODActivoFijo');
        $this->res = $this->objFunc->reportesAFGlobal($this->objParam);

        $this->objFunc=$this->create('MODActivoFijo');
        $this->res2=$this->objFunc->listaLug($this->objParam);
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res3=$this->objFunc->proveedorActivoRep($this->objParam);
        //$this->res->imprimirRespuesta($this->res->generarJson());
        //AF PENDIENTES DE APROBACION
        $this->objFunc = $this->create('MODActivoFijo');
        $this->res4 = $this->objFunc->reportesPendientesAprob($this->objParam);
//        var_dump($this->res4);
        //AF SIN ASIGNACION
        $this->objFunc = $this->create('MODActivoFijo');
        $this->res5 = $this->objFunc->reportesSinAsignacion($this->objParam);

        //ACTIVOS POR DEPOSITO        
        $this->objFunc = $this->create('MODActivoFijo');
        $this->res6 = $this->objFunc->reporteActiDepoFuncio($this->objParam);	        
		//var_dump($this->res6->datos);exit;	
        //Configuracion Reporte
        if($this->objParam->getParametro('configuracion_reporte')  == 'compras_gestion'){

            if($this->objParam->getParametro('formato_reporte')=='pdf'){
                $nombreArchivo = uniqid(md5(session_id()).'[Reporte - Compras x Gestion]').'.pdf';
            }
            else{
                $nombreArchivo = uniqid(md5(session_id()).'[Reporte - Compras x Gestion]').'.xls';
            }

        }else if($this->objParam->getParametro('configuracion_reporte') == 'detalle_af'){

            if($this->objParam->getParametro('formato_reporte')=='pdf'){
                $nombreArchivo = uniqid(md5(session_id()).'[Detalle - Activos Fijos]').'.pdf';
            }
            else{
                $nombreArchivo = uniqid(md5(session_id()).'[Detalle - Activos Fijos]').'.xls';
            }

        }else if($this->objParam->getParametro('configuracion_reporte') == 'pendientes_aprobacion'){

            if($this->objParam->getParametro('formato_reporte')=='pdf'){
                $nombreArchivo = uniqid(md5(session_id()).'[Reporte - Pendientes Aprobacion]').'.pdf';
            }
            else{
                $nombreArchivo = uniqid(md5(session_id()).'[Reporte - Pendientes Aprobacion]').'.xls';
            }

        }else if($this->objParam->getParametro('configuracion_reporte') == 'sin_asignacion'){

            if($this->objParam->getParametro('formato_reporte')=='pdf'){
                $nombreArchivo = uniqid(md5(session_id()).'[Reporte - Sin Asignacion]').'.pdf';
            }
            else{
                $nombreArchivo = uniqid(md5(session_id()).'[Reporte - Sin Asignacion]').'.xls';
            }
        }
        else if($this->objParam->getParametro('configuracion_reporte')=='acti_fun_dep'){
            if($this->objParam->getParametro('formato_reporte')=='pdf'){
                $nombreArchivo = uniqid(md5(session_id()).'[Reporte - Activos en Deposito]').'.pdf';
            }
            else{
                $nombreArchivo = uniqid(md5(session_id()).'[Reporte - Activos en Deposito').'.xls';
            }            
        }
//echo var_dump($this->objParam);exit(); fRnk: desde aquí llama a reportes de uso general
        //Definicion de parametros adicionales para el reporte.
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('titulo_archivo','ComprasGestión');
        $this->objParam->addParametro('desc_nombre',$this->objParam->getParametro('desc_nombre'));
        $this->objParam->addParametro('gestion_multi',$this->objParam->getParametro('gestion_multi'));
        $this->objParam->addParametro('activo_multi',$this->objParam->getParametro('activo_multi'));

        if($this->objParam->getParametro('configuracion_reporte')  == 'compras_gestion') {
            if ($this->objParam->getParametro('formato_reporte') == 'pdf') {
                //Orientacion Hoja Documento.
                $this->objParam->addParametro('orientacion','P');
                //Instancia la clase de pdf
                $this->objReporteFormato = new RCompraGestionPDF ($this->objParam);
                $this->objReporteFormato->setDatos($this->res->datos,$this->res2->datos,$this->res3->datos);
                $this->objReporteFormato->generarReporte();
                $this->objReporteFormato->output($this->objReporteFormato->url_archivo, 'F');
            } else {
                $reporte = new RCompraGestionXls($this->objParam);
                $reporte->setDatos($this->res->datos,$this->res2->datos,$this->res3->datos);
                $reporte->generarReporte();
            }
        }else if($this->objParam->getParametro('configuracion_reporte') == 'detalle_af'){
            if ($this->objParam->getParametro('formato_reporte') == 'pdf') {
                //Orientacion Hoja Documento.
                $this->objParam->addParametro('orientacion','L');
                $this->objReporteFormato = new RDetalleAFPDF ($this->objParam);
                $this->objReporteFormato->setDatos($this->res->datos,$this->res2->datos,$this->res3->datos);
                $this->objReporteFormato->generarReporte();
                $this->objReporteFormato->output($this->objReporteFormato->url_archivo, 'F');
            } else {
                $reporte = new RDetalleAFXls($this->objParam);
                $reporte->setDatos($this->res->datos,$this->res2->datos,$this->res3->datos);
                $reporte->generarReporte();
            }
        }else if($this->objParam->getParametro('configuracion_reporte') == 'pendientes_aprobacion'){
            if ($this->objParam->getParametro('formato_reporte') == 'pdf') {
                //Orientacion Hoja Documento.
                $this->objParam->addParametro('orientacion','P');
                $this->objReporteFormato = new RPendientesAprobAFPDF ($this->objParam);
                $this->objReporteFormato->setDatos($this->res4->datos);
                $this->objReporteFormato->generarReporte();
                $this->objReporteFormato->output($this->objReporteFormato->url_archivo, 'F');
            } else {
                $reporte = new RPendientesAprobAFXls($this->objParam);
                $reporte->setDatos($this->res4->datos);

                $reporte->generarReporte();
            }
        }else if($this->objParam->getParametro('configuracion_reporte') == 'sin_asignacion'){
            if ($this->objParam->getParametro('formato_reporte') == 'pdf') {
                //Orientacion Hoja Documento.
                $this->objParam->addParametro('orientacion','L');
                $this->objReporteFormato = new RSinAsignacionAFPDF ($this->objParam);
                $this->objReporteFormato->setDatos($this->res5->datos);
                $this->objReporteFormato->generarReporte();
                $this->objReporteFormato->output($this->objReporteFormato->url_archivo, 'F');
            } else {
                $reporte = new RSinAsignacionAFXls($this->objParam);
                $reporte->setDatos($this->res5->datos);
                $reporte->generarReporte();
            }
        }
        else if($this->objParam->getParametro('configuracion_reporte') == 'acti_fun_dep'){        			
            if ($this->objParam->getParametro('formato_reporte') == 'pdf') {
                //Orientacion Hoja Documento.
                $this->objParam->addParametro('orientacion','L');
                $this->objReporteFormato = new RActiDepaPFunFPDF ($this->objParam);
                $this->objReporteFormato->setDatos($this->res6->datos);
                $this->objReporteFormato->generarReporte();
                $this->objReporteFormato->output($this->objReporteFormato->url_archivo, 'F');
            } else {
                $reporte = new RActiDepaPFunAFXls($this->objParam);
                $reporte->setDatos($this->res6->datos);
                $reporte->generarReporte();
            }
        }		

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

    //servicio que retorna los activos de un funcionario
    function getActivosFijosFuncionarioBoa(){
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->getActivosFijosFuncionarioBoa($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


    ////////////////////////////////////////
    function ListaDetActivo(){
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->ListaDetActivo($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
        //var_dump($this->res);exit;
    }

    function ReporteDetalleActivos(){
        // var_dump($this->objParam);exit;

        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->ReporteDetalleActivos($this->objParam);


        if($this->objParam->getParametro('formato_reporte') == 'pdf'){
            $nombreArchivo = uniqid(md5(session_id()).'[Reporte Activo por Grupo]').'.pdf';
        }
        else{
            $nombreArchivo = uniqid(md5(session_id()).'[Reporte Activo por Grupo]').'.xls';
        }
        //Definicion de parametros adicionales para el reporte.
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('titulo_archivo','Reporte Activos');
        $this->objParam->addParametro('desc_nombre',$this->objParam->getParametro('desc_nombre'));



        if ($this->objParam->getParametro('formato_reporte')=='pdf') {
            //Orientacion Hoja Documento.
            $this->objParam->addParametro('orientacion','P');
            //Instancia la clase de pdf

            $this->objReporteFormato = new RActivoFijoPDF($this->objParam);
            $this->objReporteFormato->setDatos($this->res->datos);
            $this->objReporteFormato->generarReporte();
            $this->objReporteFormato->output($this->objReporteFormato->url_archivo, 'F');
        } else {
            $reporte = new RActivoFijoXls($this->objParam);
            $reporte->setDatos($this->res->datos);
            $reporte->generarReporte();
        }

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function ReporteActivoEnDetalle(){

        //var_dump('hoal');exit;
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->ReporteActivoEnDetalle($this->objParam);

        if($this->objParam->getParametro('formato_reporte') == 'pdf'){
            $nombreArchivo = uniqid(md5(session_id()).'[Reporte Activo en Detalle]').'.pdf';
        }else{
            $nombreArchivo = uniqid(md5(session_id()).'[Reporte Activo en Detalle]').'.xls';
        }
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('titulo_archivo','Reporte Activos');
        $this->objParam->addParametro('desc_nombre',$this->objParam->getParametro('desc_nombre'));


        if ($this->objParam->getParametro('formato_reporte')=='pdf') {
            //Orientacion Hoja Documento.
            $this->objParam->addParametro('orientacion','L');
            //Instancia la clase de pdf

            $this->objReporteFormato = new RActivoDetallePDF($this->objParam);
            $this->objReporteFormato->setDatos($this->res->datos);
            $this->objReporteFormato->generarReporte();
            $this->objReporteFormato->output($this->objReporteFormato->url_archivo, 'F');
        }else{
            $reporte = new RActivoFijoDetalleXls($this->objParam);
            $reporte->setDatos($this->res->datos);
            $reporte->generarReporte();
        }
        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function lecturaQRAP(){
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->lecturaQRAP($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function proveedorActivo(){
        $this->objParam->defecto('ordenacion','id_proveedor');
        $this->objParam->defecto('dir_ordenacion','asc');
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->proveedorActivo($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function proveedorActivoRep(){
        $this->objParam->defecto('ordenacion','id_proveedor');
        $this->objParam->defecto('dir_ordenacion','asc');
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->proveedorActivoRep($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function listarAF(){
        $this->objParam->defecto('ordenacion', 'id_activo_fijo');
        $this->objParam->defecto('dir_ordenacion', 'asc');
        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODActivoFijo', 'listarActivoFijo');
        } else {
            $this->objFunc = $this->create('MODActivoFijo');

            $this->res = $this->objFunc->listarAF($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function listarAFUnidSol(){
        $this->objParam->defecto('ordenacion','id_uo');
        $this->objParam->defecto('dir_ordenacion','asc');
        $this->objParam->addFiltro("uo.presupuesta= ''si''");
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->listarAFUnidSol($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function listarActivoFijoHistorico(){
        $this->objParam->defecto('ordenacion','id_activo_fijo_hist');
        $this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('id_activo_fijo')!=''){
            $this->objParam->addFiltro("afh.id_activo_fijo = ".$this->objParam->getParametro('id_activo_fijo'));
        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODActivoFijo','listarActivoFijoHistorico');
        } else{
            $this->objFunc=$this->create('MODActivoFijo');

            $this->res=$this->objFunc->listarActivoFijoHistorico($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function reporteHistoricoAF(){

        if($this->objParam->getParametro('id_activo_fijo')!=''){
            $this->objParam->addFiltro("afh.id_activo_fijo = ".$this->objParam->getParametro('id_activo_fijo'));
        }

        if ($this->objParam->getParametro('id_proceso_wf') != '') {
            $this->objParam->addFiltro("afh.id_proceso_wf = ". $this->objParam->getParametro('id_proceso_wf'));
        }

        $this->objFunc = $this->create('MODActivoFijo');
        $this->res = $this->objFunc->reporteHistoricoAF($this->objParam);


        //obtener titulo del reporte
        $titulo = 'Historico';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo = uniqid(md5(session_id()) . $titulo);
        $nombreArchivo .= '.pdf';
        $this->objParam->addParametro('orientacion', 'P');
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);


        $this->objReporteFormato = new RHistoricoAF($this->objParam);

        $this->objReporteFormato->reporteGeneral($this->res->getDatos());

        $this->objReporteFormato->output($this->objReporteFormato->url_archivo, 'F');

        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
            'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);


        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
//        var_dump($firma); exit;
    }


    function verificarNoTramiteCompra(){
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->verificarNoTramiteCompra($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }    

    function listarFuncionarioUltCargo(){
        $this->objFunc=$this->create('MODActivoFijo');
        $this->res=$this->objFunc->listarFuncionarioUltCargo($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
}
?>