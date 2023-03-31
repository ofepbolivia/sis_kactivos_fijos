<?php

class RActiDepaPFunFPDF extends ReportePDF
{
    //fRnk: modificado todo el reporte
    var $dataMaster;
    var $datos_detalle;
    var $ancho_hoja;
    var $oficina;
    var $tipo;

    function getDataSource()
    {
        return $this->datos_detalle;
    }

    function setDatos($data)
    {
        $this->ancho_hoja = $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - 10;
        $this->dataMaster = $data[0];
        $this->datos_detalle = $data;
        $this->SetMargins(2, 45, 3);
    }

    function Header()
    {
        $this->SetMargins(2, 40, 2);
        $content = '<table border="1" cellpadding="1" style="font-size: 10px;">
            <tr>
                <td style="width: 23%; color: #444444;" rowspan="2">
                    &nbsp;<img  style="width: 150px;" src="./../../../lib/' . $_SESSION['_DIR_LOGO'] . '" alt="Logo">
                </td>		
                <td style="width: 54%; color: #444444;text-align: center" rowspan="2">
                   <h4 style="font-size: 12px">DEPARTAMENTO ACTIVOS FIJOS</h4>
                   <b style="font-size: 10px">ACTIVOS FIJOS POR DEPÓSITO</b>
                </td>
                <td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Fecha:</b> ' . date('d/m/y h:i:s A') . '</td>
            </tr>
            <tr>
                <td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Usuario:</b> ' . $_SESSION['_LOGIN'] . '</td>
            </tr>
        </table>';
        $this->writeHTMLCell(0, 10, 2, 4, $content, 0, 0, 0, true, 'L', true);
        $this->Ln(24);
        $this->fieldsHeader();
    }

    public function fieldsHeader()
    {
        $html = '<table>
            <tr><td style="width: 17%;"><b>RESPONSABLE:</b></td><td>' . $this->dataMaster['encargado'] . '</td></tr>
            <tr><td><b>DEPÓSITO</b></td><td>' . $this->dataMaster['almacen'] . '</td></tr></table><br><br>';
        $this->writeHTML($html, false, false, true, false, '');
    }

    function generarReporte()
    {
        $this->AddPage();
        $html = '<table border="1" cellpadding="2" style="font-size: 11px"><tr>
                <td style="text-align: center;width: 4%"><b>N°</b></td>
                <td style="text-align: center;width: 8%"><b>CÓDIGO</b></td>
                <td style="text-align: center;width: 20%"><b>NOMBRE</b></td>
                <td style="text-align: center;width: 25%"><b>DESCRIPCIÓN</b></td>
                <td style="text-align: center;width: 8%"><b>ESTADO<br>FUNCIONAL</b></td>
                <td style="text-align: center;width: 15%"><b>FECHA DE INGRESO<br>DE DEPÓSITO</b></td>
                <td style="text-align: center;width: 20%"><b>UBICACIÓN</b></td></tr>';
        $i = 1;
        foreach ($this->getDataSource() as $datarow) {
            $fecha_ing = empty($datarow['fecha_mov']) ? '' : date("d/m/Y", strtotime($datarow['fecha_mov']));
            $html .= '<tr>';
            $html .= '<td style="text-align: center">' . $i . '</td>';
            $html .= '<td style="text-align: center">' . $datarow['codigo'] . '</td>';
            $html .= '<td>' . $datarow['denominacion'] . '</td>';
            $html .= '<td>' . $datarow['descripcion'] . '</td>';
            $html .= '<td style="text-align: center">' . $datarow['cat_desc'] . '</td>';
            $html .= '<td style="text-align: center">' . $fecha_ing . '</td>';
            $html .= '<td>' . $datarow['ubicacion'] . '</td>';
            $html .= '</tr>';
            $i++;
        }
        $html .= '</table>';
        $this->writeHTML($html, false, false, true, false, '');
    }
}

?>
