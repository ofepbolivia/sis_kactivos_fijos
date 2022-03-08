CREATE OR REPLACE FUNCTION kaf.f_depre_inversa_mes_truncado (
  p_mon_vig_act numeric,
  p_mon_vig numeric,
  p_dep_acum numeric,
  p_vida_res integer,
  p_fecha_desde date
)
RETURNS numeric AS
$body$
/**************************************************************************
 SISTEMA:		AF
 FUNCION: 		kaf.f_depre_inversa_mes_truncado
 DESCRIPCION:   funcion de depreciacion inversa, optiene el valor de la depreciacion acumulada de la gestion anterior al mes de diciembre
				, esto para usarlo en el reporte detalle depreciacion
 AUTOR: 		breydi vasquez
 FECHA:	        16/01/2020
 COMENTARIOS:	
***************************************************************************/
DECLARE
  	v_resp 				numeric;
    v_rec_tc			record;
    v_rec_tc_nuevo		record;
	v_fecha_retroceso	date;
	v_cont				integer;
    v_mes_dep			date;
    v_tipo_cambio_fin	numeric;
    v_mon_vig_act		numeric;
    
    v_nuevo_mon_vig_act numeric;
    v_nuevo_dep_mes 	numeric;
    v_nuevo_dep_acum 	numeric;
    v_nuevo_mon_vig 	numeric;
  	v_ant_mon_vig_act   numeric;
    v_mon_vig_ant 		numeric;
    v_dep_acum_ant      numeric;
    v_vida_res 			integer;
    v_gestion_aux		integer;
    v_mes_aux			integer;
    v_mes_dep_ant		date;
	v_fecha				date;
BEGIN

	v_cont = date_part('month'::text, p_fecha_desde) + 1;    

	truncate table temp_depreciacion;

	for i in 1..v_cont loop    
       

		if i = 1 then 
        
		  v_fecha = p_fecha_desde - interval '1' month;
          
          select
          o_tc_inicial, o_tc_final, o_tc_factor, o_fecha_ini, o_fecha_fin
          into v_rec_tc_nuevo
          from kaf.f_get_tipo_cambio(3, 1, null, ('01/'||date_part('month', v_fecha)::varchar||'/'||date_part('year', v_fecha)::varchar)::date);
                  
          select
          o_tc_inicial, o_tc_final, o_tc_factor, o_fecha_ini, o_fecha_fin
          into v_rec_tc
          from kaf.f_get_tipo_cambio(3, 1, v_rec_tc_nuevo.o_tc_final,  pxp.f_last_day(p_fecha_desde));  
                                                      
          v_nuevo_mon_vig_act = (p_mon_vig_act / v_rec_tc.o_tc_factor);
          v_nuevo_dep_mes 	  = (p_mon_vig / v_rec_tc.o_tc_factor - 1) / p_vida_res ; 
          v_nuevo_dep_acum 	  = (p_dep_acum / v_rec_tc.o_tc_factor ) - v_nuevo_dep_mes; 
          v_nuevo_mon_vig 	  = (p_mon_vig_act / v_rec_tc.o_tc_factor ) - v_nuevo_dep_acum;
          v_vida_res = p_vida_res;    
          v_mes_dep  =  p_fecha_desde - interval '1' month;
          v_gestion_aux = date_part('year'::text, v_mes_dep);
          v_mes_aux = date_part('month'::text, v_mes_dep);                 
          v_mes_dep = ('01/'||v_mes_aux::varchar||'/'||v_gestion_aux::varchar)::date;                                                    
          
        else

         --Obtener tipo de cambio del inicio y fin de mes 
          select
          o_tc_inicial, o_tc_final, o_tc_factor, o_fecha_ini, o_fecha_fin
          into v_rec_tc_nuevo
          from kaf.f_get_tipo_cambio(3, 1,  null, v_mes_dep);  
                
          select
          o_tc_inicial, o_tc_final, o_tc_factor, o_fecha_ini, o_fecha_fin
          into v_rec_tc
          from kaf.f_get_tipo_cambio(3, 1, v_rec_tc_nuevo.o_tc_final, v_mes_dep_ant);               		              
           
          v_nuevo_mon_vig_act = v_ant_mon_vig_act / v_rec_tc.o_tc_factor;
          v_nuevo_dep_mes 	= (v_mon_vig_ant / v_rec_tc.o_tc_factor - 1 ) / v_vida_res; 
          v_nuevo_dep_acum 	= (v_dep_acum_ant /v_rec_tc.o_tc_factor ) - v_nuevo_dep_mes;
          v_nuevo_mon_vig 	= (v_ant_mon_vig_act / v_rec_tc.o_tc_factor ) - v_nuevo_dep_acum;
          v_vida_res = v_vida_res; 
          v_mes_dep =  v_mes_dep;       
          
        end if;
        
  		insert into temp_depreciacion 
        (
          fecha,
          dep_acumulada,
          monto_vigente_actu,
          dep_mes,
          monto_vigente,
          vida_residual
          )VALUES(
          v_mes_dep,
          v_nuevo_dep_acum,
          v_nuevo_mon_vig_act,
          v_nuevo_dep_mes,
          v_nuevo_mon_vig,
          v_vida_res
        );        
    	        
        v_ant_mon_vig_act  = v_nuevo_mon_vig_act;
		v_mon_vig_ant 	   = v_nuevo_mon_vig;
        v_dep_acum_ant     = v_nuevo_dep_acum;
        v_vida_res         = v_vida_res + 1;
               
        v_mes_dep = v_mes_dep - interval '1' month; 

        --ajusta las fechas 
        v_gestion_aux = date_part('year'::text, v_mes_dep);
        v_mes_aux = date_part('month'::text, v_mes_dep);                 
        v_mes_dep = ('01/'||v_mes_aux::varchar||'/'||v_gestion_aux::varchar)::date;
        v_mes_dep_ant = v_mes_dep + interval '1' month;
                    
    end loop;
    
    select dep_acumulada into v_resp
    from temp_depreciacion
	where extract(month from fecha) = 12;
        
return v_resp;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;
