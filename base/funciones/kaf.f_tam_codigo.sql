CREATE OR REPLACE FUNCTION kaf.f_tam_codigo (
  cod varchar
)
RETURNS varchar AS
$body$
DECLARE
 large				integer;
 posi_re 			integer;
 posi_aj			integer;
 posi_me			integer;
 resp 				varchar;
 inte				varchar;
BEGIN 
	large = char_length(cod);
    posi_re  = position('-R' in cod);
    posi_aj  = position('-A' in cod);
    posi_me  = position('-M' in cod);
     
    if large in (10,13,14,16) then 
    	  resp = cod;
    else 
		  inte = substr(cod,0,
          case when posi_re !=0 then
          posi_re 
          when posi_me != 0 then
          posi_me 
          else posi_aj end);     
          resp = inte;
	end if;
    return resp;          
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;