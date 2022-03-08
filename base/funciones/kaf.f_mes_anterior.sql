CREATE OR REPLACE FUNCTION kaf.f_mes_anterior (
  fecha date,
  perido varchar
)
RETURNS text AS
$body$
/**************************************************************************
 FUNCION: 		pxp.f_mes_anterior
 DESCRIPCION:   devuelve el ultimo dia y mes anterior, y anio si este fuera primer mes 
 				del anio
 AUTOR: 	    BVP	
 FECHA:	        21/09/2018
 ***************************************************************************/

DECLARE
dia 			integer;                  
mes 			integer;
anno 			integer;  
fecha_literal 	text;                                  
v_fecha 		date;
v_dia 			integer;
mes_new 		integer;
anno_new 		integer;
mes_array  		integer[];
large			integer;
i				integer;
BEGIN

	mes_array = ARRAY[1,2,3,4,5,6,7,8,9,10,11,12];
    large = array_length(mes_array,1);		

          dia=to_char(fecha,'dd'); 
          mes=to_char(fecha,'mm');
          anno=to_char(fecha,'yyyy');
if perido='mes' then 
    if mes in (5,7,10,12) then
          v_dia = 30;
      elsif mes in (1,2,4,6,8,9,11) then
          v_dia = 31;
      elsif mes = 3 then
          v_dia = 28;
	  end if;   	         
      
    for i in 0.. large loop
    	if mes_array[i] = 1 then
        	mes_new = mes_array[12];
        	anno_new = anno-1;
    	elsif mes_array[i] = mes then
        	mes_new = mes_array[i-1];
            anno_new = anno;
		end if;            
    end loop;
    
    v_fecha = (v_dia::varchar||'/'||mes_new::varchar||'/'||anno_new::varchar)::varchar;
elsif perido='anio' then     
	v_fecha = '31/12/'||anno::varchar::integer -1;
end if;    
    return v_fecha;          
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;