<?php
//fRnk: nuevo reporte HR01341

class RMovimientoMotivos extends ReportePDF
{
    var $dataMaster;
    var $datos_detalle;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $posY;

    function getDataSource()
    {
        return $this->datos_detalle;
    }

    function datosHeader($maestro)
    {
        $this->ancho_hoja = $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - 10;
        $this->dataMaster = $maestro;
    }

    function Header()
    {
        $content = '<table border="0.5" cellpadding="1" style="font-size: 11px">
            <tr>
                <td style="width: 23%; color: #444444;" rowspan="2">
                    &nbsp;<img  style="width: 120px;" src="./../../../lib/' . $_SESSION['_DIR_LOGO'] . '" alt="Logo">
                </td>		
                <td style="width: 52%; color: #444444;text-align: center" rowspan="2">
                   <h1 style="font-size: 16px">Tipos de Movimientos</h1>
                </td>
                <td style="width: 25%; color: #444444; text-align: left;height: 30px">&nbsp;&nbsp;<b>Revisión:</b> 1</td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Página:</b> ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages() . '</td>
            </tr>
        </table>';
        $this->writeHTML($content, false, false, true, false, '');
    }

    function generarReporte()
    {
        $this->setFontSubsetting(false);
        $this->AddPage();
        //$this->SetMargins(5, 60, 5);
        $this->SetFontSize(7);
        $html = '<table border="0.5" cellpadding="2" cellspacing="0">';
        $html .= '<tr style="background-color: #cccccc;font-size: 10px;text-align: center">
                    <td width="5%"><b>Nro.</b></td>
                    <td width="8%"><b>Código</b></td>
                    <td width="27%"><b>Descripción</b></td>
                    <td width="30%"><b>Motivo de uso</b></td>
                    <td width="10%"><b>Código Mov. Motivo</b></td>
                    <td width="10%"><b>Creado por</b></td>
                    <td width="10%"><b>Fecha creación</b></td></tr>';
        foreach ($this->dataMaster as $row) {
            $html .= '<tr>';
            $html .= '<td>' . $row['nro'] . '</td>';
            $html .= '<td>' . $row['codigo_tipomov'] . '</td>';
            $html .= '<td>' . $row['descripcion_tipomov'] . '</td>';
            $html .= '<td>' . $row['descripcion_motivo'] . '</td>';
            $html .= '<td>' . $row['codigo_motivo'] . '</td>';
            $html .= '<td>' . $row['creado_por'] . '</td>';
            $html .= '<td>' . $row['fecha_creacion'] . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        $this->writeHTML($html, false, false, true, false, '');
        $this->Ln(10);
    }

    function Footer()
    {
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
        $this->Cell($ancho, 0, 'Usuario: ' . $_SESSION['_LOGIN'], '', 0, 'L');
        $pagenumtxt = 'Página' . ' ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages();
        $this->Cell($ancho, 0, $pagenumtxt, '', 0, 'C');
        $this->Cell($ancho, 0, $_SESSION['_REP_NOMBRE_SISTEMA'], '', 0, 'R');
        $this->Ln();
        $fecha_rep = date("d-m-Y H:i:s");
        $this->Cell($ancho, 0, "Fecha : " . $fecha_rep, '', 0, 'L');
        $this->Ln($line_width);
        $this->Ln();
    }
}

?>