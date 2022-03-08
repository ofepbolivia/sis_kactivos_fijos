<?php
//incluimos la libreria
//echo dirname(__FILE__);
//include_once(dirname(__FILE__).'/../PHPExcel/Classes/PHPExcel.php');
class RReporteAnexoGeneral
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
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Report File");

		$sheetId = 1;
		$this->docexcel->createSheet(NULL, $sheetId);
		$this->docexcel->setActiveSheetIndex($sheetId);


		$this->docexcel->setActiveSheetIndex(0);

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

	function imprimeInforme(){
		$this->docexcel->getActiveSheet()->setTitle('INFORME');
		$datos = $this->objParam->getParametro('informe');

    $columnas = 0;
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


    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,3,'ACTIVOS FIJOS REGISTRADOS DEL '.strtoupper($this->objParam->getParametro('fecha_ini')).' AL '.strtoupper($this->objParam->getParametro('fecha_fin')));
    $this->docexcel->getActiveSheet()->getStyle('D3:L3')->applyFromArray($styleBoa);
    $this->docexcel->getActiveSheet()->mergeCells('D3:L3');

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,4,'EN LA GESTION: '.strtoupper($this->objParam->getParametro('desc_gestion')));
    $this->docexcel->getActiveSheet()->getStyle('G4:J4')->applyFromArray($styleBoa);
    $this->docexcel->getActiveSheet()->mergeCells('G4:J4');

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,5,'INFORME '.strtoupper($this->objParam->getParametro('nombre_periodo')));
    $this->docexcel->getActiveSheet()->getStyle('G5:J5')->applyFromArray($styleBoa);
    $this->docexcel->getActiveSheet()->mergeCells('G5:J5');

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,8,'N°');
    $this->docexcel->getActiveSheet()->getStyle('D8')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,8,'N° DE PARTIDA');
    $this->docexcel->getActiveSheet()->getStyle('E8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('E8')->getAlignment()->setWrapText(true);


    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,8,'PARTIDA');
    $this->docexcel->getActiveSheet()->getStyle('F8')->applyFromArray($styleBoa2);


    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,8,'REGISTRO EN EL SIGEP DEL '.strtoupper($this->objParam->getParametro('fecha_ini')).' AL '.strtoupper($this->objParam->getParametro('fecha_fin')));
    $this->docexcel->getActiveSheet()->getStyle('G8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('G8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,8,'ACTIVOS FIJOS EN TRANSITO (PAGOS REALIZADOS EN EL SIGEP AL '.strtoupper($this->objParam->getParametro('nombre_periodo')).' '.strtoupper($this->objParam->getParametro('desc_gestion')).') QUE NO HAN SIDO DADOS DE ALTA');
    $this->docexcel->getActiveSheet()->getStyle('H8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('H8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,8,'REVERSION/MODIFICACION ENTRE EL ERP Y SIGEP');
    $this->docexcel->getActiveSheet()->getStyle('I8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('I8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9,8,'ACTIVOS EN TRANSITO PERIODO ANTERIOR INGRESADOS AL ERP AL '.strtoupper($this->objParam->getParametro('nombre_periodo')).' '.strtoupper($this->objParam->getParametro('desc_gestion')));
    $this->docexcel->getActiveSheet()->getStyle('J8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('J8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10,8,'ACTIVOS REGISTRADOS EN EL ERP/SIGEP FUERA DE FECHA DEL PRESENTE INFORME');
    $this->docexcel->getActiveSheet()->getStyle('K8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('K8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11,8,'TOTAL GENERAL');
    $this->docexcel->getActiveSheet()->getStyle('L8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('L8')->getAlignment()->setWrapText(true);

		$this->docexcel->getActiveSheet()->getStyle('D7')->applyFromArray($styleBoa2);
		$this->docexcel->getActiveSheet()->getStyle('E7')->applyFromArray($styleBoa2);
		$this->docexcel->getActiveSheet()->getStyle('F7')->applyFromArray($styleBoa2);

		$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,7,'A');
    $this->docexcel->getActiveSheet()->getStyle('G7')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,7,'B');
    $this->docexcel->getActiveSheet()->getStyle('H7')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,7,'C');
    $this->docexcel->getActiveSheet()->getStyle('I7')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9,7,'D');
    $this->docexcel->getActiveSheet()->getStyle('J7')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10,7,'E');
    $this->docexcel->getActiveSheet()->getStyle('K7')->applyFromArray($styleBoa2);

		$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11,7,'F');
    $this->docexcel->getActiveSheet()->getStyle('L7')->applyFromArray($styleBoa2);

		$this->docexcel->getActiveSheet()->getStyle('D9')->applyFromArray($styleBoa2);
		$this->docexcel->getActiveSheet()->getStyle('E9')->applyFromArray($styleBoa2);
		$this->docexcel->getActiveSheet()->getStyle('F9')->applyFromArray($styleBoa2);

		$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,9,'ANEXO N°1');
    $this->docexcel->getActiveSheet()->getStyle('G9')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,9,'ANEXO N°2');
    $this->docexcel->getActiveSheet()->getStyle('H9')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9,9,'ANEXO N°3');
    $this->docexcel->getActiveSheet()->getStyle('I9')->applyFromArray($styleBoa2);

		$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10,9,'ANEXO N°4');
    $this->docexcel->getActiveSheet()->getStyle('J9')->applyFromArray($styleBoa2);

		$this->docexcel->getActiveSheet()->getStyle('K9')->applyFromArray($styleBoa2);
		$this->docexcel->getActiveSheet()->getStyle('L9')->applyFromArray($styleBoa2);
		//$this->docexcel->getActiveSheet()->getStyle('M9')->applyFromArray($styleBoa2);


    //*************************************Cabecera*****************************************
		$this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
    $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(5);
    $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
    $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
    $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);




		//*************************************Detalle*****************************************
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

		$styleObserva = array(
				'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array(
								'rgb' => 'F8CBAD'
						)

				),
				'font'  => array(
						'bold'  => false,
						'size'  => 12,
						'name'  => 'Times New Roman',
						'color' => array(
								'rgb' => '000000'
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


		$fila = 10;
		$numero = 1;
		$aux = 10;
    $totales=array();
		$total = 10;
		$pago= 11;
		$estacion=array();
		$datos = $this->objParam->getParametro('informe');

		foreach($datos as $value){
				 if(!in_array($valor, $estacion)){
						 $estacion[]=$valor;
				 }

		}

foreach($estacion as $value1 ){
		foreach ($datos as $value) {
				$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $numero);
				$this->docexcel->getActiveSheet()->getStyle("D$fila")->applyFromArray($styleContenido);
				$this->docexcel->getActiveSheet()->getStyle("D$fila")->applyFromArray($bordes);


				$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['desc_codigo']);
				$this->docexcel->getActiveSheet()->getStyle("E$fila")->applyFromArray($bordes);
				$this->docexcel->getActiveSheet()->getStyle("E$fila")->applyFromArray($styleContenido);
				$this->docexcel->getActiveSheet()->getStyle("E$fila")->getAlignment()->setWrapText(true);

				$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['desc_partida']);
				$this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($styleContenido);
				$this->docexcel->getActiveSheet()->getStyle("F$fila")->getAlignment()->setWrapText(true);
				$this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($bordes);


				$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $total, $value['importe_sigep']);
				$this->docexcel->getActiveSheet()->getStyle("G$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
				$this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($bordes);
				$this->docexcel->getActiveSheet()->getStyle("G$total")->applyFromArray($styleContenido2);

				$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['importe_anexo1']);
				$this->docexcel->getActiveSheet()->getStyle("H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
				$this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($bordes);
				$this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($styleContenido2);

				$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $total, $value['importe_anexo2']);
				$this->docexcel->getActiveSheet()->getStyle("I$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
				$this->docexcel->getActiveSheet()->getStyle("I$total")->applyFromArray($styleContenido2);
				$this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($bordes);

				$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['importe_anexo3']);
				$this->docexcel->getActiveSheet()->getStyle("J$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
				$this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($styleContenido2);
				$this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($bordes);

				$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $total, $value['importe_anexo4']);
				$this->docexcel->getActiveSheet()->getStyle("K$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
				$this->docexcel->getActiveSheet()->getStyle("K$total")->applyFromArray($styleContenido2);
				$this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($bordes);

				$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['importe_total']);
				$this->docexcel->getActiveSheet()->getStyle("L$fila")->applyFromArray($styleContenido2);
				$this->docexcel->getActiveSheet()->getStyle("L$fila")->applyFromArray($bordes);


				$numero++;
				$fila++;
				$total++;
			}
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($total), 'Total');
			$this->docexcel->getActiveSheet()->mergeCells("D$total:F$total");
			$this->docexcel->getActiveSheet()->getStyle("D$total:F$total")->applyFromArray($bordes);
			$this->docexcel->getActiveSheet()->getStyle("D$total:L$total")->applyFromArray($styleBoa4);

			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, ($fila), "=sum(G$aux:G$fila)");
			$this->docexcel->getActiveSheet()->getStyle("G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			$this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($styleContenido3);

			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, ($fila), "=sum(H$aux:H$fila)");
			$this->docexcel->getActiveSheet()->getStyle("H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			$this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($styleContenido3);

			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, ($fila), "=sum(I$aux:I$fila)");
			$this->docexcel->getActiveSheet()->getStyle("I$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			$this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($styleContenido3);

			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, ($fila), "=sum(J$aux:J$fila)");
			$this->docexcel->getActiveSheet()->getStyle("J$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			$this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($styleContenido3);

			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, ($fila), "=sum(K$aux:K$fila)");
			$this->docexcel->getActiveSheet()->getStyle("K$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			$this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($styleContenido3);

			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, ($fila), "=sum(L$aux:L$fila)");
			$this->docexcel->getActiveSheet()->getStyle("L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			$this->docexcel->getActiveSheet()->getStyle("L$fila")->applyFromArray($styleContenido3);



			$fila++;
			$totales[]=$fila-1;
			$total++;
			$aux = $fila;


}
$obser=($total);
$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($obser), 'OBSERVACIONES: '.strtoupper($this->objParam->getParametro('observaciones')));
$this->docexcel->getActiveSheet()->mergeCells("D$obser:L$obser");
$this->docexcel->getActiveSheet()->getStyle("D$obser:L$obser")->applyFromArray($bordes);
$this->docexcel->getActiveSheet()->getStyle("D$obser:L$obser")->applyFromArray($styleObserva);
$this->docexcel->getActiveSheet()->getStyle("D$obser:L$obser")->getAlignment()->setWrapText(true);

				//************************************************Fin Detalle***********************************************
	}

	function imprimeAnexo1(){

		//$datos = $this->objParam->getParametro('anexo1');
		$columnas = 0;
		$this->docexcel->setActiveSheetIndex(1);
		$this->docexcel->getActiveSheet()->setTitle('ANEXO 1');

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


    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,3,'ACTIVOS FIJOS EN TRANSITO ( PAGOS REALIZADOS EN EL SIGEP AL: '.strtoupper($this->objParam->getParametro('nombre_periodo').' )' ));
    $this->docexcel->getActiveSheet()->getStyle('D3:L3')->applyFromArray($styleBoa);
    $this->docexcel->getActiveSheet()->mergeCells('D3:L3');

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,4,'EN LA GESTION: '.strtoupper($this->objParam->getParametro('desc_gestion')));
    $this->docexcel->getActiveSheet()->getStyle('E4:F4')->applyFromArray($styleBoa);
    $this->docexcel->getActiveSheet()->mergeCells('E4:F4');

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,4,'FECHA INICIO: '.strtoupper($this->objParam->getParametro('fecha_ini')));
    $this->docexcel->getActiveSheet()->getStyle('G4:I4')->applyFromArray($styleBoa);
    $this->docexcel->getActiveSheet()->mergeCells('G4:I4');

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9,4,'FECHA FIN: '.strtoupper($this->objParam->getParametro('fecha_ini')));
    $this->docexcel->getActiveSheet()->getStyle('J4:K4')->applyFromArray($styleBoa);
    $this->docexcel->getActiveSheet()->mergeCells('J4:K4');

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,5,'ANEXO N°1');
    $this->docexcel->getActiveSheet()->getStyle('G5:I5')->applyFromArray($styleBoa);
    $this->docexcel->getActiveSheet()->mergeCells('G5:I5');

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,8,'N°');
    $this->docexcel->getActiveSheet()->getStyle('B8')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,8,'N° DE PARTIDA');
    $this->docexcel->getActiveSheet()->getStyle('C8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('C8')->getAlignment()->setWrapText(true);


    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,8,'PARTIDA');
    $this->docexcel->getActiveSheet()->getStyle('D8')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,8,'C31');
    $this->docexcel->getActiveSheet()->getStyle('E8')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,8,'DETALLE');
    $this->docexcel->getActiveSheet()->getStyle('F8')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,8,'MONTO S/CONTRATO DE COMPRA');
    $this->docexcel->getActiveSheet()->getStyle('G8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('G8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,8,'ALTA EN EL ERP');
    $this->docexcel->getActiveSheet()->getStyle('H8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('H8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,8,'MONTO EN TRANSITO');
    $this->docexcel->getActiveSheet()->getStyle('I8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('I8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9,8,'MONTO ACUMULADO PERIODOS ANTERIORES');
    $this->docexcel->getActiveSheet()->getStyle('J8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('J8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10,8,'MONTO EN EL PERIODO');
    $this->docexcel->getActiveSheet()->getStyle('K8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('K8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11,8,'OBSERVACIONES');
    $this->docexcel->getActiveSheet()->getStyle('L8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('L8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12,8,'UNIDAD SOLICITANTE');
    $this->docexcel->getActiveSheet()->getStyle('M8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('M8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,7,'A');
    $this->docexcel->getActiveSheet()->getStyle('G7')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,7,'B');
    $this->docexcel->getActiveSheet()->getStyle('H7')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,7,'C');
    $this->docexcel->getActiveSheet()->getStyle('I7')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9,7,'D');
    $this->docexcel->getActiveSheet()->getStyle('J7')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10,7,'E');
    $this->docexcel->getActiveSheet()->getStyle('K7')->applyFromArray($styleBoa2);

    //*************************************Cabecera*****************************************
    $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
    $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
    $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);




		//*************************************Detalle*****************************************
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


    $fila = 9;
    $numero = 1;
    $aux = 9;
    $contador = 0;
    $total = 11;
    $datos = $this->objParam->getParametro('anexo1');
    $estacion=array();
    $totales=array();

    foreach($datos as $value){
        $valor=$value['desc_codigo'];
        $partida[]=$value['desc_nombre'];
        $montoContrato[]=$value['monto_contrato'];
        $montoErp[]=$value['monto_alta'];
        $montoTransito[]=$value['monto_transito'];
        $montoPagado[]=$value['monto_pagado'];
        $montoTercer[]=$value['monto_tercer'];
         if(!in_array($valor, $estacion)){
             $estacion[]=$valor;
         }
         //var_dump($datos);exit;
       }

      foreach($estacion as $value1 ){

    //var_dump($datos);exit;
    foreach ($datos as $value) {

      if ($value['desc_codigo'] == $value1) {

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $numero);
		$this->docexcel->getActiveSheet()->getStyle("B$fila")->applyFromArray($bordes);  
        $this->docexcel->getActiveSheet()->getStyle("B$fila")->applyFromArray($styleContenido);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['desc_codigo']);
        $this->docexcel->getActiveSheet()->getStyle("C$fila")->applyFromArray($styleContenido);
        $this->docexcel->getActiveSheet()->getStyle("C$fila")->applyFromArray($bordes);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['desc_nombre']);
        $this->docexcel->getActiveSheet()->getStyle("D$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("D$fila")->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle("D$fila")->applyFromArray($styleContenido);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['c31']);
        $this->docexcel->getActiveSheet()->getStyle("E$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("E$fila")->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle("E$fila")->applyFromArray($styleContenido);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['detalle_c31']);
        $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("F$fila")->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($styleContenido);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['monto_contrato']);
        $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($styleContenido2);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['monto_alta']);
        $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($styleContenido2);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['monto_transito']);
        $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("I$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($styleContenido2);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['monto_pagado']);
        $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("J$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($styleContenido2);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['monto_tercer']);
        $this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("K$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($styleContenido2);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['observaciones']);
        $this->docexcel->getActiveSheet()->getStyle("L$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("L$fila")->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle("L$fila")->applyFromArray($styleContenido);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $value['nombre_unidad']);
        $this->docexcel->getActiveSheet()->getStyle("M$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("M$fila")->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle("M$fila")->applyFromArray($styleContenido);



        $numero++;
        $fila++;
        $total++;
        //$total=$fila;
      //  $pago++;
    }
  }

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, 'Total Grupo '.$value1);
    // $this->docexcel->getActiveSheet()->getStyle("B$total:F$total")->applyFromArray($bordes);
    // $this->docexcel->getActiveSheet()->getStyle("B$total:F$total")->getAlignment()->setWrapText(true);
    $this->docexcel->getActiveSheet()->mergeCells("B$fila:F$fila");
    $this->docexcel->getActiveSheet()->getStyle("B$fila:M$fila")->applyFromArray($styleBoa3);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, ($fila), "=sum(G$aux:G$fila)");
    $this->docexcel->getActiveSheet()->getStyle("G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
    $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($styleContenido3);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, ($fila), "=sum(H$aux:H$fila)");
    $this->docexcel->getActiveSheet()->getStyle("H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
    $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($styleContenido3);

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
  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, ($total), 'Total Anexo 1:');
  $this->docexcel->getActiveSheet()->mergeCells("B$total:F$total");
  $this->docexcel->getActiveSheet()->getStyle("B$total:F$total")->applyFromArray($bordes);
  $this->docexcel->getActiveSheet()->getStyle("B$total:M$total")->applyFromArray($styleBoa4);

  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, ($total), array_sum(($montoContrato==null)?array():$montoContrato));
  $this->docexcel->getActiveSheet()->getStyle("G$total")->applyFromArray($styleContenido4);
  $this->docexcel->getActiveSheet()->getStyle("G$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, ($total), array_sum(($montoErp==null)?array():$montoErp));
  $this->docexcel->getActiveSheet()->getStyle("H$total")->applyFromArray($styleContenido4);
  $this->docexcel->getActiveSheet()->getStyle("H$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, ($total), array_sum(($montoTransito==null)?array():$montoTransito));
  $this->docexcel->getActiveSheet()->getStyle("I$total")->applyFromArray($styleContenido4);
  $this->docexcel->getActiveSheet()->getStyle("I$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, ($total), array_sum(($montoPagado==null)?array():$montoPagado));
  $this->docexcel->getActiveSheet()->getStyle("J$total")->applyFromArray($styleContenido4);
  $this->docexcel->getActiveSheet()->getStyle("J$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, ($total), array_sum(($montoTercer==null)?array():$montoTercer));
  $this->docexcel->getActiveSheet()->getStyle("K$total")->applyFromArray($styleContenido4);
  $this->docexcel->getActiveSheet()->getStyle("K$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

  }


		//************************************************Fin Detalle***********************************************


  function imprimeAnexo2(){

    $datos = $this->objParam->getParametro('anexo2');
		$columnas = 2;
    $this->docexcel->createSheet();
		$this->docexcel->setActiveSheetIndex(2);
		$this->docexcel->getActiveSheet()->setTitle('ANEXO 2');


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


    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,3,'REVERSION/MODIFICACION ENTRE EL ERP Y SIGEP');
    $this->docexcel->getActiveSheet()->getStyle('E3:I3')->applyFromArray($styleBoa);
    $this->docexcel->getActiveSheet()->getStyle('E3:I3')->getAlignment()->setWrapText(true);
    $this->docexcel->getActiveSheet()->mergeCells('E3:I3');

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,4,'ANEXO N°2');
    $this->docexcel->getActiveSheet()->getStyle('G4')->applyFromArray($styleBoa);
    //$this->docexcel->getActiveSheet()->mergeCells('G5:I5');

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,8,'N°');
    $this->docexcel->getActiveSheet()->getStyle('B8')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,8,'N° DE PARTIDA');
    $this->docexcel->getActiveSheet()->getStyle('C8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('C8')->getAlignment()->setWrapText(true);


    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,8,'PARTIDA');
    $this->docexcel->getActiveSheet()->getStyle('D8')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,8,'C31/No Proc.');
    $this->docexcel->getActiveSheet()->getStyle('E8')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,8,'MONTO SIGEP');
    $this->docexcel->getActiveSheet()->getStyle('F8')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,8,'MONTO ERP');
    $this->docexcel->getActiveSheet()->getStyle('G8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('G8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,8,'DIFERENCIA');
    $this->docexcel->getActiveSheet()->getStyle('H8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('H8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,8,'OBSERVACIONES');
    $this->docexcel->getActiveSheet()->getStyle('I8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('I8')->getAlignment()->setWrapText(true);

    //*************************************Cabecera*****************************************
    $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
    $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
    $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
    $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
    $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
		//*************************************Detalle*****************************************
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
    $datos = $this->objParam->getParametro('anexo2');
    $estacion=array();
    $totales=array();

    foreach($datos as $value){
        $valor=$value['desc_codigo'];
        $partida[]=$value['desc_nombre'];
        $montoSigep[]=$value['monto_sigep'];
        $montoErp[]=$value['monto_erp'];
        $montoTransito[]=$value['monto_transito'];
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
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $numero);
        $this->docexcel->getActiveSheet()->getStyle("B$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("B$fila")->applyFromArray($styleContenido);


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['desc_codigo']);
        $this->docexcel->getActiveSheet()->getStyle("C$fila")->applyFromArray($styleContenido);
        $this->docexcel->getActiveSheet()->getStyle("C$fila")->applyFromArray($bordes);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['desc_nombre']);
        $this->docexcel->getActiveSheet()->getStyle("D$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("D$fila")->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle("D$fila")->applyFromArray($styleContenido);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['c31']);
        $this->docexcel->getActiveSheet()->getStyle("E$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("E$fila")->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle("E$fila")->applyFromArray($styleContenido);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['monto_sigep']);
        $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("F$fila")->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($styleContenido);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['monto_erp']);
        $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($styleContenido2);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['diferencia']);
        $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($styleContenido2);


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['observaciones']);
        $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("I$fila")->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($styleContenido);

        $numero++;
        $fila++;
        $total++;
    }
  }

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, 'Total Partida '.$value1);
    $this->docexcel->getActiveSheet()->getStyle("B$fila:I$fila")->applyFromArray($bordes);
    $this->docexcel->getActiveSheet()->getStyle("B$fila:I$fila")->getAlignment()->setWrapText(true);
    $this->docexcel->getActiveSheet()->mergeCells("B$fila:E$fila");
    $this->docexcel->getActiveSheet()->getStyle("B$fila:I$fila")->applyFromArray($styleBoa3);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, ($fila), "=sum(F$aux:F$fila)");
    $this->docexcel->getActiveSheet()->getStyle("F$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
    $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($styleContenido3);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, ($fila), "=sum(G$aux:G$fila)");
    $this->docexcel->getActiveSheet()->getStyle("G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
    $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($styleContenido3);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, ($fila), "=sum(H$aux:H$fila)");
    $this->docexcel->getActiveSheet()->getStyle("H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
    $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($styleContenido3);

    $fila++;
    $totales[]=$fila-1;
    $total++;
    $aux = $fila;

}
$fila++;
$total=($fila-1);
$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, ($total), 'Total Anexo 2');
$this->docexcel->getActiveSheet()->mergeCells("B$total:E$total");
$this->docexcel->getActiveSheet()->getStyle("B$total:E$total")->applyFromArray($bordes);
$this->docexcel->getActiveSheet()->getStyle("B$total:I$total")->applyFromArray($styleBoa4);

$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, ($total), array_sum(($montoSigep==null)?array():$montoSigep));
$this->docexcel->getActiveSheet()->getStyle("F$total")->applyFromArray($styleContenido4);
$this->docexcel->getActiveSheet()->getStyle("F$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, ($total), array_sum(($montoErp==null)?array():$montoErp));
$this->docexcel->getActiveSheet()->getStyle("G$total")->applyFromArray($styleContenido4);
$this->docexcel->getActiveSheet()->getStyle("G$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, ($total), array_sum(($diferencia==null)?array():$diferencia));
$this->docexcel->getActiveSheet()->getStyle("H$total")->applyFromArray($styleContenido4);
$this->docexcel->getActiveSheet()->getStyle("H$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

}

		//************************************************Fin Detalle***********************************************


  function imprimeAnexo3(){

		$datos = $this->objParam->getParametro('anexo3');
		$columnas = 2;
    $this->docexcel->createSheet();
		$this->docexcel->setActiveSheetIndex(3);
		$this->docexcel->getActiveSheet()->setTitle('ANEXO 3');

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


    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,3,'ACTIVOS EN TRANSITO PERIODO ANTERIOR INGRESADOS AL ERP AL: '.strtoupper($this->objParam->getParametro('nombre_periodo')));
    $this->docexcel->getActiveSheet()->getStyle('D3:L3')->applyFromArray($styleBoa);
    $this->docexcel->getActiveSheet()->mergeCells('D3:L3');

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,4,'EN LA GESTION: '.strtoupper($this->objParam->getParametro('desc_gestion')));
    $this->docexcel->getActiveSheet()->getStyle('G4:I4')->applyFromArray($styleBoa);
    $this->docexcel->getActiveSheet()->mergeCells('G4:I4');

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,6,'ANEXO N°3');
    $this->docexcel->getActiveSheet()->getStyle('G6:I6')->applyFromArray($styleBoa);
    $this->docexcel->getActiveSheet()->mergeCells('G6:I6');

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4,8,'N°');
    $this->docexcel->getActiveSheet()->getStyle('E8')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,8,'N° DE PARTIDA');
    $this->docexcel->getActiveSheet()->getStyle('F8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('F8')->getAlignment()->setWrapText(true);


    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,8,'PARTIDA');
    $this->docexcel->getActiveSheet()->getStyle('G8')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,8,'C31');
    $this->docexcel->getActiveSheet()->getStyle('H8')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,8,'DETALLE');
    $this->docexcel->getActiveSheet()->getStyle('I8')->applyFromArray($styleBoa2);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9,8,'MONTO');
    $this->docexcel->getActiveSheet()->getStyle('J8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('J8')->getAlignment()->setWrapText(true);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10,8,'UNIDAD SOLICITANTE');
    $this->docexcel->getActiveSheet()->getStyle('K8')->applyFromArray($styleBoa2);
    $this->docexcel->getActiveSheet()->getStyle('K8')->getAlignment()->setWrapText(true);

    //*************************************Cabecera*****************************************
    $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
    $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
    $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
    $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
    $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);

		//*************************************Detalle*****************************************
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
    $datos = $this->objParam->getParametro('anexo3');
    $estacion=array();
    $totales=array();

    foreach($datos as $value){
        $valor=$value['desc_codigo'];
        $partida[]=$value['desc_nombre'];
        $montoSigep[]=$value['monto_erp'];
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

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['detalle_c31']);
        $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("I$fila")->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($styleContenido);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['monto_erp']);
        $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("J$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($styleContenido2);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['nombre_unidad']);
        $this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($styleContenido);

        $numero++;
        $fila++;
        $total++;
    }
  }

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, 'Total Grupo '.$value1);
    $this->docexcel->getActiveSheet()->getStyle("E$fila:I$fila")->applyFromArray($bordes);
    $this->docexcel->getActiveSheet()->getStyle("E$fila:I$fila")->getAlignment()->setWrapText(true);
    $this->docexcel->getActiveSheet()->mergeCells("E$fila:I$fila");
    $this->docexcel->getActiveSheet()->getStyle("E$fila:K$fila")->applyFromArray($styleBoa3);

    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, ($fila), "=sum(J$aux:J$fila)");
    $this->docexcel->getActiveSheet()->getStyle("J$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
    $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($styleContenido3);

    $fila++;
    $totales[]=$fila-1;
    $total++;
    $aux = $fila;
}
$fila++;
$total=($fila-1);
$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, ($total), 'Total Anexo 3');
$this->docexcel->getActiveSheet()->mergeCells("E$total:I$total");
$this->docexcel->getActiveSheet()->getStyle("E$total:I$total")->applyFromArray($bordes);
$this->docexcel->getActiveSheet()->getStyle("E$total:K$total")->applyFromArray($styleBoa4);

$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, ($total), array_sum(($montoSigep==null)?array():$montoSigep));
$this->docexcel->getActiveSheet()->getStyle("J$total")->applyFromArray($styleContenido4);
$this->docexcel->getActiveSheet()->getStyle("J$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

}

		//************************************************Fin Detalle***********************************************


  function imprimeAnexo4(){

		$datos = $this->objParam->getParametro('anexo4');
		$columnas = 2;
    $this->docexcel->createSheet();
		$this->docexcel->setActiveSheetIndex(4);
		$this->docexcel->getActiveSheet()->setTitle('ANEXO 4');

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


		//*************************************Detalle*****************************************
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
		$datos = $this->objParam->getParametro('anexo4');
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

		//************************************************Fin Detalle***********************************************


	function generarReporte() {
		//echo $this->nombre_archivo; exit;
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$this->docexcel->setActiveSheetIndex(0);

		$this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
		$this->objWriter->save($this->url_archivo);

	}


}

?>
