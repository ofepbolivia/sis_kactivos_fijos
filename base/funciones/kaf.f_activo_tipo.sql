CREATE OR REPLACE FUNCTION kaf.f_activo_tipo (
  code varchar,
  tipe text,
  fecha date
)
RETURNS numeric AS
$body$
DECLARE
resp 			varchar;
dev				numeric;
val				varchar;
rev 			numeric;
ver				varchar;
fec 			date;
BEGIN
        select codigo
        into resp
        from kaf.tactivo_fijo_valores
        where codigo=code and tipo='alta'
        limit 1;
        
    if resp is not null then 
    	if tipe = 'reval' then 
          select sum(monto_vigente_orig_100)
          into rev 
          from kaf.tactivo_fijo_valores
          where kaf.f_tam_codigo(codigo)=resp  and  tipo like '%reval%'
          and id_moneda=1 and fecha_inicio <= fecha;
          dev = rev;
        
    	elsif tipe = 'ajuste' then
    
       	  select sum(monto_vigente_orig_100)
          into rev 
          from kaf.tactivo_fijo_valores
          where kaf.f_tam_codigo(codigo)=resp  and  tipo like '%ajuste%'
          and id_moneda=1 and fecha_inicio <= fecha;
          dev = rev;    
          
   		elsif tipe = 'baja'then
    
          select sum(monto_vigente_orig_100)
          into rev 
          from kaf.tactivo_fijo_valores
          where kaf.f_tam_codigo(codigo)=resp  and  tipo like '%baja%'
          and id_moneda=1 and fecha_inicio <= fecha;
          dev = rev;  
            
		elsif tipe = 'transito' then
        
          select sum(monto_vigente_orig_100)
          into rev 
          from kaf.tactivo_fijo_valores
          where kaf.f_tam_codigo(codigo)=resp  and  tipo like '%transito%'
          and id_moneda=1 and fecha_inicio <= fecha;
          dev = rev;
        
        elsif tipe = 'leasing' then
        
          select sum(monto_vigente_orig_100)
          into rev 
          from kaf.tactivo_fijo_valores
          where kaf.f_tam_codigo(codigo)=resp  and  tipo like '%leasing%'
          and id_moneda=1 and  fecha_inicio<= fecha;
          dev = rev;
          
        else 
        dev = null;        
	    end if;        
	end if;        
            	        	            
return dev;            
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;