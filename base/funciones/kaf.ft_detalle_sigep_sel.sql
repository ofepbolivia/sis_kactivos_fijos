CREATE OR REPLACE FUNCTION kaf.ft_detalle_sigep_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Activos Fijos
 FUNCION: 		kaf.ft_detalle_sigep_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'kaf.tdetalle_sigep'
 AUTOR: 		 (ivaldivia)
 FECHA:	        25-10-2018 15:35:31
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				25-10-2018 15:35:31								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'kaf.tdetalle_sigep'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'kaf.ft_detalle_sigep_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'KAF_DETSIG_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		25-10-2018 15:35:31
	***********************************/

	if(p_transaccion='KAF_DETSIG_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select           				         
						detsig.id_detalle_sigep,
						detsig.estado_reg,
					    detsig.nro_partida,
						detsig.c31,
						detsig.monto_sigep,
						detsig.id_periodo_anexo,
						detsig.id_usuario_reg,
						detsig.fecha_reg,
						detsig.id_usuario_ai,
						detsig.usuario_ai,
						detsig.id_usuario_mod,
						detsig.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod                                               
						from kaf.tdetalle_sigep detsig
						inner join segu.tusuario usu1 on usu1.id_usuario = detsig.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = detsig.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'KAF_DETSIG_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		25-10-2018 15:35:31
	***********************************/

	elsif(p_transaccion='KAF_DETSIG_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_detalle_sigep),
			            sum(detsig.monto_sigep)
					    from kaf.tdetalle_sigep detsig
					    inner join segu.tusuario usu1 on usu1.id_usuario = detsig.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = detsig.id_usuario_mod
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
	/*********************************
 	#TRANSACCION:  'KAF_REPDETSI_SEL'
 	#DESCRIPCION:	Reporte Sigep 
 	#AUTOR:		BVP
 	#FECHA:		23/11/2018
	***********************************/

	elsif(p_transaccion='KAF_REPDETSI_SEL')then

		begin

			v_consulta:='select		         
                        detsig.nro_partida,
                        detsig.c31,
                        detsig.monto_sigep                                             
                        from kaf.tdetalle_sigep detsig
                        where  detsig.id_periodo_anexo = '||v_parametros.id_periodo_anexo||'
                        order by detsig.nro_partida asc ';

			--Devuelve la respuesta
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