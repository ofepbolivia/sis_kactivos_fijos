<?php
/**
 *@package pXP
 *@file ACTReporte.php
 *@author  Espinoza Alvarez Franklin
 *@date 08-10-2013 14:41:56
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

require_once(dirname(__FILE__).'/../reportes/RDetalleDepreciacionPDF.php');
require_once(dirname(__FILE__).'/../reportes/RDetalleDepreciacionXLS.php');
require_once(dirname(__FILE__).'/../reportes/RBajaRevalorizacionPDF.php');

class ACTReporte extends ACTbase{

    function reporteDepreciacionXLS(){

        $this->objFunc=$this->create('MODReporte');

        if ($this->objParam->getParametro('formato_reporte') == '1') {
            $this->res=$this->objFunc->reporteDepreciacionPDF($this->objParam);

        } else {
            $this->res=$this->objFunc->reporteDepreciacion($this->objParam);
        }

        //obtener titulo del reporte
        $titulo = 'DetalleDepreciacion';

        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo=uniqid(md5(session_id()).$titulo);


        $this->objParam->addParametro('datos',$this->res->datos);

        if ($this->objParam->getParametro('formato_reporte') == '1') {
            $nombreArchivo.='.pdf';
            $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
            $this->objParam->addParametro('orientacion','L');
            $this->objParam->addParametro('tamano','LEGAL');
            $this->objParam->addParametro('titulo_archivo','Depreciacion');
            $this->objReporteFormato=new RDetalleDepreciacionPDF($this->objParam);
            $this->objReporteFormato->generarReporte();
            $this->objReporteFormato->output($this->objReporteFormato->url_archivo,'F');

        } else {
            $nombreArchivo.='.xls';
            $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
            $this->objReporteFormato=new RDetalleDepreciacionXLS($this->objParam);
            $this->objReporteFormato->imprimeDatos();
            $this->objReporteFormato->generarReporte();
        }
               




        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function listarClasificacion(){
        $this->objFunc=$this->create('MODReporte');
        $this->res=$this->objFunc->listarClasificacion($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    
    function reporteDepreciacion(){

        $this->objFunc=$this->create('MODReporte');
        $this->res=$this->objFunc->reporteDepreciacion($this->objParam);
        //obtener titulo del reporte
        
        $titulo = 'Detalle de Depreciación';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo=uniqid(md5(session_id()).$titulo);
        $nombreArchivo.='.pdf';
        $this->objParam->addParametro('orientacion','L');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        //Instancia la clase de pdf

        $this->objReporteFormato=new RDetalleDepreciacionPDF ($this->objParam);
        $this->objReporteFormato->setDatos($this->res->datos);
        $this->objReporteFormato->generarReporte();
        $this->objReporteFormato->output($this->objReporteFormato->url_archivo,'F');


        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function reporteBajaRevalorizacion(){

        $this->objFunc=$this->create('MODReporte');
        $this->res=$this->objFunc->reporteBajaRevalorizacion($this->objParam);
        //obtener titulo del reporte

        $titulo = 'ActivosRevalorizados';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo=uniqid(md5(session_id()).$titulo);
        $nombreArchivo.='.pdf';
        $this->objParam->addParametro('orientacion','P');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('datos',$this->res->datos);


        //Instancia la clase de pdf

        $this->objReporteFormato=new RBajaRevalorizacionPDF ($this->objParam);

        $this->objReporteFormato->generarReporte();
        $this->objReporteFormato->output($this->objReporteFormato->url_archivo,'F');


        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }
}

?>