CREATE OR REPLACE FUNCTION kaf.ft_periodo_anexo_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Activos Fijos
 FUNCION: 		kaf.ft_periodo_anexo_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'kaf.tperiodo_anexo'
 AUTOR: 		 (ivaldivia)
 FECHA:	        19-10-2018 13:39:03
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				19-10-2018 13:39:03								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'kaf.tperiodo_anexo'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'kaf.ft_periodo_anexo_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'KAF_PERANE_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		19-10-2018 13:39:03
	***********************************/

	if(p_transaccion='KAF_PERANE_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						perane.id_periodo_anexo,
						perane.estado_reg,
						perane.nombre_periodo,
						perane.fecha_ini,
						perane.fecha_fin,
						perane.id_gestion,
						perane.observaciones,
                        perane.estado,
						perane.id_usuario_reg,
						perane.fecha_reg,
						perane.id_usuario_ai,
						perane.usuario_ai,
						perane.id_usuario_mod,
						perane.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        ges.gestion as desc_gestion 
						from kaf.tperiodo_anexo perane
						inner join segu.tusuario usu1 on usu1.id_usuario = perane.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = perane.id_usuario_mod
                        inner join param.tgestion ges on ges.id_gestion = perane.id_gestion				   
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;
        
        /*********************************
 	#TRANSACCION:  'KAF_REPORT1_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		05-11-2018 11:30:03
	***********************************/

	elsif(p_transaccion='KAF_REPORT1_SEL')then

    	begin
    		--Sentencia de la consulta
            --raise exception 'LLEGA %',v_parametros.id_periodo_anexo;
			v_consulta:='select
						anex.id_anexo,
						anex.id_partida,
						anex.tipo_anexo,
						anex.id_periodo_anexo,						
						anex.monto_contrato,
						anex.observaciones,
						anex.estado_reg,						
						anex.c31,
						anex.monto_transito,
						anex.monto_pagado,
						anex.detalle_c31,
						anex.monto_alta,	
						anex.fecha_reg,
						anex.usuario_ai,
						anex.id_usuario_reg,
						anex.id_usuario_ai,
						anex.id_usuario_mod,
						anex.fecha_mod,						
                        anex.id_uo,
                        par.codigo as desc_codigo,
                        par.nombre_partida as desc_nombre,
                        anex.monto_tercer,
                        uo.nombre_unidad,
                        anex.seleccionado as control,
                        anex.seleccionado
						from kaf.tanexo anex						
                        inner join pre.tpartida par on par.id_partida = anex.id_partida
                        left join orga.tuo uo on uo.id_uo = anex.id_uo
				        where anex.tipo_anexo = 1 AND anex.id_periodo_anexo = '||v_parametros.id_periodo_anexo;  
                        
                        v_consulta:=v_consulta||'GROUP BY anex.id_anexo,
                        anex.id_partida,
                        par.codigo,
                        par.nombre_partida,
                        uo.nombre_unidad
                        order by desc_codigo asc';
                        
                 
			
			--Devuelve la respuesta
            raise notice 'v_consulta %',v_consulta;
			return v_consulta;

		end;
        
            /*********************************
 	#TRANSACCION:  'KAF_REPORT2_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		05-11-2018 11:30:03
	***********************************/

	elsif(p_transaccion='KAF_REPORT2_SEL')then

    	begin
    		--Sentencia de la consulta
            --raise exception 'LLEGA %',v_parametros.id_periodo_anexo;
			v_consulta:='select
						anex.id_anexo,
						anex.id_partida,
						anex.tipo_anexo,
						anex.id_periodo_anexo,
                        anex.monto_sigep,
						anex.observaciones,						
						
						anex.estado_reg,
						anex.diferencia,
						anex.c31,						
						anex.monto_erp,			
						anex.fecha_reg,
						anex.usuario_ai,
						anex.id_usuario_reg,
						anex.id_usuario_ai,
						anex.id_usuario_mod,
						anex.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,               
                        par.codigo as desc_codigo,
                        par.nombre_partida as desc_nombre,
                        anex.seleccionado as control,
                        anex.seleccionado
						from kaf.tanexo anex
						inner join segu.tusuario usu1 on usu1.id_usuario = anex.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = anex.id_usuario_mod
                        inner join pre.tpartida par on par.id_partida = anex.id_partida
				        where anex.id_periodo_anexo = '||v_parametros.id_periodo_anexo||'AND anex.tipo_anexo = 2
                        order by desc_codigo asc ';  
			
			--Devuelve la respuesta
            --raise notice 'v_consulta %',v_consulta;
			return v_consulta;

		end;
        
        /*********************************
 	#TRANSACCION:  'KAF_REPORT3_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		05-11-2018 11:30:03
	***********************************/

	elsif(p_transaccion='KAF_REPORT3_SEL')then

    	begin
    		--Sentencia de la consulta
            --raise exception 'LLEGA %',v_parametros.id_periodo_anexo;
			v_consulta:='select
						anex.id_anexo,
						anex.id_partida,
						anex.tipo_anexo,
						anex.id_periodo_anexo,
                        anex.monto_erp,						
						anex.estado_reg,
						anex.c31,
                        anex.detalle_c31,                        
						anex.fecha_reg,
						anex.usuario_ai,
						anex.id_usuario_reg,
						anex.id_usuario_ai,
						anex.id_usuario_mod,
						anex.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        anex.id_uo,                        
						par.codigo as desc_codigo,
                        par.nombre_partida as desc_nombre,
						uo.nombre_unidad,
                        anex.seleccionado as control,
                        anex.seleccionado
						from kaf.tanexo anex
						inner join segu.tusuario usu1 on usu1.id_usuario = anex.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = anex.id_usuario_mod
                        inner join pre.tpartida par on par.id_partida = anex.id_partida
                        left join orga.tuo uo on uo.id_uo = anex.id_uo
				        where anex.id_periodo_anexo = '||v_parametros.id_periodo_anexo||'AND anex.tipo_anexo = 3
                        order by desc_codigo asc ';  
			
			--Devuelve la respuesta
            --raise notice 'v_consulta %',v_consulta;
			return v_consulta;

		end;
        
        /*********************************
 	#TRANSACCION:  'KAF_REPORT4_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		05-11-2018 11:30:03
	***********************************/

	elsif(p_transaccion='KAF_REPORT4_SEL')then

    	begin
    		--Sentencia de la consulta
            --raise exception 'LLEGA %',v_parametros.id_periodo_anexo;
			v_consulta:='select
						anex.id_anexo,
						anex.id_partida,
						anex.tipo_anexo,
						anex.id_periodo_anexo,
                        anex.monto_sigep,
                        anex.observaciones,						
						anex.estado_reg,
                        anex.diferencia,
						anex.c31,
                        anex.monto_erp,                        
						anex.fecha_reg,
						anex.usuario_ai,
						anex.id_usuario_reg,
						anex.id_usuario_ai,
						anex.id_usuario_mod,
						anex.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,                                           
						par.codigo as desc_codigo,
                        par.nombre_partida as desc_nombre,
                        anex.seleccionado as control,
                        anex.seleccionado
						from kaf.tanexo anex
						inner join segu.tusuario usu1 on usu1.id_usuario = anex.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = anex.id_usuario_mod
                        inner join pre.tpartida par on par.id_partida = anex.id_partida
				        where anex.id_periodo_anexo = '||v_parametros.id_periodo_anexo||'AND anex.tipo_anexo = 4
                        order by desc_codigo asc ';  
			
			--Devuelve la respuesta
            --raise notice 'v_consulta %',v_consulta;
			return v_consulta;

		end;
	/*********************************
 	#TRANSACCION:  'KAF_REPORTGE_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		05-11-2018 11:30:03
	***********************************/

	elsif(p_transaccion='KAF_REPORTGE_SEL')then

    	begin
    		--Sentencia de la consulta
            --raise exception 'LLEGA %',v_parametros.id_periodo_anexo;
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
				        where parper.id_periodo_anexo = '||v_parametros.id_periodo_anexo||'
                        order by desc_codigo asc ';  
			
			--Devuelve la respuesta
            --raise notice 'v_consulta %',v_consulta;
			return v_consulta;

		end;


	/*********************************
 	#TRANSACCION:  'KAF_PERANE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		19-10-2018 13:39:03
	***********************************/

	elsif(p_transaccion='KAF_PERANE_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_periodo_anexo)
					    from kaf.tperiodo_anexo perane
						inner join segu.tusuario usu1 on usu1.id_usuario = perane.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = perane.id_usuario_mod
                        inner join param.tgestion ges on ges.id_gestion = perane.id_gestion	
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