<?php
class RActiDepaPFunAFXls
{

    private $columnas=array();
    private $fila;
    
    private $objParam;
    public  $url_archivo;
	
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



        $this->docexcel->setActiveSheetIndex(0);
        $sheet0 = $this->docexcel->getActiveSheet();

        $sheet0->setTitle('Activos X Deposito');

        $sheet0->getColumnDimension('B')->setWidth(15);
        $sheet0->getColumnDimension('C')->setWidth(25);
        $sheet0->getColumnDimension('D')->setWidth(60);
        $sheet0->getColumnDimension('E')->setWidth(20);
		$sheet0->getColumnDimension('F')->setWidth(20);
		$sheet0->getColumnDimension('G')->setWidth(50);
				
		$encargado = $datos[0]['encargado'];
		$almacen   = $datos[0]['almacen'];
		
        $sheet0->mergeCells('B1:G1');
        $sheet0->setCellValue('B1', 'ACTIVOS FIJOS POR DEPOSITO');
        $sheet0->mergeCells('B2:G2');
        $sheet0->setCellValue('B2', 'Reporte de Activos en Detalle');
		$sheet0->mergeCells('B3:C3');
		$sheet0->setCellValue('B3','RESPONSABLE: '.$encargado.'');
		$sheet0->mergeCells('B4:C4');
		$sheet0->setCellValue('B4','ALMACEN: '.$almacen.'');
		

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

        $styleCa = array(
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
            )
        );
        $sheet0->getStyle('B1:G4')->applyFromArray($styleCabeza);        

        $styleTitulos['fill']['color']['rgb'] = '8DB4E2';
        $styleTitulos['fill']['color']['rgb'] = 'CCBBAA';

        $sheet0->getRowDimension('6')->setRowHeight(35);
        $sheet0->getStyle('B6:G6')->applyFromArray($styleCa);
        $sheet0->getStyle('C6:G6')->getAlignment()->setWrapText(true);


        //*************************************Cabecera*****************************************

        $sheet0->setCellValue('B6', 'CODIGO');
        $sheet0->setCellValue('C6', 'NOMBRE');
        $sheet0->setCellValue('D6', 'DESCRIPCION');      
        $sheet0->setCellValue('E6', 'ESTADO'."\n".'FUNCIONAL');
		$sheet0->setCellValue('F6', 'FECHA DE INGRESO'."\n".'DE DEPOSITO');
		$sheet0->setCellValue('G6', 'UBICACION');

        //*************************************Fin Cabecera*****************************************

        $fila = 7;
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


        //$tipo = $this->objParam->getParametro('tipo_reporte');
        $sheet0->getRowDimension('35')->setRowHeight(35);
	
		$sum=0;
        foreach($datos as $value) {
 
	    $sheet0->getStyle('B'.$fila.':G'.$fila)->applyFromArray($styleTitulos);
        $sheet0->getStyle('B'.$fila.':G'.$fila)->getAlignment()->setWrapText(true);
			
					$sheet0->getStyle('A'.$fila)->getNumberFormat()->setFormatCode('');
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, $value['codigo']);					
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $value['denominacion']);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $value['descripcion']);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, $value['cat_desc']);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, date("d/m/Y", strtotime($value['fecha_mov'])));
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, $value['ubicacion']);					                    
 
                    $fila ++;
                }
						
 	}
 }
?>