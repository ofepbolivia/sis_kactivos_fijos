CREATE OR REPLACE FUNCTION kaf.f_get_cantidad_hijos (
  p_id_clasificacion integer,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:     Sistema de Activos Fijos
 FUNCION:     kaf.f_get_cantidad_hijos
 DESCRIPCION: Obtiene la cantidad de hijos y nietos de los activos fijos
 AUTOR:       BVP
 FECHA:       19/04/2018
 COMENTARIOS: 
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION: 
 AUTOR:     
 FECHA:   
***************************************************************************/
DECLARE

  v_nombre_funcion        text;
  v_resp              varchar='';
  v_re              integer[];
  v_cont          integer[];
  v_record_ids      record;
  v_arra            integer[];
  
  v_contador_g	 integer=0;
  v_contador 	integer=0; 
  v_record	record;
  v_index INTEGER;
BEGIN

    v_nombre_funcion = 'kaf.f_get_cantidad_hijos';

  IF(p_transaccion='CONT_HIJOS')THEN
    for v_record in SELECT cla.id_clasificacion,cla.codigo
    				FROM kaf.tclasificacion cla
       		        WHERE cla.id_clasificacion = p_id_clasificacion
                    LOOP
  			SELECT count(ac.id_clasificacion) as contador
            into v_contador
            FROM  kaf.tactivo_fijo ac 
            where ac.id_clasificacion = v_record.id_clasificacion;
    end loop;       
  ELSIF(p_transaccion='CONT_NIETOS')THEN
  	for v_record in SELECT cla.id_clasificacion,cla.codigo
                    from kaf.tclasificacion cla 
                    where cla.id_clasificacion_fk = p_id_clasificacion
				    LOOP
            SELECT count(ac.id_clasificacion) as contador
             into v_contador
            FROM  kaf.tactivo_fijo ac 
            where ac.id_clasificacion = v_record.id_clasificacion;
		    v_contador_g = v_contador_g + v_contador; 
    end loop;
    v_contador = v_contador_g;
      
	END IF;   

    RETURN v_contador::varchar;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;