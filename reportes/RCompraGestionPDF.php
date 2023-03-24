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
	var $sum=0;

    function Header() {
        //$this->Ln(3);

        //cabecera del reporte
       // $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 16,5,40,20);
        //$this->ln(5);
        $this->SetMargins(2, 40, 2);
        //$this->SetFont('','B',10);
        //$this->Cell(0,5,"DEPARTAMENTO ACTIVOS FIJOS",0,1,'C');
        //$this->Cell(0,5,"COMPRAS DE GESTIÓN",0,1,'C');
        //$this->Cell(0,5,'Del: '.$this->objParam->getParametro('fecha_ini').' Al '.$this->objParam->getParametro('fecha_fin').' Estado: '.$this->objParam->getParametro('estado'),0,1,'C');

		//fRnk: se añadió la cabera del reporte
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
                <td style="width: 52%; color: #444444;text-align: center" rowspan="5">
                   <h4 style="font-size: 12px">DEPARTAMENTO ACTIVOS FIJOS</h4>
                   <b style="font-size: 10px">COMPRAS DE GESTIÓN</b><br>
                   <b style="font-size: 10px">Del: '. $fecha_ini.' Al '.$fecha_fin.'</b>
                   
                </td>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Gestión:</b> ' . $gestion . '</td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Fecha:</b> ' . date('d/m/y h:i:s A') . '</td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Depto.:</b> </td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Usuario:</b> ' . $_SESSION['_LOGIN'] . '</td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Estado:</b> '.$this->objParam->getParametro('estado').'</td>
            </tr>
        </table>';
		//$this->writeHTML($content, false, false, true, false, '');
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
			
				
        $control = $this->objParam->getParametro('gestion_multi');
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
	//start BVP
    public function columnsGrid($tipo){
        $place = $this->datos2[0]['nombre']; //estacion
        $factu = $this->objParam->getParametro('nr_factura'); //factura
        $depto = $this->objParam->getParametro('id_depto'); //departamento        
        $nr_tr = $this->objParam->getParametro('tramite_compra'); //nro tramite de compra
        $serie = $this->objParam->getParametro('nro_serie'); //nro de serie
        $c31   = $this->objParam->getParametro('nro_cbte_asociado'); //c31
            	
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
		$gsgmon= '';
		$gsuco ='';

		//widths
		$tam1=18;
		$tam2=51;
		$tam3=13;
		$tam4=13;
		$tam5=13;
		$tam6=15;
		$tam7=14;
		$tam8=14;
		$tam9=17;
		$tam10=17;
		$tam11=17;

		$num = 0;
		$total = 0;

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
			case 'guco': $gsuco = 'uco'; break;
			}
		}

		if ($gscod=='') {
			$tam1 = 0;
		}if ($gsdes=='') {
			$tam2 = 0;
		}if ($gsfec=='') {
			$tam3 = 0;
		}if ($gsmun=='') {
			$tam4 = 0;
		}if ($gsf31=='') {
			$tam5 = 0;
		}if ($gsfei=='') {
			$tam6 = 0;
		}if ($gsvit=='') {
			$tam7 = 0;
		}if ($gsviu=='') {
			$tam8 = 0;
		}if ($gsimp=='') {
			$tam9 = 0;
		}if ($gsgmon=='') {
			$tam10 = 0;
		}if ($gsuco=='') {
			$tam11 = 0;
		}
		//tomamos los tamanios de las columnas no mostradas y las distribuimos a las otras presentes
		$xpage = 202;//∑ tam^n ai = an
		$cont = 0;
		$resul = $tam1+$tam2+$tam3+$tam4+$tam5+$tam6+$tam7+$tam8+$tam9+$tam10+$tam11;
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
		//$this->Ln(6); no si nesto
		$this->MultiCell(10, $hGlobal,'NUM',1,'C',false,0,'','',true,0,false,true,0,'T',false);
		($gscod=='cod')?$this->MultiCell($tam1+$total, $hGlobal, 'CODIGO',1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
		($gsdes=='des')?$this->MultiCell($tam2+$total, $hGlobal, $desno, 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
		($gsfec=='fec')?$this->MultiCell($tam3+$total, $hGlobal, 'FECHA COMPRA', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
		($gsmun=='mun')?$this->MultiCell($tam4+$total, $hGlobal, 'NUM'."\x0A".'COMP', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
		($gsf31=='f31')?$this->MultiCell($tam5+$total, $hGlobal, 'FECHA COMP 31', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
		($gsfei=='fei')?$this->MultiCell($tam6+$total, $hGlobal, 'FECHA INI DEPRE', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
		($gsvit=='vit')?$this->MultiCell($tam7+$total, $hGlobal, 'VIDA UTIL ORIGINAL', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
		($gsviu=='viu')?$this->MultiCell($tam8+$total, $hGlobal, 'VIDA UTIL RESTANTE', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
		($gsimp=='imp')?$this->MultiCell($tam9+$total, $hGlobal, 'IMPORTE'."\x0A".'100%', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
		($gsgmon=='mon')?$this->MultiCell($tam10+$total,$hGlobal, 'MONTO'."\x0A".'87%', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
		($gsuco=='uco')?$this->MultiCell($tam11+$total,$hGlobal, 'UNIDAD SOLICITANTE',1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
    }

    function setDatos($datos,$datos2,$datos3) {

        $this->datos = $datos;
		$this->datos2 = $datos2;
		$this->datos3 = $datos3;
        //var_dump($this->datos3);exit;
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
		$gsuco = '';

		$tam1=18;
		$tam2=51;
		$tam3=13;
		$tam4=13;
		$tam5=13;
		$tam6=15;
		$tam7=14;
		$tam8=14;
		$tam9=17;
		$tam10=17;
		$tam11=17;

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
			case 'guco': $gsuco = 'uco' ; break;
			}
		}
		if ($gscod=='') {
			$tam1 = 0;
		}if ($gsdes=='') {
			$tam2 = 0;
		}if ($gsfec=='') {
			$tam3 = 0;
		}if ($gsmun=='') {
			$tam4 = 0;
		}if ($gsf31=='') {
			$tam5 = 0;
		}if ($gsfei=='') {
			$tam6 = 0;
		}if ($gsvit=='') {
			$tam7 = 0;
		}if ($gsviu=='') {
			$tam8 = 0;
		}if ($gsimp=='') {
			$tam9 = 0;
		}if ($gsgmon=='') {
			$tam10 = 0;
		}if ($gsuco=='') {
			$tam11 = 0;
		}

		$xpage = 202;//∑ tam^n ai = an
		$cont = 0;
		$resul = $tam1+$tam2+$tam3+$tam4+$tam5+$tam6+$tam7+$tam8+$tam9+$tam10+$tam11;
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
		$datos = array('t1'=>10,
					   'cod'=>$tam1+$total,
					   'des'=>$tam2+$total,
					   'fec'=>$tam3+$total,
					   'mun'=>$tam4+$total,
					   'f31'=>$tam5+$total,
					   'fei'=>$tam6+$total,
					   'vit'=>$tam7+$total,
					   'viu'=>$tam8+$total,
					   'imp'=>$tam9+$total,
					   'mon'=>$tam10+$total,
					   'uco'=>$tam11+$total);

		$this->tablewidths=$this->filterArray($datos);
		$tablenums0=array('t1'=>0,'cod'=>0,'des'=>0,'fec'=>0,'mun'=>0,'f31'=>0,'fei'=>0,'vit'=>0,'viu'=>0,'imp'=>2,'mon'=>2,'uco'=>0);  //1
		$tablenums1=array('t1'=>0,'cod'=>0,'des'=>0,'fec'=>0,'mun'=>0,'f31'=>0,'fei'=>0,'vit'=>0,'viu'=>0,'imp'=>0,'mon'=>0,'uco'=>0);  //2		
		$tablenums0Real = $this->filterArray($tablenums0);
		$tablenums1Real = $this->filterArray($tablenums1);
        $this->tablealigns=array('C','L','L','C','C','C','C','C','C','R','R','R');
       foreach($this->datos as $record){

            if($record['nivel'] == 0 || $record['nivel'] == 1){
                $this->SetFont('','B',6);
                if($codigo != '' && ($record['nivel'] == 0 || $record['nivel'] == 1) && $cont_87>0){

                    $total_general_87 = $total_general_87 + $cont_87;
                    $total_general_100 = $total_general_100 + $cont_100;
                    if($tipo == 1) {
                        $this->SetFillColor(224, 235, 255);
                        $this->SetTextColor(0);
                        $this->tableborders = array('LB', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B','RB');
                        //$this->tablenumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 2,0);
                        $this->tablenumbers =$tablenums0Real;
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
                            's10' => $cont_87,
                            's11' => ''
                        );
						if ($gscod==''){
							unset($RowArray['s1']);
						}if ($gsdes==''){
							unset($RowArray['s2']);
						}if ($gsfec=='') {
							unset($RowArray['s3']);
						}if ($gsmun=='') {
							unset($RowArray['s4']);
						}if ($gsf31=='') {
							unset($RowArray['s5']);
						}if ($gsfei=='') {
							unset($RowArray['s6']);
						}if ($gsvit=='') {
							unset($RowArray['s7']);
						}if ($gsviu==''){
							unset($RowArray['s8']);
						}if ($gsimp==''){
							unset($RowArray['s9']);
						}if ($gsgmon=='') {
							unset($RowArray['s10']);
						}if ($gsuco==''){
							unset($RowArray['s11']);
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
                                's10' => $total_grupo_87,
                                's11' => ''
                            );
						if ($gscod==''){
							unset($RowArray['s1']);
						}if ($gsdes==''){
							unset($RowArray['s2']);
						}if ($gsfec=='') {
							unset($RowArray['s3']);
						}if ($gsmun=='') {
							unset($RowArray['s4']);
						}if ($gsf31=='') {
							unset($RowArray['s5']);
						}if ($gsfei=='') {
							unset($RowArray['s6']);
						}if ($gsvit=='') {
							unset($RowArray['s7']);
						}if ($gsviu==''){
							unset($RowArray['s8']);
						}if ($gsimp==''){
							unset($RowArray['s9']);
						}if ($gsgmon=='') {
							unset($RowArray['s10']);
						}if ($gsuco==''){
							unset($RowArray['s11']);
						}
                            $this->MultiRow($RowArray, true, 1);
                        }else{
                            $this->SetFillColor(224, 235, 255);
                            $this->SetTextColor(0);
                            $this->tableborders = array('LB', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B','RB');
                            //$this->tablenumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 2,0);
                            $this->tablenumbers =$tablenums0Real;
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
                                's10' => $total_grupo_87,
                                's11' => ''
                            );
						if ($gscod==''){
							unset($RowArray['s1']);
						}if ($gsdes==''){
							unset($RowArray['s2']);
						}if ($gsfec=='') {
							unset($RowArray['s3']);
						}if ($gsmun=='') {
							unset($RowArray['s4']);
						}if ($gsf31=='') {
							unset($RowArray['s5']);
						}if ($gsfei=='') {
							unset($RowArray['s6']);
						}if ($gsvit=='') {
							unset($RowArray['s7']);
						}if ($gsviu==''){
							unset($RowArray['s8']);
						}if ($gsimp==''){
							unset($RowArray['s9']);
						}if ($gsgmon=='') {
							unset($RowArray['s10']);
						}if ($gsuco==''){
							unset($RowArray['s11']);
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
                    $this->tableborders = array('LB', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B','RB');
                    //$this->tablenumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0);
					$this->tablenumbers =$tablenums1Real;
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
                        's10' => '',
                        's11' => ''
                    );
						if ($gscod==''){
							unset($RowArray['s1']);
						}if ($gsdes==''){
							unset($RowArray['s2']);
						}if ($gsfec=='') {
							unset($RowArray['s3']);
						}if ($gsmun=='') {
							unset($RowArray['s4']);
						}if ($gsf31=='') {
							unset($RowArray['s5']);
						}if ($gsfei=='') {
							unset($RowArray['s6']);
						}if ($gsvit=='') {
							unset($RowArray['s7']);
						}if ($gsviu==''){
							unset($RowArray['s8']);
						}if ($gsimp==''){
							unset($RowArray['s9']);
						}if ($gsgmon=='') {
							unset($RowArray['s10']);
						}if ($gsuco==''){
							unset($RowArray['s11']);
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
                    $this->tableborders = array('RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB','RLTB');
                    //$this->tablenumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 2,0);
                    $this->tablenumbers =$tablenums0Real;
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
                        's10' => $record['monto_compra_orig'],
                        's11' => $record['nombre_unidad']
                    );
						if ($gscod==''){
							unset($RowArray['s1']);
						}if ($gsdes==''){
							unset($RowArray['s2']);
						}if ($gsfec=='') {
							unset($RowArray['s3']);
						}if ($gsmun=='') {
							unset($RowArray['s4']);
						}if ($gsf31=='') {
							unset($RowArray['s5']);
						}if ($gsfei=='') {
							unset($RowArray['s6']);
						}if ($gsvit=='') {
							unset($RowArray['s7']);
						}if ($gsviu==''){
							unset($RowArray['s8']);
						}if ($gsimp==''){
							unset($RowArray['s9']);
						}if ($gsgmon=='') {
							unset($RowArray['s10']);
						}if ($gsuco==''){
							unset($RowArray['s11']);
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
        $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','B','RB');
        //$this->tablenumbers=array(0,0,0,0,0,0,0,0,0,2,2,0);
        $this->tablenumbers =$tablenums0Real;
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
                's10' => $cont_87,
                's11' => ''
            );
						if ($gscod==''){
							unset($RowArray['s1']);
						}if ($gsdes==''){
							unset($RowArray['s2']);
						}if ($gsfec=='') {
							unset($RowArray['s3']);
						}if ($gsmun=='') {
							unset($RowArray['s4']);
						}if ($gsf31=='') {
							unset($RowArray['s5']);
						}if ($gsfei=='') {
							unset($RowArray['s6']);
						}if ($gsvit=='') {
							unset($RowArray['s7']);
						}if ($gsviu==''){
							unset($RowArray['s8']);
						}if ($gsimp==''){
							unset($RowArray['s9']);
						}if ($gsgmon=='') {
							unset($RowArray['s10']);
						}if ($gsuco==''){
							unset($RowArray['s11']);
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
                's10' => $total_grupo_87 + $cont_87,
                's11' => ''
            );
						if ($gscod==''){
							unset($RowArray['s1']);
						}if ($gsdes==''){
							unset($RowArray['s2']);
						}if ($gsfec=='') {
							unset($RowArray['s3']);
						}if ($gsmun=='') {
							unset($RowArray['s4']);
						}if ($gsf31=='') {
							unset($RowArray['s5']);
						}if ($gsfei=='') {
							unset($RowArray['s6']);
						}if ($gsvit=='') {
							unset($RowArray['s7']);
						}if ($gsviu==''){
							unset($RowArray['s8']);
						}if ($gsimp==''){
							unset($RowArray['s9']);
						}if ($gsgmon=='') {
							unset($RowArray['s10']);
						}if ($gsuco==''){
							unset($RowArray['s11']);
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
                's10' => $total_grupo_87 + $cont_87,
                's11' => ''
            );
						if ($gscod==''){
							unset($RowArray['s1']);
						}if ($gsdes==''){
							unset($RowArray['s2']);
						}if ($gsfec=='') {
							unset($RowArray['s3']);
						}if ($gsmun=='') {
							unset($RowArray['s4']);
						}if ($gsf31=='') {
							unset($RowArray['s5']);
						}if ($gsfei=='') {
							unset($RowArray['s6']);
						}if ($gsvit=='') {
							unset($RowArray['s7']);
						}if ($gsviu==''){
							unset($RowArray['s8']);
						}if ($gsimp==''){
							unset($RowArray['s9']);
						}if ($gsgmon=='') {
							unset($RowArray['s10']);
						}if ($gsuco==''){
							unset($RowArray['s11']);
						}
            $this->MultiRow($RowArray, true, 1);
        }

        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','B','RB');
        //$this->tablenumbers=array(0,0,0,0,0,0,0,0,0,2,2,0);
        $this->tablenumbers =$tablenums0Real;
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
            's10' => $total_general_87,
            's11' => ''
        );
						if ($gscod==''){
							unset($RowArray['s1']);
						}if ($gsdes==''){
							unset($RowArray['s2']);
						}if ($gsfec=='') {
							unset($RowArray['s3']);
						}if ($gsmun=='') {
							unset($RowArray['s4']);
						}if ($gsf31=='') {
							unset($RowArray['s5']);
						}if ($gsfei=='') {
							unset($RowArray['s6']);
						}if ($gsvit=='') {
							unset($RowArray['s7']);
						}if ($gsviu==''){
							unset($RowArray['s8']);
						}if ($gsimp==''){
							unset($RowArray['s9']);
						}if ($gsgmon=='') {
							unset($RowArray['s10']);
						} if($gsuco==''){
							unset($RowArray['s11']);
						}
        $this->MultiRow($RowArray,true,1);


    }
function filterArray($table){

$resp = array();
		$control = $this->objParam->getParametro('gestion_multi');
		$hiddes = explode(',', $control);
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
		$gsuco = '';

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
			case 'guco': $gsuco = 'uco' ; break;
			}
		}

$proces = $table;

		foreach ($proces as $key => $value) {
		    if($gscod==''){
		        unset($proces['cod']);
		    }
		    if($gsdes==''){
		        unset($proces['des']);
		    }
		    if($gsfec==''){
		        unset($proces['fec']);
		    }
		    if($gsmun==''){
		        unset($proces['mun']);
		    }
		    if($gsf31==''){
		        unset($proces['f31']);
		    }
		    if($gsfei==''){
		        unset($proces['fei']);
		    }
		    if($gsvit==''){
		        unset($proces['vit']);
		    }
		    if($gsviu==''){
		        unset($proces['viu']);
		    }
		    if($gsimp==''){
		        unset($proces['imp']);
		    }
		    if($gsgmon==''){
		        unset($proces['mon']);
		    }
		    if($gsuco==''){
		        unset($proces['uco']);
		    }
		}
	$resp=array();
	foreach ($proces as $value) {
		array_push($resp,$value);
		}
	return  $resp;
	} //endBVP
}
?>
