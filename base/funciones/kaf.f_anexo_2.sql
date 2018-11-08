CREATE OR REPLACE FUNCTION kaf.f_anexo_2 (
  p_id_usuario integer,
  p_c31 varchar,
  p_partida varchar,
  p_monto_sigep numeric,
  p_id_periodo_anexo integer
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
    v_registro_erp			record;
    v_resp 					numeric;
    v_sum					numeric;
    v_i 					integer;
	v_diferencia    		numeric;
BEGIN
               
      select 
             ac.nro_cbte_asociado,
             par.id_partida,
             sum(ac.monto_compra_orig_100) as monto_compra_100
             into 
             v_registro_erp
      from kaf.tclasificacion_partida cla
      inner join kaf.tclasificacion clas on clas.id_clasificacion=cla.id_clasificacion
      left join kaf.tactivo_fijo ac on ac.id_clasificacion = clas.id_clasificacion 
      inner join pre.tpartida par on par.id_partida = cla.id_partida
      where ac.estado = 'alta' and ac.nro_cbte_asociado = p_c31 and par.codigo= p_partida
      group by
             ac.nro_cbte_asociado,
             par.id_partida;               
     
  		if ( v_registro_erp.nro_cbte_asociado is not null) then   
			if v_registro_erp.monto_compra_100 <> p_monto_sigep then  
                       
                v_diferencia = v_registro_erp.monto_compra_100 - p_monto_sigep;
                
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
                     v_registro_erp.id_partida,
                     p_c31,
                     v_diferencia,
                     p_monto_sigep,
                     v_registro_erp.monto_compra_100,
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