CREATE OR REPLACE FUNCTION kaf.f_monto_contrato_c31 (
  p_c31 varchar,
  p_partida varchar,
  p_id_gestion integer
)
RETURNS TABLE (
  id_coti integer,
  c31 varchar,
  monto_contrato numeric,
  id_parti integer,
  num_partida varchar,
  id_unidad integer
) AS
$body$
DECLARE
v_gestion 			integer;
BEGIN


	--REGISTRO DE CANTIDADES ADJUDICADAS Y PRECIOS UNITARIOS PARA EL MONTO CONTRATO
    
    return query 
         select 
             cotde.id_cotizacion_det,
             afij.nro_cbte_asociado,
             case when mo.id_moneda = 1 then
              cotde.cantidad_adju * cotde.precio_unitario
             when mo.id_moneda = 2 then
              cotde.cantidad_adju *  (cotde.precio_unitario *(select ti.compra
                                        from param.ttipo_cambio ti 
                                        where ti.fecha = current_date and ti.id_moneda=2)) 
              end,
             par.id_partida,
             par.codigo,
             afij.id_uo --add
      from kaf.tactivo_fijo afij 
      inner join alm.tpreingreso_det prede on prede.id_preingreso_det = afij.id_preingreso_det
      inner join alm.tpreingreso prei on prei.id_preingreso = prede.id_preingreso
      inner join adq.tcotizacion cot on cot.id_cotizacion = prei.id_cotizacion
      inner join adq.tcotizacion_det cotde on cotde.id_cotizacion = cot.id_cotizacion
      inner join adq.tsolicitud_det solde on solde.id_solicitud_det = cotde.id_solicitud_det
      inner join pre.tpartida par on par.id_partida = solde.id_partida and par.id_gestion = p_id_gestion
      inner join param.tmoneda mo on mo.id_moneda = cot.id_moneda
      where  afij.nro_cbte_asociado like  '%'||p_c31||'%' 
      and par.codigo = p_partida

      group by 
      		cotde.id_cotizacion_det,
             afij.nro_cbte_asociado,
             cotde.cantidad_adju,
             cotde.precio_unitario,
             par.id_partida,
             par.codigo,
             mo.id_moneda,
             afij.id_uo;
	return;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100 ROWS 1000;