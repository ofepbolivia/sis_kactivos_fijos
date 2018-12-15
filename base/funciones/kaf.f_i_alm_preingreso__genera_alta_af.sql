CREATE OR REPLACE FUNCTION kaf.f_i_alm_preingreso__genera_alta_af (
  p_id_usuario integer,
  p_id_preingreso integer
)
RETURNS varchar AS
$body$
/*
Autor: RCM  (KPLIAN)
Fecha: 18/08/2017
Descripción: GENERACION DE ALTA DE ACTIVOS FIJOS DEL PREINGRESO
*/
DECLARE

    v_nombre_funcion    text;
    v_resp              varchar;
    v_rec               record;
    v_rec_det           record;
    v_rec_af            record;
    v_rec_af_det        record;
    v_id_cat_movimiento integer;
    v_id_movimiento     integer;
    v_id_movimiento_af  integer;

    v_desc_incas		varchar;

BEGIN

    v_nombre_funcion = 'kaf.f_i_alm_preingreso__genera_alta_af';

    --Búsqueda del preingreso en estado registrado
    if not exists(select 1 from alm.tpreingreso
                where id_preingreso = p_id_preingreso) then
        raise exception 'Preingreso no encontrado (%)',p_id_preingreso;
    end if;


    -----------------------------------
    --Creación de movimientos
    -----------------------------------
            select  ingas.desc_ingas
            into v_desc_incas
            from alm.tpreingreso_det predet
            inner join segu.tusuario usu1 on usu1.id_usuario = predet.id_usuario_reg
            left join segu.tusuario usu2 on usu2.id_usuario = predet.id_usuario_mod
            left join adq.tcotizacion_det cdet on cdet.id_cotizacion_det = predet.id_cotizacion_det
            left join adq.tsolicitud_det sdet on sdet.id_solicitud_det = cdet.id_solicitud_det
            left join param.tconcepto_ingas ingas on ingas.id_concepto_ingas = sdet.id_concepto_ingas
            inner join alm.tpreingreso pre on pre.id_preingreso = predet.id_preingreso
            where pre.id_preingreso = p_id_preingreso;

           -- raise exception '%, %', p_id_preingreso, v_desc_incas;
  /*
    select cat.id_catalogo
    into v_id_cat_movimiento
    from param.tcatalogo cat
    inner join param.tcatalogo_tipo ctip
    on ctip.id_catalogo_tipo = cat.id_catalogo_tipo
    where ctip.tabla = 'tmovimiento__id_cat_movimiento'
    and cat.codigo = 'alta';
 */
   -- raise exception '%', v_id_cat_movimiento;

    if (v_desc_incas = 'RENOVACION LICENCIAS DE SOFTWARE') THEN
          select cat.id_catalogo
          into v_id_cat_movimiento
          from param.tcatalogo cat
          inner join param.tcatalogo_tipo ctip
          on ctip.id_catalogo_tipo = cat.id_catalogo_tipo
          where ctip.tabla = 'tmovimiento__id_cat_movimiento'
          and cat.codigo = 'reval';
    ELSIF (v_desc_incas = 'ACTUALIZACION LICENCIAS DE SOFTWARE') THEN
    	  select cat.id_catalogo
          into v_id_cat_movimiento
          from param.tcatalogo cat
          inner join param.tcatalogo_tipo ctip
          on ctip.id_catalogo_tipo = cat.id_catalogo_tipo
          where ctip.tabla = 'tmovimiento__id_cat_movimiento'
          and cat.codigo = 'mejora';
     ELSE
     	    select cat.id_catalogo
            into v_id_cat_movimiento
            from param.tcatalogo cat
            inner join param.tcatalogo_tipo ctip
            on ctip.id_catalogo_tipo = cat.id_catalogo_tipo
            where ctip.tabla = 'tmovimiento__id_cat_movimiento'
            and cat.codigo = 'alta';
    end IF;

 --raise exception '%',v_id_cat_movimiento ;

    if v_id_cat_movimiento is null then
        raise exception 'No se encuentra registrado el Proceso de Alta. Comuníquese con el administrador del sistema.';
    end if;

    --Bucle del detalle del preingreso para crear un Movimiento por cada Depto. definido
    for v_rec in (select distinct id_depto
                from alm.tpreingreso_det
                where id_preingreso = p_id_preingreso
                and sw_generar = 'si') loop

        --Definción de parámetros
        select
        'N/D' as direccion,
        null as fecha_hasta,
        v_id_cat_movimiento as id_cat_movimiento,
        now()::date as fecha_mov,
        v_rec.id_depto as id_depto,
        --'Alta generado desde Preingresos' as glosa,
        'Generado desde Preingresos' as glosa,
        null as id_funcionario,
        null as id_oficina,
        null as _id_usuario_ai,
        p_id_usuario as id_usuario,
        null as _nombre_usuario_ai,
        null as id_persona,
        null as codigo,
        null as id_deposito,
        null as id_depto_dest,
        null as id_deposito_dest,
        null id_funcionario_dest,
        null as id_movimiento_motivo
        into v_rec_af;

        --Creación del movimiento
        v_id_movimiento = kaf.f_insercion_movimiento(p_id_usuario, hstore(v_rec_af));

        --Bucle de los preingreso detalle marcados para el alta
        for v_rec_det in (select afij.id_activo_fijo
                        from alm.tpreingreso_det pdet
                        inner join kaf.tactivo_fijo afij
                        on afij.id_preingreso_det = pdet.id_preingreso_det
                        where pdet.id_preingreso = p_id_preingreso
                        and pdet.id_depto = v_rec.id_depto
                        and pdet.sw_generar = 'si'
                        and afij.estado = 'registrado') loop

            --Definción de parámetros
            select
            v_id_movimiento as id_movimiento,
            v_rec_det.id_activo_fijo as id_activo_fijo,
            null as id_movimiento_motivo,
            null as importe,
            null as vida_util,
            null as _nombre_usuario_ai,
            null as _id_usuario_ai,
            null as depreciacion_acum
            into v_rec_af_det;

            --Inserción del movimiento
            v_id_movimiento_af = kaf.f_insercion_movimiento_af(p_id_usuario, hstore(v_rec_af_det));

        end loop;

    end loop;

    return 'Hecho';

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