<?php
require_once(dirname(__FILE__).'/../../pxp/pxpReport/DataSource.php');
require_once(dirname(__FILE__).'/../reportes/RKardexAFxls.php');
require_once(dirname(__FILE__).'/../reportes/RReporteGralAFXls.php');
require_once(dirname(__FILE__).'/../reportes/RRespInventario.php');
require_once(dirname(__FILE__).'/../reportes/RDepreciacionXls.php');
require_once(dirname(__FILE__).'/../reportes/RDepreciacionPDF.php');
require_once(dirname(__FILE__).'/../reportes/RDepreciacionActulizadoPDF.php');
require_once(dirname(__FILE__).'/../reportes/RDepreciacionPeriodoXls.php');
require_once(dirname(__FILE__).'/../reportes/RDepreciacionActulizadaXls.php');
require_once(dirname(__FILE__).'/../reportes/RDepreciacionActulizadaPDF.php');
require_once(dirname(__FILE__).'/../reportes/RKardexAFPDF.php');


class ACTReportes extends ACTbase {

    function reporteKardexAF(){
        $this->objParam->defecto('ordenacion','fecha_mov');
        $this->objParam->defecto('dir_ordenacion','asc');

        //Verifica si la petición es para elk reporte en excel o de grilla
        if($this->objParam->getParametro('tipo_salida')=='reporte'){
            $this->objFunc=$this->create('MODReportes');
            $datos=$this->objFunc->reporteKardex($this->objParam);
            $this->reporteKardexAFXls($datos);
        } else {
            if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
                $this->objReporte = new Reporte($this->objParam,$this);
                $this->res = $this->objReporte->generarReporteListado('MODReportes','reporteKardex');
            } else{
                $this->objFunc=$this->create('MODReportes');
                $this->res=$this->objFunc->reporteKardex($this->objParam);
            }
            $this->res->imprimirRespuesta($this->res->generarJson());
        }

    }

    function reporteKardexAFXls(){
    	
		if($this->objParam->getParametro('def')=='csv'){
        	$nombreArchivo = uniqid(md5(session_id()).'KardexAF').'.xls';
		}else{
			$nombreArchivo = uniqid(md5(session_id()).'KardexAF').'.pdf';
		}	

        //Recuperar datos
        $this->objFunc = $this->create('MODReportes');
        $repDatos = $this->objFunc->reporteKardex($this->objParam);

        $dataSource = $repDatos;

        //Parámetros básicos
        $tamano = 'LETTER';
        $orientacion = 'L';
        $titulo = 'Kardex Activos Fijos';

        $this->objParam->addParametro('orientacion',$orientacion);
        $this->objParam->addParametro('tamano',$tamano);
        $this->objParam->addParametro('titulo_archivo',$titulo);
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        //Generación de reporte
        if($this->objParam->getParametro('def')=='csv'){
        	$reporte = new RKardexAFxls($this->objParam);
			$reporte->setDataSet($dataSource->getDatos());
	        $reporte->datosHeader($dataSource->getDatos(), $this->objParam->getParametro('id_entrega'));
	        $reporte->generarReporte();	
					
		}else{									
            $this->objReporteFormato=new RKardexAFPDF($this->objParam);
            $this->objReporteFormato->setDatos($dataSource->getDatos());
            $this->objReporteFormato->generarReporte();
            $this->objReporteFormato->output($this->objReporteFormato->url_archivo,'F');						
		}        

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function ReporteGralAF(){
        $this->definirFiltros();
        //Verifica si la petición es para elk reporte en excel o de grilla
        if($this->objParam->getParametro('tipo_salida')=='reporte'){
            $this->objFunc=$this->create('MODReportes');
            $datos=$this->objFunc->reporteGralAF($this->objParam);
            $this->reporteGralAFXls($datos);
        } else {
            if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
                $this->objReporte = new Reporte($this->objParam,$this);
                $metodo=$this->objParam->getParametro('rep_metodo_list');
                $this->res = $this->objReporte->generarReporteListado('MODReportes',$metodo);
            } else {
                $this->objFunc=$this->create('MODReportes');

                eval('$this->res=$this->objFunc->'.$this->objParam->getParametro('rep_metodo_list').'($this->objParam);');



                //$this->res=$this->objFunc->reporteGralAF($this->objParam);
            }
            $this->res->imprimirRespuesta($this->res->generarJson());
        }

    }

    function reporteGralAFXls($datos){
        $nombreArchivo = uniqid(md5(session_id()).'ReporteGralAF').'.xls';

        //Recuperar datos
        $this->objFunc = $this->create('MODReportes');

        //Parámetros básicos
        $tamano = 'LETTER';
        $orientacion = 'L';
        $titulo = 'Reporte Activos Fijos';

        $this->objParam->addParametro('orientacion',$orientacion);
        $this->objParam->addParametro('tamano',$tamano);
        $this->objParam->addParametro('titulo_archivo',$titulo);
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        //Generación de reporte
        $reporte = new RReporteGralAFXls($this->objParam);
        $reporte->setTipoReporte($this->objParam->getParametro('reporte'));
        $reporte->setTituloReporte($this->objParam->getParametro('titulo_reporte'));
        $reporte->setMoneda($this->objParam->getParametro('desc_moneda'));
        $reporte->setDataSet($datos->getDatos());

        $reporte->generarReporte();

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function listarDepreciacionDeptoFechas(){
        $this->objParam->defecto('ordenacion','id_depto');
        $this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODReportes','listarDepreciacionDeptoFechas');
        } else {
            $this->objFunc=$this->create('MODReportes');
            $this->res=$this->objFunc->listarDepreciacionDeptoFechas($this->objParam);
        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarRepDepreciacion(){
        $this->definirFiltros();

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODReportes','listarRepDepreciacion');
        } else {
            $this->objFunc=$this->create('MODReportes');
            $this->res=$this->objFunc->listarRepDepreciacion($this->objParam);
        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //(FEA) 07/02/2018 Reporte de Depreciación de activos fijos
    function reporteDepreciacion(){
        $this->definirFiltros();
        $this->objFunc = $this->create('MODReportes');
        $this->res = $this->objFunc->listarRepDepreciacion($this->objParam);


        //Genera el nombre del archivo (aleatorio + titulo)
        if($this->objParam->getParametro('tipo')=='pdf'){
            $nombreArchivo = uniqid(md5(session_id()).'[Depreciación AF]').'.pdf';
        }
        else{
            $nombreArchivo = uniqid(md5(session_id()).'[Depreciación AF]').'.xls';
        }

        $this->objParam->addParametro('orientacion','L');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('titulo_archivo','Depreciación AF');


        if($this->objParam->getParametro('tipo')=='pdf'){
        	
        	if($this->objParam->getParametro('tipo_repo')=='gepa' || $this->objParam->getParametro('tipo_repo')==''){
	            //Instancia la clase de pdf
	            $this->objReporteFormato=new RDepreciacionPDF ($this->objParam);
	            $this->objReporteFormato->setDatos($this->res->datos);
	            $this->objReporteFormato->generarReporte();
	            $this->objReporteFormato->output($this->objReporteFormato->url_archivo,'F');
			}else{
	            $this->objReporteFormato=new RDepreciacionActulizadaPDF ($this->objParam);
	            $this->objReporteFormato->setDatos($this->res->datos);
	            $this->objReporteFormato->generarReporte();
	            $this->objReporteFormato->output($this->objReporteFormato->url_archivo,'F');				
			}
			
        }
        else{
        	if($this->objParam->getParametro('tipo_repo')=='gepa' || $this->objParam->getParametro('tipo_repo')==''){
        		
	            $reporte = new RDepreciacionXls($this->objParam);
	            $reporte->setDatos($this->res->datos);
	            $reporte->generarReporte();
			}else{
				
	            $reporte = new RDepreciacionActulizadaXls($this->objParam);
	            $reporte->setDatos($this->res->datos);
	            $reporte->generarReporte();				
			}
        }

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

    function definirFiltros() {
        $this->objParam->defecto('ordenacion','codigo');
        $this->objParam->defecto('dir_ordenacion','asc');

        //Filtros generales
        if($this->objParam->getParametro('id_activo_fijo')!=''){
            $this->objParam->addFiltro("afij.id_activo_fijo = ".$this->objParam->getParametro('id_activo_fijo'));
        }
        if($this->objParam->getParametro('id_clasificacion')!=''){
            $this->objParam->addFiltro("afij.id_clasificacion in (
					WITH RECURSIVE t(id,id_fk) AS (
    				SELECT l.id_clasificacion,l.id_clasificacion_fk
    				FROM kaf.tclasificacion l
    				WHERE l.id_clasificacion = ".$this->objParam->getParametro('id_clasificacion')."
    				UNION ALL
    				SELECT l.id_clasificacion,l.id_clasificacion_fk
    				FROM kaf.tclasificacion l, t
    				WHERE l.id_clasificacion_fk = t.id
					)
					SELECT id
					FROM t)");

		}
		if($this->objParam->getParametro('id_clasificacion_multi')!=''){
			$this->objParam->addFiltro("afij.id_clasificacion in (
					WITH RECURSIVE t(id,id_fk) AS (
    				SELECT l.id_clasificacion,l.id_clasificacion_fk
    				FROM kaf.tclasificacion l
    				WHERE l.id_clasificacion in (".$this->objParam->getParametro('id_clasificacion_multi').")
    				UNION ALL
    				SELECT l.id_clasificacion,l.id_clasificacion_fk
    				FROM kaf.tclasificacion l, t
    				WHERE l.id_clasificacion_fk = t.id
					)
					SELECT id
					FROM t)");
		}		
		if($this->objParam->getParametro('denominacion')!=''){
			$this->objParam->addFiltro("afij.denominacion ilike ''%".$this->objParam->getParametro('denominacion')."%''");
		}
		if($this->objParam->getParametro('fecha_compra')!=''){
			$this->objParam->addFiltro("afij.fecha_compra >= ''".$this->objParam->getParametro('fecha_compra')."''");
		}
		if($this->objParam->getParametro('fecha_ini_dep')!=''){
			$this->objParam->addFiltro("afij.fecha_ini_dep = ''".$this->objParam->getParametro('fecha_ini_dep')."''");
		}
		if($this->objParam->getParametro('estado')!=''){			
				$this->objParam->addFiltro("afij.estado = ''".$this->objParam->getParametro('estado')."''");	
		}
		if($this->objParam->getParametro('id_centro_costo')!=''){
			$this->objParam->addFiltro("afij.id_centro_costo in (

					WITH RECURSIVE t(id,id_fk) AS (
    				SELECT l.id_uo_hijo,l.id_uo_padre
    				FROM orga.testructura_uo l
    				WHERE l.id_uo_hijo = ".$this->objParam->getParametro('id_uo')."
    				UNION ALL
    				SELECT l.id_uo_hijo,l.id_uo_padre
    				FROM orga.testructura_uo l, t
    				WHERE l.id_uo_padre = t.id
					)
					SELECT id
					FROM t)");
        }
        if($this->objParam->getParametro('ubicacion')!=''){
            $this->objParam->addFiltro("afij.ubicacion ilike ''%".$this->objParam->getParametro('ubicacion')."%''");
        }
        if($this->objParam->getParametro('id_oficina')!=''){
            $this->objParam->addFiltro("afij.id_oficina = ".$this->objParam->getParametro('id_oficina'));
        }
        if($this->objParam->getParametro('id_funcionario')!=''){
            $this->objParam->addFiltro("afij.id_funcionario = ''".$this->objParam->getParametro('id_funcionario')."''");
        }
        if($this->objParam->getParametro('id_uo')!=''){
            $this->objParam->addFiltro("uo.id_uo in (
					WITH RECURSIVE t(id,id_fk) AS (
    				SELECT l.id_uo_hijo,l.id_uo_padre
    				FROM orga.testructura_uo l
    				WHERE l.id_uo_hijo = ".$this->objParam->getParametro('id_uo')."
    				UNION ALL
    				SELECT l.id_uo_hijo,l.id_uo_padre
    				FROM orga.testructura_uo l, t
    				WHERE l.id_uo_padre = t.id
					)
					SELECT id
					FROM t)");
        }
        if($this->objParam->getParametro('id_funcionario_compra')!=''){

        }
        if($this->objParam->getParametro('id_lugar')!=''){

        }
        if($this->objParam->getParametro('af_transito')!=''){
            if($this->objParam->getParametro('af_transito')=='tra'){
                $this->objParam->addFiltro("afij.estado = ''transito''");
            } else if($this->objParam->getParametro('af_transito')=='af') {
                $this->objParam->addFiltro("afij.estado != ''transito''");
            }
        }
        if($this->objParam->getParametro('af_tangible')!=''&&$this->objParam->getParametro('af_tangible')!='ambos'){
            $this->objParam->addFiltro("cla.tipo_activo = ''".$this->objParam->getParametro('af_tangible')."''");
        }
        if($this->objParam->getParametro('id_depto')!=''){
            if($this->objParam->getParametro('id_depto')!=0){
                $this->objParam->addFiltro("afij.id_depto = ".$this->objParam->getParametro('id_depto'));
            }
        }
        if($this->objParam->getParametro('id_deposito')!=''){
            $this->objParam->addFiltro("afij.id_deposito = ".$this->objParam->getParametro('id_deposito'));
        }
        if($this->objParam->getParametro('monto_inf')!=''){
            $this->objParam->addFiltro("afij.monto_compra >= ".$this->objParam->getParametro('monto_inf'));
        }
        if($this->objParam->getParametro('monto_sup')!=''){
            $this->objParam->addFiltro("afij.monto_compra <= ".$this->objParam->getParametro('monto_sup'));
        }
        if($this->objParam->getParametro('fecha_compra_max')!=''){
            $this->objParam->addFiltro("afij.fecha_compra <= ''".$this->objParam->getParametro('fecha_compra_max')."''");
        }
        if($this->objParam->getParametro('nro_cbte_asociado')!=''){
            $this->objParam->addFiltro("afij.nro_cbte_asociado ilike ''%".$this->objParam->getParametro('nro_cbte_asociado')."%''");
        }
        if($this->objParam->getParametro('id_lugar')!=''){
            $this->objParam->addFiltro("afij.id_oficina in (select id_oficina from orga.toficina where id_lugar = ".$this->objParam->getParametro('id_lugar').")");
        }
    }

    function ReporteRespInventario(){
        $nombreArchivo = 'RespInventario'.uniqid(md5(session_id())).'.pdf';

        $this->definirFiltros();

        $this->objFunc=$this->create('MODReportes');
        $dataSource=$this->objFunc->listarRepAsignados($this->objParam);

        //parametros basicos
        $orientacion = 'L';
        $titulo = 'Responsable-Inventario';

        $width = 160;
        $height = 80;

        $this->objParam->addParametro('orientacion',$orientacion);
        //$this->objParam->addParametro('tamano',array($width, $height));
        $this->objParam->addParametro('titulo_archivo',$titulo);
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $clsRep = $dataSource->getDatos();
        $reporte = new RRespInventario($this->objParam);

        $reporte->setOficina($this->objParam->getParametro('nombre_oficina'));
        $reporte->setTipo($this->objParam->getParametro('tipo'));
        $reporte->setColumna($this->objParam->getParametro('columna'));
        $reporte->datosHeader($dataSource->getDatos());
        $reporte->generarReporte();
        $reporte->output($reporte->url_archivo,'F');

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }
    
	function reporteDepreciacionPeriodo(){
        $this->definirFiltros();
        $this->objFunc = $this->create('MODReportes');
        $this->res = $this->objFunc->listarRepDepreciacion($this->objParam);
		//$this->res->imprimirRespuesta($this->res->generarJson());
       
        if($this->objParam->getParametro('tipo')=='pdf'){
            $nombreArchivo = uniqid(md5(session_id()).'[DepreciaciónPeriodo AF]').'.pdf';
        }
        else{
            $nombreArchivo = uniqid(md5(session_id()).'[DepreciaciónPeriodo AF]').'.xls';
        }

        $this->objParam->addParametro('orientacion','L');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('titulo_archivo','Depreciación AF');


        if($this->objParam->getParametro('tipo')=='pdf'){
        	
		        $this->objReporteFormato=new RDepreciacionActulizadoPDF ($this->objParam);
		        $this->objReporteFormato->setDatos($this->res->datos);
		        $this->objReporteFormato->generarReporte();
		        $this->objReporteFormato->output($this->objReporteFormato->url_archivo,'F');        		
        }
        else{

            $reporte = new RDepreciacionPeriodoXls($this->objParam);
            $reporte->setDatos($this->res->datos);
            $reporte->generarReporte();
        }

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());	
		
	}
   function reporteKAF()
    {
        //$this->objParam->defecto('ordenacion', 'fecha_mov');
        $this->objParam->defecto('ordenacion', 'id_activo_fijo');
        $this->objParam->defecto('dir_ordenacion', 'asc');

        if ($this->objParam->getParametro('fecha_desde') != '' && $this->objParam->getParametro('fecha_hasta') != '') {
            $this->objParam->addFiltro("(mov.fecha_mov::date  BETWEEN ''%" . $this->objParam->getParametro('fecha_mov') . "%''::date  and ''%" . $this->objParam->getParametro('fecha_hasta') . "%''::date)");
        }

        if ($this->objParam->getParametro('fecha_desde') != '' && $this->objParam->getParametro('fecha_hasta') == '') {
            $this->objParam->addFiltro("(mov.fecha_mov::date  >= ''%" . $this->objParam->getParametro('fecha_desde') . "%''::date)");
        }

        if ($this->objParam->getParametro('fecha_desde') == '' && $this->objParam->getParametro('fecha_hasta') != '') {
            $this->objParam->addFiltro("(mov.fecha_mov::date  <= ''%" . $this->objParam->getParametro('fecha_hasta') . "%''::date)");
        }

        if($this->objParam->getParametro('id_activo_fijo')!=''){
            $this->objParam->addFiltro("af.id_activo_fijo= ".$this->objParam->getParametro('id_activo_fijo'));
        }

        //  Verifica si la petición es para el reporte en excel o de grilla
        if ($this->objParam->getParametro('tipo_salida') == 'reporte') {
            $this->objFunc = $this->create('MODReportes');
            $datos = $this->objFunc->reporteKAF($this->objParam);
            $this->reporteKardexAFXls($datos);
        }

        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODReportes', 'reporteKAF');
        } else {
            $this->objFunc = $this->create('MODReportes');
            $this->res = $this->objFunc->reporteKAF($this->objParam);
        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}
?>