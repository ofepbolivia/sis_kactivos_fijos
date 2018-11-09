CREATE OR REPLACE FUNCTION kaf.f_anexo_1 (
  p_id_usuario integer,
  p_c31 varchar,
  p_partida varchar,
  p_id_periodo_anexo integer,
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
  v_registro			record;
  v_monto_transito		numeric;
  v_registro_mon		record;
  v_year 				integer;
BEGIN

	v_year = to_char(now(),'yyyy');
    
	--monto compra al 100%
    
      select ac.nro_cbte_asociado,
              sum(ac.monto_compra_orig_100) as monto_compra_100
              into 
              v_registro
      from kaf.tactivo_fijo ac 
      where ac.nro_cbte_asociado = p_c31 
      and ac.fecha_reg >= p_fecha_ini 
      group by ac.nro_cbte_asociado;      
      

    --alta en el erp gestion   
  select 
          par.id_partida,
         sum(af.monto_compra_orig_100) as monto_erp_gestion
         into 
         v_registro_mon
  from kaf.tactivo_fijo af 
  left join kaf.tclasificacion cla on cla.id_clasificacion = af.id_clasificacion
  left join kaf.tclasificacion_partida clapa on clapa.id_clasificacion = cla.id_clasificacion
  left join pre.tpartida par on par.id_partida = clapa.id_partida
  inner join param.tgestion ges on ges.id_gestion = par.id_gestion
  inner join kaf.tmovimiento_af mof on mof.id_activo_fijo = af.id_activo_fijo
  inner join kaf.tmovimiento mov on mov.id_movimiento = mof.id_movimiento
  where af.nro_cbte_asociado= p_c31 and af.estado='alta' and mov.estado='finalizado'
  and mov.fecha_mov between ('01/01/'||(v_year-1)::varchar)::date and ('31/12/'||(v_year)::varchar)::date  
  and par.codigo= p_partida
  group by 
         par.id_partida;


         
  if v_registro_mon.id_partida is not null then 
        v_monto_transito = v_registro.monto_compra_100 - v_registro_mon.monto_erp_gestion;
        insert into kaf.tanexo
          (id_usuario_reg,
          id_periodo_anexo,
          id_partida,
          c31,
          monto_contrato,
          monto_erp,
          monto_transito,
          tipo_anexo
          )
          values
          (p_id_usuario,
           p_id_periodo_anexo,
           v_registro_mon.id_partida,
           p_c31,
           v_registro.monto_compra_100,
           v_registro_mon.monto_erp_gestion,
           v_monto_transito,
           1
        );
   end if;
  
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;