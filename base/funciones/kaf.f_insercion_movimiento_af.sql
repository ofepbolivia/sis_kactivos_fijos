CREATE OR REPLACE FUNCTION kaf.f_insercion_movimiento_af (
  p_id_usuario integer,
  p_parametros public.hstore
)
RETURNS integer AS
$body$
/*
Autor: RCM
Fecha: 03/08/2017
Descripción: Función para crear un nuevo movimiento
*/
DECLARE

    v_nombre_funcion		varchar;
    v_resp					varchar;
    v_registros             record;
    v_id_cat_estado_fun     integer;
    v_id_movimiento_af      integer;
    v_aux                   record;
    v_id_moneda_base        integer;
    v_movi					record;
    v_movimiento			record;
    v_extis					record;
	v_codigo_mov			varchar;
BEGIN

    --Nombre de la función
    v_nombre_funcion = 'kaf.f_insercion_movimiento_af';

    select
    mov.estado,
    mov.codigo,
    cat.codigo as codigo_movimiento,
    mov.id_depto
    into
    v_registros
    from kaf.tmovimiento mov
    inner join  param.tcatalogo cat on cat.id_catalogo = mov.id_cat_movimiento
    where mov.id_movimiento = (p_parametros->'id_movimiento')::integer;


    /*if not kaf.f_validar_ins_mov_af((p_parametros->'id_movimiento')::integer,(p_parametros->'id_activo_fijo')::integer) then
       raise exception 'Error al validar activo fijo';
    end if;*/

    if v_registros.estado != 'borrador' THEN
       raise exception 'Solo puede insertar activos en movimientos en borrador';
    end if;

    --Obtiene estado funcional del activo fijo
    select
    id_cat_estado_fun
    into
    v_id_cat_estado_fun
    from kaf.tactivo_fijo
    where id_activo_fijo = (p_parametros->'id_activo_fijo')::integer;

    --Verificamos que el activo no esté duplicado
    if exists(select 1
            from kaf.tmovimiento_af maf
            where maf.id_movimiento = (p_parametros->'id_movimiento')::integer
            and  maf.id_activo_fijo = (p_parametros->'id_activo_fijo')::integer
            and maf.estado_reg = 'activo') then

        for v_aux in select * from kaf.tmovimiento_af maf
            where maf.id_movimiento = (p_parametros->'id_movimiento')::integer loop
            raise notice '%',v_aux.id_activo_fijo;
        end loop;

         raise exception 'El activo ya se encuentra registrado en el movimiento actual';
    end if;

		select pro.nro_tramite, cat.codigo
        into v_extis
        from kaf.tmovimiento_af maf
        inner join kaf.tmovimiento mo on mo.id_movimiento = maf.id_movimiento
        inner join kaf.tmovimiento_motivo mov on mov.id_movimiento_motivo = mo.id_movimiento_motivo
        inner join wf.tproceso_wf pro on pro.id_proceso_wf=mo.id_proceso_wf
        inner join param.tcatalogo cat on cat.id_catalogo = mov.id_cat_movimiento
        where maf.id_activo_fijo = (p_parametros->'id_activo_fijo')::integer and mo.estado<>'finalizado'
        and mov.motivo <> 'Depreciación'
        and cat.codigo not in ('ajuste', 'retiro', 'baja','reval');

      select  cat.codigo
      	into v_codigo_mov
      from kaf.tmovimiento mov
      inner join param.tcatalogo cat on cat.id_catalogo = mov.id_cat_movimiento
      where mov.id_movimiento = (p_parametros->'id_movimiento')::integer;

    if v_codigo_mov not in ('ajuste', 'retiro', 'baja','reval') then
        if v_extis is not null then
            if v_extis.codigo not in ( 'retiro') then
                raise exception 'El activo esta registrado en el movimiento %',v_extis.nro_tramite;
            end if;
        end if;
    end if;

    --Se obtiene la moneda base
    v_id_moneda_base  = param.f_get_moneda_base();

    --Inserción del registro
    insert into kaf.tmovimiento_af(
        id_movimiento,
        id_activo_fijo,
        id_cat_estado_fun,
        id_movimiento_motivo,
        estado_reg,
        importe,
        vida_util,
        fecha_reg,
        usuario_ai,
        id_usuario_reg,
        id_usuario_ai,
        id_usuario_mod,
        fecha_mod,
        depreciacion_acum,
        id_moneda,
        importe_ant,
        vida_util_ant
		-- breydi.vasquez (09/01/2020) para nuevo tipo ajuste de vida util
        ,vida_util_residual,
        deprec_acum_ant,
        valor_residual,
        monto_vig_actu,
        observacion,
        id_activo_fijo_valor,
        deprec_acu_ges_ant
    ) values(
        (p_parametros->'id_movimiento')::integer,
        (p_parametros->'id_activo_fijo')::integer,
        v_id_cat_estado_fun,
        (p_parametros->'id_movimiento_motivo')::integer,
        'activo',
        (p_parametros->'importe')::numeric,
        (p_parametros->'vida_util')::integer,
        now(),
        (p_parametros->'_nombre_usuario_ai')::varchar,
        p_id_usuario,
        (p_parametros->'_id_usuario_ai')::integer,
        null,
        null,
        (p_parametros->'depreciacion_acum')::numeric,
        v_id_moneda_base,
        (p_parametros->'importe_ant')::numeric,
        (p_parametros->'vida_util_ant')::integer
		-- breydi.vasquez (09/01/2020) para nuevo tipo ajuste de vida util
        ,(p_parametros->'vida_util_residual')::integer,
        (p_parametros->'deprec_acum_ant')::numeric,
        (p_parametros->'valor_residual')::numeric,
        (p_parametros->'monto_vig_actu')::numeric,
        (p_parametros->'observacion')::text,
        (p_parametros->'id_activo_fijo_valor')::integer,
        (p_parametros->'deprec_acu_ges_ant')::numeric
    ) returning id_movimiento_af into v_id_movimiento_af;

     /*--------------ACTUALIZANDO KMOVIMIENTO CON LOS DATOS RECUPERADOS
     ---------------------------------------------------------------------------*/
                                select
                                into
             					v_movi
                                pre.id_preingreso_det,
                                pre.movimiento,
                                preing.id_proceso_wf
                                from alm.tpreingreso_det pre
                                inner join alm.tpreingreso preing on preing.id_preingreso = pre.id_preingreso
                                inner join kaf.tactivo_fijo activo on activo.id_preingreso_det = pre.id_preingreso_det
                                where activo.id_activo_fijo = (p_parametros->'id_activo_fijo')::integer;

                                --RAISE exception 'LLEGA aqui el valor 1234: %',v_movi.movimiento;

                         		select into
                            	v_movimiento
                                  af.id_movimiento
                                  from kaf.tmovimiento movi
                                  inner join kaf.tmovimiento_af af on af.id_movimiento = movi.id_movimiento
                                  inner join kaf.tactivo_fijo act on act.id_activo_fijo = af.id_activo_fijo
                                  where act.id_preingreso_det = v_movi.id_preingreso_det;


                                update kaf.tmovimiento mov set
                                tipo_movimiento = v_movi.movimiento,
                                id_proceso_wf_doc = v_movi.id_proceso_wf
                                where mov.id_movimiento = v_movimiento.id_movimiento;


            ---------------------------------------------------------------------------------

    ------------
	--Respuesta
    ------------
    return v_id_movimiento_af;

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
