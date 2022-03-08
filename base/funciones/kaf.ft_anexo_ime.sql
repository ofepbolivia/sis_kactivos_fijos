CREATE OR REPLACE FUNCTION kaf.ft_anexo_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Activos Fijos
 FUNCION: 		kaf.ft_anexo_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'kaf.tanexo'
 AUTOR: 		 (ivaldivia)
 FECHA:	        22-10-2018 13:08:18
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				22-10-2018 13:08:18								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'kaf.tanexo'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_anexo				integer;
    v_datos_sigep			record;
    v_datos_erp				record;
    v_partida_c31			record;
    v_diferencia			numeric;
    v_id_perido_anexo		integer;
    v_consulta				varchar;
    v_seleccionado 				varchar;
    v_agrupados				record;	
    v_variables				record;
    v_totales				record;
    v_contador				record;
    v_partida				record;
    v_id_uo					record;
    v_fecha_ini				date;
    v_fecha_fin				date;
    v_anexo_origen			integer;           
    v_total					record;
    v_partidas_per			record;
    v_monto_del				record;
    v_importes				record;
	v_id_partidas			record;
    

BEGIN

    v_nombre_funcion = 'kaf.ft_anexo_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'KAF_ANEX_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	if(p_transaccion='KAF_ANEX_INS')then
    	begin
        	--Sentencia de la insercion
        	insert into kaf.tanexo(
			id_partida,
			tipo_anexo,
			id_periodo_anexo,			
			monto_contrato,
			observaciones,
			estado_reg,			
			c31,
			monto_transito,
			monto_pagado,
			detalle_c31,
			monto_alta,			
			fecha_reg,
			usuario_ai,
			id_usuario_reg,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod,
            id_uo,
            monto_tercer
          	) values(
			v_parametros.id_partida,
			v_parametros.tipo_anexo,
			v_parametros.id_periodo_anexo,		
			v_parametros.monto_contrato,
			v_parametros.observaciones,
			'activo',			
			v_parametros.c31,
			v_parametros.monto_transito,
			v_parametros.monto_pagado,
			v_parametros.detalle_c31,
			v_parametros.monto_alta,		
			now(),
			v_parametros._nombre_usuario_ai,
			p_id_usuario,
			v_parametros._id_usuario_ai,            
			null,
			null,
            v_parametros.id_uo,
            v_parametros.monto_tercer
			)RETURNING id_anexo into v_id_anexo;
            
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anexo almacenado(a) con exito (id_anexo'||v_id_anexo||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_anexo',v_id_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;	
        
        	/*********************************
 	#TRANSACCION:  'KAF_ANEX1_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX1_INS')then

        begin
        	--Sentencia de la insercion
        	insert into kaf.tanexo(
			id_partida,
			tipo_anexo,
			id_periodo_anexo,
			monto_sigep,			
			observaciones,
			estado_reg,
			diferencia,
			c31,			
			monto_erp,			
			fecha_reg,
			usuario_ai,
			id_usuario_reg,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod           
          	) values(
			v_parametros.id_partida,
			v_parametros.tipo_anexo,
			v_parametros.id_periodo_anexo,
			v_parametros.monto_sigep,			
			v_parametros.observaciones,
			'activo',
			v_parametros.diferencia,
			v_parametros.c31,			
			v_parametros.monto_erp,			
			now(),
			v_parametros._nombre_usuario_ai,
			p_id_usuario,
			v_parametros._id_usuario_ai,            
			null,
			null      
			)RETURNING id_anexo into v_id_anexo;
           
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anexo almacenado(a) con exito (id_anexo'||v_id_anexo||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_anexo',v_id_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
        
        /*********************************
 	#TRANSACCION:  'KAF_ANEX2_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX2_INS')then
    	begin
        	--Sentencia de la insercion
        	insert into kaf.tanexo(
			id_partida,
			tipo_anexo,
			id_periodo_anexo,
			monto_erp,			
			estado_reg,		
			c31,		
			detalle_c31,			
			fecha_reg,
			usuario_ai,
			id_usuario_reg,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod,
            id_uo        
          	) values(
			v_parametros.id_partida,
			v_parametros.tipo_anexo,
			v_parametros.id_periodo_anexo,
			v_parametros.monto_erp,			
			'activo',		
			v_parametros.c31,			
			v_parametros.detalle_c31,            
			now(),
			v_parametros._nombre_usuario_ai,
			p_id_usuario,
			v_parametros._id_usuario_ai,            
			null,
			null,
            v_parametros.id_uo
            )RETURNING id_anexo into v_id_anexo;
                        
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anexo almacenado(a) con exito (id_anexo'||v_id_anexo||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_anexo',v_id_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;	
        
         /*********************************
 	#TRANSACCION:  'KAF_ANEX3_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX3_INS')then
    	begin
        	--Sentencia de la insercion
        	insert into kaf.tanexo(
			id_partida,
			tipo_anexo,
			id_periodo_anexo,
			monto_sigep,
            observaciones,			
			estado_reg,
            diferencia,		
			c31,		
			monto_erp,			
			fecha_reg,
			usuario_ai,
			id_usuario_reg,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod
    
          	) values(
			v_parametros.id_partida,
			v_parametros.tipo_anexo,
			v_parametros.id_periodo_anexo,
			v_parametros.monto_sigep,
            v_parametros.observaciones,			
			'activo',
            v_parametros.diferencia,		
			v_parametros.c31,			
			v_parametros.monto_erp,            
			now(),
			v_parametros._nombre_usuario_ai,
			p_id_usuario,
			v_parametros._id_usuario_ai,            
			null,
			null
            
            )RETURNING id_anexo into v_id_anexo;
            
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anexo almacenado(a) con exito (id_anexo'||v_id_anexo||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_anexo',v_id_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;	
     
	/*********************************
 	#TRANSACCION:  'KAF_ANEX_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX_MOD')then

		begin

			--Sentencia de la modificacion
			update kaf.tanexo set
			id_partida = v_parametros.id_partida,
			tipo_anexo = v_parametros.tipo_anexo,
			id_periodo_anexo = v_parametros.id_periodo_anexo,
			
			monto_contrato = v_parametros.monto_contrato,
			observaciones = v_parametros.observaciones,
			
			c31 = v_parametros.c31,
			monto_transito = v_parametros.monto_transito,
			monto_pagado = v_parametros.monto_pagado,
			detalle_c31 = v_parametros.detalle_c31,
			monto_alta = v_parametros.monto_alta,	
            monto_tercer = v_parametros.monto_tercer,		
			
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            id_uo = v_parametros.id_uo
			where id_anexo=v_parametros.id_anexo;
			
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anexo modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_anexo',v_parametros.id_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
        /*********************************
 	#TRANSACCION:  'KAF_ANEX1_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX1_MOD')then

		begin
			--Sentencia de la modificacion
			update kaf.tanexo set
			id_partida = v_parametros.id_partida,
			tipo_anexo = v_parametros.tipo_anexo,
			id_periodo_anexo = v_parametros.id_periodo_anexo,
			monto_sigep = v_parametros.monto_sigep,
		
			observaciones = v_parametros.observaciones,
			diferencia = v_parametros.diferencia,
			c31 = v_parametros.c31,
			
		
			monto_erp = v_parametros.monto_erp,			
			
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
           
			where id_anexo=v_parametros.id_anexo;
            
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anexo modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_anexo',v_parametros.id_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
        
        /*********************************
 	#TRANSACCION:  'KAF_ANEX2_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX2_MOD')then

		begin
			--Sentencia de la modificacion
			update kaf.tanexo set
			id_partida = v_parametros.id_partida,
			tipo_anexo = v_parametros.tipo_anexo,
			id_periodo_anexo = v_parametros.id_periodo_anexo,
			monto_erp = v_parametros.monto_erp,	
			c31 = v_parametros.c31,			
			detalle_c31 = v_parametros.detalle_c31,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            id_uo = v_parametros.id_uo
			where id_anexo=v_parametros.id_anexo;
            
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anexo modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_anexo',v_parametros.id_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
        
          /*********************************
 	#TRANSACCION:  'KAF_ANEX3_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX3_MOD')then

		begin
			--Sentencia de la modificacion
			update kaf.tanexo set
			id_partida = v_parametros.id_partida,
			tipo_anexo = v_parametros.tipo_anexo,
			id_periodo_anexo = v_parametros.id_periodo_anexo,
			monto_sigep = v_parametros.monto_sigep,	
            observaciones = v_parametros.observaciones,
			c31 = v_parametros.c31,			
			diferencia = v_parametros.diferencia,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            monto_erp = v_parametros.monto_erp            
			where id_anexo=v_parametros.id_anexo;
            
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anexo modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_anexo',v_parametros.id_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
        
          /*********************************
 	#TRANSACCION:  'KAF_MOVER_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_MOVER_MOD')then

		begin

            select an.tipo_anexo
            into v_anexo_origen
            from kaf.tanexo an
            where an.id_anexo = v_parametros.id_anexo;

         if v_anexo_origen = 4 then 
         	if v_parametros.tipo_anexo = 1 then 
            	raise exception 'Cambio no permitdo';
            end if;
         end if;  
         
         if v_anexo_origen = 3 then 
         	if v_parametros.tipo_anexo = 1 then 
            	raise exception 'Cambio no permitdo';
            end if;
         end if;         
             
         if  v_anexo_origen = 1 then 
         	 if v_parametros.tipo_anexo = 4 then    
                --Sentencia de la modificacion
                update kaf.tanexo set		
                monto_sigep = v_parametros.monto_tercer,
                monto_erp = v_parametros.monto_alta,
                diferencia = v_parametros.monto_alta - v_parametros.monto_tercer,
                tipo_anexo = v_parametros.tipo_anexo,
                seleccionado = 'no'			           
                where id_anexo=v_parametros.id_anexo;
            end if;
         end if;   
         if  v_anexo_origen = 1 then
              if v_parametros.tipo_anexo = 2  then 
                --Sentencia de la modificacion
                update kaf.tanexo set		
                monto_sigep = v_parametros.monto_tercer,
                monto_erp = v_parametros.monto_alta,
                diferencia = v_parametros.monto_alta - v_parametros.monto_tercer,
                tipo_anexo = v_parametros.tipo_anexo,
                seleccionado = 'no'			           
                where id_anexo=v_parametros.id_anexo;              
              end if;
     	  end if;
         if  v_anexo_origen = 1 then
              if v_parametros.tipo_anexo = 3  then 
                --Sentencia de la modificacion
                update kaf.tanexo set 
                monto_erp = v_parametros.monto_contrato,
                tipo_anexo = v_parametros.tipo_anexo,
                seleccionado = 'no'			           
                where id_anexo=v_parametros.id_anexo;                                
              end if;
     	  end if;          
         if  v_anexo_origen = 2 then
              if v_parametros.tipo_anexo = 1  then               
                --Sentencia de la modificacion
                update kaf.tanexo set		
                monto_alta = v_parametros.monto_erp,
                tipo_anexo = v_parametros.tipo_anexo,
                seleccionado = 'no'			           
                where id_anexo=v_parametros.id_anexo;              
              end if;
          end if;    
         if  v_anexo_origen = 2 then
              if v_parametros.tipo_anexo = 3  then 
                --Sentencia de la modificacion
                update kaf.tanexo set		
                monto_erp = v_parametros.monto_erp,
                tipo_anexo = v_parametros.tipo_anexo,
                seleccionado = 'no'			           
                where id_anexo=v_parametros.id_anexo;              
              else 
                update kaf.tanexo set
                tipo_anexo = v_parametros.tipo_anexo,
                seleccionado = 'no'			           
                where id_anexo=v_parametros.id_anexo;                            
              end if;                           
          else 

			update kaf.tanexo set
			tipo_anexo = v_parametros.tipo_anexo,
            seleccionado = 'no'			           
			where id_anexo=v_parametros.id_anexo;            
	     end if;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anexo modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_anexo',v_parametros.id_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'KAF_ANEX_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		22-10-2018 13:08:18
	***********************************/

	elsif(p_transaccion='KAF_ANEX_ELI')then

		begin
    	--Sentencia de la eliminacion
			delete from kaf.tanexo
            where id_anexo=v_parametros.id_anexo;          
                  
			
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anexo eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_anexo',v_parametros.id_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
        
	/*********************************
 	#TRANSACCION:  'KAF_GENANEXOS_INS'
 	#DESCRIPCION:	Generar Anexos
 	#AUTOR:		BVP
 	#FECHA:		20-10-2018 
	***********************************/

	elsif(p_transaccion='KAF_GENANEXOS_INS')then
    
    
    update kaf.tperiodo_anexo per set
    estado = 'Generado'
    where per.id_periodo_anexo = v_parametros.id_periodo_anexo; 

    begin
	
/*****************REGISTRO DE ANEXOS********************************************/

	/*********************************ANEXO1*******************************/
	 v_fecha_ini=v_parametros.fecha_ini;
     v_fecha_fin=v_parametros.fecha_fin;


      v_consulta:='select kaf.f_anexo_1('||p_id_usuario||',de.c31, de.nro_partida,sum(de.monto_sigep),de.id_periodo_anexo,'''||v_parametros.id_gestion||''','''||v_fecha_ini||''','''||v_fecha_fin||''')
      from kaf.tdetalle_sigep de 
      where de.id_periodo_anexo = '||v_parametros.id_periodo_anexo||'
            group by de.c31,
               de.nro_partida,
               de.id_periodo_anexo
      	    order by de.c31 asc';
      execute(v_consulta);
          
	/**********************************************************************/			        				

	/*********************************ANEXO2*******************************/			
        		
      v_consulta:='select kaf.f_anexo_2('||p_id_usuario||',de.c31,de.nro_partida,sum(de.monto_sigep),de.id_periodo_anexo,'''||v_parametros.id_gestion||''','''||v_fecha_ini||''','''||v_fecha_fin||''')
      from kaf.tdetalle_sigep de 
      where de.id_periodo_anexo = '||v_parametros.id_periodo_anexo||'
      group by de.c31,
               de.nro_partida,
               de.id_periodo_anexo';
      execute(v_consulta);

	/**********************************************************************/

	/*********************************ANEXO3*******************************/
     v_consulta:='select kaf.f_anexo_3('||p_id_usuario||','''||v_parametros.id_periodo_anexo||''','''||v_parametros.id_gestion||''','''||v_fecha_ini||''','''||v_fecha_fin||''')
      ';

      execute(v_consulta);	
	/**********************************************************************/

	/*********************************ANEXO4*******************************/
     v_consulta:='select kaf.f_anexo_4('||p_id_usuario||','''||v_parametros.id_periodo_anexo||''','''||v_parametros.id_gestion||''','''||v_fecha_ini||''','''||v_fecha_fin||''')
		';
	  execute(v_consulta);                      
	/**********************************************************************/

/*******************REGISTRO DE DATOS PARTIDA PERIODO**************************/

      --------1---------------

      v_consulta:='select kaf.f_update_partida_periodo(ane.id_periodo_anexo,1,ane.id_partida,coalesce(sum(ane.monto_tercer),0))
        from kaf.tanexo ane 
        where ane.id_periodo_anexo = '||v_parametros.id_periodo_anexo||' and ane.tipo_anexo = 1
        group by 
        ane.id_periodo_anexo,
        ane.id_partida';
    execute(v_consulta);        

      --------2---------------
      v_consulta:='select kaf.f_update_partida_periodo(ane.id_periodo_anexo,2,ane.id_partida,coalesce(sum(ane.diferencia),0))
        from kaf.tanexo ane 
        where ane.id_periodo_anexo = '||v_parametros.id_periodo_anexo||' and ane.tipo_anexo = 2
        group by 
        ane.id_periodo_anexo,
        ane.id_partida';
    execute(v_consulta);
      
      --------3---------------
      v_consulta:='select kaf.f_update_partida_periodo(ane.id_periodo_anexo,3,ane.id_partida,coalesce(sum(ane.monto_erp),0))
        from kaf.tanexo ane 
        where ane.id_periodo_anexo = '||v_parametros.id_periodo_anexo||' and ane.tipo_anexo = 3
        group by 
        ane.id_periodo_anexo,
        ane.id_partida';
    execute(v_consulta);
      
      --------4---------------
      v_consulta:='select kaf.f_update_partida_periodo(ane.id_periodo_anexo,4,ane.id_partida,coalesce(sum(ane.diferencia)))
        from kaf.tanexo ane 
        where ane.id_periodo_anexo = '||v_parametros.id_periodo_anexo||' and ane.tipo_anexo = 4
        group by 
        ane.id_periodo_anexo,
        ane.id_partida';
    execute(v_consulta);
      
/*************IMPORTE TOTAL****************************************************/
      
      for v_partidas_per in        
              SELECT pa.id_partida,
          coalesce(pa.importe_sigep,0)-(coalesce(pa.importe_anexo1,0))-(-(coalesce(pa.importe_anexo2,0)))-(-(coalesce(pa.importe_anexo4,0))) as total
              FROM kaf.tpartida_periodo pa 
              where pa.id_periodo_anexo = v_parametros.id_periodo_anexo
      loop 
              update kaf.tpartida_periodo set
              importe_total =  v_partidas_per.total
              where id_partida = v_partidas_per.id_partida
              and id_periodo_anexo = v_parametros.id_periodo_anexo;
      end loop;
            
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anexo Agregado(a)');                
                
	return v_resp;		
            
		end;
        
        /*********************************
 	#TRANSACCION:  'KAF_GEGERAL_MOD'
 	#DESCRIPCION:	Generador de reporte general
 	#AUTOR:		BVP
 	#FECHA:		20-11-2018 
	***********************************/
    elseif(p_transaccion='KAF_GEGERAL_MOD') then
    
    	begin
        
/*******************ACTUALIZACION DE DATOS PARTIDA PERIODO**************************/
        
      for v_id_partidas in select
                          id_partida       
                          from kaf.tpartida_periodo
                          where id_periodo_anexo = v_parametros.id_periodo_anexo
      loop 
        update kaf.tpartida_periodo set 
              importe_anexo1=null,
              importe_anexo2=null,
              importe_anexo3=null,
              importe_anexo4=null,
              importe_total=null                       
        where id_periodo_anexo = v_parametros.id_periodo_anexo
        and id_partida = v_id_partidas.id_partida;     
      end loop;        

      --------1---------------

      v_consulta:='select kaf.f_update_partida_periodo(ane.id_periodo_anexo,1,ane.id_partida,coalesce(sum(ane.monto_tercer),0))
        from kaf.tanexo ane 
        where ane.id_periodo_anexo = '||v_parametros.id_periodo_anexo||' and ane.tipo_anexo = 1
        group by 
        ane.id_periodo_anexo,
        ane.id_partida';
    execute(v_consulta);        

      --------2---------------
      v_consulta:='select kaf.f_update_partida_periodo(ane.id_periodo_anexo,2,ane.id_partida,coalesce(sum(ane.diferencia),0))
        from kaf.tanexo ane 
        where ane.id_periodo_anexo = '||v_parametros.id_periodo_anexo||' and ane.tipo_anexo = 2
        group by 
        ane.id_periodo_anexo,
        ane.id_partida';
    execute(v_consulta);
      
      --------3---------------
      v_consulta:='select kaf.f_update_partida_periodo(ane.id_periodo_anexo,3,ane.id_partida,coalesce(sum(ane.monto_erp),0))
        from kaf.tanexo ane 
        where ane.id_periodo_anexo = '||v_parametros.id_periodo_anexo||' and ane.tipo_anexo = 3
        group by 
        ane.id_periodo_anexo,
        ane.id_partida';
    execute(v_consulta);
      
      --------4---------------
      v_consulta:='select kaf.f_update_partida_periodo(ane.id_periodo_anexo,4,ane.id_partida,coalesce(sum(ane.diferencia),0))
        from kaf.tanexo ane 
        where ane.id_periodo_anexo = '||v_parametros.id_periodo_anexo||' and ane.tipo_anexo = 4
        group by 
        ane.id_periodo_anexo,
        ane.id_partida';
    execute(v_consulta);
      
/*************IMPORTE TOTAL****************************************************/
      
      for v_partidas_per in        
              SELECT pa.id_partida,
          coalesce(pa.importe_sigep,0)-(-(coalesce(pa.importe_anexo1,0)))-(-(coalesce(pa.importe_anexo2,0)))-(-(coalesce(pa.importe_anexo4,0))) as total
              FROM kaf.tpartida_periodo pa 
              where pa.id_periodo_anexo = v_parametros.id_periodo_anexo
      loop 
              update kaf.tpartida_periodo set
              importe_total =  v_partidas_per.total
              where id_partida = v_partidas_per.id_partida
              and id_periodo_anexo = v_parametros.id_periodo_anexo;
      end loop;
          v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anexo Agregado(a)');                
                    
	return v_resp;

	end;          
        
        /*********************************
 	#TRANSACCION:  'KAF_CONTROL_CON'
 	#DESCRIPCION:	Control de los items Seleccionados
 	#AUTOR:		IVALDIVIA
 	#FECHA:		01-11-2018 10:30:00
	***********************************/
    elseif(p_transaccion='KAF_CONTROL_CON') then
    	begin

        select anex.seleccionado
        into
        v_seleccionado
        from kaf.tanexo anex
        where anex.id_anexo = v_parametros.id_anexo;

        if (v_seleccionado = 'si')then
        v_seleccionado = 'no';
        else
        v_seleccionado = 'si';
        end if;

        update kaf.tanexo set
        seleccionado = v_seleccionado
        where id_anexo = v_parametros.id_anexo;

 		v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Revision con exito (id_anexo'||v_parametros.id_anexo||')');
        v_resp = pxp.f_agrega_clave(v_resp,'id_anexo',v_parametros.id_anexo::varchar);

		--Devuelve la respuesta
        return v_resp;

	end;
    
     /*********************************
      #TRANSACCION:  'KAFF_AGRUP_INS'
      #DESCRIPCION:	Agrupacion de Anexos
      #AUTOR:		IVALDIVIA
      #FECHA:		1-11-2018 11:05:30
      ***********************************/

      elsif(p_transaccion='KAFF_AGRUP_INS')then

          begin
      	      SELECT  into v_variables 
                      anex.id_partida,
                      anex.id_periodo_anexo,
                      anex.tipo_anexo,                      
                      anex.id_uo                                          
              FROM kaf.tanexo anex 
              where anex.seleccionado = 'si';           
        		
              
              SELECT  into v_agrupados 
                     string_agg(anex.c31, ' | ') as c31,
                     string_agg(anex.detalle_c31 , ' | ') as desc_det,
                     string_agg(anex.observaciones, ' | ') as observaciones                                        
              FROM kaf.tanexo anex 
              where anex.seleccionado = 'si';
              
              select 
              into v_contador 
              count (seleccionado) as contador
              from kaf.tanexo ane
              where ane.seleccionado='si';
              
          	  SELECT  into v_partida
              count(DISTINCT anex.id_partida) as partida                                               
              FROM kaf.tanexo anex 
              where anex.seleccionado = 'si';
              
              SELECT  into v_id_uo
              count(DISTINCT anex.id_uo) as unidad                                               
              FROM kaf.tanexo anex 
              where anex.seleccionado = 'si';                 
              		
              
              select  into v_totales
              sum(anex.monto_sigep) as total_sigep,
              sum(anex.monto_contrato) as total_contrato,
              sum(anex.monto_erp) as total_erp,
              sum(anex.diferencia) as total_diferencia,
              sum(anex.monto_transito) as total_transito,
              sum(anex.monto_pagado) as total_pagado,
              sum(anex.monto_tercer) as total_tercer
              from kaf.tanexo anex 
              where anex.seleccionado = 'si'  ;                           
              
            --raise exception 'LLEGA AQUI %',v_variables.id_uo;
            if (v_id_uo.unidad = '1' OR v_id_uo.unidad = '0' ) THEN
            if (v_partida.partida = '1') THEN   
            if (v_contador.contador >= '2') then        
            insert into kaf.tanexo(
			id_partida,
			tipo_anexo,
			id_periodo_anexo,
            monto_contrato,
			monto_sigep,	
            monto_transito,
            monto_pagado,
            monto_tercer,		
			observaciones,
			estado_reg,
			diferencia,
			c31,
            detalle_c31,
            id_uo,			
			monto_erp,			
			fecha_reg,
			usuario_ai,
			id_usuario_reg,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod           
          	) values(
			v_variables.id_partida,
			v_variables.tipo_anexo,
			v_variables.id_periodo_anexo,
            v_totales.total_contrato,
			v_totales.total_sigep,
            v_totales.total_transito,
            v_totales.total_pagado,
            v_totales.total_tercer,			
			v_agrupados.observaciones,
			'activo',
			v_totales.total_diferencia,
			v_agrupados.c31,
            v_agrupados.desc_det,
            v_variables.id_uo,            			
			v_totales.total_erp,			
			now(),
			v_parametros._nombre_usuario_ai,
			p_id_usuario,
			v_parametros._id_usuario_ai,            
			null,
			null      
			)RETURNING id_anexo into v_id_anexo;
            else 
            	raise exception 'Seleccione mas de dos elemetos para poder agruparlos';  
             end if;
          else 
            raise Exception 'Seleccione datos con partidas similares.';
          end if;  
          else 
            raise Exception 'Seleccione datos con unidad solicitantes similares.';
          end if;
            update kaf.tanexo set			
			tipo_anexo = 6,
            seleccionado = 'no',
            id_seleccionado = v_id_anexo			           
			where seleccionado='si';

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anexo almacenado(a) con exito (id_anexo'||v_id_anexo||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_anexo',v_id_anexo::varchar);

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

ALTER FUNCTION kaf.ft_anexo_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;