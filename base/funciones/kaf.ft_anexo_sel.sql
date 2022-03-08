CREATE OR REPLACE FUNCTION kaf.ft_anexo_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Activos Fijos
 FUNCION: 		kaf.ft_anexo_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'kaf.tanexo'
 AUTOR: 		 (ivaldivia)
 FECHA:	        22-10-2018 13:08:18
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				22-10-2018 13:08:18								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'kaf.tanexo'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'kaf.ft_anexo_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'KAF_ANEX_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	if(p_transaccion='KAF_ANEX_SEL')then

    	begin
    		--Sentencia de la consulta
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
						anex.monto_erp,	
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
                        anex.monto_tercer,
                        uo.nombre_unidad,
                        anex.seleccionado as control,
                        anex.seleccionado,
                        anex.monto_alta,
                        pe.nombre_periodo,
                        par.nombre_partida
						from kaf.tanexo anex
						inner join segu.tusuario usu1 on usu1.id_usuario = anex.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = anex.id_usuario_mod
                        inner join pre.tpartida par on par.id_partida = anex.id_partida
                        left join orga.tuo uo on uo.id_uo = anex.id_uo
                        inner join kaf.tperiodo_anexo pe on pe.id_periodo_anexo = anex.id_periodo_anexo
				        where ';            				
            			

			--Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by par.codigo '|| v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
		--raise notice '%',v_consulta
			--Devuelve la respuesta
			return v_consulta;

		end;        
        
        /*********************************
 	#TRANSACCION:  'KAF_ANEX1_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX1_SEL')then

    	begin
    		--Sentencia de la consulta
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
                        anex.seleccionado,
                        pe.nombre_periodo,
                        par.nombre_partida
						from kaf.tanexo anex
						inner join segu.tusuario usu1 on usu1.id_usuario = anex.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = anex.id_usuario_mod
                        inner join pre.tpartida par on par.id_partida = anex.id_partida
                        inner join kaf.tperiodo_anexo pe on pe.id_periodo_anexo = anex.id_periodo_anexo
						where  ';            				
            			

			--Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by par.codigo '|| v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;        
        
        /*********************************
 	#TRANSACCION:  'KAF_ANEX2_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX2_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						anex.id_anexo,
						anex.id_partida,
						anex.tipo_anexo,
						anex.id_periodo_anexo,
                        anex.monto_sigep,						
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
                        anex.seleccionado,
                        anex.monto_erp,
                        pe.nombre_periodo,
                        par.nombre_partida
						from kaf.tanexo anex
						inner join segu.tusuario usu1 on usu1.id_usuario = anex.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = anex.id_usuario_mod
                        inner join pre.tpartida par on par.id_partida = anex.id_partida
                        left join orga.tuo uo on uo.id_uo = anex.id_uo
                        inner join kaf.tperiodo_anexo pe on pe.id_periodo_anexo = anex.id_periodo_anexo                        
				        where  ';            				
            			

			--Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by par.codigo '|| v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;        
        
         /*********************************
 	#TRANSACCION:  'KAF_ANEX3_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX3_SEL')then

    	begin
    		--Sentencia de la consulta
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
                        anex.seleccionado,
                        pe.nombre_periodo,
                        par.nombre_partida
						from kaf.tanexo anex
						inner join segu.tusuario usu1 on usu1.id_usuario = anex.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = anex.id_usuario_mod
                        inner join pre.tpartida par on par.id_partida = anex.id_partida
                        inner join kaf.tperiodo_anexo pe on pe.id_periodo_anexo = anex.id_periodo_anexo                        
				        where  ';            				
            			

			--Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by par.codigo '|| v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;       
      
	/*********************************
 	#TRANSACCION:  'KAF_ANEX_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_anexo),
            			sum(anex.monto_contrato),
            			sum(anex.monto_transito), 
                      
                        sum(anex.monto_pagado),
                        sum(anex.monto_erp),
                        sum(anex.monto_tercer)         			
					    from kaf.tanexo anex
						inner join segu.tusuario usu1 on usu1.id_usuario = anex.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = anex.id_usuario_mod
                        inner join pre.tpartida par on par.id_partida = anex.id_partida
                        left join orga.tuo uo on uo.id_uo = anex.id_uo
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;   
        /*********************************
 	#TRANSACCION:  'KAF_ANEX1_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX1_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_anexo),
            			sum(anex.monto_sigep),
                        sum(anex.monto_erp),
                        sum(anex.diferencia)
					    from kaf.tanexo anex
						inner join segu.tusuario usu1 on usu1.id_usuario = anex.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = anex.id_usuario_mod
                        inner join pre.tpartida par on par.id_partida = anex.id_partida
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;    
             /*********************************
 	#TRANSACCION:  'KAF_ANEX2_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX2_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_anexo),
            			sum(anex.monto_erp)
					    from kaf.tanexo anex
						inner join segu.tusuario usu1 on usu1.id_usuario = anex.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = anex.id_usuario_mod
                        inner join pre.tpartida par on par.id_partida = anex.id_partida
                        left join orga.tuo uo on uo.id_uo = anex.id_uo
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;     
         /*********************************
 	#TRANSACCION:  'KAF_ANEX3_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX3_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_anexo),
            			sum(anex.monto_sigep),
                        sum(anex.monto_erp),
                        sum(anex.diferencia)
					    from kaf.tanexo anex
						inner join segu.tusuario usu1 on usu1.id_usuario = anex.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = anex.id_usuario_mod
                        inner join pre.tpartida par on par.id_partida = anex.id_partida
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