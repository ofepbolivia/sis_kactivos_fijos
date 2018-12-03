<?php
require_once dirname(__FILE__) . '/../../pxp/lib/lib_reporte/ReportePDFFormulario.php';

//class RConformidadTotal extends ReportePDF
class RHistoricoAF extends ReportePDFFormulario
{
    var $customy;

    function Header()
    {
        $this->ln(15);

        $this->Image(dirname(__FILE__) . '/../../pxp/lib' . $_SESSION['_DIR_LOGO'], 20, 10, 45);


        $height = 25;
        //cabecera del reporte
        $this->Cell(100, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->SetFontSize(16);
        $this->SetFont('', 'B');
        $this->Cell(100, $height, 'REPORTE HISTORICO DE COMPRA ACTIVOS INTANGIBLES', 0, 0, 'C', false, '', 0, false, 'T', 'C');

        $this->SetMargins(10, 50, 10);
        $this->ln(20);
        $this->customy = $this->getY();


    }

    public function Footer()
    {
        $this->SetFontSize(7);
        $this->setY(-10);
        $ormargins = $this->getOriginalMargins();
        $this->SetTextColor(0, 0, 0);
        //set style for cell border
        $line_width = 0.85 / $this->getScaleFactor();
        $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $ancho = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right']) / 3);
        $this->Ln(2);
        $cur_y = $this->GetY();
        //$this->Cell($ancho, 0, 'Generado por XPHS', 'T', 0, 'L');
        $this->Cell($ancho, 0, 'Usuario: ' . $_SESSION['_LOGIN'], '', 1, 'L');
        $pagenumtxt = 'Página' . ' ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages();

        //$this->Cell($ancho, 0, '', '', 0, 'C');
        $fecha_rep = date("d-m-Y H:i:s");
        $this->Cell($ancho, 0, "Fecha impresión: " . $fecha_rep, '', 0, 'L');
        $this->Cell($ancho, 0, $pagenumtxt, '', 0, 'C');
        $this->Ln($line_width);
    }


//    function reporteGeneralSegundo($maestro)
//    {

    /*    $fecha_inicio = $maestro[0]['fecha_inicio'];
        $fecha_fin = $maestro[0]['fecha_fin'];
        $fecha_conformidad_final = $maestro[0]['fecha_conformidad_final'];
        $conformidad_final = $maestro[0]['conformidad_final'];
        $observaciones = $maestro[0]['observaciones'];
        $num_tramite = $maestro[0]['numero_tramite'];
        $proveedor = $maestro[0]['proveedor'];
        $nombre_solicitante = $maestro[0]['nombre_solicitante'];
        $nro_po = $maestro[0]['nro_po'];
        $fecha_po = $maestro[0]['fecha_po'];
        $nro_cuota_vigente = $maestro[0]['nro_cuota_vigente'];
        $desc_ingas = $maestro[0]['desc_ingas'];
        $cantidad_adju = $maestro[0]['cantidad_adju'];
        $descripcion_sol = $maestro[0]['descripcion_sol'];
        $nombre_usuario_firma = $nombre_solicitante;
        $this->firmar = $maestro[0]['firma'];
        //var_dump($nombre_usuario_firma);exit;
        $this->firma['datos_documento']['numero_tramite'] = $num_tramite;
        $this->firma['datos_documento']['nombre_solicitante'] = $nombre_solicitante;
        $this->firma['datos_documento']['proveedor'] = $proveedor;
        $this->firma['datos_documento']['fecha_conformidad_final'] = $fecha_conformidad_final;
        $this->firma['datos_documento']['conformidad_final'] = $conformidad_final;
        $this->firma['datos_documento']['cantidad_adju'] = $cantidad_adju;

        $columasconcepto = '';

        foreach ($maestro as $datomaestro) {
            $columasconcepto = $columasconcepto . '<tr>
                     <td width="40%" align="left">' . $datomaestro['desc_ingas'] . '</td>
                     <td width="40%" align="left">' . $datomaestro['descripcion_sol'] . '</td>
                     <td width="18%" align="right">' . $datomaestro['cantidad_adju'] . '</td>
        		 </tr>';
        }

        $this->AddPage();
        $this->SetMargins(10, 50, 10);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //$this->firmar='si';
        $url_firma = $this->crearFirma2();
        //var_dump('gola');exit;

        //para el nro de PO se oculte si no hay dato
        if (empty($nro_po) and empty($nro_po)) {
            $columanPo = '';
        } else {
            $columanPo = '  <tr>
            	<td width="50%"> <b>Nro PO:  </b>' . $nro_po . '<br></td>
            	<td width="50%"> <b>Fecha PO:  </b>' . $fecha_po . '<br></td>
            </tr>';
        }
        //para que las fechas de inicio fin se oculte si no hay dato
        if (empty($fecha_inicio) and empty($fecha_fin)) {
            $columanFecha = '';
        } else {
            $columanFecha = '  <tr>
            	<td width="50%"> <b>Fecha Inicio:  </b>'.$fecha_inicio.'<br></td>
            	<td width="50%"> <b>Fecha Fin:  </b>'.$fecha_fin.'<br></td>
            </tr>';
        }


        $html = <<<EOF
		<style>
		table, th, td {
   			border: 1px solid black;
   			border-collapse: collapse;
   			font-family: "Times New Roman";
   			font-size: 11pt;
		}
		</style>
		<body>
		<table border="1">
        	<tr>
            	<td width="65%"><b>Verificado Por:  </b>$nombre_solicitante<br></td>
            	<td width="35%"> <b>Fecha de Conformidad:  </b>$fecha_conformidad_final<br></td>
            </tr>
        	<tr>
            	<td width="65%"> <b>Número de Trámite:  </b>$num_tramite<br></td>
            	<td width="35%"> <b>Total Nro Cuota:  </b>$nro_cuota_vigente<br></td>
            </tr>
        	<tr>
            	<td width="100%"> <b>Proveedor:  </b>$proveedor<br></td>
            </tr>

            $columanPo

            $columanFecha
            <tr>
            	<td width="100%"> <b>Conformidad:  </b>$conformidad_final<br></td>
            </tr>
            <tr>
        	    <td width="100%" align="justify"  colspan="2">
        	    En cumplimiento al Reglamento Específico de las Normas Básicas del Sistema de Administración de Bienes y Servicios de la Empresa,  doy conformidad a lo solicitado.
        	    <br><br>
        	    <table border="0">
        	    <tr>
                     <td width="40%" align="center"><b>Concepto</b></td>
                     <td width="40%" align="center"><b>Descripción</b></td>
                     <td width="18%" align="center"><b>Cantidad Adj.</b></td>

        		 </tr>

        		  $columasconcepto
        	     </table>

            	<br><br>
            	El mismo cumple con las características y condiciones requeridas, en calidad y cantidad. La cuál fue adquirida considerando criterios de economía para la obtención de los mejores precios del mercado.
            	<br><br>
            	En conformidad de lo anteriormente mencionado firmo a continuación:
            	</td>
            </tr>

        	<tr>
            	<td width="100%" align="center"  colspan="2">   <br><br>
            	<img  style="width: 150px;" src="$url_firma" alt="Logo">
            	<br><br>
                $nombre_usuario_firma</td>
        	</tr>

        	<tr>
            	<td width="100%"> <b>Observaciones:  </b>$observaciones<br></td>
            </tr>
    	</table>
    	</body>
EOF;

        $this->setY($this->customy);
        $this->writeHTML($html);

        return $this->firma;
*/

//    }
    function reporteGeneral($maestro){

        $codigo_hist = $maestro[0]['codigo_hist'];
        $denominacion_hist = $maestro[0]['denominacion_hist'];
        $descripcion_hist = $maestro[0]['descripcion_hist'];

        $columasconcepto = '';
        $hGlobal=1;
//        $numeracion = $this->MultiCell(10, $hGlobal,'',1,'C',false,0,'','',true,0,false,true,0,'T',false);

        foreach ($maestro as $datomaestro) {
            $columasconcepto = $columasconcepto . '<tr>
                     <td width="8%" align="right">' . $datomaestro['fecha_inicio'] . '</td>
                     <td width="8%" align="right">' . $datomaestro['fecha_fin'] . '</td>
                     <td width="10%" align="right">' . $datomaestro['monto_compra_orig_hist'] . '</td>
                     <td width="10%" align="right">' . $datomaestro['monto_compra_orig_100_hist'] . '</td>
                     <td width="10%" align="right">' . $datomaestro['monto_compra_hist'] . '</td>
                     <td width="10%" align="right">' . $datomaestro['nro_cbte_asociado_hist'] . '</td>
                     <td width="8%" align="right">' . $datomaestro['fecha_cbte_asociado_hist'] . '</td>
                     <td width="10%" align="right">' . $datomaestro['tramite_compra_hist'] . '</td>
                     <td width="11%" align="center">' . $datomaestro['funcionario_responsable'] . '</td>
                     <td width="11%" align="center">' . $datomaestro['nombre_unidad'] . '</td>
        		 </tr>';
        }

        $this->AddPage('L');
        $this->SetMargins(10, 50, 10);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


        $html = <<<EOF
		<style>
		table, th, td {
   			/*border: 1px solid black;*/
   			border-collapse: collapse; 
   			font-family: "Times New Roman";
   			font-size: 10pt;
		}
		</style>
		<body>
		<table border="none">
		    <tr>
            	<td width="95%"><b>Código:  </b>$codigo_hist</td>
            </tr>
            <tr>
            	<td width="95%"><b>Denominación:  </b>$denominacion_hist</td>
            </tr>
            <tr>
            	<td width="95%"><b>Descripción:  </b>$descripcion_hist</td>
            </tr>
		 </table>
		 <p></p>
		 <table border="1">
		    <tr>
            	<td width="8%" align="center"> <b>Fecha Inicio</b><br></td>
                <td width="8%" align="center"> <b>Fecha Fin</b><br></td>
                <td width="10%" align="center"> <b>Monto (87%)</b><br></td>
                <td width="10%" align="center"> <b>Monto (100%)</b><br></td>
                <td width="10%" align="center"> <b>Valor Actual</b><br></td>
                <td width="10%" align="center"> <b>C31</b><br></td>
                <td width="8%" align="center"> <b>Fecha Comp. C31</b><br></td>
                <td width="10%" align="center"> <b>Nº de trámite</b><br></td>
                <td width="11%" align="center"> <b>Responsable</b><br></td>
                <td width="11%" align="center"> <b>Unidad Solicitante</b><br></td>
            </tr>
            $columasconcepto
		 </table>
    	</body>
EOF;
        $this->setY($this->customy);
        $this->writeHTML($html);
    }





}

?>