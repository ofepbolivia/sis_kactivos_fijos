<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';

class RKardexAFPDF extends  ReportePDF{
    var $datos ;   

    function Header() {
        //fRnk: se añadió la cabera del reporte
		$fecha_ini = date("d/m/Y",strtotime($this->objParam->getParametro('fecha_desde')));
		$fecha_fin = date("d/m/Y",strtotime($this->objParam->getParametro('fecha_hasta')));
        /*$this->Ln(3);
        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 10,5,35,20);
        $this->ln(5);
        $this->SetFont('','B',11);
        $this->Cell(0,5,"KARDEX DE ACTIVOS FIJOS",0,1,'C');
        $this->Cell(0,5,"DEL ".$fecha_ini." HASTA ".$fecha_fin,0,1,'C');
		$this->Cell(0,5,"(Expresado en Bolivianos)",0,1,'C');
        //$this->Ln(10);
        */
        $fini = explode("/", $fecha_ini);
        $ffin = explode("/", $fecha_fin);
        $gini = count($fini) > 2 ? intval($fini[2]) : '';
        $gfin = count($ffin) > 2 ? intval($ffin[2]) : '';
        $gestion = $gini != $gfin ? $gini . ' - ' . $gfin : $gini;
        $content = '<table border="1" cellpadding="1" style="font-size: 11px">
            <tr>
                <td style="width: 23%; color: #444444;" rowspan="5">
                    &nbsp;<br><img  style="width: 150px;" src="./../../../lib/' . $_SESSION['_DIR_LOGO'] . '" alt="Logo">
                </td>		
                <td style="width: 52%; color: #444444;text-align: center" rowspan="5">
                   <h4 style="font-size: 14px">KARDEX DE ACTIVOS FIJOS</h4>
                   <b style="font-size: 12px">DEL '. $fecha_ini.' HASTA '.$fecha_fin.'</b><br>
                   <b style="font-size: 12px">(Expresado en Bolivianos)</b>
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
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Estado:</b></td>
            </tr>
        </table>';
        $this->writeHTML($content, false, false, true, false, '');
    }

    function setDatos($datos) {

        $this->datos = $datos;            
    }

    function  generarReporte()
    {

        $this->AddPage();
		$this->SetFont('','',12);
		$row = $this->datos[0];
		$this->Ln(10);
				
 	$html = '<table cellpadding="2" font-size="20pt;">
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>CÓDIGO</b></td>
            <td style="width:210px;">'.$row['codigo'].'</td>            
        </tr>                
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>DESCRIPCION</b></td>
            <td style="width:210px;">'.$row['descripcion'].'</td>
            <td style="width:180px;"><b>NUMERAL</b></td>
            <td style="width:50px;">'.$row['cod_clasif'].'</td>        
        </tr>
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>NOMBRE</b></td>
            <td style="width:210px;">'.$row['denominacion'].'</td>
        </tr>
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>FECHA COMPRA</b></td>
            <td style="width:210px;">'.$row['fecha_compra'].'</td>
        </tr>
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>INICIO DE DEPRECIACION </b></td>
            <td style="width:210px;">'.$row['fecha_ini_dep'].'</td>
        </tr>
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>ESTADO DEL ACTIVO</b></td>
            <td style="width:210px;">'.$row['estado'].'</td>
            <td style="width:180px;"><b>%</b></td>
            <td style="width:50px;">100%</td>
            <td style="width:50px;">87%</td>            
        </tr>
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>UFV FECHA DE COMPRA</b></td>
            <td style="width:210px;">'.$row['ufv_fecha_compra'].'</td>
            <td style="width:180px;"><b>MONTO</b></td>
            <td style="width:50px;">'.number_format($row['monto_vigente_orig_100'],2,',','.').'</td>
            <td style="width:50px;">'.number_format($row['monto_vigente_orig'],2,',','.').'</td>            
        </tr>
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>VIDA ÚTIL ORIGINAL (MESES)</b></td>
            <td style="width:210px;">'.$row['vida_util_original'].'</td> 
            <td><b>% DEPRECIACIÓN</b></td>
            <td>'.$row['porcentaje_dep'].'</td>           
        </tr>
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>CENTRO DE COSTO</b></td>
            <td style="width:210px;">'.$row['desc_centro_costo'].'</td>
            <td><b>MÉTODO DEPRECIACIÓN</b></td>
            <td>'.$row['metodo_dep'].'</td>
        </tr>
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>UNIDAD SOLICITANTE</b></td>
            <td style="width:210px;">'.$row['desc_uo_solic'].'</td>
        </tr>
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>RESPONSABLE DE LA COMPRA</b></td>
            <td style="width:210px;">'.$row['desc_funcionario_compra'].'</td>
        </tr>                
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>LUGAR DE COMPRA</b></td>
            <td style="width:210px;">'.$row['lugar_compra'].'</td>
        </tr>                
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>No DE C31</b></td>
            <td style="width:210px;">'.$row['nro_cbte_asociado'].'</td>
            <td style="width:180px;"><b>FACTURA</b></td>
            <td style="width:50px;">'.$row['nro_factura'].'</td>
        </tr>                
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>FECHA DE C31</b></td>
            <td style="width:210px;">'.$row['fecha_cbte_asociado'].'</td>
        </tr>
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>No DE PROCESO COMPRA</b></td>
            <td style="width:210px;">'.$row['nro_pro_tramite'].'</td>
        </tr>                
		<tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>UBICACIÓN FÍSICA</b></td>
            <td style="width:210px;">'.$row['ubicacion'].'</td>
        </tr>
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>UBICACIÓN DEL BIEN (CIUDAD)</b></td>
            <td style="width:210px;">'.$row['ciudad'].'</td>
        </tr>                                        
        <tr style="font-size: 8pt; text-align: left; ">
            <td style="width:180px;"><b>RESPONSABLE DEL BIEN</b></td>
            <td style="width:210px;">'.$row['responsable'].'</td>
        </tr>
      </table>';

        $this->writeHTML($html);				
		$this->Ln();
        $arrayTmp=array();
        for ($fil=0; $fil < count($this->datos); $fil++) {
            if($this->datos[$fil]['codigo_mov']=='asig'||$this->datos[$fil]['codigo_mov']=='devol'||$this->datos[$fil]['codigo_mov']=='transf'||$this->datos[$fil]['codigo_mov']=='tranfdep') {
                array_push($arrayTmp,$this->datos[$fil]);
            }
        }
        if(!empty($arrayTmp)){//fRnk: adicionado para que no muestre en caso de estar vacío, HR1163
            $this->firstBox();
            $this->SetFont('','',8);
            $this->SetMargins(15, 50, 40);
            $this->tablewidthsHD = array(8, 40, 40, 20, 20, 45, 45);
            $this->tablealignsHD = array('C', 'C', 'C', 'C','C', 'C', 'C');
            $this->tablenumbersHD = array(0, 0, 0, 0, 0, 0, 0);
            $this->tablebordersHD = array('LRTB', 'LRTB', 'LRTB','LRTB', 'LRTB', 'LRTB', 'LRTB');
            $this->tabletextcolorHD = array();
            $cont=0;
            for ($fil=0; $fil < count($arrayTmp); $fil++) {
                $cont++;
                $fecha_sig='';
                if($arrayTmp[$fil+1]['fecha_mov']!=''){
                    $fecha_sig = date("d/m/Y",strtotime($arrayTmp[$fil-1]['fecha_mov']. ' -1 day'));
                    if($fecha_sig < date("d/m/Y",strtotime($arrayTmp[$fil]['fecha_mov']))){
                        $fecha_sig = date("d/m/Y",strtotime($arrayTmp[$fil]['fecha_mov']));
                    }
                }

                $RowArray = array(
                    's0' => $cont,
                    's1' => $arrayTmp[$fil]['num_tramite'],
                    's2' => $arrayTmp[$fil]['desc_mov'],
                    's3' => date("d/m/Y",strtotime($arrayTmp[$fil]['fecha_mov'])),
                    's4' => $fecha_sig,
                    's5' => $arrayTmp[$fil]['responsable'],
                    's6' => $arrayTmp[$fil]['cargo']
                );
                $this->MultiRowHeader($RowArray,false,1);
                $this->tablewidths = $this->tablewidthsHD;
            }
        }

        $arrayTmp=array();
        for ($fil=0; $fil < count($this->datos); $fil++) {
            if($this->datos[$fil]['codigo_mov']=='reval'||$this->datos[$fil]['codigo_mov']=='mejora'||$this->datos[$fil]['codigo_mov']=='ajuste'||$this->datos[$fil]['codigo_mov']=='retiro') {
                array_push($arrayTmp,$this->datos[$fil]);
            }
        }
        if(!empty($arrayTmp)) {//fRnk: adicionado para que no muestre en caso de estar vacío, HR1163
            $this->secondBox();

            $con=0;
            $this->SetFont('','',8);
            $this->SetMargins(15, 50, 40);
            $this->tablewidthsHD = array(8, 22, 23, 40, 25, 25, 25, 25, 25);
            $this->tablealignsHD = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $this->tablenumbersHD = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
            $this->tablebordersHD = array('LRTB', 'LRTB', 'LRTB','LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB');
            $this->tabletextcolorHD = array();

            for ($fil=0; $fil < count($arrayTmp); $fil++) {
                $con++;
                $monto=number_format($this->datos[$fil]['importe'],2,',','.');
                $RowArray = array(
                    's0' => $con,
                    's1' => date("d/m/Y",strtotime($this->datos[$fil]['fecha_mov'])),
                    's2' => $this->datos[$fil]['ufv_mov'],
                    's3' => $this->datos[$fil]['num_tramite'],
                    's4' => ($this->datos[$fil]['codigo_mov']=='mejora')?$monto:'',
                    's5' => ($this->datos[$fil]['codigo_mov']=='reval')?$monto:'',
                    's6' => ($this->datos[$fil]['codigo_mov']=='ajuste')?$monto:'',
                    's7' => ($this->datos[$fil]['codigo_mov']=='retiro')?$monto:'',
                    's8' => $this->datos[$fil]['meses']
                );
                $this->MultiRowHeader($RowArray,false,1);
                $this->tablewidths = $this->tablewidthsHD;
            }
        }
	
		$this->mainBox();
		$cont=0;
		$this->SetFont('','',8);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);				
		$this->tablewidthsHD = array(8, 40, 25, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 16);		
		$this->tablealignsHD = array('C', 'L', 'C', 'R', 'R', 'R', 'R', 'R', 'C', 'C', 'R', 'R', 'R', 'R');
		$this->tablenumbersHD = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		$this->tablebordersHD = array('LRTB', 'LRTB', 'LRTB','LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB');
		$this->tabletextcolorHD = array();
			
		$arrayTmp=array();
		for ($fil=0; $fil < count($this->datos); $fil++) {
			if($this->datos[$fil]['codigo_mov']!='asig'&&$this->datos[$fil]['codigo_mov']!='devol'&&$this->datos[$fil]['codigo_mov']!='transf'&&$this->datos[$fil]['codigo_mov']!='tranfdep') {
				array_push($arrayTmp,$this->datos[$fil]);
			}
		}
		
		for ($fil=0; $fil < count($arrayTmp); $fil++) {
            $RowArray = array(
                's0' => $cont,
                's1' => $arrayTmp[$fil]['desc_mov'],
                'S2' => date("d/m/Y",strtotime($arrayTmp[$fil]['fecha_mov'])),
                's3' => number_format($arrayTmp[$fil]['monto_vigente_orig_100'],2,',','.'),
                's4' => number_format($arrayTmp[$fil]['monto_vigente_orig'],2,',','.'),
                's5' => number_format($arrayTmp[$fil]['monto_actualiz'],2,',','.'),
                's6' => number_format($arrayTmp[$fil]['actualiz_monto_vigente'],2,',','.'),                                       
                's7' => number_format($arrayTmp[$fil]['monto_actualiz'],2,',','.'),
                's8' => $arrayTmp[$fil]['vida_util_usada'],
                's9' => $arrayTmp[$fil]['vida_util'],
                's10'=> number_format($arrayTmp[$fil]['dep_acum_gest_ant'],2,',','.'),
                's11'=> '',
                's12'=> number_format($arrayTmp[$fil]['depreciacion_per'],2,',','.'),
                's13'=> number_format($arrayTmp[$fil]['depreciacion_acum'],2,',','.'),
                's14'=> number_format($arrayTmp[$fil]['monto_vigente'],2,',','.')                
                );
	        	$this->MultiRowHeader($RowArray,false,1);
	        	$this->tablewidths = $this->tablewidthsHD;				
				$cont++;
		}			

    }

	function firstBox(){
		$this->AddPage();
		$this->Ln(10);
		$this->SetFont('','B',9);
	    $this->SetMargins(15, 50, 40);		
		$this->cell(218,10,'MOVIMIENTO FÍSICO DEL ACTIVO',1,0,'C',false,'',0,false,'RLBT','C');
		$this->Ln();
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);				
		$this->tablewidthsHD = array(8, 40, 40, 20, 20, 45, 45);
		$this->tablealignsHD = array('C', 'C', 'C', 'C','C', 'C', 'C');
		$this->tablenumbersHD = array(0, 0, 0, 0, 0, 0, 0);
		$this->tablebordersHD = array('LRTB', 'LRTB', 'LRTB','LRTB', 'LRTB', 'LRTB', 'LRTB');
		$this->tabletextcolorHD = array();
            $RowArray = array(
                's0' => 'Nro',
                's1' => 'N.PROCESO',
                's2' => 'PROCESO',
                's3' => 'DEL',
                's4' => 'AL',
                's5' => 'RESPONSABLE',		                                
                's6' => 'CARGO'
            	);                
        $this-> MultiRowHeader($RowArray,false,1);
        $this->tablewidths = $this->tablewidthsHD;
        		
	}
	
	function secondBox(){
		$this->AddPage();		
		$this->SetFont('','B',9);
	    $this->SetMargins(15, 50, 40);		
		$this->cell(218,10,'MOVIMIENTO FÍSICO DEL ACTIVO',1,0,'C',false,'',0,false,'RLBT','C');
		$this->Ln();
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);				
		$this->tablewidthsHD = array(8, 22, 23, 40, 25, 25, 25, 25, 25);		
		$this->tablealignsHD = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
		$this->tablenumbersHD = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
		$this->tablebordersHD = array('LRTB', 'LRTB', 'LRTB','LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB');
		$this->tabletextcolorHD = array();
            $RowArray = array(
                's0' => 'Nro',
                's1' => 'FECHA MOVIMIENTO',
                's2' => 'UFV DEL PROCESO',
                's3' => 'N.PROCESO',
                's4' => 'MEJORA',
                's5' => 'REVALORIZACIÓN',                                       
                's6' => 'AJUSTE (+ o -)',
                's7' => 'RETIRO',
                's8' => 'MESES'
                );                 
        $this-> MultiRowHeader($RowArray,false,1);
        $this->tablewidths = $this->tablewidthsHD;			
	}

	function mainBox(){
        //fRnk: no funcionaba todo el reporte, 'C'
		$this->AddPage();		
		$this->SetFont('','B',9);
	    $this->SetMargins(2, 50, 2);
		$this->SetXY(2, 50);		
		$this->cell(276,10,'DETALLE CONTABLE DEL BIEN',1,0,'C',false,'',0,false,'RLBT','L');
		$this->Ln();
		$this->SetFont('','B',8);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);				
		$this->tablewidthsHD = array(8, 40, 25, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 16);		
		$this->tablealignsHD = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
		$this->tablenumbersHD = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		$this->tablebordersHD = array('LRTB', 'LRTB', 'LRTB','LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB', 'LRTB');
		$this->tabletextcolorHD = array();
            $RowArray = array(
                's0' => 'Nro',
                's1' => 'PROCESO',
                'S2' => 'FECHA DE PROCESO',
                's3' => 'MONTO 100%',
                's4' => 'MONTO 87%',
                's5' => 'MONTO ACTUAL',
                's6' => 'INC. ACTUALIZADO/ACUMULADO',                                       
                's7' => 'VAL. ACTUALIZADO',
                's8' => 'VIDA USADA',
                's9' => 'VIDA RESIDUAL',
                's10'=> 'DEP. ACUM. GEST. ANT.',
                's11'=> 'ACT. DEP. GEST. ANT.',
                's12'=> 'DEP. DEL PERIODO',                
                's13'=> 'DEP. ACUMU.',
                'S14'=> 'VAL RESI.'
                );                 
        $this-> MultiRowHeader($RowArray,false,1);
        $this->tablewidths = $this->tablewidthsHD;		
	}
}
?>