CREATE OR REPLACE FUNCTION kaf.f_comparacion_movimiento_activo (
  p_accion varchar
)
RETURNS text AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Activo Fijos
 FUNCION: 		kaf.f_comparacion_movimiento_activo
 DESCRIPCION:   Funcion que compara devuelve activos con diferencia entre sus datos y las
 de su ultimo movimiento.
 Puede regularizar los datos del activo, segun el ultimo movimiento que haya sufrido
 solo movimiento del tipo ('asig', 'devol', 'transf', 'tranfdep')
 AUTOR: 		 Breydi vasquez
 FECHA:	        14/12/2020
 COMENTARIOS:
***************************************************************************/
DECLARE
v_reg	 			record;
v_af				record;
v_count 			integer=0;
v_asig				varchar='asig: (';
v_devol				varchar='devol: (';
v_transf			varchar='transf: (';
v_tranfdep			varchar='tranfdep: (';
v_resp				text;

BEGIN


  FOR v_af IN (
              select
                  afj.codigo,
                  afj.id_activo_fijo,
                  afj.en_deposito,
                  afj.id_funcionario,
                  afj.id_persona,
                  afj.id_oficina,
                  afj.id_deposito,
                  afj.fecha_asignacion,
                  afj.ubicacion,
                  afj.en_deposito,
                  afj.prestamo,
                  afj.fecha_dev_prestamo,
                  afj.id_depto

                from kaf.tactivo_fijo afj)
  LOOP

                          select
                                  mov.id_movimiento,
                                  mov.id_funcionario,
                                  mov.id_funcionario_dest,
                                  mov.id_persona,
                                  mov.id_oficina,
                                  mov.id_deposito_dest,
                                  mov.id_deposito,
                                  mov.fecha_mov,
                                  mov.direccion,
                                  mov.prestamo,
                                  mov.fecha_dev_prestamo,
                                  mov.id_depto_dest,
                                  cat.codigo
                          into v_reg
                          from kaf.tmovimiento mov
                          inner join param.tcatalogo cat on cat.id_catalogo = mov.id_cat_movimiento
                          left join wf.testado_wf ew on ew.id_estado_wf = mov.id_estado_wf
                          where mov.id_movimiento in
                          (select id_movimiento from kaf.tmovimiento_af maf where maf.id_activo_fijo = v_af.id_activo_fijo)
                          and cat.codigo in ('asig', 'devol', 'transf', 'tranfdep')
                          and mov.estado = 'finalizado'
                          and ew.fecha_reg is not null
                          order by ew.fecha_reg desc
                          limit 1;


  						IF v_reg.codigo = 'asig' THEN

                        	IF  v_af.id_funcionario != COALESCE(v_reg.id_funcionario, v_reg.id_funcionario_dest) or v_af.id_deposito is not null THEN
                            	v_asig = v_asig || v_af.id_activo_fijo||',';

                                IF p_accion = 'si' THEN
                                      update kaf.tactivo_fijo set
                                        en_deposito = 'no',
                                        id_deposito = null,
                                        id_funcionario = coalesce(v_reg.id_funcionario, v_reg.id_funcionario_dest),
                                        id_persona = v_reg.id_persona,
                                        id_oficina = coalesce(v_reg.id_oficina, v_af.id_oficina),
                                        fecha_asignacion = v_reg.fecha_mov,
                                        ubicacion = v_reg.direccion,
                                        prestamo = v_reg.prestamo,
                                        fecha_dev_prestamo = v_reg.fecha_dev_prestamo
                                      where id_activo_fijo = v_af.id_activo_fijo;
                                END IF;
                            END IF;

                        ELSIF v_reg.codigo = 'devol' THEN
                        	IF v_af.id_funcionario is not null THEN

                            	v_devol = v_devol || v_af.id_activo_fijo||',';

                                IF p_accion = 'si' THEN
                                    update kaf.tactivo_fijo set
                                      en_deposito = 'si',
                                      id_funcionario = null,
                                      id_persona = null,
                                      fecha_asignacion = v_reg.fecha_mov,
                                      ubicacion = 'Depósito',
                                      id_deposito = v_reg.id_deposito
                                    where id_activo_fijo = v_af.id_activo_fijo;
                                END IF;
                            END IF;

                        ELSIF v_reg.codigo = 'transf' THEN

                        	IF  v_af.id_funcionario is null or v_af.id_funcionario != v_reg.id_funcionario_dest THEN
                            	v_transf = v_transf || v_af.id_activo_fijo||',';

                                IF p_accion = 'si' THEN
                                  	update kaf.tactivo_fijo set
                                      en_deposito = 'no',
                                      id_funcionario = v_reg.id_funcionario_dest,
                                      id_persona = v_reg.id_persona,
                                      id_oficina = coalesce(v_reg.id_oficina, v_af.id_oficina),
                                      fecha_asignacion = v_reg.fecha_mov,
                                      ubicacion = v_reg.direccion
                                  	where id_activo_fijo = v_af.id_activo_fijo;
                                END IF;
                            END IF;

                        ELSIF v_reg.codigo = 'tranfdep' THEN

                        	IF v_af.id_deposito != v_reg.id_deposito_dest or v_af.id_depto != v_reg.id_depto_dest THEN
                            	v_tranfdep = v_tranfdep || v_af.id_activo_fijo||',';

                                IF p_accion = 'si' THEN
                                  update kaf.tactivo_fijo set
                                    id_depto = v_reg.id_depto_dest,
                                    id_deposito = v_reg.id_deposito_dest
                                  where id_activo_fijo = v_af.id_activo_fijo;
                                END IF;
                            END IF;

                        END IF;


END LOOP;

	v_resp = v_asig ||' AF '||v_devol||' AF '||v_transf||' AF '||v_tranfdep;

return v_resp;


END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;
