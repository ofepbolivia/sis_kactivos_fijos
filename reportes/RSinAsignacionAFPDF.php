<?php
require_once dirname(__FILE__) . '/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');

class RSinAsignacionAFPDF extends ReportePDF
{
    var $datos;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
    var $sum = 0;

    function Header()
    {
        $this->Ln(3);

        //cabecera del reporte
        $this->Image(dirname(__FILE__) . '/../../lib/imagenes/logos/logo.jpg', 16, 5, 40, 20);
        $this->ln(5);
        $this->SetMargins(12, 40, 2);
        $this->SetFont('', 'B', 10);
        $this->Cell(0, 5, "DEPARTAMENTO ACTIVOS FIJOS", 0, 1, 'C');
        $this->Cell(0, 5, "ACTIVOS FIJOS SIN ASIGNACIÓN", 0, 1, 'C');
        $this->Cell(0, 5, 'Del: ' . $this->objParam->getParametro('fecha_ini') . ' Al ' . $this->objParam->getParametro('fecha_fin'), 0, 1, 'C');

        $this->SetFont('', '', 7);
        $this->ln(5);


        $control = $this->objParam->getParametro('rep_sin_asignacion');
        $this->columnsGrid($control);
    }

    public function columnsGrid($tipo)
    {

        $hiddes = explode(',', $tipo);
        $sacod = '';
        $sades = '';
        $safea = '';
        $sa100 = '';
        $sam87 = '';
        $sauns = '';
        $saprc = '';
        $sac31 = '';


        //widths
        $tam1 = 30;
        $tam2 = 50;
        $tam3 = 20;
        $tam4 = 20;
        $tam5 = 20;
        $tam6 = 40;
        $tam7 = 30;
        $tam8 = 20;


        $num = 0;
        $total = 0;

        for ($i = 0; $i < count($hiddes); $i++) {
            switch ($hiddes[$i]) {
                case 'scod':
                    $sacod = 'cod';
                    break;
                case 'sdes':
                    $sades = 'des';
                    break;
                case 'sfea':
                    $safea = 'fea';
                    break;
                case 's100':
                    $sa100 = '100';
                    break;
                case 'sm87':
                    $sam87 = 'm87';
                    break;
                case 'suns':
                    $sauns = 'uns';
                    break;
                case 'sprc':
                    $saprc = 'prc';
                    break;
                case 'sc31':
                    $sac31 = 'c31';
                    break;

            }
        }

        if ($sacod == '') {
            $tam1 = 0;
        }
        if ($sades == '') {
            $tam2 = 0;
        }
        if ($safea == '') {
            $tam3 = 0;
        }
        if ($sa100 == '') {
            $tam4 = 0;
        }
        if ($sam87 == '') {
            $tam5 = 0;
        }
        if ($sauns == '') {
            $tam6 = 0;
        }
        if ($saprc == '') {
            $tam7 = 0;
        }
        if ($sac31 == '') {
            $tam8 = 0;
        }

        //tomamos los tamanios de las columnas no mostradas y las distribuimos a las otras presentes
        $xpage = 230;//∑ tam^n ai = an
        $cont = 0;
        $resul = $tam1 + $tam2 + $tam3 + $tam4 + $tam5 + $tam6 + $tam7 + $tam8;
        $alca = $xpage - $resul;
        $n = count($hiddes);
        //distribucion de tamanios
        if ($alca > 0) {
            $total = $alca / $n;
            while ($resul < $xpage) {
                $cont += 0.001;
                $resul += 1;
            }
            $total += $cont;
        } else {
            $total = 0;
        }
        $hGlobal = 7;

        $this->SetFontSize(7);
        $this->SetFont('', 'B');

        //$this->Ln(6); no si nesto
        $this->MultiCell(8, $hGlobal, 'Nº', 1, 'C', false, 0, '', '', true, 0, false, true, 0, 'T', false);
        ($sacod == 'cod') ? $this->MultiCell($tam1 + $total, $hGlobal, 'CODIGO', 1, 'C', false, 0, '', '', true, 0, false, true, 0, 'T', false) : '';
        ($sades == 'des') ? $this->MultiCell($tam2 + $total, $hGlobal, 'DESCRIPCION', 1, 'C', false, 0, '', '', true, 0, false, true, 0, 'T', false) : '';
        ($safea == 'fea') ? $this->MultiCell($tam3 + $total, $hGlobal, 'FECHA DE ALTA', 1, 'C', false, 0, '', '', true, 0, false, true, 0, 'T', false) : '';
        ($sa100 == '100') ? $this->MultiCell($tam4 + $total, $hGlobal, 'MONTO 100%', 1, 'C', false, 0, '', '', true, 0, false, true, 0, 'T', false) : '';
        ($sam87 == 'm87') ? $this->MultiCell($tam5 + $total, $hGlobal, 'MONTO 87%', 1, 'C', false, 0, '', '', true, 0, false, true, 0, 'T', false) : '';
        ($sauns == 'uns') ? $this->MultiCell($tam6 + $total, $hGlobal, 'UNIDAD SOLICITANTE', 1, 'C', false, 0, '', '', true, 0, false, true, 0, 'T', false) : '';
        ($saprc == 'prc') ? $this->MultiCell($tam7 + $total, $hGlobal, 'Nº PROCESO COMPRA', 1, 'C', false, 0, '', '', true, 0, false, true, 0, 'T', false) : '';
        ($sac31 == 'c31') ? $this->MultiCell($tam8 + $total, $hGlobal, 'C31', 1, 'C', false, 0, '', '', true, 0, false, true, 0, 'T', false) : '';
    }

    function setDatos($datos)
    {

        $this->datos = $datos;
//        $this->datos2 = $datos2;
//        $this->datos3 = $datos3;
//        $this->datos5 = $datos5;
//        var_dump($this->datos);exit;
    }

    function generarReporte()
    {
        $this->AddPage();
        $this->SetMargins(12, 40, 2);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->Ln();
        //variables para la tabla
        $codigo = '';
        $nombre = '';


        $i = 1;
        $contador = 1;
        $tipo = $this->objParam->getParametro('tipo_reporte');
        $select = $this->objParam->getParametro('rep_sin_asignacion');
        $hiddes = explode(',', $select);

        $sacod = '';
        $sades = '';
        $safea = '';
        $sa100 = '';
        $sam87 = '';
        $sauns = '';
        $saprc = '';
        $sac31 = '';

        $tam1 = 30;
        $tam2 = 50;
        $tam3 = 20;
        $tam4 = 20;
        $tam5 = 20;
        $tam6 = 40;
        $tam7 = 30;
        $tam8 = 20;


        //asigna a cada variable su valor recibido desde la vista
        for ($j = 0; $j < count($hiddes); $j++) {
            switch ($hiddes[$j]) {
                case 'scod':
                    $sacod = 'cod';
                    break;
                case 'sdes':
                    $sades = 'des';
                    break;
                case 'sfea':
                    $safea = 'fea';
                    break;
                case 's100':
                    $sa100 = '100';
                    break;
                case 'sm87':
                    $sam87 = 'm87';
                    break;
                case 'suns':
                    $sauns = 'uns';
                    break;
                case 'sprc':
                    $saprc = 'prc';
                    break;
                case 'sc31':
                    $sac31 = 'c31';
                    break;
            }
        }
        if ($sacod == '') {
            $tam1 = 0;
        }
        if ($sades == '') {
            $tam2 = 0;
        }
        if ($safea == '') {
            $tam3 = 0;
        }
        if ($sa100 == '') {
            $tam4 = 0;
        }
        if ($sam87 == '') {
            $tam5 = 0;
        }
        if ($sauns == '') {
            $tam6 = 0;
        }
        if ($saprc == '') {
            $tam7 = 0;
        }
        if ($sac31 == '') {
            $tam8 = 0;
        }

        $xpage = 230;//∑ tam^n ai = an
        $cont = 0;
        $resul = $tam1 + $tam2 + $tam3 + $tam4 + $tam5 + $tam6 + $tam7 + $tam8;
        $alca = $xpage - $resul;
        $n = count($hiddes);

        if ($alca > 0) {
            $total = $alca / $n;
            while ($resul < $xpage) {
                $cont += 0.001;
                $resul += 1;
            }
            $total += $cont;
        } else {
            $total = 0;
        }

        //arreglo para tablewidths estatica
        $datos = array('t1' => 8,
            'cod' => $tam1 + $total,
            'des' => $tam2 + $total,
            'fea' => $tam3 + $total,
            '100' => $tam4 + $total,
            'm87' => $tam5 + $total,
            'uns' => $tam6 + $total,
            'prc' => $tam7 + $total,
            'c31' => $tam8 + $total);

        $this->tablewidths = $this->filterArray($datos);
        $tablenums0 = array('t1' => 0, 'cod' => 0, 'des' => 0, 'fea' => 0, '100' => 0, 'm87' => 0, 'uns' => 0, 'prc' => 0, 'c31' => 0);  //1
        $tablenums1 = array('t1' => 0, 'cod' => 0, 'des' => 0, 'fea' => 0, '100' => 0, 'm87' => 0, 'uns' => 0, 'prc' => 0, 'c31' => 0);  //2
        $tablenums0Real = $this->filterArray($tablenums0);
        $tablenums1Real = $this->filterArray($tablenums1);
        $this->tablealigns = array('C', 'C', 'L', 'R', 'R', 'R', 'C', 'C', 'C');

        foreach ($this->datos as $record) {
//            var_dump($this->datos);exit;
            $this->SetFont('', '', 7);
            $this->tableborders = array('RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB');
//          $this->tablenumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 2,0);
            $this->tablenumbers = $tablenums0Real;

            $RowArray = array(
                's0' => $i,
                's1' => $record['codigo'],
                's2' => $record['descripcion'],
                's3' => $record['fecha_ini_dep'] == '-' ? '-' : date("d/m/Y", strtotime($record['fecha_ini_dep'])),
                's4' => $record['monto_compra_orig_100'],
                's5' => $record['monto_compra_orig'],
                's6' => $record['nombre_unidad'],
                's7' => $record['tramite_compra'],
                's8' => $record['nro_cbte_asociado']
            );
            if ($sacod == '') {
                unset($RowArray['s1']);
            }
            if ($sades == '') {
                unset($RowArray['s2']);
            }
            if ($safea == '') {
                unset($RowArray['s3']);
            }
            if ($sa100 == '') {
                unset($RowArray['s4']);
            }
            if ($sam87 == '') {
                unset($RowArray['s5']);
            }
            if ($sauns == '') {
                unset($RowArray['s6']);
            }
            if ($saprc == '') {
                unset($RowArray['s7']);
            }
            if ($sac31 == '') {
                unset($RowArray['s8']);
            }
            $this->MultiRow($RowArray);


            $i++;
        }
    }

    function filterArray($table)
    {

        $resp = array();
        $control = $this->objParam->getParametro('rep_sin_asignacion');
        $hiddes = explode(',', $control);
        $sacod = '';
        $sades = '';
        $safea = '';
        $sa100 = '';
        $sam87 = '';
        $sauns = '';
        $saprc = '';
        $sac31 = '';

        //asigna a cada variable su valor recibido desde la vista
        for ($j = 0; $j < count($hiddes); $j++) {
            switch ($hiddes[$j]) {
                case 'scod':
                    $sacod = 'cod';
                    break;
                case 'sdes':
                    $sades = 'des';
                    break;
                case 'sfea':
                    $safea = 'fea';
                    break;
                case 's100':
                    $sa100 = '100';
                    break;
                case 'sm87':
                    $sam87 = 'm87';
                    break;
                case 'suns':
                    $sauns = 'uns';
                    break;
                case 'sprc':
                    $saprc = 'prc';
                    break;
                case 'sc31':
                    $sac31 = 'c31';
                    break;
            }
        }

        $proces = $table;

        foreach ($proces as $key => $value) {
            if ($sacod == '') {
                unset($proces['cod']);
            }
            if ($sades == '') {
                unset($proces['des']);
            }
            if ($safea == '') {
                unset($proces['fea']);
            }
            if ($sa100 == '') {
                unset($proces['100']);
            }
            if ($sam87 == '') {
                unset($proces['m87']);
            }
            if ($sauns == '') {
                unset($proces['uns']);
            }
            if ($saprc == '') {
                unset($proces['prc']);
            }
            if ($sac31 == '') {
                unset($proces['c31']);
            }
        }
        $resp = array();
        foreach ($proces as $value) {
            array_push($resp, $value);
        }
        return $resp;
    } //endBVP
}

?>
