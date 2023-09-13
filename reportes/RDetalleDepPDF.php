<?php
//fRnk: nuevo reporte Depreciación de Activo Fijo PDF HR01166
require_once dirname(__FILE__) . '/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');

class RDetalleDepPDF extends ReportePDF
{
    var $dataMaster;
    var $datos_detalle;
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

    function datosHeader($maestro, $detalle)
    {
        $this->ancho_hoja = $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - 10;
        $this->datos_detalle = $detalle;
        $this->dataMaster = $maestro;
        $this->numPag = 1;
        $this->SetMargins(5, 37, 5);
    }

    function Header()
    {
        $date = new DateTime($this->objParam->getParametro('fecha_hasta'));
        $f = explode('-', $date->format('Y-m-d'));
        $mes = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
        $fechas = 'Al ' . $f[2] . ' de ' . $mes[(int)$f[1]] . ' de ' . $f[0];

        $content = '<table border="1" cellpadding="1" style="font-size: 10px;">
            <tr>
                <td style="width: 23%; color: #444444;" rowspan="2">
                    &nbsp;<img  style="width: 150px;" src="./../../../lib/' . $_SESSION['_DIR_LOGO'] . '" alt="Logo">
                </td>		
                <td style="width: 54%; color: #444444;text-align: center" rowspan="2">
                   <h4 style="font-size: 12px">CUADRO DE DEPRECIACIÓN Y ACTUALIZACIÓN DE ACTIVOS FIJOS</h4>
                   <b style="font-size: 10px">' . $fechas . '</b><br/>
                   <span>(Expresado en bolivianos)</span>
                </td>
                <td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Fecha:</b> ' . date('d/m/y h:i:s A') . '<br><br></td>
            </tr>
            <tr>
                <td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Usuario:</b> ' . $_SESSION['_LOGIN'] . '</td>
            </tr>
        </table>';
        $this->writeHTMLCell(0, 10, 2, 4, $content, 0, 0, 0, true, 'L', true);
    }

    function generarReporte()
    {
        $this->AddPage();
        $this->SetFontSize(7);
        $html = '<table border="1" cellpadding="1">';
        $html .= '<tr style="background-color: #c5d9f1; text-align: center; font-weight: bolder"><td style="width: 4%;">Gestión</td><td style="width: 4%;">Tipo</td>
                <td>Clasificación</td><td style="width: 5%;">Fecha Inicio</td>
                <td>Código</td><td style="width: 20%;">Detalle</td><td style="width: 4%;">Vida Util</td><td style="width: 4%;">Vida Restante</td><td>Valor Historico</td>
                <td>Valor Actualizado</td><td>Depreciación Acumulada Actualizada</td><td>Depreciación Anual</td>
                <td>Valor Vigente</td><td>Ajuste del Valor del Activo</td><td style="width: 6%;">Ajuste de Depreciación</td></tr>';
        $sw = true;
        $datos = $this->datos_detalle;
        $subt_j = $subt_k = $subt_l = $subt_m = $subt_n = $subt_o = $subt_p = 0;
        $totg_j = $totg_k = $totg_l = $totg_m = $totg_n = $totg_o = $totg_p = 0;
        foreach ($datos as $value) {
            if ($sw) {
                $sw = false;
            } else {
                if ($tmp_rec['id_clasificacion_raiz'] != $value['id_clasificacion_raiz'] || $tmp_rec['tipo'] != $value['tipo'] || $tmp_rec['gestion_final'] != $value['gestion_final'] || $tmp_rec['id_moneda_dep'] != $value['id_moneda_dep']) {
                    $html .= '<tr style="text-align: right; background-color: #aeffc2"><td colspan="8"></td>';
                    $html .= '<td>' . number_format($subt_j, 2, ',', '.') . '</td>
                            <td>' . number_format($subt_k, 2, ',', '.') . '</td>
                            <td>' . number_format($subt_l, 2, ',', '.') . '</td>
                            <td>' . number_format($subt_m, 2, ',', '.') . '</td>
                            <td>' . number_format($subt_n, 2, ',', '.') . '</td>
                            <td>' . number_format($subt_o, 2, ',', '.') . '</td>
                            <td>' . number_format($subt_p, 2, ',', '.') . '</td></tr>';
                    $totg_j += $subt_j;
                    $totg_k += $subt_k;
                    $totg_l += $subt_l;
                    $totg_m += $subt_m;
                    $totg_n += $subt_n;
                    $totg_o += $subt_o;
                    $totg_p += $subt_p;
                    $subt_j = $subt_k = $subt_l = $subt_m = $subt_n = $subt_o = $subt_p = 0;
                }

                if ($tmp_rec['gestion_final'] != $value['gestion_final'] || $tmp_rec['id_moneda_dep'] != $value['id_moneda_dep']) {
                    $html .= '<tr style="text-align: right;background-color: #ffff99"><td colspan="8">TOTAL AÑO ' . $tmp_rec['gestion_final'] . '</td>';
                    $html .= '<td>' . number_format($totg_j, 2, ',', '.') . '</td>
                            <td>' . number_format($totg_k, 2, ',', '.') . '</td>
                            <td>' . number_format($totg_l, 2, ',', '.') . '</td>
                            <td>' . number_format($totg_m, 2, ',', '.') . '</td>
                            <td>' . number_format($totg_n, 2, ',', '.') . '</td>
                            <td>' . number_format($totg_o, 2, ',', '.') . '</td>
                            <td>' . number_format($totg_p, 2, ',', '.') . '</td></tr>';
                    $totg_j = $totg_k = $totg_l = $totg_m = $totg_n = $totg_o = $totg_p = 0;
                }
            }
            $subt_j += $value['monto_vigente_orig'];
            $subt_k += $value['monto_actualiz_final'];
            $subt_l += $value['depreciacion_acum_final'];
            $subt_m += $value['depreciacion_per_final'];
            $subt_n += $value['monto_vigente_final'];
            $subt_o += $value['aitb_activo'];
            $subt_p += floatval($value['aitb_depreciacion_acumulada']);
            $html .= $this->imprimirFila($value);
            if (!$sw) {
                $tmp_rec = $value;
            }
        }
        $html .= '<tr style="text-align: right; background-color: #aeffc2"><td colspan="8"></td>';
        $html .= '<td>' . number_format($subt_j, 2, ',', '.') . '</td>
                <td>' . number_format($subt_k, 2, ',', '.') . '</td>
                <td>' . number_format($subt_l, 2, ',', '.') . '</td>
                <td>' . number_format($subt_m, 2, ',', '.') . '</td>
                <td>' . number_format($subt_n, 2, ',', '.') . '</td>
                <td>' . number_format($subt_o, 2, ',', '.') . '</td>
                <td>' . number_format($subt_p, 2, ',', '.') . '</td></tr>';
        $totg_j += $subt_j;
        $totg_k += $subt_k;
        $totg_l += $subt_l;
        $totg_m += $subt_m;
        $totg_n += $subt_n;
        $totg_o += $subt_o;
        $totg_p += $subt_p;
        $html .= '<tr style="text-align: right;background-color: #ffff99"><td colspan="8">TOTAL AÑO ' . $tmp_rec['gestion_final'] . '</td>';
        $html .= '<td>' . number_format($totg_j, 2, ',', '.') . '</td>
                <td>' . number_format($totg_k, 2, ',', '.') . '</td>
                <td>' . number_format($totg_l, 2, ',', '.') . '</td>
                <td>' . number_format($totg_m, 2, ',', '.') . '</td>
                <td>' . number_format($totg_n, 2, ',', '.') . '</td>
                <td>' . number_format($totg_o, 2, ',', '.') . '</td>
                <td>' . number_format($totg_p, 2, ',', '.') . '</td></tr>';
        $html .= '</table>';
        $this->writeHTML($html, false, false, true, false, '');
        $this->Ln(3);
    }

    function imprimirFila($value)
    {
        $html = '<tr>';
        $html .= '<td>' . $value['gestion_final'] . '</td>';
        $html .= '<td>' . $value['tipo'] . '</td>';
        $html .= '<td>' . $value['nombre_raiz'] . '</td>';
        $html .= '<td>' . date("d/m/Y", strtotime($value['fecha_ini_dep'])) . '</td>';
        $html .= '<td>' . $value['codigo'] . '</td>';
        $html .= '<td>' . $value['descripcion'] . '</td>';
        $html .= '<td style="text-align: right">' . number_format($value['vida_util_orig'], 2, ',', '.') . '</td>';
        $html .= '<td style="text-align: right">' . number_format($value['vida_util_final'], 2, ',', '.') . '</td>';
        $html .= '<td style="text-align: right">' . number_format($value['monto_vigente_orig'], 2, ',', '.') . '</td>';
        $html .= '<td style="text-align: right">' . number_format($value['monto_actualiz_final'], 2, ',', '.') . '</td>';
        $html .= '<td style="text-align: right">' . number_format($value['depreciacion_acum_final'], 2, ',', '.') . '</td>';
        $html .= '<td style="text-align: right">' . number_format($value['depreciacion_per_final'], 2, ',', '.') . '</td>';
        $html .= '<td style="text-align: right">' . number_format($value['monto_vigente_final'], 2, ',', '.') . '</td>';
        $html .= '<td style="text-align: right">' . number_format($value['aitb_activo'], 2, ',', '.') . '</td>';
        $html .= '<td style="text-align: right">' . number_format($value['aitb_depreciacion_acumulada'], 2, ',', '.') . '</td>';
        $html .= '</tr>';
        return $html;
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