CREATE OR REPLACE FUNCTION kaf.f_calc_inc_act_ges_ant (
  p_fecha_reporte date,
  p_fecha_ini_dep date,
  p_id_activo_fijo_valor_original integer,
  p_tipo_modificacion varchar,
  p_monto_vig numeric,
  p_cond boolean,
  p_monto_actualiz numeric
)
RETURNS numeric AS
$body$
DECLARE
	ufv_of_fecha_reporte  			numeric;
    ufv_of_fecha_ini_depreciacion 	numeric;
    v_inc_ges_ant					numeric;
    v_fecha_ini_dep					date;
    v_monto_vigente_orig			numeric;
    v_resp							numeric;
BEGIN

  select  oficial into ufv_of_fecha_reporte
  from param.ttipo_cambio
  where fecha = (p_fecha_reporte  - interval '1 year')
  and id_moneda = 3;

  IF p_id_activo_fijo_valor_original is null THEN
  		v_fecha_ini_dep = p_fecha_ini_dep;
        v_monto_vigente_orig = p_monto_vig;

  ELSE
  	  IF p_tipo_modificacion = 'ajuste_pas_act' THEN
      	v_fecha_ini_dep =  p_fecha_ini_dep;
        v_monto_vigente_orig = p_monto_vig;

      ELSE

        select fecha_ini_dep, monto_vigente_orig  into v_fecha_ini_dep, v_monto_vigente_orig
        from kaf.tactivo_fijo_valores
        where id_activo_fijo_valor = p_id_activo_fijo_valor_original;

      END IF;
  END IF;

  select oficial into ufv_of_fecha_ini_depreciacion
  from param.ttipo_cambio
  where fecha =	v_fecha_ini_dep
  and id_moneda = 3;

  v_inc_ges_ant =  ((ufv_of_fecha_reporte / ufv_of_fecha_ini_depreciacion)- 1) * v_monto_vigente_orig;

  IF v_monto_vigente_orig > 0 then
    IF v_inc_ges_ant < 0 THEN
    	v_inc_ges_ant = 0.00;
    END IF;
  END IF;
  
  IF p_cond THEN
  	v_resp = v_inc_ges_ant;
  ELSE

  	v_resp = p_monto_actualiz - v_inc_ges_ant - v_monto_vigente_orig;
  END IF;

  return v_resp;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;
