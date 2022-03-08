CREATE OR REPLACE FUNCTION kaf.ft_clasificacion_variable_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Activos Fijos
 FUNCION: 		kaf.ft_clasificacion_variable_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'kaf.tclasificacion_variable'
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

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_clasificacion_variable	integer;
    v_id_clasificacion_partida  integer;
    v_clasifica_partida			integer[];
    v_conta						integer;
    v_id_gestion_destino	    integer;
    v_registros_ges				record;
    v_registros					record;
    v_tipo						text;
    
			    
BEGIN

    v_nombre_funcion = 'kaf.ft_clasificacion_variable_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'SKA_CLAVAR_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		27-06-2017 09:34:29
	***********************************/

	if(p_transaccion='SKA_CLAVAR_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into kaf.tclasificacion_variable(
			id_clasificacion,
			nombre,
			tipo_dato,
			descripcion,
			estado_reg,
			obligatorio,
			orden_var,
			id_usuario_ai,
			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			id_usuario_mod,
			fecha_mod
          	) values(
			v_parametros.id_clasificacion,
			v_parametros.nombre,
			v_parametros.tipo_dato,
			v_parametros.descripcion,
			'activo',
			v_parametros.obligatorio,
			v_parametros.orden_var,
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			null,
			null
							
			
			
			)RETURNING id_clasificacion_variable into v_id_clasificacion_variable;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Variables almacenado(a) con exito (id_clasificacion_variable'||v_id_clasificacion_variable||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_clasificacion_variable',v_id_clasificacion_variable::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'SKA_CLAVAR_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		27-06-2017 09:34:29
	***********************************/

	elsif(p_transaccion='SKA_CLAVAR_MOD')then

		begin
			--Sentencia de la modificacion
			update kaf.tclasificacion_variable set
			id_clasificacion = v_parametros.id_clasificacion,
			nombre = v_parametros.nombre,
			tipo_dato = v_parametros.tipo_dato,
			descripcion = v_parametros.descripcion,
			obligatorio = v_parametros.obligatorio,
			orden_var = v_parametros.orden_var,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_clasificacion_variable=v_parametros.id_clasificacion_variable;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Variables modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_clasificacion_variable',v_parametros.id_clasificacion_variable::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'SKA_CLAVAR_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		27-06-2017 09:34:29
	***********************************/

	elsif(p_transaccion='SKA_CLAVAR_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from kaf.tclasificacion_variable
            where id_clasificacion_variable=v_parametros.id_clasificacion_variable;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Variables eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_clasificacion_variable',v_parametros.id_clasificacion_variable::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;
	/*********************************    
 	#TRANSACCION:  'SKA_CLASIPAR_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		BVP	
 	#FECHA:		29-10-2018 09:34:29
	***********************************/

	elsif(p_transaccion='SKA_CLASIPAR_INS')then

		begin
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
            select ARRAY(select  
            	id
            into 
            	v_clasifica_partida
            FROM t);
       	--Sentencia de la insercion
        v_conta = 0;

for v_conta in 1.. array_length(v_clasifica_partida,1) loop
	if v_clasifica_partida[v_conta] = v_parametros.id_clasificacion then
    	v_tipo = 'directo';
    else 
    	v_tipo = 'indirecto';
    end if;    
        	insert into kaf.tclasificacion_partida(
                id_clasificacion,
                id_partida,
                id_usuario_ai,
                id_usuario_reg,
                usuario_ai,
                fecha_reg,
                id_usuario_mod,
                fecha_mod,
                id_gestion,
                tipo_nodo
          	) values(
                --v_parametros.id_clasificacion,
                v_clasifica_partida[v_conta],
                v_parametros.id_partida,
                v_parametros._id_usuario_ai,
                p_id_usuario,
                v_parametros._nombre_usuario_ai,
                now(),
                null,
                null,
				v_parametros.id_gestion,
                v_tipo
			)RETURNING id_clasificacion_partida into v_id_clasificacion_partida;
	v_conta = v_conta+1;            
	end loop;			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Variables almacenado(a) con exito (id_clasificacion_partida'||v_id_clasificacion_partida||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_clasificacion_partida',v_id_clasificacion_partida::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;        
	/*********************************    
 	#TRANSACCION:  'SKA_CLASIPAR_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		BVP	
 	#FECHA:		29-10-2018
	***********************************/

	elsif(p_transaccion='SKA_CLASIPAR_MOD')then

		begin
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
            select ARRAY(select  
            	id
            into 
            	v_clasifica_partida
            FROM t);
        v_conta= 0;    
for v_conta in 1.. array_length(v_clasifica_partida,1) loop            
			--Sentencia de la modificacion
  	if v_clasifica_partida[v_conta] = v_parametros.id_clasificacion then
    	v_tipo = 'directo';
    else 
    	v_tipo = 'indirecto';
    end if;          
			update kaf.tclasificacion_partida set
			id_partida = v_parametros.id_partida,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            id_gestion = v_parametros.id_gestion,
            tipo_nodo = v_tipo
			where id_clasificacion=v_clasifica_partida[v_conta];
	v_conta = v_conta+1;            
	end loop;                    
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Filtro Partida modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_clasificacion_partida',v_parametros.id_clasificacion_partida::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'SKA_CLASIPAR_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		BVP	
 	#FECHA:		29-10-2018
	***********************************/

	elsif(p_transaccion='SKA_CLASIPAR_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from kaf.tclasificacion_partida
            where id_clasificacion_partida=v_parametros.id_clasificacion_partida; 
                                        
            --Definicion de la respuesta
            
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Filtro Partida eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_clasificacion_partida',v_parametros.id_clasificacion_partida::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;
  /*********************************    
    #TRANSACCION:  'KAF_CLONAR_IME'
    #DESCRIPCION:     Clona las partidas para la siguiente gestion
    #AUTOR:         BVP
    #FECHA:           29-10-20158 
    ***********************************/

    elsif(p_transaccion='KAF_CLONAR_IME')then

          begin
                  
          --  definir id de la gestion siguiente

         select
            ges.id_gestion,
            ges.gestion,
            ges.id_empresa
         into 
            v_registros_ges
         from 
         param.tgestion ges
         where ges.id_gestion = v_parametros.id_gestion;
          
          
          
         select
            ges.id_gestion
         into 
            v_id_gestion_destino
         from 
         param.tgestion ges
         where       ges.gestion = v_registros_ges.gestion + 1 
                 and ges.id_empresa = v_registros_ges.id_empresa 
                 and ges.estado_reg = 'activo';
           
        IF v_id_gestion_destino is null THEN        
                 raise exception 'no se encontró una siguiente gestión preparada (primero cree  gestión siguiente)';
        END IF;
        v_conta = 0;
            
          --clonamos partidas 
          FOR v_registros in (
                              select clapa.*
                              from kaf.tclasificacion_partida clapa
                              where clapa.id_gestion = v_parametros.id_gestion ) LOOP

                       --insertamos  
                         INSERT INTO  kaf.tclasificacion_partida
                                    (
                                      id_usuario_reg,
                                      fecha_reg,
                                      estado_reg,
                                      id_clasificacion,
                                      id_partida,
                                      id_gestion,
                                      tipo_nodo                                        
                                    )
                                    VALUES (
                                      p_id_usuario,
                                      now(),
                                      'activo',
                                      v_registros.id_clasificacion,
                                      v_registros.id_partida,
                                      v_id_gestion_destino,
                                      v_registros.tipo_nodo                                        
                                    )RETURNING id_clasificacion_partida into v_id_clasificacion_partida;
                      v_conta = v_conta + 1;
                                       
          END LOOP;
        
        
               
          --Definicion de la respuesta
          v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Partidas clonadas para la gestion: '||(extract(year from now()::date)+1)::varchar); 
          v_resp = pxp.f_agrega_clave(v_resp,'observaciones','Se insertaron partidas: '|| v_conta::varchar);
              
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