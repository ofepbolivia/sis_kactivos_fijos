CREATE OR REPLACE FUNCTION kaf.f_activo_rev (
  cod integer,
  fecha date
)
RETURNS varchar AS
$body$
DECLARE
resp 			varchar;
dev				varchar;
val				varchar;
rev 			varchar;
ver				varchar;
fec 			date;
BEGIN
            select codigo
            into dev
            from kaf.tactivo_fijo_valores
            where id_activo_fijo_valor=cod and (tipo='reval' or tipo='ajuste');
            
      if dev is null then
      
            select codigo
            into resp
            from kaf.tactivo_fijo_valores
            where id_activo_fijo_valor=cod and tipo='alta';
        if resp is not null then
        
            select codigo
            into ver
            from kaf.tactivo_fijo_valores
            where substr(codigo,0,14)=resp and (tipo='reval' or tipo='ajuste_red' or tipo='ajuste');                    
            if ver is not null then                      
            val=null;
            else 
            val =resp;
 			end if;
            
        end if;
     else
     	if (extract(year from fec::date)=extract(year from now()::date))then 
            val=dev;
            elsif(extract(year from fecha::date)<=extract(year from now()::date)-1)then 
            val=null;
         end if;   
          val =dev;       
     end if;                 	      
	        	            
return val;            
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;