<?php

class RAFAsignadosFunPDF extends ReportePDF
{
    //fRnk: HR00763
    private $dataMaster;
    private $datos_detalle;
    private $ancho_hoja;
    private $fecha_ini;
    private $fecha_fin;
    private $funcionario;
    private $tipo;

    function getDataSource()
    {
        return $this->datos_detalle;
    }

    function setDatos($data, $fecha_ini, $fecha_fin, $funcionario, $tipo)
    {
        $this->ancho_hoja = $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - 10;
        $this->dataMaster = $data[0];
        $this->datos_detalle = $data;
        $this->fecha_ini = $fecha_ini;
        $this->fecha_fin = $fecha_fin;
        $this->funcionario = $funcionario;
        $this->tipo = $tipo;
        $this->SetMargins(5, 45, 5);
    }

    function Header()
    {
        $this->SetMargins(5, 40, 5);
        $titulo_tipo = $this->tipo == 'acti_fun_asignados' ? 'ASIGNADOS VIGENTES' : 'DEVUELTOS';
        $content = '<table border="1" cellpadding="1" style="font-size: 10px;">
            <tr>
                <td style="width: 23%; color: #444444;" rowspan="2">
                    &nbsp;<img  style="width: 150px;" src="./../../../lib/' . $_SESSION['_DIR_LOGO'] . '" alt="Logo">
                </td>		
                <td style="width: 54%; color: #444444;text-align: center" rowspan="2">
                   <h4 style="font-size: 12px">DEPARTAMENTO ACTIVOS FIJOS</h4>
                   <b style="font-size: 10px">REPORTE DE ACTIVOS FIJOS ' . $titulo_tipo . ' POR FUNCIONARIO</b><br/>
                   <span>Del ' . $this->fecha_ini . ' al ' . $this->fecha_fin . '</span>
                </td>
                <td style="width: 23%; color: #444444; text-align: left;"><br>&nbsp;&nbsp;<b>Fecha:</b> ' . date('d/m/y h:i:s A') . '<br></td>
            </tr>
            <tr>
                <td style="width: 23%; color: #444444; text-align: left;"><br>&nbsp;&nbsp;<b>Usuario:</b> ' . $_SESSION['_LOGIN'] . '</td>
            </tr>
        </table>';
        $this->writeHTMLCell(0, 10, 5, 6, $content, 0, 0, 0, true, 'L', true);
        $this->Ln(24);
        $this->fieldsHeader();
    }

    public function fieldsHeader()
    {
        /* $html = '<table>
             <tr><td style="width: 15%;"><b>FUNCIONARIO(A):</b></td><td>' . $this->funcionario . '</td></tr>
             </table><br><br>';
         $this->writeHTML($html, false, false, true, false, '');*/
    }

    function generarReporte()
    {
        $this->AddPage();
        $html = '';
        $table_header = '<br><br><table border="1" cellpadding="2" style="font-size: 11px"><tr>
                <td style="text-align: center;width: 4%"><b>N°</b></td>
                <td style="text-align: center;width: 10%"><b>CÓDIGO</b></td>
                <td style="text-align: center;width: 22%"><b>NOMBRE</b></td>
                <td style="text-align: center;width: 26%"><b>DESCRIPCIÓN</b></td>
                <td style="text-align: center;width: 8%"><b>ESTADO<br>FUNCIONAL</b></td>
                <td style="text-align: center;width: 20%"><b>NÚM.TRÁMITE / FECHA</b></td>
                <td style="text-align: center;width: 10%"><b>UBICACIÓN</b></td></tr>';
        $cf = 1;
        foreach ($this->funcionario as $f) {
            $html .= '<table style="font-size: 11px"><tr><td style="width: 15%;"><b>FUNCIONARIO(A):</b></td><td>' . $f['nombre_completo2'] . '</td></tr>
             </table>';
            if (count($this->getDataSource()) > 0) {
                $i = 1;
                $id_ant = 0;
                $table_func = '';
                foreach ($this->getDataSource() as $datarow) {
                    if ($f['id_funcionario'] != $datarow['id_funcionario']) {
                        continue;
                    } else if ($i == 1) {
                        $table_func .= $table_header;
                    }
                    if ($this->tipo == 'acti_fun_asignados') {
                        if ($id_ant == $datarow['id_activo_fijo']) {
                            continue;
                        }
                    }
                    $fmov = empty($datarow['fecha_mov']) ? '-' : implode('/', array_reverse(explode('-', $datarow['fecha_mov'])));
                    $ffin = empty($datarow['fecha_finalizacion']) ? '-' : implode('/', array_reverse(explode('-', $datarow['fecha_finalizacion'])));
                    $tramite = '<b>Fecha:</b> ' . $fmov . '<br><b>Fecha finalización Mov.:</b> ' . $ffin . '<br><b>Trámite:</b> ' . $datarow['num_tramite'] . '<br><b>Estado:</b> ' . $datarow['estado_tramite'];
                    $table_func .= '<tr>';
                    $table_func .= '<td style="text-align: center">' . $i . '</td>';
                    $table_func .= '<td style="text-align: center">' . $datarow['codigo'] . '</td>';
                    $table_func .= '<td>' . $datarow['denominacion'] . '</td>';
                    $table_func .= '<td>' . $datarow['descripcion'] . '</td>';
                    $table_func .= '<td style="text-align: center">' . $datarow['estado_fun'] . '</td>';
                    $table_func .= '<td>' . $tramite . '</td>';
                    $table_func .= '<td>' . $datarow['ubicacion'] . '</td>';
                    $table_func .= '</tr>';
                    $i++;
                    $id_ant = $datarow['id_activo_fijo'];
                }
                $html .= $table_func == '' ? '<table style="font-size: 11px"><tr><td>Sin resultados.</td></tr>' : $table_func;
                $html .= count($this->funcionario) == $cf ? '</table>' : '</table><br><br><br>';
            } else {
                $html = '<br><p style="text-align: center">No se encontraron resultados para el criterio seleccionado.</p>';
            }
            $cf++;
        }
        $this->writeHTML($html, false, false, true, false, '');
    }
}

?>
