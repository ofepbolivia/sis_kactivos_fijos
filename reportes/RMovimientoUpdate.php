<?php
// Extend the TCPDF class to create custom MultiRow
/*
 * Autor RAC
 * Fecha: 16/03/2017
 *
 * */
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');
class RMovimientoUpdate extends  ReportePDF {
    var $dataMaster;
    var $datos_detalle;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $tipoMov;
    var $numPag;
    function getDataSource(){
        return  $this->datos_detalle;
    }
    function datosHeader  ( $maestro, $detalle ) {
        $this->ancho_hoja = $this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-10;
        $this->datos_detalle = $detalle;
        $this->dataMaster = $maestro;
        $this->tipoMov = $this->dataMaster[0]['cod_movimiento'];
        $this->numPag = 1;
        if($this->tipoMov=='asig' || $this->tipoMov=='devol' || $this->tipoMov=='transf'){
            $this->SetMargins(15, 55, 15);
        }else if ($this->tipoMov=='deprec'){
            $this->SetMargins(7, 53, 5);
        }else{
            $this->SetMargins(7, 52, 5);
        }
    }
    function Header() {
        $height = 6;
        $midHeight = 9;
        $longHeight = 18;
        $x = $this->GetX();
        $y = $this->GetY();
        $this->SetXY($x, $y);
        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 20,5,35,16);
        $this->SetFontSize(12);
        $this->SetFont('', 'B');
        $this->Cell(44, $midHeight, '', 'LRT', 0, 'C', false, '', 0, false, 'T', 'C');
        $this->tipoMov = $this->dataMaster[0]['cod_movimiento'];
        if($this->tipoMov=='transf'){
            $this->Cell(159, $midHeight, 'FORMULARIO DE '.strtoupper($this->dataMaster[0]['movimiento']).' DE ACTIVOS FIJOS', 'LRT', 0, 'C', false, '', 0, false, 'T', 'C');
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Ln();
            $this->SetFontSize(10);
            $this->SetFont('', 'B');
            $this->Cell(44, $midHeight, '', 'LRB', 0, 'C', false, '', 0, false, 'T', 'C');
            $this->Cell(159, $midHeight, strtoupper($this->dataMaster[0]['depto']), 'LRB', 0, 'C', false, '', 0, false, 'T', 'C');
            $this->SetFontSize(7);
            $width1 = 15;
            $width2 = 25;
            $this->SetXY($x, $y);
            $this->SetFont('', 'B',7);
            $this->SetXY($x, $y);
            $this->setCellPaddings(2);
            $this->Cell($width1, $height, 'PROCESO:', 1, 0, '', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '', 7);
            $this->Cell($width2+9, $height,$this->dataMaster[0]['num_tramite'], 1, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->setCellPaddings(2);
            $this->Ln();
            $this->SetX($x);
            $this->SetFont('', 'B', 7);
            $this->Cell($width1, $height, 'FECHA:', 1, 0, '', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '', 7);
            $cab_fecha = date("d/m/Y",strtotime($this->dataMaster[0]['fecha_mov']));
            $this->Cell($width2+9, $height,$cab_fecha, 1, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->setCellPaddings(2);
            $this->Ln();
            $this->SetX($x);
            $this->SetFont('', 'B', 7);
            $this->Cell($width1, $height, 'PAGINA:', 1, 0, '', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '', 7);
            $this->Cell($w = $width2+9, $h = $height, $txt = $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $this->setCellPaddings(2);
        }else if($this->tipoMov=='devol'){
            $formulario = strtr(strtoupper($this->dataMaster[0]['movimiento']), "àáâãäåæçèéêëìíîïðñòóôõöøùüú", "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ");
            $this->Cell(157.5, $midHeight, 'FORMULARIO DE '.$formulario.' DE ACTIVOS FIJOS', 'LRT', 0, 'C', false, '', 0, false, 'T', 'C');
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Ln();
            $this->SetFontSize(10);
            $this->SetFont('', 'B');
            $this->Cell(44, $midHeight, '', 'LRB', 0, 'C', false, '', 0, false, 'T', 'C');
            $this->Cell(157.5, $midHeight, strtoupper($this->dataMaster[0]['depto']), 'LRB', 0, 'C', false, '', 0, false, 'T', 'C');
            $this->SetFontSize(7);
            $width1 = 15;
            $width2 = 25;
            $this->SetXY($x, $y);
            $this->SetFont('', 'B',7);
            $this->SetXY($x, $y);
            $this->setCellPaddings(2);
            $this->Cell($width1+4, $height, 'PROCESO:', 1, 0, '', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '', 7);
            $this->Cell($width2+9, $height,$this->dataMaster[0]['num_tramite'], 1, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->setCellPaddings(2);
            $this->Ln();
            $this->SetX($x);
            $this->SetFont('', 'B', 7);
            $this->Cell($width1+4, $height, 'FECHA:', 1, 0, '', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '', 7);
            $cab_fecha = date("d/m/Y",strtotime($this->dataMaster[0]['fecha_mov']));
            $this->Cell($width2+9, $height,$cab_fecha, 1, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->setCellPaddings(2);
            $this->Ln();
            $this->SetX($x);
            $this->SetFont('', 'B', 7);
            $this->Cell($width1+4, $height, 'PAGINA:', 1, 0, '', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '', 7);
            $this->Cell($w = $width2+9, $h = $height, $txt = $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $this->setCellPaddings(2);
        }else {
            $formulario = strtr(strtoupper($this->dataMaster[0]['movimiento']), "àáâãäåæçèéêëìíîïðñòóôõöøùüú", "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ");
            if($this->dataMaster[0]['prestamo']=='si'){
                $this->Cell(166, $midHeight, 'FORMULARIO DE PRESTAMO DE ACTIVOS FIJOS', 'LRT', 0, 'C', false, '', 0, false, 'T', 'C');
            }else{
                $this->Cell(166, $midHeight, 'FORMULARIO DE ' .$formulario. ' DE ACTIVOS FIJOS', 'LRT', 0, 'C', false, '', 0, false, 'T', 'C');
            }
            $this->tipoMov = $this->dataMaster[0]['cod_movimiento'];
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Ln();
            $this->SetFontSize(10);
            $this->SetFont('', 'B');
            $this->Cell(44, $midHeight, '', 'LRB', 0, 'C', false, '', 0, false, 'T', 'C');
            $depto = strtr(mb_strtoupper($this->dataMaster[0]['depto']), "àáâãäåæçèéêëìíîïðñòóôõöøùüú", "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ");
            $this->Cell(166, $midHeight, $depto, 'LRB', 0, 'C', false, '', 0, false, 'T', 'C');
            $this->SetFontSize(7);
            $width1 = 15;
            $width2 = 25;
            $this->SetXY($x, $y);
            $this->SetFont('', 'B',7);
            $this->Cell(44, $longHeight, '', 1, 0, 'C', false, '', 0, false, 'T', 'C');
            $this->SetXY($x, $y);
            $this->setCellPaddings(2);
            $this->Cell($width1, $height, 'PROCESO:', 1, 0, '', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '', 7);
            $this->Cell($width2+4, $height,$this->dataMaster[0]['num_tramite'], "B", 0, 'L', false, '', 0, false, 'T', 'C');
            $this->setCellPaddings(2);
            $this->Ln();
            $this->SetX($x);
            $this->SetFont('', 'B', 7);
            $this->Cell($width1, $height, 'FECHA:', 1, 0, '', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '', 7);
            $cab_fecha = date("d/m/Y",strtotime($this->dataMaster[0]['fecha_mov']));
            $this->Cell($width2+4, $height,$cab_fecha, "B", 0, 'L', false, '', 0, false, 'T', 'C');
            $this->setCellPaddings(2);
            $this->Ln();
            $this->SetX($x);
            $this->SetFont('', 'B', 7);
            $this->Cell($width1, $height, 'PAGINA:', 1, 0, '', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '', 7);
            $this->Cell($w = $width2+1, $h = $height, $txt = $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), $border = "B", $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
            $this->setCellPaddings(2);
        }
        $this->Ln();
        $this->fieldsHeader($this->tipoMov);
        $this->generarCabecera($this->tipoMov);
    }
    public function fieldsHeader($tipo){
        $this->SetFont('', '');
        $this->SetFontSize(10);
        $this->Ln();
        $tbl = '<table border="1" style="font-size: 8pt;">';
        if($tipo=='asig'){
            $tbl.='<tr>
                        <td width="11%"><b>A: </b></td>
                        <td width="51%">'.$this->dataMaster[0]['responsable'].'</td>
                        <td width="40%"><b>GLOSA: </b></td>
                   </tr>
                   <tr>
                        <td width="11%" ><b>LUGAR: </b></td>
                        <td width="51%" align="left"><br>'.$this->dataMaster[0]['lugar'].'</td>
                        <td width="40%" align="left" rowspan="3">'.$this->dataMaster[0]['glosa'].'</td>
                   </tr>
                   <tr>
                        <td width="11%" ><b>OFICINA: </b></td>
                        <td width="51%" >'.$this->dataMaster[0]['oficina'].'</td>
                   </tr>
                   <tr>
                        <td width="11%"><b>DIRECCIÓN: </b></td>
                        <td width="51%" >'.$this->dataMaster[0]['direccion'].'</td>
                   </tr>
                   </table>
                   ';
            $this->writeHTML ($tbl);
        }else if($tipo=='transf'){
            $tbl.='<tr>
                        <td width="11%"><b>ORIGEN: </b></td>
                        <td width="39%">'.$this->dataMaster[0]['responsable'].'</td>
                        <td width="11%"><b>DESTINO: </b></td>
                        <td width="40%">'.$this->dataMaster[0]['responsable_dest'].'</td>
                   </tr>
                   <tr>
                        <td width="11%" ><b>LUGAR: </b></td>
                        <td width="39%" align="left">'.$this->dataMaster[0]['lugar_funcionario'].'</td>
                        <td width="11%" align="left"><b>LUGAR: </b></td>
                        <td width="40%" align="left" >'.$this->dataMaster[0]['lugar'].'</td>
                   </tr>
                   <tr>
                        <td width="11%" ><b>OFICINA: </b></td>
                        <td width="39%" >'.$this->dataMaster[0]['oficina_funcionario'].'</td>
                        <td width="11%" ><b>OFICINA: </b></td>
                        <td width="40%" >'.$this->dataMaster[0]['oficina'].'</td>
                   </tr>
                   <tr>
                        <td width="11%"><b>DIRECCIÓN: </b></td>
                        <td width="39%" >'.$this->dataMaster[0]['direccion_funcionario'].'</td>
                        <td width="11%" ><b>DIRECCIÓN: </b></td>
                        <td width="40%" >'.$this->dataMaster[0]['direccion'].'</td>
                   </tr>
                   </table>
                   ';
            $this->writeHTML ($tbl);
            $tbl = '<table border="1" style="font-size: 8pt;" >
                       <tr>
                            <td width="101%" align="center" style="font-size: 9pt;"><b>GLOSA</b></td>
                       </tr>
                       <tr>
                            <td width="101%">'.$this->dataMaster[0]['glosa'].'</td>
                       </tr>
                   </table>';
            $this->writeHTML ($tbl);

        }else if($tipo=='devol'){
            $destino_dev = '';
            if($this->dataMaster[0]['id_funcionario_dest']==null){                                
                if($this->dataMaster[0]['codigo_depto'] == 'DAF-TI'){
                    if($this->dataMaster[0]['deposito'] == null ){                        
                        $destino_dev = 'ALMACEN';
                        $direccion = $this->dataMaster[0]['direccion_responsable'];
                    }else{                        
                        $destino_dev = $this->dataMaster[0]['deposito'];
                        if ($this->dataMaster[0]['direccion'] == null || $this->dataMaster[0]['direccion'] == '' ){
                            $direccion = $this->dataMaster[0]['direccion_responsable'];                            
                        }else{
                            $direccion = $this->dataMaster[0]['direccion'];
                        }       
                    }
                }else{
                    if($this->dataMaster[0]['deposito'] == null ){                                                                        
                        $destino_dev = $this->dataMaster[0]['responsable_depto'];
                        $direccion = $this->dataMaster[0]['direccion_responsable'];
                    }else{                                                                                          
                        $destino_dev = $this->dataMaster[0]['deposito'];
                        if ($this->dataMaster[0]['direccion'] == null || $this->dataMaster[0]['direccion'] == '' ){
                            $direccion = $this->dataMaster[0]['direccion_responsable'];                            
                        }else{
                            $direccion = $this->dataMaster[0]['direccion'];
                        }                        
                    }
                }
                $tbl .= '<tr>
                            <td width="11%"><b>ORIGEN: </b></td>
                            <td width="40%">' . $this->dataMaster[0]['responsable'] . '</td>
                            <td width="11%"><b>DESTINO: </b></td>
                            <td width="40%">' . $destino_dev . '</td>
                       </tr>
                       <tr>
                            <td width="11%" ><b>LUGAR: </b></td>
                            <td width="40%" align="left">' . $this->dataMaster[0]['lugar_funcionario'] . '</td>
                            <td width="11%" align="left"><b>LUGAR: </b></td>
                            <td width="40%" align="left" >' . $this->dataMaster[0]['lugar_responsable'] . '</td>
                       </tr>
                       <tr>
                            <td width="11%" ><b>OFICINA: </b></td>
                            <td width="40%" >' . $this->dataMaster[0]['oficina_funcionario'] . '</td>
                            <td width="11%" ><b>OFICINA: </b></td>
                            <td width="40%" >' . $this->dataMaster[0]['oficina_responsable'] . '</td>
                       </tr>
                       <tr>
                            <td width="11%"><b>DIRECCIÓN: </b></td>
                            <td width="40%" >' . $this->dataMaster[0]['direccion_funcionario'] . '</td>
                            <td width="11%" ><b>DIRECCIÓN: </b></td>
                            <td width="40%" >' .$direccion. '</td>
                       </tr>
                       </table>
                       ';
                $this->writeHTML($tbl);
            }else {                            
                if($this->dataMaster[0]['codigo_depto'] == 'DAF-TI'){
                    if($this->dataMaster[0]['deposito'] == null ){
                        $destino_dev = 'ALMACEN';
                        $direccion = $this->dataMaster[0]['oficina_direccion'];
                    }else{
                        $destino_dev = $this->dataMaster[0]['deposito'];
                        if($this->dataMaster[0]['direccion'] == null || $this->dataMaster[0]['direccion'] == '' ){
                            $direccion = $this->dataMaster[0]['oficina_direccion'];
                        }else{
                            $direccion = $this->dataMaster[0]['direccion'];
                        }
                    }
                }else{                    
                    if ( $this->dataMaster[0]['deposito'] == null ) { 
                        $destino_dev = $this->dataMaster[0]['responsable_dest'];                                 
                        $direccion = $this->dataMaster[0]['oficina_direccion'];          
                    }else{
                        $destino_dev = $this->dataMaster[0]['deposito'];
                        if($this->dataMaster[0]['direccion'] == null || $this->dataMaster[0]['direccion'] == '' ){
                            $direccion = $this->dataMaster[0]['oficina_direccion'];
                        }else{
                            $direccion = $this->dataMaster[0]['direccion'];
                        }                        
                    }
                }
                $tbl .= '<tr>
                            <td width="11%"><b>ORIGEN: </b></td>
                            <td width="40%">' . $this->dataMaster[0]['responsable'] . '</td>
                            <td width="11%"><b>DESTINO: </b></td>
                            <td width="40%">' . $destino_dev . '</td>
                       </tr>
                       <tr>
                            <td width="11%" ><b>LUGAR: </b></td>
                            <td width="40%" align="left">' . $this->dataMaster[0]['lugar_funcionario'] . '</td>
                            <td width="11%" align="left"><b>LUGAR: </b></td>
                            <td width="40%" align="left" >' . $this->dataMaster[0]['lugar_destino'] . '</td>
                       </tr>
                       <tr>
                            <td width="11%" ><b>OFICINA: </b></td>
                            <td width="40%" >' . $this->dataMaster[0]['oficina_funcionario'] . '</td>
                            <td width="11%" ><b>OFICINA: </b></td>
                            <td width="40%" >' . $this->dataMaster[0]['oficina_destino'] . '</td>
                       </tr>
                       <tr>
                            <td width="11%"><b>DIRECCIÓN: </b></td>
                            <td width="40%" >' . $this->dataMaster[0]['direccion_funcionario'] . '</td>
                            <td width="11%" ><b>DIRECCIÓN: </b></td>
                            <td width="40%" >' . $direccion. '</td>
                       </tr>
                       </table>
                       ';
                $this->writeHTML($tbl);
            }
            $tbl = '<table border="1" style="font-size: 8pt;" >
                           <tr>
                                <td width="102%" align="center" style="font-size: 9pt;"><b>GLOSA</b></td>
                           </tr>
                           <tr>
                                <td width="102%">' . $this->dataMaster[0]['glosa'] . '</td>
                           </tr>
                       </table>';
            $this->writeHTML($tbl);
        }
        $this->firstPage++;
    }
    function Firmas() {
        //<img  style="width: 110px; height: 110px;" src="' . $this->generarImagen($this->dataMaster[0]['responsable_depto'], 'RESPONSABLE ACTIVOS FIJOS') . '" alt="Logo">
        //<img  style="width: 110px; height: 110px;" src="' . $this->generarImagen($this->dataMaster[0]['responsable'], strtoupper($this->dataMaster[0]['nombre_cargo'])) . '" alt="Logo">
        if ($this->getY()>=150 && $this->getY()<=234){
            $pos = $this->getY()+(234-$this->getY());
            $this->setY($pos);
        }
        $this->Ln(5);
        $tbl = '';
        $descripcion = '';
        if($this->tipoMov == 'asig'){
            if($this->dataMaster[0]['codigo_depto'] == 'DAF-TI'){
                $descripcion = 'EMPLEADO ASIGNACIÓN';
            }else{
                $descripcion = 'RECIBÍ CONFORME';
            }
            if($this->dataMaster[0]['custodio']!='' && $this->dataMaster[0]['codigo_depto'] == 'DAF-TI') {
                $tbl = '<table>
                        <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1">
                                
                                <tr>
                                    <td align="center" > 
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" rowspan="3">
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['responsable_depto'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['responsable'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['custodio'] . '</td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['cargo_jefe']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['nombre_cargo']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . 'CI. ' . strtoupper($this->dataMaster[0]['ci_custodio']) . '</td>
                                </tr>
                                
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>RESPONSABLE</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>'.$descripcion.'</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>CUSTODIO</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>ENCARGADO ALMACEN</b></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:15%;"></td>
                        </tr>
                    </table>      
                    ';
            }else if($this->dataMaster[0]['custodio']=='' && $this->dataMaster[0]['codigo_depto'] == 'DAF-TI'){
                $tbl = '<table>
                        <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1">
                                
                                <tr>
                                    <td align="center" > 
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" rowspan="3" >
                                        <br>
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">'.$this->dataMaster[0]['responsable_depto'].'</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">'.$this->dataMaster[0]['responsable'].'</td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">'.strtoupper($this->dataMaster[0]['cargo_jefe']).'</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">'.strtoupper($this->dataMaster[0]['nombre_cargo']).'</td>
                                </tr>
                                
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>RESPONSABLE</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>'.$descripcion.'</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>ENCARGADO ALMACEN</b></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:15%;"></td>
                        </tr>
                    </table>      
                    ';
            }else if($this->dataMaster[0]['custodio']!='' && $this->dataMaster[0]['codigo_depto'] == 'DAF-CENTRAL') {
                $tbl = '<table>
                        <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1">
                                
                                <tr>
                                    <td align="center" > 
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['responsable_depto'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['responsable'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['custodio'] . '</td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['cargo_jefe']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['nombre_cargo']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . 'CI. ' . strtoupper($this->dataMaster[0]['ci_custodio']) . '</td>
                                </tr>
                                
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>RESPONSABLE</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>'.$descripcion.'</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>CUSTODIO</b></td>
                                    
                                </tr>
                            </table>
                        </td>
                        <td style="width:15%;"></td>
                        </tr>
                    </table>      
                    ';
            }else if($this->dataMaster[0]['custodio']=='' && $this->dataMaster[0]['codigo_depto'] == 'DAF-CENTRAL') {
                $tbl = '<table>
                        <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1">
                                
                                <tr>
                                    <td align="center" > 
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">'.$this->dataMaster[0]['responsable_depto'].'</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">'.$this->dataMaster[0]['responsable'].'</td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">'.strtoupper($this->dataMaster[0]['cargo_jefe']).'</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">'.strtoupper($this->dataMaster[0]['nombre_cargo']).'</td>
                                </tr>
                                
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>RESPONSABLE</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>'.$descripcion.'</b></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:15%;"></td>
                        </tr>
                    </table>      
                    ';
            }
        }else if($this->tipoMov == 'transf'){
            $responsable = '';
            $cargo = '';
            if($this->dataMaster[0]['custodio']!='') {
                if($this->dataMaster[0]['id_funcionario_dest']==null){
                    if($this->dataMaster[0]['codigo_depto'] == 'DAF-TI' && $this->tipoMov == 'devol'){
                        $descripcion = 'ENCARGADO ALMACEN';
                    }else{
                        $descripcion = 'RECIBÍ CONFORME';
                        $responsable = $this->dataMaster[0]['responsable_depto'];
                        $cargo = strtoupper($this->dataMaster[0]['cargo_jefe']);
                    }
                    $tbl = '<table>
                        <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1">
                                
                                <tr>
                                    <td align="center" > 
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['func_resp_dep'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['responsable'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $responsable . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['custodio'] . '</td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['func_cargo_dep']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['nombre_cargo']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $cargo . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . 'CI. ' . strtoupper($this->dataMaster[0]['ci_custodio']) . '</td>
                                </tr>
                                
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>RESPONSABLE</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>ENTREGUE CONFORME</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>'.$descripcion.'</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>CUSTODIO</b></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:15%;"></td>
                        </tr>
                    </table>      
                    ';
                }else{
                    if($this->dataMaster[0]['codigo_depto'] == 'DAF-TI' && $this->tipoMov == 'devol'){
                        $descripcion = 'ENCARGADO ALMACEN';
                    }else{
                        $descripcion = 'RECIBÍ CONFORME';
                        $responsable = $this->dataMaster[0]['responsable_dest'];
                        $cargo = strtoupper($this->dataMaster[0]['nombre_cargo_dest']);
                    }
                    $tbl = '<table>
                        <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1">
                                
                                <tr>
                                    <td align="center" > 
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['func_resp_dep'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['responsable'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $responsable . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['custodio'] . '</td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['func_cargo_dep']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['nombre_cargo']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $cargo . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . 'CI. ' . strtoupper($this->dataMaster[0]['ci_custodio']) . '</td>
                                </tr>
                                
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>RESPONSABLE</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>ENTREGUE CONFORME</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>'.$descripcion.'</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>CUSTODIO</b></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:15%;"></td>
                        </tr>
                    </table>      
                    ';
                }
            }else{
                if($this->dataMaster[0]['id_funcionario_dest']==null) {
                    if($this->dataMaster[0]['codigo_depto'] == 'DAF-TI' && $this->tipoMov == 'devol'){
                        $descripcion = 'RECIBÍ CONFORME';                        
                    }else{
                        $descripcion = 'RECIBÍ CONFORME';
                        $responsable = $this->dataMaster[0]['responsable_depto'];                        
                        $cargo = strtoupper($this->dataMaster[0]['cargo_jefe']);                        
                    }
                    $tbl = '<table>
                        <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1">
                                
                                <tr>
                                    <td align="center" > 
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['func_resp_dep'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['responsable'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' .  $responsable . '</td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['func_cargo_dep']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['nombre_cargo']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $cargo . '</td>
                                </tr>
                                
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>RESPONSABLE</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>ENTREGUE CONFORME</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>'. $descripcion .'</b></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:15%;"></td>
                        </tr>
                    </table>      
                    ';
                }else{
                    if($this->dataMaster[0]['codigo_depto'] == 'DAF-TI' && $this->tipoMov == 'devol'){
                        $descripcion = 'ENCARGADO ALMACEN';                        
                    }else{
                        $descripcion = 'RECIBÍ CONFORME';
                        $responsable = $this->dataMaster[0]['responsable_dest'];                        
                        $cargo = strtoupper($this->dataMaster[0]['nombre_cargo_dest']);                        
                    }
                    $tbl = '<table>
                        <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1">
                                
                                <tr>
                                    <td align="center" > 
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['func_resp_dep'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['responsable'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $responsable . '</td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['func_cargo_dep']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['nombre_cargo']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $cargo . '</td>
                                </tr>
                                
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>RESPONSABLE</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>ENTREGUE CONFORME</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>'.$descripcion.'</b></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:15%;"></td>
                        </tr>
                    </table>      
                    ';
                }
            }
            //add for devolution 
        }else if ($this->tipoMov == 'devol') {
            $responsable = '';
            $cargo = '';
            if($this->dataMaster[0]['custodio']!='') {
                
                if($this->dataMaster[0]['id_funcionario_dest']==null){
                    if($this->dataMaster[0]['codigo_depto'] == 'DAF-TI' && $this->tipoMov == 'devol'){
                        $descripcion = 'RECIBÍ CONFORME';
                        $cargo = 'ENCARGADO DE DEPÓSITO';
                    }else{                        
                        $descripcion = 'RECIBÍ CONFORME';
                        $cargo = 'ENCARGADO DE DEPÓSITO';
                        $responsable = '';
                        //$responsable = $this->dataMaster[0]['responsable_depto'];
                        //$cargo = strtoupper($this->dataMaster[0]['cargo_jefe']);
                    }
                    $tbl = '<table>
                        <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1">
                                
                                <tr>
                                    <td align="center" > 
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['func_resp_dep'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['responsable'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $responsable . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['custodio'] . '</td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['func_cargo_dep']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['nombre_cargo']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $cargo . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . 'CI. ' . strtoupper($this->dataMaster[0]['ci_custodio']) . '</td>
                                </tr>
                                
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>RESPONSABLE</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>ENTREGUE CONFORME</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>'.$descripcion.'</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>CUSTODIO</b></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:15%;"></td>
                        </tr>
                    </table>      
                    ';
                }else{                    
                    if($this->dataMaster[0]['codigo_depto'] == 'DAF-TI' && $this->tipoMov == 'devol'){
                        $descripcion = 'RECIBÍ CONFORME';
                        $cargo = 'ENCARGADO DE DEPÓSITO';
                    }else{
                        $descripcion = 'RECIBÍ CONFORME';
                        $cargo = 'ENCARGADO DE DEPÓSITO';
                        $responsable = '';
                        //$responsable = $this->dataMaster[0]['responsable_dest'];
                        //$cargo = strtoupper($this->dataMaster[0]['nombre_cargo_dest']);
                    }
                    $tbl = '<table>
                        <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1">
                                
                                <tr>
                                    <td align="center" > 
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['func_resp_dep'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['responsable'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $responsable . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['custodio'] . '</td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['func_cargo_dep']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['nombre_cargo']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $cargo . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . 'CI. ' . strtoupper($this->dataMaster[0]['ci_custodio']) . '</td>
                                </tr>
                                
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>RESPONSABLE</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>ENTREGUE CONFORME</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>'.$descripcion.'</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>CUSTODIO</b></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:15%;"></td>
                        </tr>
                    </table>      
                    ';
                }
            }else{                
                if($this->dataMaster[0]['id_funcionario_dest']==null) {                    
                    if($this->dataMaster[0]['codigo_depto'] == 'DAF-TI' && $this->tipoMov == 'devol'){                        
                        $descripcion = 'RECIBÍ CONFORME';
                        $cargo = 'ENCARGADO DE DEPÓSITO';
                    }else{                        
                        $descripcion = 'RECIBÍ CONFORME';                        
                        $responsable = '';                        
                        $cargo = 'ENCARGADO DE DEPÓSITO';
                    }
                    $tbl = '<table>
                        <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1">
                                
                                <tr>
                                    <td align="center" > 
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['func_resp_dep'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['responsable'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' .  $responsable . '</td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['func_cargo_dep']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['nombre_cargo']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $cargo . '</td>
                                </tr>
                                
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>RESPONSABLE</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>ENTREGUE CONFORME</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>'. $descripcion .'</b></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:15%;"></td>
                        </tr>
                    </table>      
                    ';
                }else{                    
                    if($this->dataMaster[0]['codigo_depto'] == 'DAF-TI' && $this->tipoMov == 'devol'){                        
                        $descripcion = 'RECIBÍ CONFORME';
                        $cargo = 'ENCARGADO DE DEPÓSITO';
                    }else{
                        $descripcion = 'RECIBÍ CONFORME';                        
                        $responsable = '';                        
                        $cargo = 'ENCARGADO DE DEPÓSITO';
                    }
                    $tbl = '<table>
                        <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1">
                                
                                <tr>
                                    <td align="center" > 
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                    <td align="center" >
                                        <br>
                                        <br>
                                        <br> 
                                        <br> 
                                        <br> 
                                        <br> 
                                    </td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['func_resp_dep'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $this->dataMaster[0]['responsable'] . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $responsable . '</td>
                                </tr>
                                 
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['func_cargo_dep']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . strtoupper($this->dataMaster[0]['nombre_cargo']) . '</td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;">' . $cargo . '</td>
                                </tr>
                                
                                <tr>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>RESPONSABLE</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>ENTREGUE CONFORME</b></td>
                                    <td align="center" style="font-family: Calibri; font-size: 9px;"><b>'.$descripcion.'</b></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:15%;"></td>
                        </tr>
                    </table>      
                    ';
                }
            }
        }
        $this->Ln(5);
        $this->writeHTML($tbl, true, false, false, false, '');
    }
    function generarImagen($nom, $car){
        $cadena_qr = 'Nombre: '.$nom. "\n" . 'Cargo: '.$car."\n" ;
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
    function generarReporte() {
        $this->setFontSubsetting(false);
        $this->AddPage();
        $tipo = $this->tipoMov;
        $this->SetFontSize(7);
        $i = 0;
        if($tipo=='asig' || $tipo=='transf') {
            if($this->dataMaster[0]['codigo_depto'] == 'DAF-CENTRAL'){
                $this->Ln(7);
            }
            if($this->dataMaster[0]['codigo_depto'] == 'DAF-TI'){
                $this->Ln(3);
            }
            if($tipo=='transf'){
                $this->Ln(9);//12
            }
            foreach ($this->getDataSource() as $datarow) {
                $this->tablealigns = array('L', 'C', 'L', 'L', 'L', 'L');
                $this->tablenumbers = array(0, 0, 0, 0, 0, 0);
                $this->tableborders = array('RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB');
                $this->tabletextcolor = array();
                $RowArray = array(
                    's0' => $i + 1,
                    's1' => $datarow['codigo'],
                    's2' => $datarow['denominacion'],
                    's4' => $datarow['descripcion'],
                    's5' => $datarow['estado_fun'],
                    's6' => ''
                );
                $i++;
                $this->MultiRow($RowArray, false, 1);
                $this->revisarfinPagina();
            }
        }else if($tipo=='devol'){
            $this->Ln(12);
            foreach ($this->getDataSource() as $datarow){
                $this->tablealigns = array('L', 'C', 'L', 'L', 'L', 'L');
                $this->tablenumbers = array(0, 0, 0, 0, 0, 0);
                $this->tableborders = array('RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB');
                $this->tabletextcolor = array();
                $RowArray = array(
                    's0' => $i + 1,
                    's1' => $datarow['codigo'],
                    's2' => $datarow['denominacion'],
                    's4' => $datarow['descripcion'],
                    's5' => $datarow['estado_fun'],
                    's6' => ''
                );
                $i++;
                $this->MultiRow($RowArray, false, 1);
                $this->revisarfinPagina();
            }
        }
        $this->Ln(3);
        $this->Firmas();
    }
    function generarCabecera($tipo){
        //armca caecera de la tabla
        $this->SetFontSize(9);
        $this->SetFont('', 'B');
        ///////////////////////////////////////
        if($tipo=='asig'|| $tipo=='devol'){
            $this->tablewidthsHD=array(8,25,70,59,35,57.5);
            //$this->tablewidths=array(8,31,84.5,34,32.5,26.5,18,20.5);
            $this->tablealignsHD=array('C','C','C','C','C','C');
            $this->tablenumbersHD=array(0,0,0,0,0,0);
            $this->tablebordersHD=array('RLTB','RLTB','RLTB','RLTB','RLTB','RLTB');
            $this->tabletextcolorHD=array();
            $RowArray = array(
                's0'  => 'Nro',
                's1' => 'CODIGO',
                's2' => 'DENOMINACIÓN',
                's3' => 'DESCRIPCIÓN',
                's4' => 'ESTADO FUNCIONAL',
                's5' => 'OBSERVACIONES');
        }else if($tipo=='transf'){
            $this->tablewidthsHD=array(8,25,70,59,35,55);
            //$this->tablewidths=array(8,31,84.5,34,32.5,26.5,18,20.5);
            $this->tablealignsHD=array('C','C','C','C','C','C');
            $this->tablenumbersHD=array(0,0,0,0,0,0);
            $this->tablebordersHD=array('RLTB','RLTB','RLTB','RLTB','RLTB','RLTB');
            $this->tabletextcolorHD=array();
            $RowArray = array(
                's0'  => 'Nro',
                's1' => 'CODIGO',
                's2' => 'DENOMINACIÓN',
                's3' => 'DESCRIPCIÓN',
                's4' => 'ESTADO FUNCIONAL',
                's5' => 'OBSERVACIONES');
        }
        /////////////////////////////////
        $this-> MultiRowHeader($RowArray,false,1);
        $this->tablewidths = $this->tablewidthsHD;
        if($this->numPag>1){
            $this->SetMargins(15, $this->GetY()+5, 15);
        }
        $this->numPag = $this->numPag + 1;
    }
    function revisarfinPagina(){
        $dimensions = $this->getPageDimensions();
        $hasBorder = false; //flag for fringe case
        $startY = $this->GetY();
        $this->getNumLines($row['cell1data'], 80);
        if (($startY + 4 * 3) + $dimensions['bm'] > ($dimensions['hk'])) {
            if($this->total!= 0){
                $this->AddPage();
            }
        }
    }
    function Footer() {
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
        $this->Cell($ancho, 0, 'Usuario: '.$_SESSION['_LOGIN'], '', 0, 'L');
        $pagenumtxt = '';//'Página'.' '.$this->getAliasNumPage().' de '.$this->getAliasNbPages();
        $this->Cell($ancho, 0, $pagenumtxt, '', 0, 'C');
        $this->Cell($ancho, 0, $_SESSION['_REP_NOMBRE_SISTEMA'], '', 0, 'R');
        $this->Ln();
        $fecha_rep = date("d-m-Y H:i:s");
        $this->Cell($ancho, 0, "Fecha : ".$fecha_rep, '', 0, 'L');
        $this->Ln($line_width);
        $this->Ln();
        $barcode = $this->getBarcode();
        $style = array(
            'position' => $this->rtl?'R':'L',
            'align' => $this->rtl?'R':'L',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'padding' => 0,
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'text' => false,
            'position' => 'R'
        );
        $this->write1DBarcode($barcode, 'C128B', $ancho*2, $cur_y + $line_width+5, '', (($this->getFooterMargin() / 3) - $line_width), 0.3, $style, '');
    }
}
?>