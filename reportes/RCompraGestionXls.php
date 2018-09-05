<?php
class RCompraGestionXls
{
    private $docexcel;
    private $objWriter;
    private $nombre_archivo;
    private $hoja;
    private $columnas=array();
    private $fila;
    private $equivalencias=array();

    private $indice, $m_fila, $titulo;
    private $swEncabezado=0; //variable que define si ya se imprimi� el encabezado
    private $objParam;
    public  $url_archivo;

    var $datos_titulo;
    var $datos_detalle;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
    var $s1;
    var $t1;
    var $tg1;
    var $total;
    var $datos_entidad;
    var $datos_periodo;
    var $ult_codigo_partida;
    var $ult_concepto;



    function __construct(CTParametro $objParam){
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
        //ini_set('memory_limit','512M');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize'  => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator("PXP")
            ->setLastModifiedBy("PXP")
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "'.$this->objParam->getParametro('titulo_archivo').'", generado por el framework PXP')
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");

        $this->docexcel->setActiveSheetIndex(0);

        $this->docexcel->getActiveSheet()->setTitle($this->objParam->getParametro('titulo_archivo'));

        /*$this->docexcel->getActiveSheet()->getPageSetup()>setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $this->docexcel->getActiveSheet()->getPageSetup()>setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);*/


        $this->equivalencias=array(0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
            9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',
            18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z',
            26=>'AA',27=>'AB',28=>'AC',29=>'AD',30=>'AE',31=>'AF',32=>'AG',33=>'AH',
            34=>'AI',35=>'AJ',36=>'AK',37=>'AL',38=>'AM',39=>'AN',40=>'AO',41=>'AP',
            42=>'AQ',43=>'AR',44=>'AS',45=>'AT',46=>'AU',47=>'AV',48=>'AW',49=>'AX',
            50=>'AY',51=>'AZ',
            52=>'BA',53=>'BB',54=>'BC',55=>'BD',56=>'BE',57=>'BF',58=>'BG',59=>'BH',
            60=>'BI',61=>'BJ',62=>'BK',63=>'BL',64=>'BM',65=>'BN',66=>'BO',67=>'BP',
            68=>'BQ',69=>'BR',70=>'BS',71=>'BT',72=>'BU',73=>'BV',74=>'BW',75=>'BX',
            76=>'BY',77=>'BZ');

    }

    function setDatos ($param) {
        $this->datos = $param;
    }

    function generarReporte(){

        $this->imprimeDatos();

        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);


    }

    function imprimeDatos(){

        $datos = $this->datos;
        $columnas = 0;


        $numberFormat = '#,#0.##;[Red]-#,#0.##';

        $this->docexcel->setActiveSheetIndex(0);
        $sheet0 = $this->docexcel->getActiveSheet();

        $sheet0->setTitle('Compras x Gestión');

        //$datos = $this->objParam->getParametro('datos');
		//capture datas of the view BVP
		$selected = $this->objParam->getParametro('gestion_multi');        
		$hiddes = explode(',', $selected);
		$gscod = '';
		$gsdes = '';				
		$gsfec = '';		
		$gsmun = '';
		$gsf31 = '';
		$gsfei = '';
		$gsvit = '';
		$gsviu = '';
		$gsimp = '';
		$gsgmon = '';		
												
		for ($i=0; $i <count($hiddes) ; $i++) {
		switch ($hiddes[$i]) {
			case 'gcod': $gscod = 'cod'; break;
			case 'gdes': $gsdes = 'des'; break;
			case 'gfec': $gsfec = 'fec'; break;			
			case 'gnum': $gsmun = 'mun'; break;
			case 'gf31': $gsf31 = 'f31'; break;
			case 'gfei': $gsfei = 'fei'; break;
			case 'gvit': $gsvit = 'vit'; break;
			case 'gviu': $gsviu = 'viu'; break;			
			case 'gimp': $gsimp = 'imp'; break;
			case 'gmon': $gsgmon = 'mon'; break;														
			}									 			
		} 
		/////BVP		
        $sheet0->getColumnDimension('B')->setWidth(7);
        $sheet0->getColumnDimension('C')->setWidth(20);
        $sheet0->getColumnDimension('D')->setWidth(40);
        $sheet0->getColumnDimension('E')->setWidth(10);
        $sheet0->getColumnDimension('F')->setWidth(10);
        $sheet0->getColumnDimension('G')->setWidth(10);
        $sheet0->getColumnDimension('H')->setWidth(10);
        $sheet0->getColumnDimension('I')->setWidth(10);
        $sheet0->getColumnDimension('J')->setWidth(10);
        $sheet0->getColumnDimension('K')->setWidth(15);
        $sheet0->getColumnDimension('L')->setWidth(15);



        //$this->docexcel->getActiveSheet()->mergeCells('A1:A3');
        $sheet0->mergeCells('B1:L1');
        $sheet0->setCellValue('B1', 'DEPARTAMENTO ACTIVOS FIJOS');
        $sheet0->mergeCells('B2:L2');
        $sheet0->setCellValue('B2', 'COMPRAS DE GESTIÓN');
        $sheet0->mergeCells('B3:L3');
        $sheet0->setCellValue('B3', 'Del: '.$this->objParam->getParametro('fecha_ini').' Al '.$this->objParam->getParametro('fecha_fin').' Estado: '.$this->objParam->getParametro('estado'));


        $styleTitulos = array(
            'font' => array(
                'bold' => true,
                'size' => 8,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '768290'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));

        $styleActivos = array(
            'font' => array(
                'bold' => false,
                'size' => 8,
                'name' => 'Arial'
            )
        );




        //$this->docexcel->getActiveSheet()->getStyle('A1:T15000')->getAlignment()->setWrapText(true);

        $styleCabeza = array(
            'font' => array(
                'bold' => true,
                'size' => 8,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '768290'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                )
            )
        );


        $sheet0->getStyle('B1:L3')->applyFromArray($styleCabeza);
        /*$sheet0->getStyle('B2:L2')->applyFromArray($styleTitulos);
        $sheet0->getStyle('B3:L3')->applyFromArray($styleTitulos);*/

        $styleTitulos['fill']['color']['rgb'] = '8DB4E2';
        $styleTitulos['fill']['color']['rgb'] = 'CCBBAA';

        $sheet0->getRowDimension('5')->setRowHeight(35);
        $sheet0->getStyle('B5:L5')->applyFromArray($styleTitulos);
        $sheet0->getStyle('C5:L5')->getAlignment()->setWrapText(true);


        //*************************************Cabecera*****************************************

        $sheet0->setCellValue('B5', 'Nº');

        $sheet0->setCellValue('C5', 'CODIGO');
        if($this->objParam->getParametro('desc_nombre') == 'desc') {
            $sheet0->setCellValue('D5', 'DESCRIPCIÓN');
        }else{
            $sheet0->setCellValue('D5', 'DENOMINACIÓN');
        }

        $sheet0->setCellValue('E5', 'FECHA COMPRA');

        $sheet0->setCellValue('F5', 'NUM COMP.');

        $sheet0->setCellValue('G5', 'FECHA COMP C31');

        $sheet0->setCellValue('H5', 'FECHA INI DEPRE.');//monto_compra

        $sheet0->setCellValue('I5', 'VIDA UTIL ORIGINAL');

        $sheet0->setCellValue('J5', 'VIDA UTIL RESTANTE');

        $sheet0->setCellValue('K5', 'IMPORTE 100%');

        $sheet0->setCellValue('L5', 'MONTO 87%');


        //*************************************Fin Cabecera*****************************************

        $fila = 6;
        $codigo = '';
        $nombre = '';

        $cont_87 = 0;
        $cont_100 = 0;
        $contador = 1;
        $total_general_87 = 0;
        $total_general_100 = 0;

        $total_grupo_87 = 0;
        $total_grupo_100 = 0;

        //************************************************Detalle***********************************************
	//delete columns selected BVP					
	($gscod=='cod')?$this->docexcel->getActiveSheet()->getColumnDimension('C')->setVisible(0):'';
	($gsdes=='des')?$this->docexcel->getActiveSheet()->getColumnDimension('D')->setVisible(0):'';
	($gsfec=='fec')?$this->docexcel->getActiveSheet()->getColumnDimension('E')->setVisible(0):'';
	($gsmun=='mun')?$this->docexcel->getActiveSheet()->getColumnDimension('F')->setVisible(0):'';
	($gsf31=='f31')?$this->docexcel->getActiveSheet()->getColumnDimension('G')->setVisible(0):'';
	($gsfei=='fei')?$this->docexcel->getActiveSheet()->getColumnDimension('H')->setVisible(0):'';
	($gsvit=='vit')?$this->docexcel->getActiveSheet()->getColumnDimension('I')->setVisible(0):'';
	($gsviu=='viu')?$this->docexcel->getActiveSheet()->getColumnDimension('J')->setVisible(0):'';
	($gsimp=='imp')?$this->docexcel->getActiveSheet()->getColumnDimension('K')->setVisible(0):'';
	($gsgmon=='mon')?$this->docexcel->getActiveSheet()->getColumnDimension('L')->setVisible(0):'';	
	///		

        $tipo = $this->objParam->getParametro('tipo_reporte');
        $sheet0->getRowDimension('5')->setRowHeight(35);

        foreach($datos as $value) {

            if($value['nivel'] == 0 || $value['nivel'] == 1) {

                if ($codigo != '' && ($value['nivel'] == 0 || $value['nivel'] == 1 && $cont_87>0)) {
                    $total_general_87 = $total_general_87 + $cont_87;
                    $total_general_100 = $total_general_100 + $cont_100;
                    $styleTitulos['fill']['color']['rgb'] = '4b9bd1';
                    $sheet0->getStyle('B' . $fila . ':L' . $fila)->applyFromArray($styleTitulos);
                    $sheet0->getStyle('B' . $fila . ':L' . $fila)->getAlignment()->setWrapText(true);
                    if($tipo == 1) {
                        $sheet0->getStyle('K'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                        $sheet0->getStyle('L'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                        $sheet0->getStyle('D'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $sheet0->getStyle('K'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $sheet0->getStyle('L'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, '');
                        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, '');
                        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, 'Total Parcial');
                        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, '');
                        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, '');
                        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, '');
                        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, '');
                        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, '');
                        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila, '');
                        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $fila, $cont_100);
                        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $fila, $cont_87);

                        $fila++;
                    }

                    $total_grupo_100 += $cont_100;
                    $total_grupo_87 += $cont_87;
                    $cont_100 = 0;
                    $cont_87 = 0;
                    if($value['nivel'] == 0 && $codigo != $value['codigo_completo']){
                        if($tipo == 1) {
                            $styleTitulos['fill']['color']['rgb'] = '4b9bd1';
                            $sheet0->getStyle('B' . $fila . ':L' . $fila)->applyFromArray($styleTitulos);
                            $sheet0->getStyle('B' . $fila . ':L' . $fila)->getAlignment()->setWrapText(true);
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, '');
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, '');
                            $sheet0->getStyle('D'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, 'Total Final Grupo (' . $codigo . ')');
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, '');
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, '');
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, '');
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, '');
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, '');
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila, '');

                            $sheet0->getStyle('K'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                            $sheet0->getStyle('L'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                            $sheet0->getStyle('K'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $sheet0->getStyle('L'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $fila, $total_grupo_100);
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $fila, $total_grupo_87);
                            $fila ++;
                        }else{
                            $styleTitulos['fill']['color']['rgb'] = '4b9bd1';
                            $sheet0->getStyle('B'.$fila.':L'.$fila)->applyFromArray($styleTitulos);
                            $sheet0->getStyle('B'.$fila.':L'.$fila)->getAlignment()->setWrapText(true);
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, $contador);
                            $sheet0->getStyle('C'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $codigo);
                            $sheet0->getStyle('D'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $nombre);
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, '');
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, '');
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, '');
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, '');
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, '');
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila, '');
                            $sheet0->getStyle('K'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                            $sheet0->getStyle('L'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                            $sheet0->getStyle('K'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $sheet0->getStyle('L'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $fila, $total_grupo_100);
                            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $fila, $total_grupo_87);
                            $contador++;
                            $fila ++;
                        }
                        $total_grupo_100 = 0;
                        $total_grupo_87 = 0;

                    }

                }

                if($tipo == 1) {
                    $styleTitulos['fill']['color']['rgb'] = 'e09e1a';
                    $sheet0->getStyle('B' . $fila . ':L' . $fila)->applyFromArray($styleTitulos);
                    $sheet0->getStyle('B' . $fila . ':L' . $fila)->getAlignment()->setWrapText(true);

                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, '');
                    $sheet0->getStyle('C'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $value['codigo_completo']);
                    $sheet0->getStyle('D'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $value['nombre']);
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, '');
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, '');
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, '');
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, '');
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, '');
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila, '');
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $fila, '');
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $fila, '');
                    $fila ++;
                }

                if($value['nivel'] == 0){
                    $codigo = $value['codigo_completo'];
                    $nombre = $value['nombre'];
                }
            }else {
                if($tipo == 1) {
                    $styleTitulos['fill']['color']['rgb'] = 'e6e8f4';
                    $sheet0->getStyle('B' . $fila . ':L' . $fila)->applyFromArray($styleTitulos);
                    $sheet0->getStyle('B' . $fila . ':L' . $fila)->getAlignment()->setWrapText(true);

                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, $contador);
                    $sheet0->getStyle('C'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $value['codigo_af']);
                    $sheet0->getStyle('D'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $value['denominacion']);
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, date("d/m/Y", strtotime($value['fecha_compra'])));
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, $value['nro_cbte_asociado']);
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, date("d/m/Y", strtotime($value['fecha_cbte_asociado'])));
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, $value['fecha_ini_dep']);
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, $value['vida_util_original']);
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila, $value['-']);
                    $sheet0->getStyle('K'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet0->getStyle('L'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $sheet0->getStyle('K'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                    $sheet0->getStyle('L'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $fila, $value['monto_compra_orig_100']);
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $fila, $value['monto_compra_orig']);

                    $contador++;
                    $fila++;
                }
                $cont_100 = $cont_100 + $value['monto_compra_orig_100'];
                $cont_87  = $cont_87 + $value['monto_compra_orig'];
            }


        }
        //************************************************Fin Detalle***********************************************

        $total_general_87 = $total_general_87 + $cont_87;
        $total_general_100 = $total_general_100 + $cont_100;

        $styleTitulos['fill']['color']['rgb'] = '4b9bd1';
        $sheet0->getStyle('B'.$fila.':L'.$fila)->applyFromArray($styleTitulos);
        $sheet0->getStyle('B'.$fila.':L'.$fila)->getAlignment()->setWrapText(true);
        if($tipo == 1) {
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, '');
            $sheet0->getStyle('D'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, 'Total Parcial');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila, '');
            $sheet0->getStyle('K'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $sheet0->getStyle('L'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $sheet0->getStyle('K'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
            $sheet0->getStyle('L'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $fila, $cont_100);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $fila, $cont_87);

            $fila ++;

            $sheet0->getStyle('D'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $sheet0->getStyle('K'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
            $sheet0->getStyle('L'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
            $sheet0->getStyle('K'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $sheet0->getStyle('L'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, 'Total Final Grupo (' . $codigo . ')');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $fila, $total_grupo_100 + $cont_100);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $fila, $total_grupo_87 + $cont_87);
        }else{

            $sheet0->getStyle('C'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $sheet0->getStyle('D'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, $contador);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $codigo);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $nombre);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, '');
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila, '');
            $sheet0->getStyle('K'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
            $sheet0->getStyle('L'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
            $sheet0->getStyle('K'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $sheet0->getStyle('L'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $fila, $total_grupo_100 + $cont_100);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $fila, $total_grupo_87 + $cont_87);
        }
        $fila ++;

        $styleTitulos['fill']['color']['rgb'] = '4b9bd1';
        $sheet0->getStyle('B'.$fila.':L'.$fila)->applyFromArray($styleTitulos);
        $sheet0->getStyle('B'.$fila.':L'.$fila)->getAlignment()->setWrapText(true);

        $sheet0->getStyle('K'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
        $sheet0->getStyle('L'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
        $sheet0->getStyle('K'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet0->getStyle('L'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$fila,'');
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$fila,'');
        $sheet0->getStyle('D'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$fila,'TOTALES AF');
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$fila,'');
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$fila,'');
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$fila,'');
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$fila,'');
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$fila,'');
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$fila,'');
        $sheet0->getStyle('K'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
        $sheet0->getStyle('L'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
        $sheet0->getStyle('K'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet0->getStyle('L'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$fila,$total_general_100);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$fila,$total_general_87);

    }
}

?>