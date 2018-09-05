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
        $this->Ln(3);

        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 16,5,40,20);
        $this->ln(5);
        $this->SetMargins(10, 40, 10);

        $this->SetFont('','B',10);
        $this->Cell(0,5,"DEPARTAMENTO ACTIVOS FIJOS",0,1,'C');
        $this->Cell(0,5,"DETALLE DE ACTIVOS FIJOS",0,1,'C');
        $this->Cell(0,5,'Del: '.$this->objParam->getParametro('fecha_ini').' Al '.$this->objParam->getParametro('fecha_fin').' Estado: '.$this->objParam->getParametro('estado'),0,1,'C');

        $this->SetFont('','B',6);
        $this->Ln(6);
        //primera linea
      /*$this->Cell(10,3,'NUM','TRL',0,'C');
        $this->Cell(23,3,'CODIGO','TRL',0,'C');
        
        if($this->objParam->getParametro('desc_nombre') == 'desc'){
            $this->Cell(50,3,'DESCRIPCIÓN','TRL',0,'C');
        }else{
            $this->Cell(50,3,'DENOMINACIÓN','TRL',0,'C');
        }

		
        $this->Cell(15,3,'ESTADO','TRL',0,'C');
        $this->Cell(15,3,'ESTADO ','TRL',0,'C');
        $this->Cell(15,3,'FECHA','TRL',0,'C');
        $this->Cell(15,3,'MONTO','TRL',0,'C');
        $this->Cell(15,3,'IMPORTE','TRL',0,'C');
        $this->Cell(15,3,'VALOR','TRL',0,'C');
        $this->Cell(15,3,'C31','TRL',0,'C');
        $this->Cell(15,3,'FECHA','TRL',0,'C');
        $this->Cell(30,3,'UBICACIÓN','TRL',0,'C');
        $this->Cell(30,3,'RESPONSABLE','TRL',1,'C');

        //segunda linea
        $this->Cell(10,3,'','BRL',0,'C');
        $this->Cell(23,3,'','BRL',0,'C');
        $this->Cell(50,3,'','BRL',0,'C');
        $this->Cell(15,3,'','BRL',0,'C');
        $this->Cell(15,3,'FUNCIONAL','BRL',0,'C');
        $this->Cell(15,3,'COMPRA','BRL',0,'C');
        $this->Cell(15,3,'(87%)','BRL',0,'C');
        $this->Cell(15,3,'(100%)','BRL',0,'C');
        $this->Cell(15,3,'ACTUAL','BRL',0,'C');
        $this->Cell(15,3,'','BRL',0,'C');
        $this->Cell(15,3,'COMP C31','BRL',0,'C');
        $this->Cell(30,3,'','BRL',0,'C');
        $this->Cell(30,3,'','BRL',0,'C');*/
        $control = $this->objParam->getParametro('activo_multi');		
		$this->columnsGrid($control);

    }
    public function columnsGrid($tipo){
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
			}									 			
		}   				    
			$hGlobal=6;
			$wiTa = 188;		 		
			$this->objParam->getParametro('desc_nombre')=='desc'?$desno='DESCRIPCIÓN':$desno='DENOMINACIÓN';          		 		        
            $this->SetFontSize(6);
            $this->SetFont('', 'B');						
			$this->MultiCell(round((19/$wiTa)*100,2), $hGlobal,'NUM',1,'C',false,0,'','',true,0,false,true,0,'T',false);									
			($ascod=='cod')?'':$this->MultiCell(round((43/$wiTa)*100,2), $hGlobal, 'CODIGO',1,'C',false,0,'','',true,0,false,true,0,'T',false);			
			($asdes=='des')?'':$this->MultiCell(round((94/$wiTa)*100,2), $hGlobal, $desno, 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($asest=='est')?'':$this->MultiCell(round((28/$wiTa)*100,2), $hGlobal, 'ESTADO', 1,'C',false,0,'','',true,0,false,true,0,'T',false);			
			($asesf=='esf')?'':$this->MultiCell(round((28/$wiTa)*100,2), $hGlobal, 'ESTADO'."\x0A".'FUNCIONAL', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($asfec=='fec')?'':$this->MultiCell(round((29/$wiTa)*100,2), $hGlobal, 'FECHA COMPRA', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($asmon=='mon')?'':$this->MultiCell(round((28/$wiTa)*100,2), $hGlobal, 'MONTO'."\x0A".'(87%)', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($asimp=='imp')?'':$this->MultiCell(round((28/$wiTa)*100,2), $hGlobal, 'IMPORTE (100%)', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($asval=='val')?'':$this->MultiCell(round((28/$wiTa)*100,2), $hGlobal, 'VALOR ACTUAL', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($asc31=='c31')?'':$this->MultiCell(round((28/$wiTa)*100,2), $hGlobal, 'C31', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($asf31=='f31')?'':$this->MultiCell(round((28/$wiTa)*100,2),$hGlobal, 'FECHA'."\x0A".'COMP 31', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($asubi=='ubi')?'':$this->MultiCell(round((57/$wiTa)*100,2),$hGlobal, 'UBICACION', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			($asres=='res')?'':$this->MultiCell(round((56.4/$wiTa)*100,2),$hGlobal, 'RESPONSABLE', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
			
        $this->posY = $this->GetY();
		$this->posX = $this->GetX();
		
		
    }

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
        $this->tablealigns=array('C','L','L','C','C','C','R','R','R','C','L','L');
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
			}									 			
		}		

		//arreglo para tablewidths estatica 	
$tablewis=array('t1'=>10,'cod'=>23,'des'=>50,'est'=>15,'esf'=>15,'fec'=>15,'mon'=>15,'imp'=>15,'val'=>15,'c31'=>15,'f31'=>15,'ubi'=>30,'res'=>30);
$tablenums0=array('t1'=>0,'cod'=>0,'des'=>0,'est'=>0,'esf'=>0,'fec'=>0,'mon'=>2,'imp'=>2,'val'=>2,'c31'=>0,'f31'=>0,'ubi'=>0,'res'=>0);  //1
$tablenums1=array('t1'=>0,'cod'=>0,'des'=>0,'est'=>0,'esf'=>0,'fec'=>0,'mon'=>0,'imp'=>0,'val'=>0,'c31'=>0,'f31'=>0,'ubi'=>0,'res'=>0);  //2
$tablenums2=array('t1'=>0,'cod'=>0,'des'=>0,'est'=>0,'esf'=>0,'fec'=>0,'mon'=>2,'imp'=>2,'val'=>2,'c31'=>0,'f31'=>0,'ubi'=>0,'res'=>0);  //3
$tablenums3=array('t1'=>0,'cod'=>0,'des'=>0,'est'=>0,'esf'=>0,'fec'=>0,'mon'=>2,'imp'=>2,'val'=>2,'c31'=>0,'f31'=>0,'ubi'=>0,'res'=>0);  //4
$tablenums4=array('t1'=>0,'cod'=>0,'des'=>0,'est'=>0,'esf'=>0,'fec'=>0,'mon'=>2,'imp'=>2,'val'=>2,'c31'=>0,'f31'=>0,'ubi'=>0,'res'=>0);  //5

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
                    $this->SetFillColor(224, 235, 255);

                    $this->SetTextColor(0);
                    $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','B','B','RB');
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
                        's12' => ''
                    );
						if ($ascod=='cod'){							
							unset($RowArray['s1']);
						}if ($asdes=='des'){
							unset($RowArray['s2']);
						}if ($asest=='est') {
							unset($RowArray['s3']);
						}if ($asesf=='esf') {
							unset($RowArray['s4']);
						}if ($asfec=='fec') {
							unset($RowArray['s5']);
						}if ($asmon=='mon') {
							unset($RowArray['s6']);
						}if ($asimp=='imp') {							
							unset($RowArray['s7']);
						}if ($asval=='val') {
							unset($RowArray['s8']);
						}if ($asc31=='c31') {
							unset($RowArray['s9']);
						}if ($asf31=='f31'){
							unset($RowArray['s10']);
						}if ($asubi=='ubi'){
							unset($RowArray['s11']);
						}if ($asres=='res') {
							unset($RowArray['s12']);
						}								
                    $this->MultiRow($RowArray,true,1);
                    $total_grupo_100 += $cont_100;
                    $total_grupo_87 += $cont_87;
                    $total_grupo_actual += $cont_actual;
                    $cont_100 = 0;
                    $cont_87 = 0;
                    $cont_actual = 0;
                    if($record['nivel'] == 0 && $codigo != $record['codigo_completo']){
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
                            's12' => ''
                        );
						if ($ascod=='cod'){
							unset($RowArray['s1']);
						}if ($asdes=='des'){
							unset($RowArray['s2']);
						}if ($asest=='est') {
							unset($RowArray['s3']);
						}if ($asesf=='esf') {
							unset($RowArray['s4']);
						}if ($asfec=='fec') {
							unset($RowArray['s5']);
						}if ($asmon=='mon') {
							unset($RowArray['s6']);
						}if ($asimp=='imp') {
							unset($RowArray['s7']);
						}if ($asval=='val') {
							unset($RowArray['s8']);
						}if ($asc31=='c31') {
							unset($RowArray['s9']);
						}if ($asf31=='f31'){
							unset($RowArray['s10']);
						}if ($asubi=='ubi'){
							unset($RowArray['s11']);
						}if ($asres=='res') {
							unset($RowArray['s12']);
						}												
                        $this->MultiRow($RowArray,true,1);
                        $total_grupo_100 = 0;
                        $total_grupo_87 = 0;
                        $total_grupo_actual = 0;
                    }
                }

                $this->SetFillColor(79, 91, 147);

                $this->SetTextColor(0);
                $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','B','B','RB');
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
                    's12' => ''
                );
					if ($ascod=='cod'){						
						unset($RowArray['s1']);
					}if ($asdes=='des'){
						unset($RowArray['s2']);
					}if ($asest=='est') {
						unset($RowArray['s3']);
					}if ($asesf=='esf') {
						unset($RowArray['s4']);
					}if ($asfec=='fec') {
						unset($RowArray['s5']);
					}if ($asmon=='mon') {
						unset($RowArray['s6']);
					}if ($asimp=='imp') {
						unset($RowArray['s7']);
					}if ($asval=='val') {
						unset($RowArray['s8']);
					}if ($asc31=='c31') {
						unset($RowArray['s9']);
					}if ($asf31=='f31'){
						unset($RowArray['s10']);
					}if ($asubi=='ubi'){
						unset($RowArray['s11']);
					}if ($asres=='res') {
						unset($RowArray['s12']);
					}	
                $this->MultiRow($RowArray,true,1);
                if($record['nivel'] == 0){
                    $codigo = $record['codigo_completo'];
                }
            }else{

                $this->SetFont('','',6);
                $this->tableborders=array('RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB');
                $this->tablenumbers=$tablenums2Real;
                $RowArray = array(
                    's0'  => $record['nivel']==2?$i:'',
                    's1' => $record['nivel']==2?$record['codigo_af']:$record['camino'],
                    's2' => $record['nivel']==2?$record['denominacion']:$record['nombre'],
                    's3' => $record['estado'],
                    's4' => '-',
                    's5' => $record['fecha_compra'] == '-'?'-':date("d/m/Y",strtotime($record['fecha_compra'])),
                    's6' => $record['monto_compra_orig'],
                    's7' => $record['monto_compra_orig_100'] ,
                    's8' => $record['monto_compra'],
                    's9' => $record['nro_cbte_asociado'],
                    's10' => $record['fecha_cbte_asociado'] == '-'?'-':date("d/m/Y",strtotime($record['fecha_cbte_asociado'])),
                    's11' => $record['ubicacion'],
                    's12' => $record['responsable']
                );
			if ($ascod=='cod'){
				unset($RowArray['s1']);
			}if ($asdes=='des'){
				unset($RowArray['s2']);
			}if ($asest=='est') {
				unset($RowArray['s3']);
			}if ($asesf=='esf') {
				unset($RowArray['s4']);
			}if ($asfec=='fec') {
				unset($RowArray['s5']);
			}if ($asmon=='mon') {
				unset($RowArray['s6']);
			}if ($asimp=='imp') {
				unset($RowArray['s7']);
			}if ($asval=='val') {
				unset($RowArray['s8']);
			}if ($asc31=='c31') {
				unset($RowArray['s9']);
			}if ($asf31=='f31'){
				unset($RowArray['s10']);
			}if ($asubi=='ubi'){
				unset($RowArray['s11']);
			}if ($asres=='res') {
				unset($RowArray['s12']);
			}				
                $this->MultiRow($RowArray);

                $i++;
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
        $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','B','B','RB');
        $this->tablenumbers=$tablenums3Real;
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
            's12' => ''
        );
			if ($ascod=='cod'){
				unset($RowArray['s1']);
			}if ($asdes=='des'){
				unset($RowArray['s2']);
			}if ($asest=='est') {
				unset($RowArray['s3']);
			}if ($asesf=='esf') {
				unset($RowArray['s4']);
			}if ($asfec=='fec') {
				unset($RowArray['s5']);
			}if ($asmon=='mon') {
				unset($RowArray['s6']);
			}if ($asimp=='imp') {
				unset($RowArray['s7']);
			}if ($asval=='val') {
				unset($RowArray['s8']);
			}if ($asc31=='c31') {
				unset($RowArray['s9']);
			}if ($asf31=='f31'){
				unset($RowArray['s10']);
			}if ($asubi=='ubi'){
				unset($RowArray['s11']);
			}if ($asres=='res') {
				unset($RowArray['s12']);
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
            's12' => ''
        );
			if ($ascod=='cod'){
				unset($RowArray['s1']);
			}if ($asdes=='des'){
				unset($RowArray['s2']);
			}if ($asest=='est') {
				unset($RowArray['s3']);
			}if ($asesf=='esf') {
				unset($RowArray['s4']);
			}if ($asfec=='fec') {
				unset($RowArray['s5']);
			}if ($asmon=='mon') {
				unset($RowArray['s6']);
			}if ($asimp=='imp') {
				unset($RowArray['s7']);
			}if ($asval=='val') {
				unset($RowArray['s8']);
			}if ($asc31=='c31') {
				unset($RowArray['s9']);
			}if ($asf31=='f31'){
				unset($RowArray['s10']);
			}if ($asubi=='ubi'){
				unset($RowArray['s11']);
			}if ($asres=='res') {
				unset($RowArray['s12']);
			}			
        $this->MultiRow($RowArray,true,1);

        //$this->SetFillColor(79, 91, 147);
        $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','B','B','RB');
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
        );
			if ($ascod=='cod'){
				unset($RowArray['s1']);
			}if ($asdes=='des'){
				unset($RowArray['s2']);
			}if ($asest=='est') {
				unset($RowArray['s3']);
			}if ($asesf=='esf') {
				unset($RowArray['s4']);
			}if ($asfec=='fec') {
				unset($RowArray['s5']);
			}if ($asmon=='mon') {
				unset($RowArray['s6']);
			}if ($asimp=='imp') {
				unset($RowArray['s7']);
			}if ($asval=='val') {
				unset($RowArray['s8']);
			}if ($asc31=='c31') {
				unset($RowArray['s9']);
			}if ($asf31=='f31'){
				unset($RowArray['s10']);
			}if ($asubi=='ubi'){
				unset($RowArray['s11']);
			}if ($asres=='res') {
				unset($RowArray['s12']);
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
			}									 			
		}
$proces = $table;

		foreach ($proces as $key => $value) {	
			switch ($key) {
				case $ascod:
					unset($proces['cod']);																			
					break;
				case $asdes:			
					unset($proces['des']);
					break;
				case $asest:
					unset($proces['est']);
					break;
				case $asesf:
					unset($proces['esf']);
					break;
				case $asfec:
					unset($proces['fec']);
					break;	
				case $asmon:
					unset($proces['mon']);	
					break;
				case $asimp:
					unset($proces['imp']);
					break;
				case $asval:
					unset($proces['val']);
					break;
				case $asc31:
					unset($proces['c31']);
					break;
				case $asf31:
					unset($proces['f31']);
					break;
				case $asubi:
					unset($proces['ubi']);					
					break;
				case $asres:
					unset($proces['res']);
					break;										
			}	
		}
	$resp=array();
	foreach ($proces as $value) {
		array_push($resp,$value);
		}
	return  $resp;
	}
}
?>