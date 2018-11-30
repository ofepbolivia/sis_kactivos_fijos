CREATE OR REPLACE FUNCTION kaf.ft_partida_periodo_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Activos Fijos
 FUNCION: 		kaf.ft_partida_periodo_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'kaf.tpartida_periodo'
 AUTOR: 		 (ivaldivia)
 FECHA:	        19-10-2018 14:37:17
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				19-10-2018 14:37:17								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'kaf.tpartida_periodo'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'kaf.ft_partida_periodo_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'KAF_PARPER_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		19-10-2018 14:37:17
	***********************************/

	if(p_transaccion='KAF_PARPER_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						parper.id_partida_periodo,
						parper.estado_reg,
						parper.id_periodo_anexo,
						parper.id_partida,
						parper.importe_sigep,
						parper.importe_anexo1,
						parper.importe_anexo2,
						parper.importe_anexo3,
						parper.importe_anexo4,
						parper.importe_anexo5,
						parper.importe_total,
						parper.id_usuario_reg,
						parper.fecha_reg,
						parper.id_usuario_ai,
						parper.usuario_ai,
						parper.id_usuario_mod,
						parper.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						par.nombre_partida as desc_partida,
                        par.codigo as desc_codigo
						from kaf.tpartida_periodo parper
						inner join segu.tusuario usu1 on usu1.id_usuario = parper.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = parper.id_usuario_mod
                        inner join pre.tpartida par on par.id_partida = parper.id_partida
				        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'KAF_PARPER_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		19-10-2018 14:37:17
	***********************************/

	elsif(p_transaccion='KAF_PARPER_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select
            			count(id_partida_periodo),
            			sum(parper.importe_sigep),
                        sum(parper.importe_anexo1),
                        sum(parper.importe_anexo2),
                        sum(parper.importe_anexo3),
                        sum(parper.importe_anexo4),
                        sum(parper.importe_anexo5),
                        sum(parper.importe_total)
					    from kaf.tpartida_periodo parper
						inner join segu.tusuario usu1 on usu1.id_usuario = parper.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = parper.id_usuario_mod
                        inner join pre.tpartida par on par.id_partida = parper.id_partida
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

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
