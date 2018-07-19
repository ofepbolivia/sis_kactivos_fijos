<?php
class RActivoFijoPDF extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
	var $codigo;
	
    function Header() {
        $height = 30;
        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib'.$_SESSION['_DIR_LOGO'], 5, 8, 60, 15);
        $this->Cell(40, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->SetFontSize(16);
        $this->SetFont('','B');
        $this->Cell(105, $height, 'CUADRO RESUMEN', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Ln();
    }
	
    function setDatos($datos) {
        $this->datos = $datos;
		//$dato =json_encode($this->datos);
		//var_dump($this->datos);exit; 
		
    }
    function generarReporte() {
        $this->reporteActivo();
    }
	
    function  reporteActivo()
    {
     // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $this->AddPage();
        //$this->SetHeaderMargin(10);
        $this->SetMargins(15, 25, 15,true);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $height = 8;
        $width2 = 5;
        $width3 = 46;
        $width4 = 93;
		
		
		//armca caecera de la tabla
		$this->tablewidths=array(90,53,25,30);
        $this->tablealigns=array('L','L','L','L');
        $this->tablenumbers=array(0,0,0,0);
        $this->tableborders=array('TB','TB','TB','TB');
        $this->tabletextcolor=array();
		
	    $RowArray = array(
            			's0'  =>'CODIGO',
            			's1' => 'NOMBRE',   
                        's2' => 'SUB TOTAL',        
                        's3' => 'TOTAL'
         			);
                         
        $this-> MultiRow($RowArray,false,1);


 $sum = 0;
	for ($i=0; $i <count($this->datos) ; $i++) {
		
			
		if($this->datos[$i]["nivel"]==2 ){
	 	 $sum += $this->datos[$i]["hijos"];
		}
		    
	        $this->SetFont('', '',10);			
	        $this->Cell($width3, $height, $this->datos[$i]["codigo_completo_tmp"], 1, 0, 'L', false, '', 0, false, 'T', 'C');	
	        $this->Cell($width3+50, $height, $this->datos[$i]["nombre"], 1, 0, 'L', false, '', 0, false, 'T', 'C');
			
		if($this->datos[$i]["nivel"]==2){
			$this->SetFont('', 'B',11);
			$this->Cell($width2+20, $height, ' ', 1, 0, 'C', false, '', 0, false, 'T', 'C');
			$this->Cell($width2+20, $height, $this->datos[$i]["hijos"], 1, 0, 'R', false, '', 0, false, 'T', 'C');
		}else{
			$this->Cell($width2+20, $height, $this->datos[$i]["hijos"], 1, 0, 'R', false, '', 0, false, 'T', 'C');
			$this->Cell($width2+20, $height, ' ', 1, 0, 'C', false, '', 0, false, 'T', 'C');
		 }
        	$this->Ln();
		}	
		$this->SetFont('', 'B',12);
	    $this->Cell($width3, $height,' ', 1, 0, 'L', false, '', 0, false, 'T', 'C');
	    $this->Cell($width3+50, $height, 'TOTAL ', 1, 0, 'L', false, '', 0, false, 'T', 'C');
		$this->Cell($width2+20, $height, '', 1, 0, 'R', false, '', 0, false, 'T', 'C');
		$this->Cell($width2+20, $height, $sum, 1, 0, 'R', false, '', 0, false, 'T', 'C');
   
    }

}
?>