CREATE OR REPLACE FUNCTION kaf.f_activo_tipo (
  code integer,
  tipe text
)
RETURNS numeric AS
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
        into resp
        from kaf.tactivo_fijo_valores
        where id_activo_fijo_valor=code and tipo='alta';
        
    if resp is not null then 
    	if tipe = 'reval' then 
          select monto_vigente_orig_100
          into rev 
          from tt_detalle_depreciacion
          where kaf.f_tam_codigo(codigo)=resp  and  tipo like '%reval%';
          dev = rev;
        
    	elsif tipe = 'ajuste' then
    
       	  select monto_vigente_orig_100
          into rev 
          from tt_detalle_depreciacion
          where kaf.f_tam_codigo(codigo)=resp  and  tipo like '%ajuste%';
          dev = rev;    
          
   		elsif tipe = 'baja'then
    
          select monto_vigente_orig_100
          into rev 
          from tt_detalle_depreciacion
          where kaf.f_tam_codigo(codigo)=resp  and  tipo like '%baja%';
          dev = rev;  
            
		elsif tipe = 'transito' then
        
          select monto_vigente_orig_100
          into rev 
          from tt_detalle_depreciacion
          where kaf.f_tam_codigo(codigo)=resp  and  tipo like '%transito%';
          dev = rev;
        
        elsif tipe = 'leasing' then
        
          select monto_vigente_orig_100
          into rev 
          from tt_detalle_depreciacion
          where kaf.f_tam_codigo(codigo)=resp  and  tipo like '%leasing%';
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