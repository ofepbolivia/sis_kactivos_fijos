CREATE OR REPLACE FUNCTION kaf.ft_clasificacion_variable_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Activos Fijos
 FUNCION: 		kaf.ft_clasificacion_variable_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'kaf.tclasificacion_variable'
 AUTOR: 		 (admin)
 FECHA:	        27-06-2017 09:34:29
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:	
 AUTOR:			
 FECHA:		
***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    v_clasifica_partida varchar;
			    
BEGIN

	v_nombre_funcion = 'kaf.ft_clasificacion_variable_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'SKA_CLAVAR_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		27-06-2017 09:34:29
	***********************************/

	if(p_transaccion='SKA_CLAVAR_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						clavar.id_clasificacion_variable,
						clavar.id_clasificacion,
						clavar.nombre,
						clavar.tipo_dato,
						clavar.descripcion,
						clavar.estado_reg,
						clavar.obligatorio,
						clavar.orden_var,
						clavar.id_usuario_ai,
						clavar.usuario_ai,
						clavar.fecha_reg,
						clavar.id_usuario_reg,
						clavar.id_usuario_mod,
						clavar.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from kaf.tclasificacion_variable clavar
						inner join segu.tusuario usu1 on usu1.id_usuario = clavar.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = clavar.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'SKA_CLAVAR_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		27-06-2017 09:34:29
	***********************************/

	elsif(p_transaccion='SKA_CLAVAR_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_clasificacion_variable)
					    from kaf.tclasificacion_variable clavar
					    inner join segu.tusuario usu1 on usu1.id_usuario = clavar.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = clavar.id_usuario_mod
					    where ';
			
			--Definicion de la respuesta		    
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
        
	/*********************************    
 	#TRANSACCION:  'SKA_CLASIPAR_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		BVP	
 	#FECHA:		29-10-2018 09:34:29
	***********************************/        

	elsif(p_transaccion='SKA_CLASIPAR_SEL')then

		begin
           --recueprar los padres de la rama
              WITH RECURSIVE t(id,id_fk) AS 
            ( 
            SELECT l.id_clasificacion,
                l.id_clasificacion_fk
                FROM kaf.tclasificacion l
                WHERE l.id_clasificacion = v_parametros.id_clasificacion
                UNION ALL
                SELECT l.id_clasificacion, l.id_clasificacion_fk
                FROM kaf.tclasificacion l, t
                WHERE l.id_clasificacion_fk = t.id
                
            )
            select 
            	pxp.list(id::varchar)
            into 
            	v_clasifica_partida
            FROM t; 
            
            v_clasifica_partida = COALESCE(v_clasifica_partida, '0');
            
        
    		--Sentencia de la consulta
			v_consulta:='select 
                         clapa.id_clasificacion_partida,
                         clapa.id_clasificacion,
                         clapa.id_partida,
                         clapa.id_gestion,
                         ges.gestion,
                         clapa.id_usuario_ai,
                         clapa.id_usuario_reg,
                         clapa.usuario_ai,
                         clapa.fecha_reg,
                         clapa.id_usuario_mod,
                         clapa.fecha_mod,
                         usu1.cuenta as usr_reg,
                         usu2.cuenta as usr_mod,
                         (par.codigo ||'' - ''||par.nombre_partida)  as dec_par,                                           
						 clapa.tipo_nodo as tipo_reg
                  from kaf.tclasificacion_partida clapa
                       inner join segu.tusuario usu1 on usu1.id_usuario = clapa.id_usuario_reg
                       left join segu.tusuario usu2 on usu2.id_usuario = clapa.id_usuario_mod
                       inner join pre.tpartida par on par.id_partida = clapa.id_partida
                       inner join param.tgestion ges on ges.id_gestion = clapa.id_gestion
                       where  clapa.id_clasificacion  ='||v_parametros.id_clasificacion||' and ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;
	/*********************************    
 	#TRANSACCION:  'SKA_CLASIPAR_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		BVP	
 	#FECHA:		29-10-2018 22:07:39
	***********************************/

	elsif(p_transaccion='SKA_CLASIPAR_CONT')then

		begin
        
           --recueprar los padres de la rama
              WITH RECURSIVE t(id,id_fk) AS 
            ( 
            SELECT l.id_clasificacion,
                l.id_clasificacion_fk
                FROM kaf.tclasificacion l
                WHERE l.id_clasificacion = v_parametros.id_clasificacion
                UNION ALL
                SELECT l.id_clasificacion, l.id_clasificacion_fk
                FROM kaf.tclasificacion l, t
                WHERE l.id_clasificacion_fk = t.id
                
            )
            select 
            	pxp.list(id::varchar)
            into 
            	v_clasifica_partida
            FROM t;
             
            v_clasifica_partida = COALESCE(v_clasifica_partida, '0');
            
            
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_clasificacion_partida)
                  		from kaf.tclasificacion_partida clapa
                       	inner join segu.tusuario usu1 on usu1.id_usuario = clapa.id_usuario_reg
                       	left join segu.tusuario usu2 on usu2.id_usuario = clapa.id_usuario_mod
                       inner join pre.tpartida par on par.id_partida = clapa.id_partida                        
                       inner join param.tgestion ges on ges.id_gestion = clapa.id_gestion                       
                       	where  clapa.id_clasificacion in ('||v_clasifica_partida||') and ';
			
			--Definicion de la respuesta		    
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end; 
	/*********************************    
 	#TRANSACCION:  'SKA_PARTID_SEL'
 	#DESCRIPCION:	Consulta de datos partidas con gasto
 	#AUTOR:		BVP	
 	#FECHA:		29-10-2018 09:34:29
	***********************************/        

	elsif(p_transaccion='SKA_PARTID_SEL')then

		begin   

            v_consulta:='select
                          par.id_partida,
                          par.tipo,
                          par.codigo,                       
                          par.nombre_partida,
                          par.id_gestion,
                          par.sw_movimiento,
                          ges.gestion							
                          from pre.tpartida par
                          inner join param.tgestion ges on ges.id_gestion = par.id_gestion        
                          where par.tipo=''gasto'' and ';
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;                          
		end;
	/*********************************    
 	#TRANSACCION:  'SKA_PARTID_CONT'
 	#DESCRIPCION:	Conteo de datos partidas con gasto
 	#AUTOR:		BVP	
 	#FECHA:		29-10-2018 09:34:29
	***********************************/        

	elsif(p_transaccion='SKA_PARTID_CONT')then

		begin        
            v_consulta:='select
                          count(par.id_partida)						
                          from pre.tpartida par
                          inner join param.tgestion ges on ges.id_gestion = par.id_gestion                                         
                          where par.tipo=''gasto'' and ';
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