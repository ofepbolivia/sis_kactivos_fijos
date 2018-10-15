CREATE OR REPLACE FUNCTION kaf.f_movimiento_af_dep_insert (
)
RETURNS trigger AS
$body$
  DECLARE
    v_id_activ_valor	    integer;
	v_id_movi_af_dep		bigint;
    v_id_input_movi			bigint;

  BEGIN
    IF (TG_OP='INSERT')then
      	BEGIN
            v_id_activ_valor = NEW.id_activo_fijo_valor;
            v_id_input_movi  = NEW.id_movimiento_af_dep;

          if (v_id_activ_valor in (
           select id_activo_fijo_valor
           from kaf.tmovimiento_af_dep))then

           select max(id_movimiento_af_dep)
           into  v_id_movi_af_dep
           from kaf.tmovimiento_af_dep            
           where id_activo_fijo_valor = v_id_activ_valor;

           update pruebas.tafdep set
                  id_af_dep = v_id_movi_af_dep
                  where id_activo_valor=v_id_activ_valor;  

          else 
           insert into kaf.tafdep
              (id_af_dep,
              id_activo_valor)
              values
              (v_id_input_movi,
              v_id_activ_valor
              );
              
          end if;               
    	END;
    END IF;
    RETURN NEW;
  END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;