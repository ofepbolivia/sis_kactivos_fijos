CREATE OR REPLACE FUNCTION kaf.f_verificar_hijos (
  p_nivel integer,
  p_id_clasificacion integer,
  p_id_activo_fijo integer,
  p_fecha_ini date,
  p_fecha_fin date
)
RETURNS boolean AS
$body$
DECLARE

    v_record			record;
	v_contador			integer = 0;
    v_cont				integer = 0;
BEGIN
	raise notice '--------------------------------------------------------------------------------------';	
    raise notice 'datos entrada: %, %, %, %, %', p_nivel, p_id_activo_fijo, p_id_clasificacion, p_fecha_ini, p_fecha_fin; 
	IF(p_nivel = 0)THEN
	 	select count(tcn.id_clasificacion) 
        into v_contador
        from kaf.tclasificacion tc 
        inner join kaf.tclasificacion tch on tch.id_clasificacion_fk = tc.id_clasificacion
        inner join kaf.tclasificacion tcn on tcn.id_clasificacion_fk = tch.id_clasificacion
        inner join kaf.tactivo_fijo taf on taf.id_clasificacion = tcn.id_clasificacion
        where tc.id_clasificacion = p_id_clasificacion and taf.fecha_ini_dep between p_fecha_ini and p_fecha_fin;
        raise notice ' CERO : %', v_contador;	
    ELSIF(p_nivel = 1)THEN
    	select count(tch.id_clasificacion)
        into v_contador
        from kaf.tclasificacion tc 
        inner join kaf.tclasificacion tch on tch.id_clasificacion_fk = tc.id_clasificacion
        inner join kaf.tactivo_fijo taf on taf.id_clasificacion = tch.id_clasificacion
        where tc.id_clasificacion = p_id_clasificacion and taf.fecha_ini_dep between p_fecha_ini and p_fecha_fin;
        raise notice ' UNO : %', v_contador;
    ELSIF(p_nivel = 2)THEN
    	select count(tc.id_clasificacion)
        into v_contador
        from kaf.tclasificacion tc 
        inner join kaf.tactivo_fijo taf on taf.id_clasificacion = tc.id_clasificacion
        where taf.id_activo_fijo = p_id_activo_fijo and taf.fecha_ini_dep between p_fecha_ini and p_fecha_fin;
        raise notice ' DOS : %', v_contador;
    END IF;
    raise notice '#####################################################################################';
    
    if(v_contador > 0)then
    	return true;
    else
    	return false;
    end if;
	
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;