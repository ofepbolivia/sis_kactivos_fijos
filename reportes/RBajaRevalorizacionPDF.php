<?php

class RBajaRevalorizacionPDF extends ReportePDF  {
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
        $this->Write(0, 'BOLIVIANA DE AVIACION', '', 0, 'C', true, 0, false, false, 0);
        $this->Write(0, 'DETALLE DE ACTIVOS REVALORIZADOS CON VALOR 1', '', 0, 'C', true, 0, false, false, 0);


        $this->SetFont('helvetica','B',8);
        $this->Write(0, ' GESTION '.$this->objParam->getParametro('gestion') ,'',0, 'C', true, 0, false, false, 0);
        $this->Write(0,'(Expresado en Bolivianos)', '', 0, 'C', true, 0, false, false,0);
        $this->ln(5);
        $this->SetFont('helvetica','B',7);



        $blackAll = array('LTRB' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $this->SetFillColor(250,250,250, true);
        $this->setTextColor(0,0,0);



        $this->Cell(18, 3, 'Codigo', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(50, 3, 'Descripcion', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(20, 3, 'Fecha Revaluo', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(20, 3, 'Valor Actualiz.', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(20, 3, 'Dep. Acum.', 'LTRB', 0, 'C', true, '', 1, false, 'T', 'C');
        $this->Cell(20, 3, 'Valor Residual', 'LTRB', 1, 'C', true, '', 1, false, 'T', 'C');


    }

    function generarReporte() {
        $this->SetMargins(30,34);
        //$this->setFontSubsetting(false);
        $this->AddPage();
        $this->tablewidths=array(18,50,20,20,20,20);
        $this->tablealigns=array('L','L','L','R','R','R');
        $this->tablenumbers=array(0,0,0,1,1,1);


        $codigo_tipo = "";
        $tipo = "";

        $this->SetFillColor(220,220,220);
        $totales_grupo = array(0,0,0);
        $totales_general = array(0,0,0);


        foreach ($this->objParam->getParametro('datos') as $registro) {

            $arreglo = array_slice($registro,2);

            if ($registro['codigo_tipo'] != $codigo_tipo) {
                if ($codigo_tipo != '') {

                    $this->SetFont('helvetica', 'B', 7);

                    $this->Cell(88, 3, 'Total Grupo ' . $codigo_tipo, '', 0, 'L', false, '', 1, false, 'T', 'C');
                    $border = '';


                    for ($i = 0; $i < count($totales_grupo); $i++) {
                        $this->Cell($this->tablewidths[$i + 3], 3, number_format($totales_grupo[$i], 2), $border, 0, 'R', false, '', 1, false, 'T', 'C');
                        $totales_grupo[$i] = 0;
                    }

                    $this->ln();

                }

                $this->ln();
                $temp_array = array($registro['codigo_tipo'], $registro['tipo'], '', '', '', '');
                $this->tablenumbers = array(0, 0, 0, 0, 0, 0);
                $this->SetFont('helvetica', 'B', 7);
                $this->MultiRow($temp_array, true, 1, array(0, 0, 0), 3);

                $codigo_tipo = $registro['codigo_tipo'];
                $tipo = $registro['tipo'];
            }




            $this->tablenumbers=array(0,0,0,1,1,1);

            $this->SetFont('helvetica', '', 7);
            $this->MultiRow($arreglo, false, 1, array(0, 0, 0), 3);


            $totales_grupo[0] = $totales_grupo[0]  + $arreglo['monto_actualiz'];
            $totales_grupo[1] = $totales_grupo[1]  + $arreglo['depreciacion_acum'];
            $totales_grupo[2] = $totales_grupo[2]  + $arreglo['valor_residual'];


            $totales_general[0] = $totales_general[0] + $arreglo['monto_actualiz'];
            $totales_general[1] = $totales_general[1] + $arreglo['depreciacion_acum'];
            $totales_general[2] = $totales_general[2] + $arreglo['valor_residual'];



        }
        $this->SetFont('helvetica','B',7);

        $this->Cell(88, 3, 'Total Grupo ' . $codigo_tipo, '', 0, 'L', false, '', 1, false, 'T', 'C');
        $border = '';

        for($i = 0 ; $i < count($totales_grupo)  ;$i++ ) {
            $this->Cell($this->tablewidths[$i+3], 3,number_format($totales_grupo[$i],2) , $border, 0, 'R', false, '', 1, false, 'T', 'C');
            $totales_grupo[$i] = 0;
        }

        $this->ln();

        $this->Cell(88, 3, 'Total Final ', '', 0, 'L', true, '', 1, false, 'T', 'C');
        for($i = 0 ; $i < count($totales_general)  ;$i++ ) {
            $this->Cell($this->tablewidths[$i+3], 3,number_format($totales_general[$i],2) , '', 0, 'R', true, '', 1, false, 'T', 'C');
            $totales_general[$i] = 0;
        }




    }
}
?>