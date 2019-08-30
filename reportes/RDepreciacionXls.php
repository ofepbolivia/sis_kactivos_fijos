<?php
class RDepreciacionXls
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


        $numberFormat = '#,#0.##;[Red]-#,#0.##; #,#0.##;';
		$numberFormatporc = '#,##0.00';

        $this->docexcel->setActiveSheetIndex(0);
        $sheet0 = $this->docexcel->getActiveSheet();

        $sheet0->setTitle('Depreciación AF');

        //$datos = $this->objParam->getParametro('datos');

        $sheet0->getColumnDimension('B')->setWidth(7);
        $sheet0->getColumnDimension('C')->setWidth(20);
        $sheet0->getColumnDimension('D')->setWidth(25);
        $sheet0->getColumnDimension('E')->setWidth(10);
        $sheet0->getColumnDimension('F')->setWidth(10);
        $sheet0->getColumnDimension('G')->setWidth(10);
        $sheet0->getColumnDimension('H')->setWidth(10);
        $sheet0->getColumnDimension('I')->setWidth(10);
        $sheet0->getColumnDimension('J')->setWidth(10);
        $sheet0->getColumnDimension('K')->setWidth(15);
        $sheet0->getColumnDimension('L')->setWidth(15);
        $sheet0->getColumnDimension('L')->setWidth(12);
        $sheet0->getColumnDimension('M')->setWidth(10);
        $sheet0->getColumnDimension('N')->setWidth(10);
        $sheet0->getColumnDimension('O')->setWidth(10);
        $sheet0->getColumnDimension('P')->setWidth(10);
        //$sheet0->getColumnDimension('Q')->setWidth(10);
        /*$sheet0->getColumnDimension('R')->setWidth(10);
        $sheet0->getColumnDimension('S')->setWidth(10);
        $sheet0->getColumnDimension('T')->setWidth(10);
        $sheet0->getColumnDimension('U')->setWidth(10);
        $sheet0->getColumnDimension('V')->setWidth(10);*/


        //$this->docexcel->getActiveSheet()->mergeCells('A1:A3');
        $title = "DETALLE DE DEPRECIACION DE ACTIVOS FIJOS";
        $codigo = $datos[0]['codigo'];
        $codigo == "11" && $title = "DETALLE DE AMORTIZACION DE ACTIVOS FIJOS INTANGIBLES";

        $sheet0->mergeCells('B1:Q1');
        $sheet0->setCellValue('B1', 'BOLIVIANA DE AVIACIÓN');
        $sheet0->mergeCells('B2:Q2');
        $sheet0->setCellValue('B2', $title);
        $sheet0->mergeCells('B3:Q3');
        $sheet0->setCellValue('B3', ' Al: '.date_format(date_create($this->objParam->getParametro('fecha_hasta')), 'd/m/Y'));
        $sheet0->mergeCells('B4:Q4');
        $sheet0->setCellValue('B4', 'Formato Reporte Antiguo');


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


        $sheet0->getStyle('B1:Q4')->applyFromArray($styleCabeza);
        /*$sheet0->getStyle('B2:L2')->applyFromArray($styleTitulos);
        $sheet0->getStyle('B3:L3')->applyFromArray($styleTitulos);*/

        $styleTitulos['fill']['color']['rgb'] = '8DB4E2';
        $styleTitulos['fill']['color']['rgb'] = 'CCBBAA';

        $sheet0->getRowDimension('6')->setRowHeight(35);
        $sheet0->getStyle('B6:P6')->applyFromArray($styleTitulos);
        $sheet0->getStyle('C6:P6')->getAlignment()->setWrapText(true);

		$descnom=$this->objParam->getParametro('desc_nombre');
		switch ($descnom) {
			case 'desc' :$desno='DESCRIPCIÓN';break;
			case 'nombre' :$desno='DENOMINACIÓN';break;
			case 'ambos':$desno='NOMBRE/DESC.';break;
			default:$desno='DENOMINACIÓN';break;
		}
        //*************************************Cabecera*****************************************
        $depre_acu = 'DEP. ACUM. GEST. ANT.';
        $actu_acu  = 'ACT. DEPREC. GEST. ANT.';
        $depre_ges = 'DEP. GESTIÓN.';
        $depre_a = 'DEP. ACUM.';
        if ($codigo == "11"){
            $depre_acu = 'AMOR. ACUM. GEST. ANT.';
            $actu_acu = 'ACT. AMOR. GEST. ANT.';
            $depre_ges = 'AMOR. GESTIÓN';
            $depre_a = 'AMOR. ACUM';

        }
        $sheet0->setCellValue('B6', 'Nº');

        $sheet0->setCellValue('C6', 'CODIGO');

        $sheet0->setCellValue('D6', $desno);

        $sheet0->setCellValue('E6', 'INICIO DEP.');

        $sheet0->setCellValue('F6', 'VALOR COMPRA');

        $sheet0->setCellValue('G6', 'COSTO AF');

        $sheet0->setCellValue('H6', 'INC. X ACTUALIZ');

        $sheet0->setCellValue('I6', 'VALOR ACTUALIZ');

        $sheet0->setCellValue('J6', 'VU. ORIGINAL');

        $sheet0->setCellValue('K6', 'VU. RESIDUAL');

        $sheet0->setCellValue('L6', $depre_acu);

        $sheet0->setCellValue('M6', $actu_acu);

        $sheet0->setCellValue('N6', $depre_ges);
		
		//$sheet0->setCellValue('O6', '% DEP.G');

        $sheet0->setCellValue('O6', $depre_a);

        $sheet0->setCellValue('P6', 'VALOR RESIDUAL');		


        //*************************************Fin Cabecera*****************************************

        $fila = 7;
        $codigo = '';

        $cont_87 = 0;
        $cont_100 = 0;
        $contador = 1;
        $total_general_87 = 0;
        $total_general_100 = 0;

        //************************************************Detalle***********************************************



        $sheet0->getRowDimension('6')->setRowHeight(35);
        /*$sheet0->getStyle('B5:L5')->applyFromArray($styleTitulos);
        $sheet0->getStyle('C5:L5')->getAlignment()->setWrapText(true);*/
        foreach($datos as $value) {


            if($value['tipo'] == 'clasif') {

                $styleTitulos['fill']['color']['rgb'] = '4b9bd1';
                $sheet0->getStyle('B'.$fila.':P'.$fila)->applyFromArray($styleTitulos);
                $sheet0->getStyle('B'.$fila.':P'.$fila)->getAlignment()->setWrapText(true);
                $sheet0->getStyle('C'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet0->getStyle('D'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet0->getStyle('C'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet0->getStyle('D'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet0->getStyle('E'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet0->getStyle('F'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('G'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('H'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('I'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('L'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('M'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('N'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('O'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('P'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('F'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('G'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('H'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('I'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('L'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('M'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('N'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('O'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('P'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
				//$sheet0->getStyle('Q'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, '');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $value['codigo']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $value['denominacion']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, '-');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, $value['monto_vigente_orig_100']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, $value['monto_vigente_orig']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, $value['inc_actualiz']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, $value['monto_actualiz']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila,'-');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $fila,'-');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $fila, $value['depreciacion_acum_gest_ant']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12, $fila, $value['depreciacion_acum_actualiz_gest_ant']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13, $fila, $value['depreciacion_per']);
				//$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14, $fila, '');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14, $fila, $value['depreciacion_acum']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15, $fila, $value['monto_vigente']);
				


            }else if($value['tipo'] == 'detalle'){
                //$fecha_dep =  $value['fecha_ini_dep'] != '' ?date_format(date_create($value['fecha_ini_dep']), 'd/m/Y'):'';
                $codigo_1=substr($value['codigo'],0,2);
                $codigo_11=substr($value['codigo'],0,9);

                $styleTitulos['fill']['color']['rgb'] = 'e6e8f4';
                $sheet0->getStyle('B'.$fila.':P'.$fila)->applyFromArray($styleTitulos);
                $sheet0->getStyle('B'.$fila.':P'.$fila)->getAlignment()->setWrapText(true);
                $sheet0->getStyle('C'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet0->getStyle('D'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet0->getStyle('E'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet0->getStyle('F'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('G'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('H'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('I'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('L'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('M'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('N'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('O'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet0->getStyle('P'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                $sheet0->getStyle('F'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('G'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('H'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('I'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('L'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('M'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('N'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('O'.$fila)->getNumberFormat()->setFormatCode($numberFormatporc);
                $sheet0->getStyle('P'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
				//$sheet0->getStyle('Q'.$fila)->getNumberFormat()->setFormatCode($numberFormat);

                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, $contador);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $value['codigo']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $value['denominacion']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, date("d/m/Y", strtotime($value['fecha_ini_dep'])));
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, $value['monto_vigente_orig_100']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, $value['monto_vigente_orig']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, $value['inc_actualiz']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, $value['monto_actualiz']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila,($codigo_1=='01' || $codigo_11 == '11.01.05.')?'-':$value['vida_util_orig']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $fila,($codigo_1=='01' || $codigo_11 == '11.01.05.')?'-':$value['vida_util']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $fila, $value['depreciacion_acum_gest_ant']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12, $fila, $value['depreciacion_acum_actualiz_gest_ant']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13, $fila, $value['depreciacion_per']);
				//$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14, $fila, $value['porce_depre']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14, $fila, $value['depreciacion_acum']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15, $fila, $value['monto_vigente']);
				

                $contador++;

                $codigo = $value['codigo_completo'];
            }else{
                $styleTitulos['fill']['color']['rgb'] = '4b9bd1';
                $sheet0->getStyle('B'.$fila.':P'.$fila)->applyFromArray($styleTitulos);
                $sheet0->getStyle('B'.$fila.':P'.$fila)->getAlignment()->setWrapText(true);

                $sheet0->getStyle('C'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet0->getStyle('D'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet0->getStyle('E'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet0->getStyle('F'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('G'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('H'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('I'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('L'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('M'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('N'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('O'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sheet0->getStyle('P'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                $sheet0->getStyle('F'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('G'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('H'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('I'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('L'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('M'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('N'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('O'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
                $sheet0->getStyle('P'.$fila)->getNumberFormat()->setFormatCode($numberFormat);
				//$sheet0->getStyle('Q'.$fila)->getNumberFormat()->setFormatCode($numberFormat);

                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, '');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, 'TOTAL FINAL');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, '');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, '');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, $value['monto_vigente_orig_100']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, $value['monto_vigente_orig']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, $value['inc_actualiz']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, $value['monto_actualiz']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila,'');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $fila,'');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $fila, $value['depreciacion_acum_gest_ant']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12, $fila, $value['depreciacion_acum_actualiz_gest_ant']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13, $fila, $value['depreciacion_per']);
				//$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14, $fila, '');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14, $fila, $value['depreciacion_acum']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15, $fila, $value['monto_vigente']);
				
            }

            $fila++;
        }
        //************************************************Fin Detalle***********************************************

        
    }
}

?>