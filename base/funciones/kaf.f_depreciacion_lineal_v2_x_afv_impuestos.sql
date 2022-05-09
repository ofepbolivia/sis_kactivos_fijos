CREATE OR REPLACE FUNCTION kaf.f_depreciacion_lineal_v2_x_afv_impuestos (
  p_id_usuario integer,
  p_id_activo_fijo_valor integer,
  p_fecha_hasta date
)
RETURNS varchar AS
$body$
/*
Autor: BVP
Fecha: 30/03/2021
Descripción: Depreciación lineal de activos fijos v2 Impuestos
*/
DECLARE

    v_resp                  varchar;
    v_nombre_funcion        varchar;
    v_rec                   record;
    v_fecha_hasta           date;
    v_ant_dep_acum          numeric;
    v_ant_dep_per           numeric;
    v_ant_monto_vigente     numeric;
    v_ant_vida_util         numeric;
    v_ant_monto_actualiz    numeric;
    v_gestion_previa        integer;
    v_gestion_dep           integer;
    v_tipo_cambio_anterior  numeric;
    v_rec_tc                record;
    v_dep_acum_actualiz     numeric;
    v_dep_per_actualiz      numeric;
    v_monto_actualiz        numeric;
    v_id_movimiento_af_dep  integer;
    v_nuevo_dep_mes         numeric;
    v_nuevo_dep_acum        numeric;
    v_nuevo_dep_per         numeric;
    v_nuevo_monto_vigente   numeric;
    v_nuevo_vida_util       integer;
    v_gestion_aux           integer;
    v_mes_aux               integer;
    v_mes_dep               date;
    v_mensaje               varchar;
    v_sw_control_dep        boolean = false;
    v_id_periodo_subsistema integer;
    v_id_subsistema			integer;
    v_res                   varchar;
    v_factor_ini			numeric;
BEGIN

    v_nombre_funcion = 'kaf.f_depreciacion_lineal_v2_x_afv_impuestos';

    --Obtención de la fecha tope de la depreciación
    v_fecha_hasta = p_fecha_hasta;

    --Obtención del subsistema para posterior verificación de período abierto
    select id_subsistema into v_id_subsistema
    from segu.tsubsistema
    where codigo = 'KAF';

    --Recorrido de todos los activos fijos a depreciar
    for v_rec in select
                afv.id_activo_fijo,
                afv.id_activo_fijo_valor,
                afv.id_moneda_dep,
                afv.fecha_ult_dep_real,
                case when afv1.tipo_modificacion = 'ajuste_vida_residual' then
	                ('01/'||date_part('month'::text, afv.fecha_ult_dep_real + interval '1' month)::varchar||'/'||date_part('year'::text, afv.fecha_ult_dep_real + interval '1' month)::varchar)::date
				else
                    case
                        when afv.fecha_ult_dep_real >= afv.fecha_ini_dep and afv1.fecha_ult_dep is not null then ('01/'||date_part('month'::text, afv.fecha_ult_dep_real + interval '1' month)::varchar||'/'||date_part('year'::text, afv.fecha_ult_dep_real + interval '1' month)::varchar)::date
                    else
                        afv.fecha_ult_dep_real
                    end
                end as mes_dep,
                cla.depreciable,
                case when afv1.tipo_modificacion = 'ajuste_vida_residual' then
	                kaf.f_months_between(('01/'||date_part('month'::text, afv.fecha_ult_dep_real + interval '1' month)::varchar||'/'||date_part('year'::text, afv.fecha_ult_dep_real + interval '1' month)::varchar)::date, v_fecha_hasta)
                else
                    case
                        when afv.fecha_ult_dep_real >= afv.fecha_ini_dep and afv1.fecha_ult_dep is not null then kaf.f_months_between(('01/'||date_part('month'::text, afv.fecha_ult_dep_real + interval '1' month)::varchar||'/'||date_part('year'::text, afv.fecha_ult_dep_real + interval '1' month)::varchar)::date, v_fecha_hasta)
                    else
                        kaf.f_months_between(afv.fecha_ult_dep_real, v_fecha_hasta)
                    end
                end as meses_dep,

                mon.id_moneda_dep,
                mon.id_moneda,
                mon.id_moneda_act,
                mon.actualizar,
                mon.contabilizar,

                afv.id_activo_fijo_valor,
                afv.fecha_ult_dep,
                afv.fecha_ini_dep,
                afv.depreciacion_acum,
                afv.depreciacion_per,
                afv.monto_vigente,
                afv.vida_util,
                afv.monto_rescate,
                afv.vida_util_real,
                afv.monto_vigente_real,
                afv.fecha_ult_dep_real,
                afv.depreciacion_acum_real,
                afv.depreciacion_per_real,
                afv.depreciacion_acum_ant_real,
                afv.monto_actualiz_real,
                cla.depreciable,
                afv.vida_util_orig,
                tipo_cambio_anterior,
                afv1.fecha_fin,
                afv1.id_activo_fijo_valor_original,
                af.codigo,
                afv1.codigo as codigo_afv

                ,afv1.vida_util_resid_corregido,
                afv1.vida_util_corregido,
                afv1.deprec_acum_ant,
                afv1.valor_residual,  -- monto vigente
                afv1.tipo_modificacion,
                afv1.control_ajuste_vida,
                afv1.fecha_ajuste,
                afv1.monto_vig_actu_mod
                from kaf.vactivo_fijo_valor_impuestos afv
                inner join kaf.tmoneda_dep mon
                on mon.id_moneda_dep = afv.id_moneda_dep
                inner join kaf.tactivo_fijo af
                on af.id_activo_fijo = afv.id_activo_fijo
                inner join kaf.tclasificacion cla
                on cla.id_clasificacion = af.id_clasificacion
                inner join kaf.tactivo_fijo_valores afv1
                on afv1.id_activo_fijo_valor = afv.id_activo_fijo_valor
                where afv.id_activo_fijo_valor = p_id_activo_fijo_valor
                and afv.fecha_ult_dep_real < v_fecha_hasta --solo que tenga depreciacion menor a la fecha indicada en el movimiento
                --and afv.estado = 'activo'
                /*and (case  when cla.depreciable = 'si' then
                        afv.vida_util_real > 0
                    else
                        0=0
                    end)*/
        loop

        --Bandera de control de depreciación
        v_sw_control_dep= true;

        v_mes_dep = v_rec.mes_dep;

        --Inicialización datos última depreciación
        if v_rec.depreciable = 'si' then
            v_ant_dep_acum          = v_rec.depreciacion_acum_real;
            v_ant_dep_per           = v_rec.depreciacion_per_real;
            v_ant_monto_vigente     = v_rec.monto_vigente_real;
            v_ant_vida_util         = v_rec.vida_util_real;
            v_ant_monto_actualiz    = v_rec.monto_actualiz_real;
        else
            v_ant_dep_acum          = 0;
            v_ant_dep_per           = 0;
            v_ant_monto_vigente     = v_rec.monto_vigente_real;
            v_ant_vida_util         = 0;
            v_ant_monto_actualiz    = v_rec.monto_actualiz_real;
        end if;

        --Si las gestion anterior y última son diferentes resetear la depreciación de la gestión
        v_gestion_previa = extract(year from v_rec.fecha_ult_dep_real::date);
        v_gestion_dep    = extract(year from v_mes_dep);

        --Si detectamos que cambio la gestion reseteamos la depreciacion acumulado del periodo (gestion..)
        if v_gestion_previa != v_gestion_dep then
            v_ant_dep_per = 0.00;
        end if;

        v_tipo_cambio_anterior = v_rec.tipo_cambio_anterior;

        --RCM 18-12-2017: Verifica si es un AFV replicado a partir de otro AFV
        if v_rec.id_activo_fijo_valor_original is not null and v_rec.fecha_ult_dep is null then

            select mdep.tipo_cambio_fin
            into v_tipo_cambio_anterior
            from kaf.tmovimiento_af_dep_impuestos mdep
            where mdep.id_activo_fijo_valor = v_rec.id_activo_fijo_valor_original
            and mdep.id_moneda_dep = v_rec.id_moneda_dep
            and mdep.fecha = (select max(fecha)
                            from kaf.tmovimiento_af_dep_impuestos
                            where id_activo_fijo_valor = v_rec.id_activo_fijo_valor_original
                            and id_moneda_dep = v_rec.id_moneda_dep);

--                  raise exception 'si entra %',v_tipo_cambio_anterior;
        end if;

        --Bucle de la cantidad de meses a depreciar
        for i in 1..v_rec.meses_dep loop

        	--Verifica que la fecha fin del afv sea menor o igual a la fecha en que se está depreciando
            if v_rec.fecha_fin is not null then
                if date_trunc('month',v_mes_dep::date) >= date_trunc('month',v_rec.fecha_fin::date) then
                    exit;
                end if;
            end if;

        	--RCM:  Verificación periodo cerrado
            select po_id_periodo_subsistema into v_id_periodo_subsistema
            from param.f_get_periodo_gestion(v_mes_dep,v_id_subsistema);
            v_res = param.f_verifica_periodo_subsistema_abierto(v_id_periodo_subsistema, false);
            if v_res != 'exito' then
              --return v_res;
              --exit;
              raise exception 'No puede depreciarse el activo % en el periodo %. %',v_rec.codigo_afv, v_mes_dep,v_res;
            end if;
            --FIN RCM



            if v_rec.actualizar = 'si'  then
                --Obtener tipo de cambio del inicio y fin de mes
                select
                o_tc_inicial, o_tc_final, o_tc_factor, o_fecha_ini, o_fecha_fin
                into v_rec_tc
                from kaf.f_get_tipo_cambio(v_rec.id_moneda_act, v_rec.id_moneda, v_tipo_cambio_anterior, v_mes_dep);
            else
                --Si no requiere actulizacion el factor es igual a 1
                select
                o_tc_inicial, o_tc_final, o_tc_factor, o_fecha_ini, o_fecha_fin
                into v_rec_tc
                from kaf.f_get_tipo_cambio(v_rec.id_moneda, v_rec.id_moneda, v_tipo_cambio_anterior,  v_mes_dep);
            end if;

            --SI es llamado para depreciar .....
            if v_rec.depreciable = 'si' then
                --Actualización de importes

                /*if i = 2 then
                    raise exception '%: monto actualiz: %, v_ant_monto_actualiz: %, factor: %',i,v_monto_actualiz,v_ant_monto_actualiz,v_rec_tc.o_tc_factor;
                end if;*/

                v_dep_acum_actualiz = v_ant_dep_acum * v_rec_tc.o_tc_factor;
                v_dep_per_actualiz  = v_ant_dep_per * v_rec_tc.o_tc_factor;
                --v_monto_actualiz    = v_ant_monto_vigente * v_rec_tc.o_tc_factor;
                v_monto_actualiz    = v_ant_monto_actualiz * v_rec_tc.o_tc_factor;

                -- ini 13/01/2020 breydi.vasquez modificacion para ajuste de vida util
                if ( v_rec.tipo_modificacion = 'ajuste_vida' and (date_trunc('month', v_mes_dep) = date_trunc('month',v_rec.fecha_ajuste + interval '1' month)) and v_rec.control_ajuste_vida >= v_mes_dep) then

                    v_ant_monto_vigente = v_rec.valor_residual;
					          v_dep_acum_actualiz = v_rec.deprec_acum_ant * v_rec_tc.o_tc_factor;
                    v_ant_vida_util  = v_rec.vida_util_resid_corregido;

                elsif ( v_rec.tipo_modificacion = 'ajuste_pas_act' and (date_trunc('month', v_mes_dep) = date_trunc('month',v_rec.fecha_ajuste + interval '1' month)) and v_rec.control_ajuste_vida >= v_mes_dep) then

                  v_ant_monto_vigente = v_rec.valor_residual;
                  v_dep_acum_actualiz = v_rec.deprec_acum_ant * v_rec_tc.o_tc_factor;
                  v_ant_vida_util  = v_rec.vida_util_resid_corregido;
                  if v_ant_vida_util > 0 then
                      v_monto_actualiz = v_rec.monto_vig_actu_mod * v_rec_tc.o_tc_factor;
                  else
                      v_ant_monto_actualiz = v_rec.monto_vig_actu_mod;
                  end if;

                end if;
                -- fin bvp

                --Cálculo nuevos valores por depreciación
                --RAC 03/03/2017
                --  agrega validacion de division por cero
                if v_ant_vida_util = 0 and v_rec.depreciable = 'si' then
                    --exit; --v_nuevo_dep_mes       = 0;
                else
                    v_nuevo_dep_mes = (v_ant_monto_vigente * v_rec_tc.o_tc_factor - v_rec.monto_rescate) /  v_ant_vida_util;
                end if;

                v_nuevo_dep_acum      = v_dep_acum_actualiz + v_nuevo_dep_mes;
                v_nuevo_dep_per       = v_dep_per_actualiz + v_nuevo_dep_mes;
                v_nuevo_monto_vigente = v_monto_actualiz - v_nuevo_dep_acum;
                v_nuevo_vida_util     = v_ant_vida_util - 1;

                --RCM 12/12/2017: que siga actualizando la dep. acum aunque tenga vida util cero
                if v_ant_vida_util = 0 and v_rec.depreciable = 'si' then
                    v_dep_per_actualiz  = 0;
                    --v_monto_actualiz    = v_ant_monto_vigente * v_rec_tc.o_tc_factor;
                    v_monto_actualiz    = v_ant_monto_actualiz * v_rec_tc.o_tc_factor;
                    v_nuevo_dep_mes = 0;

                    v_nuevo_dep_acum      = v_dep_acum_actualiz + v_nuevo_dep_mes;
                    v_nuevo_dep_per       = 0;
                    v_nuevo_monto_vigente = v_rec.monto_rescate;
                    v_nuevo_vida_util     = 0;
                end if;
                --FIN RCM

            else
                --Actualización de importes
                v_dep_acum_actualiz = v_ant_dep_acum * v_rec_tc.o_tc_factor;
                v_dep_per_actualiz  = v_ant_dep_per * v_rec_tc.o_tc_factor;
                v_monto_actualiz    = v_ant_monto_actualiz * v_rec_tc.o_tc_factor;

                v_nuevo_dep_acum      = 0;
                v_nuevo_dep_per       = 0;
                v_nuevo_monto_vigente = v_monto_actualiz;
                v_nuevo_vida_util     = v_ant_vida_util ;
            end if;

            if v_rec.tipo_modificacion = 'ajuste_vida_residual' then
	            v_nuevo_monto_vigente = - v_rec.valor_residual;
                v_nuevo_dep_acum      = v_rec.valor_residual;
                v_nuevo_vida_util     = 0;
            end if;

			--Verifica que no exista el reg. id_monea_dep, id_activo_fijo_valor, fecha
            if not exists(select 1 from kaf.tmovimiento_af_dep_impuestos
            				where id_activo_fijo_valor = v_rec.id_activo_fijo_valor
                            and id_moneda_dep = v_rec.id_moneda_dep
                            and fecha = v_mes_dep) then

                --Inserción en base de datos
                INSERT INTO kaf.tmovimiento_af_dep_impuestos (
                id_usuario_reg,
                id_usuario_mod,
                fecha_reg,
                fecha_mod,
                estado_reg,
                id_usuario_ai,
                usuario_ai,
                --id_movimiento_af,
                depreciacion_acum_ant, --10
                depreciacion_per_ant,
                monto_vigente_ant,
                vida_util_ant,
                depreciacion_acum_actualiz,
                depreciacion_per_actualiz,
                monto_actualiz,
                depreciacion,
                depreciacion_acum,
                depreciacion_per, --19
                monto_vigente,
                vida_util,
                tipo_cambio_ini,
                tipo_cambio_fin,
                factor,
                id_activo_fijo_valor, --26
                fecha,
                monto_actualiz_ant,
                id_moneda,
                id_moneda_dep
                ) VALUES (
                p_id_usuario,
                null,
                now(),
                null,
                'activo',
                null,
                null,
                --v_rec.id_movimiento_af,
                v_ant_dep_acum, --10  depreciacion_acum_ant
                v_ant_dep_per,   --depreciacion_per_ant
                v_ant_monto_vigente,  --monto_vigente_ant
                v_ant_vida_util,   --  vida_util_ant
                v_dep_acum_actualiz,  --  depreciacion_acum_actualiz
                v_dep_per_actualiz,  --  depreciacion_per_actualiz
                v_monto_actualiz,    --monto_actualiz
                v_nuevo_dep_mes,   -- depreciacion
                v_nuevo_dep_acum,  -- depreciacion_acum
                v_nuevo_dep_per,   -- depreciacion_per
                v_nuevo_monto_vigente, -- 20   monto_vigente
                v_nuevo_vida_util,  -- vida_util
                v_rec_tc.o_tc_inicial,
                v_rec_tc.o_tc_final,
                v_rec_tc.o_tc_factor,
                v_rec.id_activo_fijo_valor, --25
                v_mes_dep,
                v_ant_monto_actualiz,
                v_rec.id_moneda,
                v_rec.id_moneda_dep
                ) RETURNING id_movimiento_af_dep into v_id_movimiento_af_dep;

            else
            	raise exception 'El Activo Fijo % ya fue depreciado en  %',v_rec.codigo_afv,v_mes_dep;
            end if;

            v_gestion_previa =   extract(year from v_mes_dep::date);
            v_tipo_cambio_anterior = v_rec_tc.o_tc_final;

            --Incrementa en uno el mes
            v_mes_dep = v_mes_dep + interval '1' month;

            --ajusta las fechas
            v_gestion_aux = date_part('year'::text, v_mes_dep);
            v_mes_aux = date_part('month'::text, v_mes_dep);
            v_mes_dep = ('01/'||v_mes_aux::varchar||'/'||v_gestion_aux::varchar)::date;
            v_gestion_dep = extract(year from v_mes_dep::date);

            --si detectamos que cambio la gestion reseteamos la depreciacion acumulado del periodo (gestion..)
            if v_gestion_previa != v_gestion_dep then
                v_ant_dep_per = 0.00;
            else
                v_ant_dep_per = v_nuevo_dep_per;
            end if;

            --Reinicialización de valores depreciación para siguiente iteración
            v_ant_dep_acum = v_nuevo_dep_acum;
            v_ant_monto_vigente = v_nuevo_monto_vigente;
            v_ant_vida_util = v_nuevo_vida_util;
            v_ant_monto_actualiz = v_monto_actualiz;

        end loop;

        if v_rec.meses_dep = 0 then
            v_mensaje = 'Sin depreciar. No corresponde depreciar en este periodo';
        else
            v_mes_dep = v_mes_dep - interval '1' month;
            v_mensaje = 'Depreciado hasta '||v_mes_dep::varchar;
        end if;

        update kaf.tactivo_fijo_valores set
        fecha_ult_dep = p_fecha_hasta
        where id_activo_fijo_valor = p_id_activo_fijo_valor;


    end loop;




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
