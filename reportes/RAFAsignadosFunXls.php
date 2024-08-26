<?php

//fRnk: HR00763
class RAFAsignadosFunXls
{
    private $objParam;
    public $url_archivo;
    private $fecha_ini;
    private $fecha_fin;
    private $funcionario;
    private $tipo;

    function __construct(CTParametro $objParam)
    {
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/" . $this->objParam->getParametro('nombre_archivo');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize' => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator("PXP")
            ->setLastModifiedBy("PXP")
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "' . $this->objParam->getParametro('titulo_archivo') . '"')
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");
        $this->docexcel->setActiveSheetIndex(0);
        $this->docexcel->getActiveSheet()->setTitle($this->objParam->getParametro('titulo_archivo'));
        $this->fecha_ini = $this->objParam->getParametro('fecha_ini');
        $this->fecha_fin = $this->objParam->getParametro('fecha_fin');
        //$this->funcionario = $this->objParam->getParametro('nombre_funcionario2');
        $this->tipo = $this->objParam->getParametro('configuracion_reporte');
    }

    function setDatos($param, $funcionario)
    {
        $this->datos = $param;
        $this->funcionario = $funcionario;
    }

    function generarReporte()
    {
        $this->imprimeDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

    function imprimeDatos()
    {
        $datos = $this->datos;
        $this->docexcel->setActiveSheetIndex(0);
        $sheet0 = $this->docexcel->getActiveSheet();
        $sheet0->setTitle('REPORTE DE ACTIVOS FIJOS');
        $sheet0->getColumnDimension('B')->setWidth(10);
        $sheet0->getColumnDimension('C')->setWidth(20);
        $sheet0->getColumnDimension('D')->setWidth(25);
        $sheet0->getColumnDimension('E')->setWidth(60);
        $sheet0->getColumnDimension('F')->setWidth(12);
        $sheet0->getColumnDimension('G')->setWidth(25);
        $sheet0->getColumnDimension('H')->setWidth(15);
        $sheet0->mergeCells('B1:G1');
        $sheet0->setCellValue('B1', 'DEPARTAMENTO ACTIVOS FIJOS');
        $sheet0->mergeCells('B2:G2');
        $titulo_tipo = $this->tipo == 'acti_fun_asignados' ? 'ASIGNADOS VIGENTES' : 'DEVUELTOS';
        $sheet0->setCellValue('B2', 'REPORTE DE ACTIVOS FIJOS ' . $titulo_tipo . ' POR FUNCIONARIO');
        $sheet0->mergeCells('B3:G3');
        $sheet0->setCellValue('B3', 'Del ' . $this->fecha_ini . ' al ' . $this->fecha_fin);
        $sheet0->mergeCells('B4:C4');
        //$sheet0->setCellValue('B4', 'FUNCIONARIO(A): ' . $this->funcionario . '');

        $styleTitulos = array(
            'font' => array(
                'bold' => false,
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
                    'rgb' => '96a5b7'
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
                    'rgb' => '96a5b7'
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
                    'rgb' => '96a5b7'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $sheet0->getStyle('B1:H4')->applyFromArray($styleCabeza);
        $styleTitulos['fill']['color']['rgb'] = 'ffffff';
        $sheet0->getRowDimension('6')->setRowHeight(35);
        $sheet0->getStyle('B6:H6')->applyFromArray($styleCa);
        $sheet0->getStyle('C6:H6')->getAlignment()->setWrapText(true);
        $sheet0->setCellValue('B6', 'N°');
        $sheet0->setCellValue('C6', 'CÓDIGO');
        $sheet0->setCellValue('D6', 'NOMBRE');
        $sheet0->setCellValue('E6', 'DESCRIPCIÓN');
        $sheet0->setCellValue('F6', 'ESTADO' . "\n" . 'FUNCIONAL');
        $sheet0->setCellValue('G6', 'NÚM.TRÁMITE / FECHA');
        $sheet0->setCellValue('H6', 'UBICACIÓN');
        $fila = 7;
        $sheet0->getRowDimension('35')->setRowHeight(35);
        foreach ($this->funcionario as $f) {
            $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, 'FUNCIONARIO: ' . $f['nombre_completo2']);
            $sheet0->getStyle('B' . $fila . ':H' . $fila)->applyFromArray($styleCa);
            $sheet0->getStyle('B' . $fila . ':H' . $fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $fila++;
            $i = 1;
            $id_ant = 0;
            $sin_resultados = 0;
            foreach ($datos as $value) {
                if ($f['id_funcionario'] != $value['id_funcionario']) {
                    continue;
                }
                $sin_resultados++;
                if ($this->tipo == 'acti_fun_asignados') {
                    if ($id_ant == $value['id_activo_fijo']) {
                        continue;
                    }
                }
                $fmov = empty($value['fecha_mov']) ? '-' : implode('/', array_reverse(explode('-', $value['fecha_mov'])));
                $ffin = empty($value['fecha_finalizacion']) ? '-' : implode('/', array_reverse(explode('-', $value['fecha_finalizacion'])));
                $tramite = 'Fecha: ' . $fmov . "\n" . 'Fecha finalización Mov.: ' . $ffin . "\n" . 'Trámite: ' . $value['num_tramite'] . "\n" . 'Estado: ' . $value['estado_tramite'];
                $sheet0->getStyle('B' . $fila . ':H' . $fila)->applyFromArray($styleTitulos);
                $sheet0->getStyle('B' . $fila . ':H' . $fila)->getAlignment()->setWrapText(true);
                $sheet0->getStyle('A' . $fila)->getNumberFormat()->setFormatCode('');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, $i);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $fila, $value['codigo']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $fila, $value['denominacion']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $fila, $value['descripcion']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $fila, $value['estado_fun']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $fila, $tramite);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $fila, $value['ubicacion']);
                $fila++;
                $i++;
                $id_ant = $value['id_activo_fijo'];
            }
            if ($sin_resultados == 0) {
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $fila, 'Sin resultados.');
                $fila++;
            }
        }
    }
}

?>