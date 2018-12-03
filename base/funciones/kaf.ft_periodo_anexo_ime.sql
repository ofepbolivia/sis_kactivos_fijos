CREATE OR REPLACE FUNCTION kaf.ft_periodo_anexo_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Activos Fijos
 FUNCION: 		kaf.ft_periodo_anexo_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'kaf.tperiodo_anexo'
 AUTOR: 		 (ivaldivia)
 FECHA:	        19-10-2018 13:39:03
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				19-10-2018 13:39:03								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'kaf.tperiodo_anexo'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_periodo_anexo	integer;
    v_gestion			record;
    v_anio				record;
    v_nro_partida		record;
    v_id_gestion		integer;
    v_id_partida		record;
	v_total				record;
    
   

BEGIN

    v_nombre_funcion = 'kaf.ft_periodo_anexo_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'KAF_PERANE_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		19-10-2018 13:39:03
	***********************************/

	if(p_transaccion='KAF_PERANE_INS')then

        begin
  /*----------------OBTENEMOS EL ID GESTION PRA CONTROL DE FECHAS------------------*/      
        select DISTINCT
        into v_gestion
        ges.gestion
        from param.tgestion ges        
        where ges.id_gestion=v_parametros.id_gestion;
   /*-----------------------------------------------------------------------------*/     
        
        select 
        into v_anio 
        extract(year from v_parametros.fecha_ini);  
        
        if v_gestion::VARCHAR <> v_anio::VARCHAR then
         raise exception 'La Gestion Seleccionada es: %, Y el año en la Fecha inicial o la fecha final es: %, el año debe coincidir con el periodo.',v_gestion,v_anio;      
        end if;
        
         if v_parametros.fecha_fin < v_parametros.fecha_ini then
        	raise exception 'Fecha Final debe ser mayor a la Fecha Inicial Verifique los Datos.';
        end if;
    	   
    	if v_parametros.fecha_fin = v_parametros.fecha_ini then
        	raise exception 'Las fechas no pueden ser Iguales seleccione un Rango Diferente.';
        end if;     
                      
        	--Sentencia de la insercion
        	insert into kaf.tperiodo_anexo(
			estado_reg,
			nombre_periodo,
			fecha_ini,
			fecha_fin,
			id_gestion,
			observaciones,            
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod,
            estado 
          	) values(
			'activo',
			v_parametros.nombre_periodo,
			v_parametros.fecha_ini,
			v_parametros.fecha_fin,
			v_parametros.id_gestion,
			v_parametros.observaciones,            
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null,
            'Borrador'
            
			)RETURNING id_periodo_anexo into v_id_periodo_anexo;
            
            /* update kaf.tperiodo_anexo per set
             estado = 'Borrador'
             where per.id_periodo_anexo = v_id_periodo_anexo;*/

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Periodo Anexo almacenado(a) con exito (id_periodo_anexo'||v_id_periodo_anexo||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_periodo_anexo',v_id_periodo_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
        
        
        /*********************************
 	#TRANSACCION:  'KAFF_PARPER_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		19-10-2018 13:39:03
	***********************************/

	elsif(p_transaccion='KAFF_PARPER_INS')then

        begin
  
        /***************************SUMA DE LAS PARTIDAS SIMILARES******************************************/ 
        			   for v_total IN(  
             					 SELECT det.nro_partida,
                                 SUM(det.monto_sigep) totalImporte
                                 FROM kaf.tdetalle_sigep det
                                 Where det.id_periodo_anexo = v_parametros.id_periodo_anexo
                                 GROUP BY det.nro_partida)  
         		   		LOOP	  
                        
                    select into v_id_gestion 
                    per.id_gestion
                    FROM kaf.tperiodo_anexo per
                    where per.id_periodo_anexo = v_parametros.id_periodo_anexo;
                    
         			select into v_id_partida
                    part.id_partida
                    from pre.tpartida part	
                    where part.id_gestion = v_id_gestion and part.codigo = v_total.nro_partida;
                        
                     insert into kaf.tpartida_periodo(
                          id_partida,
                          id_periodo_anexo,
                          importe_sigep,
                          id_usuario_reg,
                          fecha_reg,
                          id_usuario_ai,
                          usuario_ai,
                          id_usuario_mod,
                          fecha_mod                         
                          ) values(
                          v_id_partida.id_partida,
                          v_parametros.id_periodo_anexo,
                          v_total.totalImporte,
                          p_id_usuario,
                          now(),
                          v_parametros._id_usuario_ai,
                          v_parametros._nombre_usuario_ai,
                          null,
                          null
                          );
                      
                  end loop;            
        /*************************************************************************************/          
        
             update kaf.tperiodo_anexo per set
             estado = 'Insertado'
             where per.id_periodo_anexo = v_parametros.id_periodo_anexo;         

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Periodo Anexo almacenado(a) con exito (id_periodo_anexo'||v_id_periodo_anexo||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_periodo_anexo',v_id_periodo_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'KAF_PERANE_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		19-10-2018 13:39:03
	***********************************/

	elsif(p_transaccion='KAF_PERANE_MOD')then

		begin

			--Sentencia de la modificacion
			update kaf.tperiodo_anexo set
			nombre_periodo = v_parametros.nombre_periodo,
			fecha_ini = v_parametros.fecha_ini,
			fecha_fin = v_parametros.fecha_fin,
			id_gestion = v_parametros.id_gestion,
			observaciones = v_parametros.observaciones,            
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_periodo_anexo=v_parametros.id_periodo_anexo;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Periodo Anexo modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_periodo_anexo',v_parametros.id_periodo_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'KAF_PERANE_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		19-10-2018 13:39:03
	***********************************/

	elsif(p_transaccion='KAF_PERANE_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from kaf.tperiodo_anexo
            where id_periodo_anexo=v_parametros.id_periodo_anexo;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Periodo Anexo eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_periodo_anexo',v_parametros.id_periodo_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
        
          /*********************************
 	#TRANSACCION:  'KAFF_FINAL_IME'
 	#DESCRIPCION:	Cambio de Estado a Finalizado
 	#AUTOR:		ivaldivia
 	#FECHA:		05-11-2018 10:07:20
	***********************************/

	elsif(p_transaccion='KAFF_FINAL_IME')then

		begin
			--Sentencia de la eliminacion
			update kaf.tperiodo_anexo per set
            estado = 'Finalizado'
            where per.id_periodo_anexo = v_parametros.id_periodo_anexo; 

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Anexo Finalizado');
            v_resp = pxp.f_agrega_clave(v_resp,'id_anexo',v_parametros.id_periodo_anexo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
        
     /*********************************
      #TRANSACCION:  'KAFF_EXCEL_ELI'
      #DESCRIPCION:	Eliminacion de registros
      #AUTOR:		IVALDIVIA
      #FECHA:		31-10-2018 9:37:45
      ***********************************/

      elsif(p_transaccion='KAFF_EXCEL_ELI')then

          begin
              --Sentencia de la eliminacion
              update kaf.tperiodo_anexo per set
             estado = 'Borrador'
             where per.id_periodo_anexo = v_parametros.id_periodo_anexo;
             
              delete from kaf.tdetalle_sigep
              where id_periodo_anexo=v_parametros.id_periodo_anexo;
              
              delete from kaf.tpartida_periodo
              where id_periodo_anexo=v_parametros.id_periodo_anexo;
              
             

              --Definicion de la respuesta
              v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Archivo Excel Eliminado');
              v_resp = pxp.f_agrega_clave(v_resp,'id_periodo_anexo',v_parametros.id_periodo_anexo::varchar);

              --Devuelve la respuesta
              return v_resp;

          end;    
          
          
          /*********************************
      #TRANSACCION:  'KAF_REPORTGE_IME'
      #DESCRIPCION:	Eliminacion de registros
      #AUTOR:		IVALDIVIA
      #FECHA:		31-10-2018 9:37:45
      ***********************************/

      elsif(p_transaccion='KAF_REPORTGE_IME')then

          begin
          raise notice 'LLEGA REPORTE PARAMETROS: %',v_parametros.id_periodo_anexo;
            CREATE TEMPORARY TABLE temp_prog (
						id_anexo INTEGER,
						id_partida INTEGER ,
						tipo_anexo INTEGER,
					    id_periodo_anexo INTEGER,						
						monto_contrato NUMERIC ,
						observaciones TEXT,								
						c31 VARCHAR,
						monto_transito NUMERIC,
						monto_pagado NUMERIC,
						detalle_c31 VARCHAR,
						monto_erp NUMERIC						
						) ON COMMIT DROP;                
                        
                         insert into temp_prog(
                                      id_anexo,
                                      id_partida,
                                      tipo_anexo,
                                      id_periodo_anexo,
                                      monto_contrato,
                                      observaciones,
                                      c31,
                                      monto_transito,

                                      monto_pagado,
                                      detalle_c31,
                                      monto_erp                                      
                                       )
                                     values   (
                                      1,
                                      2,
                                      2,
                                      1,
                                      2000,
                                      'oBSERCAVAION',
                                      'leGA DATO',
                                      2000,
                                      1000,
                                      'DET',
                                      1000
                                      );

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