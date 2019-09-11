CREATE OR REPLACE FUNCTION kaf.f_reportes_af (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/***************************************************************************
 SISTEMA:        Activos Fijos
 FUNCION:        kaf.f_reportes_af
 DESCRIPCION:    Funcion que devuelve conjunto de datos para reportes de activos fijos
 AUTOR:          RCM
 FECHA:          09/05/2016
 COMENTARIOS:
***************************************************************************/

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
    v_fecha_actu      date;
    v_campos          record;
    v_id_id_activo_fijo_valor text;
	v_filtro_internac	varchar='';    
    v_condicion			varchar='';
    v_ufv_mes_repo		numeric;
    v_ufv_ant_anio		numeric;
    v_dep_ges_mes		integer;        

BEGIN

    v_nombre_funcion='kaf.f_reportes_af';
    v_parametros=pxp.f_get_record(p_tabla);

    /*********************************
     #TRANSACCION:  'SKA_RESDEP_SEL'
     #DESCRIPCION:  Reporte de depreciacion
     #AUTOR:        RCM
     #FECHA:        09/05/2016
    ***********************************/

    if(p_transaccion='SKA_RESDEP_SEL') then

        begin

            --------------------------------
            -- 0. VALIDACION DE PARAMETROS
            --------------------------------
            if v_parametros.id_movimiento is null then
                if v_parametros.fecha is null or v_parametros.ids_depto is null then
                    raise exception 'Parametros invalidos';
                end if;
            end if;


            --------------------------------------------------------------------------
            -- 1. IDENTIFICAR ACTIVOS FIJOS/REVALORIZACIONES EN BASE A LOS PARAMETROS
            --------------------------------------------------------------------------
            v_fecha = v_parametros.fecha;
            v_ids_depto = v_parametros.ids_depto;

            if p_id_movimiento is not null then
                select fecha_hasta, id_depto
                into v_fecha, v_ids_depto
                from kaf.tmovimiento
                where id_movimiento = p_id_movimiento;
            end if;

            --Creacion de tabla temporal para almacenar los IDs de los activos fijos
            create temp table tt_kaf_rep_dep (
                id_activo_fijo integer,
                id_activo_fijo_valor integer,
                id_movimiento_af_dep integer
            ) on commit drop;

            if p_id_movimiento is not null then

                insert into tt_kaf_rep_dep (id_activo_fijo,id_activo_fijo_valor,id_movimiento_af_dep)
                select maf.id_activo_fijo, mafdep.id_activo_fijo_valor, mafdep.id_movimiento_af_dep
                from kaf.tmovimiento_af maf
                inner join kaf.tmovimiento_af_dep mafdep
                on mafdep.id_movimiento_af = maf.id_movimiento_af
                where maf.id_movimiento = p_id_movimiento;

            else

                v_sql = 'insert into tt_kaf_rep_dep (id_activo_fijo,id_activo_fijo_valor,id_movimiento_af_dep)
                    select id_activo_fijo,id_activo_fijo_valor,id_movimiento_af_dep
                    from (
                    select
                    maf.id_activo_fijo, mafdep.id_activo_fijo_valor, mafdep.id_movimiento_af_dep, max(mafdep.fecha)
                    from kaf.tmovimiento_af_dep mafdep
                    inner join kaf.tmovimiento_af maf
                    on maf.id_movimiento_af = mafdep.id_movimiento_af
                    inner join kaf.tmovimiento mov
                    on mov.id_movimiento = maf.id_movimiento
                    where mov.estado = ''finalizado''
                    and mafdep.fecha <= '''||v_fecha||'''
                    and mov.id_depto = ANY(ARRAY['||v_parametros.ids_depto||'])
                    group by maf.id_activo_fijo,mafdep.id_activo_fijo_valor,mafdep.id_movimiento_af_dep
                    ) dd';

                execute(v_sql);

            end if;

            ---------------------------------------
            -- 2. CONSULTA EN FORMATO DEL REPORTE
            ---------------------------------------
            v_consulta = 'select
                    actval.codigo, af.denominacion, actval.fecha_ini_dep as ''fecha_inc'', actval.monto_vigente_orig as ''valor_original'',
                    mafdep.monto_actualiz - actval.monto_vigente_orig as ''inc_actualiz'', mafdep.monto_actualiz as ''valor_actualiz'',
                    actval.vida_util_orig, mafdep.vida_util,
                    (select o_dep_acum_ant from kaf.f_get_datos_deprec_ant(mafdep.id_activo_fijo_valor,mafdep.fecha)) as ''dep_acum_gestion_ant'',
                    (select o_inc_dep_actualiz from kaf.f_get_datos_deprec_ant(mafdep.id_activo_fijo_valor,mafdep.fecha)) as ''dep_acum_gestion_ant_actualiz'',
                    (mafdep.depreciacion_aum - select o_dep_acum_ant from kaf.f_get_datos_deprec_ant(mafdep.id_activo_fijo_valor,mafdep.fecha)) as dep_gestion,
                    mafdep.depreciacion_aum,
                    mafdep.monto_vigente
                    from tt_kaf_rep_dep rep
                    inner join kaf.tactivo_fijo af
                    on af.id_activo_fijo = rep.id_activo_fijo
                    inner join kaf.tactivo_fijo_valores actval
                    on actval.id_activo_fijo_valor = rep.id_activo_fijo_valor
                    inner join kaf.tmovimiento_af_dep mafdep
                    on mafdep.id_movimiento_af_dep = rep.id_movimiento_af_dep';


            return v_consulta;
        end;

    /*********************************
     #TRANSACCION:  'SKA_KARD_SEL'
     #DESCRIPCION:  Reporte de kardex de activo fijo
     #AUTOR:        RCM
     #FECHA:        27/07/2017
    ***********************************/

    elsif(p_transaccion='SKA_KARD_SEL') then

        begin

            v_aux = 'no';
            if(pxp.f_existe_parametro(p_tabla,'af_estado_mov')) then
                if v_parametros.af_estado_mov <> 'todos' then
                    v_aux = 'si';
                end if;
            end if;

					v_consulta:='select
                        af.codigo,
                        af.denominacion,
                        af.fecha_compra,
                        af.fecha_ini_dep,
                        af.estado,
                        af.vida_util_original,
                        (af.vida_util_original/12) as porcentaje_dep,
                        af.ubicacion,
                        af.monto_compra_orig,
                        mon.moneda,
                        af.nro_cbte_asociado,
                        af.fecha_cbte_asociado,
                        cla.codigo_completo_tmp as cod_clasif, cla.nombre as desc_clasif,
                        mdep.descripcion as metodo_dep,
                        param.f_get_tipo_cambio(3,af.fecha_compra,''O'') as ufv_fecha_compra,
                        fun.desc_funcionario2 as responsable,
                        orga.f_get_cargo_x_funcionario_str(coalesce(mov.id_funcionario_dest,coalesce(mov.id_funcionario,af.id_funcionario)),now()::date) as cargo,
                        mov.fecha_mov, mov.num_tramite,
                        proc.descripcion as desc_mov,
                        proc.codigo as codigo_mov,
                        param.f_get_tipo_cambio(3,mov.fecha_mov,''O'') as ufv_mov,
                        af.id_activo_fijo,
                        mov.id_movimiento,
                        coalesce(afvi.monto_vigente_orig_100,afvi.monto_vigente_orig) as monto_vigente_orig_100,
                        afvi.monto_vigente_orig,
                        afvi.monto_vigente_ant,
                        afvi.monto_actualiz - afvi.monto_vigente_ant actualiz_monto_vigente,
                        afvi.monto_actualiz as monto_actualiz,
                        afvi.vida_util_orig - afvi.vida_util as vida_util_usada,
                        afvi.vida_util,
                        (select
                        afvi1.depreciacion_acum
                        from kaf.f_activo_fijo_dep_x_fecha_afv_kardex(kaf.f_get_fecha_gestion_ant('''||v_parametros.fecha_hasta||'''),'''||v_aux||''','''||v_parametros.id_activo_fijo||''') afvi1
                        where afvi1.id_activo_fijo_valor = afvi.id_activo_fijo_valor
                        and afvi.id_moneda = 1 ) as dep_acum_gest_ant,
                        afvi.depreciacion_per - (select
                                                afvi1.depreciacion_acum
                                                from kaf.f_activo_fijo_dep_x_fecha_afv_kardex(kaf.f_get_fecha_gestion_ant('''||v_parametros.fecha_hasta||'''),'''||v_aux||''','''||v_parametros.id_activo_fijo||''') afvi1
                                                where afvi1.id_activo_fijo_valor = afvi.id_activo_fijo_valor
                                                and afvi.id_moneda = 1) as act_dep_gest_ant,
                        afvi.depreciacion_per,
                        afvi.depreciacion_acum,
                        afvi.monto_vigente,
                        pw.nro_tramite as nro_pro_tramite,
                        tu.nombre_unidad as desc_uo_solic,
                        af.monto_compra_orig_100,
                        lu.nombre as ciudad,
                        af.documento as nro_factura,
                        upper(af.descripcion)::varchar as  descripcion,
                        fucal.desc_funcionario1,
                        movaf.vida_util as meses,
                        movaf.importe
                        from kaf.tmovimiento_af movaf
                        inner join kaf.tmovimiento mov
                        on mov.id_movimiento = movaf.id_movimiento
                        and mov.estado <> ''cancelado''
                        inner join kaf.tactivo_fijo af
                        on af.id_activo_fijo = movaf.id_activo_fijo
                        left join kaf.f_activo_fijo_dep_x_fecha_afv_kardex('''||v_parametros.fecha_hasta||''','''||v_aux||''','''||v_parametros.id_activo_fijo||''') afvi
                        on afvi.id_activo_fijo = af.id_activo_fijo
                        and afvi.id_moneda = 1
                        inner join kaf.tclasificacion cla
                        on cla.id_clasificacion = af.id_clasificacion
                        left join orga.vfuncionario fun
                        on fun.id_funcionario = coalesce(mov.id_funcionario_dest,coalesce(mov.id_funcionario,af.id_funcionario))
                        inner join param.tmoneda mon
                        on mon.id_moneda = af.id_moneda_orig
                        left join param.tcatalogo mdep
                        on mdep.id_catalogo = cla.id_cat_metodo_dep
                        left join param.tcatalogo proc
                        on proc.id_catalogo = mov.id_cat_movimiento
                        left join wf.tproceso_wf pw on pw.id_proceso_wf = af.id_proceso_wf
                        left join orga.tuo tu on tu.id_uo = af.id_uo
                        left join orga.toficina ofi on ofi.id_oficina = af.id_oficina
                        inner join param.tlugar lu on lu.id_lugar = ofi.id_lugar
                        inner join orga.vfuncionario_cargo_lugar fucal on fucal.id_funcionario = mov.id_responsable_depto
                        where movaf.id_activo_fijo = '||v_parametros.id_activo_fijo||'
                        and mov.fecha_mov between '''||v_parametros.fecha_desde ||''' and ''' ||v_parametros.fecha_hasta||''' ';

            if(pxp.f_existe_parametro(p_tabla,'af_estado_mov')) then
                if v_parametros.af_estado_mov <> 'todos' then
                    v_consulta = v_consulta || ' and mov.estado = ''finalizado'' ';
                end if;
            end if;

            if v_parametros.tipo_salida = 'grid' then
                --Definicion de la respuesta
                v_consulta:=v_consulta||' and '||v_parametros.filtro;
                v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
            else
                v_consulta = v_consulta||' order by mov.fecha_mov';
            end if;

            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'SKA_KARD_CONT'
     #DESCRIPCION:  Reporte de kardex de activo fijo
     #AUTOR:        RCM
     #FECHA:        27/07/2017
    ***********************************/

    elsif(p_transaccion='SKA_KARD_CONT') then

        begin

            v_aux = 'no';
            if(pxp.f_existe_parametro(p_tabla,'af_estado_mov')) then
                if v_parametros.af_estado_mov <> 'todos' then
                    v_aux = 'si';
                end if;
            end if;

            v_consulta = 'select
                        count(1) as total
                        from kaf.tmovimiento_af movaf
                        inner join kaf.tmovimiento mov
                        on mov.id_movimiento = movaf.id_movimiento
                        and mov.estado <> ''cancelado''
                        inner join kaf.tactivo_fijo af
                        on af.id_activo_fijo = movaf.id_activo_fijo
                        left join kaf.f_activo_fijo_dep_x_fecha_afv_kardex('''||v_parametros.fecha_hasta ||''','''||v_aux||''','''||v_parametros.id_activo_fijo||''') afvi
                        on afvi.id_activo_fijo = af.id_activo_fijo
                        and afvi.id_moneda = 1
                        inner join kaf.tclasificacion cla
                        on cla.id_clasificacion = af.id_clasificacion
                        left join orga.vfuncionario fun
                        on fun.id_funcionario = coalesce(mov.id_funcionario_dest,coalesce(mov.id_funcionario,af.id_funcionario))
                        inner join param.tmoneda mon
                        on mon.id_moneda = af.id_moneda_orig
                        left join param.tcatalogo mdep
                        on mdep.id_catalogo = cla.id_cat_metodo_dep
                        left join param.tcatalogo proc
                        on proc.id_catalogo = mov.id_cat_movimiento
                        left join wf.tproceso_wf pw on pw.id_proceso_wf = af.id_proceso_wf
                        left join orga.tuo tu on tu.id_uo = af.id_uo
					    left join orga.toficina ofi on ofi.id_oficina = af.id_oficina                        
                        inner join param.tlugar lu on lu.id_lugar = ofi.id_lugar
						inner join orga.vfuncionario_cargo_lugar fucal on fucal.id_funcionario = mov.id_responsable_depto                        
                        where movaf.id_activo_fijo = '||v_parametros.id_activo_fijo||'
                        and mov.fecha_mov between '''||v_parametros.fecha_desde ||'''and ''' ||v_parametros.fecha_hasta||''' ';

            if(pxp.f_existe_parametro(p_tabla,'af_estado_mov')) then
                if v_parametros.af_estado_mov <> 'todos' then
                    v_consulta = v_consulta || ' and mov.estado = ''finalizado'' ';
                end if;
            end if;

            if v_parametros.tipo_salida = 'grid' then
                --Definicion de la respuesta
                v_consulta:=v_consulta||' and '||v_parametros.filtro;
            end if;


            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'SKA_GRALAF_SEL'
     #DESCRIPCION:  Reporte Gral de activos fijos con el filtro general
     #AUTOR:        RCM
     #FECHA:        27/07/2017
    ***********************************/

    elsif(p_transaccion='SKA_GRALAF_SEL') then

        begin
            --Creacion de tabla temporal de los actios fijos a filtrar
            create temp table tt_af_filtro (
                id_activo_fijo integer
            ) on commit drop;

            v_consulta = 'insert into tt_af_filtro
                        select afij.id_activo_fijo
                        from kaf.tactivo_fijo afij
                        inner join kaf.tclasificacion cla
                        on cla.id_clasificacion = afij.id_clasificacion
                        where '||v_parametros.filtro;

            execute(v_consulta);
            v_consulta='';

            v_aux = 'no';
            if(pxp.f_existe_parametro(p_tabla,'af_estado_mov')) then
                if v_parametros.af_estado_mov <> 'todos' then
                    v_aux = 'si';
                end if;
            end if;

            if v_parametros.reporte = 'rep.sasig' then

                v_consulta = 'select
                            afij.codigo,
                            afij.denominacion,
                            afij.descripcion,
                            afvi.fecha_ini_dep,
                            --afij.fecha_ini_dep,
                            --afij.monto_compra_orig_100,
                            --afij.monto_compra_orig,
                            afij.ubicacion,
                            fun.desc_funcionario2 as responsable,
                            coalesce(afvi.monto_vigente_orig_100,coalesce(afvi.monto_vigente_orig,param.f_convertir_moneda(afij.id_moneda_orig, '||v_parametros.id_moneda||',afij.monto_compra_orig_100,'''||now()||'''::date,''O'',2))) as monto_vigente_orig_100,
                            coalesce(afvi.monto_vigente_orig,param.f_convertir_moneda(afij.id_moneda_orig, '||v_parametros.id_moneda||',afij.monto_compra_orig_100,'''||now()||'''::date,''O'',2)) as monto_vigente_orig,
                            coalesce(afvi.monto_vigente_ant,0) as monto_vigente_ant,
                            coalesce(afvi.monto_actualiz - afvi.monto_vigente_ant,0) as actualiz_monto_vigente,
                            coalesce(afvi.monto_actualiz,0) as monto_actualiz,
                            coalesce(afvi.vida_util_orig - afvi.vida_util,0) as vida_util_usada,
                            coalesce(afvi.vida_util,afij.vida_util_original) as vida_util,
                            coalesce((select
                            afvi1.depreciacion_acum
                            from kaf.f_activo_fijo_dep_x_fecha_afv(kaf.f_get_fecha_gestion_ant('''||now()||'''::date),'''||v_aux||''') afvi1
                            where afvi1.id_activo_fijo_valor = afvi.id_activo_fijo_valor
                            and afvi.id_moneda = '||v_parametros.id_moneda||' ),0) as dep_acum_gest_ant,
                            coalesce(afvi.depreciacion_per - (select
                                                    afvi1.depreciacion_acum
                                                    from kaf.f_activo_fijo_dep_x_fecha_afv(kaf.f_get_fecha_gestion_ant('''||now()||'''::date),'''||v_aux||''') afvi1
                                                    where afvi1.id_activo_fijo_valor = afvi.id_activo_fijo_valor
                                                    and afvi.id_moneda = '||v_parametros.id_moneda||'),0) as act_dep_gest_ant,
                            coalesce(afvi.depreciacion_per,0) as depreciacion_per,
                            coalesce(afvi.depreciacion_acum,0) as depreciacion_acum,
                            coalesce(afvi.monto_vigente,param.f_convertir_moneda(afij.id_moneda_orig, '||v_parametros.id_moneda||',afij.monto_compra_orig,'''||now()||'''::date,''O'',2)) as monto_vigente
                            from kaf.tactivo_fijo afij
                            inner join kaf.tclasificacion cla
                            on cla.id_clasificacion = afij.id_clasificacion
                            left join orga.vfuncionario fun
                            on fun.id_funcionario = afij.id_funcionario
                            left join kaf.f_activo_fijo_dep_x_fecha_afv('''||now()||'''::date,'''||v_aux||''') afvi
                            on afvi.id_activo_fijo = afij.id_activo_fijo
                            and afvi.id_moneda = '||v_parametros.id_moneda||'
                            where afij.id_activo_fijo in (select id_activo_fijo
                                                        from tt_af_filtro)
                            and afij.en_deposito = ''si''
                            ';


            elsif v_parametros.reporte = 'rep.asig' then

                v_consulta = 'select
                            afij.codigo,
                            afij.denominacion,
                            afij.descripcion,
                            afvi.fecha_ini_dep,
                            --afij.fecha_ini_dep,
                            --afij.monto_compra_orig_100,
                            --afij.monto_compra_orig,
                            afij.ubicacion,
                            fun.desc_funcionario2 as responsable,
                            coalesce(afvi.monto_vigente_orig_100,coalesce(afvi.monto_vigente_orig,param.f_convertir_moneda(afij.id_moneda_orig, '||v_parametros.id_moneda||',afij.monto_compra_orig_100,'''||now()||'''::date,''O'',2))) as monto_vigente_orig_100,
                            coalesce(afvi.monto_vigente_orig,param.f_convertir_moneda(afij.id_moneda_orig, '||v_parametros.id_moneda||',afij.monto_compra_orig_100,'''||now()||'''::date,''O'',2)) as monto_vigente_orig,
                            coalesce(afvi.monto_vigente_ant,0) as monto_vigente_ant,
                            coalesce(afvi.monto_actualiz - afvi.monto_vigente_ant,0) as actualiz_monto_vigente,
                            coalesce(afvi.monto_actualiz,0) as monto_actualiz,
                            coalesce(afvi.vida_util_orig - afvi.vida_util,0) as vida_util_usada,
                            coalesce(afvi.vida_util,afij.vida_util_original) as vida_util,
                            coalesce((select
                            afvi1.depreciacion_acum
                            from kaf.f_activo_fijo_dep_x_fecha_afv(kaf.f_get_fecha_gestion_ant('''||now()||'''::date),'''||v_aux||''') afvi1
                            where afvi1.id_activo_fijo_valor = afvi.id_activo_fijo_valor
                            and afvi.id_moneda = '||v_parametros.id_moneda||' ),0) as dep_acum_gest_ant,
                            coalesce(afvi.depreciacion_per - (select
                                                    afvi1.depreciacion_acum
                                                    from kaf.f_activo_fijo_dep_x_fecha_afv(kaf.f_get_fecha_gestion_ant('''||now()||'''::date),'''||v_aux||''') afvi1
                                                    where afvi1.id_activo_fijo_valor = afvi.id_activo_fijo_valor
                                                    and afvi.id_moneda = '||v_parametros.id_moneda||'),0) as act_dep_gest_ant,
                            coalesce(afvi.depreciacion_per,0) as depreciacion_per,
                            coalesce(afvi.depreciacion_acum,0) as depreciacion_acum,
                            coalesce(afvi.monto_vigente,param.f_convertir_moneda(afij.id_moneda_orig, '||v_parametros.id_moneda||',afij.monto_compra_orig,'''||now()||'''::date,''O'',2)) as monto_vigente
                            from kaf.tactivo_fijo afij
                            inner join kaf.tclasificacion cla
                            on cla.id_clasificacion = afij.id_clasificacion
                            left join orga.vfuncionario fun
                            on fun.id_funcionario = afij.id_funcionario
                            left join kaf.f_activo_fijo_dep_x_fecha_afv('''||now()||'''::date,'''||v_aux||''') afvi
                            on afvi.id_activo_fijo = afij.id_activo_fijo
                            and afvi.id_moneda = '||v_parametros.id_moneda||'
                            where afij.id_activo_fijo in (select id_activo_fijo
                                                        from tt_af_filtro)
                            and afij.en_deposito = ''no''
                            ';
            else
                raise exception 'Reporte desconocido';
            end if;

            --Si la consulta es para un grid, aumenta los parametros para la páginación
            if v_parametros.tipo_salida = 'grid' then
                --Definicion de la respuesta
                v_consulta:=v_consulta||' and '||v_parametros.filtro;
                v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
            else
                v_consulta:=v_consulta||' limit 2000';
            end if;

            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'SKA_GRALAF_CONT'
     #DESCRIPCION:  Reporte de kardex de activo fijo
     #AUTOR:        RCM
     #FECHA:        27/07/2017
    ***********************************/

    elsif(p_transaccion='SKA_GRALAF_CONT') then

        begin

            --Creacion de tabla temporal de los actios fijos a filtrar
            create temp table tt_af_filtro (
                id_activo_fijo integer
            ) on commit drop;

            v_consulta = 'insert into tt_af_filtro
                        select afij.id_activo_fijo
                        from kaf.tactivo_fijo afij
                        inner join kaf.tclasificacion cla
                        on cla.id_clasificacion = afij.id_clasificacion
                        where '||v_parametros.filtro;

            execute(v_consulta);
            v_consulta='';

            v_aux = 'no';
            if(pxp.f_existe_parametro(p_tabla,'af_estado_mov')) then
                if v_parametros.af_estado_mov <> 'todos' then
                    v_aux = 'si';
                end if;
            end if;

            if v_parametros.reporte = 'rep.sasig' then

                v_consulta = 'select
                            count(1) as total
                            from kaf.tactivo_fijo afij
                            inner join kaf.tclasificacion cla
                            on cla.id_clasificacion = afij.id_clasificacion
                            left join orga.vfuncionario fun
                            on fun.id_funcionario = afij.id_funcionario
                            left join kaf.f_activo_fijo_dep_x_fecha_afv('''||now()||'''::date,'''||v_aux||''') afvi
                            on afvi.id_activo_fijo = afij.id_activo_fijo
                            and afvi.id_moneda = '||v_parametros.id_moneda||'
                            where afij.id_activo_fijo in (select id_activo_fijo
                                                        from tt_af_filtro)
                            and afij.en_deposito = ''si''
                            and ';

            elsif v_parametros.reporte = 'rep.asig' then
                 v_consulta = 'select
                            count(1) as total
                            from kaf.tactivo_fijo afij
                            inner join kaf.tclasificacion cla
                            on cla.id_clasificacion = afij.id_clasificacion
                            left join orga.vfuncionario fun
                            on fun.id_funcionario = afij.id_funcionario
                            left join kaf.f_activo_fijo_dep_x_fecha_afv('''||now()||'''::date,'''||v_aux||''') afvi
                            on afvi.id_activo_fijo = afij.id_activo_fijo
                            and afvi.id_moneda = '||v_parametros.id_moneda||'
                            where afij.id_activo_fijo in (select id_activo_fijo
                                                        from tt_af_filtro)
                            and afij.en_deposito = ''no''
                            and ';
            else
                raise exception 'Reporte desconocido';
            end if;

            --Se aumenta el filtro para el listado
            v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'SKA_DEPDEPTO_SEL'
     #DESCRIPCION:  Reporte de kardex de activo fijo
     #AUTOR:        RCM
     #FECHA:        15/09/2017
    ***********************************/

    elsif(p_transaccion='SKA_DEPDEPTO_SEL') then

        begin

            v_consulta = 'select
                        mov.id_depto, dep.codigo || '' - '' || dep.nombre as desc_depto, max(mov.fecha_hasta) as fecha_max_dep
                        from kaf.tmovimiento mov
                        inner join param.tcatalogo cat
                        on cat.id_catalogo = mov.id_cat_movimiento
                        inner join param.tdepto dep
                        on dep.id_depto = mov.id_depto
                        where cat.codigo = ''deprec''
                        --and mov.estado = ''finalizado''
                        ';

            if coalesce(v_parametros.deptos,'') <> '' and coalesce(v_parametros.deptos,'') <> '%' then
                v_consulta = v_consulta || ' and mov.id_depto in ('||v_parametros.deptos||') ';
            end if;


            v_consulta = v_consulta || ' group by mov.id_depto, dep.codigo, dep.nombre';

            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'SKA_RASIG_SEL'
     #DESCRIPCION:  Reporte Gral de activos fijos con el filtro general
     #AUTOR:        RCM
     #FECHA:        05/10/2017
    ***********************************/

    elsif(p_transaccion='SKA_RASIG_SEL') then

        begin

            select tl.nombre
            into v_lugar
            from param.tlugar tl
            where id_lugar = v_parametros.id_lugar::integer;

            if (v_lugar is null) then
                v_lugar = '';
            end if;
            --raise exception 'v_parametros.filtro: %',v_parametros.filtro;
            --Creacion de tabla temporal de los actios fijos a filtrar
            create temp table tt_af_filtro (
                id_activo_fijo integer
            ) on commit drop;

            v_consulta = 'insert into tt_af_filtro
                        select afij.id_activo_fijo
                        from kaf.tactivo_fijo afij
                        inner join kaf.tclasificacion cla
                        on cla.id_clasificacion = afij.id_clasificacion
                        where '||v_parametros.filtro;

            execute(v_consulta);

            if(v_parametros.tipo = 'lug_fun')then
                v_filtro = ' afij.id_funcionario in (SELECT tf.id_funcionario
                                                     FROM orga.vfuncionario_cargo_lugar tf
                                                     where  (tf.fecha_finalizacion > now()::date or tf.fecha_finalizacion is null) and tf.id_oficina in (select id_oficina from orga.toficina where id_lugar = '||v_parametros.id_lugar||'))
                            and afij.en_deposito = ''no'' and afij.id_depto = '||v_parametros.id_depto;
            else
                v_filtro = ' afij.id_activo_fijo in (select id_activo_fijo
                                                        from tt_af_filtro)
                            and afij.en_deposito = ''no''';
            end if;

            --Consulta
            v_consulta = 'select
                            afij.codigo,
                            '''||v_lugar||'''::varchar as lugar,
                            cla.nombre as desc_clasificacion,
                            afij.denominacion,
                            (case when '''||v_parametros.columna||''' = ''desc'' then afij.descripcion::varchar
                                  when '''||v_parametros.columna||''' = ''nombre'' then afij.denominacion::varchar
                                  else  afij.descripcion::varchar
                                  end) as descripcion,
                            afij.estado,
                            afij.observaciones,
                            ''''::varchar as ubicacion,
                            afij.fecha_asignacion,
                            ofi.nombre,
                            fun.desc_funcionario2 as responsable,
                            orga.f_get_cargo_x_funcionario_str(afij.id_funcionario,now()::date,''oficial'') as cargo,
                            dep.codigo ||'' - ''||dep.nombre,
                            case when '''||v_parametros.columna||''' = '''' then ''ambos''::varchar else '''||v_parametros.columna||'''::varchar end as tipo_columna,
                            cat.descripcion as cat_desc,
                            afij.ubicacion as ubi_fisica_ante,
                            afij.prestamo
                            from kaf.tactivo_fijo afij
                            inner join kaf.tclasificacion cla on cla.id_clasificacion = afij.id_clasificacion
                            left join orga.vfuncionario fun on fun.id_funcionario = afij.id_funcionario
                            inner join orga.toficina ofi on ofi.id_oficina = afij.id_oficina
                            inner join param.tdepto dep on dep.id_depto = afij.id_depto
                            left join param.tcatalogo cat on cat.id_catalogo=afij.id_cat_estado_fun
                            where '||v_filtro;

            v_consulta:=v_consulta||' order by fun.desc_funcionario2, ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion;
            --raise EXCEPTION 'v_consulta: %', v_consulta;
            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'SKA_RASIG_CONT'
     #DESCRIPCION:  Reporte Gral de activos fijos con el filtro general
     #AUTOR:        RCM
     #FECHA:        05/10/2017
    ***********************************/

    elsif(p_transaccion='SKA_RASIG_CONT') then

        begin

            select tl.nombre
            into v_lugar
            from param.tlugar tl
            where id_lugar = v_parametros.id_lugar::integer;

            if (v_lugar is null) then
                v_lugar = '';
            end if;

            --Creacion de tabla temporal de los actios fijos a filtrar
            create temp table tt_af_filtro (
                id_activo_fijo integer
            ) on commit drop;

            v_consulta = 'insert into tt_af_filtro
                        select afij.id_activo_fijo
                        from kaf.tactivo_fijo afij
                        inner join kaf.tclasificacion cla
                        on cla.id_clasificacion = afij.id_clasificacion
                        where '||v_parametros.filtro;

            execute(v_consulta);

            if(v_parametros.tipo = 'lug_fun')then
                v_filtro = ' afij.id_funcionario in (SELECT tf.id_funcionario
                                                     FROM orga.vfuncionario_cargo_lugar tf
                                                     where (tf.fecha_finalizacion > now()::date or tf.fecha_finalizacion is null) and tf.id_oficina in (select id_oficina from orga.toficina where id_lugar = '||v_parametros.id_lugar||'))
                            and afij.en_deposito = ''no''and afij.id_depto = '||v_parametros.id_depto;
            else
                v_filtro = ' afij.id_activo_fijo in (select id_activo_fijo
                                                        from tt_af_filtro)
                            and afij.en_deposito = ''no''';
            end if;

            --Consulta
            v_consulta = 'select
                            count(1) as total
                            from kaf.tactivo_fijo afij
                            inner join kaf.tclasificacion cla
                            on cla.id_clasificacion = afij.id_clasificacion
                            left join orga.vfuncionario fun
                            on fun.id_funcionario = afij.id_funcionario
                            inner join orga.toficina ofi
                            on ofi.id_oficina = afij.id_oficina
                            inner join param.tdepto dep
                            on dep.id_depto = afij.id_depto
                            where '||v_filtro;

            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'SKA_RSINASIG_SEL'
     #DESCRIPCION:  Reporte de Activos Fijos sin Asignar
     #AUTOR:        RCM
     #FECHA:        05/10/2017
    ***********************************/

    elsif(p_transaccion='SKA_RSINASIG_SEL') then

        begin

            --Creacion de tabla temporal de los actios fijos a filtrar
            create temp table tt_af_filtro (
                id_activo_fijo integer
            ) on commit drop;

            v_consulta = 'insert into tt_af_filtro
                        select afij.id_activo_fijo
                        from kaf.tactivo_fijo afij
                        inner join kaf.tclasificacion cla
                        on cla.id_clasificacion = afij.id_clasificacion
                        where '||v_parametros.filtro;

            execute(v_consulta);

            --Consulta
            v_consulta = 'select
                            afij.codigo,
                            cla.nombre as desc_clasificacion,
                            afij.denominacion,
                            afij.descripcion,
                            afij.estado,
                            afij.observaciones,
                            afij.ubicacion,
                            afij.fecha_asignacion,
                            ofi.nombre,
                            fun.desc_funcionario2 as responsable
                            from kaf.tactivo_fijo afij
                            inner join kaf.tclasificacion cla
                            on cla.id_clasificacion = afij.id_clasificacion
                            left join orga.vfuncionario fun
                            on fun.id_funcionario = afij.id_funcionario
                            inner join orga.toficina ofi
                            on ofi.id_oficina = afij.id_oficina
                            where afij.id_activo_fijo in (select id_activo_fijo
                                                        from tt_af_filtro)
                            and afij.en_deposito = ''si''
                            ';
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'SKA_RSINASIG_CONT'
     #DESCRIPCION:  Reporte de Activos Fijos en Depósito
     #AUTOR:        RCM
     #FECHA:        05/10/2017
    ***********************************/

    elsif(p_transaccion='SKA_RSINASIG_CONT') then

        begin

            --Creacion de tabla temporal de los actios fijos a filtrar
            create temp table tt_af_filtro (
                id_activo_fijo integer
            ) on commit drop;

            v_consulta = 'insert into tt_af_filtro
                        select afij.id_activo_fijo
                        from kaf.tactivo_fijo afij
                        inner join kaf.tclasificacion cla
                        on cla.id_clasificacion = afij.id_clasificacion
                        where '||v_parametros.filtro;

            execute(v_consulta);

            --Consulta
            v_consulta = 'select
                            count(1) as total
                            from kaf.tactivo_fijo afij
                            inner join kaf.tclasificacion cla
                            on cla.id_clasificacion = afij.id_clasificacion
                            left join orga.vfuncionario fun
                            on fun.id_funcionario = afij.id_funcionario
                            inner join orga.toficina ofi
                            on ofi.id_oficina = afij.id_oficina
                            where afij.id_activo_fijo in (select id_activo_fijo
                                                        from tt_af_filtro)
                            and afij.en_deposito = ''si''
                            ';

            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'SKA_RENDEP_SEL'
     #DESCRIPCION:  Reporte activos fijos asignados en Depósito
     #AUTOR:        RCM
     #FECHA:        05/10/2017
    ***********************************/

    elsif(p_transaccion='SKA_RENDEP_SEL') then

        begin

            --Creacion de tabla temporal de los actios fijos a filtrar
            create temp table tt_af_filtro (
                id_activo_fijo integer
            ) on commit drop;

            v_consulta = 'insert into tt_af_filtro
                        select afij.id_activo_fijo
                        from kaf.tactivo_fijo afij
                        inner join kaf.tclasificacion cla
                        on cla.id_clasificacion = afij.id_clasificacion
                        where '||v_parametros.filtro;

            execute(v_consulta);

            --Consulta
            v_consulta = 'select
                            afij.codigo,
                            cla.nombre as desc_clasificacion,
                            afij.denominacion,
                            afij.descripcion,
                            afij.estado,
                            afij.observaciones,
                            afij.ubicacion,
                            afij.fecha_asignacion,
                            ofi.nombre,
                            fun.desc_funcionario2 as responsable
                            from kaf.tactivo_fijo afij
                            inner join kaf.tclasificacion cla
                            on cla.id_clasificacion = afij.id_clasificacion
                            left join orga.vfuncionario fun
                            on fun.id_funcionario = afij.id_funcionario
                            inner join orga.toficina ofi
                            on ofi.id_oficina = afij.id_oficina
                            where afij.id_activo_fijo in (select id_activo_fijo
                                                        from tt_af_filtro)
                            and afij.en_deposito = ''no''
                            ';

            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'SKA_RENDEP_CONT'
     #DESCRIPCION:  Reporte activos fijos asignados en Depósito
     #AUTOR:        RCM
     #FECHA:        05/10/2017
    ***********************************/

    elsif(p_transaccion='SKA_RENDEP_CONT') then

        begin

            --Creacion de tabla temporal de los actios fijos a filtrar
            create temp table tt_af_filtro (
                id_activo_fijo integer
            ) on commit drop;

            v_consulta = 'insert into tt_af_filtro
                        select afij.id_activo_fijo
                        from kaf.tactivo_fijo afij
                        inner join kaf.tclasificacion cla
                        on cla.id_clasificacion = afij.id_clasificacion
                        where '||v_parametros.filtro;

            execute(v_consulta);

            --Consulta
            v_consulta = 'select
                            count(1) as total
                            from kaf.tactivo_fijo afij
                            inner join kaf.tclasificacion cla
                            on cla.id_clasificacion = afij.id_clasificacion
                            left join orga.vfuncionario fun
                            on fun.id_funcionario = afij.id_funcionario
                            inner join orga.toficina ofi
                            on ofi.id_oficina = afij.id_oficina
                            where afij.id_activo_fijo in (select id_activo_fijo
                                                        from tt_af_filtro)
                            and afij.en_deposito = ''no''
                            ';

            --Devuelve la respuesta
            return v_consulta;

        end;


    /*********************************
     #TRANSACCION:  'SKA_RDETDEP_SEL'
     #DESCRIPCION:  Reporte del Detalle de depreciación
     #AUTOR:        RCM
     #FECHA:        16/10/2017
    ***********************************/

    elsif(p_transaccion='SKA_RDETDEP_SEL') then

        begin

            --Creacion de tabla temporal de los actios fijos a filtrar
            create temp table tt_af_filtro (
                id_activo_fijo integer
            ) on commit drop;

            v_consulta = 'insert into tt_af_filtro
                        select afij.id_activo_fijo
                        from kaf.tactivo_fijo afij
                        inner join kaf.tclasificacion cla
                        on cla.id_clasificacion = afij.id_clasificacion
                        where '||v_parametros.filtro;

            execute(v_consulta);


            --Consulta
            v_consulta:=' SELECT
                              daf.id_moneda_dep,
                              mod.descripcion as desc_moneda,
                              daf.gestion_final::INTEGER,
                              daf.tipo,
                              cr.nombre_raiz,
                              daf.fecha_ini_dep,
                              daf.id_movimiento,
                              daf.id_movimiento_af,
                              daf.id_activo_fijo_valor,
                              daf.id_activo_fijo,
                              daf.codigo,
                              daf.id_clasificacion,
                              daf.descripcion,
                              daf.monto_vigente_orig,
                              daf.monto_vigente_inicial,
                              daf.monto_vigente_final,
                              daf.monto_actualiz_inicial,
                              daf.monto_actualiz_final,
                              daf.depreciacion_acum_inicial,
                              daf.depreciacion_acum_final,
                              daf.aitb_activo,
                              daf.aitb_depreciacion_acumulada,
                              daf.vida_util_orig,
                              daf.vida_util_inicial,
                              daf.vida_util_final,
                              daf.vida_util_orig - daf.vida_util_final as vida_util_trans,
                              cr.codigo_raiz,
                              cr.id_claificacion_raiz,
                              daf.depreciacion_per_final,
                              daf.depreciacion_per_actualiz_final

                          FROM kaf.vdetalle_depreciacion_activo daf
                          INNER  JOIN kaf.vclaificacion_raiz cr on cr.id_clasificacion = daf.id_clasificacion
                          INNER JOIN kaf.tmoneda_dep mod on mod.id_moneda_dep = daf.id_moneda_dep
                          WHERE daf.id_activo_fijo in (select id_activo_fijo
                                                        from tt_af_filtro)
                        and daf.id_moneda = ' ||v_parametros.id_moneda||'
                          ORDER BY
                              daf.id_moneda_dep,
                              daf.gestion_final,
                              daf.tipo,
                              cr.id_claificacion_raiz,
                              daf.id_clasificacion,
                              id_activo_fijo_valor ,
                              daf.fecha_ini_dep';


            v_consulta:=v_consulta||' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'SKA_RDETDEP_CONT'
     #DESCRIPCION:  Reporte Detalle de depreciación
     #AUTOR:        RCM
     #FECHA:        16/10/2017
    ***********************************/

    elsif(p_transaccion='SKA_RDETDEP_CONT') then

        begin

            --Creacion de tabla temporal de los actios fijos a filtrar
            create temp table tt_af_filtro (
                id_activo_fijo integer
            ) on commit drop;

            v_consulta = 'insert into tt_af_filtro
                        select afij.id_activo_fijo
                        from kaf.tactivo_fijo afij
                        inner join kaf.tclasificacion cla
                        on cla.id_clasificacion = afij.id_clasificacion
                        where '||v_parametros.filtro;

            execute(v_consulta);

            --Consulta
            v_consulta:=' SELECT count(1) as total
                          FROM kaf.vdetalle_depreciacion_activo daf
                          INNER  JOIN kaf.vclaificacion_raiz cr on cr.id_clasificacion = daf.id_clasificacion
                          INNER JOIN kaf.tmoneda_dep mod on mod.id_moneda_dep = daf.id_moneda_dep
                          WHERE daf.id_activo_fijo in (select id_activo_fijo
                                                        from tt_af_filtro)
                        and daf.id_moneda = ' ||v_parametros.id_moneda;

            --Devuelve la respuesta
            return v_consulta;

        end;


    /*********************************
     #TRANSACCION:  'SKA_RDEPREC_SEL'
     #DESCRIPCION:  Reporte del Detalle de depreciación
     #AUTOR:        RCM
     #FECHA:        18/10/2017
    ***********************************/

    elsif(p_transaccion='SKA_RDEPREC_SEL') then

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
            insert into tt_detalle_depreciacion(
            id_activo_fijo_valor,codigo, denominacion ,fecha_ini_dep,monto_vigente_orig_100,monto_vigente_orig,inc_actualiz,
            monto_actualiz,vida_util_orig,vida_util,
            depreciacion_per,depreciacion_acum,monto_vigente,codigo_padre,denominacion_padre,tipo,tipo_cambio_fin,id_moneda_act,
            id_activo_fijo_valor_original
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
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.fecha_ini_dep
                else (select fecha_ini_dep from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end as fecha_ini_dep,
            --coalesce(afv.monto_vigente_orig_100,afv.monto_vigente_orig),
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig_100
                else (select monto_vigente_orig_100 from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end as monto_vigente_orig_100,
--            afv.monto_vigente_orig,
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
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
            afv.vida_util_orig, mdep.vida_util,
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
            afv.id_activo_fijo_valor_original
            from kaf.tmovimiento_af_dep mdep
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


            insert into tt_detalle_depreciacion(
            id_activo_fijo_valor,codigo, denominacion ,fecha_ini_dep,monto_vigente_orig_100,monto_vigente_orig,inc_actualiz,
            monto_actualiz,vida_util_orig,vida_util,
            depreciacion_per,depreciacion_acum,monto_vigente,codigo_padre,denominacion_padre,tipo,tipo_cambio_fin,id_moneda_act,
            id_activo_fijo_valor_original
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
            afv.vida_util_orig, mdep.vida_util,
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
                                                from tt_detalle_depreciacion)
            --and afv.id_activo_fijo_valor not in (select id_activo_fijo_valor from kaf.tactivo_fijo_valores where id_activo_fijo_valor_original = afv.id_activo_fijo_valor /*and tipo = 'alta'*/ )
            and date_trunc('month',mdep.fecha) <> date_trunc('month',v_parametros.fecha_hasta::date)
            and date_trunc('month',mdep.fecha) < date_trunc('month',v_parametros.fecha_hasta::date) --between date_trunc('month',('01-01-'||extract(year from v_parametros.fecha_hasta::date)::varchar)::date) and date_trunc('month',v_parametros.fecha_hasta::date)
            and date_trunc('month',mdep.fecha) = (select max(fecha)
                                                    from kaf.tmovimiento_af_dep
                                                    where id_activo_fijo_valor = afv.id_activo_fijo_valor
                                                    and id_moneda_dep = mdep.id_moneda_dep
                                                    and date_trunc('month',fecha) <> date_trunc('month',v_parametros.fecha_hasta::date)
                                                    and date_trunc('month',fecha) < date_trunc('month',v_parametros.fecha_hasta::date) --between date_trunc('month',('01-01-'||extract(year from v_parametros.fecha_hasta)::varchar)::date) and date_trunc('month',v_parametros.fecha_hasta)
                                                )
            and mdep.id_moneda_dep = coalesce(v_parametros.id_moneda,1)
            and af.id_activo_fijo in (select id_activo_fijo from tt_af_filtro)
            and afv.id_activo_fijo_valor not in (select id_activo_fijo_valor
                                                from tt_detalle_depreciacion)
            and af.estado <> 'eliminado'
            and af.fecha_baja > v_parametros.fecha_hasta::date;

            --------------------------------
            --------------------------------
            insert into tt_detalle_depreciacion(
            id_activo_fijo_valor,codigo, denominacion ,fecha_ini_dep,monto_vigente_orig_100,monto_vigente_orig,inc_actualiz,
            monto_actualiz,vida_util_orig,vida_util,
            depreciacion_per,depreciacion_acum,monto_vigente,codigo_padre,denominacion_padre,tipo,tipo_cambio_fin,id_moneda_act,
            id_activo_fijo_valor_original
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
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.fecha_ini_dep
                else (select fecha_ini_dep from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end as fecha_ini_dep,
            --coalesce(afv.monto_vigente_orig_100,afv.monto_vigente_orig),
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig_100
                else (select monto_vigente_orig_100 from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
            end as monto_vigente_orig_100,
--            afv.monto_vigente_orig,
            case coalesce(afv.id_activo_fijo_valor_original,0)
                when 0 then afv.monto_vigente_orig
                else (select monto_vigente_orig from kaf.tactivo_fijo_valores where id_activo_fijo_valor = afv.id_activo_fijo_valor_original)
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
            afv.vida_util_orig, mdep.vida_util,
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
            afv.id_activo_fijo_valor_original
            from kaf.tmovimiento_af_dep mdep
            inner join kaf.tactivo_fijo_valores afv
            on afv.id_activo_fijo_valor = mdep.id_activo_fijo_valor
            inner join kaf.tactivo_fijo af
            on af.id_activo_fijo = afv.id_activo_fijo
            inner join kaf.tmoneda_dep mon
            on mon.id_moneda =  afv.id_moneda_dep
            where af.estado in ('baja','retiro')
            and mdep.fecha >= '01-01-2019'
            and mdep.fecha = (select max(fecha) from kaf.tmovimiento_af_dep mdep1
                                where mdep1.id_activo_fijo_valor = afv.id_activo_fijo_valor
                                and fecha between ('01-01-'||extract(year from mdep.fecha))::date and v_parametros.fecha_hasta::date)

            and mdep.id_moneda_dep = coalesce(v_parametros.id_moneda,1)
            and af.id_activo_fijo in (select id_activo_fijo from tt_af_filtro)
                                                            --and afv.codigo not like '%-G%'
            and afv.id_activo_fijo_valor not in (select id_activo_fijo_valor
                                                from tt_detalle_depreciacion);
            --------------------------------
            --------------------------------



            ----
            /*insert into tt_detalle_depreciacion(
            id_activo_fijo_valor,codigo, denominacion ,fecha_ini_dep,monto_vigente_orig_100,monto_vigente_orig,inc_actualiz,
            monto_actualiz,vida_util_orig,vida_util,
            depreciacion_per,depreciacion_acum,monto_vigente,codigo_padre,denominacion_padre
            )
            select
            afv.id_activo_fijo_valor,
            afv.codigo,
            af.denominacion,
            afv.fecha_ini_dep,
            coalesce(afv.monto_vigente_orig_100,afv.monto_vigente_orig),
            afv.monto_vigente_orig,
            (coalesce(mdep.monto_actualiz,0) - coalesce(afv.monto_vigente_orig,0)) as inc_actualiz,
            mdep.monto_actualiz,
            afv.vida_util_orig, afv.vida_util,
            /*coalesce((select depreciacion_acum
                    from kaf.tmovimiento_af_dep
                    where id_activo_fijo_valor = afv.id_activo_fijo_valor
                    and id_moneda_dep = mdep.id_moneda_dep
                    and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_parametros.fecha_hasta)::integer -1 )::date)),0) as depreciacion_acum_gest_ant,
            coalesce((select depreciacion_acum_actualiz
                    from kaf.tmovimiento_af_dep
                    where id_activo_fijo_valor = afv.id_activo_fijo_valor
                    and id_moneda_dep = mdep.id_moneda_dep
                    and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_parametros.fecha_hasta)::integer -1 )::date)),0) as depreciacion_acum_actualiz_gest_ant,*/
            mdep.depreciacion_per,
            mdep.depreciacion_acum,
            mdep.monto_vigente,
            substr(afv.codigo,1, position('.' in afv.codigo)-1) as codigo_padre,
            (select nombre from kaf.tclasificacion where codigo_completo_tmp = substr(afv.codigo,1, position('.' in afv.codigo)-1)) as denominacion_padre
            from kaf.tmovimiento_af_dep mdep
            inner join kaf.tactivo_fijo_valores afv
            on afv.id_activo_fijo_valor = mdep.id_activo_fijo_valor
            inner join kaf.tactivo_fijo af
            on af.id_activo_fijo = afv.id_activo_fijo
            where date_trunc('month',mdep.fecha) <> date_trunc('month',v_parametros.fecha_hasta)
            and date_trunc('month',mdep.fecha) < date_trunc('month',v_parametros.fecha_hasta) --between date_trunc('month',('01-01-'||extract(year from v_parametros.fecha_hasta)::varchar)::date) and date_trunc('month',v_parametros.fecha_hasta)
            and date_trunc('month',mdep.fecha) = (select max(fecha)
                                                    from kaf.tmovimiento_af_dep
                                                    where id_activo_fijo_valor = afv.id_activo_fijo_valor
                                                    and id_moneda_dep = mdep.id_moneda_dep
                                                    and date_trunc('month',fecha) <> date_trunc('month',v_parametros.fecha_hasta)
                                                    and date_trunc('month',fecha) < date_trunc('month',v_parametros.fecha_hasta) --between date_trunc('month',('01-01-'||extract(year from v_parametros.fecha_hasta)::varchar)::date) and date_trunc('month',v_parametros.fecha_hasta)
                                                )
            and mdep.id_moneda_dep = coalesce(v_parametros.id_moneda,1)
            and af.id_activo_fijo in (select id_activo_fijo from tt_af_filtro)
            and afv.id_activo_fijo_valor not in (select id_activo_fijo_valor
                                                from tt_detalle_depreciacion)
                                                and afv.codigo not like '%-G%'
            and af.estado <> 'eliminado';*/

            --Obtiene los datos de gestion anterior
            /*update tt_detalle_depreciacion set
            depreciacion_acum_gest_ant = coalesce((
                select depreciacion_acum
                from kaf.tmovimiento_af_dep
                where id_activo_fijo_valor = tt_detalle_depreciacion.id_activo_fijo_valor
                and id_moneda_dep = v_parametros.id_moneda
                and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_parametros.fecha_hasta)::integer -1 )::date)
            ),0),
            depreciacion_acum_actualiz_gest_ant = coalesce((
                select depreciacion_acum_actualiz
                from kaf.tmovimiento_af_dep
                where id_activo_fijo_valor = tt_detalle_depreciacion.id_activo_fijo_valor
                and id_moneda_dep = v_parametros.id_moneda
                and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_parametros.fecha_hasta)::integer -1 )::date)
            ),0);*/

            --Obtiene los datos de gestion anterior
            update tt_detalle_depreciacion set
            depreciacion_acum_gest_ant = coalesce((
                select depreciacion_acum
                from kaf.tmovimiento_af_dep
                where id_activo_fijo_valor = tt_detalle_depreciacion.id_activo_fijo_valor
                and id_moneda_dep = coalesce(v_parametros.id_moneda,1)
                and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_parametros.fecha_hasta::date)::integer -1 )::date)
            ),0),
            depreciacion_acum_actualiz_gest_ant = (((case when substr(tt_detalle_depreciacion.codigo,1,13) ='05.03.01.0001' then 
             v_ufv_mes_repo else tt_detalle_depreciacion.tipo_cambio_fin end/(param.f_get_tipo_cambio_v2(tt_detalle_depreciacion.id_moneda_act, coalesce(v_parametros.id_moneda,1), ('31/12/'||extract(year from v_parametros.fecha_hasta::date)::integer -1)::date, 'O'))))-1)*(coalesce((
                            select depreciacion_acum
                            from kaf.tmovimiento_af_dep
                            where id_activo_fijo_valor = tt_detalle_depreciacion.id_activo_fijo_valor
                            and id_moneda_dep = coalesce(v_parametros.id_moneda,1)
                            and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_parametros.fecha_hasta)::integer -1 )::date)
                        ),0));

            --Si la depreciación anterior es cero, busca la depreciación de su activo fijo valor original si es que tuviese
            update tt_detalle_depreciacion set
            depreciacion_acum_gest_ant = coalesce((
                select depreciacion_acum
                from kaf.tmovimiento_af_dep
                where id_activo_fijo_valor = tt_detalle_depreciacion.id_activo_fijo_valor_original
                and tipo = tt_detalle_depreciacion.tipo
                and id_moneda_dep = coalesce(v_parametros.id_moneda,1)
                and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_parametros.fecha_hasta::date)::integer -1 )::date)
            ),0),
            depreciacion_acum_actualiz_gest_ant = (((case when substr(tt_detalle_depreciacion.codigo,1,13) ='05.03.01.0001' then 
             v_ufv_mes_repo else tt_detalle_depreciacion.tipo_cambio_fin end/(param.f_get_tipo_cambio_v2(tt_detalle_depreciacion.id_moneda_act, coalesce(v_parametros.id_moneda,1), ('31/12/'||extract(year from v_parametros.fecha_hasta::date)::integer -1)::date, 'O'))))-1)*(coalesce((
                            select depreciacion_acum
                            from kaf.tmovimiento_af_dep
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


                /*coalesce((select depreciacion_acum
                    from kaf.tmovimiento_af_dep
                    where id_activo_fijo_valor = afv.id_activo_fijo_valor
                    and id_moneda_dep = mdep.id_moneda_dep
                    and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_parametros.fecha_hasta)::integer -1 )::date)),0) as depreciacion_acum_gest_ant,
            coalesce((select depreciacion_acum_actualiz
                    from kaf.tmovimiento_af_dep
                    where id_activo_fijo_valor = afv.id_activo_fijo_valor
                    and id_moneda_dep = mdep.id_moneda_dep
                    and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_parametros.fecha_hasta)::integer -1 )::date)),0) as depreciacion_acum_actualiz_gest_ant,*/


            --Creación de la tabla con la agrupación y totales
            create temp table tt_detalle_depreciacion_totales (
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
                        'clasif'
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
                        id_activo_fijo_valor_original
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
                        'detalle'
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
                        'total'
                        from tt_detalle_depreciacion;
                        
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
                        from kaf.f_depre_ges_ant(v_id_id_activo_fijo_valor,coalesce(v_parametros.id_moneda,1),v_fecha_actu,v_parametros.total_consol,v_parametros.af_deprec);    

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
                        from kaf.f_depre_ges_ant(v_id_id_activo_fijo_valor,coalesce(v_parametros.id_moneda,1),v_fecha_actu,v_parametros.total_consol,v_parametros.af_deprec);    
                    end if;                                              
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
                                            ac.inc_ac as inc_ac_acum,
                                            ac.color,
                                            case when ac.inc_ac is null then 
                                            (de.monto_actualiz - de.monto_vigente_orig)::numeric(18,2)
                                            else
                                            (de.monto_actualiz - ac.inc_ac - de.monto_vigente_orig)::numeric(18,2) end as val_acu_perido,
                                            0.00 as porce_depre    
                                            from tt_detalle_depreciacion_totales de 
                                            left join tt_actuli_acumulado ac on ac.code=de.codigo
                                            where tipo in '||v_where||'                       
                                            order by codigo, orden';
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

ALTER FUNCTION kaf.f_reportes_af (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
