<?php
class RReporteAnexo4
{
    private $docexcel;
    private $objWriter;
    private $equivalencias=array();
    private $monto=array();
    private $montoDebito=array();
    private $objParam;
    public  $url_archivo;
    public  $fill = 0;
    public  $filles = 0;
    public  $garante = 0;
    public  $pika = 0;


    function __construct(CTParametro $objParam){
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
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

        $this->equivalencias=array( 0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
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

    function imprimeCabecera() {
        $this->docexcel->createSheet();
        $this->docexcel->getActiveSheet()->setTitle('ANEXO 4');
        $this->docexcel->setActiveSheetIndex(0);


        $styleTitulos = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );


        $styleBoa = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                //'color' => array(
                    //'rgb' => 'D8D8D8'
              //  )
            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 14,
                'name'  => 'Times New Roman',
                //'color' => array(
                      //    'rgb'=>'021E49')

            ),

        );
        $styleBoa2 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'D8E4BC'
                )

            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 10,
                'name'  => 'Times New Roman'


            ),
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );



        //titulos

        $gdImage = imagecreatefromjpeg('../../../sis_kactivos_fijos/reportes/LogoBoa.jpg');
        // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Sample image');
        $objDrawing->setDescription('Sample image');
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(105);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());
        $this->docexcel->getActiveSheet()->mergeCells('A1:C1');


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,3,'ACTIVOS REGISTRADOS EN EL ERP/SIGEP FUERA DE LA FECHA DEL PRESENTE INFORME');
        $this->docexcel->getActiveSheet()->getStyle('D3:L3')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('D3:L3');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,5,'ANEXO N°4');
        $this->docexcel->getActiveSheet()->getStyle('G5:I5')->applyFromArray($styleBoa);
        $this->docexcel->getActiveSheet()->mergeCells('G5:I5');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,8,'N°');
        $this->docexcel->getActiveSheet()->getStyle('E8')->applyFromArray($styleBoa2);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,8,'N° DE PARTIDA');
        $this->docexcel->getActiveSheet()->getStyle('F8')->applyFromArray($styleBoa2);
        $this->docexcel->getActiveSheet()->getStyle('F8')->getAlignment()->setWrapText(true);


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,8,'PARTIDA');
        $this->docexcel->getActiveSheet()->getStyle('G8')->applyFromArray($styleBoa2);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,8,'C31');
        $this->docexcel->getActiveSheet()->getStyle('H8')->applyFromArray($styleBoa2);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,8,'MONTO SIGEP');
        $this->docexcel->getActiveSheet()->getStyle('I8')->applyFromArray($styleBoa2);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9,8,'MONTO ERP');
        $this->docexcel->getActiveSheet()->getStyle('J8')->applyFromArray($styleBoa2);
        $this->docexcel->getActiveSheet()->getStyle('J8')->getAlignment()->setWrapText(true);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10,8,'DIFERENCIA');
        $this->docexcel->getActiveSheet()->getStyle('K8')->applyFromArray($styleBoa2);
        $this->docexcel->getActiveSheet()->getStyle('K8')->getAlignment()->setWrapText(true);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11,8,'OBSERVACIONES');
        $this->docexcel->getActiveSheet()->getStyle('L8')->applyFromArray($styleBoa2);
        $this->docexcel->getActiveSheet()->getStyle('L8')->getAlignment()->setWrapText(true);

        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);

    }

    function generarDatos(){
        $this->imprimeCabecera();
        $bordes = array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),

        );
        $styleTitulos = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $styleContenido = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'font'  => array(
                'bold'  => false,
                'size'  => 10,
                'name'  => 'Times New Roman'

            ),
        );
        $styleBoa = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'D8D8D8'
                )
            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Arial',
                'color' => array(
                          'rgb'=>'021E49')

            ),

        );
        $styleContenido2 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'font'  => array(
                'bold'  => false,
                'size'  => 10,
                'name'  => 'Times New Roman'

            ),

        );
        $styleContenido3 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 14,
                'name'  => 'Times New Roman'


            ),
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );




        $styleBoa2 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'D8E4BC'
                )

            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 10,
                'name'  => 'Times New Roman'


            ),
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        $styleBoa3 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'D8E4BC'
                )

            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 16,
                'name'  => 'Times New Roman'


            ),
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        $styleBoa4 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '5B9BD5'
                )

            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 16,
                'name'  => 'Times New Roman',
                'color' => array(
                    'rgb' => 'FFFFFF'
                )


            ),
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        $styleContenido4 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'font'  => array(
                'bold'  => true,
                'size'  => 14,
                'name'  => 'Times New Roman'


            ),
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );

        $fila = 9;
        $numero = 1;
        $aux = 9;
        $contador = 0;
        $total = 11;
        $datos = $this->objParam->getParametro('datos');
        $estacion=array();
        $totales=array();

        foreach($datos as $value){
            $valor=$value['desc_codigo'];
            $partida[]=$value['desc_nombre'];
            $montoSigep[]=$value['monto_sigep'];
            $montoErp[]=$value['monto_erp'];
            $diferencia[]=$value['diferencia'];
             if(!in_array($valor, $estacion)){
                 $estacion[]=$valor;
             }
             //var_dump($datos);exit;
           }

           foreach($estacion as $value1 ){


        //var_dump($datos);exit;
        foreach ($datos as $value) {
          if ($value['desc_codigo'] == $value1) {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $numero);
            $this->docexcel->getActiveSheet()->getStyle("E$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->getStyle("E$fila")->applyFromArray($styleContenido);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['desc_codigo']);
            $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($styleContenido);
            $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($bordes);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['desc_nombre']);
            $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->getStyle("G$fila")->getAlignment()->setWrapText(true);
            $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($styleContenido);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['c31']);
            $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->getStyle("H$fila")->getAlignment()->setWrapText(true);
            $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($styleContenido);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['monto_sigep']);
            $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->getStyle("I$fila")->getAlignment()->setWrapText(true);
            $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($styleContenido);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['monto_erp']);
            $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->getStyle("J$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($styleContenido2);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['diferencia']);
            $this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->getStyle("K$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($styleContenido2);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['observaciones']);
            $this->docexcel->getActiveSheet()->getStyle("L$fila")->applyFromArray($bordes);
            $this->docexcel->getActiveSheet()->getStyle("L$fila")->getAlignment()->setWrapText(true);
            //$this->docexcel->getActiveSheet()->getStyle("L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("L$fila")->applyFromArray($styleContenido);

            $numero++;
            $fila++;
            $total++;
        }
      }

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, 'Total Partida '.$value1);
        $this->docexcel->getActiveSheet()->getStyle("E$fila:H$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("E$fila:H$fila")->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells("E$fila:H$fila");
        $this->docexcel->getActiveSheet()->getStyle("E$fila:L$fila")->applyFromArray($styleBoa3);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, ($fila), "=sum(I$aux:I$fila)");
        $this->docexcel->getActiveSheet()->getStyle("I$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($styleContenido3);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, ($fila), "=sum(J$aux:J$fila)");
        $this->docexcel->getActiveSheet()->getStyle("J$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($styleContenido3);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, ($fila), "=sum(K$aux:K$fila)");
        $this->docexcel->getActiveSheet()->getStyle("K$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($styleContenido3);

        $fila++;
        $totales[]=$fila-1;
        $total++;
        $aux = $fila;
    }
    $fila++;
    $total=($fila-1);
    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, ($total), 'Total Anexo 4');
    $this->docexcel->getActiveSheet()->mergeCells("E$total:H$total");
    $this->docexcel->getActiveSheet()->getStyle("E$total:H$total")->applyFromArray($bordes);
    $this->docexcel->getActiveSheet()->getStyle("E$total:L$total")->applyFromArray($styleBoa4);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, ($total), array_sum(($montoSigep==null)?array():$montoSigep));
    $this->docexcel->getActiveSheet()->getStyle("I$total")->applyFromArray($styleContenido4);
    $this->docexcel->getActiveSheet()->getStyle("I$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, ($total), array_sum(($montoErp==null)?array():$montoErp));
    $this->docexcel->getActiveSheet()->getStyle("J$total")->applyFromArray($styleContenido4);
    $this->docexcel->getActiveSheet()->getStyle("J$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, ($total), array_sum(($diferencia==null)?array():$diferencia));
    $this->docexcel->getActiveSheet()->getStyle("K$total")->applyFromArray($styleContenido4);
    $this->docexcel->getActiveSheet()->getStyle("K$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

  }

    function generarReporte(){
        $this->generarDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>
