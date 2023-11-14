<?php
set_time_limit(0);
ini_set('memory_limit','-1');
// Extend the TCPDF class to create custom MultiRow
/*
 * Autor RAC
 * Fecha: 16/03/2017
 * 
 * */
class RMovimiento2 extends ReportePDF {
	var $dataMaster;
	var $datos_detalle;
	var $ancho_hoja;
	var $gerencia;
	var $numeracion;
	var $ancho_sin_totales;
    var $tipoMov;
    var $motivo_ajuste;
    var $nro_documento_ajuste;
    var $posY;
    private $id_movimiento;
	
	function getDataSource(){
		return  $this->datos_detalle;		
	}
	
	function datosHeader  ($maestro, $detalle, $id_movimiento=0) {
		$this->ancho_hoja = $this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-10;
		$this->datos_detalle = $detalle;
		$this->dataMaster = $maestro;
        $this->id_movimiento = $id_movimiento;
        $this->tipoMov  = $this->dataMaster[0]['cod_movimiento']; 
        $this->motivo_ajuste = $this->dataMaster[0]['codigo_mov_motivo'];
         
        $this->nro_documento_ajuste = $this->dataMaster[0]['nro_documento'];

		if($this->tipoMov=='asig'||$tipo=='devol'){
			$this->SetMargins(7, 55, 5);
        } 
        else if ($this->tipoMov=='deprec'){
        	$this->SetMargins(7, 58, 5);
         }
		else{
			$this->SetMargins(7, 58, 5);
		}	
	}
	
	function Header() {
		$height = 6;
        $midHeight = 9;
        $longHeight = 18;
        $title_motivo_ret='';
        switch ($this->motivo_ajuste) {
            case 'PAR_RET': $title_motivo_ret=' REVALORIZADOS';break;               
                break; 
            case 'AJ_VID_UT_PAS': $title_motivo=' A LA VIDA UTIL';break;           
            default: 
                $title_motivo='';              
                break;
        }
        //($this->motivo_ajuste != '')?$title_motivo=' A LA VIDA UTIL':$title_motivo='';

        //fRnk: se modificó la cabecera del reporte
        $arr = explode("-", $this->dataMaster[0]['num_tramite']);
        $gestion=count($arr)>3?$arr[3]:'';
        if($this->tipoMov=='alta' || $this->tipoMov=='baja'){//deprec para depreciaciones
            $title='DETALLE INDIVIDUAL DE ACTIVOS FIJOS';
        }else{
                $title='FORMULARIO DE ' . mb_strtoupper($this->dataMaster[0]['movimiento'], 'UTF-8') . $title_motivo . ' DE ACTIVOS FIJOS ' . $title_motivo_ret;
        }
        $rowspan=5;
        $tr_estado='<tr>
                        <td style="width: 25%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Estado:</b> ' . $this->dataMaster[0]['estado'] . '</td>
                    </tr>';
        if($this->tipoMov=='deprec'){
            $rowspan=4;
            $tr_estado='';
        }
        $content='<table border="1" cellpadding="1" style="font-size: 11px">
            <tr>
                <td style="width: 23%; color: #444444;" rowspan="'.$rowspan.'">
                    &nbsp;<br><img  style="width: 150px;" src="./../../../lib/' . $_SESSION['_DIR_LOGO'] . '" alt="Logo">
                </td>		
                <td style="width: 52%; color: #444444;text-align: center" rowspan="'.$rowspan.'">
                   <h1 style="font-size: 16px">'. $title . '</h1>
                   <!--<h4 style="font-size: 14px">' . strtoupper($this->dataMaster[0]['depto']) . '</h4>-->
                </td>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Gestión:</b> ' . $gestion . '</td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Fecha:</b> ' . date('d/m/y h:i:s A') . '</td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>N° Trámite:</b> ' . $this->dataMaster[0]['num_tramite'] . '</td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Usuario:</b> ' . $_SESSION['_LOGIN'] . '</td>
            </tr>
            '.$tr_estado.'
        </table>';
        $this->writeHTML($content, false, false, true, false, '');
       //echo var_dump($this->dataMaster[0]); exit();
      /*
        $x = $this->GetX();
        $y = $this->GetY();
        $this->SetXY($x, $y);
       
		//$this->Image(dirname(__FILE__).'/../../lib/'.$_SESSION['_DIR_LOGO'], 10,5,35,20);
        $this->Image(dirname(__FILE__).'/../../lib'.$_SESSION['_DIR_LOGO'], 10,5,35,16);
		//$this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 10,5,35,16);

        $this->SetFontSize(12);
        $this->SetFont('', 'B');
        $this->Cell(53, $midHeight, '', 'LRT', 0, 'C', false, '', 0, false, 'T', 'C');
       
        $this->Cell(168, $midHeight, 'FORMULARIO DE '.strtoupper($this->dataMaster[0]['movimiento']).$title_motivo.' DE ACTIVOS FIJOS '.$title_motivo_ret, 'LRT', 0, 'C', false, '', 0, false, 'T', 'C');
        $this->tipoMov = $this->dataMaster[0]['cod_movimiento']; 

        $x = $this->GetX();
        $y = $this->GetY();
        $this->Ln();
        $this->SetFontSize(10);
        $this->SetFont('', 'B');
        $this->Cell(53, $midHeight, '', 'LRB', 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(168, $midHeight, strtoupper($this->dataMaster[0]['depto']), 'LRB', 0, 'C', false, '', 0, false, 'T', 'C');

        $this->SetFontSize(7);
        //PDF::writeHTMLCell($item->width, $item->height, $item->position_x, $item->position_y, $html, 0, 0, 0, true, 'L', true);


        $width1 = 15;
        $width2 = 25;
        $this->SetXY($x, $y);
        
        $this->SetFont('', '');
        $this->Cell(44, $longHeight, '', 1, 0, 'C', false, '', 0, false, 'T', 'C');

        $this->SetXY($x, $y+3);
        $this->setCellPaddings(2);
        $this->Cell($width1-4, $height, 'CÓDIGO:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('', 'B');
        $this->SetFontSize(6);
        $this->Cell($width2+8, $height,$this->dataMaster[0]['num_tramite'], "B", 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFontSize(7);
        $this->setCellPaddings(2);
        $this->Ln();
        $this->SetX($x);
        $this->SetFont('', '');
        $this->Cell($width1-4, $height, 'FECHA:', "", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('', 'B');
        $cab_fecha = date("d/m/Y",strtotime($this->dataMaster[0]['fecha_mov']));
        $this->Cell($width2+8, $height,$cab_fecha, "", 0, 'L', false, '', 0, false, 'T', 'C');
        $this->setCellPaddings(2);
        $this->Ln();
        $this->SetX($x);
        $this->SetFont('', '');
        //$this->Cell($width1-4, $height, 'PAGINA:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->Cell($width1-4, $height, '', "", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('', 'B');
        //$this->Cell($w = $width2, $h = $height, $txt = $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), $border = "B", $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $this->Cell($w = $width2, $h = $height, $txt = '', $border = "", $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
        $this->setCellPaddings(2);*/
		
		//$this->Ln();
		$this->fieldsHeader($this->tipoMov);
		$this->generarCabecera($this->tipoMov);
		
	}

    public function fieldsHeader($tipo){

            $this->SetFontSize(10);
            $this->Ln(2);
            if($tipo=='asig'){
                $this->Ln();
                $this->SetFont('', 'B');
                $this->Cell(35, $height,'Responsable:', "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster[0]['responsable'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                $this->SetFont('', 'B');

                //Ciudad
                $this->SetFont('', 'B');
                $this->Cell(25, $height,'Ciudad:', "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster[0]['lugar'], $border = 0, $ln = 1, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                 
                //Custodio
                $this->SetFont('', 'B');
                $lblCust='Custodio:';

                $this->Cell(35, $height,$lblCust, "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster[0]['custodio'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

                //Oficina
                $this->SetFont('', 'B');
                $this->Cell(25, $height,'Oficina:', "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster[0]['oficina'], $border = 0, $ln = 1, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                
                //Dirección
                $this->SetFont('', 'B');
                $this->Cell(135, $height,'', "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->Cell(25, $height,'Dirección:', "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->MultiCell($w = 100, $h = $hGlobal, $txt = $this->cortar_texto($this->dataMaster[0]['direccion'],165), $border = 0, $align = 'L', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
                $this->Cell(135, $height,'', "", 0, 'L', false, '', 0, false, 'T', 'C');
            } else if($tipo=='devol'){
                $this->Ln();
                $this->SetFont('', 'B');
                $this->Cell(35, $height,'Responsable:', "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster[0]['responsable'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                $this->SetFont('', 'B');
                 $this->SetFont('', 'B');
                $lblCust='Custodio:';
                $this->Cell(35, $height,$lblCust, "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster[0]['custodio'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                $this->Ln(0.4);
                
                
            } else if ($tipo=='deprec'){
                $this->Ln();
                $this->SetFont('', 'B');
                $this->Cell(35, $height,'Depreciación hasta:', "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster[0]['fecha_hasta'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            } else if ($tipo=='actua'){
                $this->Ln();
                $this->SetFont('', 'B');
                $this->Cell(35, $height,'Actualización hasta:', "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster[0]['fecha_hasta'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            }else if($tipo=='transf'){
                $this->Ln();
                $this->SetFont('', 'B');
                $this->Cell(35, $height,'Origen:', "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster[0]['responsable'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                $this->SetFont('', 'B');

                //Ciudad
                $this->SetFont('', 'B');
                $this->Cell(25, $height,'Ciudad:', "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster[0]['lugar'], $border = 0, $ln = 1, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                 
                //Custodio
                $this->SetFont('', 'B');
                $this->Cell(35, $height,'Destino:', "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster[0]['responsable_dest'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');

                //Oficina
                $this->SetFont('', 'B');
                $this->Cell(25, $height,'Oficina:', "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster[0]['oficina'], $border = 0, $ln = 1, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                
                //Dirección
                $this->SetFont('', 'B');
                $this->Cell(135, $height,'', "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->Cell(25, $height,'Dirección:', "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->MultiCell($w = 100, $h = $hGlobal, $txt = $this->cortar_texto($this->dataMaster[0]['direccion'],165), $border = 0, $align = 'L', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);

                //Custodio
                $this->SetFont('', 'B');
                $lblCust='Custodio:';
                $this->Cell(35, $height,$lblCust, "", 0, 'L', false, '', 0, false, 'T', 'C');
                $this->SetFont('', '');
                $this->Cell($w = 100,$h = $hGlobal, $txt = $this->dataMaster[0]['custodio'], $border = 0, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
                
            } else if($tipo=='alta' || $tipo=='baja'){
                $this->SetFont('', 'B');
                $this->Cell(100, 1,'Tipo de proceso: '.$this->dataMaster[0]['movimiento'], "", 0, 'L', false, '', 0, false, 'T', 'C');
            }

            //Estado
            $this->Ln();
            $this->SetFont('', 'B');
            $this->SetFont('', '');
            $this->SetFont('', 'B');
            $this->Cell(100, $height,'N° Informe: '.$this->nro_documento_ajuste, "", 0, 'L', false, '', 0, false, 'T', 'C');
            $this->Ln();
            //Glosa
            $this->SetFont('', 'B');
            $this->Cell($width2+8, $height,'Glosa:', "", 0, 'L', false, '', 0, false, 'T', 'C');
            $this->Ln();
            $this->SetFont('', '');
            $this->MultiCell($w = 0, $h = $hLong, $txt = $this->cortar_texto($this->dataMaster[0]['glosa'],495), $border = 0, $align = 'L', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
            $this->Ln();
            $this->firstPage++;
            
            $this->posY = $this->GetY();


    }

	function Firmas() {
		$this->SetFontSize(7);
        if ($this->dataMaster[0]['cod_movimiento'] == 'retiro'){
            $responsable = $this->dataMaster[0]['resp_af'];
        }else{
            $responsable = $this->dataMaster[0]['responsable_depto'];
        }
        
        $_firma100='';
        $_firma110=$responsable;
        $_firma111='RESPONSABLE ACTIVOS FIJOS';
        
        $_firma200='';
        $_firma210='';
        $_firma211='';

        $_firma300='';
        $_firma310='';
        $_firma311='';

        $_firma400='';
        $_firma410='';
        $_firma411='';

        if($this->tipoMov=='asig'){
            $_firma100=$this->dataMaster[0]['responsable_depto'];
            $_firma110='RESPONSABLE ACTIVOS FIJOS';
            $_firma111='ENTREGUÉ CONFORME';
            
            $_firma200=strtoupper($this->dataMaster[0]['responsable']);
            $_firma210=strtoupper($this->dataMaster[0]['nombre_cargo']);
            $_firma211='RECIBÍ CONFORME';

            if($this->dataMaster[0]['custodio']!=''){
                $_firma300=strtoupper($this->dataMaster[0]['custodio']);
                $_firma310='CI. '.strtoupper($this->dataMaster[0]['ci_custodio']);
                $_firma311='* CUSTODIO';    
            }
        }
		
		
		if($this->tipoMov=='transf'){
            $_firma100=$this->cortar_texto_firma($this->dataMaster[0]['responsable_depto']);
            $_firma110='RESPONSABLE ACTIVOS FIJOS';
            $_firma111='SUPERVISOR';
            
            $_firma200=$this->cortar_texto_firma(strtoupper($this->dataMaster[0]['responsable']));
            $_firma210=$this->cortar_texto_firma(strtoupper($this->dataMaster[0]['nombre_cargo']));
            $_firma211='ENTREGUE CONFORME';
            
            $_firma300=$this->cortar_texto_firma(strtoupper($this->dataMaster[0]['responsable_dest']));
            $_firma310=$this->cortar_texto_firma(strtoupper($this->dataMaster[0]['nombre_cargo_dest']));
            $_firma311='RECIBÍ CONFORME';

            if($this->dataMaster[0]['custodio']!=''){
                $_firma400=strtoupper($this->dataMaster[0]['custodio']);
                $_firma410='CI. '.strtoupper($this->dataMaster[0]['ci_custodio']);
                $_firma411='* CUSTODIO';    
            }
        }

        if($this->tipoMov=='devol'){
            $_firma100=$this->dataMaster[0]['responsable_depto'];
            $_firma110='RESPONSABLE ACTIVOS FIJOS';
            $_firma111='RECIBÍ CONFORME';
            
            $_firma200=$this->cortar_texto_firma(strtoupper($this->dataMaster[0]['responsable']));
            $_firma210=$this->cortar_texto_firma(strtoupper($this->dataMaster[0]['nombre_cargo']));
            $_firma211='ENTREGUÉ CONFORME';

            if($this->dataMaster[0]['custodio']!=''){
                $_firma300=strtoupper($this->dataMaster[0]['custodio']);
                $_firma310='CI. '.strtoupper($this->dataMaster[0]['ci_custodio']);
                $_firma311='* CUSTODIO';    
            }

        }


        //Bordes
        $border1='';//'LRT';
        $border2='';//'LR';
        $border3='';//'LRBT';

        $this->Cell(64, $midHeight, '', $border1, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(64, $midHeight, '', $border1, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(64, $midHeight, '', $border1, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(63, $midHeight, '', $border1, 1, 'C', false, '', 0, false, 'T', 'C');
         
        $this->Cell(64, $midHeight, '', $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(64, $midHeight, '', $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(64, $midHeight, '', $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(63, $midHeight, '', $border2, 1, 'C', false, '', 0, false, 'T', 'C');
        
        $this->Cell(64, $midHeight, '', $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(64, $midHeight, '', $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(64, $midHeight, '', $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(63, $midHeight, '', $border2, 1, 'C', false, '', 0, false, 'T', 'C');

        $this->Cell(64, $midHeight, '', $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(64, $midHeight, '', $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(64, $midHeight, '', $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(63, $midHeight, '', $border2, 1, 'C', false, '', 0, false, 'T', 'C');

        $this->Cell(64, $midHeight, $_firma100, $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(64, $midHeight, $_firma200, $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(64, $midHeight, $_firma300, $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(63, $midHeight, $_firma400, $border2, 1, 'C', false, '', 0, false, 'T', 'C');

        $this->Cell(64, $midHeight, $_firma110, $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(64, $midHeight, $_firma210, $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(64, $midHeight, $_firma310, $border2, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(63, $midHeight, $_firma410, $border2, 1, 'C', false, '', 0, false, 'T', 'C');
        
        $this->Cell(64, $midHeight, $_firma111, $border3, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(64, $midHeight, $_firma211, $border3, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(64, $midHeight, $_firma311, $border3, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Cell(63, $midHeight, $_firma411, $border3, 1, 'C', false, '', 0, false, 'T', 'C');

        //Nota a pie
        $this->Ln(5);
        if($this->tipoMov=='asig'){
            if($this->dataMaster[0]['custodio']!=''){
                $this->Cell(130, $midHeight, '* Esta casilla será firmada por personal que trabaja en la empresa pero no figura en planillas', $border1, 0, 'L', false, '', 0, false, 'T', 'C');
            }
            
        } else if($this->tipoMov=='transf'){
            if($this->dataMaster[0]['custodio']!=''){
                $this->Cell(130, $midHeight, '* Esta casilla será firmada por personal que trabaja en la empresa pero no figura en planillas', $border1, 0, 'L', false, '', 0, false, 'T', 'C');
            }
             
        } else if($this->tipoMov=='devol'){
            if($this->dataMaster[0]['custodio']!=''){
                $this->Cell(130, $midHeight, '* Esta casilla será firmada por personal que trabaja en la empresa pero no figura en planillas', $border1, 0, 'L', false, '', 0, false, 'T', 'C');
            }
            
        }
		
		
	}

    function cortar_texto_firma($texto){
        $lim=39;
        $len = strlen($texto);
        $cad = $texto;
        if($len > $lim){
            $cad = substr($texto, 0, $lim).' ...';
        }
        return $cad;
    }

    function cortar_texto($texto,$lim){
        $len = strlen($texto);
        $cad = $texto;
        if($len > $lim){
            $cad = substr($texto, 0, $lim).' ...';
        }
        return $cad;
    }
   
   function generarReporte() {
   	      $this->setFontSubsetting(false);
		  $this->AddPage();
		  $tipo = $this->tipoMov;
          $tipo_mov_ajus = $this->motivo_ajuste;
		  $this->SetFontSize(7);

          //Definición de la fila donde empezar a desplegar los datos
          if($this->tipoMov=='asig'){
            $this->SetY($this->posY+4.2);
          } else if($this->tipoMov=='devol'){
           $this->SetY($this->posY+4.2);
          } else if($this->tipoMov=='transf'){
            $this->SetY($this->posY+4.2);
          } else if($this->tipoMov=='alta'){
            $this->SetY($this->posY+4.2);
          } else if($this->tipoMov=='retiro'){
            $this->SetY($this->posY+4.2);
          } else if($this->tipoMov=='deprec'){
            $this->SetY($this->posY+8.2);
          } else {
            $this->SetY($this->posY+8.2);
          }
      
         $totalAF = 0;
         $totalCompra = 0;
         
		 foreach ($this->getDataSource() as $datarow) {
            if($tipo=='baja'){
               
			  $this->tablealigns=array('L','L','L','L','L','L','L');
		      $this->tablenumbers=array(0,0,0,0,0,0,0);
		      $this->tableborders=array('RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB');
	          $this->tabletextcolor=array();
			  $RowArray = array(
	            			's0'  => $i+1,
	            			's1' => $datarow['codigo'],   
	                        's2' => $datarow['descripcion'],        
	                        's3' => $datarow['marca'],
	                        's4' => $datarow['nro_serie'],            
	                        's5' => $datarow['estado_fun'],
	                        's6' => $datarow['motivo']);
				
				
            } else if($tipo=='reval'){
               
				
				$this->tablealigns=array('L','L','L','L','L','L','R','R','L');
//		        $this->tablenumbers=array(0,0,0,0,0,0,1,1);
		        $this->tableborders=array('RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB');
	            $this->tabletextcolor=array();
				
				$RowArray = array(
	            			's0'  => $i+1,
	            			's1' => $datarow['codigo'],   
	                        's2' => $datarow['descripcion'],       
	                        's4' => $datarow['marca'],
	                        's5' => $datarow['nro_serie'],
	                        's6' => $datarow['estado_fun'] ,  
	                        's7' => $datarow['vida_util'] ,
	                        's8' => number_format($datarow['importe'],2,',','.'),
	                        );
				

            } else if($tipo=='deprec'||$tipo=='actua'){
               
				
				$this->tablealigns=array('L','L','L','L','L','L','L');
		        $this->tablenumbers=array(0,0,0,0,0,0,0);
		        $this->tableborders=array('RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB');
	            $this->tabletextcolor=array();
				
				$RowArray = array(
	            			's0'  => $i+1,
	            			's1' => $datarow['codigo'],   
	                        's2' => $datarow['descripcion'],       
	                        's4' => $datarow['marca'],
	                        's5' => $datarow['nro_serie'],
	                        's6' => $datarow['estado_fun'] ,  
	                        's7' => number_format($datarow['monto_compra'],2,',','.'),
	                        's8' => number_format($datarow['importe'],2,',','.')
	                        );
				

            } else if($tipo=='asig'||$tipo=='devol'){
                	
               				
				$this->tablealigns=array('L','L','L','L','L','L','L');
		        $this->tablenumbers=array(0,0,0,0,0,0,0);
		        $this->tableborders=array('RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB');
	            $this->tabletextcolor=array();
				
				$RowArray = array(
	            			's0'  => $i+1,
	            			's1' => $datarow['codigo'],   
                            's2' => $datarow['denominacion'],
	                        's3' => $datarow['descripcion'],
	                        's4' => $datarow['estado_fun'] ,           
	                        's5' => ''
	                        );
				
				
            } else if($tipo=='transf'){
                    
                            
                $this->tablealigns=array('L','L','L','L','L','L','L');
                $this->tablenumbers=array(0,0,0,0,0,0,0);
                $this->tableborders=array('RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB');
                $this->tabletextcolor=array();
                
                $RowArray = array(
                            's0'  => $i+1,
                            's1' => $datarow['codigo'],   
                            's2' => $datarow['denominacion'],
                            's3' => $datarow['descripcion'],
                            's4' => $datarow['estado_fun'] ,           
                            's5' => $datarow['observaciones']
                            );
                
                
            } else if($tipo=='alta'){
                    
                            
                $this->tablealigns=array('L','L','L','L','L','C','R','R','L','C','L');
//                $this->tablenumbers=array(0,0,0,0,0,0,1,1,0,0);
                $this->tableborders=array('RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB');
                $this->tabletextcolor=array();
                //totales de montos
                $totalAF += $datarow['monto_compra_orig'];
                $totalCompra += $datarow['monto_compra_orig_100'];

                $RowArray = array(
                            's0'  => $i+1,
                            's1' => $datarow['codigo'],
                            's2' => $datarow['desc_clasificacion'],
                            's3' => $datarow['denominacion'],
                            's4' => $datarow['descripcion'],
                            's5' => date("d/m/Y",strtotime($datarow['fecha_ini_dep'])),
                            's6' => number_format($datarow['monto_compra_orig'],2,',','.'),
                            's7' => number_format($datarow['monto_compra_orig_100'],2,',','.'),
                            's8' => $datarow['nro_cbte_asociado'],
                            's9' => (substr($datarow['codigo'],0,9) == '11.01.05.')? '-' : $datarow['vida_util_original'],
                            's10' => ''
                            );
                
                
            }  else if($tipo=='retiro'){

                if( $this->motivo_ajuste == 'PAR_RET'){
                    
                    $this->tablealigns=array('L','L','L','L','L','L');
                    $this->tablenumbers=array(0,0,0,0,0,0);
                    $this->tableborders=array('RLTB','RLTB','RLTB','RLTB','RLTB','RLTB');
                    $this->tabletextcolor=array();
                    
                    $RowArray = array(
                                's0'  => $i+1,
                                's1' => $datarow['codigo'],   
                                's2' => $datarow['codigo_afval'],
                                's3' => $datarow['denominacion'],
                                's4' => $datarow['descripcion'],                                        
                                's5' => $datarow['observaciones']
                                );
                }else{
                    
                            
                $this->tablealigns=array('L','L','L','L','L','L','L');
                $this->tablenumbers=array(0,0,0,0,0,0,0);
                $this->tableborders=array('RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB');
                $this->tabletextcolor=array();
                
                $RowArray = array(
                            's0'  => $i+1,
                            's1' => $datarow['codigo'],   
                            's2' => $datarow['denominacion'],
                            's3' => $datarow['descripcion'],
                            's4' => $datarow['estado_fun'] ,           
                            's5' => $datarow['observaciones']
                            );
                }                
                
            //} else if($tipo == 'ajuste'){
            }   else if ($tipo_mov_ajus == 'AJ_VID_UT_PAS') { 

                    $this->tablealigns=array('L','L','L','L','R','C','C','R','R','R','R');
                    $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,0,0);
                    $this->tableborders=array('RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB');
                    $this->tabletextcolor=array();
                    $RowArray = array(
                                  's0'  => $i+1,
                                  's1' => $datarow['codigo'],   
                                  's2' => $datarow['denominacion'],   
                                  's3' => $datarow['descripcion'],       
                                  's4' => $datarow['monto_vig_actu'],
                                  's5' => $datarow['vida_util'],
                                  's6' => $datarow['vida_util_residual'],           
                                  's7' => $datarow['deprec_acum_ant'],
                                  's8' => $datarow['valor_residual'],
                                  's9' => $this->nro_documento_ajuste,
                                  's10' => $datarow['observacion']                                 
                                  );           
                
            } else {
			  $this->tablealigns=array('L','L','L','L','L','L','L');
		      $this->tablenumbers=array(0,0,0,0,0,0,0);
		      $this->tableborders=array('RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB');
	          $this->tabletextcolor=array();
			  $RowArray = array(
	            			's0'  => $i+1,
	            			's1' => $datarow['codigo'],   
	                        's2' => $datarow['descripcion'],   
	                        's3' => $datarow['tipo_activo'],       
	                        's4' => $datarow['marca'],
	                        's5' => $datarow['nro_serie'],
	                        's6' => $datarow['fecha_compra'] ,           
	                        's7' => $datarow['estado_fun']
	                        );
				
				
            }
            $i++;
			
			$this-> MultiRow($RowArray,false,1);
			$this->revisarfinPagina();
			
        }
		if($tipo=='alta'){
            $this->tablealigns=array('L','L','L','L','L','C','R','R','L','L','L');
//            $this->tablenumbers=array(0,0,0,0,0,0,1,1,0,0);
            $this->tableborders=array('','','','','','','RLTB','RLTB','','','');
            $this->tabletextcolor=array();
            $RowArray = array(
                's0'  => '',
                's1' => '',
                's2' => '',
                's3' => '',
                's4' => '',
                's5' => 'TOTALES:',
                's6' => number_format($totalAF,2,',','.'),
                's7' => number_format($totalCompra,2,',','.'),
                's8' => '',
                's9' => '',
                's10' => ''
            );
            $this-> MultiRow($RowArray,false,1);
        }
		$this->Ln(10);	
		
		$this->Firmas();
		
   } 
   
   function generarCabecera($tipo){
        $tipo_mov_ajus = $this->motivo_ajuste;
		//armca caecera de la tabla
		$this->SetFontSize(9);
        $this->SetFont('', 'B');
		///////////////////////////////////////
		if($tipo=='baja'){
            
	          $this->tablewidthsHD=array(10,35,80,50,50,20,20);
	          $this->tablealignsHD=array('C','C','C','C','C','C','C');
		      $this->tablenumbersHD=array(0,0,0,0,0,0,0);
		      $this->tablebordersHD=array('TB','TB','TB','TB','TB','TB','TB');
	          $this->tabletextcolorHD=array();
			  $RowArray = array(
	            			's0'  => 'Nro',
	            			's1' => 'Código',   
	                        's2' => 'Descripción',
	                        's3' => 'Marca',
	                        's4' => 'Nro. Serie',            
	                        's5' => 'Estado Fun.',
	                        's6' => 'Motivo');
			

        } else if($tipo=='reval'){
        	
			  $this->tablewidthsHD=array(8,35,90,35,25,20,20,20);
	          $this->tablealignsHD=array('C','C','C','C','C','C','C');
		      $this->tablenumbersHD=array(0,0,0,0,0,0,0,0);
		      $this->tablebordersHD=array('TB','TB','TB','TB','TB','TB','TB','TB');
	          $this->tabletextcolorHD=array();
			  $RowArray = array(
	            			's0'  => 'Nro',
	            			's1' => 'Código',   
	                        's2' => 'Descripción',
	                        's3' => 'Marca',
	                        's4' => 'Nro. Serie',            
	                        's5' => 'Estado Fun.',
	                        's6' => 'Inc.Vida Util',
	                        's7' => 'Inc.Valor')  ;

        } else if($tipo=='deprec'||$tipo=='actua'){
        	
			  $this->tablewidthsHD=array(8,35,102,35,25,20,20,20);
	          $this->tablealignsHD=array('C','C','C','C','C','C','C');
		      $this->tablenumbersHD=array(0,0,0,0,0,0,0,0);
		      $this->tablebordersHD=array('LTB','TB','TB','TB','TB','TB','TB','TBR');
	          $this->tabletextcolorHD=array();
			  $RowArray = array(
	            			's0'  => 'Nro',
	            			's1' => 'Código',   
	                        's2' => 'Descripción',
	                        's3' => 'Marca',
	                        's4' => 'Nro. Serie',            
	                        's5' => 'Estado Fun.',
	                        's6' => 'Imp. Compral',
	                        's7' => 'Imp. Vigente');
				
        } else if($tipo=='asig'||$tipo=='devol'){
            	
              $this->tablewidthsHD=array(8,25,59,90,26,57);
			   //$this->tablewidths=array(8,31,84.5,34,32.5,26.5,18,20.5);
	          $this->tablealignsHD=array('C','C','C','C','C','C','C','C');
		      $this->tablenumbersHD=array(0,0,0,0,0,0,0,0);
		      $this->tablebordersHD=array('LTB','TB','TB','TB','TB','TBR');
	          $this->tabletextcolorHD=array();
			  $RowArray = array(
	            			's0'  => 'Nro',
	            			's1' => 'Código',   
	                        's2' => 'Denominación',
                            's3' => 'Descripción',
	                        //'s3' => 'Marca',
	                        //'s4' => 'Nro. Serie',            
	                        's4' => 'Estado Fun.',           
	                        's5' => 'Observaciones');
			
			
            
        } else if($tipo=='transf'){
            $this->tablewidthsHD=array(8,25,59,90,26,57);
            //$this->tablewidths=array(8,31,84.5,34,32.5,26.5,18,20.5);
            $this->tablealignsHD=array('C','C','C','C','C','C','C','C');
            $this->tablenumbersHD=array(0,0,0,0,0,0,0,0);
            $this->tablebordersHD=array('LTB','TB','TB','TB','TB','TBR');
            $this->tabletextcolorHD=array();
            $RowArray = array(
                            's0'  => 'Nro',
                            's1' => 'Código',   
                            's2' => 'Denominación',
                            's3' => 'Descripción',
                            's4' => 'Estado Fun.',           
                            's5' => 'Observaciones');
            
            
            
        }  else if($tipo=='alta'){
                
              $this->tablewidthsHD=array(8,23,23,35,50,19,24,24,15,15,29);
               //$this->tablewidths=array(8,31,84.5,34,32.5,26.5,18,20.5);
              $this->tablealignsHD=array('C','C','C','C','C','C','C','C','C','C','c');
              $this->tablenumbersHD=array(0,0,0,0,0,0,0,0,0);
              $this->tablebordersHD=array('LTB','TB','TB','TB','TB','TB','TBR');
              $this->tabletextcolorHD=array();
              $RowArray = array(
                            's0'  => 'Nro',
                            's1' => 'Código',
                            's2' => 'Clasificación',
                            's3' => 'Denominación',
                            's4' => 'Descripción',
                            's5' => 'Inicio.Dep.',           
                            's6' => 'Costo AF',
                            's7' => 'Valor Compra',
                            's8' => 'C31',
                            's9' => 'Vida U.',
                            's10' =>'Observaciones'
                        );
            
            
            
        } else if($tipo=='retiro'){

            if( $this->motivo_ajuste == 'PAR_RET'){

               $this->tablewidthsHD=array(8,25,30,65,73,65);                
               $this->tablealignsHD=array('C','C','C','C','C','C');
               $this->tablenumbersHD=array(0,0,0,0,0,0);
               $this->tablebordersHD=array('LTB','TB','TB','TB','TB','TBR');
               $this->tabletextcolorHD=array();
               $RowArray = array(
                             's0'  => 'Nro',
                             's1' => 'Código',   
                             's2' => 'Código Retiro',   
                             's3' => 'Denominación',
                             's4' => 'Descripción',                             
                             's5' => 'Observaciones');                
            }else{
                
            $this->tablewidthsHD=array(8,25,50,90,26,57);
               //$this->tablewidths=array(8,31,84.5,34,32.5,26.5,18,20.5);
              $this->tablealignsHD=array('C','C','C','C','C','C','C','C');
              $this->tablenumbersHD=array(0,0,0,0,0,0,0,0);
              $this->tablebordersHD=array('LTB','TB','TB','TB','TB','TBR');
              $this->tabletextcolorHD=array();
              $RowArray = array(
                            's0'  => 'Nro',
                            's1' => 'Código',   
                            's2' => 'Denominación',
                            's3' => 'Descripción',
                            's4' => 'Estado Fun.',           
                            's5' => 'Observaciones');
              }
        //} else if($tipo == 'ajuste'){
            
        }else if ($tipo_mov_ajus == 'AJ_VID_UT_PAS') {
                $this->tablewidthsHD=array(8,20,35,40,25,20,20,25,25,15,30);
                $this->tablealignsHD=array('C','C','C','C','C','C','C','C','C','C');
                $this->tablenumbersHD=array(0,0,0,0,0,0,0,0,0,0,0);
                $this->tablebordersHD=array('LTB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TB');
                $this->tabletextcolorHD=array();
                
                $RowArray = array(
                            's0'  => 'Nro',
                            's1' => 'Código',   
                            's2' => 'Denominación',        
                            's3' => 'Descripción',
                            's4' => 'Valor Actualizado',            
                            's5' => 'Vida Original',
                            's6' => 'Vida Residual',
                            's7' => 'Dep. Gesti. Anter.',                            
                            's8' => 'Valor Residual.',
                            's9' => 'N° Informe',
                            's10' => 'Observaciones'
                        );                
            
        
        } else {
            
            $this->tablewidthsHD=array(8,31,94,34,32.5,26.5,18,20.5);
            $this->tablealignsHD=array('C','C','C','C','C','C','C','C');
	        $this->tablenumbersHD=array(0,0,0,0,0,0,0,0);
	        $this->tablebordersHD=array('LTB','TB','TB','TB','TB','TB','TB','TBR');
            $this->tabletextcolorHD=array();
		    
		    $RowArray = array(
            			's0'  => 'Nro',
            			's1' => 'Código',   
                        's2' => 'Descripcion',        
                        's3' => 'Tipo de Activo',
                        's4' => 'Marca',            
                        's5' => 'Nro. Serie',
                        's6' => 'Fecha Compra',
                        's7' => 'Estado Fun.');

        }
		
		/////////////////////////////////	                         
        $this-> MultiRowHeader($RowArray,false,1);
		$this->tablewidths = $this->tablewidthsHD;
		
    }

   function revisarfinPagina(){
		$dimensions = $this->getPageDimensions();
		$hasBorder = false; //flag for fringe case

		$startY = $this->GetY();
		$this->getNumLines($row['cell1data'], 80);
        if ($this->tipoMov=='deprec'){ //fRnk: HR01341
            $this->SetMargins(7, 66.2, 5);
        }
		if (($startY + 4 * 3) + $dimensions['bm'] > ($dimensions['hk'])) {
		    if($this->total!= 0){
				$this->AddPage();
			}
		} 
	}

    function Footer() {
        $this->setY(-15);
        $ormargins = $this->getOriginalMargins();
        $this->SetTextColor(0, 0, 0);
        //set style for cell border
        $line_width = 0.85 / $this->getScaleFactor();
        $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $ancho = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right']) / 3);
        $this->Ln(2);
        $cur_y = $this->GetY();
        //$this->Cell($ancho, 0, 'Generado por XPHS', 'T', 0, 'L');
        $this->Cell($ancho, 0, 'Usuario: '.$_SESSION['_LOGIN'], '', 0, 'L');
        $pagenumtxt = 'Página'.' '.$this->getAliasNumPage().' de '.$this->getAliasNbPages();
        $this->Cell($ancho, 0, $pagenumtxt, '', 0, 'C');
        $this->Cell($ancho, 0, $_SESSION['_REP_NOMBRE_SISTEMA'], '', 0, 'R');
        $this->Ln();
        $fecha_rep = date("d-m-Y H:i:s");
        $this->Cell($ancho, 0, "Fecha : ".$fecha_rep, '', 0, 'L');
        $this->Ln($line_width);
        $this->Ln();
        $barcode = $this->getBarcode();
        $style = array(
            'position' => $this->rtl?'R':'L',
            'align' => $this->rtl?'R':'L',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'padding' => 0,
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'text' => false
        );

        if($this->dataMaster[0]['con_firma_digital']=='si') { //fRnk: adicionado para firma digital
            $style['position']='C';
            $this->write1DBarcode($barcode, 'C128B', $ancho*2, $cur_y + $line_width+5, '', (($this->getFooterMargin() / 3) - $line_width), 0.3, $style, '');
            $style = array(
                'position' => 'R',
                'vpadding' => 'auto',
                'hpadding' => 'auto',
                'fgcolor' => array(0,0,0),
                'bgcolor' => false,
                'module_width' => 1,
                'module_height' => 1
            );
            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                $url = "https://";
            else
                $url = "http://";
            $url.= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $aurl=explode('/lib/', $url);
            $this->write2DBarcode($aurl[0].'/sis_kactivos_fijos/reportes/FirmaDigital.php?m='.$this->id_movimiento, 'QRCODE,H', 10, 185, 28, 28, $style, 'N');
            $this->SetFontSize(6);
            //$this->Text(236.7, 182, 'FIRMA DIGITAL');
        } else {
            $style['position']='R';
            $this->write1DBarcode($barcode, 'C128B', $ancho*2, $cur_y + $line_width+5, '', (($this->getFooterMargin() / 3) - $line_width), 0.3, $style, '');
        }
    }
   
   
 
}
?>