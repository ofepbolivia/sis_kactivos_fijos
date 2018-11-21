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
  num_partida varchar
) AS
$body$
DECLARE

BEGIN
	--REGISTRO DE CANTIDADES ADJUDICADAS Y PRECIOS UNITARIOS PARA EL MONTO CONTRATO
    
    return query 
         select 
             cotde.id_cotizacion_det,
             afij.nro_cbte_asociado,
             case when mo.id_moneda = 1 then
              cotde.cantidad_adju * cotde.precio_unitario
             when mo.id_moneda = 2 then
              cotde.cantidad_adju *  (cotde.precio_unitario * 6.86) 
              end,
             par.id_partida,
             par.codigo
      from kaf.tactivo_fijo afij 
      inner join alm.tpreingreso_det prede on prede.id_preingreso_det = afij.id_preingreso_det
      inner join alm.tpreingreso prei on prei.id_preingreso = prede.id_preingreso
      inner join adq.tcotizacion cot on cot.id_cotizacion = prei.id_cotizacion
      inner join adq.tcotizacion_det cotde on cotde.id_cotizacion = cot.id_cotizacion
      inner join adq.tsolicitud_det solde on solde.id_solicitud_det = cotde.id_solicitud_det
      inner join pre.tpartida par on par.id_partida = solde.id_partida
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
             mo.id_moneda;
	return;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100 ROWS 1000;