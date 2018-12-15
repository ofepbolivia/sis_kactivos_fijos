
<?php

class RActiDepaPFunFPDF extends ReportePDF {
    var $dataMaster;
    var $datos_detalle;
    var $ancho_hoja;        
    var $oficina;
    var $tipo;

    function getDataSource(){    	
        return  $this->datos_detalle;
    }

    function setOficina($val){
        $this->oficina = $val;
        if($val==''||$val=='%'){
            $this->oficina = 'todos';
        }
    }

    function setTipo($val){
        $this->tipo = $val;
    }

    function  setColumna($val){
        $this->columna = $val;
    }

    function setDatos ($data) {

        $this->ancho_hoja = $this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-10;
        $this->dataMaster = $data[0];
        $this->datos_detalle = $data;
        //var_dump($this->dataMaster);exit;
        $this->SetMargins(2, 45, 3);
    }

    function Header() {
        $height = 6;
        $midHeight = 9;
        $longHeight = 18;

        $x = $this->GetX();
        $y = $this->GetY();
        $this->SetXY($x, $y);
       
        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 17,5,35,16);		

        $this->SetFontSize(12);
        $this->SetFont('', 'B');
        $this->Cell(53, $midHeight, '', 'LRT', 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(168, $midHeight, 'ACTIVOS FIJOS POR DEPOSITO', 'LRT', 0, 'C', false, '', 0, false, 'T', 'C');		
		
        $x = $this->GetX();
        $y = $this->GetY();
        $this->Ln();
        $this->SetFontSize(10);
        $this->SetFont('', 'B');
        $this->Cell(53, $midHeight, '', 'LRB', 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(168, $midHeight, '', 'LRB', 0, 'C', false, '', 0, false, 'T', 'C');

        $this->SetFontSize(7);

        $width1 = 15;
        $width2 = 25;
        $this->SetXY($x, $y);

        $this->SetFont('', '');
        $this->Cell(54, $longHeight, '', 1, 0, 'C', false, '', 0, false, 'T', 'C');

        $this->SetXY($x, $y+3);
        $this->setCellPaddings(2);
        //$this->Cell($width1-4, $height, '', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('', 'B');
        $this->SetFontSize(6);
        //$this->Cell($width2+8, $height,'', "B", 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFontSize(7);
        $this->setCellPaddings(2);
        $this->Ln();
        $this->SetX($x);
        $this->SetFont('', '');
        $this->Cell($width1-4, $height, '', "", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('', 'B');        
        $this->Cell($width2+8, $height,'', "", 0, 'L', false, '', 0, false, 'T', 'C');
        $this->setCellPaddings(2);
        $this->Ln();
        $this->SetX($x);
        $this->SetFont('', '');        
        $this->Cell($width1-4, $height, '', "", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('', 'B');        
        $this->Cell($w = $width2, $h = $height, $txt = '', $border = "", $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $this->setCellPaddings(2);

        $this->fieldsHeader();
        $this->generarCabecera();		
    }

    public function fieldsHeader(){

        $this->SetFontSize(10);
        $this->Ln(-5);
            //Responsable
            $this->SetFont('', 'B');
            $this->Cell(35, $height,'RESPONSABLE:', "", 0, 'L', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '');
            $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster['encargado'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $this->SetFont('', 'B');

            //Lugar
            $this->SetFont('', 'B');
            $this->Cell(40, $height,'', "", 0, 'L', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '');
            $this->Cell($w = 100,$h = $hGlobal, $txt = '', $border = 0, $ln = 1, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

            //Cargo
            $this->SetFont('', 'B');
            $this->Cell(35, $height,"DEPOSITO", "", 0, 'L', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '');
            $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster['almacen'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

            //Depto
            $this->SetFont('', 'B');
            $this->Cell(40, $height,'', "", 0, 'L', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '');
            $this->Cell($w = 50,$h = $hGlobal, $txt = '', $border = 0, $ln = 1, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

            //Dirección
            $this->SetFont('', 'B');
            $this->Cell(135, $height,'', "", 0, 'L', false, '', 0, false, 'T', 'C');
            $this->Cell(25, $height,'', "", 0, 'L', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '');
            $this->MultiCell($w = 100, $h = $hGlobal, $txt ='', $border = 0, $align = 'L', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
            $this->Cell(135, $height,'', "", 0, 'L', false, '', 0, false, 'T', 'C');
     


        $this->Ln(11.5);
        $this->SetFont('', 'B');
        $this->SetFont('', '');
        $this->firstPage++;
    }


    function generarReporte() {    	    	           
        $this->AddPage();		
        $this->SetFont('', '',8);
		//$this->Ln(3.5);		
 		$i=1;
        foreach ($this->getDataSource() as $datarow) {
		    $this->tablewidthsHD = array(10, 28, 55, 75, 22, 35,50);
	        $this->tablealigns = array('C', 'C', 'L', 'L', 'C', 'C', 'L');
	        $this->tablenumbers = array(0, 0, 0, 0, 0, 0, 0);
	        $this->tableborders = array('RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB');
	        $this->tabletextcolor = array(); 
		        	                  
                    $RowArray = array(
                        's0' => $i,
                        's1' => $datarow['codigo'],
                        's2' => $datarow['denominacion'],
                        's3' => $datarow['descripcion'],
                        's4' => $datarow['cat_desc'],
                        's5' => date("d/m/Y",strtotime($this->$datarow['fecha_mov'])),
                        's6' => $datarow['ubicacion']                       
                    );    
					$i++;        	
            $this-> MultiRow($RowArray,false,1);            					
			 }                       

     }
    

    function generarCabecera(){
		$this->Ln(-15);        
        $this->SetFont('', 'B',9);
    
		$this->tablewidthsHD = array(10, 28, 55, 75, 22, 35,50);
		$this->tablealignsHD = array('C', 'C', 'C', 'C', 'C', 'C','C');
		$this->tablenumbersHD = array(0, 0, 0, 0, 0, 0, 0);
		$this->tablebordersHD = array('LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LTRB');
		$this->tabletextcolorHD = array();
            $RowArray = array(
                's0' => 'Nro',
                's1' => 'CODIGO',
                's2' => 'NOMBRE',
                's3' => 'DESCRIPCION',
                's4' => 'ESTADO'."\n".'FUNCIONAL',		                
                's5' => 'FECHA DE INGRESO'."\n".'DE DEPOSITO',
                's6' => 'UBICACION'
            );      		
	                
 		$this->MultiRowHeader($RowArray,false,1);
        $this->tablewidths = $this->tablewidthsHD;		
    }

    function revisarfinPagina(){
        $dimensions = $this->getPageDimensions();
        $hasBorder = false; //flag for fringe case

        $startY = $this->GetY();
        $this->getNumLines($row['cell1data'], 80);

        if (($startY + 4 * 3) + $dimensions['bm'] > ($dimensions['hk'])) {
            if($this->total!= 0){
                $this->AddPage();
            }
        }
    }
}
?>