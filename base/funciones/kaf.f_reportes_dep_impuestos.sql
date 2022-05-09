CREATE OR REPLACE FUNCTION kaf.f_reportes_dep_impuestos (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/***************************************************************************
 SISTEMA:        Activos Fijos
 FUNCION:        kaf.f_reportes_dep_impuestos
 DESCRIPCION:    Funcion que devuelve conjunto de datos para reportes de activos fijos
 AUTOR:         BVP
 FECHA:          30/03/2021
 COMENTARIOS:
***************************************************************************/

DECLARE

    v_nombre_funcion  varchar;
    v_consulta        varchar;
    v_parametros      record;
    v_respuesta       varchar;
    v_id_items        varchar[];
    v_where           varchar;
    v_fecha           date;
    v_ids_depto       varchar;

    v_lugar           varchar = '';
    v_filtro          varchar;
    v_record          record;
    v_desc_nombre     varchar;
    v_fecha_actu      date;
    v_campos          record;
    v_id_id_activo_fijo_valor text;
	v_filtro_internac	varchar='';
    v_condicion			varchar='';
    v_ufv_mes_repo		numeric;
    v_ufv_ant_anio		numeric;
    v_dep_ges_mes		integer;

BEGIN

    v_nombre_funcion='kaf.f_reportes_dep_impuestos';
    v_parametros=pxp.f_get_record(p_tabla);


    /*********************************
     #TRANSACCION:  'SKA_RDEPIMP_SEL'
     #DESCRIPCION:  Reporte del Detalle de depreciación impuestos
     #AUTOR:        bvp
     #FECHA:        30/03/2021
    ***********************************/

    if(p_transaccion='SKA_RDEPIMP_SEL') then

        begin

            --raise exception 'v_parametros.filtro: %', v_parametros.filtro;
            --Creacion de tabla temporal de los actios fijos a filtrar
            create temp table tt_af_filtro (
                id_activo_fijo integer
            ) on commit drop;

            if(pxp.f_existe_parametro(p_tabla, 'ubi_nac_inter'))then

                    v_filtro_internac = 'left join orga.toficina of on of.id_oficina = afij.id_oficina
                                        left join param.tlugar tlug on tlug.id_lugar = of.id_lugar
                                        left join orga.vfuncionario vf on vf.id_funcionario =afij.id_funcionario
                                        left join param.tcatalogo cat on cat.id_catalogo =afij.id_cat_estado_fun
                                        left join orga.tuo tu on tu.id_uo = afij.id_uo ';

            	if (v_parametros.ubi_nac_inter = 'internaci' )then
                    v_condicion = '((tlug.id_lugar_fk not in (1,2,61,63,65,66,67,68,70,256,282))) and ';
            	elsif (v_parametros.ubi_nac_inter = 'nacional' )then
                    v_condicion = '((tlug.id_lugar_fk in (1,2,61,63,65,66,67,68,70,256,282))) and ';
                else
                	v_condicion = '';
                end if;
            else
            		v_filtro_internac = '';
                    v_condicion = '';
			end if;

            v_consulta = 'insert into tt_af_filtro
                        select afij.id_activo_fijo
                        from kaf.tactivo_fijo afij
                        inner join kaf.tclasificacion cla
                        on cla.id_clasificacion = afij.id_clasificacion
                        ' ||v_filtro_internac|| '
                        where '||v_condicion||' '||v_parametros.filtro;

              --Grover activos del exterior
               /*afij.id_activo_fijo in (

            Select af.id_activo_fijo
            --,af.codigo, af.denominacion, af.descripcion,
            --of.nombre, param.f_get_id_lugar_pais(of.id_lugar)
            from kaf.tactivo_fijo af
            inner join orga.tfuncionario fun on fun.id_funcionario=af.id_funcionario
            inner join orga.tuo_funcionario uf on uf.id_funcionario=fun.id_funcionario
            inner join orga.tcargo car on car.id_cargo=uf.id_cargo
            inner join orga.toficina of on of.id_oficina=car.id_oficina
            where param.f_get_id_lugar_pais(of.id_lugar)<>1)

            and */


            execute(v_consulta);

            select tica.oficial
              into v_ufv_mes_repo
            from param.ttipo_cambio tica
            where tica.fecha = v_parametros.fecha_hasta
            and tica.id_moneda = 3 ;

            select tica.oficial
            	into v_ufv_ant_anio
            from param.ttipo_cambio tica
            where tica.fecha = ('31/12/'||(date_part('YEAR',v_parametros.fecha_hasta)-1)::text)::date
            and tica.id_moneda = 3;

			v_dep_ges_mes = (date_part('MONTH',v_parametros.fecha_hasta))::integer;

            --Creación de la tabla con los datos de la depreciación
            create temp table tt_detalle_depreciacion (
                id_activo_fijo_valor integer,
                codigo varchar(50),
                denominacion varchar,
                fecha_ini_dep date,
                monto_vigente_orig_100 numeric(18,2),
                monto_vigente_orig numeric(18,2),
                inc_actualiz numeric(18,2),
                monto_actualiz numeric(18,2),
                vida_util_orig integer,
                vida_util integer,
                depreciacion_acum_gest_ant numeric(18,2),
                depreciacion_acum_actualiz_gest_ant numeric(18,2),
                depreciacion_per numeric(18,2),
                depreciacion_acum numeric(18,2),
                monto_vigente numeric(18,2),
                codigo_padre varchar(15),
                denominacion_padre varchar(100),
                tipo varchar(50),
                tipo_cambio_fin numeric,
                id_moneda_act integer,
                id_activo_fijo_valor_original integer,
                inc_ac_acum			numeric(18,2),
                val_acu_perido		numeric(18,2)
            ) on commit drop;

            --Carga los datos en la tabla temporal
            insert into tt_detalle_depreciacion(
            id_activo_fijo_valor,codigo, denominacion ,fecha_ini_dep,monto_vigente_orig_100,monto_vigente_orig,inc_actualiz,
            monto_actualiz,vida_util_orig,vida_util,
            depreciacion_per,depreciacion_acum,monto_vigente,codigo_padre,denominacion_padre,tipo,tipo_cambio_fin,id_moneda_act,
            id_activo_fijo_valor_original, inc_ac_acum, val_acu_perido
            )
            select
            afv.id_activo_fijo_valor,
           case when (v_parametros.total_consol='deta' or v_parametros.total_consol='') then
            afv.codigo
            else
            kaf.f_tam_codigo(afv.codigo)
            end as codigo,
             case when v_parametros.desc_nombre='desc' then
                upper(af.descripcion)
                when v_parametros.desc_nombre='nombre' or v_parametros.desc_nombre='' then
                upper(af.denominacion)
                else
                upper(af.denominacion||' / '||chr(10)||af.descripcion)
            end,
            --af.descripcion,
            --afv.fecha_ini_dep,
            case when afv.tipo_modificacion = 'ajuste_pas_act' and v_parametros.fecha_hasta > afv.fecha_ajuste then
              afv.fecha_ini_dep
            else
              case coalesce(afv.id_activo_fijo_valor_original,0)
                  when 0 then afv.fecha_ini_dep
                  else (select fecha_ini_dep from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
              end
            end as fecha_ini_dep,
            --coalesce(afv.monto_vigente_orig_100,afv.monto_vigente_orig),
            case when afv.tipo_modificacion = 'ajuste_pas_act' and v_parametros.fecha_hasta > afv.fecha_ajuste then
                    afv.monto_vigente_orig_100
            else
              case coalesce(afv.id_activo_fijo_valor_original,0)
                  when 0 then afv.monto_vigente_orig_100
                  else (select monto_vigente_orig_100 from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
              end
            end as monto_vigente_orig_100,

--            afv.monto_vigente_orig,
          case when afv.tipo_modificacion = 'ajuste_pas_act' and v_parametros.fecha_hasta > afv.fecha_ajuste then
                  afv.monto_vigente_orig
          else
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end
          end as monto_vigente_orig,
            --(coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0)) as inc_actualiz,
            case
                when (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0)) < 0 then 0
                else (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0))
            end as inc_actualiz,
            case when substr(afv.codigo,1,13) ='05.03.01.0001' then
--------------------------------------------------
            (v_ufv_mes_repo/(select tica.oficial
            from param.ttipo_cambio tica
            where tica.fecha = (
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.fecha_ini_dep
                else (select fecha_ini_dep from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end)
            and tica.id_moneda = 3) * (case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end))
            else
            mdep.monto_actualiz
            end as monto_actualiz,
--------------------------------------------------
            /*case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig * mdep.factor
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original) * mdep.factor
            end as monto_actualiz,*/
            case when afv.tipo_modificacion = 'ajuste_vida'  and v_parametros.fecha_hasta > afv.fecha_ajuste then
            afv.vida_util_corregido  else
            afv.vida_util_orig end as vida_util_orig,

            mdep.vida_util,
--------------------------------------------------
            case when substr(afv.codigo,1,13) ='05.03.01.0001' then
            ((v_ufv_mes_repo/(select tica.oficial
            from param.ttipo_cambio tica
            where tica.fecha = (
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.fecha_ini_dep
                else (select fecha_ini_dep from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end)
            and tica.id_moneda = 3) * (case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end))/case when afv.vida_util_orig = 0 then 1 else afv.vida_util_orig end  * ( case when mdep.vida_util = 0 then
							            mdep.vida_util
                                        else
            							v_dep_ges_mes
                                        end)

            )
            else
            mdep.depreciacion_per
            end  as depreciacion_per,
--------------------------------------------------
            mdep.depreciacion_acum,
            case when mdep.monto_vigente <=1 then
                case when substr(af.codigo,1,2)='11' then
                    0.00
                else
                case when afv.tipo in ('ajuste','reval') then
                	mdep.monto_vigente
                    else
                    1.00
                    end
                end
            else
            mdep.monto_vigente
            end,
            --mdep.monto_vigente,
            substr(afv.codigo,1, position('.' in afv.codigo)-1) as codigo_padre,
            (select nombre from kaf.tclasificacion where codigo_completo_tmp = substr(afv.codigo,1, position('.' in afv.codigo)-1)) as denominacion_padre,
            afv.tipo,
            mdep.tipo_cambio_fin,
            mon.id_moneda_act,
            afv.id_activo_fijo_valor_original,
            kaf.f_calc_inc_act_ges_ant(v_parametros.fecha_hasta::date, afv.fecha_ini_dep, afv.id_activo_fijo_valor_original, afv.tipo_modificacion, afv.monto_vigente_orig, true,0),
        	  kaf.f_calc_inc_act_ges_ant(v_parametros.fecha_hasta::date, afv.fecha_ini_dep,afv.id_activo_fijo_valor_original, afv.tipo_modificacion, afv.monto_vigente_orig,  false, mdep.monto_actualiz)

            from kaf.tmovimiento_af_dep_impuestos mdep
            inner join kaf.tactivo_fijo_valores afv
            on afv.id_activo_fijo_valor = mdep.id_activo_fijo_valor
            inner join kaf.tactivo_fijo af
            on af.id_activo_fijo = afv.id_activo_fijo
            inner join kaf.tmoneda_dep mon
            on mon.id_moneda =  afv.id_moneda_dep
            where date_trunc('month',mdep.fecha) = date_trunc('month',v_parametros.fecha_hasta::date)
            and mdep.id_moneda_dep = coalesce(v_parametros.id_moneda,1)
            and af.id_activo_fijo in (select id_activo_fijo from tt_af_filtro)
                                                            --and afv.codigo not like '%-G%'
            and af.estado <> 'eliminado' and
            case when v_parametros.baja_retiro = 'alta' then
            (afv.fecha_fin is null or af.estado = 'alta')
            when v_parametros.baja_retiro = 'baj_ret' then
            afv.fecha_fin = '31/01/2019'::date
            else
            0 = 0
            end;
            -- fin 1

            insert into tt_detalle_depreciacion(
            id_activo_fijo_valor,codigo, denominacion ,fecha_ini_dep,monto_vigente_orig_100,monto_vigente_orig,inc_actualiz,
            monto_actualiz,vida_util_orig,vida_util,
            depreciacion_per,depreciacion_acum,monto_vigente,codigo_padre,denominacion_padre,tipo,tipo_cambio_fin,id_moneda_act,
            id_activo_fijo_valor_original, inc_ac_acum, val_acu_perido
            )
            select
            afv.id_activo_fijo_valor,
           case when (v_parametros.total_consol='deta' or v_parametros.total_consol='') then
            afv.codigo
            else
            kaf.f_tam_codigo(afv.codigo)
            end as codigo,
             case when v_parametros.desc_nombre='desc' then
                upper(af.descripcion)
                when v_parametros.desc_nombre='nombre' or v_parametros.desc_nombre='' then
                upper(af.denominacion)
                else
                upper(af.denominacion||' / '||chr(10)||af.descripcion)
            end,
            afv.fecha_ini_dep,
            afv.monto_vigente_orig_100,
            afv.monto_vigente_orig,
            --(coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0)) as inc_actualiz,
            case
                  when (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0)) < 0 then 0
                  else (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0))
            end as inc_actualiz,

            case when substr(afv.codigo,1,13) ='05.03.01.0001' then
--------------------------------------------------
            (v_ufv_mes_repo/(select tica.oficial
            from param.ttipo_cambio tica
            where tica.fecha = (
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.fecha_ini_dep
                else (select fecha_ini_dep from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end)
            and tica.id_moneda = 3) * (case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end))
            else
            mdep.monto_actualiz
            end as monto_actualiz,
--------------------------------------------------
            /*case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig * mdep.factor
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original) * mdep.factor
            end as monto_actualiz,*/
            case when afv.tipo_modificacion = 'ajuste_vida'  and v_parametros.fecha_hasta > afv.fecha_ajuste then
            afv.vida_util_corregido  else
            afv.vida_util_orig end as vida_util_orig,

            mdep.vida_util,
--------------------------------------------------
            case when substr(afv.codigo,1,13) ='05.03.01.0001' then
            ((v_ufv_mes_repo/(select tica.oficial
            from param.ttipo_cambio tica
            where tica.fecha = (
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.fecha_ini_dep
                else (select fecha_ini_dep from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end)
            and tica.id_moneda = 3) * (case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end))/case when afv.vida_util_orig = 0 then 1 else afv.vida_util_orig end  * ( case when mdep.vida_util = 0 then
							            mdep.vida_util
                                        else
            							v_dep_ges_mes
                                        end)

            )
            else
            mdep.depreciacion_per
            end  as depreciacion_per,
--------------------------------------------------
            mdep.depreciacion_acum,
            case when mdep.monto_vigente <=1 then
                case when substr(af.codigo,1,2)='11' then
                    0.00
                else
                case when afv.tipo in ('ajuste','reval') then
                	mdep.monto_vigente
                    else
                    1.00
                    end
                end
            else
            mdep.monto_vigente
            end,
            --mdep.monto_vigente,
            substr(afv.codigo,1, position('.' in afv.codigo)-1) as codigo_padre,
            (select nombre from kaf.tclasificacion where codigo_completo_tmp = substr(afv.codigo,1, position('.' in afv.codigo)-1)) as denominacion_padre,
            afv.tipo,
            mdep.tipo_cambio_fin,
            mon.id_moneda_act,
            afv.id_activo_fijo_valor_original,
            kaf.f_calc_inc_act_ges_ant(v_parametros.fecha_hasta::date, afv.fecha_ini_dep, afv.id_activo_fijo_valor_original, afv.tipo_modificacion, afv.monto_vigente_orig, true,0),
        	  kaf.f_calc_inc_act_ges_ant(v_parametros.fecha_hasta::date, afv.fecha_ini_dep,afv.id_activo_fijo_valor_original, afv.tipo_modificacion, afv.monto_vigente_orig,  false, mdep.monto_actualiz)

            from kaf.tmovimiento_af_dep_impuestos mdep
            inner join kaf.tactivo_fijo_valores afv
            on afv.id_activo_fijo_valor = mdep.id_activo_fijo_valor
            inner join kaf.tactivo_fijo af
            on af.id_activo_fijo = afv.id_activo_fijo
            inner join kaf.tmoneda_dep mon
            on mon.id_moneda =  afv.id_moneda_dep
            where afv.fecha_fin is not null
            and  not exists (select from kaf.tactivo_fijo_valores where id_activo_fijo_valor_original = afv.id_activo_fijo_valor and tipo<>'alta')
            and afv.codigo not in (select codigo
                                                from tt_detalle_depreciacion)
            --and afv.id_activo_fijo_valor not in (select id_activo_fijo_valor from kaf.tactivo_fijo_valores where id_activo_fijo_valor_original = afv.id_activo_fijo_valor /*and tipo = 'alta'*/ )
            --inicio cambio 06/11/2019 por activos de baja recuperados de un mes anterior y no asi a  la fecha (bvp)
            --and date_trunc('month',mdep.fecha) <> date_trunc('month',v_parametros.fecha_hasta::date)
            --and date_trunc('month',mdep.fecha) < date_trunc('month',v_parametros.fecha_hasta::date) --between date_trunc('month',('01-01-'||extract(year from v_parametros.fecha_hasta::date)::varchar)::date) and date_trunc('month',v_parametros.fecha_hasta::date)
            --fin cambios 06/11/2019
            and date_trunc('month',mdep.fecha) = (select max(fecha)
                                                    from kaf.tmovimiento_af_dep_impuestos
                                                    where id_activo_fijo_valor = afv.id_activo_fijo_valor
                                                    and id_moneda_dep = mdep.id_moneda_dep
                                                    --and date_trunc('month',fecha) <> date_trunc('month',v_parametros.fecha_hasta::date)
                                                    and date_trunc('month',fecha) <= date_trunc('month',v_parametros.fecha_hasta::date) --between date_trunc('month',('01-01-'||extract(year from v_parametros.fecha_hasta)::varchar)::date) and date_trunc('month',v_parametros.fecha_hasta)
                                                )
            and mdep.id_moneda_dep = coalesce(v_parametros.id_moneda,1)
            and af.id_activo_fijo in (select id_activo_fijo from tt_af_filtro)
            and afv.id_activo_fijo_valor not in (select id_activo_fijo_valor
                                                from tt_detalle_depreciacion)
            and af.estado not in ('eliminado', 'baja')
            and af.fecha_baja > v_parametros.fecha_hasta::date;
            ---- 2---
            --------------------------------
            ----------------ini----------------
            /** nueva consulta para seleccionar las bajas que tengan un solo mes de depreciacion

            **/
            insert into tt_detalle_depreciacion(
            id_activo_fijo_valor,codigo, denominacion ,fecha_ini_dep,monto_vigente_orig_100,monto_vigente_orig,inc_actualiz,
            monto_actualiz,vida_util_orig,vida_util,
            depreciacion_per,depreciacion_acum,monto_vigente,codigo_padre,denominacion_padre,tipo,tipo_cambio_fin,id_moneda_act,
            id_activo_fijo_valor_original, inc_ac_acum, val_acu_perido
            )
            select
            afv.id_activo_fijo_valor,
           case when (v_parametros.total_consol='deta' or v_parametros.total_consol='') then
            afv.codigo
            else
            kaf.f_tam_codigo(afv.codigo)
            end as codigo,
             case when v_parametros.desc_nombre='desc' then
                upper(af.descripcion)
                when v_parametros.desc_nombre='nombre' or v_parametros.desc_nombre='' then
                upper(af.denominacion)
                else
                upper(af.denominacion||' / '||chr(10)||af.descripcion)
            end,
            afv.fecha_ini_dep,
            afv.monto_vigente_orig_100,
            afv.monto_vigente_orig,
            --(coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0)) as inc_actualiz,
            case
                  when (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0)) < 0 then 0
                  else (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0))
            end as inc_actualiz,

            case when substr(afv.codigo,1,13) ='05.03.01.0001' then
--------------------------------------------------
            (v_ufv_mes_repo/(select tica.oficial
            from param.ttipo_cambio tica
            where tica.fecha = (
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.fecha_ini_dep
                else (select fecha_ini_dep from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end)
            and tica.id_moneda = 3) * (case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end))
            else
            mdep.monto_actualiz
            end as monto_actualiz,
--------------------------------------------------
            /*case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig * mdep.factor
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original) * mdep.factor
            end as monto_actualiz,*/
            case when afv.tipo_modificacion = 'ajuste_vida' and v_parametros.fecha_hasta > afv.fecha_ajuste then
            afv.vida_util_corregido  else
            afv.vida_util_orig end as vida_util_orig,

            mdep.vida_util,
--------------------------------------------------
            case when substr(afv.codigo,1,13) ='05.03.01.0001' then
            ((v_ufv_mes_repo/(select tica.oficial
            from param.ttipo_cambio tica
            where tica.fecha = (
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.fecha_ini_dep
                else (select fecha_ini_dep from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end)
            and tica.id_moneda = 3) * (case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end))/case when afv.vida_util_orig = 0 then 1 else afv.vida_util_orig end  * ( case when mdep.vida_util = 0 then
							            mdep.vida_util
                                        else
            							v_dep_ges_mes
                                        end)

            )
            else
            mdep.depreciacion_per
            end  as depreciacion_per,
--------------------------------------------------
            mdep.depreciacion_acum,
            case when mdep.monto_vigente <=1 then
                case when substr(af.codigo,1,2)='11' then
                    0.00
                else
                case when afv.tipo in ('ajuste','reval') then
                	mdep.monto_vigente
                    else
                    1.00
                    end
                end
            else
            mdep.monto_vigente
            end,
            --mdep.monto_vigente,
            substr(afv.codigo,1, position('.' in afv.codigo)-1) as codigo_padre,
            (select nombre from kaf.tclasificacion where codigo_completo_tmp = substr(afv.codigo,1, position('.' in afv.codigo)-1)) as denominacion_padre,
            afv.tipo,
            mdep.tipo_cambio_fin,
            mon.id_moneda_act,
            afv.id_activo_fijo_valor_original,
            kaf.f_calc_inc_act_ges_ant(v_parametros.fecha_hasta::date, afv.fecha_ini_dep, afv.id_activo_fijo_valor_original, afv.tipo_modificacion, afv.monto_vigente_orig, true,0),
        	  kaf.f_calc_inc_act_ges_ant(v_parametros.fecha_hasta::date, afv.fecha_ini_dep,afv.id_activo_fijo_valor_original, afv.tipo_modificacion, afv.monto_vigente_orig,  false, mdep.monto_actualiz)

            from kaf.tmovimiento_af_dep_impuestos mdep
            inner join kaf.tactivo_fijo_valores afv
            on afv.id_activo_fijo_valor = mdep.id_activo_fijo_valor
            inner join kaf.tactivo_fijo af
            on af.id_activo_fijo = afv.id_activo_fijo
            inner join kaf.tmoneda_dep mon
            on mon.id_moneda =  afv.id_moneda_dep
            where afv.fecha_fin is not null
            and  not exists (select from kaf.tactivo_fijo_valores where id_activo_fijo_valor_original = afv.id_activo_fijo_valor and tipo<>'alta')
            and afv.codigo not in (select codigo
                                                from tt_detalle_depreciacion)
			and (select count(id_activo_fijo_valor) from kaf.tmovimiento_af_dep_impuestos where id_activo_fijo_valor = afv.id_activo_fijo_valor) = 1
            and mdep.id_moneda_dep = coalesce(v_parametros.id_moneda,1)
            and af.id_activo_fijo in (select id_activo_fijo from tt_af_filtro)
            and afv.id_activo_fijo_valor not in (select id_activo_fijo_valor
                                                from tt_detalle_depreciacion)
            and af.estado <> 'eliminado'
            and af.fecha_baja > v_parametros.fecha_hasta::date;
            -----------------fin---------------
            --- 3---

            --------------------------------
            insert into tt_detalle_depreciacion(
            id_activo_fijo_valor,codigo, denominacion ,fecha_ini_dep,monto_vigente_orig_100,monto_vigente_orig,inc_actualiz,
            monto_actualiz,vida_util_orig,vida_util,
            depreciacion_per,depreciacion_acum,monto_vigente,codigo_padre,denominacion_padre,tipo,tipo_cambio_fin,id_moneda_act,
            id_activo_fijo_valor_original, inc_ac_acum, val_acu_perido
            )
            select
            afv.id_activo_fijo_valor,
           case when (v_parametros.total_consol='deta' or v_parametros.total_consol='') then
            afv.codigo
            else
            kaf.f_tam_codigo(afv.codigo)
            end as codigo,
             case when v_parametros.desc_nombre='desc' then
                upper(af.descripcion)
                when v_parametros.desc_nombre='nombre' or v_parametros.desc_nombre='' then
                upper(af.denominacion)
                else
                upper(af.denominacion||' / '||chr(10)||af.descripcion)
            end,
            --afv.fecha_ini_dep,
            case when afv.tipo_modificacion = 'ajuste_pas_act' and v_parametros.fecha_hasta > afv.fecha_ajuste then
      	            afv.fecha_ini_dep
            else
              case coalesce(afv.id_activo_fijo_valor_original,0)
                  when 0 then afv.fecha_ini_dep
                  else (select fecha_ini_dep from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
              end
            end as fecha_ini_dep,
            --coalesce(afv.monto_vigente_orig_100,afv.monto_vigente_orig),
            case when afv.tipo_modificacion = 'ajuste_pas_act' and v_parametros.fecha_hasta > afv.fecha_ajuste then
            	afv.monto_vigente_orig_100
            else
              case coalesce(afv.id_activo_fijo_valor_original,0)
                  when 0 then afv.monto_vigente_orig_100
                  else (select monto_vigente_orig_100 from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
              end
            end as monto_vigente_orig_100,
--            afv.monto_vigente_orig,
            case when afv.tipo_modificacion = 'ajuste_pas_act' and v_parametros.fecha_hasta > afv.fecha_ajuste then
                    afv.monto_vigente_orig
            else
              case coalesce(afv.id_activo_fijo_valor_original,0)
                  when 0 then afv.monto_vigente_orig
                  else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
              end
            end as monto_vigente_orig,
            --(coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0)) as inc_actualiz,
            case
                when (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0)) < 0 then 0
                else (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0))
            end as inc_actualiz,
--------------------------------------------------
            case when substr(afv.codigo,1,13) ='05.03.01.0001' then
            (v_ufv_mes_repo/(select tica.oficial
            from param.ttipo_cambio tica
            where tica.fecha = (
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.fecha_ini_dep
                else (select fecha_ini_dep from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end)
            and tica.id_moneda = 3) * (case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end))
            else
            mdep.monto_actualiz
            end as monto_actualiz,
--------------------------------------------------
            /*case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig * mdep.factor
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original) * mdep.factor
            end as monto_actualiz,*/
            case when afv.tipo_modificacion = 'ajuste_vida' and v_parametros.fecha_hasta > afv.fecha_ajuste then
            afv.vida_util_corregido  else
            afv.vida_util_orig end as vida_util_orig,
            mdep.vida_util,
--------------------------------------------------
            case when substr(afv.codigo,1,13) ='05.03.01.0001' then
            ((v_ufv_mes_repo/(select tica.oficial
            from param.ttipo_cambio tica
            where tica.fecha = (
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.fecha_ini_dep
                else (select fecha_ini_dep from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end)
            and tica.id_moneda = 3) * (case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end))/case when afv.vida_util_orig = 0 then 1 else afv.vida_util_orig end  * ( case when mdep.vida_util = 0 then
							            mdep.vida_util
                                        else
            							v_dep_ges_mes
                                        end)

            )
            else
            mdep.depreciacion_per
            end  as depreciacion_per,
--------------------------------------------------
            mdep.depreciacion_acum,
            case when mdep.monto_vigente <=1 then
                case when substr(af.codigo,1,2)='11' then
                    0.00
                else
                case when afv.tipo in ('ajuste','reval') then
                	mdep.monto_vigente
                    else
                    1.00
                    end
                end
            else
            mdep.monto_vigente
            end,
            --mdep.monto_vigente,
            substr(afv.codigo,1, position('.' in afv.codigo)-1) as codigo_padre,
            (select nombre from kaf.tclasificacion where codigo_completo_tmp = substr(afv.codigo,1, position('.' in afv.codigo)-1)) as denominacion_padre,
            afv.tipo,
            mdep.tipo_cambio_fin,
            mon.id_moneda_act,
            afv.id_activo_fijo_valor_original,
            kaf.f_calc_inc_act_ges_ant(v_parametros.fecha_hasta::date, afv.fecha_ini_dep, afv.id_activo_fijo_valor_original, afv.tipo_modificacion, afv.monto_vigente_orig, true,0),
        	  kaf.f_calc_inc_act_ges_ant(v_parametros.fecha_hasta::date, afv.fecha_ini_dep,afv.id_activo_fijo_valor_original, afv.tipo_modificacion, afv.monto_vigente_orig,  false, mdep.monto_actualiz)

            from kaf.tmovimiento_af_dep_impuestos mdep
            inner join kaf.tactivo_fijo_valores afv
            on afv.id_activo_fijo_valor = mdep.id_activo_fijo_valor
            inner join kaf.tactivo_fijo af
            on af.id_activo_fijo = afv.id_activo_fijo
            inner join kaf.tmoneda_dep mon
            on mon.id_moneda =  afv.id_moneda_dep
            where af.estado in ('baja','retiro')
            and mdep.fecha >= '01-01-2021'
            and mdep.fecha = (select max(fecha) from kaf.tmovimiento_af_dep_impuestos mdep1
                                where mdep1.id_activo_fijo_valor = afv.id_activo_fijo_valor
                                and fecha between ('01-01-'||extract(year from mdep.fecha))::date and v_parametros.fecha_hasta::date)

            and mdep.id_moneda_dep = coalesce(v_parametros.id_moneda,1)
            and af.id_activo_fijo in (select id_activo_fijo from tt_af_filtro)
                                                            --and afv.codigo not like '%-G%'
            and afv.id_activo_fijo_valor not in (select id_activo_fijo_valor
                                                from tt_detalle_depreciacion);
            --------------------------------
            --------------------------------


            --Obtiene los datos de gestion anterior
            update tt_detalle_depreciacion set
            depreciacion_acum_gest_ant = coalesce((
                select
                  case when tipo_modificacion in ('ajuste_vida', 'ajuste_pas_act') and v_parametros.fecha_hasta > fecha_ajuste_vida then
    	              depreciacion_acum_corregido
                  else
	                  depreciacion_acum end
                from kaf.tmovimiento_af_dep_impuestos
                where id_activo_fijo_valor = tt_detalle_depreciacion.id_activo_fijo_valor
                and id_moneda_dep = coalesce(v_parametros.id_moneda,1)
                and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_parametros.fecha_hasta::date)::integer -1 )::date)
            ),0),
            depreciacion_acum_actualiz_gest_ant = (((case when substr(tt_detalle_depreciacion.codigo,1,13) ='05.03.01.0001' then
             v_ufv_mes_repo else tt_detalle_depreciacion.tipo_cambio_fin end/(param.f_get_tipo_cambio_v2(tt_detalle_depreciacion.id_moneda_act, coalesce(v_parametros.id_moneda,1), ('31/12/'||extract(year from v_parametros.fecha_hasta::date)::integer -1)::date, 'O'))))-1)*(coalesce((
                            select
                              case when tipo_modificacion in ('ajuste_vida', 'ajuste_pas_act') and v_parametros.fecha_hasta > fecha_ajuste_vida then
                                  depreciacion_acum_corregido
                              else
                                  depreciacion_acum end
                            from kaf.tmovimiento_af_dep_impuestos
                            where id_activo_fijo_valor = tt_detalle_depreciacion.id_activo_fijo_valor
                            and id_moneda_dep = coalesce(v_parametros.id_moneda,1)
                            and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_parametros.fecha_hasta)::integer -1 )::date)
                        ),0));

            --Si la depreciación anterior es cero, busca la depreciación de su activo fijo valor original si es que tuviese
            update tt_detalle_depreciacion set
            depreciacion_acum_gest_ant = coalesce((
                select depreciacion_acum
                from kaf.tmovimiento_af_dep_impuestos
                where id_activo_fijo_valor = tt_detalle_depreciacion.id_activo_fijo_valor_original
                and tipo = tt_detalle_depreciacion.tipo
                and id_moneda_dep = coalesce(v_parametros.id_moneda,1)
                and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_parametros.fecha_hasta::date)::integer -1 )::date)
            ),0),
            depreciacion_acum_actualiz_gest_ant = (((case when substr(tt_detalle_depreciacion.codigo,1,13) ='05.03.01.0001' then
             v_ufv_mes_repo else tt_detalle_depreciacion.tipo_cambio_fin end/(param.f_get_tipo_cambio_v2(tt_detalle_depreciacion.id_moneda_act, coalesce(v_parametros.id_moneda,1), ('31/12/'||extract(year from v_parametros.fecha_hasta::date)::integer -1)::date, 'O'))))-1)*(coalesce((
                            select depreciacion_acum
                            from kaf.tmovimiento_af_dep_impuestos
                            where id_activo_fijo_valor = tt_detalle_depreciacion.id_activo_fijo_valor_original
                            and tipo = tt_detalle_depreciacion.tipo
                            and id_moneda_dep = coalesce(v_parametros.id_moneda,1)
                            and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_parametros.fecha_hasta::date)::integer -1 )::date)
                        ),0))
            where coalesce(depreciacion_acum_gest_ant,0) = 0
            and id_activo_fijo_valor_original is not null;


            --Verifica si hay reg con tipo = ajuste_restar, y le cambia el signo
            update tt_detalle_depreciacion set
            monto_vigente_orig_100 = -1 * monto_vigente_orig_100,
            monto_vigente_orig = -1 * monto_vigente_orig,
            inc_actualiz = -1 * inc_actualiz,
            monto_actualiz = -1 * monto_actualiz,
            depreciacion_acum_gest_ant = -1 * depreciacion_acum_gest_ant,
            depreciacion_acum_actualiz_gest_ant = -1 * depreciacion_acum_actualiz_gest_ant,
            depreciacion_per = -1 * depreciacion_per,
            depreciacion_acum = -1 * depreciacion_acum,
            monto_vigente = -1 * monto_vigente
            where tipo = 'ajuste_restar';


            --Creación de la tabla con la agrupación y totales
            create temp table tt_detalle_depreciacion_totales (
                codigo varchar(50),
                denominacion varchar,
                fecha_ini_dep date,
                monto_vigente_orig_100 numeric(24,2),
                monto_vigente_orig numeric(24,2),
                inc_actualiz numeric(24,2),
                monto_actualiz numeric(24,2),
                vida_util_orig integer,
                vida_util integer,
                depreciacion_acum_gest_ant numeric(24,2),
                depreciacion_acum_actualiz_gest_ant numeric(24,2),
                depreciacion_per numeric(24,2),
                depreciacion_acum numeric(24,2),
                monto_vigente numeric(24,2),
                nivel integer,
                orden bigint,
                tipo varchar(10),
                inc_ac_acum	numeric(24,2),
          			val_acu_perido	numeric(24,2),
                reval numeric(24,2),
                ajust numeric(24,2),
                baja  numeric(24,2),
                transito numeric(24,2),
                leasing numeric(24,2)
            ) on commit drop;

            select pxp.list(distinct(de.id_activo_fijo_valor)::text)
            into v_id_id_activo_fijo_valor
            from tt_detalle_depreciacion de;

            --Inserta los totales por clasificacióm
---------------------------------------DEPRECIACION---------------------------------------------

    if v_parametros.tipo_repo='gepa' then
    --raise exception 'primera';
                if v_parametros.total_consol='deta' then------------detallado
        --            raise exception 'detallado';
                      if(v_parametros.estado_depre='') then
          --                raise exception 'sin filtro';
                            --sin modificaciones
                            --Inserta los totales por clasificacióm
                            insert into tt_detalle_depreciacion_totales
                            select
                            codigo_padre,
                            denominacion_padre,
                            null,
                            sum(monto_vigente_orig_100),
                            sum(monto_vigente_orig),
                            sum(inc_actualiz),
                            sum(monto_actualiz),
                            null,
                            null,
                            sum(depreciacion_acum_gest_ant),
                            sum(depreciacion_acum_actualiz_gest_ant),
                            sum(depreciacion_per),
                            sum(depreciacion_acum),
                            sum(monto_vigente),
                            replace(codigo_padre,'RE','')::integer,
                            0,
                            'clasif'
                            from tt_detalle_depreciacion
                            group by codigo_padre, denominacion_padre;

                            --Inserta el detalle
                            insert into tt_detalle_depreciacion_totales
                            select
                            codigo,
                            denominacion,
                            fecha_ini_dep,
                            monto_vigente_orig_100,
                            monto_vigente_orig,
                            inc_actualiz,
                            monto_actualiz,
                            vida_util_orig,
                            vida_util,
                            depreciacion_acum_gest_ant,
                            depreciacion_acum_actualiz_gest_ant,
                            depreciacion_per,
                            depreciacion_acum,
                            monto_vigente,
                            codigo_padre::integer,
                            replace(replace(replace(replace(replace(replace(replace(codigo,'A0',''),'AJ',''),'G',''),'RE',''),'ME',''),'.',''),'-','')::bigint,
                            'detalle'
                            from tt_detalle_depreciacion;

                            --Inserta los totales finales
                            insert into tt_detalle_depreciacion_totales
                            select
                            'TOTAL FINAL',
                            null,
                            null,
                            sum(monto_vigente_orig_100),
                            sum(monto_vigente_orig),
                            sum(inc_actualiz),
                            sum(monto_actualiz),
                            null,
                            null,
                            sum(depreciacion_acum_gest_ant),
                            sum(depreciacion_acum_actualiz_gest_ant),
                            sum(depreciacion_per),
                            sum(depreciacion_acum),
                            sum(monto_vigente),
                            999,
                            0,
                            'total'
                            from tt_detalle_depreciacion;
                      else ---------------------------------------con filtros estado depreciacion
                            insert into tt_detalle_depreciacion_totales
                            select
                            codigo_padre,
                            denominacion_padre,
                            null,
                            sum(monto_vigente_orig_100),
                            sum(monto_vigente_orig),
                            sum(inc_actualiz),
                            sum(monto_actualiz),
                            null,
                            null,
                            sum(depreciacion_acum_gest_ant),
                            sum(depreciacion_acum_actualiz_gest_ant),
                            sum(depreciacion_per),
                            sum(depreciacion_acum),
                            sum(monto_vigente),
                            replace(codigo_padre,'RE','')::integer,
                            0,
                            'clasif'
                            from tt_detalle_depreciacion
                            where tipo like '%'||v_parametros.estado_depre||'%'
                            group by codigo_padre, denominacion_padre;

                            --Inserta el detalle
                            insert into tt_detalle_depreciacion_totales
                            select
                            codigo,
                            denominacion,
                            fecha_ini_dep,
                            monto_vigente_orig_100,
                            monto_vigente_orig,
                            inc_actualiz,
                            monto_actualiz,
                            vida_util_orig,
                            vida_util,
                            depreciacion_acum_gest_ant,
                            depreciacion_acum_actualiz_gest_ant,
                            depreciacion_per,
                            depreciacion_acum,
                            monto_vigente,
                            codigo_padre::integer,
                            replace(replace(replace(replace(replace(replace(replace(codigo,'A0',''),'AJ',''),'G',''),'RE',''),'ME',''),'.',''),'-','')::bigint,
                            'detalle'
                            from tt_detalle_depreciacion
                            where tipo like '%'||v_parametros.estado_depre||'%';

                            --Inserta los totales finales
                            insert into tt_detalle_depreciacion_totales
                            select
                            'TOTAL FINAL',
                            null,
                            null,
                            sum(monto_vigente_orig_100),
                            sum(monto_vigente_orig),
                            sum(inc_actualiz),
                            sum(monto_actualiz),
                            null,
                            null,
                            sum(depreciacion_acum_gest_ant),
                            sum(depreciacion_acum_actualiz_gest_ant),
                            sum(depreciacion_per),
                            sum(depreciacion_acum),
                            sum(monto_vigente),
                            999,
                            0,
                            'total'
                            from tt_detalle_depreciacion
                            where tipo like '%'||v_parametros.estado_depre||'%';

                      end if;

                else-------------------consolidado
        --          raise exception 'consolidado';

                            insert into tt_detalle_depreciacion_totales
                            select
                            codigo_padre,
                            denominacion_padre,
                            null,
                            sum(monto_vigente_orig_100),
                            sum(monto_vigente_orig),
                            sum(inc_actualiz),
                            sum(monto_actualiz),
                            null,
                            null,
                            sum(depreciacion_acum_gest_ant),
                            sum(depreciacion_acum_actualiz_gest_ant),
                            sum(depreciacion_per),
                            sum(depreciacion_acum),
                            sum(monto_vigente),
                            replace(codigo_padre,'RE','')::integer,
                            0,
                            'clasif'
                            from tt_detalle_depreciacion
                            group by codigo_padre, denominacion_padre;

                            --Inserta el detalle
                            insert into tt_detalle_depreciacion_totales
                            select
                            de.codigo,
                            de.denominacion,
                            COALESCE(
                            (select max(af.fecha_ini_dep)
                            from tt_detalle_depreciacion af
                            where af.codigo=de.codigo
                            and af.tipo = 'reval'),
                            (select max(af.fecha_ini_dep)
                            from tt_detalle_depreciacion af
                            where af.codigo=de.codigo
                            and af.tipo = 'alta')),
                            sum(de.monto_vigente_orig_100),
                            sum(de.monto_vigente_orig),
                            sum(de.inc_actualiz),
                            sum(de.monto_actualiz),
                            COALESCE(
                            (select af.vida_util_orig
                            from tt_detalle_depreciacion af
                            where af.codigo=de.codigo
                            and af.tipo = 'reval'
                            order by af.fecha_ini_dep desc
                            limit 1),
                            (select af.vida_util_orig
                            from tt_detalle_depreciacion af
                            where af.codigo=de.codigo
                            and af.tipo = 'alta'
                            order by af.fecha_ini_dep desc
                            limit 1)) as vida_util_orig,
                            COALESCE(
                            (select af.vida_util
                            from tt_detalle_depreciacion af
                            where af.codigo=de.codigo
                            and af.tipo = 'reval'
                            order by af.fecha_ini_dep asc
                            limit 1),
                            (select af.vida_util
                            from tt_detalle_depreciacion af
                            where af.codigo=de.codigo
                            and af.tipo = 'alta'
                            order by af.fecha_ini_dep asc
                            limit 1)) as vida_util,
                            sum(de.depreciacion_acum_gest_ant),
                            sum(de.depreciacion_acum_actualiz_gest_ant),
                            sum(de.depreciacion_per),
                            sum(de.depreciacion_acum),
                            sum(de.monto_vigente),
                            de.codigo_padre::integer,
                            replace(replace(replace(replace(replace(replace(replace(de.codigo,'A0',''),'AJ',''),'G',''),'RE',''),'ME',''),'.',''),'-','')::bigint,
                            'detalle'
                            from tt_detalle_depreciacion de
                            group by  de.codigo,
                            de.denominacion,
                            de.codigo_padre;

                            --Inserta los totales finales
                            insert into tt_detalle_depreciacion_totales
                            select
                            'TOTAL FINAL',
                            null,
                            null,
                            sum(monto_vigente_orig_100),
                            sum(monto_vigente_orig),
                            sum(inc_actualiz),
                            sum(monto_actualiz),
                            null,
                            null,
                            sum(depreciacion_acum_gest_ant),
                            sum(depreciacion_acum_actualiz_gest_ant),
                            sum(depreciacion_per),
                            sum(depreciacion_acum),
                            sum(monto_vigente),
                            999,
                            0,
                            'total'
                            from tt_detalle_depreciacion;
                end if;

    elsif v_parametros.tipo_repo='geac' then
                CREATE temp TABLE tt_detalle_depreciacion_consol (
                  id_activo_fijo_valor INTEGER,
                  codigo VARCHAR(50),
                  denominacion VARCHAR,
                  fecha_ini_dep DATE,
                  monto_vigente_orig_100 NUMERIC(18,2),
                  monto_vigente_orig NUMERIC(18,2),
                  inc_actualiz NUMERIC(18,2),
                  monto_actualiz NUMERIC(18,2),
                  vida_util_orig INTEGER,
                  vida_util INTEGER,
                  depreciacion_acum_gest_ant NUMERIC(18,2),
                  depreciacion_acum_actualiz_gest_ant NUMERIC(18,2),
                  depreciacion_per NUMERIC(18,2),
                  depreciacion_acum NUMERIC(18,2),
                  monto_vigente NUMERIC(18,2),
                  codigo_padre VARCHAR(15),
                  denominacion_padre VARCHAR(100),
                  tipo VARCHAR(50),
                  tipo_cambio_fin NUMERIC,
                  id_moneda_act INTEGER,
                  id_activo_fijo_valor_original INTEGER,
                  inc_ac_acum	NUMERIC(18,2),
                  val_acu_perido NUMERIC(18,2)
                )on commit drop;

                if v_parametros.total_consol='deta' then ------detallado para gestion actual

                        insert into tt_detalle_depreciacion_totales
                        select
                        codigo_padre,
                        denominacion_padre,
                        null,
                        sum(monto_vigente_orig_100),
                        sum(monto_vigente_orig),
                        sum(inc_actualiz),
                        sum(monto_actualiz),
                        null,
                        null,
                        sum(depreciacion_acum_gest_ant),
                        sum(depreciacion_acum_actualiz_gest_ant),
                        sum(depreciacion_per),
                        sum(depreciacion_acum),
                        sum(monto_vigente),
                        replace(codigo_padre,'RE','')::integer,
                        0,
                        'clasif',
                        sum(inc_ac_acum),
                        sum(val_acu_perido)
                        from tt_detalle_depreciacion
                        group by codigo_padre, denominacion_padre;

                insert into tt_detalle_depreciacion_consol
                        select
                        id_activo_fijo_valor,
                        codigo,
                        denominacion,
                        fecha_ini_dep,
                        monto_vigente_orig_100,
                        monto_vigente_orig,
                        inc_actualiz,
                        monto_actualiz,
                        vida_util_orig,
                        vida_util,
                        depreciacion_acum_gest_ant,
                        depreciacion_acum_actualiz_gest_ant,
                        depreciacion_per,
                        depreciacion_acum,
                        monto_vigente,
                        codigo_padre,
                        denominacion_padre,
                        tipo,
                        tipo_cambio_fin,
                        id_moneda_act,
                        id_activo_fijo_valor_original,
                        inc_ac_acum,
                        val_acu_perido
                        from tt_detalle_depreciacion
                        order by codigo;

                        --Inserta el detalle
                        insert into tt_detalle_depreciacion_totales
                        select
                        de.codigo,
                        de.denominacion,
                        de.fecha_ini_dep,
                        sum(de.monto_vigente_orig_100),
                        sum(de.monto_vigente_orig),
                        sum(de.inc_actualiz),
                        sum(de.monto_actualiz),
                        ac.vida_util_orig,
                        ac.vida_util,
                        sum(de.depreciacion_acum_gest_ant),
                        sum(de.depreciacion_acum_actualiz_gest_ant),
                        sum(de.depreciacion_per),
                        sum(de.depreciacion_acum),
                        sum(de.monto_vigente),
                        de.codigo_padre::integer,
                        replace(replace(replace(replace(replace(replace(replace(de.codigo,'A0',''),'AJ',''),'G',''),'RE',''),'ME',''),'.',''),'-','')::bigint,
                        'detalle',
                        sum(de.inc_ac_acum),
                        sum(de.val_acu_perido)
                        from tt_detalle_depreciacion_consol de
                        inner join tt_detalle_depreciacion ac on ac.codigo=de.codigo
                        group by de.codigo,de.denominacion,
                        de.codigo_padre,ac.vida_util_orig,ac.vida_util,de.fecha_ini_dep;

                        --Inserta los totales finales
                        insert into tt_detalle_depreciacion_totales
                        select
                        'TOTAL FINAL',
                        null,
                        null,
                        sum(monto_vigente_orig_100),
                        sum(monto_vigente_orig),
                        sum(inc_actualiz),
                        sum(monto_actualiz),
                        null,
                        null,
                        sum(depreciacion_acum_gest_ant),
                        sum(depreciacion_acum_actualiz_gest_ant),
                        sum(depreciacion_per),
                        sum(depreciacion_acum),
                        sum(monto_vigente),
                        999,
                        0,
                        'total',
                        sum(inc_ac_acum),
                        sum(val_acu_perido)
                        from tt_detalle_depreciacion;

                        /*v_fecha_actu = kaf.f_mes_anterior(v_parametros.fecha_hasta,v_parametros.actu_perido);

                        create temp table tt_actuli_acumulado (
                            code    VARCHAR(100),
                            inc_ac  numeric(18,2),
                            color   varchar(2)
                        ) on commit drop;

                        insert into tt_actuli_acumulado
                        select
                        cod,
                        inc_act,
                        col
                        from kaf.f_depre_ges_ant(v_id_id_activo_fijo_valor,coalesce(v_parametros.id_moneda,1),v_fecha_actu,v_parametros.total_consol,v_parametros.af_deprec);*/

                else ----consolidado----para gestion actual

                        insert into tt_detalle_depreciacion_totales
                        select
                        codigo_padre,
                        denominacion_padre,
                        null,
                        sum(monto_vigente_orig_100),
                        sum(monto_vigente_orig),
                        sum(inc_actualiz),
                        sum(monto_actualiz),
                        null,
                        null,
                        sum(depreciacion_acum_gest_ant),
                        sum(depreciacion_acum_actualiz_gest_ant),
                        sum(depreciacion_per),
                        sum(depreciacion_acum),
                        sum(monto_vigente),
                        replace(codigo_padre,'RE','')::integer,
                        0,
                        'clasif',
                        sum(inc_ac_acum),
                        sum(val_acu_perido)
                        from tt_detalle_depreciacion
                        group by codigo_padre, denominacion_padre;

                            --Inserta el detalle
                            insert into tt_detalle_depreciacion_totales
                            select
                            de.codigo,
                            de.denominacion,
                            COALESCE(
                            (select max(af.fecha_ini_dep)
                            from tt_detalle_depreciacion af
                            where af.codigo=de.codigo
                            and af.tipo = 'reval'),
                            (select max(af.fecha_ini_dep)
                            from tt_detalle_depreciacion af
                            where af.codigo=de.codigo
                            and af.tipo = 'alta')),
                            sum(de.monto_vigente_orig_100),
                            sum(de.monto_vigente_orig),
                            sum(de.inc_actualiz),
                            sum(de.monto_actualiz),
                            COALESCE(
                            (select af.vida_util_orig
                            from tt_detalle_depreciacion af
                            where af.codigo=de.codigo
                            and af.tipo = 'reval'
                            order by af.fecha_ini_dep desc
                            limit 1),
                            (select af.vida_util_orig
                            from tt_detalle_depreciacion af
                            where af.codigo=de.codigo
                            and af.tipo = 'alta'
                            order by af.fecha_ini_dep desc
                            limit 1)) as vida_util_orig,
                            COALESCE(
                            (select af.vida_util
                            from tt_detalle_depreciacion af
                            where af.codigo=de.codigo
                            and af.tipo = 'reval'
                            order by af.fecha_ini_dep asc
                            limit 1),
                            (select af.vida_util
                            from tt_detalle_depreciacion af
                            where af.codigo=de.codigo
                            and af.tipo = 'alta'
                            order by af.fecha_ini_dep asc
                            limit 1)) as vida_util,
                            sum(de.depreciacion_acum_gest_ant),
                            sum(de.depreciacion_acum_actualiz_gest_ant),
                            sum(de.depreciacion_per),
                            sum(de.depreciacion_acum),
                            sum(de.monto_vigente),
                            de.codigo_padre::integer,
                            replace(replace(replace(replace(replace(replace(replace(de.codigo,'A0',''),'AJ',''),'G',''),'RE',''),'ME',''),'.',''),'-','')::bigint,
                            'detalle',
                            sum(de.inc_ac_acum),
                            sum(de.val_acu_perido)
                            from tt_detalle_depreciacion de
                            group by  de.codigo,
                            de.denominacion,
                            de.codigo_padre;

                        --Inserta los totales finales
                        insert into tt_detalle_depreciacion_totales
                        select
                        'TOTAL FINAL',
                        null,
                        null,
                        sum(monto_vigente_orig_100),
                        sum(monto_vigente_orig),
                        sum(inc_actualiz),
                        sum(monto_actualiz),
                        null,
                        null,
                        sum(depreciacion_acum_gest_ant),
                        sum(depreciacion_acum_actualiz_gest_ant),
                        sum(depreciacion_per),
                        sum(depreciacion_acum),
                        sum(monto_vigente),
                        999,
                        0,
                        'total',
                        sum(inc_ac_acum),
                        sum(val_acu_perido)
                        from tt_detalle_depreciacion;

                        /*v_fecha_actu = kaf.f_mes_anterior(v_parametros.fecha_hasta,v_parametros.actu_perido);

                        create temp table tt_actuli_acumulado (
                            code    VARCHAR(100),
                            inc_ac  numeric(18,2),
                            color   varchar(2)
                        ) on commit drop;

                        insert into tt_actuli_acumulado
                        select
                        cod,
                        inc_act,
                        col
                        from kaf.f_depre_ges_ant(v_id_id_activo_fijo_valor,coalesce(v_parametros.id_moneda,1),v_fecha_actu,v_parametros.total_consol,v_parametros.af_deprec);*/
                    end if;
                               v_where = '(''total'',''detalle'',''clasif'')';
                               if v_parametros.af_deprec = 'clasif' then
                                v_where = '(''total'',''clasif'')';
                               end if;

					 if v_parametros.af_deprec = 'ministerio' then

                                     create temp table tt_detalle_totales_minis(
                                            codigo varchar(50),
                                            denominacion varchar,
                                            monto_vigente_orig_100 numeric(24,2),
                                            monto_vigente_orig numeric(24,2),
                                            inc_actualiz numeric(24,2),
                                            monto_actualiz numeric(24,2),
                                            depreciacion_acum_gest_ant numeric(24,2),
                                            depreciacion_acum_actualiz_gest_ant numeric(24,2),
                                            depreciacion_per numeric(24,2),
                                            depreciacion_acum numeric(24,2),
                                            monto_vigente numeric(24,2),
                                            tipo    	varchar(10),
											inc_ac_acum  numeric(18,2),
                                            orden 	bigint
                                        ) on commit drop;

                     	 	insert into  tt_detalle_totales_minis
									select
                                           case when de.codigo in ('02','10') and de.nivel in (2,10) then
                                            '02'
                                              when de.codigo in ('03','04') and de.nivel in (3,4) then
                                            '03'
                                              when de.codigo in ('05') and de.nivel in (5) then
                                            '04'
                                              when de.codigo in ('06') and de.nivel in (6) then
                                            '05'
                                              when de.codigo in ('07') and de.nivel in (7) then
                                            '06'
                                              when de.codigo in ('08') and de.nivel in (8) then
                                            '07'
                                              when de.codigo in ('09') and de.nivel in (9) then
                                            '08'
                                            else
                                            de.codigo
                                            end,
                                           case when de.codigo in ('02','10') and de.nivel in (2,10) then
                                            'EDIFICIO'
                                              when de.codigo in ('03','04') and de.nivel in (3,4) then
                                            'EQUIPO DE OFICINA Y MUEBLES'
                                            else
                                            de.denominacion
                                            end,
                                            de.monto_vigente_orig_100,
                                            de.monto_vigente_orig,
                                            (de.monto_actualiz - de.monto_vigente_orig)::numeric(18,2) as inc_actualiz,
                                            de.monto_actualiz,
                                            de.depreciacion_acum_gest_ant,
                                            de.depreciacion_acum_actualiz_gest_ant,
                                            case when monto_vigente_orig_100 < 0 then
                                             (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant)
                                            else
                                              case when
                                              (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant) <=0.01 then
                                                  0.00
                                              else
                                                 (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant)
                                              end
                                            end as depreciacion_per,
                                            de.depreciacion_acum,
                                            de.monto_vigente,
                                            case when de.codigo = 'TOTAL FINAL' then
                                             'total'
                                              else
                                              'clasif'
                                              end,
                                            de.inc_ac_acum
                                            from tt_detalle_depreciacion_totales de
                                            -- left join tt_actuli_acumulado ac on ac.code=de.codigo
                                            where de.tipo in ('total','clasif')
                                            order by de.codigo, de.orden;

                                    v_consulta = 'select
                                            codigo::varchar(50),
                                            denominacion::varchar,
                                            now()::date,
                                            sum(monto_vigente_orig_100),
                                            sum(monto_vigente_orig),
                                            sum(monto_actualiz - monto_vigente_orig)::numeric(18,2) as inc_actualiz,
                                            sum(monto_actualiz),
                                            0::integer as vida_util_orig,
                                            0::integer as vida_util,
                                            sum(depreciacion_acum_gest_ant),
                                            sum(depreciacion_acum_actualiz_gest_ant),
				                            sum(depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant),
                                            sum(depreciacion_acum),
                                            sum(monto_vigente),
                                            0::integer as nivel,
                                            orden,
                                            tipo,
                                            0.00 reval,
                                            0.00 ajust,
                                            0.00 baja,
                                            0.00 transito,
                                            0.00 leasing,
                                            sum(inc_ac_acum),
                        					''-''::varchar as color,
                                            sum(monto_actualiz - inc_ac_acum - monto_vigente_orig) as val_acu_perido,
                                            0.00 as porce_depre
                                            from tt_detalle_totales_minis
                                            group by codigo, orden, tipo,denominacion
                                            order by codigo, orden';
							else

                                    v_consulta = 'select
                                            de.codigo,
                                            de.denominacion,
                                            de.fecha_ini_dep,
                                            de.monto_vigente_orig_100,
                                            de.monto_vigente_orig,
                                            (de.monto_actualiz - de.monto_vigente_orig)::numeric(18,2) as inc_actualiz,
                                            de.monto_actualiz,
                                            de.vida_util_orig,
                                            de.vida_util,
                                            de.depreciacion_acum_gest_ant,
                                            de.depreciacion_acum_actualiz_gest_ant,
                          case when monto_vigente_orig_100 < 0 then
                           (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant)
                          else
                            case when
                            (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant) <=0.01 then
                                0.00
                            else
                               (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant)
                            end
                          end as depreciacion_per,
                                            --de.depreciacion_acum - de.depreciacion_acum_gest_ant - de.depreciacion_acum_actualiz_gest_ant,--depreciacion_per,
                                            de.depreciacion_acum,
                                            de.monto_vigente,
                                            de.nivel,
                                            de.orden,
                                            de.tipo,
                                            de.reval,
                                            de.ajust,
                                            de.baja,
                                            de.transito,
                                            de.leasing,
                                            de.inc_ac_acum,
                                            ''''::varchar as color,
                                            de.val_acu_perido,
                                            0.00 as porce_depre
                                            from tt_detalle_depreciacion_totales de
                                            -- left join tt_actuli_acumulado ac on ac.code=de.codigo
                                            where tipo in '||v_where||'
                                            order by codigo, orden';
                        end if;
                        --Devuelve la respuesta
                        return v_consulta;


    else ---------detalle depreciacion nuevo reporte creado


        --          raise exception 'consolidado';
                          insert into tt_detalle_depreciacion_totales

                          select dedep.codigo_padre,
                                 dedep.denominacion_padre,
                                 null,
                                 sum(dedep.monto_vigente_orig_100),
                                 sum(dedep.monto_vigente_orig),
                                 sum(dedep.inc_actualiz),
                                 sum(dedep.monto_actualiz),
                                 null,
                                 null,
                                 sum(dedep.depreciacion_acum_gest_ant),
                                 sum(dedep.depreciacion_acum_actualiz_gest_ant),
                                 sum(dedep.depreciacion_per),
                                 sum(dedep.depreciacion_acum),
                                 sum(dedep.monto_vigente),
                                 replace(dedep.codigo_padre,'RE','')::integer,
                                 0,
                                 'clasif',
                                sum((select sum(dev.monto_vigente_orig)
                                  from tt_detalle_depreciacion dev
                                  where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo='reval'
                                )),
                                sum((select sum(dev.monto_vigente_orig)
                                    from tt_detalle_depreciacion dev
                                    where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo like '%ajuste%'
                                )),
                                sum((select sum(dev.monto_vigente_orig)
                                    from tt_detalle_depreciacion dev
                                    where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo='baja'
                                )),
                                sum((select sum(dev.monto_vigente_orig)
                                    from tt_detalle_depreciacion dev
                                    where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo='transito'
                                )),
                                sum((select sum(dev.monto_vigente_orig)
                                    from tt_detalle_depreciacion dev
                                    where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo='leasing'
                                ))
                            from tt_detalle_depreciacion dedep
                            where dedep.tipo='alta'
                            group by dedep.codigo_padre, dedep.denominacion_padre;




                          insert into tt_detalle_depreciacion_totales

                          select dedep.codigo,
                                 dedep.denominacion,
                                 dedep.fecha_ini_dep,
                                 dedep.monto_vigente_orig_100,
                                 dedep.monto_vigente_orig ,
                                 dedep.inc_actualiz + coalesce(
                                        (select sum(dev.inc_actualiz)
                                         from tt_detalle_depreciacion dev
                                     where dedep.codigo = kaf.f_tam_codigo(dev.codigo)
                                     and dev.tipo <> 'alta'
                                 ),0),
                                 dedep.monto_actualiz + coalesce(
                                        (select sum(dev.monto_actualiz)
                                         from tt_detalle_depreciacion dev
                                     where dedep.codigo = kaf.f_tam_codigo(dev.codigo)
                                     and dev.tipo <> 'alta'
                                 ),0),
                                 dedep.vida_util_orig,
                                 dedep.vida_util,
                                 dedep.depreciacion_acum_gest_ant + coalesce(
                                        (select sum(dev.depreciacion_acum_gest_ant)
                                         from tt_detalle_depreciacion dev
                                     where dedep.codigo = kaf.f_tam_codigo(dev.codigo)
                                     and dev.tipo <> 'alta'
                                 ),0),
                                 dedep.depreciacion_acum_actualiz_gest_ant + coalesce(
                                        (select sum(dev.depreciacion_acum_actualiz_gest_ant)
                                         from tt_detalle_depreciacion dev
                                     where dedep.codigo = kaf.f_tam_codigo(dev.codigo)
                                     and dev.tipo <> 'alta'
                                 ),0),
                                 dedep.depreciacion_per + coalesce(
                                        (select sum(dev.depreciacion_per)
                                         from tt_detalle_depreciacion dev
                                     where dedep.codigo = kaf.f_tam_codigo(dev.codigo)
                                     and dev.tipo <> 'alta'
                                 ),0),
                                 dedep.depreciacion_acum + coalesce(
                                        (select sum(dev.depreciacion_acum)
                                         from tt_detalle_depreciacion dev
                                     where dedep.codigo = kaf.f_tam_codigo(dev.codigo)
                                     and dev.tipo <> 'alta'
                                 ),0),
                                 dedep.monto_vigente + coalesce(
                                        (select sum(dev.monto_vigente)
                                         from tt_detalle_depreciacion dev
                                     where dedep.codigo = kaf.f_tam_codigo(dev.codigo)
                                     and dev.tipo <> 'alta'
                                 ),0),
                                 dedep.codigo_padre::integer,
                                 replace(replace(replace(replace(replace(replace(replace(codigo,'A0',''),'AJ',''),'G',''),'RE',''),'ME',''),'.',''),'-','')::bigint,
                                 'detalle',
                                (select sum(dev.monto_vigente_orig)
                                  from tt_detalle_depreciacion dev
                                  where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo='reval'
                                ),
                                (select sum(dev.monto_vigente_orig)
                                    from tt_detalle_depreciacion dev
                                    where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo like '%ajuste%'
                                ),
                                (select sum(dev.monto_vigente_orig)
                                    from tt_detalle_depreciacion dev
                                    where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo='baja'
                                ),
                                (select sum(dev.monto_vigente_orig)
                                    from tt_detalle_depreciacion dev
                                    where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo='transito'
                                ),
                                (select sum(dev.monto_vigente_orig)
                                    from tt_detalle_depreciacion dev
                                    where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo='leasing'
                                )
                            from tt_detalle_depreciacion dedep
                            where dedep.tipo='alta';



                          insert into tt_detalle_depreciacion_totales

                          select 'TOTAL FINAL',
                                 null,
                                 null,
                                 sum(dedep.monto_vigente_orig_100),
                                 sum(dedep.monto_vigente_orig),
                                 sum(dedep.inc_actualiz),
                                 sum(dedep.monto_actualiz),
                                 null,
                                 null,
                                 sum(dedep.depreciacion_acum_gest_ant),
                                 sum(dedep.depreciacion_acum_actualiz_gest_ant),
                                 sum(dedep.depreciacion_per),
                                 sum(dedep.depreciacion_acum),
                                 sum(dedep.monto_vigente),
                                 999,
                                 0,
                                 'total',
                                sum((select sum(dev.monto_vigente_orig)
                                  from tt_detalle_depreciacion dev
                                  where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo='reval'
                                )),
                                sum((select sum(dev.monto_vigente_orig)
                                    from tt_detalle_depreciacion dev
                                    where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo like '%ajuste%'
                                )),
                                sum((select sum(dev.monto_vigente_orig)
                                    from tt_detalle_depreciacion dev
                                    where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo='baja'
                                )),
                                sum((select sum(dev.monto_vigente_orig)
                                    from tt_detalle_depreciacion dev
                                    where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo='transito'
                                )),
                                sum((select sum(dev.monto_vigente_orig)
                                    from tt_detalle_depreciacion dev
                                    where dedep.codigo = kaf.f_tam_codigo(dev.codigo) and dev.tipo='leasing'
                                ))
                            from tt_detalle_depreciacion_totales dedep
                            where dedep.tipo='detalle';


                        v_fecha_actu = kaf.f_mes_anterior(v_parametros.fecha_hasta,v_parametros.actu_perido);

                        create temp table tt_actuli_acumulado (
                            code    VARCHAR(100),
                            inc_ac  numeric(18,2),
                            color   varchar(2)
                        ) on commit drop;

                        insert into tt_actuli_acumulado
                        select
                        cod,
                        inc_act,
                        col
                        from kaf.f_depre_ges_ant(v_id_id_activo_fijo_valor,coalesce(v_parametros.id_moneda,1),v_fecha_actu,'consoli',v_parametros.af_deprec);

                              v_where = '(''total'',''detalle'',''clasif'')';
                               if v_parametros.af_deprec = 'clasif' then
                                v_where = '(''total'',''clasif'')';
                               end if;

                                    v_consulta = 'select
                                            de.codigo,
                                            de.denominacion,
                                            de.fecha_ini_dep,
                                            de.monto_vigente_orig_100,
                                            de.monto_vigente_orig,
                                            (de.monto_actualiz - de.monto_vigente_orig)::numeric(18,2) as inc_actualiz,
                                            de.monto_actualiz,
                                            de.vida_util_orig,
                                            de.vida_util,
                                            de.depreciacion_acum_gest_ant,
                                            de.depreciacion_acum_actualiz_gest_ant,
                          case when codigo like ''%AJ%'' then
                           (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant)
                          else
                            case when
                            (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant) <=0.01 then
                                0.00
                            else
                               (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant)
                            end
                          end as depreciacion_per,
                                            --de.depreciacion_acum - de.depreciacion_acum_gest_ant - de.depreciacion_acum_actualiz_gest_ant,--depreciacion_per,
                                            de.depreciacion_acum,
                                            de.monto_vigente,
                                            de.nivel,
                                            de.orden,
                                            de.tipo,
                                            de.reval,
                                            de.ajust,
                                            de.baja,
                                            de.transito,
                                            de.leasing,
                                            ac.inc_ac as inc_ac_acum,
                                            ac.color,
                                            (de.monto_actualiz - ac.inc_ac - de.monto_vigente_orig)::numeric(18,2) as val_acu_perido,
                                            0.00 as porce_depre
                                            from tt_detalle_depreciacion_totales de
                                            inner join tt_actuli_acumulado ac on ac.code=de.codigo
                                            where tipo in '||v_where||'
                                            order by codigo, orden';
                                --Devuelve la respuesta
                                return v_consulta;


    end if;
              v_where = '(''total'',''detalle'',''clasif'')';
              if v_parametros.af_deprec = 'clasif' then
                  v_where = '(''total'',''clasif'')';
              end if;

    	   if v_parametros.af_deprec = 'ministerio' then

                create temp table tt_detalle_totales_minis(
                    codigo varchar(50),
                    denominacion varchar,
                    monto_vigente_orig_100 numeric(24,2),
                    monto_vigente_orig numeric(24,2),
                    inc_actualiz numeric(24,2),
                    monto_actualiz numeric(24,2),
                    depreciacion_acum_gest_ant numeric(24,2),
                    depreciacion_acum_actualiz_gest_ant numeric(24,2),
                    depreciacion_per numeric(24,2),
                    depreciacion_acum numeric(24,2),
                    monto_vigente numeric(24,2),
                    tipo    varchar(10),
                    orden 	bigint
                ) on commit drop;

              	 insert into tt_detalle_totales_minis
              		select
                         case when codigo in ('02','10') and nivel in (2,10) then
                          '02'
                            when codigo in ('03','04') and nivel in (3,4) then
                          '03'
                            when codigo in ('05') and nivel in (5) then
                          '04'
                            when codigo in ('06') and nivel in (6) then
                          '05'
                            when codigo in ('07') and nivel in (7) then
                          '06'
                            when codigo in ('08') and nivel in (8) then
                          '07'
                            when codigo in ('09') and nivel in (9) then
                          '08'
                          else
                          codigo
                          end,
                         case when codigo in ('02','10') and nivel in (2,10) then
                          'EDIFICIO'
                            when codigo in ('03','04') and nivel in (3,4) then
                          'EQUIPO DE OFICINA Y MUEBLES'
                          else
                          denominacion
                          end,
                          monto_vigente_orig_100,
                          monto_vigente_orig,
                          (monto_actualiz - monto_vigente_orig)::numeric(18,2) as inc_actualiz,
                          monto_actualiz,
                          depreciacion_acum_gest_ant,
                          depreciacion_acum_actualiz_gest_ant,
                          case when monto_vigente_orig_100 < 0 then
                           (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant)
                          else
                            case when
                            (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant) <=0.01 then
                                0.00
                            else
                               (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant)
                            end
                          end as depreciacion_per,
                          depreciacion_acum,
                          monto_vigente,
                         case when codigo = 'TOTAL FINAL' then
                         'total'
                          else
                          'clasif'
                          end
                          from tt_detalle_depreciacion_totales
                          where tipo  in ('total','clasif')
                          order by codigo, orden;

				v_consulta = 'select
                          codigo::varchar(50),
                          denominacion::varchar,
                          now()::date as fecha_ini_dep,
                          sum(monto_vigente_orig_100),
                          sum(monto_vigente_orig),
                          sum((monto_actualiz - monto_vigente_orig)),
                          sum(monto_actualiz),
                          0::integer as vida_util_orig,
                          0::integer as vida_util,
                          sum(depreciacion_acum_gest_ant),
                          sum(depreciacion_acum_actualiz_gest_ant),
                          sum(depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant),
                          sum(depreciacion_acum),
                          sum(monto_vigente),
                          0::integer as nivel,
                          orden,
                          tipo,
                          0.00 reval,
                          0.00 ajust,
                          0.00 baja,
                          0.00 transito,
                          0.00 leasing,
                          0.00 as inc_ac_acum,
                          ''-''::varchar as color,
                          0.00 as val_acu_perido,
                          0.00 as porce_depre
                          from tt_detalle_totales_minis
                          group by codigo, orden, tipo,denominacion
                          order by codigo';

           else

              v_consulta = 'select
                          codigo,
                          denominacion,
                          fecha_ini_dep,
                          monto_vigente_orig_100,
                          monto_vigente_orig,
                          (monto_actualiz - monto_vigente_orig)::numeric(18,2) as inc_actualiz,
                          monto_actualiz,
                          vida_util_orig,
                          vida_util,
                          depreciacion_acum_gest_ant,
                          depreciacion_acum_actualiz_gest_ant,
                          case when monto_vigente_orig_100 < 0 then
                           (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant)
                          else
                            case when
                            (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant) <=0.01 then
                                0.00
                            else
                               (depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant)
                            end
                          end as depreciacion_per,
                          --depreciacion_acum - depreciacion_acum_gest_ant - depreciacion_acum_actualiz_gest_ant,--depreciacion_per,
                          depreciacion_acum,
                          monto_vigente,
                          nivel,
                          orden,
                          tipo,
                          reval,
                          ajust,
                          baja,
                          transito,
                          leasing,
                          0.00 as inc_ac_acum, --para completar el modelo no valido
                          ''-''::varchar as color,
                          0.00 as val_acu_perido,
                          case when (vida_util_orig = 0 or vida_util_orig = 1)then
                          0.00
                          else
                          (100/(vida_util_orig::numeric/12)) end as porce_depre
                          from tt_detalle_depreciacion_totales
                          where tipo in '||v_where||'
                          order by codigo, orden';
            end if;

              raise notice 'v_consulta: %', v_consulta;
              --Devuelve la respuesta

        return v_consulta;

    	end;
    else
        raise exception 'Transacción inexistente';
    end if;
EXCEPTION
  WHEN OTHERS THEN
    v_respuesta='';
    v_respuesta=pxp.f_agrega_clave(v_respuesta,'mensaje',SQLERRM);
    v_respuesta=pxp.f_agrega_clave(v_respuesta,'codigo_error',SQLSTATE);
    v_respuesta=pxp.f_agrega_clave(v_respuesta,'procedimiento',v_nombre_funcion);
    raise exception '%',v_respuesta;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION kaf.f_reportes_dep_impuestos (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
