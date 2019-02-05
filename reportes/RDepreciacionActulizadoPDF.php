<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');
class RDepreciacionActulizadoPDF extends  ReportePDF{
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
        $this->ln(3);
        $this->SetMargins(1, 50.5, 1);

        $title = "DETALLE DE DEPRECIACION DE ACTIVOS FIJOS";
        $codigo = $this->datos[0]['codigo'];
        $codigo == "11" && $title = "DETALLE DE AMORTIZACION DE ACTIVOS FIJOS INTANGIBLES";

        $this->SetFont('','B',10);
        $this->Cell(0,5,"BOLIVIANA DE AVIACION",0,1,'C');
        $this->Cell(0,5,$title,0,1,'C');

        $this->SetFont('','B',5);
        $this->Cell(0,3,' Al: '.date_format(date_create($this->objParam->getParametro('fecha_hasta')), 'd/m/Y'),0,1,'C');

        $moneda = '';
        if($this->objParam->getParametro('id_moneda') == 1 or $this->objParam->getParametro('id_moneda')==''){
            $moneda = 'Bolivianos';
        }else if($this->objParam->getParametro('id_moneda')== 2){
            $moneda = 'Dolares Americanos';
        }else{
            $moneda = 'UFV';
        }
        $this->Cell(0,2,'(Expresado en '.$moneda.')',0,1,'C');

        $this->SetFont('','B',6);
        $this->Ln(11);
		$descnom=$this->objParam->getParametro('desc_nombre');
		switch ($descnom) {
			case 'desc' :$desno='DESCRIPCIÓN';break;
			case 'nombre' :$desno='DENOMINACIÓN';break;
			case 'ambos':$desno='NOMBRE/DESC.';break;
			default:$desno='DENOMINACIÓN';break;
		}		
		//$this->objParam->getParametro('desc_nombre')=='desc'?$desno='DESCRIPCIÓN':$desno='DENOMINACIÓN';
		$y = 13;

        $depre_acu = 'DEP. ACUM. GEST. ANT.';
        $actu_acu  = 'ACT. DEPREC. GEST. ANT.';
        $depre_ges = 'DEP. GESTIÓN';
        $depre_a = 'DEP.'."\x0A".' ACUM.';
        if ($codigo == "11"){
            $depre_acu = 'AMOR. ACUM. GEST. ANT.';
            $actu_acu = 'ACT. AMOR. GEST. ANT.';
            $depre_ges = 'AMOR. GESTION';
            $depre_a = 'AMOR.'."\x0A".' ACUM.';

        }

        $this->MultiCell(8,$y, 'NUM',1,'C',false,0,'','',true,0,false,true,0,'T',false);                                    
        $this->MultiCell(13,$y, 'CODIGO',1,'C',false,0,'','',true,0,false,true,0,'T',false);         
        $this->MultiCell(30,$y, $desno, 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(15,$y, 'FECHA'."\x0A".'INIDEP/COMPRA', 1,'C',false,0,'','',true,0,false,true,0,'T',false);            
        $this->MultiCell(12,$y, 'COMP 100%', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(12,$y, 'COMP 87%', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(10,$y, 'SALDO'."\x0A".'AÑO'."\x0A".' ANTERIOR', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(10,$y, 'INCORPORACIONES/ALTA', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(13,$y, 'REVALORIZACIONES.RENOVACIONES', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(11,$y, 'AJUSTES', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(11,$y, 'BAJAS', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(12,$y, 'TRANSITO', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(11,$y, 'LEASING', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(12,$y, 'INC. ACTUALIZ/ACUMULADO', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(12,$y, 'INC. ACTUALIZ DEL PERIODO', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(12,$y, 'VALOR ACTUALIZ', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(8,$y, 'VIDA USADA', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(8,$y, 'VIDA RESI', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(10,$y, $depre_acu, 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(10,$y, $actu_acu, 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(10,$y, $depre_ges, 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        //$this->MultiCell(10,$y, 'DEP DEL PERIODO', 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(10,$y, $depre_a, 1,'C',false,0,'','',true,0,false,true,0,'T',false);
        $this->MultiCell(15,$y, 'VAL RESI', 1,'C',false,0,'','',true,0,false,true,0,'T',false);

    }

    function setDatos($datos) {

        $this->datos = $datos;
        //var_dump( $this->datos);exit;
    }

    function  generarReporte()
    {

        $this->AddPage();
        $this->SetMargins(1, 80, 1);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->Ln();

        //variables para la tabla
        $codigo = '';
        $contador=1;

        $this->tablewidths=array(8,13,30,15,12,12,10,10,13,11,11,12,11,12,12,12,8,8,10,10,10,10,15);
        $this->tablealigns=array('C','L','L','C','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');

        foreach($this->datos as $record){

		if($record['tipo'] == 'detalle'){
            if($record['color']=='si'){
                $this->SetFillColor(224, 235, 100);
                $this->SetTextColor(0,100,0);
            }else{
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0);
            }
                $this->SetFont('','',4);
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0);                
                $this->tableborders=array('LB','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR');
                $this->tablenumbers=array(0,0,0,0,2,2,0,0,2,2,2,2,2,2,2,2,0,0,2,2,2,2,2);
                $codigo_1=substr($record['codigo'],0,2);
                $codigo_11=substr($record['codigo'],0,9);
                $RowArray = array(
                    's0'  => $contador,
                    's1' => $record['codigo'],
                    's2' => $record['denominacion'],
                    's3' => $record['fecha_ini_dep'],                    
                    's4' => $record['monto_vigente_orig_100']!=''?$record['monto_vigente_orig_100']:0,
                    's5' => $record['monto_vigente_orig']!=''?$record['monto_vigente_orig']:0,
                    's6' => '',
                    's7' => '',
                    's8' => $record['reval']!=''?$record['reval']:0,
                    's9' => $record['ajust']!=''?$record['ajust']:0,
                    's10' => $record['baja']!=''?$record['baja']:0,
                    's11' => $record['transito']!=''?$record['transito']:0,
                    's12' => $record['leasing']!=''?$record['leasing']:0,
                    's13' => $record['inc_ac_acum']!=''?$record['inc_ac_acum']:0,
                    's14' => $record['val_acu_perido']!=''?$record['val_acu_perido']:0,
                    's15' => $record['monto_actualiz']!=''?$record['monto_actualiz']:0,
                    's16' => ($codigo_1=='01' || $codigo_11 == '11.01.05.')?'-':$record['vida_util_orig'],
                    's17' => ($codigo_1=='01' || $codigo_11 == '11.01.05.')?'-':$record['vida_util'],
                    's18' => $record['depreciacion_acum_gest_ant']!=''?$record['depreciacion_acum_gest_ant']:0,
                    's19' => $record['depreciacion_acum_actualiz_gest_ant']!=''?$record['depreciacion_acum_actualiz_gest_ant']:0,
                    's20' => $record['depreciacion_per']!=''?$record['depreciacion_per']:0,                    
                    's21' => $record['depreciacion_acum']!=''?$record['depreciacion_acum']:0,
                    's22' => $record['monto_vigente']!=''?$record['monto_vigente']:0
                );

                $this->MultiRow($RowArray,true,1);
                $contador ++;
            }else if($record['tipo'] == 'total') {

                $this->tableborders=array('LB','B','B','B','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLB','BLR','BLR','BLR','BLR','BLR','BLR');
                $this->tablenumbers=array(0,0,0,0,2,2,0,0,2,2,2,2,2,2,2,2,0,0,2,2,2,2,2);
                $this->SetFont('','B',4);
                $this->SetFillColor(224, 235, 255);

                $this->SetTextColor(0);
                $RowArray = array(
                    's0'  => '',
                    's1' => 'TOTAL FINAL',
                    's2' => '',
                    's3' => '',
                    's4' => $record['monto_vigente_orig_100']!=''?$record['monto_vigente_orig_100']:0,
                    's5' => $record['monto_vigente_orig']!=''?$record['monto_vigente_orig']:0,
                    's6' => '',
                    's7' => '',
                    's8' => $record['reval']!=''?$record['reval']:0,
                    's9' => $record['ajust']!=''?$record['ajust']:0,
                    's10' => $record['baja']!=''?$record['baja']:0,
                    's11' => $record['transito']!=''?$record['transito']:0,
                    's12' => $record['leasing']!=''?$record['leasing']:0,
                    's13' => $record['inc_ac_acum']!=''?$record['inc_ac_acum']:0,
                    's14' => $record['val_acu_perido']!=''?$record['val_acu_perido']:0,
                    's15' => $record['monto_actualiz']!=''?$record['monto_actualiz']:0,
                    's16' => '',
                    's17' => '',
                    's18' => $record['depreciacion_acum_gest_ant']!=''?$record['depreciacion_acum_gest_ant']:0,
                    's19' => $record['depreciacion_acum_actualiz_gest_ant']!=''?$record['depreciacion_acum_actualiz_gest_ant']:0,
                    's20' => $record['depreciacion_per']!=''?$record['depreciacion_per']:0,                    
                    's21' => $record['depreciacion_acum']!=''?$record['depreciacion_acum']:0,
                    's22' => $record['monto_vigente']!=''?$record['monto_vigente']:0
                );

                $this->MultiRow($RowArray,true,1);
            }

        }

    }
}
?>