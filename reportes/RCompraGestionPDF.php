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
        $this->Cell(10,3,'NUM','TRL',0,'C');
        $this->Cell(18,3,'CODIGO','TRL',0,'C');
        $this->Cell(57,3,'DESCRIPCIÓN','TRL',0,'C');
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
        $this->Cell(17,3,'87%','BRL',0,'C');

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
        $cont_87 = 0;
        $cont_100 = 0;
        $contador = 0;
        $total_general_87 = 0;
        $total_general_100 = 0;

        $i=1;

        $this->tablewidths=array(10,18,57,13,13,13,15,14,14,17,17);
        $this->tablealigns=array('C','L','L','C','C','C','C','C','C','R','R');


        foreach($this->datos as $record){

            $this->SetFont('','B',6);
            if($record['nivel'] == 0 || $record['nivel'] == 1){

                if($codigo != '' && ($record['nivel'] == 0 || $record['nivel'] == 1) && $cont_87>0){
                    $total_general_87 = $total_general_87 + $cont_87;
                    $total_general_100 = $total_general_100 + $cont_100;
                    $this->SetFillColor(79, 91, 147);

                    $this->SetTextColor(0);
                    $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','RB');
                    $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,2,2);
                    $RowArray = array(
                        's0'  => '',
                        's1' => '',
                        's2' => 'Total Parcial',
                        's3' => '',
                        's4' => '',
                        's5' => '',
                        's6' => '',
                        's7' => '',
                        's8' => '',
                        's9' => $cont_100,
                        's10' => $cont_87
                    );

                    $this->MultiRow($RowArray,true,1);
                    $cont_100 = 0;
                    $cont_87 = 0;
                }

                $this->SetFillColor(224, 235, 255);

                $this->SetTextColor(0);
                $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','RB');
                $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,0,0);
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
                    's10' => ''
                );

                $this->MultiRow($RowArray,true,1);

            }else{


                $this->tableborders=array('RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB');
                $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,2,2);
                $RowArray = array(
                    's0'  => $record['nivel']==2?$i:'',
                    's1' => $record['nivel']==2?$record['codigo_af']:$record['camino'],
                    's2' => $record['nivel']==2?$record['denominacion']:$record['nombre'],
                    's3' => $record['fecha_compra'] == '-'?'-':date("d/m/Y",strtotime($record['fecha_compra'])),
                    's4' => $record['nro_cbte_asociado'],
                    's5' => $record['fecha_cbte_asociado'] == '-'?'-':date("d/m/Y",strtotime($record['fecha_cbte_asociado'])),
                    's6' => $record['fecha_ini_dep'] == '-'?'-':date("d/m/Y",strtotime($record['fecha_ini_dep'])),
                    's7' => $record['vida_util_original'],
                    's8' => '-',
                    's9' => $record['monto_compra_orig_100'],
                    's10' => $record['monto_compra_orig']
                );

                $this->MultiRow($RowArray);

                $i++;
                $cont_100 = $cont_100 + $record['monto_compra_orig_100'];
                $cont_87  = $cont_87+ $record['monto_compra_orig'];
                $codigo = $record['codigo_completo'];
            }

        }

        $total_general_87 = $total_general_87 + $cont_87;
        $total_general_100 = $total_general_100 + $cont_100;
        $this->SetFillColor(79, 91, 147);

        $this->SetTextColor(0);
        $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','RB');
        $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,2,2);
        $RowArray = array(
            's0'  => '',
            's1' => '',
            's2' => 'Total Parcial',
            's3' => '',
            's4' => '',
            's5' => '',
            's6' => '',
            's7' => '',
            's8' => '',
            's9' => $cont_100,
            's10' => $cont_87
        );

        $this->MultiRow($RowArray,true,1);

        $this->SetFillColor(79, 91, 147);
        $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','RB');
        $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,2,2);
        $RowArray = array(
            's0'  => '',
            's1' => '',
            's2' => 'Totales',
            's3' => '',
            's4' => '',
            's5' => '',
            's6' => '',
            's7' => '',
            's8' => '',
            's9' => $total_general_100,
            's10' => $total_general_87
        );

        $this->MultiRow($RowArray,true,1);


    }

    function basico($numero) {
        $valor = array ('Uno','Dos','Tres','Cuatro','Cinco','Seis','Siete','Ocho',
            'Nueve','Diez','Once','Doce','Trece','Catorce','Quince','Dieciséis','Diecisiete',
            'Dieciocho','Diecinueve','Veinte','Veintiuno','Veintidós','Veintitrés','Veinticuatro','Veinticinco',
            'Veintiséis','Veintisiete','Veintiocho','Veintinueve');
        return $valor[$numero - 1];
    }

    function decenas($n) {
        $decenas = array (30=>'Treinta',40=>'Cuarenta',50=>'Cincuenta',60=>'Sesenta',
            70=>'Setenta',80=>'Ochenta',90=>'Noventa');
        if( $n <= 29) return $this->basico($n);
        $x = $n % 10;
        if ( $x == 0 ) {
            return $decenas[$n];
        } else
            return $decenas[$n - $x].' y '. $this->basico($x);
    }

    function centenas($n) {
        $cientos = array (100 =>'Cien',200 =>'Doscientos',300=>'Trecientos',
            400=>'Cuatrocientos', 500=>'Quinientos',600=>'Seiscientos',
            700=>'Setecientos',800=>'Ochocientos', 900 =>'Novecientos');
        if( $n >= 100) {
            if ( $n % 100 == 0 ) {
                return $cientos[$n];
            } else {
                $u = (int) substr($n,0,1);
                $d = (int) substr($n,1,2);
                return
                    (($u == 1)?'Ciento':$cientos[$u*100]).' '.$this->decenas($d);
            }
        } else
            return $this->decenas($n);
    }

    function miles($n) {
        if($n > 999) {
            if( $n == 1000) {return 'Mil';}
            else {
                $l = strlen($n);
                $c = (int)substr($n,0,$l-3);
                $x = (int)substr($n,-3);
                if($c == 1) {$cadena = 'Mil '.$this->centenas($x);}
                else if($x != 0) {$cadena = $this->centenas($c).' Mil '.$this->centenas($x);}
                else $cadena = $this->centenas($c). ' Mil';
                return $cadena;
            }
        } else return $this->centenas($n);
    }

    function millones($n) {
        if($n == 1000000) {return 'Un Millón';}
        else {
            $l = strlen($n);
            $c = (int)substr($n,0,$l-6);
            $x = (int)substr($n,-6);
            if($c == 1) {
                $cadena = ' Millón ';
            } else {
                $cadena = ' Millones ';
            }
            return $this->miles($c).$cadena.(($x > 0)?$this->miles($x):'');
        }
    }
    function convertir($n) {
        switch (true) {
            case ( $n >= 1 && $n <= 29) : return $this->basico($n); break;
            case ( $n >= 30 && $n < 100) : return $this->decenas($n); break;
            case ( $n >= 100 && $n < 1000) : return $this->centenas($n); break;
            case ($n >= 1000 && $n <= 999999): return $this->miles($n); break;
            case ($n >= 1000000): return $this->millones($n);
        }
    }

    function generarImagen($nom, $car, $ofi){
        $cadena_qr = 'Nombre: '.$nom. "\n" . 'Cargo: '.$car."\n".'Oficina: '.$ofi ;
        $barcodeobj = new TCPDF2DBarcode($cadena_qr, 'QRCODE,M');
        $png = $barcodeobj->getBarcodePngData($w = 8, $h = 8, $color = array(0, 0, 0));
        $im = imagecreatefromstring($png);
        if ($im !== false) {
            header('Content-Type: image/png');
            imagepng($im, dirname(__FILE__) . "/../../reportes_generados/" . $nom . ".png");
            imagedestroy($im);

        } else {
            echo 'A ocurrido un Error.';
        }
        $url_archivo = dirname(__FILE__) . "/../../reportes_generados/" . $nom . ".png";

        return $url_archivo;
    }

}
?>