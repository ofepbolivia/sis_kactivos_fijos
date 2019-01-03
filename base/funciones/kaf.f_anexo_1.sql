CREATE OR REPLACE FUNCTION kaf.f_anexo_1 (
  p_id_usuario integer,
  p_c31 varchar,
  p_partida varchar,
  p_monto_sigep numeric,
  p_id_periodo_anexo integer,
  p_id_gestion integer,
  p_fecha_ini date,
  p_fecha_fin date
)
RETURNS void AS
$body$
/*
Autor: BVP
Fecha: 25/10/2018
Descripción: generador de datos para anexo 1 
*/
DECLARE
  v_registro				    record;
  v_monto_transito				numeric;
  v_alta_erp			 	    record;
  v_year 					    integer;
  v_monto_sigep				  	record;
  v_monto_contrato				record;
  v_monto_erp 				  	record;
  v_diferencia				  	record;
  v_sigep_duplicado				numeric;
  v_monto_sigep_tot				numeric;
  v_monto_sigep_ac      		numeric;

BEGIN

    v_year = to_char(now(),'yyyy');
    
    --MONTO ACUMULADO  PAGADO HASTA EL PERIODO ANTERIOR, SEGÚN SIGEP EN LA GESTION ACTUAL
    
        select  de.c31, 
		        de.nro_partida, 
        		sum(de.monto_sigep) as monto_periodo_anterior
	      into 
    			v_monto_sigep
                                  
        from kaf.tdetalle_sigep de 
        where de.id_periodo_anexo < p_id_periodo_anexo
        	 and de.c31 = p_c31  
        group by de.c31,
                 de.nro_partida;
                 
             
	--MONTO CONTRATO TOTAL    
   
		select mo.id_parti as id_partida,
           mo.c31,
    	   round(sum(mo.monto_contrato),2) as monto_compra_100,
           mo.id_unidad as uni_solici
         into 
         v_monto_contrato            
    from kaf.f_monto_contrato_c31(p_c31,p_partida,p_id_gestion) mo
    group by mo.id_parti,
             mo.c31,
           mo.id_unidad;
             
                        
  if v_monto_contrato.id_partida is not null then
    
     if not exists ( select  1
                from kaf.tanexo  anes
                where anes.c31 = v_monto_contrato.c31 and anes.id_periodo_anexo = p_id_periodo_anexo) then 
        	              
                  
    	if (v_monto_contrato.monto_compra_100 - p_monto_sigep <> 0)then
 
          select 
                  par.id_partida,
                  af.nro_cbte_asociado,
                 sum(af.monto_compra_orig_100) as monto_erp_gestion
                 into 
                 v_alta_erp
          from kaf.tactivo_fijo af 
          inner join kaf.tclasificacion cla on cla.id_clasificacion = af.id_clasificacion
          inner join kaf.tclasificacion_partida clapa on clapa.id_clasificacion = cla.id_clasificacion
          inner join pre.tpartida par on par.id_partida = clapa.id_partida and par.id_gestion = p_id_gestion
          where af.nro_cbte_asociado like '%'||p_c31||'%' and af.estado = 'alta' 
          and af.fecha_ini_dep between ('01/01/'||v_year::varchar)::date and p_fecha_fin 
          and par.codigo = p_partida
          group by 
                 par.id_partida,
                 af.nro_cbte_asociado;
                           
         v_monto_transito =   v_monto_contrato.monto_compra_100 - coalesce(0,v_alta_erp.monto_erp_gestion);      

            
            insert into kaf.tanexo
              (id_usuario_reg,
              id_periodo_anexo,
              id_partida,
              id_uo,
              c31,
              monto_contrato,
              monto_alta,
              monto_transito,
              monto_pagado,
              monto_tercer,
              tipo_anexo
              )
              values
              (p_id_usuario,
               p_id_periodo_anexo,
			   v_monto_contrato.id_partida,
               v_monto_contrato.uni_solici,
               v_monto_contrato.c31,
               v_monto_contrato.monto_compra_100,
               v_alta_erp.monto_erp_gestion,
               v_monto_transito,
               v_monto_sigep.monto_periodo_anterior,
               p_monto_sigep,
               1
            );
	     end if;        
    end if;       
  end if;
  
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;