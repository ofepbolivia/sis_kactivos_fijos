CREATE OR REPLACE FUNCTION kaf.ft_detalle_sigep_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Activos Fijos
 FUNCION: 		kaf.ft_detalle_sigep_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'kaf.tdetalle_sigep'
 AUTOR: 		 (ivaldivia)
 FECHA:	        25-10-2018 15:35:31
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				25-10-2018 15:35:31								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'kaf.tdetalle_sigep'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_detalle_sigep	integer;

BEGIN

    v_nombre_funcion = 'kaf.ft_detalle_sigep_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'KAF_DETSIG_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		25-10-2018 15:35:31
	***********************************/

	if(p_transaccion='KAF_DETSIG_INS')then

        begin
        	--Sentencia de la insercion
        	insert into kaf.tdetalle_sigep(
			estado_reg,
			nro_partida,
			c31,
			monto_sigep,
			id_periodo_anexo,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			rpad(REPLACE(v_parametros.nro_partida, '.', ''),5,'0'),
			v_parametros.c31,
			v_parametros.monto_sigep,
			v_parametros.id_periodo_anexo,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null
			)RETURNING id_detalle_sigep into v_id_detalle_sigep;

             update kaf.tperiodo_anexo per set
             estado = 'Cargado'
             where per.id_periodo_anexo = v_parametros.id_periodo_anexo;


			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle Sigep almacenado(a) con exito (id_detalle_sigep'||v_id_detalle_sigep||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_detalle_sigep',v_id_detalle_sigep::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'KAF_DETSIG_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		25-10-2018 15:35:31
	***********************************/

	elsif(p_transaccion='KAF_DETSIG_MOD')then

		begin
			--Sentencia de la modificacion
			update kaf.tdetalle_sigep set
			nro_partida = v_parametros.nro_partida,
			c31 = v_parametros.c31,
			monto_sigep = v_parametros.monto_sigep,
			id_periodo_anexo = v_parametros.id_periodo_anexo,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_detalle_sigep=v_parametros.id_detalle_sigep;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle Sigep modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_detalle_sigep',v_parametros.id_detalle_sigep::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'KAF_DETSIG_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		25-10-2018 15:35:31
	***********************************/

	elsif(p_transaccion='KAF_DETSIG_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from kaf.tdetalle_sigep
            where id_detalle_sigep=v_parametros.id_detalle_sigep;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Detalle Sigep eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_detalle_sigep',v_parametros.id_detalle_sigep::varchar);

            --Devuelve la respuesta
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
