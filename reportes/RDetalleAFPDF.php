<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');
class RDetalleAFPDF extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;

    function Header() {
        /*$this->Ln(3);

        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib'.$_SESSION['_DIR_LOGO'], 16,5,40,20);
        $this->ln(5);
        $this->SetMargins(2, 40, 2);

        $this->SetFont('','B',10);
        $this->Cell(0,5,"DEPARTAMENTO ACTIVOS FIJOS",0,1,'C');
        $this->Cell(0,5,"DETALLE DE ACTIVOS FIJOS",0,1,'C');
        $this->Cell(0,5,'Del: '.$this->objParam->getParametro('fecha_ini').' Al '.$this->objParam->getParametro('fecha_fin').' Estado: '.$this->objParam->getParametro('estado'),0,1,'C');
        */
        //fRnk: se añadió la cabera del reporte
        $this->SetMargins(2, 40, 2);
        $fecha_ini = $this->objParam->getParametro('fecha_ini');
        $fecha_fin = $this->objParam->getParametro('fecha_fin');
        $fini = explode("/", $fecha_ini);
        $ffin = explode("/", $fecha_fin);
        $gini = count($fini) > 2 ? intval($fini[2]) : '';
        $gfin = count($ffin) > 2 ? intval($ffin[2]) : '';
        $gestion = $gini != $gfin ? $gini . ' - ' . $gfin : $gini;
        $content = '<table border="1" cellpadding="1" style="font-size: 10px;">
            <tr>
                <td style="width: 23%; color: #444444;" rowspan="5">
                    &nbsp;<img  style="width: 150px;" src="./../../../lib/' . $_SESSION['_DIR_LOGO'] . '" alt="Logo">
                </td>		
                <td style="width: 54%; color: #444444;text-align: center" rowspan="5">
                   <h4 style="font-size: 12px">DEPARTAMENTO ACTIVOS FIJOS</h4>
                   <b style="font-size: 10px">DETALLE DE ACTIVOS FIJOS</b><br>
                   <b style="font-size: 10px">Del: '. $fecha_ini.' Al '.$fecha_fin.'</b>
                   
                </td>
                <td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Gestión:</b> ' . $gestion . '</td>
            </tr>
            <tr>
                <td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Fecha:</b> ' . date('d/m/y h:i:s A') . '</td>
            </tr>
            <tr>
                <td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Depto.:</b> </td>
            </tr>
            <tr>
                <td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Usuario:</b> ' . $_SESSION['_LOGIN'] . '</td>
            </tr>
            <tr>
                <td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Estado:</b> '.$this->objParam->getParametro('estado').'</td>
            </tr>
        </table>';
        $this->writeHTMLCell(0, 10, 2, 4, $content, 0, 0, 0, true, 'L', true);
        $this->Ln(24);

		$this->SetFont('','',6);	
        $place = count($this->datos2); //estacion
        $factu = $this->objParam->getParametro('nr_factura'); //factura
        $depto = $this->objParam->getParametro('id_depto'); //departamento        
        $nr_tr = $this->objParam->getParametro('tramite_compra'); //nro tramite de compra
        $serie = $this->objParam->getParametro('nro_serie'); //nro de serie
        $c31   = $this->objParam->getParametro('nro_cbte_asociado'); //c31
        
        $this->SetFontSize(6);

        if ($place!=0 || $factu!='' || $depto!='' || $nr_tr!='' || $serie!='' || $c31!='') {
         $this->fieldsHeader();
			$this->Ln(2.1); //si	     
        }else{
        	$this->Ln(6);
        }
        $control = $this->objParam->getParametro('activo_multi');		
		$this->columnsGrid($control);

    }
    public function fieldsHeader(){
        $place = $this->datos2[0]['nombre']; //estacion
        $factu = $this->objParam->getParametro('nr_factura'); //factura
        $depto = $this->objParam->getParametro('id_depto'); //departamento        
        $nr_tr = $this->objParam->getParametro('tramite_compra'); //nro tramite de compra
        $serie = $this->objParam->getParametro('nro_serie'); //nro de serie
        $c31   = $this->objParam->getParametro('nro_cbte_asociado'); //c31
        $retVal = '';
        if ($depto=='' || $depto==3) {
            $retVal;
        }else if($depto==7){
        	$retVal='Unidad Activos Fijos';
        }else{
        	$retVal='Unidad Activos Fijos TI';
        }        
		        
		$cell1 = ($place!='')?'<td><b>ESTACION:</b>'.$place.'</td>':'<td></td>';
		$cell2 = ($retVal!='')?'<td><b>DEPTO.:</b>'.$retVal.'</td>':'<td></td>';
        $cell3 = ($factu!='')?'<td><b>FACTURA:</b> '.$factu.'</td>':'<td></td>';
        $cell4 = ($nr_tr!='')?'<td><b>Nº TRA. COMP.:</b> '.$nr_tr.'</td>':'<td></td>';
        $cell5 = ($serie!='')?'<td><b>Nro SERIE:</b> '.$serie.'</td>':'<td></td>';
        $cell6 = ($c31!='')?'<td><b>C31.:</b> '.$c31.'</td>':'<td></td>';   
		
        $this->SetFontSize(6);        
                $html = <<<EOF
            <style>    
            table {
                width: 100%;
                height: 100px;
            }
            </style>                
                <table>
                <tr>
                $cell1
                $cell2
                $cell3
                $cell4
                $cell5
                $cell6                                                          
                </tr>
EOF;

        $this->writeHTML ($html);
		if ($place!='' || $factu!='' || $depto!='' || $nr_tr!='' || $serie!='' || $c31!='') {		
        	$this->Ln(-5); //si
		}
        //$this->Ln(6); //no sin esto
    }
	//startBVP
    public function columnsGrid($tipo){
        $place = $this->datos2[0]['nombre']; //estacion
        $factu = $this->objParam->getParametro('nr_factura'); //factura
        $depto = $this->objParam->getParametro('id_depto'); //departamento        
        $nr_tr = $this->objParam->getParametro('tramite_compra'); //nro tramite de compra
        $serie = $this->objParam->getParametro('nro_serie'); //nro de serie
        $c31   = $this->objParam->getParametro('nro_cbte_asociado'); //c31
            	
		$hiddes = explode(',', $tipo);
		$ascod = '';
		$asdes = '';
		$asest = '';
		$asesf = '';
		$asfec = '';
		$asmon = '';
		$asimp = '';
		$asval = '';
		$asc31 = '';
		$asf31 = '';
		$asubi = '';
		$asres = '';
		$asuco = '';		
												
		for ($i=0; $i <count($hiddes) ; $i++) {
		switch ($hiddes[$i]) {
			case 'acod': $ascod = 'cod'; break;
			case 'ades': $asdes = 'des'; break;
			case 'aest': $asest = 'est'; break;
			case 'aesf': $asesf = 'esf'; break;
			case 'afec': $asfec = 'fec'; break;
			case 'amon': $asmon = 'mon'; break;
			case 'aimp': $asimp = 'imp'; break;
			case 'aval': $asval = 'val'; break;			
			case 'ac31': $asc31 = 'c31'; break;
			case 'af31': $asf31 = 'f31'; break;
			case 'aubi': $asubi = 'ubi'; break;
			case 'ares': $asres = 'res'; break;
			case 'auco': $asuco = 'uco'; break;														
			}									 			
		} 
		$tam1=23;
		$tam2=50;
		$tam3=15;
		$tam4=15;
		$tam5=15;
		$tam6=15;
		$tam7=15; 
		$tam8=18;		
		$tam9=15;
		$tam10=15;
		$tam11=24;
		$tam12=24;
		$tam13=21;

		if($ascod==''){
			$tam1=0;
		} 
		if($asdes==''){
			$tam2=0;
		} 
		if($asest==''){
			$tam3=0;
		} 
		if($asesf==''){
			$tam4=0;
		} 
		if($asfec==''){
			$tam5=0;
		} 
		if($asmon==''){
			$tam6=0;
		} 
		if($asimp==''){
			$tam7=0;
		} 
		if($asval==''){
			$tam8=0;
		} 
		if($asc31==''){
			$tam9=0;
		} 
		if($asf31==''){
			$tam10=0;
		} 
		if($asubi==''){
			$tam11=0;
		} 
		if($asres==''){
			$tam12=0;
		} 
		if($asuco==''){
			$tam13=0;
		}  
		//tomamos los tamanios de las columnas no mostradas y las distribuimos a las otras presentes 						 
		$xpage = 265;//∑ tam^n ai = an
		$cont = 0;			
		$resul = $tam1+$tam2+$tam3+$tam4+$tam5+$tam6+$tam7+$tam8+$tam9+$tam10+$tam11+$tam12+$tam13;
		$alca = $xpage - $resul;
		$n = count($hiddes);
		//distribucion de tamanios 
		if($alca>0){					 
			$total = $alca/$n;	 
			while ($resul<$xpage) {
				$cont += 0.001;
				$resul += 1;
			}
			$total += $cont;					 
		}else{				
		 	$total= 0;		 
		}
		
			$hGlobal=6;
			$wiTa = 188;		 		
			$descnom= $this->objParam->getParametro('desc_nombre');
			switch ($descnom) {
				case 'desc' :$desno='DESCRIPCIÓN';break;
				case 'nombre' :$desno='DENOMINACIÓN';break;
				default:$desno='NOMBRE / DESCRIPCIÓN';break;
			}					          		 		        
            $this->SetFontSize(6);
            $this->SetFont('', 'B');
			if ($place!='' || $factu!='' || $depto!='' || $nr_tr!='' || $serie!='' || $c31!='') {		
				$this->Ln(1);//si
			}												
			$this->MultiCell(10, $hGlobal,'NUM',1,'C',false,0,'','',true,0,false,true,0,'T',false);									
			($ascod=='cod')?$this->MultiCell($tam1+$total, $hGlobal, 'CODIGO',1,'C',false,0,'','',true,0,false,true,0,'T',false):'';			
			($asdes=='des')?$this->MultiCell($tam2+$total, $hGlobal, $desno, 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
			($asest=='est')?$this->MultiCell($tam3+$total, $hGlobal, 'ESTADO', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';			
			($asesf=='esf')?$this->MultiCell($tam4+$total, $hGlobal, 'ESTADO'."\x0A".'FUNCIONAL', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
			($asfec=='fec')?$this->MultiCell($tam5+$total, $hGlobal, 'FECHA COMPRA', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
			($asmon=='mon')?$this->MultiCell($tam6+$total, $hGlobal, 'MONTO'."\x0A".'(87%)', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
			($asimp=='imp')?$this->MultiCell($tam7+$total, $hGlobal, 'IMPORTE (100%)', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
			($asval=='val')?$this->MultiCell($tam8+$total, $hGlobal, 'VALOR ACTUAL', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
			($asc31=='c31')?$this->MultiCell($tam9+$total, $hGlobal, 'C31', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
			($asf31=='f31')?$this->MultiCell($tam10+$total,$hGlobal, 'FECHA'."\x0A".'COMP 31', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
			($asubi=='ubi')?$this->MultiCell($tam11+$total,$hGlobal, 'UBICACION', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
			($asres=='res')?$this->MultiCell($tam12+$total,$hGlobal, 'RESPONSABLE', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
			($asuco=='uco')?$this->MultiCell($tam13+$total,$hGlobal, 'UNIDAD SOLICITANTE',1,'C',false,0,'','',true,0,false,true,0,'T',false):'';						
    }

    function setDatos($datos,$datos2,$datos3) {

        $this->datos = $datos;
		$this->datos2 = $datos2;
		$this->datos3 = $datos3;
        //var_dump( $this->datos);exit;
    }			
    function  generarReporte()
    {		
        $this->AddPage();
        $this->SetMargins(2, 40, 2);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);		
        $this->Ln();

        //variables para la tabla
        $codigo = '';
        $nombre = '';
        $contador = 0;

        $cont_87 = 0;
        $cont_100 = 0;
        $cont_actual = 0;

        $total_general_87 = 0;
        $total_general_100 = 0;
        $total_general_actual = 0;

        $total_grupo_87 = 0;
        $total_grupo_100 = 0;
        $total_grupo_actual = 0;
		
        $i=1;
        //$this->tablewidths=array(10,23,50,15,15,15,15,15,15,15,15,30,30);
        $this->tablealigns=array('C','L','L','C','C','C','R','R','R','C','L','L','L','L');
        $tipo = $this->objParam->getParametro('tipo_reporte');
		$control = $this->objParam->getParametro('activo_multi');
			
		$hiddes = explode(',', $control);
		$ascod = '';
		$asdes = '';
		$asest = '';
		$asesf = '';
		$asfec = '';
		$asmon = '';
		$asimp = '';
		$asval = '';
		$asc31 = '';
		$asf31 = '';
		$asubi = '';
		$asres = '';
		$asuco = '';
											
		for ($a=0; $a <count($hiddes) ; $a++) {
		switch ($hiddes[$a]) {
			case 'acod': $ascod = 'cod'; break;
			case 'ades': $asdes = 'des'; break;
			case 'aest': $asest = 'est'; break;
			case 'aesf': $asesf = 'esf'; break;
			case 'afec': $asfec = 'fec'; break;
			case 'amon': $asmon = 'mon'; break;
			case 'aimp': $asimp = 'imp'; break;
			case 'aval': $asval = 'val'; break;
			case 'ac31': $asc31 = 'c31'; break;
			case 'af31': $asf31 = 'f31'; break;
			case 'aubi': $asubi = 'ubi'; break;
			case 'ares': $asres = 'res'; break;
			case 'auco': $asuco = 'uco'; break;								
			}									 			
		}

		$tam1=23;
		$tam2=50;
		$tam3=15;
		$tam4=15;
		$tam5=15;
		$tam6=15;
		$tam7=15; 
		$tam8=18;		
		$tam9=15;
		$tam10=15;
		$tam11=24;
		$tam12=24;
		$tam13=21;

		if($ascod==''){
			$tam1=0;
		} 
		if($asdes==''){
			$tam2=0;
		} 
		if($asest==''){
			$tam3=0;
		} 
		if($asesf==''){
			$tam4=0;
		} 
		if($asfec==''){
			$tam5=0;
		} 
		if($asmon==''){
			$tam6=0;
		} 
		if($asimp==''){
			$tam7=0;
		} 
		if($asval==''){
			$tam8=0;
		} 
		if($asc31==''){
			$tam9=0;
		} 
		if($asf31==''){
			$tam10=0;
		} 
		if($asubi==''){
			$tam11=0;
		} 
		if($asres==''){
			$tam12=0;
		} 
		if($asuco==''){
			$tam13=0;
		}  
								 
		$xpage = 265;//∑ tam^n ai = an
		$cont = 0;			
		$resul = $tam1+$tam2+$tam3+$tam4+$tam5+$tam6+$tam7+$tam8+$tam9+$tam10+$tam11+$tam12+$tam13;
		$alca = $xpage - $resul;
		$n = count($hiddes);
		
		if($alca>0){					 
			$total = $alca/$n;	 
			while ($resul<$xpage) {
				$cont += 0.001;
				$resul += 1;
			}
			$total += $cont;					 
		}else{				
		 	$total= 0;		 
		}				

			//arreglo para tablewidths estatica 	
	$tablewis=array('t1'=>10,'cod'=>$tam1+$total,'des'=>$tam2+$total,'est'=>$tam3+$total,'esf'=>$tam4+$total,'fec'=>$tam5+$total,'mon'=>$tam6+$total,'imp'=>$tam7+$total,'val'=>$tam8+$total,'c31'=>$tam9+$total,'f31'=>$tam10+$total,'ubi'=>$tam11+$total,'res'=>$tam12+$total,'uco'=>$tam13+$total);
	$tablenums0=array('t1'=>0,'cod'=>0,'des'=>0,'est'=>0,'esf'=>0,'fec'=>0,'mon'=>2,'imp'=>2,'val'=>2,'c31'=>0,'f31'=>0,'ubi'=>0,'res'=>0,'uco'=>0);  //1
	$tablenums1=array('t1'=>0,'cod'=>0,'des'=>0,'est'=>0,'esf'=>0,'fec'=>0,'mon'=>0,'imp'=>0,'val'=>0,'c31'=>0,'f31'=>0,'ubi'=>0,'res'=>0,'uco'=>0);  //2
	$tablenums2=array('t1'=>0,'cod'=>0,'des'=>0,'est'=>0,'esf'=>0,'fec'=>0,'mon'=>2,'imp'=>2,'val'=>2,'c31'=>0,'f31'=>0,'ubi'=>0,'res'=>0,'uco'=>0);  //3
	$tablenums3=array('t1'=>0,'cod'=>0,'des'=>0,'est'=>0,'esf'=>0,'fec'=>0,'mon'=>2,'imp'=>2,'val'=>2,'c31'=>0,'f31'=>0,'ubi'=>0,'res'=>0,'uco'=>0);  //4
	$tablenums4=array('t1'=>0,'cod'=>0,'des'=>0,'est'=>0,'esf'=>0,'fec'=>0,'mon'=>2,'imp'=>2,'val'=>2,'c31'=>0,'f31'=>0,'ubi'=>0,'res'=>0,'uco'=>0);  //5
	
	$tablewisReal = $this->filterArray($tablewis);
	$tablenums0Real = $this->filterArray($tablenums0);
	$tablenums1Real = $this->filterArray($tablenums1);
	$tablenums2Real = $this->filterArray($tablenums2);
	$tablenums3Real = $this->filterArray($tablenums3);
	$tablenums4Real = $this->filterArray($tablenums4);

	
		$this->tablewidths=$tablewisReal;			
      foreach($this->datos as $record){

            if($record['nivel'] == 0 || $record['nivel'] == 1){
                $this->SetFont('','B',6);
                if($codigo != '' && ($record['nivel'] == 0 || $record['nivel'] == 1) && $cont_87>0){

                    $total_general_87 = $total_general_87 + $cont_87;
                    $total_general_100 = $total_general_100 + $cont_100;
                    $total_general_actual = $total_general_actual + $cont_actual;
                if ($tipo == 1) {
                	
                
                    $this->SetFillColor(224, 235, 255);

                    $this->SetTextColor(0);
                    $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','B','B','B','RB');
                    $this->tablenumbers=$tablenums0Real;
                    $RowArray = array(
                        's0'  => '',
                        's1' => '',
                        's2' => 'Total Parcial Grupo',
                        's3' => '',
                        's4' => '',
                        's5' => '',
                        's6' => $cont_87,
                        's7' => $cont_100,
                        's8' => $cont_actual,
                        's9' => '',
                        's10' => '',
                        's11' => '',
                        's12' => '',
                        's13' => ''
                    );
						if ($ascod==''){							
							unset($RowArray['s1']);
						}if ($asdes==''){
							unset($RowArray['s2']);
						}if ($asest=='') {
							unset($RowArray['s3']);
						}if ($asesf=='') {
							unset($RowArray['s4']);
						}if ($asfec=='') {
							unset($RowArray['s5']);
						}if ($asmon=='') {
							unset($RowArray['s6']);
						}if ($asimp=='') {							
							unset($RowArray['s7']);
						}if ($asval=='') {
							unset($RowArray['s8']);
						}if ($asc31=='') {
							unset($RowArray['s9']);
						}if ($asf31==''){
							unset($RowArray['s10']);
						}if ($asubi==''){
							unset($RowArray['s11']);
						}if ($asres=='') {
							unset($RowArray['s12']);
						}if ($asuco==''){
							unset($RowArray['s13']);
						}								
                    $this->MultiRow($RowArray,true,1);
                }
                    $total_grupo_100 += $cont_100;
                    $total_grupo_87 += $cont_87;
                    $total_grupo_actual += $cont_actual;
                    $cont_100 = 0;
                    $cont_87 = 0;
                    $cont_actual = 0;
                    if($record['nivel'] == 0 && $codigo != $record['codigo_completo']){
                    	if ($tipo == 1) {
                    		
                    	
                        $RowArray = array(
                            's0'  => '',
                            's1' => '',
                            's2' => 'Total Final Grupo ('.$codigo.')',
                            's3' => '',
                            's4' => '',
                            's5' => '',
                            's6' => $total_grupo_87,
                            's7' => $total_grupo_100,
                            's8' => $total_grupo_actual,
                            's9' => '',
                            's10' => '',
                            's11' => '',
                            's12' => '',
                            's13' => ''
                        );
						if ($ascod==''){
							unset($RowArray['s1']);
						}if ($asdes==''){
							unset($RowArray['s2']);
						}if ($asest=='') {
							unset($RowArray['s3']);
						}if ($asesf=='') {
							unset($RowArray['s4']);
						}if ($asfec=='') {
							unset($RowArray['s5']);
						}if ($asmon=='') {
							unset($RowArray['s6']);
						}if ($asimp=='') {
							unset($RowArray['s7']);
						}if ($asval=='') {
							unset($RowArray['s8']);
						}if ($asc31=='') {
							unset($RowArray['s9']);
						}if ($asf31==''){
							unset($RowArray['s10']);
						}if ($asubi==''){
							unset($RowArray['s11']);
						}if ($asres=='') {
							unset($RowArray['s12']);
						}if ($asuco==''){
							unset($RowArray['s13']);
						}												
                        $this->MultiRow($RowArray,true,1);
                    }else{
                    $this->SetFillColor(224, 235, 255);

                    $this->SetTextColor(0);
                    $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','B','B','B','RB');
                    $this->tablenumbers=$tablenums0Real;
                $RowArray = array(
                    's0'  => '',
                    's1' => $codigo,
                    's2' => $nombre,
                    's3' => '',
                    's4' => '',
                    's5' => '',
                    's6' => $total_grupo_87,
                    's7' => $total_grupo_100,
                    's8' => $total_grupo_actual,
                    's9' => '',
                    's10' => '',
                    's11' => '',
                    's12' => '',
                    's13' => ''
                );
					if ($ascod==''){						
						unset($RowArray['s1']);
					}if ($asdes==''){
						unset($RowArray['s2']);
					}if ($asest=='') {
						unset($RowArray['s3']);
					}if ($asesf=='') {
						unset($RowArray['s4']);
					}if ($asfec=='') {
						unset($RowArray['s5']);
					}if ($asmon=='') {
						unset($RowArray['s6']);
					}if ($asimp=='') {
						unset($RowArray['s7']);
					}if ($asval=='') {
						unset($RowArray['s8']);
					}if ($asc31=='') {
						unset($RowArray['s9']);
					}if ($asf31==''){
						unset($RowArray['s10']);
					}if ($asubi==''){
						unset($RowArray['s11']);
					}if ($asres=='') {
						unset($RowArray['s12']);
					}if ($asuco==''){
						unset($RowArray['s13']);
					}	
                $this->MultiRow($RowArray,true,1);                    


                    }
                        $total_grupo_100 = 0;
                        $total_grupo_87 = 0;
                        $total_grupo_actual = 0;
                    }
                }

                if ($tipo == 1) {
                	
                
                $this->SetFillColor(79, 91, 147);

                $this->SetTextColor(0);
                $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','B','B','B','RB');
                $this->tablenumbers=$tablenums1Real;
                $RowArray = array(
                    's0'  => '',
                    's1' => $record['codigo_completo'],
                    's2' => $record['nombre'],
                    's3' => '',
                    's4' => '',
                    's5' => '',
                    's6' => '',
                    's7' => '',
                    's8' => '',
                    's9' => '',
                    's10' => '',
                    's11' => '',
                    's12' => '',
                    's13' => ''
                );
					if ($ascod==''){						
						unset($RowArray['s1']);
					}if ($asdes==''){
						unset($RowArray['s2']);
					}if ($asest=='') {
						unset($RowArray['s3']);
					}if ($asesf=='') {
						unset($RowArray['s4']);
					}if ($asfec=='') {
						unset($RowArray['s5']);
					}if ($asmon=='') {
						unset($RowArray['s6']);
					}if ($asimp=='') {
						unset($RowArray['s7']);
					}if ($asval=='') {
						unset($RowArray['s8']);
					}if ($asc31=='') {
						unset($RowArray['s9']);
					}if ($asf31==''){
						unset($RowArray['s10']);
					}if ($asubi==''){
						unset($RowArray['s11']);
					}if ($asres=='') {
						unset($RowArray['s12']);
					}if ($asuco==''){
						unset($RowArray['s13']);
					}	
                $this->MultiRow($RowArray,true,1);
            }
                if($record['nivel'] == 0){
                    $codigo = $record['codigo_completo'];
                    $nombre = $record['nombre'];
                }
            }else{
            if ($tipo == 1) {
            	
            
                $this->SetFont('','',6);
                $this->tableborders=array('RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB');
                $this->tablenumbers=$tablenums2Real;
                $RowArray = array(
                    's0'  => $record['nivel']==2?$i:'',
                    's1' => $record['nivel']==2?$record['codigo_af']:$record['camino'],
                    's2' => $record['nivel']==2?$record['denominacion']:$record['nombre'],
                    's3' => $record['estado'],
                    's4' => $record['estado_fun'],
                    's5' => $record['fecha_compra'] == '-'?'-':date("d/m/Y",strtotime($record['fecha_compra'])),
                    's6' => $record['monto_compra_orig'],
                    's7' => $record['monto_compra_orig_100'] ,
                    's8' => $record['monto_compra'],
                    's9' => $record['nro_cbte_asociado'],
                    's10' => $record['fecha_cbte_asociado'] == '-'?'-':date("d/m/Y",strtotime($record['fecha_cbte_asociado'])),
                    's11' => $record['ubicacion'],
                    's12' => $record['responsable'],
                    's13' => $record['nombre_unidad'] == '-'?'-':$record['nombre_unidad']
                );
			if ($ascod==''){
				unset($RowArray['s1']);
			}if ($asdes==''){
				unset($RowArray['s2']);
			}if ($asest=='') {
				unset($RowArray['s3']);
			}if ($asesf=='') {
				unset($RowArray['s4']);
			}if ($asfec=='') {
				unset($RowArray['s5']);
			}if ($asmon=='') {
				unset($RowArray['s6']);
			}if ($asimp=='') {
				unset($RowArray['s7']);
			}if ($asval=='') {
				unset($RowArray['s8']);
			}if ($asc31=='') {
				unset($RowArray['s9']);
			}if ($asf31==''){
				unset($RowArray['s10']);
			}if ($asubi==''){
				unset($RowArray['s11']);
			}if ($asres=='') {
				unset($RowArray['s12']);
			}if ($asuco==''){
				unset($RowArray['s13']);
			}				
                $this->MultiRow($RowArray);

                $i++;
            }
                $cont_100 += $record['monto_compra_orig_100'];
                $cont_87  += $record['monto_compra_orig'];
                $cont_actual  +=  $record['monto_compra'];
                //$codigo = $record['codigo_completo'];
            }

        }

        $total_general_87 += $cont_87;
        $total_general_100 += $cont_100;
        $total_general_actual += $cont_actual;

        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','B','B','B','RB');
        $this->tablenumbers=$tablenums3Real;
        if ($tipo == 1) {
        	
        
        $RowArray = array(
            's0'  => '',
            's1' => '',
            's2' => 'Total Parcial Grupo',
            's3' => '',
            's4' => '',
            's5' => '',
            's6' => $cont_87,
            's7' => $cont_100,
            's8' => $cont_actual,
            's9' => '',
            's10' => '',
            's11' => '',
            's12' => '',
            's13' => ''
        );
			if ($ascod==''){
				unset($RowArray['s1']);
			}if ($asdes==''){
				unset($RowArray['s2']);
			}if ($asest=='') {
				unset($RowArray['s3']);
			}if ($asesf=='') {
				unset($RowArray['s4']);
			}if ($asfec=='') {
				unset($RowArray['s5']);
			}if ($asmon=='') {
				unset($RowArray['s6']);
			}if ($asimp=='') {
				unset($RowArray['s7']);
			}if ($asval=='') {
				unset($RowArray['s8']);
			}if ($asc31=='') {
				unset($RowArray['s9']);
			}if ($asf31==''){
				unset($RowArray['s10']);
			}if ($asubi==''){
				unset($RowArray['s11']);
			}if ($asres=='') {
				unset($RowArray['s12']);
			}if ($asuco==''){
				unset($RowArray['s13']);
			}					
        $this->MultiRow($RowArray,true,1);

        //Final Grupo
        $RowArray = array(
            's0'  => '',
            's1' => '',
            's2' => 'Total Final Grupo ('.$codigo.')',
            's3' => '',
            's4' => '',
            's5' => '',
            's6' => $total_grupo_87+$cont_87,
            's7' => $total_grupo_100+$cont_100,
            's8' => $total_grupo_actual+$cont_actual,
            's9' => '',
            's10' => '',
            's11' => '',
            's12' => '',
            's13' => ''
        );
			if ($ascod==''){
				unset($RowArray['s1']);
			}if ($asdes==''){
				unset($RowArray['s2']);
			}if ($asest=='') {
				unset($RowArray['s3']);
			}if ($asesf=='') {
				unset($RowArray['s4']);
			}if ($asfec=='') {
				unset($RowArray['s5']);
			}if ($asmon=='') {
				unset($RowArray['s6']);
			}if ($asimp=='') {
				unset($RowArray['s7']);
			}if ($asval=='') {
				unset($RowArray['s8']);
			}if ($asc31=='') {
				unset($RowArray['s9']);
			}if ($asf31==''){
				unset($RowArray['s10']);
			}if ($asubi==''){
				unset($RowArray['s11']);
			}if ($asres=='') {
				unset($RowArray['s12']);
			}if ($asuco==''){
				unset($RowArray['s13']);
			}			
        $this->MultiRow($RowArray,true,1);
    }else{
                    $this->SetFillColor(224, 235, 255);

                    $this->SetTextColor(0);
                    $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','B','B','B','RB');
                    $this->tablenumbers=$tablenums0Real;
                $RowArray = array(
                    's0'  => '',
                    's1' => $codigo,
                    's2' => $nombre,
                    's3' => '',
                    's4' => '',
                    's5' => '',
		            's6' => $total_grupo_87+$cont_87,
		            's7' => $total_grupo_100+$cont_100,
		            's8' => $total_grupo_actual+$cont_actual,
                    's9' => '',
                    's10' => '',
                    's11' => '',
                    's12' => '',
                    's13' => ''
                );
					if ($ascod==''){						
						unset($RowArray['s1']);
					}if ($asdes==''){
						unset($RowArray['s2']);
					}if ($asest=='') {
						unset($RowArray['s3']);
					}if ($asesf=='') {
						unset($RowArray['s4']);
					}if ($asfec=='') {
						unset($RowArray['s5']);
					}if ($asmon=='') {
						unset($RowArray['s6']);
					}if ($asimp=='') {
						unset($RowArray['s7']);
					}if ($asval=='') {
						unset($RowArray['s8']);
					}if ($asc31=='') {
						unset($RowArray['s9']);
					}if ($asf31==''){
						unset($RowArray['s10']);
					}if ($asubi==''){
						unset($RowArray['s11']);
					}if ($asres=='') {
						unset($RowArray['s12']);
					}if ($asuco==''){
						unset($RowArray['s13']);
					}	
                $this->MultiRow($RowArray,true,1);        	
    }

        //$this->SetFillColor(79, 91, 147);
        $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','B','B','B','RB');
        $this->tablenumbers=$tablenums4Real;
        $RowArray = array(
            's0'  => '',
            's1' => '',
            's2' => 'TOTALES AF',
            's3' => '',
            's4' => '',
            's5' => '',
            's6' => $total_general_87,
            's7' => $total_general_100,
            's8' => $total_general_actual,
            's9' => '',
            's10' => '',
            's11' => '',
            's12' => '',
            's13' => ''
        );
			if ($ascod==''){
				unset($RowArray['s1']);
			}if ($asdes==''){
				unset($RowArray['s2']);
			}if ($asest=='') {
				unset($RowArray['s3']);
			}if ($asesf=='') {
				unset($RowArray['s4']);
			}if ($asfec=='') {
				unset($RowArray['s5']);
			}if ($asmon=='') {
				unset($RowArray['s6']);
			}if ($asimp=='') {
				unset($RowArray['s7']);
			}if ($asval=='') {
				unset($RowArray['s8']);
			}if ($asc31=='') {
				unset($RowArray['s9']);
			}if ($asf31==''){
				unset($RowArray['s10']);
			}if ($asubi==''){
				unset($RowArray['s11']);
			}if ($asres=='') {
				unset($RowArray['s12']);
			}if($asuco==''){
				unset($RowArray['s13']);
			}			
        $this->MultiRow($RowArray,true,1);


    }
function filterArray($table){

$resp = array();
		$control = $this->objParam->getParametro('activo_multi');
			
		$hiddes = explode(',', $control);
		$ascod = '';
		$asdes = '';
		$asest = '';
		$asesf = '';
		$asfec = '';
		$asmon = '';
		$asimp = '';
		$asval = '';
		$asc31 = '';
		$asf31 = '';
		$asubi = '';
		$asres = '';
		$asuco = '';
											
		for ($i=0; $i <count($hiddes) ; $i++) {
		switch ($hiddes[$i]) {
			case 'acod': $ascod = 'cod'; break;
			case 'ades': $asdes = 'des'; break;
			case 'aest': $asest = 'est'; break;
			case 'aesf': $asesf = 'esf'; break;
			case 'afec': $asfec = 'fec'; break;
			case 'amon': $asmon = 'mon'; break;
			case 'aimp': $asimp = 'imp'; break;
			case 'aval': $asval = 'val'; break;			
			case 'ac31': $asc31 = 'c31'; break;
			case 'af31': $asf31 = 'f31'; break;
			case 'aubi': $asubi = 'ubi'; break;
			case 'ares': $asres = 'res'; break;
			case 'auco': $asuco = 'uco'; break;								
			}									 			
		}
$proces = $table;

		foreach ($proces as $key => $value) {	
		    if($ascod==''){
		        unset($proces['cod']);      
		    }   
		    if($asdes==''){            
		        unset($proces['des']);
		    }   
		    if($asest==''){
		        unset($proces['est']);
		    }   
		    if($asesf==''){
		        unset($proces['esf']);
		    }   
		    if($asfec==''){
		        unset($proces['fec']);
		    }    
		    if($asmon==''){
		        unset($proces['mon']);  
		    }   
		    if($asimp==''){
		        unset($proces['imp']);
		    }   
		    if($asval==''){
		        unset($proces['val']);
		    }   
		    if($asc31==''){
		        unset($proces['c31']);
		    }   
		    if($asf31==''){
		        unset($proces['f31']);
		    }   
		    if($asubi==''){
		        unset($proces['ubi']);
		    }   
		    if($asres==''){
		        unset($proces['res']);
		    }   
		    if($asuco==''){
		        unset($proces['uco']);
		    }	
		}
	$resp=array();
	foreach ($proces as $value) {
		array_push($resp,$value);
		}
	return  $resp;
	}//endBVP
}
?>