<?php
class RActivoDetallePDF extends  ReportePDF{
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
        $this->Cell(150, $height, 'REPORTE DE ACTIVO EN DETALLE', 0, 0, 'C', false, '', 0, false, 'T', 'C');
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
    	
		
		
        $this->SetMargins(20,35,20);
        $this->setFontSubsetting(false);
        $this->AddPage();
        //$this->Ln(10);
        $this->SetFont('','B',9);

        $conf_det_tablewidths=array(10,15,23,38,33,15,15,15,16,18,10,20,28);
        $conf_det_tablealigns=array('C','C','C','L','C','C','C','C','C','C','C','L','L');

        $this->tablewidths=$conf_det_tablewidths;
        $this->tablealigns=$conf_det_tablealigns;


        $RowArray = array(

            'TIPO',
            'SUB-TIPO',
            'CODIGO',
            'DESCRIPCION',
            'CLASIFICACION',
            'MARCA',
            'SERIAL',
            'ESTADO',
            'ESTADO FUNCIONAL',
            'FECHA COMPRA',
            'C31',
            'UBICACION',
            'RESPONSABLE'
        );
        $this-> MultiRow($RowArray,false,1);
        $this->SetFont('','',8);
        $conf_det_tablewidths=array(10,15,23,38,33,15,15,15,16,18,10,20,28);
        $conf_det_tablealigns=array('C','C','L','L','L','C','C','C','C','C','C','L','L');
        $this->tablewidths=$conf_det_tablewidths;
        $this->tablealigns=$conf_det_tablealigns;

        $cont_filas = 1;
        //$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //$this->SetHeaderMargin(50);
        foreach ($this->datos as $Row) {

            $RowArray = array(
                'TIPO' => $Row['tipo'],
                'SUB-TIPO'=> $Row['subtipo'],
                'CODIGO' => $Row['codigo'],
                'DESCRIPCION' => $Row['descripcion'],
                'CLASIFICACION' => $Row['denominacion'],
                'MARCA' => $Row['marca'],
                'SERIAL' =>  $Row['nombre'],
                'ESTADO' =>  $Row['estado'],
                'ESTADO FUNCIONAL' =>  $Row['estado_funcional'],
                'FECHA COMPRA' =>  $Row['fecha_compra'],
                'C31' =>  $Row['c31'],
                'UBICACION' =>  $Row['ubicacion'],
                'RESPONSABLE' =>  $Row['responsable']             

            );

            $this-> MultiRow($RowArray);


        }
        
    
		
		
		
     // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        /*$this->AddPage();
        //$this->SetHeaderMargin(10);
        $this->SetMargins(15, 25, 15,true);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		$height = 20;

		$width1 = 15;
        $width2 = 10;
        $width3 = 25;
        $width4 = 23;
		$width5 = 30;
		$width6 = 20;
		$width7 = 35;
		$width8 = 50;
		
		

	   

   
        $this->SetFont('','B',8);
        //$this->Ln(6);
        //primera linea
        $this->Cell(10,3,'TIPO','TRL',0,'C');
        $this->Cell(15,3,'SUP-','TRL',0,'C');
        $this->Cell(20,3,'CODIGO','TRL',0,'C');
        $this->Cell(35,3,'DESCRIPCION ','TRL',0,'C');
        $this->Cell(25,3,'CLASIFICACION','TRL',0,'C');
        $this->Cell(15,3,'MARCA','TRL',0,'C');
        $this->Cell(15,3,'SERIAL','TRL',0,'C');
        $this->Cell(15,3,'ESTADO','TRL',0,'C');
		$this->Cell(20,3,'ESTADO','TRL',0,'C');
        $this->Cell(15,3,'FECHA','TRL',0,'C');
        $this->Cell(10,3,'C31','TRL',0,'C');
        $this->Cell(30,3,'UBICACIÓN','TRL',0,'C');
        $this->Cell(30,3,'RESPONSABLE','TRL',1,'C');

        //segunda linea
        $this->Cell(10,3,'','BRL',0,'C');
        $this->Cell(15,3,'TIPO','BRL',0,'C');
        $this->Cell(20,3,'','BRL',0,'C');
        $this->Cell(35,3,'','BRL',0,'C');		
        $this->Cell(25,3,'','BRL',0,'C');
		$this->Cell(15,3,'','BRL',0,'C');
        $this->Cell(15,3,'','BRL',0,'C');
		$this->Cell(15,3,'','BRL',0,'C');
        $this->Cell(20,3,'FUNCIONAL','BRL',0,'C');
        $this->Cell(15,3,'COMPRA','BRL',0,'C');
        $this->Cell(10,3,'','BRL',0,'C');
        $this->Cell(30,3,'','BRL',0,'C');
		$this->Cell(30,3,'','BRL',0,'C');
		$this->Ln(3.8);
	
		
	     for ($i=0; $i <count($this->datos) ; $i++) { 

	        $this->SetFont('', '',8);  									
	        $this->Cell($width2, $height, $this->datos[$i]["tipo"], 1, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->MultiCell($width1, $height, $this->datos[$i]["subtipo"], 1,'L', false,0, '','',true, 0, false,true, 'J',true);
	      
	        $this->SetFont('', '',7);
			$this->MultiCell($width6, $height, $this->datos[$i]["codigo"], 1,'L', false,0, '','',true, 0, false,true, 'J',true);
			
			$this->SetFont('','',6);
			$codAux = substr($this->datos[$i]["descripcion"],0,130);
			$this->MultiCell($width7, $height,$codAux, 1,'L', false,0, '','',true, 0, false,true,$width7, 'J',false);	
			
			$this->SetFont('', '',7);  
			$this->MultiCell($width3, $height, $this->datos[$i]["denominacion"], 1,'L', false,0, '','',true, 0, false,true, 'J',true);

			$this->SetFont('', '',7);
			$this->Cell($width1, $height, $this->datos[$i]["marca"], 1, 0, 'L', false, '', 1, false, 'T', 'C');
			$this->Cell($width1, $height, $this->datos[$i]["nombre"], 1, 0, 'L', false, '', 1, false, 'T', 'C');
			$this->Cell($width1, $height, $this->datos[$i]["estado"], 1, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width6, $height, $this->datos[$i]["estado_funcional"], 1, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width1, $height, $this->datos[$i]["fecha_compra"], 1, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width2, $height, $this->datos[$i]["c31"], 1, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->MultiCell($width5, $height, $this->datos[$i]["ubicacion"], 1,'L', false,0, '','',true, 0, false,true, 'J',true);
			
			$this->MultiCell($width5, $height, $this->datos[$i]["responsable"], 1,'L', false,0, '','',true, 0, false,true, 'J',true);

        	$this->Ln();
			 }*/
  
    }

}
?>