CREATE OR REPLACE FUNCTION kaf.f_activo_ajuste (
  code varchar
)
RETURNS date AS
$body$
DECLARE
resp      record;
dev       varchar;
val       varchar;
rev       varchar;
ver       varchar;
fec       date;
BEGIN
        select codigo,fecha_ini_dep
        into resp
        from kaf.tactivo_fijo_valores
        where kaf.f_tam_codigo(codigo)= kaf.f_tam_codigo(code) and tipo='alta';
        
    if resp.codigo is not null then 
    
        select fecha_ini_dep
        into rev 
        from kaf.tactivo_fijo_valores
        where kaf.f_tam_codigo(codigo)= kaf.f_tam_codigo(resp.codigo) and  tipo like '%reval%';
        dev = rev;
        
        if rev is null then
        select fecha_ini_dep
        into ver 
        from kaf.tactivo_fijo_valores
        where kaf.f_tam_codigo(codigo)=kaf.f_tam_codigo(resp.codigo)  and (tipo like '%ajuste%' or tipo like '%alta%');        
        dev =resp.fecha_ini_dep;
        end if;
                 
  else 
    dev = null;        
    end if;        
                                    
return dev;            
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;