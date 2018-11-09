CREATE OR REPLACE FUNCTION kaf.f_anexo_3 (
  p_id_usuario integer,
  p_c31 varchar,
  p_partida varchar,
  p_id_periodo_anexo integer,
  p_fecha_ini date,
  p_fecha_fin date
)
RETURNS void AS
$body$
DECLARE
	v_registro_anex3 		record;
    v_fecha_ini				date;
    v_fecha_fin				date;
    v_perido_anterior		record;
    v_mes_1					integer;
    v_mes_2					integer;
    v_anno					integer;
    v_text_1				text;
    v_text_2				varchar;
    id						integer;

BEGIN   

for v_perido_anterior in 
        select anex.c31,
        anex.id_partida
        from kaf.tanexo anex
        inner join kaf.tperiodo_anexo pe on pe.id_periodo_anexo=anex.id_periodo_anexo
        where   pe.id_periodo_anexo = (p_id_periodo_anexo-1) and anex.tipo_anexo = 1
	loop
    	            
    select 
         par.id_partida,
        sum(af.monto_compra_orig_100) as monto_compra_100
        into 
        v_registro_anex3
    from kaf.tactivo_fijo af
    left join kaf.tclasificacion cla on cla.id_clasificacion = af.id_clasificacion
    left join kaf.tclasificacion_partida clapa on clapa.id_clasificacion = cla.id_clasificacion
    inner join pre.tpartida par on par.id_partida = clapa.id_partida and par.id_gestion = clapa.id_gestion
    where af.nro_cbte_asociado = v_perido_anterior.c31 and par.id_partida = v_perido_anterior.id_partida
    --and af.estado='registrado'
    and af.fecha_reg between p_fecha_ini::date and p_fecha_fin::date
    group by par.id_partida;
  
	if (v_registro_anex3.id_partida is not null) then     
        insert into kaf.tanexo
          (id_usuario_reg,
          id_periodo_anexo,
          id_partida,
          c31,
          monto_sigep,
          tipo_anexo
          )
          values
          (p_id_usuario,
           p_id_periodo_anexo,
           v_registro_anex3.id_partida,
           p_c31,
           v_registro_anex3.monto_compra_100,
           3
        );
     end if;  
 end loop;     
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;