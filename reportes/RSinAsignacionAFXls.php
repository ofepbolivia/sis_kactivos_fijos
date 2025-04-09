<?php
class RSinAsignacionAFXls
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
//        $this->datos2 = $param2;
//        $this->datos3 = $param3;
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

        $sheet0->setTitle('Sin Asignación');

        //$datos = $this->objParam->getParametro('datos');
        //capture datas of the view BVP
        $selected = $this->objParam->getParametro('rep_sin_asignacion');
        $hiddes = explode(',', $selected);
        $sacod = '';
        $sades = '';
        $safea = '';
        $sa100 = '';
        $sam87 = '';
        $sauns = '';
        $saprc = '';
        $sac31 = '';

        for ($i=0; $i <count($hiddes) ; $i++) {
            switch ($hiddes[$i]) {
                case 'scod':
                    $sacod = 'cod';
                    break;
                case 'sdes':
                    $sades = 'des';
                    break;
                case 'sfea':
                    $safea = 'fea';
                    break;
                case 's100':
                    $sa100 = '100';
                    break;
                case 'sm87':
                    $sam87 = 'm87';
                    break;
                case 'suns':
                    $sauns = 'uns';
                    break;
                case 'sprc':
                    $saprc = 'prc';
                    break;
                case 'sc31':
                    $sac31 = 'c31';
                    break;
            }
        }
        /////BVP
        $sheet0->getColumnDimension('B')->setWidth(10);
        $sheet0->getColumnDimension('C')->setWidth(30);
        $sheet0->getColumnDimension('D')->setWidth(50);
        $sheet0->getColumnDimension('E')->setWidth(20);
        $sheet0->getColumnDimension('F')->setWidth(20);
        $sheet0->getColumnDimension('G')->setWidth(20);
        $sheet0->getColumnDimension('H')->setWidth(40);
        $sheet0->getColumnDimension('I')->setWidth(30);
        $sheet0->getColumnDimension('J')->setWidth(20);




        //$this->docexcel->getActiveSheet()->mergeCells('A1:A3');
        $sheet0->mergeCells('B1:J1');
        $sheet0->setCellValue('B1', 'DEPARTAMENTO ACTIVOS FIJOS');
        $sheet0->mergeCells('B2:J2');
        $sheet0->setCellValue('B2', 'ACTIVOS FIJOS SIN ASIGNACIÓN');
        $sheet0->mergeCells('B3:J3');
        $sheet0->setCellValue('B3', 'Del: '.$this->objParam->getParametro('fecha_ini').' Al '.$this->objParam->getParametro('fecha_fin'));

        $styleExtras=array(
            'font' => array(
                'bold' => true,
                'size' => 8,
                'name' => 'Arial'
            )
        );


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
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $styleActivos2 = array(
            'font' => array(
                'bold' => false,
                'size' => 8,
                'name' => 'Arial'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $styleActivos3 = array(
            'font' => array(
                'bold' => false,
                'size' => 8,
                'name' => 'Arial'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );


        $styleCabeza = array(
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
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                )
            )
        );


        $sheet0->getStyle('B1:J3')->applyFromArray($styleCabeza);

        $styleTitulos['fill']['color']['rgb'] = '808080';
        $styleTitulos['fill']['color']['rgb'] = 'd4d4d4';

        $sheet0->getRowDimension('4')->setRowHeight(35);
        $sheet0->getStyle('B5:J5')->applyFromArray($styleTitulos);
        $sheet0->getStyle('C5:J5')->getAlignment()->setWrapText(true);

        $this->docexcel->getActiveSheet()->getStyle('F:G')->getNumberFormat()->setFormatCode('#,##0.00');


        //*************************************Cabecera*****************************************
        //fRnk: modificación cabeceras MONTO 100% y 87%, de acuerdo a solicitud por correo 09-04-2025
        $sheet0->setCellValue('B5', 'Nº');

        $sheet0->setCellValue('C5', 'CÓDIGO');

        $sheet0->setCellValue('D5', 'DESCRIPCIÓN');

        $sheet0->setCellValue('E5', 'FECHA DE ALTA');

        $sheet0->setCellValue('F5', 'VALOR COMPRA');

        $sheet0->setCellValue('G5', 'COSTO AF');

        $sheet0->setCellValue('H5', 'UNIDAD SOLICITANTE');

        $sheet0->setCellValue('I5', 'Nº PROCESO COMPRA');

        $sheet0->setCellValue('J5', 'C31');




        //*************************************Fin Cabecera*****************************************

        $fila = 6;

        $contador = 1;

        //************************************************Detalle***********************************************
        //delete columns selected BVP
        ($sacod=='cod')?'':$this->docexcel->getActiveSheet()->getColumnDimension('C')->setVisible(0);
        ($sades=='des')?'':$this->docexcel->getActiveSheet()->getColumnDimension('D')->setVisible(0);
        ($safea=='fea')?'':$this->docexcel->getActiveSheet()->getColumnDimension('E')->setVisible(0);
        ($sa100=='100')?'':$this->docexcel->getActiveSheet()->getColumnDimension('F')->setVisible(0);
        ($sam87=='m87')?'':$this->docexcel->getActiveSheet()->getColumnDimension('G')->setVisible(0);
        ($sauns=='uns')?'':$this->docexcel->getActiveSheet()->getColumnDimension('H')->setVisible(0);
        ($saprc=='prc')?'':$this->docexcel->getActiveSheet()->getColumnDimension('I')->setVisible(0);
        ($sac31=='c31')?'':$this->docexcel->getActiveSheet()->getColumnDimension('J')->setVisible(0);
        ///

        $tipo = $this->objParam->getParametro('tipo_reporte');
        $sheet0->getRowDimension('5')->setRowHeight(35);

        foreach($datos as $value) {

            $styleTitulos['fill']['color']['rgb'] = 'e6e8f4';
            $sheet0->getStyle('E' . $fila . ':G' . $fila)->applyFromArray($styleActivos);
            $sheet0->getStyle('B' . $fila . ':D' . $fila)->applyFromArray($styleActivos2);
            $sheet0->getStyle('H' . $fila . ':J' . $fila)->applyFromArray($styleActivos3);
            $sheet0->getStyle('B' . $fila . ':J' . $fila)->getAlignment()->setWrapText(true);

            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, $contador);
//            $sheet0->getStyle('C'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $value['codigo']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $value['descripcion']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, date("d/m/Y", strtotime($value['fecha_ini_dep'])));
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, $value['monto_compra_orig_100']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, $value['monto_compra_orig']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, $value['nombre_unidad']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $fila, $value['tramite_compra']);
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $fila, $value['nro_cbte_asociado']);

            $contador++;
            $fila++;

        }

    }
}

?>