CREATE OR REPLACE FUNCTION kaf.f_range (
  id_co integer
)
RETURNS varchar AS
$body$
DECLARE
resp 		varchar;
cont		integer;
yes 		varchar;
tam			integer;
BEGIN
    select codigo
	into resp		
    from tt_detalle_depreciacion             
    where id_activo_fijo_valor=id_co;
    
cont = char_length(resp);
if cont=13 then
	tam = 0;
elsif cont=14 then
	tam = 1;
elsif cont=17  then
	tam = 2;
elsif cont=20 then
	tam = 3;
end if;   

	if tam=2 then
    	yes=substr(resp,0,14);
    elsif tam=3 then
    	yes=substr(resp,0,17);
    else
    	yes=resp;
	end if;
return yes;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;