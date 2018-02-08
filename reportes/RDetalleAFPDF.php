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
        $this->Cell(10,3,'NUM','TRL',0,'C');
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
        $this->Cell(30,3,'','BRL',0,'C');

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
        $this->tablewidths=array(10,23,50,15,15,15,15,15,15,15,15,30,30);
        $this->tablealigns=array('C','L','L','C','C','C','R','R','R','C','L','L');

        $tipo = $this->objParam->getParametro('tipo_reporte');
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
                    $this->tablenumbers=array(0,0,0,0,0,0,2,2,2,0,0,0,0);
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
                        $this->MultiRow($RowArray,true,1);
                        $total_grupo_100 = 0;
                        $total_grupo_87 = 0;
                        $total_grupo_actual = 0;
                    }
                }


                $this->SetFillColor(79, 91, 147);

                $this->SetTextColor(0);
                $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','B','B','RB');
                $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,0,0,0,0);
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

                $this->MultiRow($RowArray,true,1);
                if($record['nivel'] == 0){
                    $codigo = $record['codigo_completo'];
                }
            }else{

                $this->SetFont('','',6);
                $this->tableborders=array('RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB','RLB');
                $this->tablenumbers=array(0,0,0,0,0,0,2,2,2,0,0,0,0);
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
        $this->tablenumbers=array(0,0,0,0,0,0,2,2,2,0,0,0,0);
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
        $this->MultiRow($RowArray,true,1);

        //$this->SetFillColor(79, 91, 147);
        $this->tableborders=array('LB','B','B','B','B','B','B','B','B','B','B','B','RB');
        $this->tablenumbers=array(0,0,0,0,0,0,2,2,2,0,0,0,0);
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

        $this->MultiRow($RowArray,true,1);


    }
}
?>