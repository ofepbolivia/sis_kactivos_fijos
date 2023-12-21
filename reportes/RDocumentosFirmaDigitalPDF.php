<?php
//fRnk: nuevo reporte Documentos con Firma Digital PDF HR01318
require_once dirname(__FILE__) . '/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');

class RDocumentosFirmaDigitalPDF extends ReportePDF
{
    var $desde;
    var $hasta;
    var $datos_detalle;
    var $titulo;
    var $ancho_hoja;
    var $numPag;
    public $url_archivo;
    var $datos_titulo;
    var $total;
    var $datos_entidad;
    var $datos_periodo;
    var $ult_codigo_partida;
    var $ult_concepto;

    function getDataSource()
    {
        return $this->datos_detalle;
    }

    function datosHeader($detalle, $fecha_desde, $fecha_hasta, $titulo)
    {
        $this->ancho_hoja = $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - 10;
        $this->datos_detalle = $detalle;
        $this->desde = $fecha_desde;
        $this->hasta = $fecha_hasta;
        $this->titulo = $titulo;
        $this->numPag = 1;
        $this->SetMargins(5, 37, 5);
    }

    function Header()
    {
        $fechas = 'Desde: ' . $this->desde . ' Hasta: ' . $this->hasta;
        $content = '<table border="1" cellpadding="1" style="font-size: 11px;">
            <tr>
                <td style="width: 23%; color: #444444;" rowspan="2">
                    &nbsp;<img  style="width: 130px;" src="./../../../lib/' . $_SESSION['_DIR_LOGO'] . '" alt="Logo">
                </td>		
                <td style="width: 54%; color: #444444;text-align: center" rowspan="2">
                   <h4 style="font-size: 13px">' . mb_strtoupper($this->titulo, 'UTF-8') . '</h4>
                   <span style="font-size: 11px">' . $fechas . '</span>
                </td>
                <td style="width: 23%; color: #444444; text-align: left;height: 30px;">&nbsp;&nbsp;<b>Fecha:</b> ' . date('d/m/y h:i:s A') . '<br></td>
            </tr>
            <tr>
                <td style="width: 23%; color: #444444; text-align: left;height: 15px">&nbsp;&nbsp;<b>Página:</b> ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages() . '</td>
            </tr>
        </table>';
        $this->writeHTMLCell(0, 10, 5, 4, $content, 0, 0, 0, true, 'L', true);
        $this->Ln(1);
        $html = '<table border="1" cellpadding="1">';
        $html .= '<tr style="background-color: #c5d9f1; text-align: center; font-weight: bold;font-size: 10px">
                <td style="width: 5%;">Nro.</td><td style="width: 20%;">Tipo de Proceso</td>
                <td style="width: 20%;">Nro. de Trámite</td><td style="width: 20%;">Estado Trámite</td>
                <td style="width: 15%;">Fecha Firma</td><td style="width: 20%;">Usuario Firma</td></tr>';
        $html .= '</table>';
        $this->writeHTMLCell(0, 10, 5, 33, $html, 0, 1, 0, false, 'L', true);
    }

    function generarReporte()
    {
        $this->AddPage();
        $this->SetFontSize(8);
        if (count($this->datos_detalle) > 0) {
            $html = '<table border="1" cellpadding="1">';
            $i = 1;
            foreach ($this->datos_detalle as $value) {
                $html .= '<tr>';
                $html .= '<td style="width: 5%;text-align: center">' . $i . '</td>';
                $html .= '<td style="width: 20%;">' . $value['tipo_proceso'] . '</td>';
                $html .= '<td style="width: 20%;text-align: center">' . $value['nro_tramite'] . '</td>';
                $html .= '<td style="width: 20%;text-align: center">' . $value['estado_firma'] . '</td>';
                $html .= '<td style="width: 15%;">' . $value['fecha_firma'] . '</td>';
                $html .= '<td style="width: 20%;">' . $value['usuario_firma'] . '</td>';
                $html .= '</tr>';
                $i++;
            }
            $html .= '</table>';
        } else {
            $html = '<p style="text-align: center"><br>No hay resultados para el rango de fechas seleccionado.</p>';
        }
        $this->writeHTML($html, false, false, true, false, '');
        $this->Ln(3);
    }

    function Footer()
    {
        $this->setY(-15);
        $ormargins = $this->getOriginalMargins();
        $this->SetTextColor(0, 0, 0);
        $line_width = 0.85 / $this->getScaleFactor();
        $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $ancho = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right']) / 3);
        $this->Ln(2);
        $cur_y = $this->GetY();
        $this->Cell($ancho, 0, 'Usuario: ' . $_SESSION['_LOGIN'], '', 0, 'L');
        $pagenumtxt = '';//'Página'.' '.$this->getAliasNumPage().' de '.$this->getAliasNbPages();
        $this->Cell($ancho, 0, $pagenumtxt, '', 0, 'C');
        $this->Cell($ancho, 0, $_SESSION['_REP_NOMBRE_SISTEMA'], '', 0, 'R');
        $this->Ln();
        $fecha_rep = date("d-m-Y H:i:s");
        $this->Cell($ancho, 0, "Fecha : " . $fecha_rep, '', 0, 'L');
        $this->Ln($line_width);
        $this->Ln();
        $barcode = $this->getBarcode();
        $style = array(
            'position' => 'R',
            'align' => $this->rtl ? 'R' : 'L',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'padding' => 0,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false,
            'text' => false,
        );
        $this->write1DBarcode($barcode, 'C128B', $ancho * 2, $cur_y + $line_width + 5, '', (($this->getFooterMargin() / 3) - $line_width), 0.3, $style, '');
    }
}

?>