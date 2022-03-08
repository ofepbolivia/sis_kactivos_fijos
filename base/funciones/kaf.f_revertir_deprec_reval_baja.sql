CREATE OR REPLACE FUNCTION kaf.f_revertir_deprec_reval_baja (
)
RETURNS varchar AS
$body$
/*************************************************************************ajuste*
 SISTEMA:		Activos Fijos
 FUNCION: 		kaf.f_revertir_deprec_reval_baja
 DESCRIPCION:   Revierte depreciaciones finalizadas
 AUTOR: 		 (admin)
 FECHA:	        05-05-2016 16:41:21
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION: MODIFICACIONES PARA EL CASO DE VIATICOS
 AUTOR: RCM
 FECHA: 05/09/2017
***************************************************************************/

DECLARE

	v_resp		        varchar;
	v_nombre_funcion    text;
	v_mensaje_error     text;
    v_rec               record;

BEGIN

    v_nombre_funcion = 'kaf.f_revertir_deprec';

    --WF: activa los estados iniciales de los flujos de todos los procesos
    update wf.testado_wf set
    estado_reg = 'activo'
    from wf.testado_wf ew
    inner join wf.ttipo_estado tew
    on tew.id_tipo_estado = ew.id_tipo_estado
    where ew.id_proceso_wf in (select
                      mov.id_proceso_wf
                      from kaf.tmovimiento mov
                      inner join param.tcatalogo cat
                      on cat.id_catalogo = mov.id_cat_movimiento
                      where cat.codigo in ('deprec','baja','retiro','reval','ajuste'))
    and tew.codigo = 'borrador' --38
    and wf.testado_wf.id_estado_wf = ew.id_estado_wf;

    --WF: setea a  null los estados anteriores
    update wf.testado_wf set
    id_estado_anterior = null
    from wf.testado_wf ew
    inner join wf.ttipo_estado tew
    on tew.id_tipo_estado = ew.id_tipo_estado
    where ew.id_proceso_wf in (select
                      mov.id_proceso_wf
                      from kaf.tmovimiento mov
                      inner join param.tcatalogo cat
                      on cat.id_catalogo = mov.id_cat_movimiento
                      where cat.codigo in ('deprec','baja','retiro','reval','ajuste'))
    --and tew.codigo = 'borrador' --38
    and wf.testado_wf.id_estado_wf = ew.id_estado_wf;

    --Actualiza el estado del wf en los movimientos
    update kaf.tmovimiento set
    id_estado_wf = ew.id_estado_wf,
    estado = 'borrador'
    from wf.testado_wf ew
    inner join wf.ttipo_estado tew
    on tew.id_tipo_estado = ew.id_tipo_estado
    where ew.id_proceso_wf in (select
                      mov.id_proceso_wf
                      from kaf.tmovimiento mov
                      inner join param.tcatalogo cat
                      on cat.id_catalogo = mov.id_cat_movimiento
                      where cat.codigo in ('deprec','baja','retiro','reval','ajuste'))
    and tew.codigo = 'borrador' --38
    and kaf.tmovimiento.id_proceso_wf = ew.id_proceso_wf;

    --WF: elimina el historial delos flujos con excepcion de los iniciales
    delete from wf.testado_wf
    where id_estado_wf in (select
                            ew.id_estado_wf
                            from wf.testado_wf ew
                            inner join wf.ttipo_estado tew
                            on tew.id_tipo_estado = ew.id_tipo_estado
                            where ew.id_proceso_wf in (select
                                                      mov.id_proceso_wf
                                                      from kaf.tmovimiento mov
                                                      inner join param.tcatalogo cat
                                                      on cat.id_catalogo = mov.id_cat_movimiento
                                                      where cat.codigo in ('deprec','baja','retiro','reval','ajuste'))
                            and tew.codigo != 'borrador');

    -----------------
    --DEPRECIACIONES
    -----------------
    --Eliminar registros en kaf.tmovimiento_af_dep
    delete from kaf.tmovimiento_af_dep;
	alter sequence kaf.tmovimiento_af_dep_id_movimiento_af_dep_seq RESTART WITH 1;


    --Seteo en null de la fecha de ultima depreciacion de los activos fijos
    update kaf.tactivo_fijo_valores set
    fecha_ult_dep = null;
    
    update kaf.tmovimiento_af set
    respuesta = null;

    ---------------------------
    --REVALORIZACIONES MEJORAS
    ---------------------------
    update kaf.tactivo_fijo set
    cantidad_revaloriz = 0
    from kaf.tmovimiento_af maf
    inner join kaf.tmovimiento mov on mov.id_movimiento = maf.id_movimiento
    inner join param.tcatalogo cat on cat.id_catalogo = mov.id_cat_movimiento
    where cat.codigo = 'reval'
    and kaf.tactivo_fijo.id_activo_fijo = maf.id_activo_fijo;
    
    update kaf.tactivo_fijo_valores set
    fecha_fin = null;

    --Eliminacion de AFVs creados por revalorizacion
    delete from kaf.tactivo_fijo_valores where codigo like '%-RE%' or codigo like '%-G%' or codigo like '%-AJ%';
    delete from kaf.tactivo_fijo_valores where id_activo_fijo_valor_original is not null;

    --------
    --BAJAS
    --------
    update kaf.tactivo_fijo set
    estado = 'alta'
    from kaf.tmovimiento_af maf
    inner join kaf.tmovimiento mov on mov.id_movimiento = maf.id_movimiento
    inner join param.tcatalogo cat on cat.id_catalogo = mov.id_cat_movimiento
    where cat.codigo in ('baja','retiro')
    and kaf.tactivo_fijo.id_activo_fijo = maf.id_activo_fijo;    


    --Setea a null la fecha fin de todos los afv
    update kaf.tactivo_fijo_valores set
    fecha_fin = null
    from kaf.tmovimiento_af maf
    inner join kaf.tmovimiento mov on mov.id_movimiento = maf.id_movimiento
    inner join kaf.tactivo_fijo af on af.id_activo_fijo = maf.id_activo_fijo
    inner join param.tcatalogo cat on cat.id_catalogo = mov.id_cat_movimiento
    inner join kaf.tactivo_fijo_valores afv on afv.id_activo_fijo = af.id_activo_fijo
    where cat.codigo in ('reval','ajuste')
    and mov.estado != 'borrador'
    and kaf.tactivo_fijo_valores.id_activo_fijo_valor = afv.id_activo_fijo_valor;

    --Actualizar estado de activos que estaban de baja
    return 'hecho';
    

EXCEPTION

	WHEN OTHERS THEN
		v_resp='';
		v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
		v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
		v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
		raise exception '%',v_resp;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;