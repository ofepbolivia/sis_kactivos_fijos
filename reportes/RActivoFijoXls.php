<?php
class RActivoFijoXls
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
		//var_dump($this->datos);exit;
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

        $sheet0->setTitle('Reporte de Activos por Grupo');        

        $sheet0->getColumnDimension('B')->setWidth(20);
        $sheet0->getColumnDimension('C')->setWidth(35);
        $sheet0->getColumnDimension('D')->setWidth(20);
        $sheet0->getColumnDimension('E')->setWidth(20);
		        
        $sheet0->mergeCells('B1:E1');
        $sheet0->setCellValue('B1', 'DEPARTAMENTO ACTIVOS FIJOS');
        $sheet0->mergeCells('B2:E2');
        $sheet0->setCellValue('B2', 'Reporte de Activos por Grupo');       


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
       $styleTotales = array(
            'font' => array(
                'bold' => true,
                'size' => 10,
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


        $sheet0->getStyle('B1:E3')->applyFromArray($styleCabeza);
        $sheet0->getStyle('B2:E2')->applyFromArray($styleCabeza);        

        $styleTitulos['fill']['color']['rgb'] = '8DB4E2';
        $styleTitulos['fill']['color']['rgb'] = 'CCBBAA';

        $sheet0->getRowDimension('5')->setRowHeight(35);
        $sheet0->getStyle('B5:E5')->applyFromArray($styleCa);
        $sheet0->getStyle('C5:E5')->getAlignment()->setWrapText(true);


        //*************************************Cabecera*****************************************

        $sheet0->setCellValue('B5', 'CODIGO');

        $sheet0->setCellValue('C5', 'NOMBRE');

        $sheet0->setCellValue('D5', 'SUB TOTAL');
        

        $sheet0->setCellValue('E5', 'TOTAL');


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

        
        $sheet0->getRowDimension('15')->setRowHeight(35);
	
		$sum=0;
        foreach($datos as $value) {
        	if($value['nivel']==2){
        		$sum += $value['hijos'];
        	}	    
        $sheet0->getStyle('B'.$fila.':E'.$fila)->applyFromArray($styleTitulos);
        $sheet0->getStyle('B'.$fila.':E'.$fila)->getAlignment()->setWrapText(true);
		
					
					$valor = "=\"" . $value['codigo_completo_tmp']. "\"";
    	            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila,$valor);
                    
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $value['nombre']);
                    
					if($value['nivel'] == 3){
                    	$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $value['hijos']);
					}else{
						$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, $value['hijos']);						
					}                    
 
                    $fila ++;
                }
						
					$sheet0->getStyle('B'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$sheet0->getStyle('B'.$fila.':E'.$fila)->applyFromArray($styleTotales);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$fila, '');
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$fila,'TOTAL');
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$fila, '');				
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$fila, $sum);
 	}
 }
?>