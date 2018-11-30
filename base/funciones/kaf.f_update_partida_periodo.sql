CREATE OR REPLACE FUNCTION kaf.f_update_partida_periodo (
  p_id_periodo_anexo integer,
  p_tipo_anexo integer,
  p_id_partida integer,
  p_total_grupo numeric
)
RETURNS void AS
$body$
DECLARE

BEGIN
------------ACTUALIZACION DE PARTIDA PERIODO CON SUS RESPECTIVOS GRUPOS Y MONTOS 

		 if    p_tipo_anexo = 1 then
                update kaf.tpartida_periodo set		 
                importe_anexo1 = p_total_grupo
                where id_periodo_anexo = p_id_periodo_anexo and id_partida = p_id_partida;
    	 elsif p_tipo_anexo = 2 then 
                update kaf.tpartida_periodo set		 
                importe_anexo2 = p_total_grupo
                where id_periodo_anexo = p_id_periodo_anexo and id_partida = p_id_partida;         
         elsif p_tipo_anexo = 3 then 
                update kaf.tpartida_periodo set		 
                importe_anexo3 = p_total_grupo
                where id_periodo_anexo = p_id_periodo_anexo and id_partida = p_id_partida;         
         elsif p_tipo_anexo = 4 then 
                update kaf.tpartida_periodo set		 
                importe_anexo4 = p_total_grupo
                where id_periodo_anexo = p_id_periodo_anexo and id_partida = p_id_partida;         
         end if;  
	return;            
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;