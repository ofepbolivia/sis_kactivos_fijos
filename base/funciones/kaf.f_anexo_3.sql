CREATE OR REPLACE FUNCTION kaf.f_anexo_3 (
  p_id_usuario integer,
  p_id_periodo_anexo integer,
  p_id_gestion integer,
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
    v_anexo_1				record;

BEGIN   

	--C31 EN TRANSITO GESTION ANTERIOR ANEX_1
    for 	v_anexo_1 in 
                          select par.codigo,
                          		 anex.c31
                      from kaf.tanexo anex 
                      inner join pre.tpartida par on par.id_partida = anex.id_partida 
                      where anex.id_periodo_anexo = (p_id_periodo_anexo - 1) and anex.tipo_anexo = 1
                      group by par.codigo,
                      			anex.c31
                          
          loop 

      --INGRESADOS AL ERP EN EL PERIODO    
                   select 
                         par.id_partida,
                         af.nro_cbte_asociado as c31,
                         sum(af.monto_compra_orig_100) as monto_erp_100,
                         af.id_uo as uni_solici
                         into
                         v_registro_anex3
                    from kaf.tactivo_fijo af
                    inner join kaf.tclasificacion cla on cla.id_clasificacion = af.id_clasificacion
                    inner join kaf.tclasificacion_partida clapa on clapa.id_clasificacion = cla.id_clasificacion
                    inner join pre.tpartida par on par.id_partida = clapa.id_partida and par.id_gestion = p_id_gestion
                    where af.nro_cbte_asociado like '%'||v_anexo_1.c31||'%' and par.codigo = v_anexo_1.codigo
                    and af.estado='alta'
                    and af.fecha_ini_dep between p_fecha_ini and p_fecha_fin
                    group by par.id_partida,
                    af.nro_cbte_asociado,
                    af.id_uo;
                             

        if v_registro_anex3.id_partida is not null then     
            insert into kaf.tanexo
              (id_usuario_reg,
              id_periodo_anexo,
              id_partida,
              id_uo,
              c31,
              monto_erp,
              tipo_anexo
              )
              values
              (p_id_usuario,
               p_id_periodo_anexo,
               v_registro_anex3.id_partida,
               v_registro_anex3.uni_solici,
               v_anexo_1.c31,
               v_registro_anex3.monto_erp_100,
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