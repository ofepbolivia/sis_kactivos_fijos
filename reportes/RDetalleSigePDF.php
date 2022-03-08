<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';

class RDetalleSigePDF extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;

    function Header() {        
        
		$fecha_ini = $this->objParam->getParametro('fecha_ini');
		$fecha_fin = $this->objParam->getParametro('fecha_fin');		
        $this->Ln(3);
        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 10,5,35,20);
        $this->ln(5);
        $this->SetFont('','B',11);
        $this->Cell(0,5,"REPORTE DETALLE SIGEP",0,1,'C');
        $this->Cell(0,5,"DEL ".$fecha_ini." HASTA ".$fecha_fin,0,1,'C');
        $this->Ln(10);

        $this->SetFont('','B',12);
		$h = 0;
        $this->Cell(10,$h,"N°",1,0,'C');
        $this->Cell(45,$h,"N° PARTIDA",1,0,'C');
        $this->Cell(55,$h,"PREVENTIVO (C31)",1,0,'C');
        $this->Cell(45,$h,"MONTO SIGEP",1,0,'C');        
    }

    function setDatos($datos) {

        $this->datos = $datos;
        $this->SetHeaderMargin(1);
        $this->SetAutoPageBreak(TRUE, 12);
        $this->ancho_hoja = $this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-10;
        $this->SetMargins(35, 34.5, 35);        
    }

    function  generarReporte()
    {

        $this->AddPage();

        $fill = 1;
        $contador = 1;
		$total = 0 ; 
        foreach ($this->datos as $record) {
        	
        $this->Ln();
        $total += $record['monto_sigep'];    
		$this->Cell(10,0,$contador, 1, 0, 'L', false, '', 0, false, 'T', 'C');
		$this->Cell(45,0,$record['nro_partida'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
		$this->Cell(55,0,$record['c31'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
		$this->Cell(45,0,$record['monto_sigep'], 1, 0, 'R', false, '', 0, false, 'T', 'C');            
        $contador++;

        }
		$this->Ln();
		$this->setFont('','B',13);		
		$this->Cell(55,0, 'TOTAL ', 1, 0, 'L', false, '', 0, false, 'T', 'C');
		$this->Cell(55,0, '', 1, 0, 'L', false, '', 0, false, 'T', 'C');
		$this->Cell(45, 0, $total, 1, 0, 'R', false, '', 0, false, 'T', 'C');



    }
}
?>