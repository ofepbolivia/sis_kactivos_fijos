CREATE OR REPLACE FUNCTION kaf.f_acualiza_id_uo_activos_fijos (
)
RETURNS boolean AS
$body$
DECLARE
 v_record		record;
BEGIN

for v_record in
    select taf.codigo,get.id_uo
from kaf.tactivo_fijo taf
left join adq.tcotizacion_det cot on cot.id_cotizacion_det=taf.id_cotizacion_det
left join adq.tsolicitud_det sold on sold.id_solicitud_det=cot.id_solicitud_det
left join adq.tsolicitud sol on sol.id_solicitud=sold.id_solicitud
left join orga.tuo get on get.id_uo=sol.id_uo
where get.nombre_unidad is not null and taf.codigo is not null
LOOP
	update kaf.tactivo_fijo set
	id_uo= v_record.id_uo
    where codigo=''||v_record.codigo||'';
end loop;
return true;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;
