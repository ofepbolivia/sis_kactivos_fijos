CREATE OR REPLACE FUNCTION kaf.ft_activo_fijo_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:   Sistema de Activos Fijos
 FUNCION:     kaf.ft_activo_fijo_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'kaf.tactivo_fijo'
 AUTOR:      (admin)
 FECHA:         29-10-2015 03:18:45
 COMENTARIOS: 
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION: 
 AUTOR:     
 FECHA:   
***************************************************************************/

DECLARE

  v_consulta        varchar;
  v_parametros      record;
  v_nombre_funcion    text;
  v_resp        varchar;
    v_lista_af      varchar;
    v_criterio_filtro varchar;
    v_clase_reporte   varchar;
  v_condicion     varchar = ''; 
    v_ges       varchar[];      
    v_tamano      integer;
    v_i         integer;
    v_n         integer=0;
    v_presu             record; 
    first       varchar='niv.codigo';
    v_filtro      varchar='';
    ord					text;
BEGIN

  v_nombre_funcion = 'kaf.ft_activo_fijo_sel';
    v_parametros = pxp.f_get_record(p_tabla);

  /*********************************    
  #TRANSACCION:  'SKA_AFIJ_SEL'
  #DESCRIPCION: Consulta de datos
  #AUTOR:   admin 
  #FECHA:   29-10-2015 03:18:45
  ***********************************/

  if(p_transaccion='SKA_AFIJ_SEL')then
            
      begin
        --Sentencia de la consulta
      v_consulta:='select
                            afij.id_activo_fijo,
                            afij.id_persona,
                            afij.cantidad_revaloriz,
                            coalesce(afij.foto,''./../../../uploaded_files/sis_kactivos_fijos/ActivoFijo/default.jpg'') as foto,
                            afij.id_proveedor,
                            afij.estado_reg,
                            afij.fecha_compra,
                            afij.monto_vigente,
                            afij.id_cat_estado_fun,
                            afij.ubicacion,
                            afij.vida_util,
                            afij.documento,
                            afij.observaciones,
                            afij.fecha_ult_dep,
                            afij.monto_rescate,
                            afij.denominacion,
                            afij.id_funcionario,
                            afij.id_deposito,
                            afij.monto_compra,
                            afij.id_moneda,
                            afij.depreciacion_mes,
                            afij.codigo,
                            afij.descripcion,
                            afij.id_moneda_orig,
                            afij.fecha_ini_dep,
                            afij.id_cat_estado_compra,
                            afij.depreciacion_per,
                            afij.vida_util_original,
                            afij.depreciacion_acum,
                            afij.estado,
                            afij.id_clasificacion,
                            afij.id_centro_costo,
                            afij.id_oficina,
                            afij.id_depto,
                            afij.id_usuario_reg,
                            afij.fecha_reg,
                            afij.usuario_ai,
                            afij.id_usuario_ai,
                            afij.id_usuario_mod,
                            afij.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            per.nombre_completo2 as persona,
                            pro.desc_proveedor,
                            cat1.descripcion as estado_fun,
                            cat2.descripcion as estado_compra,
                            cla.codigo_completo_tmp || '' '' || cla.nombre as clasificacion,
                            cc.codigo_cc as centro_costo,
                            ofi.codigo || '' '' || ofi.nombre as oficina,
                            dpto.codigo || '' '' || dpto.nombre as depto,
                            fun.desc_funcionario2 as funcionario,
                            depaf.nombre as deposito,
                            depaf.codigo as deposito_cod,
                            mon.codigo as desc_moneda_orig,
                            afij.en_deposito,
                            coalesce(afij.extension,''jpg'') as extension,
                            afij.codigo_ant,
                            afij.marca,
                            afij.nro_serie,
                            afij.caracteristicas,
                            COALESCE(round(afvi.monto_vigente_real_af,2), afij.monto_compra) as monto_vigente_real_af,
                            COALESCE(afvi.vida_util_real_af,afij.vida_util_original) as vida_util_real_af,                            
                            afvi.fecha_ult_dep_real_af,
                            COALESCE(round(afvi.depreciacion_acum_real_af,2),0) as depreciacion_acum_real_af,
                            COALESCE(round( afvi.depreciacion_per_real_af,2),0) as depreciacion_per_real_af,
                            cla.tipo_activo,
                            cla.depreciable,
                            afij.monto_compra_orig,
                            afij.id_proyecto,
                            proy.codigo_proyecto as desc_proyecto,
                            afij.cantidad_af,
                            afij.id_unidad_medida,
                            unmed.codigo as codigo_unmed,
                            unmed.descripcion as descripcion_unmed,
                            afij.monto_compra_orig_100,
                            afij.nro_cbte_asociado,
                            afij.fecha_cbte_asociado,
                            round(afij.vida_util_original/12,2)::numeric as vida_util_original_anios,
                            uo.nombre_cargo,
                            afij.fecha_asignacion,
                            afij.prestamo,
                            afij.fecha_dev_prestamo,
                            afij.tramite_compra,
                            afij.id_proceso_wf,
                            afij.subtipo,
                            uoac.nombre_unidad,
                            afij.id_uo                            
            from kaf.tactivo_fijo afij                       
            inner join segu.tusuario usu1 on usu1.id_usuario = afij.id_usuario_reg            
            left join param.tcatalogo cat1 on cat1.id_catalogo = afij.id_cat_estado_fun
            left join param.tcatalogo cat2 on cat2.id_catalogo = afij.id_cat_estado_compra
            inner join kaf.tclasificacion cla on cla.id_clasificacion = afij.id_clasificacion
            inner join param.tdepto dpto on dpto.id_depto = afij.id_depto
            inner join param.tmoneda mon on mon.id_moneda = afij.id_moneda_orig
                        left join param.tproyecto proy on proy.id_proyecto = afij.id_proyecto 
                        left  join kaf.tdeposito depaf on depaf.id_deposito = afij.id_deposito
                        left join kaf.vactivo_fijo_vigente_estado_rep afvi on afvi.id_activo_fijo = afij.id_activo_fijo
                        and afvi.id_moneda = afij.id_moneda_orig
                        and (afvi.estado_mov_dep = ''finalizado'' or afvi.estado_mov_dep is null) 

                        --left join kaf.f_activo_fijo_vigente() afvi
                        --on afvi.id_activo_fijo = afij.id_activo_fijo
                        --and afvi.id_moneda = afij.id_moneda_orig

                        left join param.vcentro_costo cc on cc.id_centro_costo = afij.id_centro_costo
                        left join segu.tusuario usu2 on usu2.id_usuario = afij.id_usuario_mod           
            left join orga.vfuncionario fun on fun.id_funcionario = afij.id_funcionario
            left join orga.toficina ofi on ofi.id_oficina = afij.id_oficina
            left join segu.vpersona per on per.id_persona = afij.id_persona
            left join param.vproveedor pro on pro.id_proveedor = afij.id_proveedor
                        left join param.tunidad_medida unmed on unmed.id_unidad_medida = afij.id_unidad_medida
                        left join orga.tuo_funcionario uof
                        on uof.id_funcionario = afij.id_funcionario
                        and uof.fecha_asignacion <= now()
                        and coalesce(uof.fecha_finalizacion, now())>=now() 
                        and uof.estado_reg = ''activo'' 
                        and uof.tipo = ''oficial''
                        left join orga.tuo uo
                        on uo.id_uo = uof.id_uo
                        left join orga.tuo uoac on uoac.id_uo = afij.id_uo                        
                where  ';

            --Verifica si la consulta es por usuario
            if pxp.f_existe_parametro(p_tabla,'por_usuario') then
                if v_parametros.por_usuario = 'si' then
                    v_consulta = v_consulta || ' afij.id_funcionario in (select 
                                                fun.id_funcionario
                                                from segu.tusuario usu
                                                inner join orga.vfuncionario_persona fun
                                                on fun.id_persona = usu.id_persona
                                                where usu.id_usuario = '||p_id_usuario||') and ';
                end if;
            end if;
      
      --Definicion de la respuesta
      v_consulta:=v_consulta||v_parametros.filtro;
      v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

      --Devuelve la respuesta
      return v_consulta;
            
            
    end;

  /*********************************    
  #TRANSACCION:  'SKA_AFIJ_CONT'
  #DESCRIPCION: Conteo de registros
  #AUTOR:   admin 
  #FECHA:   29-10-2015 03:18:45
  ***********************************/

  elsif(p_transaccion='SKA_AFIJ_CONT')then

    begin
      --Sentencia de la consulta de conteo de registros
      v_consulta:='select count(afij.id_activo_fijo)
              from kaf.tactivo_fijo afij                       
            inner join segu.tusuario usu1 on usu1.id_usuario = afij.id_usuario_reg            
            left join param.tcatalogo cat1 on cat1.id_catalogo = afij.id_cat_estado_fun
            left join param.tcatalogo cat2 on cat2.id_catalogo = afij.id_cat_estado_compra
            inner join kaf.tclasificacion cla on cla.id_clasificacion = afij.id_clasificacion
            inner join param.tdepto dpto on dpto.id_depto = afij.id_depto
            inner join param.tmoneda mon on mon.id_moneda = afij.id_moneda_orig
                        left join param.tproyecto proy on proy.id_proyecto = afij.id_proyecto 
                        left  join kaf.tdeposito depaf on depaf.id_deposito = afij.id_deposito
                        
                        /*
                        left join kaf.vactivo_fijo_vigente_estado afvi on afvi.id_activo_fijo = afij.id_activo_fijo
                        and afvi.id_moneda = afij.id_moneda_orig
                        and (afvi.estado_mov_dep = ''finalizado'' or afvi.estado_mov_dep is null) */

                        --left join kaf.f_activo_fijo_vigente() afvi
                        --on afvi.id_activo_fijo = afij.id_activo_fijo
                        --and afvi.id_moneda = afij.id_moneda_orig

                        left join param.vcentro_costo cc on cc.id_centro_costo = afij.id_centro_costo
                        left join segu.tusuario usu2 on usu2.id_usuario = afij.id_usuario_mod           
            left join orga.vfuncionario fun on fun.id_funcionario = afij.id_funcionario
            left join orga.toficina ofi on ofi.id_oficina = afij.id_oficina
            left join segu.vpersona per on per.id_persona = afij.id_persona
            left join param.vproveedor pro on pro.id_proveedor = afij.id_proveedor
                        left join param.tunidad_medida unmed on unmed.id_unidad_medida = afij.id_unidad_medida
                        left join orga.tuo_funcionario uof
                        on uof.id_funcionario = afij.id_funcionario
                        and uof.fecha_asignacion <= now()
                        and coalesce(uof.fecha_finalizacion, now())>=now() 
                        and uof.estado_reg = ''activo'' 
                        and uof.tipo = ''oficial''
                        left join orga.tuo uo
                        on uo.id_uo = uof.id_uo
                        left join orga.tuo uoac on uoac.id_uo = afij.id_uo                        
                where  ';

            --Verifica si la consulta es por usuario
            if pxp.f_existe_parametro(p_tabla,'por_usuario') then
                if v_parametros.por_usuario = 'si' then
                    v_consulta = v_consulta || ' afij.id_funcionario in (select 
                                                fun.id_funcionario
                                                from segu.tusuario usu
                                                inner join orga.vfuncionario_persona fun
                                                on fun.id_persona = usu.id_persona
                                                where usu.id_usuario = '||p_id_usuario||') and ';
                end if;
            end if;
      
      --Definicion de la respuesta        
      v_consulta:=v_consulta||v_parametros.filtro;

      --Devuelve la respuesta
      return v_consulta;

    end;

  /*********************************    
  #TRANSACCION:  'SKA_IDAF_SEL'
  #DESCRIPCION: Generación de lista de ID de activos fijos en base a un criterio
  #AUTOR:     RCM
  #FECHA:     30/12/2015
  ***********************************/

  elsif(p_transaccion='SKA_IDAF_SEL')then

    begin
          --Sentencia de la consulta
      v_consulta:='select
            pxp.list(afij.id_activo_fijo::text) as ids
            from kaf.tactivo_fijo afij
            inner join segu.tusuario usu1 on usu1.id_usuario = afij.id_usuario_reg
            left join segu.tusuario usu2 on usu2.id_usuario = afij.id_usuario_mod
            left join param.tcatalogo cat1 on cat1.id_catalogo = afij.id_cat_estado_fun
            left join param.tcatalogo cat2 on cat2.id_catalogo = afij.id_cat_estado_compra
            inner join kaf.tclasificacion cla on cla.id_clasificacion = afij.id_clasificacion
            left join param.vcentro_costo cc on cc.id_centro_costo = afij.id_centro_costo
            inner join param.tdepto dpto on dpto.id_depto = afij.id_depto
            left join orga.vfuncionario fun on fun.id_funcionario = afij.id_funcionario
            left join orga.toficina ofi on ofi.id_oficina = afij.id_oficina
            left join segu.vpersona per on per.id_persona = afij.id_persona
            left join param.vproveedor pro on pro.id_proveedor = afij.id_proveedor
            inner join kaf.tdeposito depaf on depaf.id_deposito = afij.id_deposito
                where  ';
      
      --Definicion de la respuesta
      v_consulta:=v_consulta||v_parametros.filtro;

      --Devuelve la respuesta
      return v_consulta;
        
        end;
  
    /*********************************    
  #TRANSACCION:  'SKA_GEVARTQR_SEL'
  #DESCRIPCION: listado de activos segun criterio de formulario para generacion del reporte de codigos QR
  #AUTOR:     RAC
  #FECHA:     17/03/2017
  ***********************************/

  elsif(p_transaccion='SKA_GEVARTQR_SEL')then

    begin
          
            v_criterio_filtro = '  0=0 ';
           -- raise exception 'sss';
            
            IF  pxp.f_existe_parametro(p_tabla, 'id_clasificacion') THEN   
            
              IF v_parametros.id_clasificacion is not null THEN     
        
                  WITH RECURSIVE clasificacion_rec(id_clasificacion, codigo, id_clasificacion_fk) AS (
                  select 
                    c.id_clasificacion,
                    c.codigo,
                    c.id_clasificacion_fk
                  from kaf.tclasificacion c  
                  where c.estado_reg = 'activo' and c.id_clasificacion = v_parametros.id_clasificacion

                  UNION

                  select 
                    c2.id_clasificacion,
                    c2.codigo,
                    c2.id_clasificacion_fk
                  from kaf.tclasificacion  c2, clasificacion_rec pc
                  WHERE c2.id_clasificacion_fk = pc.id_clasificacion  and c2.estado_reg = 'activo'
                  )
                  
                  
                  SELECT pxp.list(id_clasificacion::varchar) 
                     into 
                        v_lista_af 
                  FROM clasificacion_rec; 
                  v_criterio_filtro = '  id_clasificacion in ('|| COALESCE(v_lista_af,'0')||')';
                  
                  
              END IF;
            
            END IF;
            
            
            IF  pxp.f_existe_parametro(p_tabla, 'desde') THEN  
               IF v_parametros.desde is not null   THEN     
                    v_criterio_filtro = v_criterio_filtro||'  and kaf.fecha_compra >= '''||v_parametros.desde||'''::date  ';
               END IF;
            END IF;
            
             IF  pxp.f_existe_parametro(p_tabla, 'hasta') THEN  
                IF v_parametros.hasta is not null   THEN     
                     v_criterio_filtro = v_criterio_filtro||'  and kaf.fecha_compra <= '''||v_parametros.hasta||'''::date  ';
                END IF;
            END IF;
            
            --Sentencia de la consulta
      v_consulta:='select 
                            kaf.id_activo_fijo,
                            kaf.codigo::varchar,
                            kaf.codigo_ant::varchar,
                            kaf.denominacion::varchar,
                            COALESCE(dep.nombre_corto, '''')::varchar as nombre_depto,
                            COALESCE(ent.nombre, '''')::varchar as nombre_entidad
                          from kaf.tactivo_fijo  kaf
                          inner join param.tdepto dep on dep.id_depto = kaf.id_depto 
                          left join param.tentidad ent on ent.id_entidad = dep.id_entidad
                          where kaf.estado = ''alta'' and  '||v_criterio_filtro;
      
      
      
            
            raise notice '%',v_consulta;

      --Devuelve la respuesta
      return v_consulta;
        
        end;

    /*********************************    
    #TRANSACCION:  'SKA_AFFECH_SEL'
    #DESCRIPCION:   Consulta de datos considerando fecha para obtener el valor real a una fecha
    #AUTOR:         RCM   
    #FECHA:         14/06/2017
    ***********************************/

    elsif(p_transaccion='SKA_AFFECH_SEL')then
                    
        begin
            if v_parametros.fecha_mov is null then
                raise exception 'Debe especificar la fecha';
            end if;
      if pxp.f_existe_parametro(p_tabla, 'no_asignado') then
              if v_parametros.no_asignado = 'alta' then
                  v_condicion = '((afij.id_activo_fijo not in (select coalesce(tmaf.id_activo_fijo,0) from kaf.tmovimiento_af tmaf) or afij.id_funcionario is null) and afij.estado = ''registrado'') and ';
              end if; 
            end if;
            --Sentencia de la consulta
            v_consulta:='select
                            afij.id_activo_fijo,
                            afij.id_persona,
                            afij.cantidad_revaloriz,
                            coalesce(afij.foto,''./../../../uploaded_files/sis_kactivos_fijos/ActivoFijo/default.jpg'') as foto,
                            afij.id_proveedor,
                            afij.estado_reg,
                            afij.fecha_compra,
                            afij.monto_vigente,
                            afij.id_cat_estado_fun,
                            afij.ubicacion,
                            afij.vida_util,
                            afij.documento,
                            afij.observaciones,
                            afij.fecha_ult_dep,
                            afij.monto_rescate,
                            afij.denominacion,
                            afij.id_funcionario,
                            afij.id_deposito,
                            afij.monto_compra,
                            afij.id_moneda,
                            afij.depreciacion_mes,
                            afij.codigo,
                            afij.descripcion,
                            afij.id_moneda_orig,
                            afij.fecha_ini_dep,
                            afij.id_cat_estado_compra,
                            afij.depreciacion_per,
                            afij.vida_util_original,
                            afij.depreciacion_acum,
                            afij.estado,
                            afij.id_clasificacion,
                            afij.id_centro_costo,
                            afij.id_oficina,
                            afij.id_depto,
                            afij.id_usuario_reg,
                            afij.fecha_reg,
                            afij.usuario_ai,
                            afij.id_usuario_ai,
                            afij.id_usuario_mod,
                            afij.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            per.nombre_completo2 as persona,
                            pro.desc_proveedor,
                            cat1.descripcion as estado_fun,
                            cat2.descripcion as estado_compra,
                            cla.codigo_completo_tmp || '' '' || cla.nombre as clasificacion,
                            cc.codigo_cc as centro_costo,
                            ofi.codigo || '' '' || ofi.nombre as oficina,
                            dpto.codigo || '' '' || dpto.nombre as depto,                           
                fun.desc_funcionario2 as funcionario,
                            depaf.nombre as deposito,
                            depaf.codigo as deposito_cod,
                            mon.codigo as desc_moneda_orig,
                            afij.en_deposito,
                            coalesce(afij.extension,''jpg'') as extension,
                            afij.codigo_ant,
                            afij.marca,
                            afij.nro_serie,
                            afij.caracteristicas,
                            afij.monto_compra, --COALESCE(round(afvi.monto_vigente_real_af,2), afij.monto_compra),
                            afij.vida_util_original, --COALESCE(afvi.vida_util_real_af,afij.vida_util_original),                            
                            afij.fecha_ini_dep, --afvi.fecha_ult_dep_real_af,
                            afij.depreciacion_acum,--COALESCE(round(afvi.depreciacion_acum_real_af,2),0),
                            afij.depreciacion_per,--COALESCE(round( afvi.depreciacion_per_real_af,2),0),
                            cla.tipo_activo,
                            cla.depreciable,
                            afij.monto_compra_orig,
                            afij.id_proyecto,
                            proy.codigo_proyecto as desc_proyecto,
                            afij.cantidad_af,
                            afij.id_unidad_medida,
                            unmed.codigo as codigo_unmed,
                            unmed.descripcion as descripcion_unmed,
                            afij.monto_compra_orig_100,
                            afij.nro_cbte_asociado,
                            afij.fecha_cbte_asociado,
                            uo.nombre_cargo
                        from kaf.tactivo_fijo afij                       
                        inner join segu.tusuario usu1 on usu1.id_usuario = afij.id_usuario_reg                      
                        left join param.tcatalogo cat1 on cat1.id_catalogo = afij.id_cat_estado_fun
                        left join param.tcatalogo cat2 on cat2.id_catalogo = afij.id_cat_estado_compra
                        inner join kaf.tclasificacion cla on cla.id_clasificacion = afij.id_clasificacion
                        inner join param.tdepto dpto on dpto.id_depto = afij.id_depto
                        inner join param.tmoneda mon on mon.id_moneda = afij.id_moneda_orig
                        left join param.tproyecto proy on proy.id_proyecto = afij.id_proyecto 
                        left  join kaf.tdeposito depaf on depaf.id_deposito = afij.id_deposito
                        /*left join kaf.vactivo_fijo_vigente_estado afvi on afvi.id_activo_fijo = afij.id_activo_fijo
                        and afvi.id_moneda = afij.id_moneda_orig
                        and (afvi.estado_mov_dep = ''finalizado'' or afvi.estado_mov_dep is null) */

                        /*left join kaf.f_activo_fijo_vigente('''||v_parametros.fecha_mov||''') afvi
                        on afvi.id_activo_fijo = afij.id_activo_fijo
                        and afvi.id_moneda = afij.id_moneda_orig*/

                        left join param.vcentro_costo cc on cc.id_centro_costo = afij.id_centro_costo
                        left join segu.tusuario usu2 on usu2.id_usuario = afij.id_usuario_mod                       
                        left join orga.vfuncionario fun on fun.id_funcionario = afij.id_funcionario
                        left join orga.toficina ofi on ofi.id_oficina = afij.id_oficina
                        left join segu.vpersona per on per.id_persona = afij.id_persona
                        left join param.vproveedor pro on pro.id_proveedor = afij.id_proveedor
                        left join param.tunidad_medida unmed on unmed.id_unidad_medida = afij.id_unidad_medida
                        left join orga.tuo_funcionario uof
                        on uof.id_funcionario = afij.id_funcionario
                        and uof.fecha_asignacion <= ''' || v_parametros.fecha_mov || '''
                        and coalesce(uof.fecha_finalizacion, '''||v_parametros.fecha_mov||''')>=''' || v_parametros.fecha_mov || '''
                        and uof.estado_reg = ''activo'' 
                        and uof.tipo = ''oficial''
                        left join orga.tuo uo
                        on uo.id_uo = uof.id_uo
                        where ' ||v_condicion;
            
            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            --Devuelve la respuesta
            return v_consulta;
            
                        
        end;

    /*********************************    
    #TRANSACCION:  'SKA_AFFECH_CONT'

    #DESCRIPCION:   Conteo de registros
    #AUTOR:         RCM   
    #FECHA:         14/06/2017
    ***********************************/

    elsif(p_transaccion='SKA_AFFECH_CONT')then

        begin
            if v_parametros.fecha_mov is null then
                raise exception 'Debe especificar la fecha';
            end if;
            --(fea)
            if pxp.f_existe_parametro(p_tabla, 'no_asignado') then
              if v_parametros.no_asignado = 'asignado' then
                  v_condicion = '((afij.id_activo_fijo not in (select coalesce(tmaf.id_activo_fijo,0) from kaf.tmovimiento_af tmaf) or afij.id_funcionario is null) and afij.estado = ''registrado'') and ';
              end if; 
            end if;
            --Sentencia de la consulta de conteo de registros
            v_consulta:='select count(afij.id_activo_fijo)
                        from kaf.tactivo_fijo afij                       
                        inner join segu.tusuario usu1 on usu1.id_usuario = afij.id_usuario_reg                      
                        left join param.tcatalogo cat1 on cat1.id_catalogo = afij.id_cat_estado_fun
                        left join param.tcatalogo cat2 on cat2.id_catalogo = afij.id_cat_estado_compra
                        inner join kaf.tclasificacion cla on cla.id_clasificacion = afij.id_clasificacion
                        inner join param.tdepto dpto on dpto.id_depto = afij.id_depto                        
                        inner join param.tmoneda mon on mon.id_moneda = afij.id_moneda_orig
                        left join param.tproyecto proy on proy.id_proyecto = afij.id_proyecto 
                        left join kaf.tdeposito depaf on depaf.id_deposito = afij.id_deposito
                        /*left join kaf.vactivo_fijo_vigente afvi on afvi.id_activo_fijo = afij.id_activo_fijo*/
                        /*left join kaf.f_activo_fijo_vigente('''||v_parametros.fecha_mov||''') afvi
                        on afvi.id_activo_fijo = afij.id_activo_fijo
                        and afvi.id_moneda = afij.id_moneda_orig*/
                        left join param.vcentro_costo cc on cc.id_centro_costo = afij.id_centro_costo
                        left join segu.tusuario usu2 on usu2.id_usuario = afij.id_usuario_mod                       
                        left join orga.vfuncionario fun on fun.id_funcionario = afij.id_funcionario
                        left join orga.toficina ofi on ofi.id_oficina = afij.id_oficina
                        left join segu.vpersona per on per.id_persona = afij.id_persona
                        left join param.vproveedor pro on pro.id_proveedor = afij.id_proveedor
                        left join param.tunidad_medida unmed on unmed.id_unidad_medida = afij.id_unidad_medida
                        left join orga.tuo_funcionario uof
                        on uof.id_funcionario = afij.id_funcionario
                        and uof.fecha_asignacion <= ''' || v_parametros.fecha_mov || '''
                        and coalesce(uof.fecha_finalizacion, '''||v_parametros.fecha_mov||''')>=''' || v_parametros.fecha_mov || '''
                        and uof.estado_reg = ''activo'' 
                        and uof.tipo = ''oficial''
                        left join orga.tuo uo
                        on uo.id_uo = uof.id_uo
                        where  '||v_condicion;
            
            --Definicion de la respuesta            
            v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************    
    #TRANSACCION:  'SKA_QRVARIOS_SEL'
    #DESCRIPCION:   Listado para imprimir varios códigos de barra
    #AUTOR:         RCM
    #FECHA:         04/10/2017
    ***********************************/

    elsif(p_transaccion='SKA_QRVARIOS_SEL')then

        begin

            --Recuperar configuracion del reporte de codigo de barrar por defecto de variable global
             v_clase_reporte = pxp.f_get_variable_global('kaf_clase_reporte_codigo');

            --Sentencia de la consulta
            v_consulta:='select 
                        kaf.id_activo_fijo,
                        kaf.codigo,
                        kaf.codigo_ant,
                        kaf.denominacion,
                        coalesce(dep.nombre_corto, '''') as nombre_depto,
                        coalesce(ent.nombre, '''') as nombre_entidad,
                        kaf.descripcion,'''
                        ||v_clase_reporte||'''::varchar as clase_rep
                        from kaf.tactivo_fijo  kaf
                        inner join param.tdepto dep on dep.id_depto = kaf.id_depto 
                        left join param.tentidad ent on ent.id_entidad = dep.id_entidad
                        where ';
            
            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;
        
        end;
    /*********************************    
    #TRANSACCION:  'SKA_COMPRAS_GEST_SEL'
    #DESCRIPCION:   Reporte Compras por Gestion
    #AUTOR:         FEA
    #FECHA:         24/1/2018
    ***********************************/

    elsif(p_transaccion='SKA_COMPRAS_GEST_SEL')then

        begin     
        --raise exception 'parama: %', pxp.f_existe_parametro(p_tabla, 'ubicacion');
          if(pxp.f_existe_parametro(p_tabla, 'ubicacion'))then 
             
              if(v_parametros.ubicacion::integer = 1)then 
                v_condicion = '((tlug.id_lugar_fk in (1,2,61,63,65,66,67,68,70,256,282)) or vf.desc_funcionario2 is null) and ';  
                elsif(v_parametros.ubicacion::integer = 2)then
                  v_condicion = '((tlug.id_lugar_fk not in (1,2,61,63,65,66,67,68,70,256,282)) or vf.desc_funcionario2 is null) and';
                else 
                  v_condicion = '';
                end if;
                  
            else 
              v_condicion = '';
            end if;
            
            --Sentencia de la consulta
            v_consulta:='
              with recursive niveles (nivel, id_clasificacion, id_clasificacion_fk, codigo, nombre, camino, codigo_completo, tipo_activo) as 
        (  
                    select 0, tcc.id_clasificacion, tcc.id_clasificacion_fk, tcc.codigo, tcc.nombre, tcc.codigo::TEXT as camino, tcc.codigo_completo_tmp, tcc.tipo_activo                      
                    from kaf.tclasificacion tcc             
                    where tcc.id_clasificacion_fk is null                                         

                    union all              

                    select padre.nivel+1,  hijo.id_clasificacion, hijo.id_clasificacion_fk, hijo.codigo, hijo.nombre, padre.camino || ''.'' || hijo.codigo::TEXT,  hijo.codigo_completo_tmp, hijo.tipo_activo                  
                    from kaf.tclasificacion hijo,  niveles padre             
                    where hijo.id_clasificacion_fk  = padre.id_clasificacion
  
        )       

              select  
              niv.id_clasificacion, 
              niv.id_clasificacion_fk,
        niv.codigo,
        niv.codigo_completo,  
              niv.nivel, 
            niv.nombre,
              niv.camino,
        taf.codigo as codig_af,
              case when '''||v_parametros.desc_nombre||'''= ''desc'' then coalesce(taf.descripcion, ''-'')
              when '''||v_parametros.desc_nombre||'''= ''descnom'' then coalesce(taf.denominacion||'' /'||chr(10)||'''||taf.descripcion, ''-'')
              else coalesce(taf.denominacion, ''-'') end as denominacion, 
              coalesce(taf.fecha_compra::varchar, ''-'')::varchar as fecha_compra,               
              coalesce(taf.nro_cbte_asociado, ''-'') as nro_cbte_asociado, 
              coalesce(taf.fecha_cbte_asociado::varchar, ''-'') as fecha_cbte_asociado,
              coalesce(taf.fecha_ini_dep::varchar, ''-'') as fecha_ini_dep, 
              coalesce(taf.vida_util_original, 0) as  vida_util_original,
              coalesce(taf.monto_compra_orig_100, 0) as monto_compra_orig_100,
              coalesce(taf.monto_compra_orig, 0) as monto_compra_orig,
              niv.tipo_activo,
              (tlug.nombre||''-''||tof.nombre)::varchar as ubicacion,
              vf.desc_funcionario2::varchar as responsable,
              taf.monto_compra,
              taf.estado,
        coalesce(tu.nombre_unidad,''-'') as nombre_unidad,
              cat.descripcion as estado_fun
                            
              from niveles  niv 
              left join kaf.tactivo_fijo taf on taf.id_clasificacion = niv.id_clasificacion
              left join orga.vfuncionario vf on vf.id_funcionario = taf.id_funcionario
              left join orga.toficina tof on tof.id_oficina = taf.id_oficina
              left join param.tlugar tlug on tlug.id_lugar = tof.id_lugar
              left join param.tcatalogo cat on cat.id_catalogo = taf.id_cat_estado_fun
              left join orga.tuo tu on tu.id_uo= taf.id_uo           
              where '||v_condicion||'(              
            ';
            
            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro||' and kaf.f_verificar_hijos (niv.nivel,niv.id_clasificacion, coalesce(taf.id_activo_fijo, 0),'''||v_parametros.fecha_ini||''','''||v_parametros.fecha_fin||'''))';
            v_consulta = v_consulta||' order by niv.camino, taf.codigo';
      raise notice 'v_consulta: %',v_consulta;
            --Devuelve la respuesta
            return v_consulta;
        
        end;
        
    /*********************************    
    #TRANSACCION:  'SKA_REP_DETAF_SEL'
    #DESCRIPCION:   Reporte De Activo
    #AUTOR:         BVP
    #FECHA:         19/04/2018
    ***********************************/
    elsif(p_transaccion='SKA_REP_DETAF_SEL')then
      begin 
        --raise exception 'tr:%',v_parametros.id_clasificacion;
                v_consulta:=' with recursive niveles (id_clasificacion, id_clasificacion_fk, codigo_completo_tmp, nombre, nivel) 
                as 
                (select
                        cla.id_clasificacion,
                        cla.id_clasificacion_fk,
                        cla.codigo_completo_tmp,
                        cla.nombre,
                        claf.nivel
                        from kaf.vclasificacion_arbol claf
                        inner join kaf.tclasificacion cla on cla.id_clasificacion = claf.id_clasificacion
                        where cla.id_clasificacion_fk in ('||v_parametros.id_clasificacion||')                
          union all
          
          select
                          cla.id_clasificacion,
                          cla.id_clasificacion_fk,
                          cla.codigo_completo_tmp,
                          cla.nombre,
                          claf.nivel
                          from kaf.vclasificacion_arbol claf
                          inner join niveles niv on claf.id_clasificacion_fk = niv.id_clasificacion
                          inner join kaf.tclasificacion cla on cla.id_clasificacion = claf.id_clasificacion
                          where claf.id_clasificacion_fk = niv.id_clasificacion
          )   
          select
                        niv.id_clasificacion,
                        niv.id_clasificacion_fk,
                        niv.codigo_completo_tmp,
                        niv.nombre,
                        niv.nivel,
                        case when claf.nivel = 2 then kaf.f_get_cantidad_hijos(claf.id_clasificacion,''CONTA_NIETOS'') 
                        when claf.nivel = 3 then kaf.f_get_cantidad_hijos(claf.id_clasificacion,''CONTA_HIJOS'') end as hijos
                        from niveles niv
                        inner join kaf.vclasificacion_arbol claf on claf.id_clasificacion = niv.id_clasificacion
                        inner join kaf.tclasificacion cla  on cla.id_clasificacion = claf.id_clasificacion
                        order by niv.codigo_completo_tmp';
              raise notice 'v_consulta%',v_consulta;
      --Devuelve la respuesta
      return v_consulta;
        end;
    /*********************************    
    #TRANSACCION:  'SKA_REP_ACTEDET_SEL'
    #DESCRIPCION:   Reporte De Activo por detalle
    #AUTOR:         BVP
    #FECHA:         19/04/2018
    ***********************************/
    elsif(p_transaccion='SKA_REP_ACTEDET_SEL')then 
    
      begin 
    
        v_consulta:= '
        with recursive niveles (nivel, id_clasificacion, id_clasificacion_fk, codigo, nombre, camino, codigo_completo, tipo_activo) 
              as 
                  ( 
                     select 0, tcc.id_clasificacion, tcc.id_clasificacion_fk, tcc.codigo, tcc.nombre, 
                     tcc.codigo::TEXT as camino, tcc.codigo_completo_tmp, tcc.tipo_activo 
                     from kaf.tclasificacion tcc 
                     where tcc.id_clasificacion_fk in ('||v_parametros.id_clasificacion||')
                     union all 
                     select padre.nivel+1, hijo.id_clasificacion, hijo.id_clasificacion_fk,
                      hijo.codigo, hijo.nombre, padre.camino || ''.'' || hijo.codigo::TEXT, hijo.codigo_completo_tmp, 
                      hijo.tipo_activo 
                     from kaf.tclasificacion hijo, niveles padre 
                     where hijo.id_clasificacion_fk = padre.id_clasificacion
                  )
               select 
              substr(niv.codigo_completo,1,2)::varchar as tipo,
               taf.marca,
               niv.codigo_completo as subtipo, 
               taf.codigo as codigo,
               taf.descripcion,
               niv.nombre as clasificacion,
               taf.denominacion,
               taf.estado,
               cat.descripcion as estado_funcional,
               coalesce(taf.fecha_compra::varchar, ''-'')::varchar as fecha_compra,
               coalesce(taf.nro_cbte_asociado, ''-'')::varchar as c31, 
               (tlug.nombre||''-''||tof.nombre)::varchar as ubicacion,
               vf.desc_funcionario2::varchar as responsable
               from niveles niv 
               left join kaf.tactivo_fijo taf on taf.id_clasificacion = niv.id_clasificacion
               left join param.tcatalogo cat on cat.id_catalogo = taf.id_cat_estado_fun
               left join orga.vfuncionario vf on vf.id_funcionario = taf.id_funcionario
               left join orga.toficina tof on tof.id_oficina = taf.id_oficina
               left join param.tlugar tlug on tlug.id_lugar = tof.id_lugar  
               order by niv.codigo_completo';
               
        --raise notice 'v_consulta%',v_consulta;
        return v_consulta;  
        end;
    /*********************************    
    #TRANSACCION:  'SKA_LI_ACLIDE_SEL'
    #DESCRIPCION:   Reporte De Activo por detalle
    #AUTOR:         BVP
    #FECHA:         19/04/2018
    ***********************************/
      elsif(p_transaccion='SKA_LI_ACLIDE_SEL')then   
       begin  
          v_consulta:='
                  select
                            cla.id_clasificacion,
                            cla.codigo,
                            cla.nombre,
                            ar.nivel
                    from kaf.vclaificacion_raiz cla
                    inner join kaf.vclasificacion_arbol ar on ar.id_clasificacion =cla.id_clasificacion           
                    where ar.nivel = 1 
                    order by cla.codigo';
                --raise notice 'v_consulta%',v_consulta;
        return v_consulta;
      end;
    /*********************************    
    #TRANSACCION:  'SKA_PROV_AC_SEL'
    #DESCRIPCION:   PROVEEDORES DE ACTIVOS REPORTE
    #AUTOR:         BVP
    #FECHA:         22/09/2018
    ***********************************/
      elsif(p_transaccion='SKA_PROV_AC_SEL')then   
       begin  
          v_consulta:='select 
                        distinct pro.desc_proveedor as provee,
                                pro.id_proveedor
                            from kaf.tactivo_fijo ac 
                            inner join param.vproveedor pro on pro.id_proveedor=ac.id_proveedor
                            where ';
               -- raise exception '%',v_consulta;
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;                            
        return v_consulta;
      end;
  /*********************************    
  #TRANSACCION:  'SKA_ACTVAL_CONT'
  #DESCRIPCION: PROVEEDORES DE ACTIVOS CONT
    #AUTOR:         BVP
    #FECHA:         22/09/2018
  ***********************************/

  elsif(p_transaccion='SKA_PROV_AC_CONT')then

    begin
      --Sentencia de la consulta de conteo de registros
      v_consulta:='select count (distinct pro.desc_proveedor)
                        from kaf.tactivo_fijo ac
                        inner join param.vproveedor pro on pro.id_proveedor = ac.id_proveedor 
                        where';
      
      --Definicion de la respuesta        
      v_consulta:=v_consulta||v_parametros.filtro;

      --Devuelve la respuesta
      return v_consulta;

    end;
  /*********************************    
  #TRANSACCION:  'SKA_ACTLUG_SEL'
  #DESCRIPCION: lUGAR ACTIVO
    #AUTOR:         BVP
    #FECHA:         22/09/2018
  ***********************************/

  elsif(p_transaccion='SKA_ACTLUG_SEL')then

    begin
      --Sentencia de la consulta de conteo de registros
      v_consulta:='select
                          lug.id_lugar,
                          lug.codigo,
                          lug.nombre
                          from param.tlugar lug
                          inner join segu.tusuario usu1 on usu1.id_usuario = lug.id_usuario_reg
                          left join segu.tusuario usu2 on usu2.id_usuario = lug.id_usuario_mod
                          where lug.id_lugar = '||v_parametros.id_lugar;
        return v_consulta;

    end;
  /*********************************    
  #TRANSACCION:  'SKA_PROV_REP_SEL'
  #DESCRIPCION: lUGAR ACTIVO
    #AUTOR:         BVP
    #FECHA:         22/09/2018
  ***********************************/

  elsif(p_transaccion='SKA_PROV_REP_SEL')then

    begin
      --Sentencia de la consulta de conteo de registros

      v_consulta:='select 
                            pro.desc_proveedor
                            from kaf.tactivo_fijo ac 
                            inner join param.vproveedor pro on pro.id_proveedor=ac.id_proveedor
                            where ac.id_proveedor = '||v_parametros.id_proveedor||'
                            limit 1 ';
        return v_consulta;

    end;                       
----------------------------------------------------------

  /*********************************    
  #TRANSACCION:  'KA_AFIJOS_SEL'
  #DESCRIPCION: Consulta de datos
  #AUTOR:   BVP 
  #FECHA:   04-10-2018
  ***********************************/

   elsif(p_transaccion='KA_AFIJOS_SEL')then
            
      begin
        --Sentencia de la consulta
      v_consulta:='select
                            afij.id_activo_fijo,
                            afij.denominacion,
                            afij.codigo
            from kaf.tactivo_fijo afij                       
                        where ';
      --Devuelve la respuesta
      --Definicion de la respuesta
      v_consulta:=v_consulta||v_parametros.filtro;
      v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
      return v_consulta;
            
            
    end;

  /*********************************    
  #TRANSACCION:  'KA_AFIJOS_CONT'
  #DESCRIPCION: Conteo de registros
  #AUTOR:   BVP 
  #FECHA:   04-10-2018
  ***********************************/

  elsif(p_transaccion='KA_AFIJOS_CONT')then

    begin
      --Sentencia de la consulta de conteo de registros
      v_consulta:='select count(afij.id_activo_fijo)
              from kaf.tactivo_fijo afij                        
            where  ';
      v_consulta:=v_consulta||v_parametros.filtro;
      --Devuelve la respuesta
      return v_consulta;

    end;
    /*********************************    
    #TRANSACCION:  'SKA_GET_AF_BOA_SEL'
    #DESCRIPCION:   Servicio que retorna los activos asignados un funcionario BOA
    #AUTOR:         FEA
    #FECHA:         17/7/2018
    ***********************************/

    elsif(p_transaccion='SKA_GET_AF_BOA_SEL')then     
    
      begin
                if v_parametros.orden='1' then
                 ord= 'order by taf.fecha_asignacion desc';
                 else
                 ord = 'order by taf.fecha_asignacion asc';
                 end if;
                  --Sentencia de la consulta
                  v_consulta:='select vf.desc_funcionario1::varchar as responsable, 
                               taf.codigo,
                               taf.denominacion::varchar as denominacion,
                               taf.descripcion::varchar as descripcion, 
                               pxp.f_fecha_literal(taf.fecha_asignacion)::text as fecha_asignacion, 
                               (tl.codigo||''-''||tof.nombre)::varchar as oficina, 
                               taf.ubicacion::varchar as  ubicacion
                              from kaf.tactivo_fijo taf
                              inner join orga.vfuncionario vf on vf.id_funcionario = taf.id_funcionario
                              inner join orga.toficina tof on tof.id_oficina = taf.id_oficina
                              inner join param.tlugar tl on tl.id_lugar = tof.id_lugar
                where taf.id_funcionario = '||v_parametros.id_funcionario||' and taf.denominacion like ''%'||upper(v_parametros.busca)||'%'' and ';
                  
                  --Definicion de la respuesta
                  v_consulta = v_consulta||v_parametros.filtro;
          v_consulta = v_consulta||''||ord||'';
                  --Devuelve la respuesta
                  return v_consulta;
              
              end;    
----------------------------------------------------------        
        /*********************************    
        #TRANSACCION:  'KA_AFUNSOL_SEL'
        #DESCRIPCION: Conteo de registros
        #AUTOR:   BVP 
        #FECHA:   23-10-2018
        ***********************************/

        elsif(p_transaccion='KA_AFUNSOL_SEL')then

          begin
            --Sentencia de la consulta de conteo de registros
            v_consulta:='select uo.id_uo,
                            uo.nombre_unidad
                              from orga.tuo uo 
                  where  ';
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
            return v_consulta;

          end;
              
        /*********************************    
        #TRANSACCION:  'KA_AFUNSOL_CONT'
        #DESCRIPCION: Conteo de registros
        #AUTOR:   BVP 
        #FECHA:   23-10-2018
        ***********************************/

        elsif(p_transaccion='KA_AFUNSOL_CONT')then

          begin
            --Sentencia de la consulta de conteo de registros
            v_consulta:='select count(uo.id_uo)
                              from orga.tuo uo 
                  where  ';
            v_consulta:=v_consulta||v_parametros.filtro;
            return v_consulta;

          end;
  else
    raise exception 'Transaccion inexistente';
                   
  end if;    
          
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