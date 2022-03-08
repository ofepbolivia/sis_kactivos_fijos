CREATE OR REPLACE FUNCTION kaf.f_anexo_4 (
  p_id_usuario integer,
  p_id_periodo_anexo integer,
  p_id_gestion integer,
  p_fecha_ini date,
  p_fecha_fin date
)
RETURNS void AS
$body$
DECLARE
    v_registro_anex4	record;
    v_sigep				record;
    v_erp				record;
    v_diferencia		numeric;
    v_partida			integer;
BEGIN

---C31 DEL DETALLE SIGEP QUE NO ESTA EN EL ERP EN EL PERIODO(trimestre)

	for v_sigep in  (select 
    				par.id_partida,	
    	            de.c31,
        	       sum(de.monto_sigep) as monto_sigep
            from kaf.tdetalle_sigep de 
            inner join pre.tpartida par on par.codigo = de.nro_partida and par.id_gestion = p_id_gestion        
            where de.id_periodo_anexo = p_id_periodo_anexo
            and de.c31 not in (
                            select af.nro_cbte_asociado
                            from kaf.tactivo_fijo af
                            where af.fecha_ini_dep between p_fecha_ini and p_fecha_fin
                            and af.estado = 'alta'
                            )
            group by 
                    par.id_partida,
                    de.c31)
		loop                                         
            if v_sigep.id_partida is not null then 

                v_diferencia = 0 - v_sigep.monto_sigep;
                
                    insert into kaf.tanexo
                      (id_usuario_reg,
                      id_periodo_anexo,
                      id_partida,
                      c31,
                      monto_sigep,
                      diferencia,
                      tipo_anexo
                      )
                      values
                      (p_id_usuario,
                       p_id_periodo_anexo,
                       v_sigep.id_partida,
                       v_sigep.c31,
                       v_sigep.monto_sigep,
                       v_diferencia,
                       4
                    );
              end if;                      
         end loop;  
          
 --C31 DEL ERP QUE NO ESTA EN EL DETALLE SIGEP DEL PERIODO(trimestre)
     
    for v_erp in   select                   
                      pa.id_partida,
                      ac.nro_cbte_asociado as c31,
                      sum(ac.monto_compra_orig_100) as monto_erp_100
                  from kaf.tactivo_fijo ac 
                  inner join kaf.tclasificacion cla on cla.id_clasificacion = ac.id_clasificacion
                  inner join kaf.tclasificacion_partida par on par.id_clasificacion = cla.id_clasificacion
                  inner join pre.tpartida pa on pa.id_partida = par.id_partida and pa.id_gestion = p_id_gestion
                  where ac.fecha_ini_dep between p_fecha_ini and p_fecha_fin and ac.estado = 'alta'
                  and ac.nro_cbte_asociado not in (select c31
                                                    from kaf.tdetalle_sigep 
                                                    where id_periodo_anexo = p_id_periodo_anexo)
                  group by 
                  pa.id_partida,
                  ac.nro_cbte_asociado

        loop          
          if v_erp.id_partida is not null then 
          
              v_diferencia = v_erp.monto_erp_100 - 0;
              
                  insert into kaf.tanexo
                    (id_usuario_reg,
                    id_periodo_anexo,
                    id_partida,
                    c31,
                    monto_erp,
                    diferencia,
                    tipo_anexo
                    )
                    values
                    (p_id_usuario,
                     p_id_periodo_anexo,
                     v_erp.id_partida,
                     v_erp.c31,
                     v_erp.monto_erp_100,
                     v_diferencia,
                     4
                  );
            end if;         
        end loop;        
       
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;