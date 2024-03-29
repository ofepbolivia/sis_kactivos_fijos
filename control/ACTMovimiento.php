<?php
/**
 * @package pXP
 * @file gen-ACTMovimiento.php
 * @author  (admin)
 * @date 22-10-2015 20:42:41
 * @description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */
require_once(dirname(__FILE__) . '/../../pxp/pxpReport/ReportWriter.php');
require_once(dirname(__FILE__) . '/../reportes/RMovimiento2.php');
require_once(dirname(__FILE__) . '/../reportes/RMovimientoUpdate.php');
require_once(dirname(__FILE__) . '/../../pxp/pxpReport/DataSource.php');
require_once(dirname(__FILE__) . '/../reportes/RDetalleDepXls.php');
require_once(dirname(__FILE__) . '/../reportes/RDetalleDepPDF.php');
require_once(dirname(__FILE__) . '/../reportes/RAsig_Trans_DevAFXls.php');

class ACTMovimiento extends ACTbase
{

    function listarMovimiento()
    {
        $this->objParam->defecto('ordenacion', 'id_movimiento');
        $this->objParam->defecto('dir_ordenacion', 'desc');

        if ($this->objParam->getParametro('cod_movimiento') != '') {
            if ($this->objParam->getParametro('cod_movimiento') != '%') {
                $arrFilter = explode(',', $this->objParam->getParametro('cod_movimiento'));
                $filter = "(";
                foreach ($arrFilter as $key => $fil) {
                    $filter .= "''" . $fil . "''" . ",";
                }
                $filter = substr($filter, 0, strlen($filter) - 1) . ")";
                $this->objParam->addFiltro("cat.codigo in " . $filter);
            }
        }

        if ($this->objParam->getParametro('id_activo_fijo') != '') {
            $this->objParam->addFiltro("mov.id_movimiento  in (select id_movimiento from kaf.tmovimiento_af  maf where maf.id_activo_fijo = " . $this->objParam->getParametro('id_activo_fijo') . ")");
        }

        if ($this->objParam->getParametro('id_movimiento') != '') {
            $this->objParam->addFiltro("mov.id_movimiento = " . $this->objParam->getParametro('id_movimiento'));
        }

        if ($this->objParam->getParametro('historico') == 'no') {
            $this->objParam->addFiltro("estado <> ''finalizado''");
        }

        //fRnk: reporte detalle depreciación HR01163
        if(!empty($this->objParam->getParametro('repdetdep'))){
            if ($this->objParam->getParametro('cod_movimiento') == 'deprec,actua' && $this->objParam->getParametro('repdetdep') == 'si') {
                $this->objParam->addFiltro("estado = ''finalizado''");
            }
        }

        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODMovimiento', 'listarMovimiento');
        } else {
            $this->objFunc = $this->create('MODMovimiento');

            $this->res = $this->objFunc->listarMovimiento($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarMovimiento()
    {
        $this->objFunc = $this->create('MODMovimiento');
        if ($this->objParam->insertar('id_movimiento')) {
            $this->res = $this->objFunc->insertarMovimiento($this->objParam);
        } else {
            $this->res = $this->objFunc->modificarMovimiento($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarMovimiento()
    {
        $this->objFunc = $this->create('MODMovimiento');
        $this->res = $this->objFunc->eliminarMovimiento($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function generarDetMovimiento()
    {
        $this->objFunc = $this->create('MODMovimiento');
        $this->res = $this->objFunc->generarDetMovimiento($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarReporteMovimientoMaestro()
    {
        $this->objFunc = $this->create('MODMovimiento');
        $cbteHeader = $this->objFunc->listarReporteMovimientoMaestro($this->objParam);
        if ($cbteHeader->getTipo() == 'EXITO') {
            return $cbteHeader;
        } else {
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }

    }

    function listarReporteMovimientoDetalle()
    {
        $this->objFunc = $this->create('MODMovimiento');
        $cbteHeader = $this->objFunc->listarReporteMovimientoDetalle($this->objParam);
        if ($cbteHeader->getTipo() == 'EXITO') {
            return $cbteHeader;
        } else {
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }

    }

    function generarReporteMovimiento()
    {
        //fRnk: Firma Digital (Alta)
        if (!empty($this->objParam->getParametro('firma_digital')) && $this->objParam->getParametro('firma_digital') == 'si') {
            $nombre_archivo = $this->objParam->getParametro('nombre_archivo');
            if (empty($nombre_archivo)) {
                $nombreArchivo = 'Movimientos' . uniqid(md5(session_id())) . '.pdf';
                $obj = $this->listarReporteMovimientoMaestro();
                $objDetalle = $this->listarReporteMovimientoDetalle();
                $tamano = 'LETTER';
                $orientacion = 'L';
                $titulo = 'Consolidado';
                $this->objParam->addParametro('orientacion', $orientacion);
                $this->objParam->addParametro('tamano', $tamano);
                $this->objParam->addParametro('titulo_archivo', $titulo);
                $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
                $reporte = new RMovimiento2($this->objParam);
                $reporte->datosHeader($obj->getDatos(), $objDetalle->getDatos(), $this->objParam->getParametro('id_movimiento'));
                $reporte->generarReporte();
                $reporte->output($reporte->url_archivo, 'F');
                $this->mensajeExito = new Mensaje();
                $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
                $this->mensajeExito->setArchivoGenerado($nombreArchivo);
                $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
            } else {
                $this->mensajeExito = new Mensaje();
                $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombre_archivo, 'control');
                $this->mensajeExito->setArchivoGenerado($nombre_archivo);
                $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
            }
        } else {
            $nombre_archivo = $this->objParam->getParametro('nombre_archivo');
            if (!empty($nombre_archivo)) {
                $this->mensajeExito = new Mensaje();
                $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombre_archivo, 'control');
                $this->mensajeExito->setArchivoGenerado($nombre_archivo);
                $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
            }
            else{
                $nombreArchivo = 'Movimientos' . uniqid(md5(session_id())) . '.pdf';

                $obj = $this->listarReporteMovimientoMaestro();
                $objDetalle = $this->listarReporteMovimientoDetalle();

                $dataMaestro = $obj->getDatos();
                $dataDetalle = $objDetalle->getDatos();


                //parametros basicos
                $tamano = 'LETTER';
                $orientacion = 'L';
                $titulo = 'Consolidado';


                $this->objParam->addParametro('orientacion', $orientacion);
                $this->objParam->addParametro('tamano', $tamano);
                $this->objParam->addParametro('titulo_archivo', $titulo);
                $this->objParam->addParametro('nombre_archivo', $nombreArchivo);

                //Instancia la clase de pdf
                $reporte = new RMovimiento2($this->objParam);
                $reporte->datosHeader($obj->getDatos(), $objDetalle->getDatos());
                $reporte->generarReporte();
                $reporte->output($reporte->url_archivo, 'F');
                $this->mensajeExito = new Mensaje();
                $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
                $this->mensajeExito->setArchivoGenerado($nombreArchivo);
                $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
            }
        }
    }


    function generarReporteMovimiento_bk()
    {


        $obj = $this->listarReporteMovimientoMaestro();
        $objDetalle = $this->listarReporteMovimientoDetalle();

        $dataMaestro = $obj->getDatos();
        $dataDetalle = $objDetalle->getDatos();


        $reporte = new RMovimiento();
        $reporte->setDataMaster($dataMaestro);
        $reporte->setDataDetalle($dataDetalle);
        $nombreArchivo = 'movimiento_af.pdf';

        $reportWriter = new ReportWriter($reporte, dirname(__FILE__) . '/../../reportes_generados/' . $nombreArchivo);
        $reportWriter->writeReport(ReportWriter::PDF);
        $mensajeExito = new Mensaje();
        $mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->res = $mensajeExito;
        $this->res->imprimirRespuesta($this->res->generarJson());

    }

    function siguienteEstadoMovimiento()
    {
        $this->objFunc = $this->create('MODMovimiento');
        $this->objParam->addParametro('id_funcionario_usu', $_SESSION["ss_id_funcionario"]);
        //fRnk: adicionado para Firma Digital
        if (!empty($this->objParam->getParametro('firma_digital')) && $this->objParam->getParametro('firma_digital') == 'si') {
            $this->res = $this->objFunc->siguienteEstadoMovimientoFirmaDigital($this->objParam);
        } else {
            $this->res = $this->objFunc->siguienteEstadoMovimiento($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function anteriorEstadoMovimiento()
    {
        $this->objFunc = $this->create('MODMovimiento');
        $this->objParam->addParametro('id_funcionario_usu', $_SESSION["ss_id_funcionario"]);
        //fRnk: adicionado para Firma Digital
        if (!empty($this->objParam->getParametro('firma_digital')) && $this->objParam->getParametro('firma_digital') == 'si') {
            $this->res = $this->objFunc->anteriorEstadoMovimientoFirmaDigital($this->objParam);
        } else {
            $this->res = $this->objFunc->anteriorEstadoMovimiento($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


    function recuperarDetalleDep()
    {
        $this->objFunc = $this->create('MODMovimiento');
        $cbteHeader = $this->objFunc->listarDatalleDepreciaconReporte($this->objParam);
        if ($cbteHeader->getTipo() == 'EXITO') {
            return $cbteHeader;
        } else {
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }
    }

    function generarReporteDepreciacionXls()
    {
        $nombreArchivo = uniqid(md5(session_id()) . 'RDetalleDepXls') . '.xls';
        $dataSource = $this->recuperarDetalleDep();
        //parametros basicos
        $tamano = 'LETTER';
        $orientacion = 'L';
        $titulo = 'Consolidado';

        $this->objParam->addParametro('orientacion', $orientacion);
        $this->objParam->addParametro('tamano', $tamano);
        $this->objParam->addParametro('titulo_archivo', $titulo);
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);

        $reporte = new RDetalleDepXls($this->objParam);
        $reporte->datosHeader($dataSource->getDatos(), $this->objParam->getParametro('id_movimiento'));
        $reporte->generarReporte();

        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function generarReporteDepreciacion()
    { //fRnk: nuevo reporte depreciación en PDF
        $nombreArchivo = 'RDetalleDepPDF' . uniqid(md5(session_id())) . '.pdf';
        $dataSource = $this->recuperarDetalleDep();
        $tamano = 'LETTER';
        $orientacion = 'L';
        $titulo = 'Consolidado';
        $this->objParam->addParametro('orientacion', $orientacion);
        $this->objParam->addParametro('tamano', $tamano);
        $this->objParam->addParametro('titulo_archivo', $titulo);
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        $reporte = new RDetalleDepPDF($this->objParam);
        $reporte->datosHeader($dataSource->getDatos(), $dataSource->getDatos());
        $reporte->generarReporte();
        $reporte->output($reporte->url_archivo, 'F');
        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function reporteDepreciacion(){
        if ($this->objParam->getParametro('formato_reporte') == '1') {
            $this->generarReporteDepreciacion();

        } else {
            $this->generarReporteDepreciacionXls();
        }
    }
    function generarMovimientoRapido()
    {
        $this->objFunc = $this->create('MODMovimiento');
        $this->res = $this->objFunc->generarMovimientoRapido($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function generarReporteAsig_Trans_DevAFXls()
    {

        $nombreArchivo = uniqid(md5(session_id()) . 'RAsig_Trans_DevAFXls') . '.xls';

        $obj = $this->listarReporteMovimientoMaestro();
        $objDetalle = $this->listarReporteMovimientoDetalle();

        $dataMaestro = $obj->getDatos();
        $dataDetalle = $objDetalle->getDatos();

        //parametros basicos
        $tamano = 'LETTER';
        $orientacion = 'L';
        $titulo = 'AsignacionAF';

        $this->objParam->addParametro('orientacion', $orientacion);
        $this->objParam->addParametro('tamano', $tamano);
        $this->objParam->addParametro('titulo_archivo', $titulo);
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);


        $reporte = new RAsig_Trans_DevAFXls($this->objParam);
        $reporte->datosHeader($dataMaestro, $dataDetalle);
        $reporte->generarReporte();

        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

    function generarReporteMovimientoUpdate()
    {
        //fRnk: Firma Digital
        if (!empty($this->objParam->getParametro('firma_digital')) && $this->objParam->getParametro('firma_digital') == 'si') {
            $nombre_archivo = $this->objParam->getParametro('nombre_archivo');
            if (empty($nombre_archivo)) {
                $nombreArchivo = 'Movimientos' . uniqid(md5(session_id())) . '.pdf';
                $obj = $this->listarReporteMovimientoMaestro();
                $objDetalle = $this->listarReporteMovimientoDetalle();
                $tamano = 'LETTER';
                $orientacion = 'L';
                $titulo = 'Consolidado';
                $this->objParam->addParametro('orientacion', $orientacion);
                $this->objParam->addParametro('tamano', $tamano);
                $this->objParam->addParametro('titulo_archivo', $titulo);
                $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
                $reporte = new RMovimientoUpdate($this->objParam);
                $reporte->datosHeader($obj->getDatos(), $objDetalle->getDatos(), $this->objParam->getParametro('id_movimiento'));
                $reporte->generarReporte();
                $reporte->output($reporte->url_archivo, 'F');
                $this->mensajeExito = new Mensaje();
                $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
                $this->mensajeExito->setArchivoGenerado($nombreArchivo);
                $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
            } else {
                $this->mensajeExito = new Mensaje();
                $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombre_archivo, 'control');
                $this->mensajeExito->setArchivoGenerado($nombre_archivo);
                $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
            }
        } else {
            $nombre_archivo = $this->objParam->getParametro('nombre_archivo');
            if (!empty($nombre_archivo)) {
                $this->mensajeExito = new Mensaje();
                $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombre_archivo, 'control');
                $this->mensajeExito->setArchivoGenerado($nombre_archivo);
                $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
            } else {
                $nombreArchivo = 'Movimientos' . uniqid(md5(session_id())) . '.pdf';
                $obj = $this->listarReporteMovimientoMaestro();
                $objDetalle = $this->listarReporteMovimientoDetalle();
                $dataMaestro = $obj->getDatos();
                $dataDetalle = $objDetalle->getDatos();
                //parametros basicos
                $tamano = 'LETTER';
                $orientacion = 'L';
                $titulo = 'Consolidado';
                $this->objParam->addParametro('orientacion', $orientacion);
                $this->objParam->addParametro('tamano', $tamano);
                $this->objParam->addParametro('titulo_archivo', $titulo);
                $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
                //Instancia la clase de pdf
                $reporte = new RMovimientoUpdate($this->objParam);
                $reporte->datosHeader($obj->getDatos(), $objDetalle->getDatos());
                $reporte->generarReporte();
                $reporte->output($reporte->url_archivo, 'F');
                $this->mensajeExito = new Mensaje();
                $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
                $this->mensajeExito->setArchivoGenerado($nombreArchivo);
                $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
            }
        }
    }

    function listarMovimientoPendienteFirma()
    {
        $this->objParam->defecto('ordenacion', 'id_movimiento');
        $this->objParam->defecto('dir_ordenacion', 'desc');

        if ($this->objParam->getParametro('cod_movimiento') != '') {
            if ($this->objParam->getParametro('cod_movimiento') != '%') {
                $arrFilter = explode(',', $this->objParam->getParametro('cod_movimiento'));
                $filter = "(";
                foreach ($arrFilter as $key => $fil) {
                    $filter .= "''" . $fil . "''" . ",";
                }
                $filter = substr($filter, 0, strlen($filter) - 1) . ")";
                $this->objParam->addFiltro("cat.codigo in " . $filter);
            }
        }

        if ($this->objParam->getParametro('id_activo_fijo') != '') {
            $this->objParam->addFiltro("mov.id_movimiento  in (select id_movimiento from kaf.tmovimiento_af  maf where maf.id_activo_fijo = " . $this->objParam->getParametro('id_activo_fijo') . ")");
        }

        if ($this->objParam->getParametro('id_movimiento') != '') {
            $this->objParam->addFiltro("mov.id_movimiento = " . $this->objParam->getParametro('id_movimiento'));
        }

        if ($this->objParam->getParametro('historico') == 'no') {
            $this->objParam->addFiltro("estado <> ''finalizado''");
        }


        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODMovimiento', 'listarMovimientoPendienteFirma');
        } else {
            $this->objFunc = $this->create('MODMovimiento');
            $this->res = $this->objFunc->listarMovimientoPendienteFirma($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function obtenerDocumentoFirma()
    {
        if ($this->objParam->getParametro('firmado') == 'no') {
            $archivo_fd = $this->objParam->getParametro('nombre_archivo');
            if (empty($archivo_fd)) {
                $archivo_fd = $this->objParam->getParametro('nuevo_archivo');
                $obj = $this->listarReporteMovimientoMaestro();
                $objDetalle = $this->listarReporteMovimientoDetalle();
                $tamano = 'LETTER';
                $orientacion = 'L';
                $titulo = 'Consolidado';
                $this->objParam->addParametro('orientacion', $orientacion);
                $this->objParam->addParametro('tamano', $tamano);
                $this->objParam->addParametro('titulo_archivo', $titulo);
                $this->objParam->addParametro('nombre_archivo', $archivo_fd);
                if ($this->objParam->getParametro('cod_movimiento') == 'alta' || $this->objParam->getParametro('cod_movimiento') == 'deprec' || $this->objParam->getParametro('cod_movimiento') ==  'baja' || $this->objParam->getParametro('cod_movimiento') ==  'reval') {
                    $reporte = new RMovimiento2($this->objParam);
                } else {
                    $reporte = new RMovimientoUpdate($this->objParam);
                }

                $reporte->datosHeader($obj->getDatos(), $objDetalle->getDatos(), $this->objParam->getParametro('id_movimiento'));
                $reporte->generarReporte();
                $reporte->output($reporte->url_archivo, 'F');
                //copy(from,to);
            }
            $file_path_ori = dirname(__FILE__) . '/../../reportes_generados/' . $archivo_fd;
            $pdf = base64_encode(file_get_contents($file_path_ori));
            echo $pdf;
            exit();
        }
    }

    function firmarDocumento()
    {
        $archivo_fd = $this->objParam->getParametro('nombre_archivo');
        $pdf_firmado_base64 = $this->objParam->getParametro('pdf_firmado_base64');
        if (!empty($pdf_firmado_base64)) {
            $base64_data = base64_decode($pdf_firmado_base64, true);
            file_put_contents(dirname(__FILE__) . '/../../reportes_generados/' . $archivo_fd, $base64_data);
            $this->objFunc = $this->create('MODMovimiento');
            $this->res = $this->objFunc->firmarDocumento($this->objParam);
            $this->res->imprimirRespuesta($this->res->generarJson());
        }
    }
}

?>
