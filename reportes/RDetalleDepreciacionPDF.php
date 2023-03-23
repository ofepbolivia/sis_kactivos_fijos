<?php

class RDetalleDepreciacionPDF extends ReportePDF  {
    var $datos ;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;

    function Header(){

        $height = 5;
        $height2 = 40;
        $this->Image(dirname(__FILE__) . '/../../lib' . $_SESSION['_DIR_LOGO'], 275, 2, 30, 15);
        //$this->SetMargins(25,5);
        $this->SetFont('helvetica', 'B', 12);
        //$this->Write(0, 'BOLIVIANA DE AVIACION', '', 0, 'C', true, 0, false, false, 0); //fRnk: comentado
        $this->Write(0, 'DETALLE DEPRECIACION DE '. strtoupper($this->objParam->getParametro('desc_tipo')), '', 0, 'C', true, 0, false, false, 0);
        $a_date = "2016-02-23";
        $gestion = $this->objParam->getParametro('gestion');
        $periodo= $this->objParam->getParametro('periodo');
        if (strlen($periodo) == 1) {
            $periodo = '0'.$periodo;
        }

        $this->SetFont('helvetica','B',8);
        $this->Write(0, ' Al '.date("t/m/Y", strtotime("$gestion-$periodo-01")) ,'',0, 'C', true, 0, false, false, 0);
        $this->Write(0,'(Expresado en Bolivianos)', '', 0, 'C', true, 0, false, false,0);
        $this->ln(5);
        $this->SetFont('helvetica','B',7);



        $blackAll = array('LTRB' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $this->SetFillColor(250,250,250, true);
        $this->setTextColor(0,0,0);

        $this->Cell(18,3,'','',0,'C');
        $this->Cell(50,3,'','',0,'C');
        $this->Cell(15,3,'','',0,'C');
        $this->Cell(20,3,'','',0,'C');
        $this->Cell(20,3,'','',0,'C');
        $this->Cell(20,3,'','',0,'C');
        $this->Cell(20,3,'','',0,'C');
        $this->Cell(26,3,'Vida','LTR',0,'C');
        $this->Cell(20,3,'Dep. Acum.','LTR',0,'C');
        $this->Cell(20,3,'Act. Deprec.','LTR',0,'C');
        $this->Cell(20,3,'','',0,'C');
        $this->Cell(20,3,'','',0,'C');
        $this->Cell(20,3,'','',1,'C');


        $this->Cell(18, 3, 'Codigo', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        if ($this->objParam->getParametro('tipo') == 'consolidado') {
            $this->Cell(50, 3, 'Descripcion', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
            $this->Cell(15, 3, 'Inicio Dep.', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        } else {
            $this->Cell(65, 3, 'Descripcion', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        }
        $this->Cell(20, 3, 'Compra (100%)', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(20, 3, 'Compra (80%)', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(20, 3, 'Inc. x Actualiz.', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(20, 3, 'Valor Actualiz.', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(13, 3, 'Usada', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(13, 3, 'Residual', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(20, 3, 'Gestion Ant.', 'LRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(20, 3, 'Gestion Ant.', 'LRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(20, 3, 'Dep. Gestion', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(20, 3, 'Dep. Acum.', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(20, 3, 'Valor Residual', 'LTRB', 1, 'C', true, '', 1, false, 'T', 'C');


    }

    function generarReporte() {
        $this->SetMargins(30,34);
        //$this->setFontSubsetting(false);
        $this->AddPage();
        $this->tablewidths=array(18,50,15,20,20,20,20,13,13,20,20,20,20,20);
        $this->tablealigns=array('L','L','C','R','R','R','R','R','R','R','R','R','R','R','R');
        $this->tablenumbers=array(0,0,0,1,1,1,1,0,0,1,1,1,1,1);


        $codigo_tipo = "";
        $tipo = "";
        $codigo_subtipo = "";
        $codigo_rama = "";
        $this->SetFillColor(220,220,220);
        $totales_grupo = array(0,0,0,0,0,0,0,0,0,0,0);
        $totales_general = array(0,0,0,0,0,0,0,0,0,0,0);

        foreach ($this->objParam->getParametro('datos') as $registro) {
            $arreglo = array_slice($registro,6);
            if ($registro['codigo_tipo'] != $codigo_tipo) {
                if ($codigo_tipo != '') {

                    $this->SetFont('helvetica', 'B', 7);
                    if ($this->objParam->getParametro('tipo') == 'consolidado') {
                        $this->Cell(83, 3, 'Total Grupo ' . $codigo_tipo, '', 0, 'L', false, '', 1, false, 'T', 'C');
                        $border = '';
                    } else {
                        $this->Cell(18, 3, $codigo_tipo, 'LRTB', 0, 'L', false, '', 1, false, 'T', 'C');
                        $this->Cell(65, 3, $tipo, 'LRTB', 0, 'L', false, '', 1, false, 'T', 'C');
                        $border = 'LRTB';
                    }

                    for ($i = 0; $i < count($totales_grupo); $i++) {
                        $this->Cell($this->tablewidths[$i + 3], 3, number_format($totales_grupo[$i], 2), $border, 0, 'R', false, '', 1, false, 'T', 'C');
                        $totales_grupo[$i] = 0;
                    }

                    $this->ln();

                }
                if ($this->objParam->getParametro('tipo') == 'consolidado') {
                    $this->ln();
                    $temp_array = array($registro['codigo_tipo'], $registro['tipo'], '', '', '', '', '', '', '', '', '', '', '', '');
                    $this->tablenumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
                    $this->SetFont('helvetica', 'B', 7);
                    $this->MultiRow($temp_array, true, 1, array(0, 0, 0), 3);
                }
                $codigo_tipo = $registro['codigo_tipo'];
                $tipo = $registro['tipo'];
            }



            if ($registro['codigo_subtipo'] != $codigo_subtipo && $this->objParam->getParametro('tipo') == 'consolidado') {

                $temp_array = array($registro['codigo_subtipo'],$registro['subtipo'],'','','','','','','','','','','','');
                $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
                $this->SetFont('helvetica','B',7);
                $this->MultiRow($temp_array,true,1,array(0,0,0),3);
                $codigo_subtipo = $registro['codigo_subtipo'];


            }

            if ($registro['codigo_rama'] != $codigo_rama && $this->objParam->getParametro('tipo') == 'consolidado') {

                $temp_array = array($registro['codigo_rama'],$registro['rama'],'','','','','','','','','','','','');
                $this->tablenumbers=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
                $this->SetFont('helvetica','B',7);
                $this->MultiRow($temp_array,true,1,array(0,0,0),3);
                $codigo_rama = $registro['codigo_rama'];

            }
            $this->tablenumbers=array(0,0,0,1,1,1,1,0,0,1,1,1,1,1);
            if ($this->objParam->getParametro('tipo') == 'consolidado') {
                $this->SetFont('helvetica', '', 7);
                $this->MultiRow($arreglo, false, 1, array(0, 0, 0), 3);
            }

            $totales_grupo[0] = $totales_grupo[0]  + $arreglo['importe_100'];
            $totales_grupo[1] = $totales_grupo[1]  + $arreglo['monto_compra'];
            $totales_grupo[2] = $totales_grupo[2]  + $arreglo['actualizacion'];
            $totales_grupo[3] = $totales_grupo[3]  + $arreglo['monto_actualiz'];
            $totales_grupo[4] = $totales_grupo[4]  + $arreglo['vida_usada'];
            $totales_grupo[5] = $totales_grupo[5]  + $arreglo['vida_util'];
            $totales_grupo[6] = $totales_grupo[6]  + $arreglo['depreciacion_acum_gestion_anterior'];
            $totales_grupo[7] = $totales_grupo[7]  + $arreglo['depre_actu_gestion_anterior'];
            $totales_grupo[8] = $totales_grupo[8]  + $arreglo['depreciacion_gestion'];
            $totales_grupo[9] = $totales_grupo[9]  + $arreglo['depreciacion_acum'];
            $totales_grupo[10] = $totales_grupo[10]  + $arreglo['valor_residual'];

            $totales_general[0] = $totales_general[0] + $arreglo['importe_100'];
            $totales_general[1] = $totales_general[1] + $arreglo['monto_compra'];
            $totales_general[2] = $totales_general[2] + $arreglo['actualizacion'];
            $totales_general[3] = $totales_general[3] + $arreglo['monto_actualiz'];
            $totales_general[4] = $totales_general[4] + $arreglo['vida_usada'];
            $totales_general[5] = $totales_general[5] + $arreglo['vida_util'];
            $totales_general[6] = $totales_general[6] + $arreglo['depreciacion_acum_gestion_anterior'];
            $totales_general[7] = $totales_general[7] + $arreglo['depre_actu_gestion_anterior'];
            $totales_general[8] = $totales_general[8] + $arreglo['depreciacion_gestion'];
            $totales_general[9] = $totales_general[9] + $arreglo['depreciacion_acum'];
            $totales_general[10] = $totales_general[10] + $arreglo['valor_residual'];


        }
        $this->SetFont('helvetica','B',7);
        if ($this->objParam->getParametro('tipo') == 'consolidado') {
            $this->Cell(83, 3, 'Total Grupo ' . $codigo_tipo, '', 0, 'L', false, '', 1, false, 'T', 'C');
            $border = '';
        } else {
            $this->Cell(18, 3, $codigo_tipo, 'LRTB', 0, 'L', false, '', 1, false, 'T', 'C');
            $this->Cell(65, 3, $tipo, 'LRTB', 0, 'L', false, '', 1, false, 'T', 'C');
            $border = 'LRTB';
        }
        for($i = 0 ; $i < count($totales_grupo)  ;$i++ ) {
            $this->Cell($this->tablewidths[$i+3], 3,number_format($totales_grupo[$i],2) , $border, 0, 'R', false, '', 1, false, 'T', 'C');
            $totales_grupo[$i] = 0;
        }

        $this->ln();

        $this->Cell(83, 3, 'Total Final ', '', 0, 'L', true, '', 1, false, 'T', 'C');
        for($i = 0 ; $i < count($totales_general)  ;$i++ ) {
            $this->Cell($this->tablewidths[$i+3], 3,number_format($totales_general[$i],2) , '', 0, 'R', true, '', 1, false, 'T', 'C');
            $totales_general[$i] = 0;
        }




    }
}
?>