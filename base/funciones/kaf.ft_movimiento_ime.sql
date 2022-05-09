CREATE OR REPLACE FUNCTION kaf.ft_movimiento_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:   Sistema de Activos Fijos
 FUNCION:     kaf.ft_movimiento_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'kaf.tmovimiento'
 AUTOR:      (admin)
 FECHA:         22-10-2015 20:42:41
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

    v_nro_requerimiento           integer;
    v_parametros                  record;
    v_id_requerimiento            integer;
    v_resp                        varchar;
    v_nombre_funcion              text;
    v_mensaje_error               text;
    v_id_movimiento               integer;
    v_sql                         varchar;
    v_rec                         record;
    v_id_responsable_depto        integer;
    v_id_gestion                  integer;
    v_codigo_tipo_proceso         varchar;
    v_id_proceso_macro            integer;
    v_id_proceso_wf               integer;
    v_id_estado_wf                integer;
    v_codigo_estado               varchar;
    v_num_tramite                 varchar;
    v_acceso_directo              varchar;
    v_clase                       varchar;
    v_parametros_ad               varchar;
    v_tipo_noti                   varchar;
    v_titulo                      varchar;
    v_movimiento                  record;
    v_id_tipo_estado              integer;
    v_pedir_obs                   varchar;
    v_codigo_estado_siguiente     varchar;
    v_id_depto                    integer;
    v_obs                         varchar;
    v_id_estado_actual            integer;
    v_id_funcionario              integer;
    v_id_usuario_reg              integer;
    v_id_estado_wf_ant            integer;
    v_cod_movimiento              varchar;
    v_codigo_estado_anterior      varchar;
    v_registros_mov               record;
    v_id_moneda_base              integer;
    v_registros_af_mov            record;
    v_registros_mod               record;
    v_monto_rescate               numeric;
    v_monto_compra                numeric;
    v_id_int_comprobante          integer;
    v_kaf_cbte                    varchar;
    v_kaf_cbte_aitb               varchar;
    v_id_int_comprobante_aitb     integer;
    v_monto_inc_dec_real          numeric;
    v_vida_util_inc_dec_real      integer;
    v_fun                         varchar;
    v_id_subsistema               integer;
    v_id_periodo_subsistema       integer;
    v_res                         varchar;
    v_rec_af                      record;
    v_numero                      integer;
    v_nuevo_codigo                varchar;
    v_rec_mov_esp                 record;
    v_id_activo_fijo              integer;
    v_ids_mov                     varchar;
    v_desc_movimiento             varchar;
    v_id_cat_movimiento           integer;
    v_cods_mov                    varchar;
    v_cod_mov_aux                 varchar;
    v_registros                   record;
    v_id_movimiento_af            integer;
    v_monto_compra_100            numeric;
    v_id_conf                     integer;
    v_reg_masivo                  boolean;
    v_id_movi_motivo			  integer;

   --bvp
    v_depreciacion_acum_corregido numeric;
    v_id_af_valor_mod			  integer;
    v_mes_fecha					  integer;
    v_anio 						  integer;
    v_codigo_retiro				  varchar;
BEGIN

  v_nombre_funcion = 'kaf.ft_movimiento_ime';
  v_parametros = pxp.f_get_record(p_tabla);

  --Obtención del subsistema para posterior verificación de período abierto
  select id_subsistema into v_id_subsistema
  from segu.tsubsistema
  where codigo = 'KAF';

  /*********************************
  #TRANSACCION:  'SKA_MOV_INS'
  #DESCRIPCION: Insercion de registros
  #AUTOR:   admin
  #FECHA:   22-10-2015 20:42:41
  ***********************************/

  if(p_transaccion='SKA_MOV_INS')then

    begin
        --Verificar bandera para registro masivo de Transferencias y Devoluciones
        v_reg_masivo = false;
        if coalesce(v_parametros.tipo_asig,'seleccionar') = 'todos' then
            v_reg_masivo = true;
        end if;

        select
        coalesce(v_parametros.direccion,null) as direccion,
        coalesce(v_parametros.fecha_hasta,null) as fecha_hasta,
        coalesce(v_parametros.id_cat_movimiento,null) as id_cat_movimiento,
        coalesce(v_parametros.fecha_mov,null) as fecha_mov,
        coalesce(v_parametros.id_depto,null) as id_depto,
        coalesce(v_parametros.glosa,null) as glosa,
        coalesce(v_parametros.id_funcionario,null) as id_funcionario,
        coalesce(v_parametros.id_oficina,null) as id_oficina,
        coalesce(v_parametros._id_usuario_ai,null) as _id_usuario_ai,
        coalesce(p_id_usuario,null) as id_usuario,
        coalesce(v_parametros._nombre_usuario_ai,null) as _nombre_usuario_ai,
        coalesce(v_parametros.id_persona,null) as id_persona,
        coalesce(v_parametros.codigo,null) as codigo,
        coalesce(v_parametros.id_deposito,null) as id_deposito,
        coalesce(v_parametros.id_depto_dest,null) as id_depto_dest,
        coalesce(v_parametros.id_deposito_dest,null) as id_deposito_dest,
        coalesce(v_parametros.id_funcionario_dest,null) as id_funcionario_dest,
        coalesce(v_parametros.id_movimiento_motivo,null) as id_movimiento_motivo,
        coalesce(v_parametros.prestamo,null) as prestamo,
        coalesce(v_parametros.fecha_dev_prestamo,null) as fecha_dev_prestamo,
        coalesce(v_parametros.tipo_asig,null) as tipo_asig,
        coalesce(v_parametros.nro_documento,null) as nro_documento,
        coalesce(v_parametros.tipo_documento,null) as tipo_documento,
        coalesce(v_parametros.tipo_drepeciacion,null) as tipo_drepeciacion,
        v_reg_masivo as reg_masivo
        into v_rec_af;

        --Inserción del movimiento
        v_id_movimiento = kaf.f_insercion_movimiento(p_id_usuario, hstore(v_rec_af));

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Movimiento de Activos Fijos almacenado(a) con exito (id_movimiento'||v_id_movimiento||')');
        v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento',v_id_movimiento::varchar);

        --Devuelve la respuesta
        return v_resp;

  end;

  /*********************************
  #TRANSACCION:  'SKA_MOV_MOD'
  #DESCRIPCION: Modificacion de registros
  #AUTOR:   admin
  #FECHA:   22-10-2015 20:42:41
  ***********************************/

  elsif(p_transaccion='SKA_MOV_MOD')then

    begin

        --Verificar bandera para registro masivo de Transferencias y Devoluciones
        v_reg_masivo = false;
        if coalesce(v_parametros.tipo_asig,'seleccionar') = 'todos' then
            v_reg_masivo = true;
        end if;

        --Edicion solo para estado borrador
        if not exists(select 1 from kaf.tmovimiento
                    where id_movimiento = v_parametros.id_movimiento
                    and estado = 'borrador') then
            raise exception 'No se pueden Modificar los datos porque no esta en Borrador';
        end if;

        --Obtiene los datos actuales del movimiento
        select
           *
        into
            v_rec
        from kaf.tmovimiento
        where id_movimiento = v_parametros.id_movimiento;

        --Verificación de período abierto.
        select id_subsistema into v_id_subsistema
        from segu.tsubsistema
        where codigo = 'KAF';

        --Verificación de período abierto
        select po_id_periodo_subsistema into v_id_periodo_subsistema
        from param.f_get_periodo_gestion(v_parametros.fecha_mov,v_id_subsistema);

        v_res = param.f_verifica_periodo_subsistema_abierto(v_id_periodo_subsistema);

        --Obtiene el usuario responsable del depto.
        if not exists(select 1 from param.tdepto_usuario
              where id_depto = (v_parametros.id_depto)::integer
              and cargo = 'responsable') then
            raise exception 'No es posible guardar el movimiento porque no se ha definido Responsable del Depto. de Activos Fijos';
        end if;

        select id_usuario into v_id_responsable_depto
        from param.tdepto_usuario
        where id_depto = (v_parametros.id_depto)::integer
        and cargo = 'responsable' limit 1;

        if not exists(select 1
                    from segu.tusuario usu
                    inner join orga.vfuncionario_persona fun
                    on fun.id_persona = usu.id_persona
                    where usu.id_usuario = v_id_responsable_depto
                    ) then
            raise exception 'El usuario responsable del Dpto. no está registrado como funcionario';
        end if;

        select fun.id_funcionario
        into v_id_responsable_depto
        from segu.tusuario usu
        inner join orga.vfuncionario_persona fun
        on fun.id_persona = usu.id_persona
        where usu.id_usuario = v_id_responsable_depto;

        if v_id_responsable_depto is not null then
            update kaf.tmovimiento set
            id_responsable_depto = v_id_responsable_depto
            where id_movimiento=v_parametros.id_movimiento;
        end if;

        --Permite el cambio solamente si esta en estado Borrador
        if v_rec.estado = 'borrador' then

            --Sentencia de la modificacion
            update kaf.tmovimiento set
                direccion = v_parametros.direccion,
                fecha_hasta = v_parametros.fecha_hasta,
                id_cat_movimiento = v_parametros.id_cat_movimiento,
                fecha_mov = v_parametros.fecha_mov,
                id_depto = v_parametros.id_depto,
                id_proceso_wf = v_parametros.id_proceso_wf,
                id_estado_wf = v_parametros.id_estado_wf,
                glosa = v_parametros.glosa,
                id_funcionario = v_parametros.id_funcionario,
                estado = v_parametros.estado,
                id_oficina = v_parametros.id_oficina,
                fecha_mod = now(),
                id_usuario_mod = p_id_usuario,
                id_usuario_ai = v_parametros._id_usuario_ai,
                usuario_ai = v_parametros._nombre_usuario_ai,
                id_persona = v_parametros.id_persona,
                codigo=v_parametros.codigo,
                id_deposito=v_parametros.id_deposito,
                id_depto_dest=v_parametros.id_depto_dest,
                id_deposito_dest=v_parametros.id_deposito_dest,
                id_funcionario_dest=v_parametros.id_funcionario_dest,
                id_movimiento_motivo=v_parametros.id_movimiento_motivo,
                prestamo = v_parametros.prestamo,
                fecha_dev_prestamo = v_parametros.fecha_dev_prestamo,
                tipo_movimiento = v_parametros.tipo_movimiento,
                nro_documento = v_parametros.nro_documento,
                tipo_documento = v_parametros.tipo_documento,
                tipo_drepeciacion = v_parametros.tipo_drepeciacion
            where id_movimiento=v_parametros.id_movimiento;

            --Verifica el tipo de movimiento para aplicar reglas
            select
                cat.codigo
            into
                v_cod_movimiento
            from param.tcatalogo cat
            where cat.id_catalogo = v_parametros.id_cat_movimiento;

            if v_cod_movimiento = 'deprec' then
                --Si se cambio de depto se borra el detalle y se lo vuelve a llenar
                if v_rec.id_depto != v_parametros.id_depto then

                    if v_parametros.tipo_drepeciacion = 'deprec_impuesto' then
                          delete from kaf.tmovimiento_af_dep_impuestos
                          where id_movimiento_af in (select id_movimiento_af from kaf.tmovimiento_af where id_movimiento = v_parametros.id_movimiento);
                    else
                      delete from kaf.tmovimiento_af_dep
                      where id_movimiento_af in (select id_movimiento_af from kaf.tmovimiento_af where id_movimiento = v_parametros.id_movimiento);
                    end if;

                    delete from kaf.tmovimiento_af where id_movimiento = v_parametros.id_movimiento;

                    insert into kaf.tmovimiento_af(
                        id_movimiento,
                        id_activo_fijo,
                        id_cat_estado_fun,
                        estado_reg,
                        fecha_reg,
                        id_usuario_reg,
                        fecha_mod
                    )
                    select
                        v_parametros.id_movimiento,
                        afij.id_activo_fijo,
                        afij.id_cat_estado_fun,
                        'activo',
                        now(),
                        p_id_usuario,
                        null
                        from kaf.tactivo_fijo afij
                        where afij.estado = 'alta'
                        and afij.id_depto = v_parametros.id_depto
                        and ((afij.fecha_ult_dep is null and afij.fecha_ini_dep < v_parametros.fecha_hasta) or (afij.fecha_ult_dep < v_parametros.fecha_hasta));

                end if;

            elsif v_cod_movimiento = 'transf' then

                if v_rec.id_funcionario != v_parametros.id_funcionario then

                    delete from kaf.tmovimiento_af where id_movimiento = v_parametros.id_movimiento;

                    if v_reg_masivo then

                        --Registra todos los activos del funcionario origen
                        insert into kaf.tmovimiento_af(
                            id_movimiento,
                            id_activo_fijo,
                            id_cat_estado_fun,
                            estado_reg,
                            fecha_reg,
                            id_usuario_reg,
                            fecha_mod
                        )
                        select
                        v_parametros.id_movimiento,
                        afij.id_activo_fijo,
                        afij.id_cat_estado_fun,
                        'activo',
                        now(),
                        p_id_usuario,
                        null
                        from kaf.tactivo_fijo afij
                        where afij.id_funcionario = v_parametros.id_funcionario
                        and afij.estado = 'alta'
                        and afij.en_deposito = 'no';

                    end if;



                end if;


            elsif v_cod_movimiento = 'devol' then

                if v_rec.id_funcionario != v_parametros.id_funcionario then

                    delete from kaf.tmovimiento_af where id_movimiento = v_parametros.id_movimiento;

                    if v_reg_masivo then

                        --Registra todos los activos del funcionario origen
                        insert into kaf.tmovimiento_af(
                            id_movimiento,
                            id_activo_fijo,
                            id_cat_estado_fun,
                            estado_reg,
                            fecha_reg,
                            id_usuario_reg,
                            fecha_mod
                        )
                        select
                        v_parametros.id_movimiento,
                        afij.id_activo_fijo,
                        afij.id_cat_estado_fun,
                        'activo',
                        now(),
                        p_id_usuario,
                        null
                        from kaf.tactivo_fijo afij
                        where afij.id_funcionario = v_parametros.id_funcionario
                        and afij.estado = 'alta'
                        and afij.en_deposito = 'no';

                    end if;

                end if;


            end if;

        else
            raise exception 'Modificacion no permitida, debe estar en Estado Borrador';
        end if;



      --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Movimiento de Activos Fijos modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento',v_parametros.id_movimiento::varchar);

            --Devuelve la respuesta
            return v_resp;

    end;

  /*********************************
  #TRANSACCION:  'SKA_MOV_ELI'
  #DESCRIPCION: Eliminacion de registros
  #AUTOR:   admin
  #FECHA:   22-10-2015 20:42:41
  ***********************************/

  elsif(p_transaccion='SKA_MOV_ELI')then

    begin
          --Verifica existencia del movimiento
            if not exists(select 1 from kaf.tmovimiento
                  where id_movimiento = v_parametros.id_movimiento) then
              raise exception 'Movimiento no encontrado';
            end if;

            --Obtiene los datos actuales del movimiento
            select mov.estado, cat.codigo
            into v_rec
            from kaf.tmovimiento mov
            inner join param.tcatalogo cat on cat.id_catalogo = mov.id_cat_movimiento
            where mov.id_movimiento = v_parametros.id_movimiento;

            if v_rec.estado = 'borrador' then

            	if v_rec.codigo in ('actua','deprec') then
                	--Elimina el detalle
                    delete from kaf.tmovimiento_af where id_movimiento = v_parametros.id_movimiento;

                  	--Sentencia de la eliminacion
                  	delete from kaf.tmovimiento
                  	where id_movimiento=v_parametros.id_movimiento;
                else
                    if exists(select 1 from kaf.tmovimiento_af
                        where id_movimiento = v_parametros.id_movimiento) then
                      raise exception 'Elimine el detalle del Movimiento previamente y vuelva a intentarlo';
                    end if;
                    --Sentencia de la eliminacion
                    delete from kaf.tmovimiento
                    where id_movimiento=v_parametros.id_movimiento;
                end if;
            else
                raise exception 'Eliminacion no permitida, debe estar en Estado Borrador';
            end if;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Movimiento de Activos Fijos eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento',v_parametros.id_movimiento::varchar);

            --Devuelve la respuesta
            return v_resp;

    end;

  /*********************************
  #TRANSACCION: 'SKA_MOVREL_DAT'
  #DESCRIPCION: Rellena masivamente el detalle del movimiento con los activos fijos enviados
  #AUTOR:       RCM
  #FECHA:       14/01/2016
  ***********************************/

  elsif(p_transaccion='SKA_MOVREL_DAT')then

    begin
          --Verifica existencia del movimiento
            if not exists(select 1 from kaf.tmovimiento
                  where id_movimiento = v_parametros.id_movimiento) then
              raise exception 'Movimiento no encontrado';
            end if;

            --Obtiene datos del movimiento
            select
            mov.id_movimiento, cat.codigo as tipo_mov
            into v_rec
            from kaf.tmovimiento mov
            inner join param.tcatalogo cat
            on cat.id_catalogo = mov.id_cat_movimiento
            where id_movimiento = v_parametros.id_movimiento;

            --Generación de registros en el detalle del movimiento por tipo de movimiento
            v_sql = 'insert into kaf.tmovimiento_af(
                id_usuario_reg,fecha_reg,estado_reg,id_movimiento,id_activo_fijo,
                id_cat_estado_fun,id_depto,id_funcionario,id_persona,vida_util,monto_vigente,
                estado
                )
                select '||
                p_id_usuario||','''|| now()||''',''activo'','||v_rec.id_movimiento||',af.id_activo_fijo,
                af.id_cat_estado_fun,af.id_depto,af.id_funcionario,af.id_persona,af.vida_util_original,
                af.monto_vigente,''borrador''
                from kaf.tactivo_fijo af
                where af.id_activo_fijo in (' ||v_parametros.ids||')
                and af.id_activo_fijo not in (select id_activo_fijo
                              from kaf.tmovimiento_af
                                            where id_movimiento = '||v_rec.id_movimiento||')';

            execute v_sql;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Activos fijos registrados masivamente el el movimiento');
            v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento',v_parametros.id_movimiento::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'ids',v_parametros.ids);

            --Devuelve la respuesta
            return v_resp;

    end;

    /*********************************
    #TRANSACCION:  'KAF_SIGEMOV_IME'
    #DESCRIPCION:   funcion que controla el cambio al Siguiente estado del movimiento
    #AUTOR:         RCM
    #FECHA:         30/03/2016
    ***********************************/

    elseif(p_transaccion='KAF_SIGEMOV_IME') then

        begin
            --Obtiene los datos del movimiento
            select
                mov.*,
                cat.codigo as cod_movimiento
            into
                v_movimiento
            from kaf.tmovimiento mov
            inner join param.tcatalogo cat
            on cat.id_catalogo = mov.id_cat_movimiento
            where id_proceso_wf = v_parametros.id_proceso_wf_act;

            --Verificación de período abierto
            select po_id_periodo_subsistema into v_id_periodo_subsistema
            from param.f_get_periodo_gestion(v_movimiento.fecha_mov,v_id_subsistema);
            v_res = param.f_verifica_periodo_subsistema_abierto(v_id_periodo_subsistema);

            --Obtención de la moneda base
            v_id_moneda_base  = param.f_get_moneda_base();

            --Obtención de datos del Estado Actual
            select
                ew.id_tipo_estado,
                te.pedir_obs,
                ew.id_estado_wf
            into
                v_id_tipo_estado,
                v_pedir_obs,
                v_id_estado_wf
            from wf.testado_wf ew
            inner join wf.ttipo_estado te
            on te.id_tipo_estado = ew.id_tipo_estado
            where ew.id_estado_wf = v_parametros.id_estado_wf_act;

            if pxp.f_existe_parametro(p_tabla,'id_depto_wf') then
                v_id_depto = v_parametros.id_depto_wf;
            end if;

            if pxp.f_existe_parametro(p_tabla,'obs') THEN
                v_obs=v_parametros.obs;
            else
                v_obs='---';
            end if;

            --Configuración del acceso directo para la alarma
            v_acceso_directo = '';
            v_clase = '';
            v_parametros_ad = '';
            v_tipo_noti = 'notificacion';
            v_titulo  = 'Visto Bueno';

            select
                codigo
            into
                v_codigo_estado_siguiente
            from wf.ttipo_estado tes
            where tes.id_tipo_estado =  v_parametros.id_tipo_estado;

            if v_codigo_estado_siguiente not in ('finalizado') then
                v_acceso_directo = '../../../sis_kactivos_fijos/vista/movimiento/Movimiento.php';
                v_clase = 'Movimiento';
                v_parametros_ad = '{filtro_directo:{campo:"mov.id_proceso_wf",valor:"'||v_parametros.id_proceso_wf_act::varchar||'"}}';
                v_tipo_noti = 'notificacion';
                v_titulo  = 'Notificacion';
            end if;

            --Obtención id del estaado actual
            v_id_estado_actual =  wf.f_registra_estado_wf(
                v_parametros.id_tipo_estado,
                v_parametros.id_funcionario_wf,
                v_parametros.id_estado_wf_act,
                v_parametros.id_proceso_wf_act,
                p_id_usuario,
                v_parametros._id_usuario_ai,
                v_parametros._nombre_usuario_ai,
                v_id_depto,
                coalesce(v_movimiento.codigo,'--')||' Obs:'||v_obs,
                v_acceso_directo ,
                v_clase,
                v_parametros_ad,
                v_tipo_noti,
                v_titulo
            );

            --Actualiza el estado actual del movimiento
            update kaf.tmovimiento set
            id_estado_wf = v_id_estado_actual,
            estado = v_codigo_estado_siguiente
            where id_movimiento = v_movimiento.id_movimiento;

            --------------------------------------------
            --Acciones por Tipo de Movimiento y Estado
            --------------------------------------------
            if v_movimiento.cod_movimiento = 'alta' then

                if v_codigo_estado_siguiente = 'cbte' then

                    --Obtencion dela plantilla de comprobante
                    v_kaf_cbte = kaf.f_get_plantilla_cbte(v_movimiento.id_movimiento_motivo);

                    --Generación comprobante de depreciación
                    v_id_int_comprobante = conta.f_gen_comprobante (v_movimiento.id_movimiento,
                                                                    v_kaf_cbte ,
                                                                    v_id_estado_actual,
                                                                    p_id_usuario,
                                                                    v_parametros._id_usuario_ai,
                                                                    v_parametros._nombre_usuario_ai);

                    --Se relaciona los comprobantes generados con el movimiento
                    update  kaf.tmovimiento  set
                    id_int_comprobante = v_id_int_comprobante
                    where id_movimiento = v_movimiento.id_movimiento;

                elsif v_codigo_estado_siguiente = 'finalizado' then

                    --Verifica si los comprobantes fueron validados
                    /*if not exists(select 1 from conta.tint_comprobante
                                  where id_int_comprobante = v_movimiento.id_int_comprobante
                                  and estado_reg = 'validado') then
                      raise exception 'El Comprobante contable (ID: %) aún no ha sido validado',v_movimiento.id_int_comprobante;
                    end if;*/

                    --Actualiza estado de activo fijo
                    update kaf.tactivo_fijo set
                    estado = 'alta',
                    codigo = kaf.f_genera_codigo (movaf.id_activo_fijo)
                    from kaf.tmovimiento_af movaf
                    where kaf.tactivo_fijo.id_activo_fijo = movaf.id_activo_fijo
                    and movaf.id_movimiento = v_movimiento.id_movimiento;

                    --Recorrido de los activos fijos dentro del movimiento
                    for v_registros_af_mov in (select
                                                 af.id_activo_fijo,
                                                  af.monto_compra,
                                                  af.vida_util_original,
                                                  af.fecha_ini_dep,
                                                  af.monto_compra,
                                                  af.vida_util_original,
                                                  af.monto_rescate,
                                                  movaf.id_movimiento_af,
                                                  af.codigo,
                                                  af.fecha_compra,
                                                  af.monto_compra_orig_100,
                                                  af.id_moneda_orig
                                              from kaf.tmovimiento_af movaf
                                              inner join kaf.tactivo_fijo af
                                              on af.id_activo_fijo = movaf.id_activo_fijo
                                              where movaf.id_movimiento = v_movimiento.id_movimiento) loop

                        --Recorrido de las monedas configuradas para insertar un registro para cada una
                        for v_registros_mod in (select
                                                    mod.id_moneda_dep,
                                                    mod.id_moneda,
                                                    mod.id_moneda_act
                                                from kaf.tmoneda_dep mod
                                                where mod.estado_reg = 'activo') loop

                            v_monto_compra = param.f_convertir_moneda(v_id_moneda_base, --moneda origen para conversion
                                                                      v_registros_mod.id_moneda,   --moneda a la que sera convertido
                                                                      v_registros_af_mov.monto_compra, --este monto siemrpe estara en moenda base
                                                                      v_registros_af_mov.fecha_ini_dep,
                                                                      'O',-- tipo oficial, venta, compra
                                                                      NULL);--defecto dos decimales

                            v_monto_compra_100 = param.f_convertir_moneda(v_registros_af_mov.id_moneda_orig, --moneda origen para conversion
                                                                          v_registros_mod.id_moneda,   --moneda a la que sera convertido
                                                                          v_registros_af_mov.monto_compra_orig_100, --este monto siemrpe estara en moenda base
                                                                          v_registros_af_mov.fecha_ini_dep,
                                                                          'O',-- tipo oficial, venta, compra
                                                                          NULL);--defecto dos decimales

                            v_monto_rescate = param.f_convertir_moneda(v_id_moneda_base, --moneda origen para conversion
                                                                       v_registros_mod.id_moneda,   --moneda a la que sera convertido
                                                                       v_registros_af_mov.monto_rescate, --este monto siemrpe estara en moenda base
                                                                       v_registros_af_mov.fecha_ini_dep,
                                                                       'O',-- tipo oficial, venta, compra
                                                                       NULL);--defecto dos decimales

                            --Crea el registro de importes
                            insert into kaf.tactivo_fijo_valores(
                                id_usuario_reg,
                                fecha_reg,
                                estado_reg,
                                id_activo_fijo,
                                monto_vigente_orig,
                                vida_util_orig,
                                fecha_ini_dep,
                                depreciacion_mes,
                                depreciacion_per,
                                depreciacion_acum,
                                monto_vigente,
                                vida_util,
                                estado,
                                principal,
                                monto_rescate,
                                id_movimiento_af,
                                tipo,
                                codigo,
                                id_moneda_dep,
                                id_moneda,
                                fecha_inicio,
                                monto_vigente_orig_100
                            ) values(
                                p_id_usuario,
                                now(),
                                'activo',
                                v_registros_af_mov.id_activo_fijo,
                                v_monto_compra,            --  monto_vigente_orig
                                v_registros_af_mov.vida_util_original,      --  vida_util_orig
                                v_registros_af_mov.fecha_ini_dep,           --  fecha_ini_dep
                                0,
                                0,
                                0,
                                v_monto_compra,            --  monto_vigente
                                v_registros_af_mov.vida_util_original,      --  vida_util
                                'activo',
                                'si',
                                v_monto_rescate,           --  monto_rescate
                                v_registros_af_mov.id_movimiento_af,
                                'alta',
                                v_registros_af_mov.codigo,
                                v_registros_mod.id_moneda_dep,
                                v_registros_mod.id_moneda,
                                v_registros_af_mov.fecha_ini_dep,           --  fecha_ini  desde cuando se considera el activo valor
                                v_monto_compra_100
                           );

                        end loop; -- fin loop moneda

                    end loop; -- fin loop movimeinto

                end if;

            elsif v_movimiento.cod_movimiento = 'baja' then

                if v_codigo_estado_siguiente = 'finalizado' then

                    --Verifica si los comprobantes fueron validados
                    /*if not exists(select 1 from conta.tint_comprobante
                                  where id_int_comprobante = v_movimiento.id_int_comprobante
                                  and estado_reg = 'validado') then
                      raise exception 'El Comprobante contable (ID: %) aún no ha sido validado',v_movimiento.id_int_comprobante;
                    end if;*/

                    --Actualiza estado de activo fijo
                    update kaf.tactivo_fijo set
                    estado = 'baja',
                    fecha_baja = mov.fecha_mov
                    from kaf.tmovimiento_af movaf
                    inner join kaf.tmovimiento mov
                    on mov.id_movimiento = movaf.id_movimiento
                    where kaf.tactivo_fijo.id_activo_fijo = movaf.id_activo_fijo
                    and movaf.id_movimiento = v_movimiento.id_movimiento;

                    --Finalización de AFV(s) vigentes (seteando fecha_fin)
                    for v_registros_af_mov in (select id_activo_fijo
                                            from kaf.tmovimiento_af
                                            where id_movimiento = v_movimiento.id_movimiento) loop

                        v_fun = kaf.f_afv_finalizar(p_id_usuario,
                                                  v_registros_af_mov.id_activo_fijo,
                                                  v_movimiento.fecha_mov);
                    end loop;

                elsif v_codigo_estado_siguiente = 'cbte' then

                    --Obtencion dela plantilla de comprobante
                    v_kaf_cbte = kaf.f_get_plantilla_cbte(v_movimiento.id_movimiento_motivo);

                    --Generación comprobante de depreciación
                    v_id_int_comprobante = conta.f_gen_comprobante (v_movimiento.id_movimiento,
                                                                    v_kaf_cbte ,
                                                                    v_id_estado_actual,
                                                                    p_id_usuario,
                                                                    v_parametros._id_usuario_ai,
                                                                    v_parametros._nombre_usuario_ai);

                    --Se relaciona los comprobantes generados con el movimiento
                    update  kaf.tmovimiento  set
                    id_int_comprobante = v_id_int_comprobante
                    where id_movimiento = v_movimiento.id_movimiento;

                end if;

            elsif v_movimiento.cod_movimiento = 'asig' then

                if v_codigo_estado_siguiente = 'finalizado' then

                    --Actualiza estado de activo fijo
                    update kaf.tactivo_fijo set
                    en_deposito = 'no',
                    id_deposito = null,
                    id_funcionario = coalesce(mov.id_funcionario,mov.id_funcionario_dest),
                    id_persona = mov.id_persona,
                    id_oficina = coalesce(mov.id_oficina,kaf.tactivo_fijo.id_oficina),
                    fecha_asignacion = mov.fecha_mov,
                    ubicacion = mov.direccion,
                    prestamo = mov.prestamo,
                    fecha_dev_prestamo = mov.fecha_dev_prestamo
                    from kaf.tmovimiento_af movaf
                    inner join kaf.tmovimiento mov
                    on mov.id_movimiento = movaf.id_movimiento
                    where kaf.tactivo_fijo.id_activo_fijo = movaf.id_activo_fijo
                    and movaf.id_movimiento = v_movimiento.id_movimiento;

                end if;

            -- RAC 03/03/2017
            -- aumento un if para considerar el caso de transferencias finalizadas
            -- y actulizar el responsable del activo
            -- TODO (Validaolo rodrigo)
            -- TODO que pasa cuando tranfieres de un departamento a otro ....????

            elsif v_movimiento.cod_movimiento = 'transf' then

                if v_codigo_estado_siguiente = 'finalizado' then

                    --Actualiza estado de activo fijo
                    update kaf.tactivo_fijo set
                    en_deposito = 'no',
                    id_funcionario = mov.id_funcionario_dest,
                    id_persona = mov.id_persona,
                    id_oficina = coalesce(mov.id_oficina,kaf.tactivo_fijo.id_oficina),
                    fecha_asignacion = mov.fecha_mov,
                    ubicacion = mov.direccion
                    from kaf.tmovimiento_af movaf
                    inner join kaf.tmovimiento mov on mov.id_movimiento = movaf.id_movimiento
                    where kaf.tactivo_fijo.id_activo_fijo = movaf.id_activo_fijo
                    and movaf.id_movimiento = v_movimiento.id_movimiento;

                end if;

            elsif v_movimiento.cod_movimiento = 'devol' then

                if v_codigo_estado_siguiente = 'finalizado' then

                    --Actualiza estado de activo fijo
                    update kaf.tactivo_fijo set
                    en_deposito = 'si',
                    id_funcionario = null,
                    -- id_funcionario = mov.id_responsable_depto,
                    id_persona = null,
                    fecha_asignacion = mov.fecha_mov,
                    ubicacion = 'Depósito',
                    id_deposito = mov.id_deposito
                    from kaf.tmovimiento_af movaf
                    inner join kaf.tmovimiento mov
                    on mov.id_movimiento = movaf.id_movimiento
                    where kaf.tactivo_fijo.id_activo_fijo = movaf.id_activo_fijo
                    and movaf.id_movimiento = v_movimiento.id_movimiento;

                end if;

            elsif v_movimiento.cod_movimiento = 'reval' then

                if v_codigo_estado_siguiente = 'finalizado' then

                    --Verifica si los comprobantes fueron validados
                    /*if not exists(select 1 from conta.tint_comprobante
                                  where id_int_comprobante = v_movimiento.id_int_comprobante
                                  and estado_reg = 'validado') then
                      raise exception 'El Comprobante contable (ID: %) aún no ha sido validado',v_movimiento.id_int_comprobante;
                    end if;*/

                    --------------------------------------------------------------------------------------------------------
                    --  La vida util de un activo es la maxima de activo_fijo_valor
                    --  el precio de activo fijo es la suma se los monto_vigentes, entre depreciacion y revalorizaciones
                    --  para evitar problema de incoherencias estos datos ya NO se actulizaran en la tabla de activo fijo
                    --------------------------------------------------------------------------------------------------------

                    --Incrementa la cantidad de revalorizaciones
                    update kaf.tactivo_fijo set
                    cantidad_revaloriz = cantidad_revaloriz + 1
                    from kaf.tmovimiento_af movaf
                    where kaf.tactivo_fijo.id_activo_fijo = movaf.id_activo_fijo
                    and movaf.id_movimiento = v_movimiento.id_movimiento;

                    --Recorrido de los activos fijos de la revalorización
                    for v_registros_af_mov in (select
                                              af.id_activo_fijo,
                                              af.monto_compra,
                                              af.vida_util_original,
                                              mov.fecha_mov,
                                              maf.importe,
                                              maf.vida_util,
                                              maf.id_movimiento_af,
                                              af.codigo,
                                              af.cantidad_revaloriz,
                                              av.monto_vigente_real_af,
                                              av.vida_util_real_af
                                              from kaf.tmovimiento_af maf
                                              inner join kaf.tmovimiento mov
                                              on mov.id_movimiento = maf.id_movimiento
                                              inner join kaf.tactivo_fijo af
                                              on af.id_activo_fijo = maf.id_activo_fijo
                                              inner join kaf.vactivo_fijo_vigente av
                                              on av.id_activo_fijo = maf.id_activo_fijo
                                              and av.id_moneda = v_id_moneda_base
                                              where maf.id_movimiento = v_movimiento.id_movimiento) loop

                        --Obtener el valor real de la revalorización
                        --RCM 12/12/2017: se coloca directo el importe del movaf para depreciación en boa




                        --v_monto_inc_dec_real = v_registros_af_mov.importe - v_registros_af_mov.monto_vigente_real_af;
                        --v_vida_util_inc_dec_real = v_registros_af_mov.vida_util - v_registros_af_mov.vida_util_real_af;

                        v_monto_inc_dec_real = v_registros_af_mov.importe;  --100%
                        v_vida_util_inc_dec_real = v_registros_af_mov.vida_util; --100%






                        if v_monto_inc_dec_real = 0  then
                          raise exception 'Inc/Dec de la revalorización es cero. Nada que hacer.';
                        end if;

                        --Caso en función del valor vigente
                        if v_registros_af_mov.monto_vigente_real_af <= 1 then
                            ----------
                            --Caso 1
                            ----------
                            --Finalización de AFV(s) vigentes (seteando fecha_fin)
                            v_fun = kaf.f_afv_finalizar(p_id_usuario,
                                                        v_registros_af_mov.id_activo_fijo,
                                                        v_registros_af_mov.fecha_mov);

                            --Creación de los nuevos AFV para la revalorización en todas las monedas
                            v_fun = kaf.f_afv_crear(p_id_usuario,
                                                    v_movimiento.cod_movimiento,
                                                    v_registros_af_mov.id_activo_fijo,
                                                    v_id_moneda_base,
                                                    v_registros_af_mov.id_movimiento_af,
                                                    v_registros_af_mov.fecha_mov,
                                                    v_monto_inc_dec_real,
                                                    v_registros_af_mov.vida_util,
                                                    kaf.f_get_codigo_nuevo_afv(v_registros_af_mov.id_activo_fijo,v_movimiento.cod_movimiento),
                                                    'no');

                        else

                            if v_monto_inc_dec_real > 0 then
                                ----------
                                --Caso 2
                                ----------
                                --Finalización de AFV(s) vigentes (seteando fecha_fin)
                                v_fun = kaf.f_afv_finalizar(p_id_usuario,
                                                          v_registros_af_mov.id_activo_fijo,
                                                          v_registros_af_mov.fecha_mov);

                                --Replicación de AFV(s), con seteo de la nueva vida útil
                                v_fun = kaf.f_afv_replicar(p_id_usuario,
                                                          v_registros_af_mov.id_activo_fijo,
                                                          v_registros_af_mov.id_movimiento_af,
                                                          v_registros_af_mov.vida_util,
                                                          v_registros_af_mov.fecha_mov);

                                --Creación de los nuevos AFV para la revalorización en todas las monedas
                                v_fun = kaf.f_afv_crear(p_id_usuario,
                                                        v_movimiento.cod_movimiento,
                                                        v_registros_af_mov.id_activo_fijo,
                                                        v_id_moneda_base,
                                                        v_registros_af_mov.id_movimiento_af,
                                                        v_registros_af_mov.fecha_mov,
                                                        v_monto_inc_dec_real,
                                                        v_registros_af_mov.vida_util,
                                                        kaf.f_get_codigo_nuevo_afv(v_registros_af_mov.id_activo_fijo,v_movimiento.cod_movimiento),
                                                        'no');

                            else
                                ----------
                                --Caso 3
                                ----------
                                --Finalización de AFV(s) vigentes (seteando fecha_fin)
                                v_fun = kaf.f_afv_finalizar(p_id_usuario,
                                                          v_registros_af_mov.id_activo_fijo,
                                                          v_registros_af_mov.fecha_mov);

                                --Creación de los nuevos AFV para la revalorización en todas las monedas
                                v_fun = kaf.f_afv_crear(p_id_usuario,
                                                        v_movimiento.cod_movimiento,
                                                        v_registros_af_mov.id_activo_fijo,
                                                        v_id_moneda_base,
                                                        v_registros_af_mov.id_movimiento_af,
                                                        v_registros_af_mov.fecha_mov,
                                                        v_monto_inc_dec_real,
                                                        v_registros_af_mov.vida_util,
                                                        kaf.f_get_codigo_nuevo_afv(v_registros_af_mov.id_activo_fijo,v_movimiento.cod_movimiento),
                                                        'si');

                            end if;

                        end if;

                    end loop;

                elsif v_codigo_estado_siguiente = 'cbte' then

                    --Obtencion de la plantilla de comprobante
                    v_kaf_cbte = kaf.f_get_plantilla_cbte(v_movimiento.id_movimiento_motivo);

                    --Generación comprobante de depreciación
                    v_id_int_comprobante = conta.f_gen_comprobante (v_movimiento.id_movimiento,
                                                                      v_kaf_cbte ,
                                                                      v_id_estado_actual,
                                                                      p_id_usuario,
                                                                      v_parametros._id_usuario_ai,
                                                                      v_parametros._nombre_usuario_ai);

                    --Se relaciona los comprobantes generados con el movimiento
                    update  kaf.tmovimiento  set
                    id_int_comprobante = v_id_int_comprobante
                    where id_movimiento = v_movimiento.id_movimiento;

                end if;

            elsif v_movimiento.cod_movimiento = 'deprec' then

                if v_codigo_estado_siguiente = 'generado' then

                    --Generación de la depreciación
                    /*for v_rec in (select id_movimiento_af, id_activo_fijo
                                from kaf.tmovimiento_af
                                where id_movimiento = v_movimiento.id_movimiento) loop
                        v_resp = kaf.f_depreciacion_lineal(p_id_usuario,v_rec.id_activo_fijo,v_movimiento.fecha_hasta,  v_rec.id_movimiento_af);

                        --Actualiza la respuesta en el movimiento
                        update kaf.tmovimiento_af set
                        respuesta = v_resp
                        where id_movimiento_af = v_rec.id_movimiento_af;

                    end loop;*/
                    IF v_movimiento.tipo_drepeciacion = 'deprec_impuesto' THEN
                        v_resp = kaf.f_depreciacion_lineal_v2_f_impuestos(p_id_usuario,v_movimiento.id_movimiento);
                    ELSE
                          v_resp = kaf.f_depreciacion_lineal_v2(p_id_usuario,v_movimiento.id_movimiento);
                    END IF;

                elsif v_codigo_estado_siguiente = 'cbte' then

                    --Obtención del código de plantilla para depreciación
                    v_kaf_cbte = pxp.f_get_variable_global('kaf_cbte_depreciacion');

                    --Generación comprobante de depreciación
                    v_id_int_comprobante = conta.f_gen_comprobante (v_movimiento.id_movimiento,
                                                                    v_kaf_cbte ,
                                                                    v_id_estado_actual,
                                                                    p_id_usuario,
                                                                    v_parametros._id_usuario_ai,
                                                                    v_parametros._nombre_usuario_ai);

                    --Obtención del código de plantilla para aitb/depreciacion
                    v_kaf_cbte_aitb = pxp.f_get_variable_global('kaf_cbte_depreciacion_aitb');

                    --Generación de comprobante de depreciación
                    v_id_int_comprobante_aitb =   conta.f_gen_comprobante (
                                                   v_movimiento.id_movimiento,
                                                   v_kaf_cbte_aitb ,
                                                   v_id_estado_actual,
                                                   p_id_usuario,
                                                   v_parametros._id_usuario_ai,
                                                   v_parametros._nombre_usuario_ai);

                    --Se relaciona los comprobantes generados con el movimiento
                    update  kaf.tmovimiento  set
                    id_int_comprobante = v_id_int_comprobante ,
                    id_int_comprobante_aitb = v_id_int_comprobante_aitb
                    where id_movimiento = v_movimiento.id_movimiento;

                    --Generar comprobante de actualización  (RCM ??????? 28/06/2017)

                elsif v_codigo_estado_siguiente = 'finalizado' then
                    --Verificar si los comprobantes fueron validados
                    /*if not exists(select 1 from conta.tint_comprobante
                                  where id_int_comprobante = v_movimiento.id_int_comprobante_aitb
                                  and estado_reg = 'validado') then
                      raise exception 'El Comprobante de Actualización por AITB aún no ha sido validado (%)',v_movimiento.id_int_comprobante_aitb;
                    end if;
                    if not exists(select 1 from conta.tint_comprobante
                                  where id_int_comprobante = v_movimiento.id_int_comprobante
                                  and estado_reg = 'validado') then
                      raise exception 'El Comprobante de depreciación aún no ha sido validado (%)',v_movimiento.id_int_comprobante;
                    end if;*/

                    --raise exception 'aqui era el error';
                    --Actualiza la última fecha de depreciación
                    update kaf.tactivo_fijo_valores set
                    fecha_ult_dep = mov.fecha_hasta
                    from kaf.tmovimiento_af maf
                    inner join kaf.tmovimiento_af_dep mdep
                    on mdep.id_movimiento_af = maf.id_movimiento_af
                    inner join kaf.tmovimiento mov
                    on mov.id_movimiento = maf.id_movimiento
                    where maf.id_movimiento = v_movimiento.id_movimiento
                    and kaf.tactivo_fijo_valores.id_activo_fijo_valor = mdep.id_activo_fijo_valor;


                end if;

            --RAC 04/04/2017 nuevo tipo de movimeinto
            --los activos no depreciable solo pueden actulizar AITB
            elsif v_movimiento.cod_movimiento = 'actua' then

                if v_codigo_estado_siguiente = 'generado' then

                    --Generacion de la depreciacion
                    /*for v_rec in (select id_movimiento_af, id_activo_fijo
                                from kaf.tmovimiento_af
                                where id_movimiento = v_movimiento.id_movimiento) loop
                        v_resp = kaf.f_depreciacion_lineal(p_id_usuario,v_rec.id_activo_fijo,v_movimiento.fecha_hasta,  v_rec.id_movimiento_af,'NO'); --el ultimo parametro indi
                    end loop;*/
                     IF v_movimiento.tipo_drepeciacion = 'deprec_impuesto' THEN
 	                      v_resp = kaf.f_depreciacion_lineal_v2_f_impuestos(p_id_usuario,v_movimiento.id_movimiento);
                     ELSE
                           v_resp = kaf.f_depreciacion_lineal_v2(p_id_usuario,v_movimiento.id_movimiento);
                     END IF;

                elsif v_codigo_estado_siguiente = 'cbte' then

                    --Obtencion dela plantilla de comprobante
                    v_kaf_cbte = kaf.f_get_plantilla_cbte(v_movimiento.id_movimiento_motivo);

                    --Generación comprobante de depreciación
                    v_id_int_comprobante = conta.f_gen_comprobante (v_movimiento.id_movimiento,
                                                                      v_kaf_cbte ,
                                                                      v_id_estado_actual,
                                                                      p_id_usuario,
                                                                      v_parametros._id_usuario_ai,
                                                                      v_parametros._nombre_usuario_ai);

                    --Se relaciona los comprobantes generados con el movimiento
                    update  kaf.tmovimiento  set
                    id_int_comprobante = v_id_int_comprobante
                    where id_movimiento = v_movimiento.id_movimiento;

                elsif v_codigo_estado_siguiente = 'finalizado' then

                    --Verifica si los comprobantes fueron validados
                    /*if not exists(select 1 from conta.tint_comprobante
                                  where id_int_comprobante = v_movimiento.id_int_comprobante
                                  and estado_reg = 'validado') then
                      raise exception 'El Comprobante contable (ID: %) aún no ha sido validado',v_movimiento.id_int_comprobante;
                    end if;*/
                    --Actualiza la última fecha de actualización
                    update kaf.tactivo_fijo_valores set
                    fecha_ult_dep = mov.fecha_hasta
                    from kaf.tmovimiento_af maf
                    inner join kaf.tmovimiento_af_dep mdep
                    on mdep.id_movimiento_af = maf.id_movimiento_af
                    inner join kaf.tmovimiento mov
                    on mov.id_movimiento = maf.id_movimiento
                    where maf.id_movimiento = v_movimiento.id_movimiento
                    and kaf.tactivo_fijo_valores.id_activo_fijo_valor = mdep.id_activo_fijo_valor;

                end if;

            --RCM 28-06-2017: nuevos movimientos (especiales)
            elsif v_movimiento.cod_movimiento = 'divis' then

                if v_codigo_estado_siguiente = 'finalizado' then

                    --Verifica si los comprobantes fueron validados
                    /*if not exists(select 1 from conta.tint_comprobante
                                  where id_int_comprobante = v_movimiento.id_int_comprobante
                                  and estado_reg = 'validado') then
                      raise exception 'El Comprobante contable (ID: %) aún no ha sido validado',v_movimiento.id_int_comprobante;
                    end if;*/

                    --Finalizar AFV actual colocando fecha_fin
                    v_fun = kaf.f_afv_finalizar(p_id_usuario,
                                                v_registros_af_mov.id_activo_fijo,
                                                v_registros_af_mov.fecha_mov);

                    --Recorrido de los activos fijos de la revalorización
                    for v_registros_af_mov in (select
                                              mov.id_movimiento_af,
                                              af.id_activo_fijo,
                                              af.monto_compra,
                                              af.vida_util_original,
                                              mov.fecha_mov,
                                              maf.importe,
                                              maf.vida_util,
                                              maf.id_movimiento_af,
                                              af.codigo,
                                              af.cantidad_revaloriz,
                                              av.monto_vigente_real_af,
                                              av.vida_util_real_af
                                              from kaf.tmovimiento_af maf
                                              inner join kaf.tmovimiento mov
                                              on mov.id_movimiento = maf.id_movimiento
                                              inner join kaf.tactivo_fijo af
                                              on af.id_activo_fijo = maf.id_activo_fijo
                                              inner join kaf.vactivo_fijo_vigente av
                                              on av.id_activo_fijo = maf.id_activo_fijo
                                              and av.id_moneda = v_id_moneda_base
                                              where maf.id_movimiento = v_movimiento.id_movimiento) loop

                        --Recorre el detalle de los movimientos especiales e inserta los AFV nuevos según la división de valores definida
                        for v_rec_mov_esp in select
                                              importe
                                              from kaf.tmovimiento_af_especial
                                              where id_movimiento_af = v_registros_af_mov.id_movimiento_af loop

                            --Creación de los nuevos AFV para la revalorización en todas las monedas
                            v_fun = kaf.f_afv_crear(p_id_usuario,
                                                    v_movimiento.cod_movimiento,
                                                    v_registros_af_mov.id_activo_fijo,
                                                    v_id_moneda_base,
                                                    v_registros_af_mov.id_movimiento_af,
                                                    v_registros_af_mov.fecha_mov,
                                                    v_rec_mov_esp.importe,
                                                    v_registros_af_mov.vida_util_real_af,
                                                    kaf.f_get_codigo_nuevo_afv(v_registros_af_mov.id_activo_fijo,v_movimiento.cod_movimiento),
                                                    'si');

                        end loop;

                    end loop;

                elsif v_codigo_estado_siguiente = 'cbte' then

                    --Obtencion dela plantilla de comprobante
                    v_kaf_cbte = kaf.f_get_plantilla_cbte(v_movimiento.id_movimiento_motivo);

                    --Generación comprobante de depreciación
                    v_id_int_comprobante = conta.f_gen_comprobante (v_movimiento.id_movimiento,
                                                                      v_kaf_cbte ,
                                                                      v_id_estado_actual,
                                                                      p_id_usuario,
                                                                      v_parametros._id_usuario_ai,
                                                                      v_parametros._nombre_usuario_ai);

                    --Se relaciona los comprobantes generados con el movimiento
                    update  kaf.tmovimiento  set
                    id_int_comprobante = v_id_int_comprobante
                    where id_movimiento = v_movimiento.id_movimiento;

                end if;

            elsif v_movimiento.cod_movimiento = 'desgl' then

                if v_codigo_estado_siguiente = 'finalizado' then

                    --Verifica si los comprobantes fueron validados
                    /*if not exists(select 1 from conta.tint_comprobante
                                  where id_int_comprobante = v_movimiento.id_int_comprobante
                                  and estado_reg = 'validado') then
                      raise exception 'El Comprobante contable (ID: %) aún no ha sido validado',v_movimiento.id_int_comprobante;
                    end if;*/

                    --Recorrido de los activos fijos de la revalorización
                    for v_registros_af_mov in (select
                                              mov.id_movimiento_af,
                                              af.id_activo_fijo,
                                              af.monto_compra,
                                              af.vida_util_original,
                                              mov.fecha_mov,
                                              maf.importe,
                                              maf.vida_util,
                                              maf.id_movimiento_af,
                                              af.codigo,
                                              af.cantidad_revaloriz,
                                              av.monto_vigente_real_af,
                                              av.vida_util_real_af
                                              from kaf.tmovimiento_af maf
                                              inner join kaf.tmovimiento mov
                                              on mov.id_movimiento = maf.id_movimiento
                                              inner join kaf.tactivo_fijo af
                                              on af.id_activo_fijo = maf.id_activo_fijo
                                              inner join kaf.vactivo_fijo_vigente av
                                              on av.id_activo_fijo = maf.id_activo_fijo
                                              and av.id_moneda = v_id_moneda_base
                                              where maf.id_movimiento = v_movimiento.id_movimiento) loop

                        --Obtiene la cantidad de activos fijos previamente relacionados
                        select count(1)+1 into v_numero
                        from kaf.tactivo_fijo
                        where id_activo_fijo_padre = v_registros_af_mov.id_activo_fijo;

                        --Recorre el detalle de los movimientos especiales e inserta los AFV nuevos según la división de valores definida
                        for v_rec_mov_esp in select
                                              importe, id_activo_fijo_valor, id_movimiento_af_especial
                                              from kaf.tmovimiento_af_especial
                                              where id_movimiento_af = v_registros_af_mov.id_movimiento_af loop

                            --Finalización de los AFV desglosados
                            update kaf.tactivo_fijo_valores set
                            fecha_fin = pxp.f_last_day(date_trunc('month', v_movimiento.fecha_mov - interval '1' month)::date),
                            fecha_mod = now(),
                            id_usuario_mod = p_id_usuario
                            where id_activo_fijo_valor = v_rec_mov_esp.id_activo_fijo_valor;

                            --Definición de código derivado
                            v_nuevo_codigo = v_registros_af_mov.codigo||'-DES'||v_numero::varchar;

                            --Creación de nuevos activos fijos de acuerdo al desglose
                            select
                            id_persona,
                            id_proveedor,
                            fecha_compra,
                            --v_parametros.monto_vigente,
                            id_cat_estado_fun,
                            ubicacion,
                            --v_parametros.vida_util,
                            documento,
                            observaciones,
                            --v_parametros.fecha_ult_dep,
                            monto_rescate,
                            denominacion,
                            id_funcionario,
                            id_deposito,
                            v_rec_mov_esp.importe,
                            v_id_moneda_base,
                            '',
                            descripcion,
                            v_id_moneda_base,
                            v_registros_af_mov.fecha_mov,
                            id_cat_estado_compra,
                            v_registros_af_mov.vida_util_real_af,
                            id_clasificacion,
                            id_oficina,
                            id_depto,
                            p_id_usuario,
                            null, -- v_parametros.nombre_usuario_ai,
                            null, --v_parametros.id_usuario_ai
                            codigo_ant,
                            marca,
                            nro_serie,
                            NULL,
                            id_proyecto
                            into v_rec_af
                            from kaf.tactivo_fijo
                            where id_activo_fijo = v_registros_af_mov.id_activo_fijo;

                            --Inserción del registro
                            v_id_activo_fijo = kaf.f_insercion_af(p_id_usuario, hstore(v_rec_af));

                            --Relación del nuevo activo fijo con el movimiento especial
                            update kaf.tmovimiento_af_especial set
                            id_activo_fijo_creado = v_id_activo_fijo
                            where id_movimiento_af_especial = v_rec_mov_esp.id_movimiento_af_especial;

                            --Actualización del nuevo activo fijo
                            update kaf.tactivo_fijo set
                            codigo = v_nuevo_codigo,
                            id_activo_fijo_padre = v_registros_af_mov.id_activo_fijo
                            where id_activo_fijo = v_id_activo_fijo;

                            v_numero = v_numero + 1;

                        end loop;


                    end loop;

                elsif v_codigo_estado_siguiente = 'cbte' then

                    --Obtencion dela plantilla de comprobante
                    v_kaf_cbte = kaf.f_get_plantilla_cbte(v_movimiento.id_movimiento_motivo);

                    --Generación comprobante de depreciación
                    v_id_int_comprobante = conta.f_gen_comprobante (v_movimiento.id_movimiento,
                                                                      v_kaf_cbte ,
                                                                      v_id_estado_actual,
                                                                      p_id_usuario,
                                                                      v_parametros._id_usuario_ai,
                                                                      v_parametros._nombre_usuario_ai);

                    --Se relaciona los comprobantes generados con el movimiento
                    update  kaf.tmovimiento  set
                    id_int_comprobante = v_id_int_comprobante
                    where id_movimiento = v_movimiento.id_movimiento;

                end if;

            elsif v_movimiento.cod_movimiento = 'intpar' then

                if v_codigo_estado_siguiente = 'finalizado' then

                    --Verifica si los comprobantes fueron validados
                    /*if not exists(select 1 from conta.tint_comprobante
                                  where id_int_comprobante = v_movimiento.id_int_comprobante
                                  and estado_reg = 'validado') then
                      raise exception 'El Comprobante contable (ID: %) aún no ha sido validado',v_movimiento.id_int_comprobante;
                    end if;*/

                     --Recorrido de los activos fijos de la revalorización
                    for v_registros_af_mov in (select
                                              mov.id_movimiento_af,
                                              af.id_activo_fijo,
                                              af.monto_compra,
                                              af.vida_util_original,
                                              mov.fecha_mov,
                                              maf.importe,
                                              maf.vida_util,
                                              maf.id_movimiento_af,
                                              af.codigo,
                                              af.cantidad_revaloriz,
                                              av.monto_vigente_real_af,
                                              av.vida_util_real_af
                                              from kaf.tmovimiento_af maf
                                              inner join kaf.tmovimiento mov
                                              on mov.id_movimiento = maf.id_movimiento
                                              inner join kaf.tactivo_fijo af
                                              on af.id_activo_fijo = maf.id_activo_fijo
                                              inner join kaf.vactivo_fijo_vigente av
                                              on av.id_activo_fijo = maf.id_activo_fijo
                                              and av.id_moneda = v_id_moneda_base
                                              where maf.id_movimiento = v_movimiento.id_movimiento) loop

                        --Recorre el detalle de los movimientos especiales e inserta los AFV nuevos según la división de valores definida
                        for v_rec_mov_esp in select
                                              importe, id_activo_fijo_valor, id_movimiento_af_especial, id_activo_fijo
                                              from kaf.tmovimiento_af_especial
                                              where id_movimiento_af = v_registros_af_mov.id_movimiento_af loop

                            --Se relaciona el nuevo activo con el AFV
                            update kaf.tactivo_fijo_valores set
                            id_activo_fijo = v_rec_mov_esp.id_activo_fijo
                            where id_activo_fijo_valor = v_rec_mov_esp.id_activo_fijo_valor;

                        end loop;

                    end loop;

                elsif v_codigo_estado_siguiente = 'cbte' then

                    --Obtencion dela plantilla de comprobante
                    v_kaf_cbte = kaf.f_get_plantilla_cbte(v_movimiento.id_movimiento_motivo);

                    --Generación comprobante de depreciación
                    v_id_int_comprobante = conta.f_gen_comprobante (v_movimiento.id_movimiento,
                                                                      v_kaf_cbte ,
                                                                      v_id_estado_actual,
                                                                      p_id_usuario,
                                                                      v_parametros._id_usuario_ai,
                                                                      v_parametros._nombre_usuario_ai);

                    --Se relaciona los comprobantes generados con el movimiento
                    update  kaf.tmovimiento  set
                    id_int_comprobante = v_id_int_comprobante
                    where id_movimiento = v_movimiento.id_movimiento;

                end if;

          elsif v_movimiento.cod_movimiento = 'mejora' then

              if v_codigo_estado_siguiente = 'finalizado' then

                  --Verifica si los comprobantes fueron validados
                  /*if not exists(select 1 from conta.tint_comprobante
                                where id_int_comprobante = v_movimiento.id_int_comprobante
                                and estado_reg = 'validado') then
                    raise exception 'El Comprobante contable (ID: %) aún no ha sido validado',v_movimiento.id_int_comprobante;
                  end if;*/

                    --------------------------------------------------------------------------------------------------------
                    --  La vida util de un activo es la maxima de activo_fijo_valor
                    --  el precio de activo fijo es la suma se los monto_vigentes, entre depreciacion y revalorizaciones
                    --  para evitar problema de incoherencias estos datos ya NO se actulizaran en la tabla de activo fijo
                    --------------------------------------------------------------------------------------------------------

                    --Recorrido de los activos fijos de la mejora
                    for v_registros_af_mov in (select
                                              af.id_activo_fijo,
                                              af.monto_compra,
                                              af.vida_util_original,
                                              mov.fecha_mov,
                                              maf.importe,
                                              maf.vida_util,
                                              maf.id_movimiento_af,
                                              af.codigo,
                                              af.cantidad_revaloriz,
                                              av.monto_vigente_real_af,
                                              av.vida_util_real_af
                                              from kaf.tmovimiento_af maf
                                              inner join kaf.tmovimiento mov
                                              on mov.id_movimiento = maf.id_movimiento
                                              inner join kaf.tactivo_fijo af
                                              on af.id_activo_fijo = maf.id_activo_fijo
                                              inner join kaf.vactivo_fijo_vigente av
                                              on av.id_activo_fijo = maf.id_activo_fijo
                                              and av.id_moneda = v_id_moneda_base
                                              where maf.id_movimiento = v_movimiento.id_movimiento) loop

                        --Obtener el valor real de la mejora
                        --RCM 12/12/2017: se comenta temporalmente para poner el monto del movaf para cuadrar depre boa
                        --v_monto_inc_dec_real = v_registros_af_mov.importe - v_registros_af_mov.monto_vigente_real_af;
                        --v_vida_util_inc_dec_real = v_registros_af_mov.vida_util - v_registros_af_mov.vida_util_real_af;
                        v_monto_inc_dec_real = v_registros_af_mov.importe;
                        v_vida_util_inc_dec_real = v_registros_af_mov.vida_util;

                        if v_monto_inc_dec_real = 0  then
                          raise exception 'La mejora debe ser mayor a cero. Nada que hacer.';
                        end if;

                        --Caso en función del valor vigente
                        if v_registros_af_mov.monto_vigente_real_af <= 1 then
                            ----------
                            --Caso 1
                            ----------
                            --Finalización de AFV(s) vigentes (seteando fecha_fin)
                            v_fun = kaf.f_afv_finalizar(p_id_usuario,
                                                        v_registros_af_mov.id_activo_fijo,
                                                        v_registros_af_mov.fecha_mov);

                            --Creación de los nuevos AFV para la mejora en todas las monedas
                            v_fun = kaf.f_afv_crear(p_id_usuario,
                                                    v_movimiento.cod_movimiento,
                                                    v_registros_af_mov.id_activo_fijo,
                                                    v_id_moneda_base,
                                                    v_registros_af_mov.id_movimiento_af,
                                                    v_registros_af_mov.fecha_mov,
                                                    v_monto_inc_dec_real,
                                                    v_registros_af_mov.vida_util,
                                                    kaf.f_get_codigo_nuevo_afv(v_registros_af_mov.id_activo_fijo,v_movimiento.cod_movimiento),
                                                    'no');

                            --TODO: ¿¿Matar depreciación??

                        else

                            if v_monto_inc_dec_real > 0 then
                                ----------
                                --Caso 2
                                ----------
                                --Finalización de AFV(s) vigentes (seteando fecha_fin)
                                v_fun = kaf.f_afv_finalizar(p_id_usuario,
                                                          v_registros_af_mov.id_activo_fijo,
                                                          v_registros_af_mov.fecha_mov);

                                --Replicación de AFV(s), con seteo de la nueva vida útil
                                v_fun = kaf.f_afv_replicar(p_id_usuario,
                                                          v_registros_af_mov.id_activo_fijo,
                                                          v_registros_af_mov.id_movimiento_af,
                                                          v_registros_af_mov.vida_util,
                                                          v_registros_af_mov.fecha_mov);

                                --Creación de los nuevos AFV para la mejora en todas las monedas
                                v_fun = kaf.f_afv_crear(p_id_usuario,
                                                        v_movimiento.cod_movimiento,
                                                        v_registros_af_mov.id_activo_fijo,
                                                        v_id_moneda_base,
                                                        v_registros_af_mov.id_movimiento_af,
                                                        v_registros_af_mov.fecha_mov,
                                                        v_monto_inc_dec_real,
                                                        v_registros_af_mov.vida_util,
                                                        kaf.f_get_codigo_nuevo_afv(v_registros_af_mov.id_activo_fijo,v_movimiento.cod_movimiento),
                                                        'no');

                            else
                                ----------
                                --Caso 3
                                ----------
                                raise exception 'La mejora debe incrementar el valor vigente del activo. Nada que hacer.';

                            end if;

                        end if;

                    end loop;

                elsif v_codigo_estado_siguiente = 'cbte' then

                    --Obtencion dela plantilla de comprobante
                    v_kaf_cbte = kaf.f_get_plantilla_cbte(v_movimiento.id_movimiento_motivo);

                    --Generación comprobante de depreciación
                    v_id_int_comprobante = conta.f_gen_comprobante (v_movimiento.id_movimiento,
                                                                      v_kaf_cbte ,
                                                                      v_id_estado_actual,
                                                                      p_id_usuario,
                                                                      v_parametros._id_usuario_ai,
                                                                      v_parametros._nombre_usuario_ai);

                    --Se relaciona los comprobantes generados con el movimiento
                    update  kaf.tmovimiento  set
                    id_int_comprobante = v_id_int_comprobante
                    where id_movimiento = v_movimiento.id_movimiento;

                end if;

          elsif v_movimiento.cod_movimiento = 'ajuste' then

              if v_codigo_estado_siguiente = 'finalizado' then

                  --Verifica si los comprobantes fueron validados
                  /*if not exists(select 1 from conta.tint_comprobante
                                where id_int_comprobante = v_movimiento.id_int_comprobante
                                and estado_reg = 'validado') then
                    raise exception 'El Comprobante contable (ID: %) aún no ha sido validado',v_movimiento.id_int_comprobante;
                  end if;*/

                  --Recorrido de los activos fijos de la mejora
                    for v_registros_af_mov in (select
                                              af.id_activo_fijo,
                                              af.monto_compra,
                                              af.vida_util_original,
                                              mov.fecha_mov,
                                              maf.importe,
                                              maf.vida_util,
                                              maf.id_movimiento_af,
                                              af.codigo,
                                              af.cantidad_revaloriz,
                                              av.monto_vigente_real_af,
                                              av.vida_util_real_af
                                              -- breydi.vasquez (10/01/2020) valores nuevos para ajuste
                                              ,maf.vida_util_residual,
                                              maf.deprec_acum_ant,
                                              maf.valor_residual,
                                              maf.monto_vig_actu,
                                              mot.codigo_mov_motivo,
                                              maf.deprec_acu_ges_ant,
                                              mov.tipo_drepeciacion
                                              -- fin breydi.vasquez
                                              from kaf.tmovimiento_af maf
                                              inner join kaf.tmovimiento mov
                                              on mov.id_movimiento = maf.id_movimiento
                                              inner join kaf.tactivo_fijo af
                                              on af.id_activo_fijo = maf.id_activo_fijo
                                              inner join kaf.vactivo_fijo_vigente av
                                              on av.id_activo_fijo = maf.id_activo_fijo
                                              and av.id_moneda = v_id_moneda_base
                                              inner join kaf.tmovimiento_motivo mot on mot.id_movimiento_motivo = mov.id_movimiento_motivo
                                              where maf.id_movimiento = v_movimiento.id_movimiento) loop

                        --Obtener el valor real de la mejora
                        --v_monto_inc_dec_real = v_registros_af_mov.importe - v_registros_af_mov.monto_vigente_real_af;
                        --v_vida_util_inc_dec_real = v_registros_af_mov.vida_util - v_registros_af_mov.vida_util_real_af;

                        v_monto_inc_dec_real = v_registros_af_mov.importe;
                        v_vida_util_inc_dec_real = v_registros_af_mov.vida_util;

                -- breydi.vasquez (10/01/2020) valores nuevos para ajuste de vida util
                if (v_registros_af_mov.deprec_acum_ant is not null and v_registros_af_mov.codigo_mov_motivo = 'AJ_VID_UT_PAS') then
						     -- actualizacion de valores para corregir la depreciacion del activo

                             select va.id_activo_fijo_valor into v_id_af_valor_mod
                                from kaf.tactivo_fijo_valores va
                                where va.id_activo_fijo = v_registros_af_mov.id_activo_fijo
                                and va.tipo = 'alta'
                                and va.id_moneda = 1
                                and va.fecha_fin is null;


                              v_mes_fecha =  extract(month from v_registros_af_mov.fecha_mov)::integer;
                              if v_mes_fecha = 12 then
                                  v_anio = extract(year from v_registros_af_mov.fecha_mov)::integer + 1;
                              else
	                              v_anio = extract(year from v_registros_af_mov.fecha_mov)::integer;
                              end if;

                              update kaf.tactivo_fijo_valores
                              set
                              vida_util_corregido = v_registros_af_mov.vida_util,
                              vida_util_resid_corregido = v_registros_af_mov.vida_util_residual,
                              valor_residual = v_registros_af_mov.valor_residual,
                              deprec_acum_ant = v_registros_af_mov.deprec_acum_ant,
                              tipo_modificacion = 'ajuste_vida',
                              control_ajuste_vida = (('01-12-'||v_anio)::date),
                              fecha_ajuste = v_registros_af_mov.fecha_mov
                              where id_activo_fijo_valor = v_id_af_valor_mod;

                              if v_mes_fecha = 12 then
                                if v_registros_af_mov.tipo_drepeciacion = 'deprec_impuesto' then

                                  update kaf.tmovimiento_af_dep_impuestos
                                  set
                                      tipo_modificacion = 'ajuste_vida',
                                      depreciacion_acum_corregido	 = v_registros_af_mov.deprec_acum_ant,
                                      fecha_ajuste_vida = v_registros_af_mov.fecha_mov
                                  where id_activo_fijo_valor = v_id_af_valor_mod
                                  and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_registros_af_mov.fecha_mov))::date);

                                else
                                  update kaf.tmovimiento_af_dep
                                  set
                                      tipo_modificacion = 'ajuste_vida',
                                      depreciacion_acum_corregido	 = v_registros_af_mov.deprec_acum_ant,
                                      fecha_ajuste_vida = v_registros_af_mov.fecha_mov
                                  where id_activo_fijo_valor = v_id_af_valor_mod
                                  and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_registros_af_mov.fecha_mov))::date);
                                end if;

                              else

                              	v_depreciacion_acum_corregido = kaf.f_depre_inversa_mes_truncado(v_registros_af_mov.monto_vig_actu, v_registros_af_mov.valor_residual, v_registros_af_mov.deprec_acum_ant, v_registros_af_mov.vida_util_residual, v_registros_af_mov.fecha_mov);

                                if v_registros_af_mov.tipo_drepeciacion = 'deprec_impuesto' then

                                  update kaf.tmovimiento_af_dep_impuestos
                                  set
                                      tipo_modificacion = 'ajuste_vida',
                                      depreciacion_acum_corregido	 = v_depreciacion_acum_corregido,
                                      fecha_ajuste_vida = v_registros_af_mov.fecha_mov
                                  where id_activo_fijo_valor = v_id_af_valor_mod
                                  and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_registros_af_mov.fecha_mov)::integer -1 )::date);

                                else
                                  update kaf.tmovimiento_af_dep
                                  set
                                      tipo_modificacion = 'ajuste_vida',
                                      depreciacion_acum_corregido	 = v_depreciacion_acum_corregido,
                                      fecha_ajuste_vida = v_registros_af_mov.fecha_mov
                                  where id_activo_fijo_valor = v_id_af_valor_mod
                                  and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_registros_af_mov.fecha_mov)::integer -1 )::date);
                                end if;

                              end if;
                    elsif v_registros_af_mov.codigo_mov_motivo = 'AJ_ACT_PAS'then
                             select va.id_activo_fijo_valor into v_id_af_valor_mod
                                from kaf.tactivo_fijo_valores va
                                where va.id_activo_fijo = v_registros_af_mov.id_activo_fijo
                                and va.tipo = 'alta'
                                and va.id_moneda = 1
                                and va.fecha_fin is null;


                              v_mes_fecha =  extract(month from v_registros_af_mov.fecha_mov)::integer;
                              if v_mes_fecha = 12 then
                                  v_anio = extract(year from v_registros_af_mov.fecha_mov)::integer + 1;
                              else
	                              v_anio = extract(year from v_registros_af_mov.fecha_mov)::integer;
                              end if;

                              update kaf.tactivo_fijo_valores
                              set
                              vida_util_corregido = v_registros_af_mov.vida_util,
                              vida_util_resid_corregido = v_registros_af_mov.vida_util_residual,
                              valor_residual = v_registros_af_mov.valor_residual,
                              deprec_acum_ant = v_registros_af_mov.deprec_acum_ant,
                              tipo_modificacion = 'ajuste_pas_act',
                              control_ajuste_vida = (('01-12-'||v_anio)::date),
                              fecha_ajuste = v_registros_af_mov.fecha_mov,
                              monto_vig_actu_mod = v_registros_af_mov.monto_vig_actu
                              where id_activo_fijo_valor = v_id_af_valor_mod;

                              if v_mes_fecha = 12 then
                                if v_registros_af_mov.tipo_drepeciacion = 'deprec_impuesto' then

                                    update kaf.tmovimiento_af_dep_impuestos
                                    set
                                        tipo_modificacion = 'ajuste_pas_act',
                                        depreciacion_acum_corregido	 = v_registros_af_mov.deprec_acum_ant,
                                        fecha_ajuste_vida = v_registros_af_mov.fecha_mov
                                    where id_activo_fijo_valor = v_id_af_valor_mod
                                    and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_registros_af_mov.fecha_mov))::date);

                                  else
                                    update kaf.tmovimiento_af_dep
                                    set
                                        tipo_modificacion = 'ajuste_pas_act',
                                        depreciacion_acum_corregido	 = v_registros_af_mov.deprec_acum_ant,
                                        fecha_ajuste_vida = v_registros_af_mov.fecha_mov
                                    where id_activo_fijo_valor = v_id_af_valor_mod
                                    and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_registros_af_mov.fecha_mov))::date);
                                  end if;
                              else

                                if v_registros_af_mov.deprec_acu_ges_ant is null or v_registros_af_mov.vida_util_residual > 0 then
                                   v_depreciacion_acum_corregido = kaf.f_depre_inversa_mes_truncado(v_registros_af_mov.monto_vig_actu, v_registros_af_mov.valor_residual, v_registros_af_mov.deprec_acum_ant, v_registros_af_mov.vida_util_residual, v_registros_af_mov.fecha_mov);
                                   if v_registros_af_mov.tipo_drepeciacion = 'deprec_impuesto' then
                                       update kaf.tmovimiento_af_dep_impuestos
                                       set
                                         tipo_modificacion = 'ajuste_pas_act',
                                         depreciacion_acum_corregido	 = v_depreciacion_acum_corregido,
                                         fecha_ajuste_vida = v_registros_af_mov.fecha_mov
                                     where id_activo_fijo_valor = v_id_af_valor_mod
                                     and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_registros_af_mov.fecha_mov)::integer -1 )::date);

                                   else

                                      update kaf.tmovimiento_af_dep
                                      set
                                        tipo_modificacion = 'ajuste_pas_act',
                                        depreciacion_acum_corregido	 = v_depreciacion_acum_corregido,
                                        fecha_ajuste_vida = v_registros_af_mov.fecha_mov
                                    where id_activo_fijo_valor = v_id_af_valor_mod
                                    and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_registros_af_mov.fecha_mov)::integer -1 )::date);

                                  end if;
								else
                                if v_registros_af_mov.tipo_drepeciacion = 'deprec_impuesto' then
                                    update kaf.tmovimiento_af_dep_impuestos
                                    set
                                      tipo_modificacion = 'ajuste_pas_act',
                                      depreciacion_acum_corregido	 = v_registros_af_mov.deprec_acu_ges_ant,
                                      fecha_ajuste_vida = v_registros_af_mov.fecha_mov
                                  where id_activo_fijo_valor = v_id_af_valor_mod
                                  and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_registros_af_mov.fecha_mov)::integer -1 )::date);
                                else
                                    update kaf.tmovimiento_af_dep
                                    set
                                      tipo_modificacion = 'ajuste_pas_act',
                                      depreciacion_acum_corregido	 = v_registros_af_mov.deprec_acu_ges_ant,
                                      fecha_ajuste_vida = v_registros_af_mov.fecha_mov
                                  where id_activo_fijo_valor = v_id_af_valor_mod
                                  and date_trunc('month',fecha) = date_trunc('month',('01-12-'||extract(year from v_registros_af_mov.fecha_mov)::integer -1 )::date);
                                end if;
                              end if;
                            end if;
                        elsif v_registros_af_mov.codigo_mov_motivo = 'AJ_VID_RES' then

                          v_fun = kaf.f_afv_crear(p_id_usuario,
                                                    v_movimiento.cod_movimiento,
                                                    v_registros_af_mov.id_activo_fijo,
                                                    v_id_moneda_base,
                                                    v_registros_af_mov.id_movimiento_af,
                                                    v_registros_af_mov.fecha_mov,
                                                    v_monto_inc_dec_real,
                                                    v_registros_af_mov.vida_util,
                                                    kaf.f_get_codigo_nuevo_afv(v_registros_af_mov.id_activo_fijo,v_movimiento.cod_movimiento),
                                                    'si');

						  update kaf.tactivo_fijo_valores
                          set
                          tipo_modificacion = 'ajuste_vida_residual',
                          valor_residual = v_monto_inc_dec_real,
                          monto_vigente_orig = 0,
                          monto_vigente_orig_100 = 0,
                          monto_vigente = 0
                          where id_activo_fijo =  v_registros_af_mov.id_activo_fijo
                          and id_moneda = 1
                          and tipo = 'ajuste';

                          update kaf.tactivo_fijo_valores set
                          fecha_fin = pxp.f_last_day(date_trunc('month', v_registros_af_mov.fecha_mov + interval '2' month)::date),
                          fecha_mod = now(),
                          id_usuario_mod = p_id_usuario
                          where id_activo_fijo = v_registros_af_mov.id_activo_fijo
                          and fecha_fin is null;


						  update kaf.tactivo_fijo_valores
                          set
                          tipo_modificacion = 'ajuste_vida_residual'
                          where id_activo_fijo =  v_registros_af_mov.id_activo_fijo
                          and id_moneda = 1
                          and tipo = 'ajuste';

                    	else

                        --Finalización de AFV(s) vigentes (seteando fecha_fin)
                        v_fun = kaf.f_afv_finalizar(p_id_usuario,
                                                    v_registros_af_mov.id_activo_fijo,
                                                    v_registros_af_mov.fecha_mov);

                        --Creación de los nuevos AFV para la mejora en todas las monedas
                        v_fun = kaf.f_afv_crear(p_id_usuario,
                                                v_movimiento.cod_movimiento,
                                                v_registros_af_mov.id_activo_fijo,
                                                v_id_moneda_base,
                                                v_registros_af_mov.id_movimiento_af,
                                                v_registros_af_mov.fecha_mov,
                                                v_monto_inc_dec_real,
                                                v_registros_af_mov.vida_util,
                                                kaf.f_get_codigo_nuevo_afv(v_registros_af_mov.id_activo_fijo,v_movimiento.cod_movimiento),
                                                'si');
                        end if;

                    end loop;

              elsif v_codigo_estado_siguiente = 'cbte' then

                  --Obtencion dela plantilla de comprobante
                  v_kaf_cbte = kaf.f_get_plantilla_cbte(v_movimiento.id_movimiento_motivo);

                  --Generación comprobante de depreciación
                  v_id_int_comprobante = conta.f_gen_comprobante (v_movimiento.id_movimiento,
                                                                    v_kaf_cbte ,
                                                                    v_id_estado_actual,
                                                                    p_id_usuario,
                                                                    v_parametros._id_usuario_ai,
                                                                    v_parametros._nombre_usuario_ai);

                  --Se relaciona los comprobantes generados con el movimiento
                  update  kaf.tmovimiento  set
                  id_int_comprobante = v_id_int_comprobante
                  where id_movimiento = v_movimiento.id_movimiento;

              end if;

          elsif v_movimiento.cod_movimiento = 'retiro' then

              if v_codigo_estado_siguiente = 'finalizado' then

                  --Verifica si los comprobantes fueron validados
                  /*if not exists(select 1 from conta.tint_comprobante
                                where id_int_comprobante = v_movimiento.id_int_comprobante
                                and estado_reg = 'validado') then
                    raise exception 'El Comprobante contable (ID: %) aún no ha sido validado',v_movimiento.id_int_comprobante;
                  end if;*/

                /*modificacion: 28/01/2020
                  author: breyi.vasquez
                  descripcion: nuevo tipo de motivo de retiro, setea la fecha del movimiento
                  en la tabla activo_fijo_valores para el activo regitrado
                */
                select
                	mo.codigo_mov_motivo
                    into v_codigo_retiro
                from kaf.tmovimiento m
                inner join kaf.tmovimiento_motivo mo on mo.id_movimiento_motivo = m.id_movimiento_motivo
                where m.id_movimiento = v_movimiento.id_movimiento;

            if v_codigo_retiro is null or v_codigo_retiro <> 'PAR_RET' then
                --Actualiza estado de activo fijo
                update kaf.tactivo_fijo set
                estado = 'retiro'
                from kaf.tmovimiento_af movaf
                inner join kaf.tmovimiento mov
                on mov.id_movimiento = movaf.id_movimiento
                where kaf.tactivo_fijo.id_activo_fijo = movaf.id_activo_fijo
                and movaf.id_movimiento = v_movimiento.id_movimiento;
            end if;
                --Finalización de AFV(s) vigentes (seteando fecha_fin)
                for v_registros_af_mov in (select maf.id_activo_fijo,
                                                  mo.codigo_mov_motivo,
                                                  maf.id_activo_fijo_valor
                                            from kaf.tmovimiento_af maf
                                            inner join kaf.tmovimiento m on m.id_movimiento = maf.id_movimiento
                                            inner join kaf.tmovimiento_motivo mo on mo.id_movimiento_motivo = m.id_movimiento_motivo
                                            where maf.id_movimiento = v_movimiento.id_movimiento) loop

				   if v_registros_af_mov.codigo_mov_motivo = 'PAR_RET' then

                   	update kaf.tactivo_fijo_valores
                    set
                    fecha_fin = pxp.f_last_day(v_movimiento.fecha_mov),
                    fecha_mod = now(),
					id_usuario_mod = p_id_usuario
                    where id_activo_fijo_valor = v_registros_af_mov.id_activo_fijo_valor;

                   else

                    v_fun = kaf.f_afv_finalizar(p_id_usuario,
                                              v_registros_af_mov.id_activo_fijo,
                                              v_movimiento.fecha_mov);
                    end if;
                end loop;

              elsif v_codigo_estado_siguiente = 'cbte' then

                  --Obtencion dela plantilla de comprobante
                  v_kaf_cbte = kaf.f_get_plantilla_cbte(v_movimiento.id_movimiento_motivo);

                  --Generación comprobante de depreciación
                  v_id_int_comprobante = conta.f_gen_comprobante (v_movimiento.id_movimiento,
                                                                    v_kaf_cbte ,
                                                                    v_id_estado_actual,
                                                                    p_id_usuario,
                                                                    v_parametros._id_usuario_ai,
                                                                    v_parametros._nombre_usuario_ai);

                  --Se relaciona los comprobantes generados con el movimiento
                  update  kaf.tmovimiento  set
                  id_int_comprobante = v_id_int_comprobante
                  where id_movimiento = v_movimiento.id_movimiento;

              end if;

          elsif v_movimiento.cod_movimiento = 'tranfdep' then

               if v_codigo_estado_siguiente = 'finalizado' then

                  --Verifica si los comprobantes fueron validados
                  /*if not exists(select 1 from conta.tint_comprobante
                                where id_int_comprobante = v_movimiento.id_int_comprobante
                                and estado_reg = 'validado') then
                    raise exception 'El Comprobante contable (ID: %) aún no ha sido validado',v_movimiento.id_int_comprobante;
                  end if;*/

                  --Actualiza estado de activo fijo
                  update kaf.tactivo_fijo set
                  id_depto = mov.id_depto_dest,
                  id_deposito = mov.id_deposito_dest
                  from kaf.tmovimiento_af movaf
                  inner join kaf.tmovimiento mov
                  on mov.id_movimiento = movaf.id_movimiento
                  where kaf.tactivo_fijo.id_activo_fijo = movaf.id_activo_fijo
                  and movaf.id_movimiento = v_movimiento.id_movimiento;

              elsif v_codigo_estado_siguiente = 'cbte' then

                  --Obtencion dela plantilla de comprobante
                  v_kaf_cbte = kaf.f_get_plantilla_cbte(v_movimiento.id_movimiento_motivo);

                  --Generación comprobante de depreciación
                  v_id_int_comprobante = conta.f_gen_comprobante (v_movimiento.id_movimiento,
                                                                    v_kaf_cbte ,
                                                                    v_id_estado_actual,
                                                                    p_id_usuario,
                                                                    v_parametros._id_usuario_ai,
                                                                    v_parametros._nombre_usuario_ai);

                  --Se relaciona los comprobantes generados con el movimiento
                  update  kaf.tmovimiento  set
                  id_int_comprobante = v_id_int_comprobante
                  where id_movimiento = v_movimiento.id_movimiento;

              end if;

          elsif v_movimiento.cod_movimiento = 'transito' then

            if v_codigo_estado_siguiente = 'finalizado' then

                --Verifica si los comprobantes fueron validados
                /*if not exists(select 1 from conta.tint_comprobante
                              where id_int_comprobante = v_movimiento.id_int_comprobante
                              and estado_reg = 'validado') then
                  raise exception 'El Comprobante contable (ID: %) aún no ha sido validado',v_movimiento.id_int_comprobante;
                end if;*/

                --Actualiza estado de activo fijo
                update kaf.tactivo_fijo set
                estado = 'transito'
                from kaf.tmovimiento_af movaf
                inner join kaf.tmovimiento mov
                on mov.id_movimiento = movaf.id_movimiento
                where kaf.tactivo_fijo.id_activo_fijo = movaf.id_activo_fijo
                and movaf.id_movimiento = v_movimiento.id_movimiento;

            elsif v_codigo_estado_siguiente = 'cbte' then

                --Obtencion dela plantilla de comprobante
                v_kaf_cbte = kaf.f_get_plantilla_cbte(v_movimiento.id_movimiento_motivo);

                --Generación comprobante de depreciación
                v_id_int_comprobante = conta.f_gen_comprobante (v_movimiento.id_movimiento,
                                                                v_kaf_cbte ,
                                                                v_id_estado_actual,
                                                                p_id_usuario,
                                                                v_parametros._id_usuario_ai,
                                                                v_parametros._nombre_usuario_ai);

                --Se relaciona los comprobantes generados con el movimiento
                update  kaf.tmovimiento  set
                id_int_comprobante = v_id_int_comprobante
                where id_movimiento = v_movimiento.id_movimiento;

            end if;

          end if;

          -- si hay mas de un estado disponible  preguntamos al usuario
          v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado del movimiento)');
          v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');


          -- Devuelve la respuesta
          return v_resp;

        end;


    /*********************************
    #TRANSACCION:  'KAF_ANTEMOV_IME'
    #DESCRIPCION:   Transaccion utilizada  pasar a  estados anterior al movimiento
    #AUTOR:         RCM
    #FECHA:         02/04/2016
    ***********************************/

    elseif(p_transaccion='KAF_ANTEMOV_IME') then

        begin

            --Obtiene los datos del movimiento
            select mov.*, cat.codigo as cod_movimiento
            into v_movimiento
            from kaf.tmovimiento mov
            inner join param.tcatalogo cat
            on cat.id_catalogo = mov.id_cat_movimiento
            where id_proceso_wf = v_parametros.id_proceso_wf;

            --Obtiene los datos del Estado Actual
            select
                ew.id_tipo_estado,
                te.pedir_obs,
                ew.id_estado_wf
            into
                v_id_tipo_estado,
                v_pedir_obs,
                v_id_estado_wf
            from wf.testado_wf ew
            inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
            where ew.id_estado_wf = v_parametros.id_estado_wf;


            --------------------------------------------------
            --Retrocede al estado inmediatamente anterior
            -------------------------------------------------
            SELECT
                ps_id_tipo_estado,
                ps_id_funcionario,
                ps_id_usuario_reg,
                ps_id_depto,
                ps_codigo_estado,
                ps_id_estado_wf_ant
            into
                v_id_tipo_estado,
                v_id_funcionario,
                v_id_usuario_reg,
                v_id_depto,
                v_codigo_estado,
                v_id_estado_wf_ant
            FROM wf.f_obtener_estado_ant_log_wf(v_parametros.id_estado_wf);

            --Obtener datos tipo estado
             if v_movimiento.cod_movimiento = 'deprec' then
                if v_codigo_estado = 'borrador' then
                -- tipo depreciacion
                  IF v_movimiento.tipo_drepeciacion = 'deprec_impuesto' THEN
                  --Eliminar registros de la depreciacion
                  delete from kaf.tmovimiento_af_dep_impuestos
                  where id_movimiento_af in (select id_movimiento_af
                                          from kaf.tmovimiento_af
                                          where id_movimiento = v_movimiento.id_movimiento);
                  ELSE
                    --Eliminar registros de la depreciacion
                    delete from kaf.tmovimiento_af_dep
                    where id_movimiento_af in (select id_movimiento_af
                                            from kaf.tmovimiento_af
                                            where id_movimiento = v_movimiento.id_movimiento);
                  END IF;
                end if;
            elsif v_movimiento.cod_movimiento = 'actua' then

                if v_codigo_estado = 'borrador' then
                -- tipo depreciacion
                  IF v_movimiento.tipo_drepeciacion = 'deprec_impuesto' THEN
                  --Eliminar registros de la depreciacion
                  delete from kaf.tmovimiento_af_dep_impuestos
                  where id_movimiento_af in (select id_movimiento_af
                                          from kaf.tmovimiento_af
                                          where id_movimiento = v_movimiento.id_movimiento);
                  ELSE
                    --Eliminar registros de la depreciacion
                    delete from kaf.tmovimiento_af_dep
                    where id_movimiento_af in (select id_movimiento_af
                                            from kaf.tmovimiento_af
                                            where id_movimiento = v_movimiento.id_movimiento);
                  END IF;
                end if;
            end if;


            select
            ew.id_proceso_wf
            into
            v_id_proceso_wf
            from wf.testado_wf ew
            where ew.id_estado_wf= v_id_estado_wf_ant;


            --Configurar acceso directo para la alarma
            v_acceso_directo = '';
            v_clase = '';
            v_parametros_ad = '';
            v_tipo_noti = 'notificacion';
            v_titulo  = 'Notificacion';


            if v_codigo_estado_siguiente not in ('finalizado') then
                v_acceso_directo = '../../../sis_kactivos_fijos/vista/movimiento/Movimiento.php';
                v_clase = 'Movimiento';
                v_parametros_ad = '{filtro_directo:{campo:"mov.id_proceso_wf",valor:"'||v_parametros.id_proceso_wf_act::varchar||'"}}';
                v_tipo_noti = 'notificacion';
                v_titulo  = 'Notificacion';
            end if;


          --Registra nuevo estado
            v_id_estado_actual = wf.f_registra_estado_wf(
                v_id_tipo_estado,
                v_id_funcionario,
                v_parametros.id_estado_wf,
                v_id_proceso_wf,
                p_id_usuario,
                v_parametros._id_usuario_ai,
                v_parametros._nombre_usuario_ai,
                v_id_depto,
                '[RETROCESO] '|| v_parametros.obs,
                v_acceso_directo,
                v_clase,
                v_parametros_ad,
                v_tipo_noti,
                v_titulo
            );

            --Actualiza el estado actual
            select
            codigo
            into v_codigo_estado_siguiente
            from wf.ttipo_estado tes
            inner join wf.testado_wf ew
            on ew.id_tipo_estado = tes.id_tipo_estado
            where ew.id_estado_wf = v_id_estado_actual;

            update kaf.tmovimiento set
            id_estado_wf = v_id_estado_actual,
            estado = v_codigo_estado_siguiente
            where id_movimiento = v_movimiento.id_movimiento;


            -- si hay mas de un estado disponible  preguntamos al usuario
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se retorocedio el estado del movimiento');
            v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');


            --Devuelve la respuesta
            return v_resp;

        end;

  /*********************************
  #TRANSACCION:  'SKA_MOVRAP_INS'
  #DESCRIPCION:   Generación de los movimientos rápidos
  #AUTOR:         RCM
  #FECHA:         03/08/2017
  ***********************************/

  elseif(p_transaccion='SKA_MOVRAP_INS') then

      begin
          --Inicialización de variables
          v_ids_mov='';
          v_cods_mov='';

          --Verificación de los activos fijos recibidos
          if coalesce(v_parametros.ids_af,'')='' then
            raise exception 'Movimientos no creados. No se recibieron Activos Fijos';
          end if;

          --Obtención del id del movimiento a realizar
          select
          cat.id_catalogo, cat.codigo, cat.descripcion,mom.id_movimiento_motivo
          into
          v_id_cat_movimiento, v_cod_movimiento, v_desc_movimiento,v_id_movi_motivo
          from param.tcatalogo_tipo ctip
          inner join param.tcatalogo cat
          on cat.id_catalogo_tipo = ctip.id_catalogo_tipo
          inner join kaf.tmovimiento_motivo mom
          on mom.id_cat_movimiento = cat.id_catalogo
          where ctip.tabla = 'tmovimiento__id_cat_movimiento'
          and cat.codigo = v_parametros.tipo_movimiento;


          for v_rec in execute('select distinct id_depto
                              from kaf.tactivo_fijo
                              where id_activo_fijo in ('||v_parametros.ids_af||')') loop
              v_cod_mov_aux='';
              --Creación de la cabecera
              select
              coalesce(v_parametros.fecha_mov,null) as fecha_mov,
              coalesce(v_parametros.glosa,null) as glosa,
              coalesce(v_parametros.id_funcionario,null) as id_funcionario,
              coalesce(v_parametros.id_funcionario_dest,null) as id_funcionario_dest,
              coalesce(v_parametros.direccion,null) as direccion,
              coalesce(v_parametros.id_oficina,null) as id_oficina,
              coalesce(v_parametros.id_depto, null) as id_depto,
              coalesce(v_parametros.id_deposito, null) as id_deposito,
              coalesce(v_id_movi_motivo, null) as id_movimiento_motivo,
              v_id_cat_movimiento as id_cat_movimiento,
              v_rec.id_depto as id_depto,
              false as reg_masivo
              into v_rec_af;

			  if v_parametros.tipo_movimiento = 'asig' then
				v_rec_af.id_funcionario := v_parametros.id_funcionario_dest;
                v_rec_af.id_funcionario_dest := null;
              end if;

              --Inserción del movimiento
              v_id_movimiento = kaf.f_insercion_movimiento(p_id_usuario, hstore(v_rec_af));
              v_ids_mov = v_ids_mov || v_id_movimiento::varchar;

              select num_tramite into v_cod_mov_aux
              from kaf.tmovimiento
              where id_movimiento = v_id_movimiento;

              v_cods_mov = v_cods_mov || v_cod_mov_aux || ' ,';

              for v_rec_af in execute('select id_activo_fijo
                              from kaf.tactivo_fijo
                              where id_activo_fijo in ('||v_parametros.ids_af||')
                              and id_depto = '||v_rec.id_depto) loop

                --Se setea los datos para llamar a la función de inserción
                select
                coalesce(v_id_movimiento,null) as id_movimiento,
                coalesce(v_rec_af.id_activo_fijo,null) as id_activo_fijo,
                null as id_movimiento_motivo,
                null as importe,
                null as vida_util,
                coalesce(v_parametros._nombre_usuario_ai,null) as _nombre_usuario_ai,
                coalesce(v_parametros._id_usuario_ai,null) as _id_usuario_ai,
                null as depreciacion_acum
                into v_registros;

                --Inserción del movimiento
                v_id_movimiento_af = kaf.f_insercion_movimiento_af(p_id_usuario, hstore(v_registros));

              end loop;

          end loop;

          --Caso por tipo de movimiento
          if v_parametros.tipo_movimiento = 'asig' then

          elsif v_parametros.tipo_movimiento = 'transf' then
          elsif v_parametros.tipo_movimiento = 'dev' then
          end if;

          --RESPUESTA
          v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se crearon los siguientes Movimientos: '||v_cods_mov);
          v_resp = pxp.f_agrega_clave(v_resp,'ids_movimiento',v_ids_mov);
          v_resp = pxp.f_agrega_clave(v_resp,'cods_movimiento',v_cods_mov);

          return v_resp;

      end;

  else

      raise exception 'Transaccion inexistente: %',p_transaccion;

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
