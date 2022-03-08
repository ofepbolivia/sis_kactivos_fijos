CREATE OR REPLACE FUNCTION kaf.f_activo_si_no_rev (
  cod varchar
)
RETURNS varchar AS
$body$
DECLARE
resp      varchar;
dev       integer;
val       varchar;
rev       varchar;
ver       varchar;
fec       date;
id_activo     integer;
valor     integer;
BEGIN
            select id_activo_fijo_valor
            into dev
            from kaf.tactivo_fijo_valores
            where codigo=cod;           

            select id_activo_fijo_valor
            into valor
            from kaf.tactivo_fijo_valores
            where id_activo_fijo_valor=dev and (tipo like '%reval%' or tipo like '%ajuste%');            
                        
      if valor is null then
      
            select codigo
            into resp
            from kaf.tactivo_fijo_valores
            where id_activo_fijo_valor=dev and tipo='alta';
        if resp is not null then
      select codigo
            into ver
            from kaf.tactivo_fijo_valores
            where kaf.f_tam_codigo(codigo)=resp and (tipo='reval' or tipo='ajuste_red' or tipo='ajuste' or tipo='ajuste_normal');                    
            if ver is not null then                      
            val='si';
            else 
            val ='no';
      end if;        
        else
            val=null;
        end if;
     ELSE
          val=null;          

  end if;
                      
return val;            
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;