<?php

class RDetalleDepreciacionXLS
{
    private $docexcel;
    private $objWriter;
    private $nombre_archivo;
    private $hoja;
    private $columnas=array();
    private $fila;
    //private $equivalencias=array();

    private $indice, $m_fila, $titulo;
    private $swEncabezado=0; //variable que define si ya se imprimi el encabezado
    private $objParam;
    public  $url_archivo;
    private $resumen = array();
    private $resumen_regional = array();

    function __construct(CTParametro $objParam){

        //reducido menos 23,24,26,27,29,30
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
            ->setKeywords("office 2013 openxml php")
            ->setCategory("Report File");



        /*$this->equivalencias=array(0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
            9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',
            18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z',
            26=>'AA',27=>'AB',28=>'AC',29=>'AD',30=>'AE',31=>'AF',32=>'AG',33=>'AH',
            34=>'AI',35=>'AJ',36=>'AK',37=>'AL',38=>'AM',39=>'AN',40=>'AO',41=>'AP',
            42=>'AQ',43=>'AR',44=>'AS',45=>'AT',46=>'AU',47=>'AV',48=>'AW',49=>'AX',
            50=>'AY',51=>'AZ',
            52=>'BA',53=>'BB',54=>'BC',55=>'BD',56=>'BE',57=>'BF',58=>'BG',59=>'BH',
            60=>'BI',61=>'BJ',62=>'BK',63=>'BL',64=>'BM',65=>'BN',66=>'BO',67=>'BP',
            68=>'BQ',69=>'BR',70=>'BS',71=>'BT',72=>'BU',73=>'BV',74=>'BW',75=>'BX',
            76=>'BY',77=>'BZ');*/

    }

    function imprimeDatos()
    {
        $numberFormat = '#,#0.##;[Red]-#,#0.##';

        $this->docexcel->setActiveSheetIndex(0);
        $sheet0 = $this->docexcel->getActiveSheet();

        $sheet0->setTitle('Reporte Activos Depreciación');

        $datos = $this->objParam->getParametro('datos');

        $sheet0->getColumnDimension('A')->setWidth(7);
        $sheet0->getColumnDimension('B')->setWidth(25);
        $sheet0->getColumnDimension('C')->setWidth(30);
        $sheet0->getColumnDimension('D')->setWidth(40);
        $sheet0->getColumnDimension('E')->setWidth(15);
        $sheet0->getColumnDimension('F')->setWidth(10);
        $sheet0->getColumnDimension('G')->setWidth(15);
        $sheet0->getColumnDimension('H')->setWidth(15);
        $sheet0->getColumnDimension('I')->setWidth(15);
        $sheet0->getColumnDimension('J')->setWidth(15);
        $sheet0->getColumnDimension('K')->setWidth(15);
        $sheet0->getColumnDimension('L')->setWidth(12);
        $sheet0->getColumnDimension('M')->setWidth(10);
        $sheet0->getColumnDimension('N')->setWidth(10);
        $sheet0->getColumnDimension('O')->setWidth(10);
        $sheet0->getColumnDimension('P')->setWidth(10);
        $sheet0->getColumnDimension('Q')->setWidth(10);
        $sheet0->getColumnDimension('R')->setWidth(10);
        $sheet0->getColumnDimension('S')->setWidth(10);
        $sheet0->getColumnDimension('T')->setWidth(10);
        $sheet0->getColumnDimension('U')->setWidth(10);
        $sheet0->getColumnDimension('V')->setWidth(10);


        //$this->docexcel->getActiveSheet()->mergeCells('A1:A3');
        $sheet0->mergeCells('A1:V1');
        //$sheet0->setCellValue('A1', 'BOLIVIANA DE AVIACION'); //fRnk: comentado y vacio
        $sheet0->setCellValue('A1', '');
        $sheet0->mergeCells('A2:V2');
        $sheet0->setCellValue('A2', 'DETALLE DEPRECIACION DE ACTIVOS FIJOS');
        $sheet0->mergeCells('A3:V3');
        $sheet0->setCellValue('A3', 'Al:');


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


        $sheet0->getStyle('A1:V1')->applyFromArray($styleTitulos);
        $sheet0->getStyle('A2:V2')->applyFromArray($styleTitulos);
        $sheet0->getStyle('A3:V3')->applyFromArray($styleTitulos);

        $styleTitulos['fill']['color']['rgb'] = '8DB4E2';
        $styleTitulos['fill']['color']['rgb'] = 'CCBBAA';

        $sheet0->getRowDimension('5')->setRowHeight(35);
        $sheet0->getStyle('A5:V5')->applyFromArray($styleTitulos);
        $sheet0->getStyle('C5:V5')->getAlignment()->setWrapText(true);


        //*************************************Cabecera*****************************************

        $sheet0->setCellValue('A5', 'Nº');

        $sheet0->setCellValue('B5', 'CODIGO');

        $sheet0->setCellValue('C5', 'DESCRIPCION/NOMBRE');

        $sheet0->setCellValue('D5', 'DESCRIPCION/NOMBRE LARGA');

        $sheet0->setCellValue('E5', 'FECHA INIDEP/COMPRA');

        $sheet0->setCellValue('F5', 'COMP. 100%');

        $sheet0->setCellValue('G5', 'COMP. 87%');//monto_compra

        $sheet0->setCellValue('H5', 'VALOR ACTUALIZ. GEST. ANT.');

        $sheet0->setCellValue('I5', 'ACTUALIZ / GESTION ANTERIOR');

        $sheet0->setCellValue('J5', 'INC. ACTUALIZ / GESTION ACTUAL');

        $sheet0->setCellValue('K5', 'INC. ACTUALIZ DEL PERIODO');

        $sheet0->setCellValue('L5', 'MONTO ACTUALIZADO');

        $sheet0->setCellValue('M5', 'VIDA USADA');

        $sheet0->setCellValue('N5', 'VIDA RESI');

        $sheet0->setCellValue('O5', 'DEP. ACUM. GEST. ANT');

        $sheet0->setCellValue('P5', 'DEP ACT. GEST. ANT.');

        $sheet0->setCellValue('Q5', 'DEP. GESTION');

        $sheet0->setCellValue('R5', 'DEP. PERIODO');

        $sheet0->setCellValue('S5', 'DEP. ACUMULADA');

        $sheet0->setCellValue('T5', 'VAL RESIDUAL');

        $sheet0->setCellValue('U5', 'COMPRAS GESTION');

        $sheet0->setCellValue('V5', 'REVA/AJUS GESTION');

        //*************************************Datos a Imprimir*****************************************

        $numero = 1;
        $columna = 0;
        $fila = 7;
        $codigo_tipo = "";
        $tipo = "";
        $codigo_subtipo = "";
        $codigo_rama = "";

        $fila_det = 4;

        $columna_det = 0;

        $cont_col1=0;//importe_100
        $cont_col2=0;//monto_compra
        $cont_col3=0;//monto_vigente
        $cont_col4 =0;//actualizacion_gestion_anterior
        $cont_col5 =0;//actualizacion_gestion_actual
        $cont_col6 =0;//actualizacion_periodo
        $cont_col7 =0;//monto_actualiz
        $cont_col8 =0;//vida_usada
        $cont_col9 =0;//vida_util
        $cont_col10 =0;//depreciacion_acum_gestion_anterior
        $cont_col11 =0;//depre_actu_gestion_anterior
        $cont_col12 =0;//depreciacion_gestion
        $cont_col13 =0;//depreciacion_periodo
        $cont_col14 =0;//depreciacion_acum
        $cont_col15 =0;//valor_residual
        $cont_col16 =0;//compras gestion
        $cont_col17 =0;//reva_aju gestion


        $total_col1 = 0;
        $total_col2 = 0;
        $total_col3 = 0;
        $total_col4 = 0;
        $total_col5 = 0;
        $total_col6 = 0;
        $total_col7 = 0;
        $total_col8 = 0;
        $total_col9 = 0;
        $total_col10 = 0;
        $total_col11 = 0;
        $total_col12 = 0;
        $total_col13 = 0;
        $total_col14 = 0;
        $total_col15 = 0;
        $total_col16 =0;//compras gestion
        $total_col17 =0;//reva_aju gestion

        $cont_grupo = 1;

        $sheetId = 1;
        $this->docexcel->createSheet(NULL, $sheetId);
        $this->docexcel->getSheet(1)->setTitle('Resumen Totales');
        $sheet1 = $this->docexcel->getSheet(1);


        $sheet1->getColumnDimension('A')->setWidth(7);
        $sheet1->getColumnDimension('B')->setWidth(25);
        $sheet1->getColumnDimension('C')->setWidth(30);
        $sheet1->getColumnDimension('D')->setWidth(15);
        $sheet1->getColumnDimension('E')->setWidth(15);
        $sheet1->getColumnDimension('F')->setWidth(10);
        $sheet1->getColumnDimension('G')->setWidth(20);
        $sheet1->getColumnDimension('H')->setWidth(20);
        $sheet1->getColumnDimension('I')->setWidth(15);
        $sheet1->getColumnDimension('J')->setWidth(15);
        $sheet1->getColumnDimension('K')->setWidth(10);
        $sheet1->getColumnDimension('L')->setWidth(10);
        $sheet1->getColumnDimension('M')->setWidth(10);
        $sheet1->getColumnDimension('N')->setWidth(10);
        $sheet1->getColumnDimension('O')->setWidth(10);
        $sheet1->getColumnDimension('P')->setWidth(10);
        $sheet1->getColumnDimension('Q')->setWidth(12);
        $sheet1->getColumnDimension('R')->setWidth(12);
        $sheet1->getColumnDimension('S')->setWidth(12);
        $sheet1->getColumnDimension('T')->setWidth(12);


        $sheet1->getRowDimension('2')->setRowHeight(35);

        $sheet1->mergeCells('B1:T1');
        $sheet1->setCellValue('B1', 'DETALLE TOTALES POR GRUPOS');
        $sheet1->getStyle('B1:T1')->applyFromArray($styleTitulos);

        //$this->docexcel->getSheet(1)->getStyle('A2:R2')->applyFromArray($styleTitulos);


        $styleTitulos['fill']['color']['rgb'] = 'CCBBAA';

        $sheet1->getStyle('B2:T2')->applyFromArray($styleTitulos);
        $sheet1->getStyle('B2:T2')->getAlignment()->setWrapText(true);
        //$this->docexcel->getSheet(1)->setCellValue('A2', 'Nº');
        $sheet1->setCellValue('B2', 'TOTALES');

        $sheet1->setCellValue('C2', 'DESCRIPCION/NOMBRE');
        $sheet1->setCellValue('D2', 'COMP. 100%');
        $sheet1->setCellValue('E2', 'COMP. 87%');//monto_compra
        $sheet1->setCellValue('F2', 'VALOR ACTUALIZ. GEST. ANT.');
        $sheet1->setCellValue('G2', 'INC. ACTUALIZ / GESTION ANTERIOR');
        $sheet1->setCellValue('H2', 'INC. ACTUALIZ / GESTION ACTUAL');
        $sheet1->setCellValue('I2', 'INC. ACTUALIZ DEL PERIODO');
        $sheet1->setCellValue('J2', 'MONTO ACTUALIZADO');
        $sheet1->setCellValue('K2', 'VIDA USADA');
        $sheet1->setCellValue('L2', 'VIDA RESI');
        $sheet1->setCellValue('M2', 'DEP. ACUM. GEST. ANT');
        $sheet1->setCellValue('N2', 'DEP ACT. GEST. ANT.');
        $sheet1->setCellValue('O2', 'DEP. GESTION');
        $sheet1->setCellValue('P2', 'DEP. PERIODO');
        $sheet1->setCellValue('Q2', 'DEP. ACUMULADA');
        $sheet1->setCellValue('R2', 'VAL RESIDUAL');
        $sheet1->setCellValue('S2', 'COMPRAS GESTION');
        $sheet1->setCellValue('T2', 'REVA/AJUS GESTION');


        foreach ($datos as $record) {
           //print_r ($datos);exit;
            if ($record['codigo_tipo'] != $codigo_tipo) {
                if ($codigo_tipo != '') {
                    //imprimir totales del tipo

                    $sheet0->getStyle('B'.$fila.':V'.$fila)->applyFromArray($styleTitulos);
                    //$sheet0->mergeCells('B'.$fila.':E'.$fila);
                    $sheet0->setCellValueByColumnAndRow(1, $fila, 'Total Grupo '. $codigo_tipo);


                    $sheet1->getStyle('B'.$fila_det.':T'.$fila_det)->applyFromArray($styleTitulos);
                    $sheet1->setCellValueByColumnAndRow(1, $fila_det, 'Total Grupo '. $codigo_tipo);


                    //impresion de totales grupos
                    $sheet1->setCellValueByColumnAndRow(2, $fila_det, $tipo);


                    $sheet0->setCellValueByColumnAndRow(5, $fila, $cont_col1);
                    $sheet1->setCellValueByColumnAndRow(3, $fila_det, $cont_col1);


                    $sheet0->setCellValueByColumnAndRow(6, $fila, $cont_col2);
                    $sheet1->setCellValueByColumnAndRow(4, $fila_det, $cont_col2);

                    $sheet0->setCellValueByColumnAndRow(7, $fila, $cont_col3);
                    $sheet1->setCellValueByColumnAndRow(5, $fila_det, $cont_col3);

                    $sheet0->setCellValueByColumnAndRow(8, $fila, $cont_col4);
                    $sheet1->setCellValueByColumnAndRow(6, $fila_det, $cont_col4);

                    $sheet0->setCellValueByColumnAndRow(9, $fila, $cont_col5);;
                    $sheet1->setCellValueByColumnAndRow(7, $fila_det, $cont_col5);

                    $sheet0->setCellValueByColumnAndRow(10, $fila, $cont_col6);
                    $sheet1->setCellValueByColumnAndRow(8, $fila_det, $cont_col6);

                    $sheet0->setCellValueByColumnAndRow(11, $fila, $cont_col7);
                    $sheet1->setCellValueByColumnAndRow(9, $fila_det, $cont_col7);

                    $sheet0->setCellValueByColumnAndRow(12, $fila, $cont_col8);
                    $sheet1->setCellValueByColumnAndRow(10, $fila_det, $cont_col8);

                    $sheet0->setCellValueByColumnAndRow(13, $fila, $cont_col9);
                    $sheet1->setCellValueByColumnAndRow(11, $fila_det, $cont_col9);

                    $sheet0->setCellValueByColumnAndRow(14, $fila, $cont_col10);
                    $sheet1->setCellValueByColumnAndRow(12, $fila_det, $cont_col10);

                    $sheet0->setCellValueByColumnAndRow(15, $fila, $cont_col11);
                    $sheet1->setCellValueByColumnAndRow(13, $fila_det, $cont_col11);

                    $sheet0->setCellValueByColumnAndRow(16, $fila, $cont_col12);
                    $sheet1->setCellValueByColumnAndRow(14, $fila_det, $cont_col12);

                    $sheet0->setCellValueByColumnAndRow(17, $fila, $cont_col13);
                    $sheet1->setCellValueByColumnAndRow(15, $fila_det, $cont_col13);

                    $sheet0->setCellValueByColumnAndRow(18, $fila, $cont_col14);
                    $sheet1->setCellValueByColumnAndRow(16, $fila_det, $cont_col14);

                    $sheet0->setCellValueByColumnAndRow(19, $fila, $cont_col15);
                    $sheet1->setCellValueByColumnAndRow(17, $fila_det, $cont_col15);

                    $sheet0->setCellValueByColumnAndRow(20, $fila, $cont_col16);
                    $sheet1->setCellValueByColumnAndRow(18, $fila_det, $cont_col16);

                    $sheet0->setCellValueByColumnAndRow(21, $fila, $cont_col17);
                    $sheet1->setCellValueByColumnAndRow(19, $fila_det, $cont_col17);

                    $total_col1 = $total_col1 + $cont_col1;
                    $total_col2 = $total_col2 + $cont_col2;
                    $total_col3 = $total_col3 + $cont_col3;
                    $total_col4 = $total_col4 + $cont_col4;
                    $total_col5 = $total_col5 + $cont_col5;
                    $total_col6 = $total_col6 + $cont_col6;
                    $total_col7 = $total_col7 + $cont_col7;
                    $total_col8 = $total_col8 + $cont_col8;
                    $total_col9 = $total_col9 + $cont_col9;
                    $total_col10 = $total_col10 + $cont_col10;
                    $total_col11 = $total_col11 + $cont_col11;
                    $total_col12 = $total_col12 + $cont_col12;
                    $total_col13 = $total_col13 + $cont_col13;
                    $total_col14 = $total_col14 + $cont_col14;
                    $total_col15 = $total_col15 + $cont_col15;
                    $total_col16 = $total_col16 + $cont_col16;
                    $total_col17 = $total_col17 + $cont_col17;

                    $cont_col1=0;//importe_100
                    $cont_col2=0;//monto_compra
                    $cont_col3=0;//monto_vigente
                    $cont_col4 =0;//actualizacion_gestion_anterior
                    $cont_col5 =0;//actualizacion_gestion_actual
                    $cont_col6 =0;//actualizacion_periodo
                    $cont_col7 =0;//monto_actualiz
                    $cont_col8 =0;//vida_usada
                    $cont_col9 =0;//vida_util
                    $cont_col10 =0;//depreciacion_acum_gestion_anterior
                    $cont_col11 =0;//depre_actu_gestion_anterior
                    $cont_col12 =0;//depreciacion_gestion
                    $cont_col13 =0;//depreciacion_periodo
                    $cont_col14 =0;//depreciacion_acum
                    $cont_col15 =0;//valor_residual
                    $cont_col16 =0;//
                    $cont_col17 =0;//




                    $fila_det=$fila_det + 1;
                    $fila=$fila+2;

                }


                $sheet0->getStyle('B'.$fila.':V'.$fila)->applyFromArray($styleTitulos);
                //$sheet0->mergeCells('D'.$fila.':T'.$fila);
                $sheet0->setCellValueByColumnAndRow(1, $fila, $record['codigo_tipo'].' ');
                $sheet0->setCellValueByColumnAndRow(2, $fila, $record['tipo']);
                $codigo_tipo = $record['codigo_tipo'];
                $tipo = $record['tipo'];
                $fila++;
            }

            if ($record['codigo_subtipo'] != $codigo_subtipo) {
                $sheet0->getStyle('B'.$fila.':V'.$fila)->applyFromArray($styleTitulos);
                //$sheet0->mergeCells('D'.$fila.':T'.$fila);
                $sheet0->setCellValueByColumnAndRow(1, $fila, $record['codigo_subtipo'].' ');
                $sheet0->setCellValueByColumnAndRow(2, $fila, $record['subtipo']);
                $codigo_subtipo = $record['codigo_subtipo'];
                $fila++;
            }

            if ($record['codigo_rama'] != $codigo_rama) {
                $sheet0->getStyle('B'.$fila.':V'.$fila)->applyFromArray($styleTitulos);
                //$sheet0->mergeCells('D'.$fila.':T'.$fila);
                $sheet0->setCellValueByColumnAndRow(1, $fila, $record['codigo_rama']);
                $sheet0->setCellValueByColumnAndRow(2, $fila, $record['rama']);
                $codigo_rama = $record['codigo_rama'];
                $fila++;
            }

            $sheet0->getStyle('A'.$fila.':V'.$fila)->applyFromArray($styleActivos);

            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $numero++);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['codigo']);
            //$this->docexcel->getActiveSheet()->getStyle('C'.$fila)->getAlignment()->setWrapText(true);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['descripcion']);
            //$this->docexcel->getActiveSheet()->getStyle('D'.$fila)->getAlignment()->setWrapText(true);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['descripcion_larga']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['fecha_ini_dep']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['importe_100']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['monto_compra']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['monto_vigente']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['actualizacion_gestion_anterior']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['actualizacion_gestion_actual']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['actualizacion_periodo']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['monto_actualiz']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['vida_usada']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['vida_util']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['depreciacion_acum_gestion_anterior']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['depre_actu_gestion_anterior']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['depreciacion_gestion']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['depreciacion_periodo']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['depreciacion_acum']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['valor_residual']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['compra_gestion']);
            $sheet0->setCellValueByColumnAndRow($columna++, $fila, $record['ajuste_reva_gestion']);

            $cont_col1 = $cont_col1 + $record['importe_100'];
            $cont_col2 = $cont_col2 + $record['monto_compra'];
            $cont_col3 = $cont_col3 + $record['monto_vigente'];
            $cont_col4 = $cont_col4 + $record['actualizacion_gestion_anterior'];
            $cont_col5 = $cont_col5 + $record['actualizacion_gestion_actual'];
            $cont_col6 = $cont_col6 + $record['actualizacion_periodo'];
            $cont_col7 = $cont_col7 + $record['monto_actualiz'];
            $cont_col8 = $cont_col8 + $record['vida_usada'];
            $cont_col9 = $cont_col9 + $record['vida_util'];
            $cont_col10 = $cont_col10 + $record['depreciacion_acum_gestion_anterior'];
            $cont_col11 = $cont_col11 + $record['depre_actu_gestion_anterior'];
            $cont_col12 = $cont_col12 + $record['depreciacion_gestion'];
            $cont_col13 = $cont_col13 + $record['depreciacion_periodo'];
            $cont_col14 = $cont_col14 + $record['depreciacion_acum'];
            $cont_col15 = $cont_col15 + $record['valor_residual'];
            $cont_col16 = $cont_col16 + $record['compra_gestion'];
            $cont_col17 = $cont_col17 + $record['ajuste_reva_gestion'];


            if($columna == 22){
                $columna = 0;
            }
            $fila++;
            //************************************************Fin Datos a Imprimir***********************************************

        }

        $total_col1 = $total_col1 + $cont_col1;
        $total_col2 = $total_col2 + $cont_col2;
        $total_col3 = $total_col3 + $cont_col3;
        $total_col4 = $total_col4 + $cont_col4;
        $total_col5 = $total_col5 + $cont_col5;
        $total_col6 = $total_col6 + $cont_col6;
        $total_col7 = $total_col7 + $cont_col7;
        $total_col8 = $total_col8 + $cont_col8;
        $total_col9 = $total_col9 + $cont_col9;
        $total_col10 = $total_col10 + $cont_col10;
        $total_col11 = $total_col11 + $cont_col11;
        $total_col12 = $total_col12 + $cont_col12;
        $total_col13 = $total_col13 + $cont_col13;
        $total_col14 = $total_col14 + $cont_col14;
        $total_col15 = $total_col15 + $cont_col15;
        $total_col16 = $total_col16 + $cont_col16;
        $total_col17 = $total_col17 + $cont_col17;


        $sheet0->getStyle('B'.$fila.':V'.$fila)->applyFromArray($styleTitulos);
        //$sheet0->mergeCells('B'.$fila.':E'.$fila);
        $sheet0->setCellValueByColumnAndRow(1, $fila, 'Total Grupo '. $codigo_tipo);



        $sheet1->getStyle('B'.$fila_det.':T'.$fila_det)->applyFromArray($styleTitulos);
        $sheet1->setCellValueByColumnAndRow(1, $fila_det, 'Total Grupo '. $codigo_tipo);

        /*total del grupo*/
        $sheet1->setCellValueByColumnAndRow(2, $fila_det, $record['tipo']);

        $sheet0->setCellValueByColumnAndRow(5, $fila, $cont_col1);
        $sheet1->setCellValueByColumnAndRow(3, $fila_det, $cont_col1);

        $sheet0->setCellValueByColumnAndRow(6, $fila, $cont_col2);
        $sheet1->setCellValueByColumnAndRow(4, $fila_det, $cont_col2);

        $sheet0->setCellValueByColumnAndRow(7, $fila, $cont_col3);
        $sheet1->setCellValueByColumnAndRow(5, $fila_det, $cont_col3);

        $sheet0->setCellValueByColumnAndRow(8, $fila, $cont_col4);
        $sheet1->setCellValueByColumnAndRow(6, $fila_det, $cont_col4);

        $sheet0->setCellValueByColumnAndRow(9, $fila, $cont_col5);
        $sheet1->setCellValueByColumnAndRow(7, $fila_det, $cont_col5);

        $sheet0->setCellValueByColumnAndRow(10, $fila, $cont_col6);
        $sheet1->setCellValueByColumnAndRow(8, $fila_det, $cont_col6);

        $sheet0->setCellValueByColumnAndRow(11, $fila, $cont_col7);
        $sheet1->setCellValueByColumnAndRow(9, $fila_det, $cont_col7);

        $sheet0->setCellValueByColumnAndRow(12, $fila, $cont_col8);
        $sheet1->setCellValueByColumnAndRow(10, $fila_det, $cont_col8);

        $sheet0->setCellValueByColumnAndRow(13, $fila, $cont_col9);
        $sheet1->setCellValueByColumnAndRow(11, $fila_det, $cont_col9);

        $sheet0->setCellValueByColumnAndRow(14, $fila, $cont_col10);
        $sheet1->setCellValueByColumnAndRow(12, $fila_det, $cont_col10);

        $sheet0->setCellValueByColumnAndRow(15, $fila, $cont_col11);
        $sheet1->setCellValueByColumnAndRow(13, $fila_det, $cont_col11);

        $sheet0->setCellValueByColumnAndRow(16, $fila, $cont_col12);
        $sheet1->setCellValueByColumnAndRow(14, $fila_det, $cont_col12);

        $sheet0->setCellValueByColumnAndRow(17, $fila, $cont_col13);
        $sheet1->setCellValueByColumnAndRow(15, $fila_det, $cont_col13);

        $sheet0->setCellValueByColumnAndRow(18, $fila, $cont_col14);
        $sheet1->setCellValueByColumnAndRow(16, $fila_det, $cont_col14);

        $sheet0->setCellValueByColumnAndRow(19, $fila, $cont_col15);
        $sheet1->setCellValueByColumnAndRow(17, $fila_det, $cont_col15);

        $sheet0->setCellValueByColumnAndRow(20, $fila, $cont_col16);
        $sheet1->setCellValueByColumnAndRow(18, $fila_det, $cont_col16);

        $sheet0->setCellValueByColumnAndRow(21, $fila, $cont_col17);
        $sheet1->setCellValueByColumnAndRow(19, $fila_det, $cont_col17);

        $fila=$fila+2;

        $fila_det ++;

        /*imprime total final*/

        //$sheet0->mergeCells('B'.$fila.':E'.$fila);
        $sheet0->getStyle('B'.$fila.':V'.$fila)->applyFromArray($styleTitulos);
        $sheet0->setCellValueByColumnAndRow(1, $fila, 'TOTAL FINAL');

        //$sheet1->mergeCells('B'.$fila_det.':C'.$fila_det);
        $sheet1->setCellValueByColumnAndRow(1, $fila_det, 'TOTAL FINAL');
        $sheet1->getStyle('B'.$fila_det.':T'.$fila_det)->applyFromArray($styleTitulos);


        $sheet0->setCellValueByColumnAndRow(5, $fila, $total_col1);
        $sheet1->setCellValueByColumnAndRow(3, $fila_det, $total_col1);

        $sheet0->setCellValueByColumnAndRow(6, $fila, $total_col2);
        $sheet1->setCellValueByColumnAndRow(4, $fila_det, $total_col2);

        $sheet0->setCellValueByColumnAndRow(7, $fila, $total_col3);
        $sheet1->setCellValueByColumnAndRow(5, $fila_det, $total_col3);

        $sheet0->setCellValueByColumnAndRow(8, $fila, $total_col4);
        $sheet1->setCellValueByColumnAndRow(6, $fila_det, $total_col4);

        $sheet0->setCellValueByColumnAndRow(9, $fila, $total_col5);
        $sheet1->setCellValueByColumnAndRow(7, $fila_det, $total_col5);

        $sheet0->setCellValueByColumnAndRow(10, $fila, $total_col6);
        $sheet1->setCellValueByColumnAndRow(8, $fila_det, $total_col6);

        $sheet0->setCellValueByColumnAndRow(11, $fila, $total_col7);
        $sheet1->setCellValueByColumnAndRow(9, $fila_det, $total_col7);

        $sheet0->setCellValueByColumnAndRow(12, $fila, $total_col8);
        $sheet1->setCellValueByColumnAndRow(10, $fila_det, $total_col8);

        $sheet0->setCellValueByColumnAndRow(13, $fila, $total_col9);
        $sheet1->setCellValueByColumnAndRow(11, $fila_det, $total_col9);

        $sheet0->setCellValueByColumnAndRow(14, $fila, $total_col10);
        $sheet1->setCellValueByColumnAndRow(12, $fila_det, $total_col10);

        $sheet0->setCellValueByColumnAndRow(15, $fila, $total_col11);
        $sheet1->setCellValueByColumnAndRow(13, $fila_det, $total_col11);

        $sheet0->setCellValueByColumnAndRow(16, $fila, $total_col12);
        $sheet1->setCellValueByColumnAndRow(14, $fila_det, $total_col12);

        $sheet0->setCellValueByColumnAndRow(17, $fila, $total_col13);
        $sheet1->setCellValueByColumnAndRow(15, $fila_det, $total_col13);

        $sheet0->setCellValueByColumnAndRow(18, $fila, $total_col14);
        $sheet1->setCellValueByColumnAndRow(16, $fila_det, $total_col14);

        $sheet0->setCellValueByColumnAndRow(19, $fila, $total_col15);
        $sheet1->setCellValueByColumnAndRow(17, $fila_det, $total_col15);

        $sheet0->setCellValueByColumnAndRow(20, $fila, $total_col16);
        $sheet1->setCellValueByColumnAndRow(18, $fila_det, $total_col16);

        $sheet0->setCellValueByColumnAndRow(21, $fila, $total_col17);
        $sheet1->setCellValueByColumnAndRow(19, $fila_det, $total_col17);

        //$sheet0->getStyle("F6:V$fila")->getNumberFormat()->setFormatCode($numberFormat);
        $sheet0->getStyle("D4:T$fila")->getNumberFormat()->setFormatCode($numberFormat);

    }


    function generarReporte(){
        //echo $this->nombre_archivo; exit;
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->docexcel->setActiveSheetIndex(0);

        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel2007');
        $this->objWriter->save($this->url_archivo);

    }

}
?>