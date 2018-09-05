<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');
class RCompraGestionPDF extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;

    function Header() {
        $this->Ln(3);

        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 16,5,40,20);
        $this->ln(5);
        $this->SetMargins(10, 40, 10);

        $this->SetFont('','B',10);
        $this->Cell(0,5,"DEPARTAMENTO ACTIVOS FIJOS",0,1,'C');
        $this->Cell(0,5,"COMPRAS DE GESTIÓN",0,1,'C');
        $this->Cell(0,5,'Del: '.$this->objParam->getParametro('fecha_ini').' Al '.$this->objParam->getParametro('fecha_fin').' Estado: '.$this->objParam->getParametro('estado'),0,1,'C');

        $this->SetFont('','B',6);
        $this->Ln(6);		
        //primera linea
      /*  $this->Cell(10,3,'NUM','TRL',0,'C');
        $this->Cell(18,3,'CODIGO','TRL',0,'C');
        //var_dump($this->objParam->getParametro('desc_nombre'));exit;
        if($this->objParam->getParametro('desc_nombre') == 'desc'){
            $this->Cell(57,3,'DESCRIPCIÓN','TRL',0,'C');
        }else{
            $this->Cell(57,3,'DENOMINACIÓN','TRL',0,'C');
        }

        $this->Cell(13,3,'FECHA','TRL',0,'C');
        $this->Cell(13,3,'NUM','TRL',0,'C');
        $this->Cell(13,3,'FECHA','TRL',0,'C');
        $this->Cell(15,3,'FECHA INI','TRL',0,'C');
        $this->Cell(14,3,'VIDA UTIL','TRL',0,'C');
        $this->Cell(14,3,'VIDA UTIL','TRL',0,'C');
        $this->Cell(17,3,'IMPORTE','TRL',0,'C');
        $this->Cell(17,3,'MONTO','TRL',1,'C');

        //segunda linea
        $this->Cell(10,3,'','BRL',0,'C');
        $this->Cell(18,3,'','BRL',0,'C');
        $this->Cell(57,3,'','BRL',0,'C');
        $this->Cell(13,3,'COMPRA','BRL',0,'C');
        $this->Cell(13,3,'COMP.','BRL',0,'C');
        $this->Cell(13,3,'COMP C31','BRL',0,'C');
        $this->Cell(15,3,'DEPRE.','BRL',0,'C');
        $this->Cell(14,3,'ORIGINAL','BRL',0,'C');
        $this->Cell(14,3,'RESTANTE','BRL',0,'C');
        $this->Cell(17,3,'100%','BRL',0,'C');
        $this->Cell(17,3,'87%','BRL',0,'C');*/        				

        $control = $this->objParam->getParametro('gestion_multi');		
		$this->columnsGrid($control);		
    }	
    public function columnsGrid($tipo){
		$hiddes = explode(',', $tipo);
		$gscod = '';
		$gsdes = '';				
		$gsfec = '';		
		$gsmun = '';
		$gsf31 = '';
		$gsfei = '';
		$gsvit = '';
		$gsviu = '';
		$gsimp = '';
		$gsgmon = '';		
												
		for ($i=0; $i <count($hiddes) ; $i++) {
		switch ($hiddes[$i]) {
			case 'gcod': $gscod = 'cod'; break;
			case 'gdes': $gsdes = 'des'; break;
			case 'gfec': $gsfec = 'fec'; break;			
			case 'gnum': $gsmun = 'mun'; break;
			case 'gf31': $gsf31 = 'f31'; break;
			case 'gfei': $gsfei = 'fei'; break;
			case 'gvit': $gsvit = 'vit'; break;
			case 'gviu': $gsviu = 'viu'; break;			
			case 'gimp': $gsimp = 'imp'; break;
			case 'gmon': $gsgmon = 'mon'; break;														
			}									 			
		}   
				    
			$hGlobal=6;		 		
			$this->objParam->getParametro('desc_nombre')=='desc'?$desno='DESCRIPCIÓN':$desno='DENOMINACIÓN';          		 		        
            $this->SetFontSize(6);
            $this->SetFont('', 'B');						
			$this->MultiCell(round((19/188)*100,2), $hGlobal,'NUM',1,'C',false,0,'','',true,0,false,true,0,'T',false);									
			($gscod=='cod')?'':$this->MultiCell(round((34/188)*100,2), $hGlobal, 'CODIGO',1,'C',false,0,'','',true,0,false,true,0,'T',false);			
			($gsdes=='des')?'':$this->MultiCell(round((107/188)*100,2), $hGlobal, $desno, 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($gsfec=='fec')?'':$this->MultiCell(round((25/188)*100,2), $hGlobal, 'FECHA COMPRA', 1,'C',false,0,'','',true,0,false,true,0,'T',false);			
			($gsmun=='mun')?'':$this->MultiCell(round((24/188)*100,2), $hGlobal, 'NUM'."\x0A".'COMP', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($gsf31=='f31')?'':$this->MultiCell(round((24/188)*100,2), $hGlobal, 'FECHA COMP 31', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($gsfei=='fei')?'':$this->MultiCell(round((28/188)*100,2), $hGlobal, 'FECHA INI DEPRE', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($gsvit=='vit')?'':$this->MultiCell(round((27/188)*100,2), $hGlobal, 'VIDA UTIL ORIGINAL', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($gsviu=='viu')?'':$this->MultiCell(round((26/188)*100,2), $hGlobal, 'VIDA UTIL RESTANTE', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($gsimp=='imp')?'':$this->MultiCell(round((32/188)*100,2), $hGlobal, 'IMPORTE'."\x0A".'100%', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($gsgmon=='mon')?'':$this->MultiCell(round((31.6/188)*100,2),$hGlobal, 'MONTO'."\x0A".'87%', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			
        $this->posY = $this->GetY();
    }
  /*
	function celHeader($wi, $he, $tx, $bo, $al, $fi, $ln, $fx, $fy, $re, $st, $ish, $au, $fl, $ca, $fit){		
	  	$this->MultiCell($wi, $he, $tx, $bo, $al, $fi, $ln, $fx, $fy, $re, $st, $ish, $au, $fl, $ca, $fit);		
	}	 */

    function setDatos($datos) {

        $this->datos = $datos;
        //var_dump( $this->datos);exit;
    }

    function  generarReporte()
    {

        $this->AddPage();
        $this->SetMargins(10, 40, 10);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->Ln();
        //variables para la tabla
        $codigo = '';
        $nombre = '';

        $cont_87 = 0;
        $cont_100 = 0;

        $total_general_87 = 0;
        $total_general_100 = 0;

        $total_grupo_87 = 0;
        $total_grupo_100 = 0;

        $i=1;
        $contador = 1; 
        $tipo = $this->objParam->getParametro('tipo_reporte');
		$select = $this->objParam->getParametro('gestion_multi');
		$hiddes = explode(',', $select);
		$gscod = '';
		$gsdes = '';				
		$gsfec = '';		
		$gsmun = '';
		$gsf31 = '';
		$gsfei = '';
		$gsvit = '';
		$gsviu = '';
		$gsimp = '';
		$gsgmon = '';		
		//asigna a cada variable su valor recibido desde la vista										
		for ($j=0; $j <count($hiddes) ; $j++) {
		switch ($hiddes[$j]) {
			case 'gcod': $gscod = 'cod'; break;
			case 'gdes': $gsdes = 'des'; break;
			case 'gfec': $gsfec = 'fec'; break;			
			case 'gnum': $gsmun = 'mun'; break;
			case 'gf31': $gsf31 = 'f31'; break;
			case 'gfei': $gsfei = 'fei'; break;
			case 'gvit': $gsvit = 'vit'; break;
			case 'gviu': $gsviu = 'viu'; break;			
			case 'gimp': $gsimp = 'imp'; break;
			case 'gmon': $gsgmon = 'mon'; break;														
			}									 			
		}
		//arreglo para tablewidths estatica 	
		$datos = array('t1'=>10,
					   'cod'=>18,
					   'des'=>57,
					   'fec'=>13,
					   'mun'=>13,
					   'f31'=>13,
					   'fei'=>15,
					   'vit'=>14,
					   'viu'=>14,
					   'imp'=>17,
					   'mon'=>17);
		//modificacion del array $datos, elimina los campos seleccinados de la vista para ser dinamico
		foreach ($datos as $key => $value) {	
			switch ($key) {
				case $gscod:
					unset($datos['cod']);																			
					break;
				case $gsdes:			
					unset($datos['des']);
					break;
				case $gsfec:
					unset($datos['fec']);
					break;
				case $gsmun:
					unset($datos['mun']);
					break;
				case $gsf31:
					unset($datos['f31']);
					break;	
				case $gsfei:
					unset($datos['fei']);	
					break;
				case $gsvit:
					unset($datos['vit']);
					break;
				case $gsviu:
					unset($datos['viu']);
					break;
				case $gsimp:
					unset($datos['imp']);
					break;
				case $gsgmon:
					unset($datos['mon']);
					break;				
			}	
		}
		//volvemos el arreglo de arreglo aun array simple que si recibe tablewidths
		$real=array();
		foreach ($datos as $value) {
			array_push($real,$value);
		}
		$this->tablewidths=$real;
 	
        $this->tablealigns=array('C','L','L','C','C','C','C','C','C','R','R');					
       foreach($this->datos as $record){
            
            if($record['nivel'] == 0 || $record['nivel'] == 1){
                $this->SetFont('','B',6);
                if($codigo != '' && ($record['nivel'] == 0 || $record['nivel'] == 1) && $cont_87>0){

                    $total_general_87 = $total_general_87 + $cont_87;
                    $total_general_100 = $total_general_100 + $cont_100;
                    if($tipo == 1) {
                        $this->SetFillColor(224, 235, 255);
                        $this->SetTextColor(0);
                        $this->tableborders = array('LB', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'RB');
                        $this->tablenumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 2);
                        $RowArray = array(
                            's0' => '',
                            's1' => '',
                            's2' => 'Total Parcial Grupo',
                            's3' => '',
                            's4' => '',
                            's5' => '',
                            's6' => '',
                            's7' => '',
                            's8' => '',
                            's9' => $cont_100,
                            's10' => $cont_87
                        );
						if ($gscod=='cod'){
							unset($RowArray['s1']);
						}if ($gsdes=='des'){
							unset($RowArray['s2']);
						}if ($gsfec=='fec') {
							unset($RowArray['s3']);
						}if ($gsmun=='mun') {
							unset($RowArray['s4']);
						}if ($gsf31=='f31') {
							unset($RowArray['s5']);
						}if ($gsfei=='fei') {
							unset($RowArray['s6']);
						}if ($gsvit=='vit') {
							unset($RowArray['s7']);
						}if ($gsviu=='viu'){
							unset($RowArray['s8']);
						}if ($gsimp=='imp'){
							unset($RowArray['s9']);
						}if ($gsgmon=='mon') {
							unset($RowArray['s10']);
						}																										
                        $this->MultiRow($RowArray, true, 1);
                    }
                    $total_grupo_100 += $cont_100;
                    $total_grupo_87 += $cont_87;
                    $cont_100 = 0;
                    $cont_87 = 0;
                    if($record['nivel'] == 0 && $codigo != $record['codigo_completo']){
                        if($tipo == 1) {
                            $RowArray = array(
                                's0' => '',
                                's1' => '',
                                's2' => 'Total Final Grupo (' . $codigo . ')',
                                's3' => '',
                                's4' => '',
                                's5' => '',
                                's6' => '',
                                's7' => '',
                                's8' => '',
                                's9' => $total_grupo_100,
                                's10' => $total_grupo_87
                            );
						if ($gscod=='cod'){
							unset($RowArray['s1']);
						}if ($gsdes=='des'){
							unset($RowArray['s2']);
						}if ($gsfec=='fec') {
							unset($RowArray['s3']);
						}if ($gsmun=='mun') {
							unset($RowArray['s4']);
						}if ($gsf31=='f31') {
							unset($RowArray['s5']);
						}if ($gsfei=='fei') {
							unset($RowArray['s6']);
						}if ($gsvit=='vit') {
							unset($RowArray['s7']);
						}if ($gsviu=='viu'){
							unset($RowArray['s8']);
						}if ($gsimp=='imp'){
							unset($RowArray['s9']);
						}if ($gsgmon=='mon') {
							unset($RowArray['s10']);
						}		
                            $this->MultiRow($RowArray, true, 1);
                        }else{
                            $this->SetFillColor(224, 235, 255);
                            $this->SetTextColor(0);
                            $this->tableborders = array('LB', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'RB');
                            $this->tablenumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 2);
                            $RowArray = array(
                                's0' => '',
                                's1' => $codigo,
                                's2' => $nombre,
                                's3' => '',
                                's4' => '',
                                's5' => '',
                                's6' => '',
                                's7' => '',
                                's8' => '',
                                's9' => $total_grupo_100,
                                's10' => $total_grupo_87
                            );
						if ($gscod=='cod'){
							unset($RowArray['s1']);
						}if ($gsdes=='des'){
							unset($RowArray['s2']);
						}if ($gsfec=='fec') {
							unset($RowArray['s3']);
						}if ($gsmun=='mun') {
							unset($RowArray['s4']);
						}if ($gsf31=='f31') {
							unset($RowArray['s5']);
						}if ($gsfei=='fei') {
							unset($RowArray['s6']);
						}if ($gsvit=='vit') {
							unset($RowArray['s7']);
						}if ($gsviu=='viu'){
							unset($RowArray['s8']);
						}if ($gsimp=='imp'){
							unset($RowArray['s9']);
						}if ($gsgmon=='mon') {
							unset($RowArray['s10']);
						}							
                            $this->MultiRow($RowArray, true, 1);
                            //$contador++;
                        }
                        $total_grupo_100 = 0;
                        $total_grupo_87 = 0;
                    }
                }

                if($tipo == 1) {
                    $this->SetFillColor(79, 91, 147);
                    $this->SetTextColor(0);
                    $this->tableborders = array('LB', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'RB');
                    $this->tablenumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
                    $RowArray = array(
                        's0' => '',
                        's1' => $record['codigo_completo'],
                        's2' => $record['nombre'],
                        's3' => '',
                        's4' => '',
                        's5' => '',
                        's6' => '',
                        's7' => '',
                        's8' => '',
                        's9' => '',
                        's10' => ''
                    );
						if ($gscod=='cod'){
							unset($RowArray['s1']);
						}if ($gsdes=='des'){
							unset($RowArray['s2']);
						}if ($gsfec=='fec') {
							unset($RowArray['s3']);
						}if ($gsmun=='mun') {
							unset($RowArray['s4']);
						}if ($gsf31=='f31') {
							unset($RowArray['s5']);
						}if ($gsfei=='fei') {
							unset($RowArray['s6']);
						}if ($gsvit=='vit') {
							unset($RowArray['s7']);
						}if ($gsviu=='viu'){
							unset($RowArray['s8']);
						}if ($gsimp=='imp'){
							unset($RowArray['s9']);
						}if ($gsgmon=='mon') {
							unset($RowArray['s10']);
						}							
                    $this->MultiRow($RowArray, true, 1);
                }
                if($record['nivel'] == 0){
                    $codigo = $record['codigo_completo'];
                    $nombre = $record['nombre'];
                }
            }else{
                if($tipo == 1) {
                    $this->SetFont('', '', 6);
                    $this->tableborders = array('RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB');
                    $this->tablenumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 2);
                    $RowArray = array(
                        's0' => $record['nivel'] == 2 ? $i : '',
                        's1' => $record['nivel'] == 2 ? $record['codigo_af'] : $record['camino'],
                        's2' => $record['nivel'] == 2 ? $record['denominacion'] : $record['nombre'],
                        's3' => $record['fecha_compra'] == '-' ? '-' : date("d/m/Y", strtotime($record['fecha_compra'])),
                        's4' => $record['nro_cbte_asociado'],
                        's5' => $record['fecha_cbte_asociado'] == '-' ? '-' : date("d/m/Y", strtotime($record['fecha_cbte_asociado'])),
                        's6' => $record['fecha_ini_dep'] == '-' ? '-' : date("d/m/Y", strtotime($record['fecha_ini_dep'])),
                        's7' => $record['vida_util_original'],
                        's8' => '-',
                        's9' => $record['monto_compra_orig_100'],
                        's10' => $record['monto_compra_orig']
                    );
						if ($gscod=='cod'){
							unset($RowArray['s1']);
						}if ($gsdes=='des'){
							unset($RowArray['s2']);
						}if ($gsfec=='fec') {
							unset($RowArray['s3']);
						}if ($gsmun=='mun') {
							unset($RowArray['s4']);
						}if ($gsf31=='f31') {
							unset($RowArray['s5']);
						}if ($gsfei=='fei') {
							unset($RowArray['s6']);
						}if ($gsvit=='vit') {
							unset($RowArray['s7']);
						}if ($gsviu=='viu'){
							unset($RowArray['s8']);
						}if ($gsimp=='imp'){
							unset($RowArray['s9']);
						}if ($gsgmon=='mon') {
							unset($RowArray['s10']);
						}																	
                    $this->MultiRow($RowArray);
                    $i++;
                }
                $cont_100 = $cont_100 + $record['monto_compra_orig_100'];
                $cont_87  = $cont_87+ $record['monto_compra_orig'];
            }
        }

        $total_general_87 += $cont_87;
        $total_general_100 += $cont_100;

        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','RB');
        $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,2,2);
        if($tipo == 1) {
            $RowArray = array(
                's0' => '',
                's1' => '',
                's2' => 'Total Parcial Grupo',
                's3' => '',
                's4' => '',
                's5' => '',
                's6' => '',
                's7' => '',
                's8' => '',
                's9' => $cont_100,
                's10' => $cont_87
            );
						if ($gscod=='cod'){
							unset($RowArray['s1']);
						}if ($gsdes=='des'){
							unset($RowArray['s2']);
						}if ($gsfec=='fec') {
							unset($RowArray['s3']);
						}if ($gsmun=='mun') {
							unset($RowArray['s4']);
						}if ($gsf31=='f31') {
							unset($RowArray['s5']);
						}if ($gsfei=='fei') {
							unset($RowArray['s6']);
						}if ($gsvit=='vit') {
							unset($RowArray['s7']);
						}if ($gsviu=='viu'){
							unset($RowArray['s8']);
						}if ($gsimp=='imp'){
							unset($RowArray['s9']);
						}if ($gsgmon=='mon') {
							unset($RowArray['s10']);
						}					
            $this->MultiRow($RowArray, true, 1);

            //Final Grupo
            $RowArray = array(
                's0' => '',
                's1' => '',
                's2' => 'Total Final Grupo (' . $codigo . ')',
                's3' => '',
                's4' => '',
                's5' => '',
                's6' => '',
                's7' => '',
                's8' => '',
                's9' => $total_grupo_100 + $cont_100,
                's10' => $total_grupo_87 + $cont_87
            );
						if ($gscod=='cod'){
							unset($RowArray['s1']);
						}if ($gsdes=='des'){
							unset($RowArray['s2']);
						}if ($gsfec=='fec') {
							unset($RowArray['s3']);
						}if ($gsmun=='mun') {
							unset($RowArray['s4']);
						}if ($gsf31=='f31') {
							unset($RowArray['s5']);
						}if ($gsfei=='fei') {
							unset($RowArray['s6']);
						}if ($gsvit=='vit') {
							unset($RowArray['s7']);
						}if ($gsviu=='viu'){
							unset($RowArray['s8']);
						}if ($gsimp=='imp'){
							unset($RowArray['s9']);
						}if ($gsgmon=='mon') {
							unset($RowArray['s10']);
						}											
            $this->MultiRow($RowArray, true, 1);
        }else{
            $this->SetFillColor(224, 235, 255);
            $this->SetTextColor(0);
            $RowArray = array(
                's0' => '',
                's1' => $codigo,
                's2' => $nombre,
                's3' => '',
                's4' => '',
                's5' => '',
                's6' => '',
                's7' => '',
                's8' => '',
                's9' => $total_grupo_100 + $cont_100,
                's10' => $total_grupo_87 + $cont_87
            );
						if ($gscod=='cod'){
							unset($RowArray['s1']);
						}if ($gsdes=='des'){
							unset($RowArray['s2']);
						}if ($gsfec=='fec') {
							unset($RowArray['s3']);
						}if ($gsmun=='mun') {
							unset($RowArray['s4']);
						}if ($gsf31=='f31') {
							unset($RowArray['s5']);
						}if ($gsfei=='fei') {
							unset($RowArray['s6']);
						}if ($gsvit=='vit') {
							unset($RowArray['s7']);
						}if ($gsviu=='viu'){
							unset($RowArray['s8']);
						}if ($gsimp=='imp'){
							unset($RowArray['s9']);
						}if ($gsgmon=='mon') {
							unset($RowArray['s10']);
						}			
            $this->MultiRow($RowArray, true, 1);
        }

        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','RB');
        $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,2,2);
        $RowArray = array(
            's0'  => '',
            's1' => '',
            's2' => 'TOTALES AF',
            's3' => '',
            's4' => '',
            's5' => '',
            's6' => '',
            's7' => '',
            's8' => '',
            's9' => $total_general_100,
            's10' => $total_general_87
        );
						if ($gscod=='cod'){
							unset($RowArray['s1']);
						}if ($gsdes=='des'){
							unset($RowArray['s2']);
						}if ($gsfec=='fec') {
							unset($RowArray['s3']);
						}if ($gsmun=='mun') {
							unset($RowArray['s4']);
						}if ($gsf31=='f31') {
							unset($RowArray['s5']);
						}if ($gsfei=='fei') {
							unset($RowArray['s6']);
						}if ($gsvit=='vit') {
							unset($RowArray['s7']);
						}if ($gsviu=='viu'){
							unset($RowArray['s8']);
						}if ($gsimp=='imp'){
							unset($RowArray['s9']);
						}if ($gsgmon=='mon') {
							unset($RowArray['s10']);
						}	
        $this->MultiRow($RowArray,true,1);


    }
}
?>