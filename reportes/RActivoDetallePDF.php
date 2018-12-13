<?php
class RActivoDetallePDF extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
	var $codigo;
	
    function Header() {
        $height = 30;
        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib'.$_SESSION['_DIR_LOGO'], 5, 8, 60, 15);
        $this->Cell(40, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->SetFontSize(16);
        $this->SetFont('','B');
        $this->Cell(150, $height, 'REPORTE DE ACTIVO EN DETALLE', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Ln();
    }
	
    function setDatos($datos) {
        $this->datos = $datos;
		
    }
    function generarReporte() {
        $this->reporteActivo();
    }
	
    function  reporteActivo()
    {    					
        $this->SetMargins(1,35,2);
        $this->setFontSubsetting(false);
        $this->AddPage();
        $this->SetFont('','B',8);

        $conf_det_tablewidths=array(10,10,13,21,38,33,14,14,14,18,16,15,31,30);
        $conf_det_tablealigns=array('C','C','C','C','L','C','C','C','C','C','C','C','L','L');

        $this->tablewidths=$conf_det_tablewidths;
        $this->tablealigns=$conf_det_tablealigns;


        $RowArray = array(
			'No.',
            'TIPO',
            'SUB-TIPO',
            'CODIGO',
            'DESCRIPCION',
            'CLASIFICACION',
            'MARCA',
            'SERIAL',
            'ESTADO',
            'ESTADO FUNCIONAL',
            'FECHA COMPRA',
            'C31',
            'UBICACION',
            'RESPONSABLE'
        );
        $this-> MultiRow($RowArray,false,1);
        $this->SetFont('','',7);
        $conf_det_tablewidths=array(10,10,13,21,38,33,14,14,14,18,16,15,31,30);
        $conf_det_tablealigns=array('L','C','C','L','L','L','C','C','C','C','C','C','L','L');
        $this->tablewidths=$conf_det_tablewidths;
        $this->tablealigns=$conf_det_tablealigns;

        $cont_filas = 1;
        foreach ($this->datos as $Row) {

            $RowArray = array(
            	'No.' => $cont_filas,
                'TIPO' => $Row['tipo'],
                'SUB-TIPO'=> $Row['subtipo'],
                'CODIGO' => $Row['codigo'],
                'DESCRIPCION' => $Row['descripcion'],
                'CLASIFICACION' => $Row['denominacion'],
                'MARCA' => $Row['marca'],
                'SERIAL' =>  $Row['nro_serie'],
                'ESTADO' =>  $Row['estado'],
                'ESTADO FUNCIONAL' =>  $Row['estado_funcional'],
                'FECHA COMPRA' =>  $Row['fecha_compra'],
                'C31' =>  $Row['c31'],
                'UBICACION' =>  $Row['ubicacion'],
                'RESPONSABLE' =>  $Row['responsable']             

            );
            $this-> MultiRow($RowArray);			
			$cont_filas++;
        }
  
    }

}
?>