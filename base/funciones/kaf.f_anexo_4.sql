CREATE OR REPLACE FUNCTION kaf.f_anexo_4 (
  p_id_usuario integer,
  p_c31 varchar,
  p_monto_sigep numeric,
  p_partida varchar,
  p_id_periodo_anexo integer,
  p_fecha_ini date,
  p_fecha_fin date
)
RETURNS void AS
$body$
DECLARE
    v_registro_anex4	record;
    v_sigep				record;
    v_erp				record;
    v_diferencia		numeric;
BEGIN
    -------sigep queno esten en compra dentro el erp
      select 
             afij.nro_cbte_asociado,
             par.id_partida,
             sum(afij.monto_compra_orig_100) as monto_compra_100  
             into 
             v_sigep    
      from kaf.tactivo_fijo afij 
      left join alm.tpreingreso_det prede on prede.id_preingreso_det = afij.id_preingreso_det
      inner join alm.tpreingreso prei on prei.id_preingreso = prede.id_preingreso
      inner join adq.tcotizacion cot on cot.id_cotizacion = prei.id_cotizacion
      inner join adq.tcotizacion_det cotde on cotde.id_cotizacion = cot.id_cotizacion
      inner join adq.tsolicitud_det solde on solde.id_solicitud_det = cotde.id_solicitud_det
      inner join pre.tpartida par on par.id_partida = solde.id_partida
      inner join adq.tproceso_compra pro on pro.id_solicitud=solde.id_solicitud
      where  afij.nro_cbte_asociado = p_c31 and par.codigo = p_partida
     and  pro.fecha_ini_proc not between p_fecha_ini and p_fecha_fin
     group by 
             afij.nro_cbte_asociado,
            par.id_partida; 
    	
    ------erp del periodo que no estan en detalle_sigep
     select 
             afij.nro_cbte_asociado,
             par.id_partida,
             sum(afij.monto_compra_orig_100) as monto_compra_100  
             into 
             v_erp   
      from kaf.tactivo_fijo afij 
      left join alm.tpreingreso_det prede on prede.id_preingreso_det = afij.id_preingreso_det
      inner join alm.tpreingreso prei on prei.id_preingreso = prede.id_preingreso
      inner join adq.tcotizacion cot on cot.id_cotizacion = prei.id_cotizacion
      inner join adq.tcotizacion_det cotde on cotde.id_cotizacion = cot.id_cotizacion
      inner join adq.tsolicitud_det solde on solde.id_solicitud_det = cotde.id_solicitud_det
      inner join pre.tpartida par on par.id_partida = solde.id_partida
      inner join adq.tproceso_compra pro on pro.id_solicitud=solde.id_solicitud
      where pro.fecha_ini_proc between p_fecha_ini and p_fecha_fin and par.codigo=p_partida
      and afij.nro_cbte_asociado not in (select de.c31
                                            from kaf.tdetalle_sigep de
                                            where de.id_periodo_anexo=p_id_periodo_anexo)
     group by 
             afij.nro_cbte_asociado,
                     par.id_partida; 
                     
                      
     if v_sigep.nro_cbte_asociado is not null  then 
        v_diferencia = v_sigep.monto_compra_100 - p_monto_sigep;
            insert into kaf.tanexo
              (id_usuario_reg,
              id_periodo_anexo,
              id_partida,
              c31,
              monto_sigep,
              monto_erp,
              diferencia,
              tipo_anexo
              )
              values
              (p_id_usuario,
               p_id_periodo_anexo,
               v_sigep.id_partida,
               p_c31,
               p_monto_sigep,
               v_sigep.monto_compra_100,               
               v_diferencia,
               4
            );
       end if; 

     if v_erp.nro_cbte_asociado is not null  then 
        v_diferencia = v_erp.monto_compra_100 - p_monto_sigep;
            insert into kaf.tanexo
              (id_usuario_reg,
              id_periodo_anexo,
              id_partida,
              c31,
              monto_sigep,
              monto_erp,
              diferencia,
              tipo_anexo
              )
              values
              (p_id_usuario,
               p_id_periodo_anexo,
               v_erp.id_partida,
               p_c31,
               p_monto_sigep,
               v_erp.monto_compra_100,               
               v_diferencia,
               4
            );
       end if;       
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;