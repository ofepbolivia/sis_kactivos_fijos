CREATE OR REPLACE FUNCTION kaf.f_fecha_reva_activo (
  code varchar
)
RETURNS date AS
$body$
DECLARE
resp 				date;
fec_rev 			date;
fec_sin_rev			date;
BEGIN
/*funcion que verifica si el activo tuvo una revalorizacion 
devolviendo la fecha inicio depreciacion  de la revalorizacion.
caso contrario devuelve la fecha del activo con su fecha inicio depreciacion
*/
    select fecha_ini_dep
    into fec_rev 
    from kaf.tactivo_fijo_valores acv
    where acv.id_activo_fijo = (select ac.id_activo_fijo
                                from kaf.tactivo_fijo ac 
                                where ac.codigo=kaf.f_tam_codigo(code))
    and acv.tipo like '%reval%'
    limit 1;


    if fec_rev is not null then        
		resp = fec_rev;
	else 
      select fecha_ini_dep
		into fec_sin_rev
      from kaf.tactivo_fijo_valores acv
      where acv.id_activo_fijo = (select ac.id_activo_fijo
                                  from kaf.tactivo_fijo ac 
                                  where ac.codigo=kaf.f_tam_codigo(code))
                         and acv.tipo like '%alta%'
                         limit 1;				                                  
		resp = fec_sin_rev;
    end if;        
    return resp;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;