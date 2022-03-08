CREATE OR REPLACE FUNCTION kaf.ft_partida_periodo_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Activos Fijos
 FUNCION: 		kaf.ft_partida_periodo_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'kaf.tpartida_periodo'
 AUTOR: 		 (ivaldivia)
 FECHA:	        19-10-2018 14:37:17
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				19-10-2018 14:37:17								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'kaf.tpartida_periodo'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_partida_periodo	integer;

BEGIN

    v_nombre_funcion = 'kaf.ft_partida_periodo_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'KAF_PARPER_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		19-10-2018 14:37:17
	***********************************/

	if(p_transaccion='KAF_PARPER_INS')then

        begin
        	--Sentencia de la insercion
        	insert into kaf.tpartida_periodo(
			estado_reg,
			id_periodo_anexo,
			id_partida,
			importe_sigep,
			importe_anexo1,
			importe_anexo2,
			importe_anexo3,
			importe_anexo4,
			importe_anexo5,
			importe_total,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.id_periodo_anexo,
			v_parametros.id_partida,
			v_parametros.importe_sigep,
			v_parametros.importe_anexo1,
			v_parametros.importe_anexo2,
			v_parametros.importe_anexo3,
			v_parametros.importe_anexo4,
			v_parametros.importe_anexo5,
			v_parametros.importe_total,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null



			)RETURNING id_partida_periodo into v_id_partida_periodo;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Partida Periodo almacenado(a) con exito (id_partida_periodo'||v_id_partida_periodo||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_partida_periodo',v_id_partida_periodo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'KAF_PARPER_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		19-10-2018 14:37:17
	***********************************/

	elsif(p_transaccion='KAF_PARPER_MOD')then

		begin
			--Sentencia de la modificacion
			update kaf.tpartida_periodo set
			id_periodo_anexo = v_parametros.id_periodo_anexo,
			id_partida = v_parametros.id_partida,
			importe_sigep = v_parametros.importe_sigep,
			importe_anexo1 = v_parametros.importe_anexo1,
			importe_anexo2 = v_parametros.importe_anexo2,
			importe_anexo3 = v_parametros.importe_anexo3,
			importe_anexo4 = v_parametros.importe_anexo4,
			importe_anexo5 = v_parametros.importe_anexo5,
			importe_total = v_parametros.importe_total,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_partida_periodo=v_parametros.id_partida_periodo;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Partida Periodo modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_partida_periodo',v_parametros.id_partida_periodo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'KAF_PARPER_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		19-10-2018 14:37:17
	***********************************/

	elsif(p_transaccion='KAF_PARPER_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from kaf.tpartida_periodo
            where id_partida_periodo=v_parametros.id_partida_periodo;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Partida Periodo eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_partida_periodo',v_parametros.id_partida_periodo::varchar);

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
