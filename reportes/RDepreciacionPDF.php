<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');
class RDepreciacionPDF extends  ReportePDF{
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
        $this->SetMargins(2, 38.5, 1);
        $title = "DETALLE DE DEPRECIACION DE ACTIVOS FIJOS";
        $codigo = $this->datos[0]['codigo'];
        $codigo == "11" && $title = "DETALLE DE AMORTIZACION DE ACTIVOS FIJOS INTANGIBLES";

        $this->SetFont('','B',10);
        $this->Cell(0,5,"BOLIVIANA DE AVIACION",0,1,'C');
        $this->Cell(0,5,$title,0,1,'C');

        $this->SetFont('','B',6);
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
        $this->Cell(0,2,'Formato Reporte Antiguo',0,1,'C');

        $this->SetFont('','B',6);
        $this->Ln(3);
		$descnom=$this->objParam->getParametro('desc_nombre');
		switch ($descnom) {
			case 'desc' :$desno='DESCRIPCIÓN';break;
			case 'nombre' :$desno='DENOMINACIÓN';break;
			case 'ambos':$desno='NOMBRE/DESC.';break;
			default:$desno='DENOMINACIÓN';break;
		}

		$depre_acu = 'DEP. ACUM.';
        $actu_acu  = 'ACT. DEPREC.';
        $depre_ges = 'DEP. GESTION';
		if ($codigo == "11"){
		    $depre_acu = 'AMOR. ACUM.';
		    $actu_acu = 'ACT. AMOR.';
		    $depre_ges = 'AMOR. GESTION';

        }
        //primera linea
        $this->Cell(10,3,'','',0,'C');
        $this->Cell(20,3,'','',0,'C');
        $this->Cell(36,3,'','',0,'C');        
        $this->Cell(15,3,'','',0,'C');

        $this->Cell(18,3,'','',0,'C');
        $this->Cell(18,3,'','',0,'C');
        $this->Cell(20,3,'','',0,'C');
        $this->Cell(20,3,'','',0,'C');



        $this->Cell(22,3,'VIDA','TRL',0,'C');
        $this->Cell(17,3,$depre_acu,'TRL',0,'C');
        $this->Cell(17,3,$actu_acu,'TRL',0,'C');

        $this->Cell(17,3,'','',0,'C');
        $this->Cell(17,3,'','',0,'C');
        $this->Cell(19,3,'','',1,'C');

        //segunda linea
        $this->Cell(10,3,'NUM','TBRL',0,'C');
        $this->Cell(20,3,'CODIGO','TBRL',0,'C');
        $this->Cell(36,3,$desno,'BTRL',0,'C');
        $this->Cell(15,3,'INICIO DEP.','TBRL',0,'C');

        $this->Cell(18,3,'COMPRA (100%)','TBRL',0,'C');
        $this->Cell(18,3,'COMPRA (87%)','TBRL',0,'C');
        $this->Cell(20,3,'INC. X ACTUALIZ.','TBRL',0,'C');
        $this->Cell(20,3,'VALOR ACTUALIZ.','TBRL',0,'C');


        $this->Cell(11,3,'USADA','TBRL',0,'C');
        $this->Cell(11,3,'RESIDUAL','TBRL',0,'C');
        $this->Cell(17,3,'GESTION ANT.','BRL',0,'C');
        $this->Cell(17,3,'GESTION ANT.','BRL',0,'C');

        $this->Cell(17,3,$depre_ges,'TBRL',0,'C');
		//$this->Cell(10,3,'% DEP.G','TBRL',0,'C');		
        $this->Cell(17,3,$depre_acu,'TBRL',0,'C');
        $this->Cell(19,3,'VALOR RESIDUAL','TBRL',0,'C');
		



    }

    function setDatos($datos) {

        $this->datos = $datos;
        //var_dump( $this->datos);exit;
    }

    function  generarReporte()
    {

        $this->AddPage();
        $this->SetMargins(2, 80, 1);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->Ln();




        //variables para la tabla
        $codigo = '';
        $contador=1;

        $this->tablewidths=array(10,20,36,15,18,18,20,20,11,11,17,17,17,17,19);
        $this->tablealigns=array('C','L','L','C','R','R','R','R','R','R','R','R','R','R','R');

        foreach($this->datos as $record){

            if($record['tipo'] == 'clasif') {


                $this->SetFont('','B',6);
                $this->SetFillColor(224, 235, 255);

                $this->SetTextColor(0);

                $this->tableborders=array('LB','B','B','B','BLR','BLR','BLR','BLR','B','B','BLR','BLR','BLR','BLR','BRL');
                $this->tablenumbers=array(0,0,0,0,2,2,2,2,0,0,2,2,2,2,2);
                $RowArray = array(
                    's0'  => '',
                    's1' => $record['codigo'],
                    's2' => $record['denominacion'],
                    's3' => '',
                    's4' => $record['monto_vigente_orig_100']!=''?$record['monto_vigente_orig_100']:0,
                    's5' => $record['monto_vigente_orig']!=''?$record['monto_vigente_orig']:0,
                    's6' => $record['inc_actualiz']!=''?$record['inc_actualiz']:0,
                    's7' => $record['monto_actualiz']!=''?$record['monto_actualiz']:0,
                    's8' => '',
                    's9' => '',
                    's10' => $record['depreciacion_acum_gest_ant']!=''?$record['depreciacion_acum_gest_ant']:0,
                    's11' => $record['depreciacion_acum_actualiz_gest_ant']!=''?$record['depreciacion_acum_actualiz_gest_ant']:0,
                    's12' => $record['depreciacion_per']!=''?$record['depreciacion_per']:0,
                    //'s13' => '',
                    's13' => $record['depreciacion_acum']!=''?$record['depreciacion_acum']:0,
                    's14' => $record['monto_vigente']!=''?$record['monto_vigente']:0
                    
                );

                $this->MultiRow($RowArray,true,1);

            }else if($record['tipo'] == 'detalle'){
                $this->SetFont('','',6);
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0);
                //$fecha_dep =  $record['fecha_ini_dep'] != '' ?date_format(date_create($record['fecha_ini_dep']), 'd/m/Y'):'';
                $this->tableborders=array('LB','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','BLR','RBL');
                $this->tablenumbers=array(0,0,0,0,2,2,2,2,0,0,2,2,2,2,2);
                $codigo_1=substr($record['codigo'],0,2);
                $codigo_11=substr($record['codigo'],0,9);
                $RowArray = array(
                    's0'  => $contador,
                    's1' => $record['codigo'],
                    's2' => $record['denominacion'],
                    's3' => $record['fecha_ini_dep'],
                    's4' => $record['monto_vigente_orig_100']!=''?$record['monto_vigente_orig_100']:0,
                    's5' => $record['monto_vigente_orig']!=''?$record['monto_vigente_orig']:0,
                    's6' => $record['inc_actualiz']!=''?$record['inc_actualiz']:0,
                    's7' => $record['monto_actualiz']!=''?$record['monto_actualiz']:0,
                    's8' => ($codigo_1=='01' || $codigo_11 == '11.01.05.')?'-':$record['vida_util_orig'],
                    's9' => ($codigo_1=='01' || $codigo_11 == '11.01.05.')?'-':$record['vida_util'],
                    's10' => $record['depreciacion_acum_gest_ant']!=''?$record['depreciacion_acum_gest_ant']:0,
                    's11' => $record['depreciacion_acum_actualiz_gest_ant']!=''?$record['depreciacion_acum_actualiz_gest_ant']:0,
                    's12' => $record['depreciacion_per']!=''?$record['depreciacion_per']:0,
                    //'s13' => number_format($record['porce_depre'],2,'.',''),
                    's13' => $record['depreciacion_acum']!=''?$record['depreciacion_acum']:0,
                    's14' => $record['monto_vigente']!=''?$record['monto_vigente']:0                    
                );

                $this->MultiRow($RowArray,true,1);
                $contador ++;
            }else if($record['tipo'] == 'total') {

                $this->tableborders=array('LB','B','B','B','BLR','BLR','BLR','BLR','B','B','BLR','BLR','BLR','BLR','RBL');
                $this->tablenumbers=array(0,0,0,0,2,2,2,2,0,0,2,2,2,2,2);
                $this->SetFont('','B',6);
                $this->SetFillColor(224, 235, 255);

                $this->SetTextColor(0);
                $RowArray = array(
                    's0'  => '',
                    's1' => 'TOTAL FINAL',
                    's2' => '',
                    's3' => '',
                    's4' => $record['monto_vigente_orig_100']!=''?$record['monto_vigente_orig_100']:0,
                    's5' => $record['monto_vigente_orig']!=''?$record['monto_vigente_orig']:0,
                    's6' => $record['inc_actualiz']!=''?$record['inc_actualiz']:0,
                    's7' => $record['monto_actualiz']!=''?$record['monto_actualiz']:0,
                    's8' => '',
                    's9' => '',
                    's10' => $record['depreciacion_acum_gest_ant']!=''?$record['depreciacion_acum_gest_ant']:0,
                    's11' => $record['depreciacion_acum_actualiz_gest_ant']!=''?$record['depreciacion_acum_actualiz_gest_ant']:0,
                    's12' => $record['depreciacion_per']!=''?$record['depreciacion_per']:0,
                    //'s13' => '',
                    's13' => $record['depreciacion_acum']!=''?$record['depreciacion_acum']:0,
                    's14' => $record['monto_vigente']!=''?$record['monto_vigente']:0
                    
                );

                $this->MultiRow($RowArray,true,1);
            }

        }






    }
}
?>