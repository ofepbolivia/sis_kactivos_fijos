CREATE OR REPLACE FUNCTION kaf.f_depre_ges_ant (
  filtro text,
  moneda integer,
  fecha_enviada date,
  total_consol varchar,
  af_deprec varchar
)
RETURNS TABLE (
  cod varchar,
  inc_act numeric,
  col varchar
) AS
$body$
    DECLARE

    v_nombre_funcion  varchar;
    v_consulta        varchar;
    v_parametros      record;
    v_respuesta       varchar;
    v_id_items        varchar[];
    v_where           varchar;
    v_ids             varchar;
    v_fecha           date;
    v_ids_depto       varchar;
    v_sql             varchar;
    v_aux             varchar;
        
    v_lugar           varchar = '';
    v_filtro          varchar;
    v_record          record;
    v_desc_nombre     varchar;
    v_resp            varchar;
    v_peti			  numeric(18,2);
        
    BEGIN

    begin 
            
            
            create temp table tt_af_filtro_actu (
                id_activo_fijo integer
            ) on commit drop;

            v_consulta = 'insert into tt_af_filtro_actu
                        select afij.id_activo_fijo_valor
                        from kaf.tactivo_fijo_valores afij
                        where afij.id_activo_fijo_valor in ('||filtro||')';                                                                                               
            
            execute(v_consulta);
            
            
            --Creación de la tabla con los datos de la depreciación
            create temp table tt_detalle_depreciacion_actu (
                id_activo_fijo_valor integer,
                codigo varchar(50),
                denominacion varchar(500),
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
                id_activo_fijo_valor_original integer
            ) on commit drop;

            --Carga los datos en la tabla temporal
            insert into tt_detalle_depreciacion_actu(
            id_activo_fijo_valor,codigo, denominacion ,fecha_ini_dep,monto_vigente_orig_100,monto_vigente_orig,inc_actualiz,
            monto_actualiz,vida_util_orig,vida_util,
            depreciacion_per,depreciacion_acum,monto_vigente,codigo_padre,denominacion_padre,tipo,tipo_cambio_fin,id_moneda_act,
            id_activo_fijo_valor_original
            )
            select
            afv.id_activo_fijo_valor,
            afv.codigo,        
            af.denominacion,          
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.fecha_ini_dep
                else (select fecha_ini_dep from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end as fecha_ini_dep,
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig_100
                else (select monto_vigente_orig_100 from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end as monto_vigente_orig_100,

            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end as monto_vigente_orig,

            case 
                when (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0)) < 0 then 0
                else (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0))
            end as inc_actualiz,
            mdep.monto_actualiz,

            afv.vida_util_orig, mdep.vida_util,
            mdep.depreciacion_per,
            mdep.depreciacion_acum,
            mdep.monto_vigente,
            substr(afv.codigo,1, position('.' in afv.codigo)-1) as codigo_padre,
            (select nombre from kaf.tclasificacion where codigo_completo_tmp = substr(afv.codigo,1, position('.' in afv.codigo)-1)) as denominacion_padre,
            afv.tipo,
            mdep.tipo_cambio_fin,
            mon.id_moneda_act,
            afv.id_activo_fijo_valor_original
            from kaf.tmovimiento_af_dep mdep
            inner join kaf.tactivo_fijo_valores afv
            on afv.id_activo_fijo_valor = mdep.id_activo_fijo_valor
            inner join kaf.tactivo_fijo af
            on af.id_activo_fijo = afv.id_activo_fijo
            inner join kaf.tmoneda_dep mon
            on mon.id_moneda =  afv.id_moneda_dep
            where date_trunc('month',mdep.fecha) = date_trunc('month',fecha_enviada::date)
            and mdep.id_moneda_dep = moneda
            and afv.id_activo_fijo_valor in (select id_activo_fijo from tt_af_filtro_actu)
                                                            
            and af.estado <> 'eliminado';
            
        
            insert into tt_detalle_depreciacion_actu(
            id_activo_fijo_valor,codigo, denominacion ,fecha_ini_dep,monto_vigente_orig_100,monto_vigente_orig,inc_actualiz,
            monto_actualiz,vida_util_orig,vida_util,
            depreciacion_per,depreciacion_acum,monto_vigente,codigo_padre,denominacion_padre,tipo,tipo_cambio_fin,id_moneda_act,
            id_activo_fijo_valor_original
            )
            select
            afv.id_activo_fijo_valor,
            afv.codigo,
            af.denominacion,
            afv.fecha_ini_dep,
            afv.monto_vigente_orig_100,
            afv.monto_vigente_orig,
            case 
                  when (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0)) < 0 then 0
                  else (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0))
            end as inc_actualiz,
            mdep.monto_actualiz,

            afv.vida_util_orig, mdep.vida_util,
            mdep.depreciacion_per,
            mdep.depreciacion_acum,
            mdep.monto_vigente,
            substr(afv.codigo,1, position('.' in afv.codigo)-1) as codigo_padre,
            (select nombre from kaf.tclasificacion where codigo_completo_tmp = substr(afv.codigo,1, position('.' in afv.codigo)-1)) as denominacion_padre,
            afv.tipo,
            mdep.tipo_cambio_fin,
            mon.id_moneda_act,
            afv.id_activo_fijo_valor_original
            from kaf.tmovimiento_af_dep mdep
            inner join kaf.tactivo_fijo_valores afv
            on afv.id_activo_fijo_valor = mdep.id_activo_fijo_valor
            inner join kaf.tactivo_fijo af
            on af.id_activo_fijo = afv.id_activo_fijo
            inner join kaf.tmoneda_dep mon
            on mon.id_moneda =  afv.id_moneda_dep
            where afv.fecha_fin is not null
            and  not exists (select from kaf.tactivo_fijo_valores where id_activo_fijo_valor_original = afv.id_activo_fijo_valor and tipo<>'alta')
            and afv.codigo not in (select codigo
                                                from tt_detalle_depreciacion_actu)
            
            and date_trunc('month',mdep.fecha) <> date_trunc('month',fecha_enviada::date)
            and date_trunc('month',mdep.fecha) < date_trunc('month',fecha_enviada::date) 
            and date_trunc('month',mdep.fecha) = (select max(fecha)
                                                    from kaf.tmovimiento_af_dep
                                                    where id_activo_fijo_valor = afv.id_activo_fijo_valor
                                                    and id_moneda_dep = mdep.id_moneda_dep
                                                    and date_trunc('month',fecha) <> date_trunc('month',fecha_enviada::date)
                                                    and date_trunc('month',fecha) < date_trunc('month',fecha_enviada::date) 
                                                )
            and mdep.id_moneda_dep = moneda
            and afv.id_activo_fijo_valor in (select id_activo_fijo from tt_af_filtro_actu)
            and afv.id_activo_fijo_valor not in (select id_activo_fijo_valor
                                                from tt_detalle_depreciacion_actu)
            and af.estado <> 'eliminado'
            and af.fecha_baja >= fecha_enviada::date;     
            
            --------------------------------
            --------------------------------
            insert into tt_detalle_depreciacion_actu(
            id_activo_fijo_valor,codigo, denominacion ,fecha_ini_dep,monto_vigente_orig_100,monto_vigente_orig,inc_actualiz,
            monto_actualiz,vida_util_orig,vida_util,
            depreciacion_per,depreciacion_acum,monto_vigente,codigo_padre,denominacion_padre,tipo,tipo_cambio_fin,id_moneda_act,
            id_activo_fijo_valor_original
            )
            select
            afv.id_activo_fijo_valor,
            afv.codigo,
            af.denominacion,            
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.fecha_ini_dep
                else (select fecha_ini_dep from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end as fecha_ini_dep,

            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig_100
                else (select monto_vigente_orig_100 from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end as monto_vigente_orig_100,

            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end as monto_vigente_orig,

            case 
                when (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0)) < 0 then 0
                else (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0))
            end as inc_actualiz,
            mdep.monto_actualiz,
            afv.vida_util_orig, mdep.vida_util,
            mdep.depreciacion_per,
            mdep.depreciacion_acum,
            mdep.monto_vigente,
            substr(afv.codigo,1, position('.' in afv.codigo)-1) as codigo_padre,
            (select nombre from kaf.tclasificacion where codigo_completo_tmp = substr(afv.codigo,1, position('.' in afv.codigo)-1)) as denominacion_padre,
            afv.tipo,
            mdep.tipo_cambio_fin,
            mon.id_moneda_act,
            afv.id_activo_fijo_valor_original
            from kaf.tmovimiento_af_dep mdep
            inner join kaf.tactivo_fijo_valores afv
            on afv.id_activo_fijo_valor = mdep.id_activo_fijo_valor
            inner join kaf.tactivo_fijo af
            on af.id_activo_fijo = afv.id_activo_fijo
            inner join kaf.tmoneda_dep mon
            on mon.id_moneda =  afv.id_moneda_dep
            where af.estado in ('baja','retiro')
            and mdep.fecha >= '01-01-2017'
            and mdep.fecha = (select max(fecha) from kaf.tmovimiento_af_dep mdep1
                                where mdep1.id_activo_fijo_valor = afv.id_activo_fijo_valor
                                and fecha between ('01-01-'||extract(year from mdep.fecha))::date and fecha_enviada::date)
            
            and mdep.id_moneda_dep = moneda
            and afv.id_activo_fijo_valor in (select id_activo_fijo from tt_af_filtro_actu)
                                                            --and afv.codigo not like '%-G%'
            and afv.id_activo_fijo_valor not in (select id_activo_fijo_valor
                                                from tt_detalle_depreciacion_actu);


            
            
            --Obtiene los datos de gestion anterior
            update tt_detalle_depreciacion_actu set
            depreciacion_acum_gest_ant = coalesce((
                select depreciacion_acum
                from kaf.tmovimiento_af_dep
                where id_activo_fijo_valor = tt_detalle_depreciacion_actu.id_activo_fijo_valor
                and id_moneda_dep = moneda
                and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from fecha_enviada::date)::integer -1 )::date)
            ),0),
            depreciacion_acum_actualiz_gest_ant = (((tt_detalle_depreciacion_actu.tipo_cambio_fin/(param.f_get_tipo_cambio_v2(tt_detalle_depreciacion_actu.id_moneda_act, moneda, ('31/12/'||extract(year from fecha_enviada::date)::integer -1)::date, 'O'))))-1)*(coalesce((
                            select depreciacion_acum
                            from kaf.tmovimiento_af_dep
                            where id_activo_fijo_valor = tt_detalle_depreciacion_actu.id_activo_fijo_valor
                            and id_moneda_dep = moneda
                            and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from fecha_enviada)::integer -1 )::date)
                        ),0));
                        
            
            update tt_detalle_depreciacion_actu set
            depreciacion_acum_gest_ant = coalesce((
                select depreciacion_acum
                from kaf.tmovimiento_af_dep
                where id_activo_fijo_valor = tt_detalle_depreciacion_actu.id_activo_fijo_valor_original
                and tipo = tt_detalle_depreciacion_actu.tipo
                and id_moneda_dep = moneda
                and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from fecha_enviada::date)::integer -1 )::date)
            ),0),
            depreciacion_acum_actualiz_gest_ant = (((tt_detalle_depreciacion_actu.tipo_cambio_fin/(param.f_get_tipo_cambio_v2(tt_detalle_depreciacion_actu.id_moneda_act, moneda, ('31/12/'||extract(year from fecha_enviada::date)::integer -1)::date, 'O'))))-1)*(coalesce((
                            select depreciacion_acum
                            from kaf.tmovimiento_af_dep
                            where id_activo_fijo_valor = tt_detalle_depreciacion_actu.id_activo_fijo_valor_original
                            and tipo = tt_detalle_depreciacion_actu.tipo
                            and id_moneda_dep = moneda
                            and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from fecha_enviada::date)::integer -1 )::date)
                        ),0))
            where coalesce(depreciacion_acum_gest_ant,0) = 0
            and id_activo_fijo_valor_original is not null;
            

            --Verifica si hay reg con tipo = ajuste_restar, y le cambia el signo
            update tt_detalle_depreciacion_actu set
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

            create temp table tt_detalle_depreciacion_totales_actu (
                codigo varchar(50),
                denominacion varchar(500),
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
                color varchar(2)
            ) on commit drop;



  CREATE temp TABLE tt_detalle_depreciacion_consol_actuali (
        id_activo_fijo_valor INTEGER,
        codigo VARCHAR(50),
        denominacion VARCHAR(500),
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
        id_activo_fijo_valor_original INTEGER
      )on commit drop;
      
	if total_consol = 'consoli' then 

            insert into tt_detalle_depreciacion_totales_actu
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
            from tt_detalle_depreciacion_actu
            group by codigo_padre, denominacion_padre;

    insert into tt_detalle_depreciacion_consol_actuali
            select
            id_activo_fijo_valor,                     
            kaf.f_range(id_activo_fijo_valor),            
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
            id_activo_fijo_valor_original
            from tt_detalle_depreciacion_actu
            order by codigo;     
                                           
            --Inserta el detalle
    insert into tt_detalle_depreciacion_totales_actu            
            select                             
            de.codigo,                  
            de.denominacion,
            ac.fecha_ini_dep,          
            sum(de.monto_vigente_orig_100),
            sum(de.monto_vigente_orig),            
            sum(de.inc_actualiz),
            sum(de.monto_actualiz),
            null,
            null,        
            sum(de.depreciacion_acum_gest_ant),                            
            sum(de.depreciacion_acum_actualiz_gest_ant),                                                                                                          
            sum(de.depreciacion_per),                            
            sum(de.depreciacion_acum),                            
            sum(de.monto_vigente),
            de.codigo_padre::integer,                            
			replace(replace(replace(replace(replace(replace(replace(de.codigo,'A0',''),'AJ',''),'G',''),'RE',''),'ME',''),'.',''),'-','')::bigint,
            'detalle',
            kaf.f_activo_si_no_rev(de.codigo)    
            from tt_detalle_depreciacion_consol_actuali de
            inner join kaf.tactivo_fijo ac on ac.codigo=de.codigo
            group by de.codigo,de.denominacion,ac.fecha_ini_dep,
            de.codigo_padre;

            --Inserta los totales finales
    insert into tt_detalle_depreciacion_totales_actu
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
            from tt_detalle_depreciacion_actu;                      
	else 
insert into tt_detalle_depreciacion_totales_actu
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
       from tt_detalle_depreciacion_actu         
       group by codigo_padre, denominacion_padre;

       --Inserta el detalle
       insert into tt_detalle_depreciacion_totales_actu
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
       from tt_detalle_depreciacion_actu;

       --Inserta los totales finales
       insert into tt_detalle_depreciacion_totales_actu
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
       from tt_detalle_depreciacion_actu;         
    end if;	 
       
    if af_deprec = 'clasif' then 
		return query
	    	   select 
	           codigo,
	          (monto_actualiz - monto_vigente_orig)::numeric(18,2) as inc_actualiz,
	          color
	          from tt_detalle_depreciacion_totales_actu
	          where tipo in ('total','clasif')
	          order by codigo, orden;     
    else 
		return query
	    	   select 
	           codigo,
	          (monto_actualiz - monto_vigente_orig)::numeric(18,2) as inc_actualiz,
	          color
	          from tt_detalle_depreciacion_totales_actu
	          where tipo in ('total','detalle','clasif')
	          order by codigo, orden;     
    end if; 
          
	return;
    end; 
    END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100 ROWS 1000;