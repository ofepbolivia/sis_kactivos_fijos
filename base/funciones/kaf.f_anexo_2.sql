CREATE OR REPLACE FUNCTION kaf.f_anexo_2 (
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
Descripción: generador de datos para anexo 2 
*/
DECLARE
	v_registro 				record;
    v_monto_erp				record;
    v_resp 					numeric;
    v_sum					numeric;
    v_i 					integer;
	v_diferencia    		numeric;
BEGIN

		--MONTO EN EL ERP EN EL PERIODO
          select 
              pa.id_partida,
              ac.nro_cbte_asociado as c31,
             sum(ac.monto_compra_orig_100) as monto_compra_100
             into 
             v_monto_erp
          from kaf.tactivo_fijo ac 
          inner join kaf.tclasificacion cla on cla.id_clasificacion = ac.id_clasificacion
          inner join kaf.tclasificacion_partida par on par.id_clasificacion = cla.id_clasificacion
          inner join pre.tpartida pa on pa.id_partida = par.id_partida and pa.id_gestion = p_id_gestion
          where ac.estado = 'alta'  and ac.nro_cbte_asociado like '%'||p_c31||'%' and pa.codigo = p_partida
          and ac.fecha_ini_dep between p_fecha_ini and p_fecha_fin 
          group by 
          pa.id_partida,
          ac.nro_cbte_asociado;
            
                 
  		if  v_monto_erp.id_partida is not null then 
                  
                  v_diferencia = v_monto_erp.monto_compra_100 - p_monto_sigep; 
                                        
                if ((v_monto_erp.monto_compra_100 - p_monto_sigep) <> 0) then 

                  insert into  kaf.tanexo
                    (id_usuario_reg,
                    id_periodo_anexo,
                    id_partida,
                    c31,
                    diferencia,
                    monto_sigep,
                    monto_erp,
                    tipo_anexo
                    )
                  values
                    (p_id_usuario,
                    p_id_periodo_anexo,                     
                     v_monto_erp.id_partida,
                     v_monto_erp.c31,
                     v_diferencia,
                     p_monto_sigep,
                     v_monto_erp.monto_compra_100,
                     2
                  );
                  
            end if;
        end if;
            
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;