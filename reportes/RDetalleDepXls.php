<?php
class RDetalleDepXls
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
	var $cont_detalle = 0;
	var $cont_clas = 0;
	var $cont_tipo = 0;
	var $cont_ges = 0;
	
	
	
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
	
	function datosHeader ( $detalle, $id_movimiento) {
		
		$this->datos_detalle = $detalle;
		$this->id_movimiento = $id_movimiento;
		
	}
	
	function ImprimeCabera($inicio_filas){
		
		$this->styleTitulos = array(
							      'font'  => array(
							          'bold'  => true,
							          'size'  => 8,
							          'name'  => 'Arial'
							      ),
							      'alignment' => array(
							          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
							      ),
								   'fill' => array(
								      'type' => PHPExcel_Style_Fill::FILL_SOLID,
								      'color' => array('rgb' => 'c5d9f1')
								   ),
								   'borders' => array(
								         'allborders' => array(
								             'style' => PHPExcel_Style_Border::BORDER_THIN
								         )
								     ));
									 
									   
		
		//$this->FORMAT_ACCOUNTING = '_(#,##0.00_);_( \(#,##0.00\);_( “-“??_);_(@_)';
		$this->FORMAT_ACCOUNTING = '_(""* #,##0.00_);_(""* \(#,##0.00\);_(""* 0.00_);_(@_)';
		
		$this->docexcel->setActiveSheetIndex(0)->getStyle("A".($this->fila).":P".($this->fila))->applyFromArray($this->styleTitulos);
		$this->docexcel->setActiveSheetIndex(0)->getStyle("A".($this->fila).":P".($this->fila))->getAlignment()->setWrapText(true);
					
		
		//*************************************Cabecera*****************************************//
		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[0])->setWidth(8);		
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$inicio_filas,'Gestión');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[1])->setWidth(8);		
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$inicio_filas,'Tipo');		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[2])->setWidth(20);		
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$inicio_filas,'Clasificación');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[3])->setWidth(4);		
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$inicio_filas,'Nro');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[4])->setWidth(15);		
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$inicio_filas,'Fecha Inicio');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[5])->setWidth(20);		
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$inicio_filas,'Código');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[6])->setWidth(40);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$inicio_filas,'Detalle');
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[7])->setWidth(10);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$inicio_filas,'Vida Util');		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[8])->setWidth(10);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$inicio_filas,'Vida Restante');	
			
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[9])->setWidth(15);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$inicio_filas,'Valor Historico');	
		$this->docexcel->getActiveSheet()->getStyle($this->equivalencias[9])->getNumberFormat()->setFormatCode($this->FORMAT_ACCOUNTING);
			
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[10])->setWidth(15);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$inicio_filas,'Valor Actualizado');
		$this->docexcel->getActiveSheet()->getStyle($this->equivalencias[10])->getNumberFormat()->setFormatCode($this->FORMAT_ACCOUNTING);	
			
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[11])->setWidth(15);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$inicio_filas,'Depreciación Acumulada Actulizada');
		$this->docexcel->getActiveSheet()->getStyle($this->equivalencias[11])->getNumberFormat()->setFormatCode($this->FORMAT_ACCOUNTING);	
			
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[12])->setWidth(15);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$inicio_filas,'Depreciación Anual');
		$this->docexcel->getActiveSheet()->getStyle($this->equivalencias[12])->getNumberFormat()->setFormatCode($this->FORMAT_ACCOUNTING);
		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[13])->setWidth(15);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,$inicio_filas,'Valor Vigente');	
		$this->docexcel->getActiveSheet()->getStyle($this->equivalencias[13])->getNumberFormat()->setFormatCode($this->FORMAT_ACCOUNTING);
		
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[14])->setWidth(15);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$inicio_filas,'Ajuste del Valor del Activo');
		$this->docexcel->getActiveSheet()->getStyle($this->equivalencias[14])->getNumberFormat()->setFormatCode($this->FORMAT_ACCOUNTING);
			
		$this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[15])->setWidth(15);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15,$inicio_filas,'Ajuste de Depreciación');	
		$this->docexcel->getActiveSheet()->getStyle($this->equivalencias[15])->getNumberFormat()->setFormatCode($this->FORMAT_ACCOUNTING);		
		
		//*************************************Fin Cabecera*****************************************//
		
		
		
	}
	
	function imprimirFila($fila, $value){
		
		$this->cont_detalle++;
	    $this->cont_clas++;
		$this->cont_tip++;
		$this->cont_ges++;	
		
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$fila,$value['gestion_final']);	
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$fila,$value['tipo']);	
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$fila,$value['nombre_raiz']);	
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$fila,$this->cont_detalle);	
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$fila,date("d/m/Y", strtotime( $value['fecha_ini_dep'])));
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$fila,$value['codigo']);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$fila,$value['descripcion']);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$fila,$value['vida_util_orig']);	
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$fila,$value['vida_util_final']);	
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$fila,$value['monto_vigente_orig']);	
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$fila,$value['monto_actualiz_final']);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$fila,$value['depreciacion_acum_final']);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$fila,$value['depreciacion_per_final']);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,$fila,$value['monto_vigente_final']);	
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$fila,$value['aitb_activo']);
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15,$fila,$value['aitb_depreciacion_acumulada']);
		
		
		
		


	}

    function cerrarDetalle($tmp_rec){
    	
		
					//insertamos fila de sumatoria
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$this->fila,$this->cont_detalle);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$this->fila,$tmp_rec['codigo_raiz']);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$this->fila,$tmp_rec['nombre_raiz']);					

					$this->fila_fin_par = $this->fila;
                    array_push($this->array_clasificacion, $this->fila_fin_par); //fRnk: adicionado para el totalizador de la gestión HR1166
					$formula = 'SUM(J'.$this->fila_ini_par.':J'.($this->fila_fin_par-1).')';
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$this->fila,'='.$formula);

					$formula = 'SUM(K'.$this->fila_ini_par.':K'.($this->fila_fin_par-1).')';	
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$this->fila,'='.$formula);
					
					$formula = 'SUM(L'.$this->fila_ini_par.':L'.($this->fila_fin_par-1).')';	
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$this->fila,'='.$formula);
					
					$formula = 'SUM(M'.$this->fila_ini_par.':M'.($this->fila_fin_par-1).')';	
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$this->fila,'='.$formula);
					
					$formula = 'SUM(N'.$this->fila_ini_par.':N'.($this->fila_fin_par-1).')';	
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,$this->fila,'='.$formula);
					
					$formula = 'SUM(O'.$this->fila_ini_par.':O'.($this->fila_fin_par-1).')';	
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$this->fila,'='.$formula);
					
					$formula = 'SUM(P'.$this->fila_ini_par.':P'.($this->fila_fin_par-1).')';	
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15,$this->fila,'='.$formula);
					
					
					array_push($this->array_detalle, $this->fila);
					//echo $this->fila;exit;

					
					//agrupamos celdas inicial y final
					$this->docexcel->setActiveSheetIndex(0)->mergeCells("C".($this->fila_ini_par).":C".($this->fila_fin_par));
					//Dar formato a las celdas tolizadoras
					$this->docexcel->setActiveSheetIndex(0)->getStyle("C".($this->fila_ini_par).":C".($this->fila_fin_par))->applyFromArray($this->styleArrayGroupPar);
					$this->docexcel->setActiveSheetIndex(0)->getStyle("C".($this->fila_fin_par).":P".($this->fila_fin_par))->applyFromArray($this->styleArrayTotalPar);
					$this->docexcel->setActiveSheetIndex(0)->getStyle("C".($this->fila_ini_par).":C".($this->fila_fin_par))->getAlignment()->setWrapText(true);
					
					//definir agrupador de filas
					for ($row = $this->fila_ini_par; $row <= $this->fila_fin_par-1; ++$row) {
						$this->docexcel->setActiveSheetIndex(0)
									->getRowDimension($row)
									->setOutlineLevel(3)
									->setVisible(false)
									->setCollapsed(true);
					}
					$this->fila++;
			        $this->contador++;
					
					//reiniciamos agrupadores de celda
					$this->fila_fin_par = $this->fila;
					$this->fila_ini_par = $this->fila;
					$this->cont_detalle= 0;
		
		
    }


    function cerrarClasificacion($tmp_rec){
    	//si la categoria es distinta insertamos fila agrupadora					
					//insertamos fila de sumatoria
					/*$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$this->fila,$this->cont_clas);
					$this->fila_fin = $this->fila;	
					array_push($this->array_clasificacion, $this->fila);
									
					
					
					

					
					$formulaJ = '';
					$formulaK = '';
					$formulaL = '';
					$formulaM = '';
					$formulaN = '';
					$formulaO = '';
					$formulaP = '';
					
					foreach ($this->array_detalle as $row){
						$this->docexcel->setActiveSheetIndex(0)
									->getRowDimension($row)
									->setOutlineLevel(2)
									->setVisible(false)
									->setCollapsed(true);
									
									
						
							$formulaJ.= '+J'.$row;
							$formulaK.= '+K'.$row;
							$formulaL.= '+L'.$row;
							$formulaM.= '+M'.$row;
							$formulaN.= '+N'.$row;
							$formulaO.= '+O'.$row;
							$formulaP.= '+P'.$row;								
							
						
					}
					
					//sumatoria del grupo	
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$this->fila,'='.$formulaJ);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$this->fila,'='.$formulaK);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$this->fila,'='.$formulaL);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$this->fila,'='.$formulaM);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,$this->fila,'='.$formulaN);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$this->fila,'='.$formulaO);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15,$this->fila,'='.$formulaP);
					
					$this->fila++;
			        $this->contador++;
					
					//agrupamos celdas inicial y final					
					$this->docexcel->setActiveSheetIndex(0)->mergeCells("B".($this->fila_ini).":B".($this->fila_fin));
					//Dar formato a las celdas tolizadoras
					$this->docexcel->setActiveSheetIndex(0)->getStyle("B".($this->fila_ini).":B".($this->fila_fin))->applyFromArray($this->styleArrayGroup);
					$this->docexcel->setActiveSheetIndex(0)->getStyle("B".($this->fila_fin).":P".($this->fila_fin))->applyFromArray($this->styleArrayTotal);
					$this->docexcel->setActiveSheetIndex(0)->getStyle("B".($this->fila_fin).":B".($this->fila_fin))->getAlignment()->setWrapText(true);
					//definir agrupador de filas
					
					
					
					//reiniciamos agrupadores de celda
					$this->fila_fin = $this->fila;
					$this->fila_ini = $this->fila;
					
					
					//SI cerramos un categoria iniciamos tambien las partida
					//reiniciamos agrupadores de celda
					$this->fila_fin_par = $this->fila;
					$this->fila_ini_par = $this->fila;
					
					
					//reseta detalle
					unset($this->array_detalle); // $foo is gone
					$this->array_detalle = array(); // $foo is here again
					
					$this->cont_clas = 0;*/
		
     }

     function cerrarTipo($tmp_rec){
     	            //si la categoria es distinta insertamos fila agrupadora					
					//insertamos fila de sumatoria
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$this->fila,$this->cont_tip);
					
					
					
					$this->fila_fin_cg = $this->fila;					
					
					
					
					$formulaJ = '';
					$formulaK = '';
					$formulaL = '';
					$formulaM = '';
					$formulaN = '';
					$formulaO = '';
					$formulaP = '';

					//definir agrupador de filas
					foreach ($this->array_clasificacion as $row){
						$this->docexcel->setActiveSheetIndex(0)
										->getRowDimension($row)
											->setOutlineLevel(1)
											->setVisible(false)
											->setCollapsed(true);
											
						$formulaJ.= '+J'.$row;
						$formulaK.= '+K'.$row;
						$formulaL.= '+L'.$row;
						$formulaM.= '+M'.$row;
						$formulaN.= '+N'.$row;
						$formulaO.= '+O'.$row;
						$formulaP.= '+P'.$row;	
						
					}
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$this->fila,'TOTAL AÑO '.$tmp_rec['gestion_final']);
					//sumatoria del grupo
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$this->fila,'='.$formulaJ);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$this->fila,'='.$formulaK);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$this->fila,'='.$formulaL);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12,$this->fila,'='.$formulaM);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13,$this->fila,'='.$formulaN);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14,$this->fila,'='.$formulaO);
					$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15,$this->fila,'='.$formulaP);
					
					
					$this->fila++;
			        $this->contador++;
					
					//agrupamos celdas inicial y final					
					$this->docexcel->setActiveSheetIndex(0)->mergeCells("A".($this->fila_ini_cg).":A".($this->fila_fin_cg));
					//Dar formato a las celdas tolizadoras
					$this->docexcel->setActiveSheetIndex(0)->getStyle("A".($this->fila_ini_cg).":A".($this->fila_fin_cg))->applyFromArray($this->styleArrayGroupCg);
					$this->docexcel->setActiveSheetIndex(0)->getStyle("A".($this->fila_fin_cg).":P".($this->fila_fin_cg))->applyFromArray($this->styleArrayTotalCg);
			     
			        //$this->contador++;
					//reiniciamos agrupadores de celda
					$this->fila_fin_cg = $this->fila;
					$this->fila_ini_cg = $this->fila;
					
					
					//SI cerramos un categoria iniciamos tambien las partida
					//reiniciamos agrupadores de celda
					$this->fila_fin_par = $this->fila;
					$this->fila_ini_par = $this->fila;
					
					//reiniciamos las categorias porgramticas
					$this->fila_fin_ = $this->fila;
					$this->fila_ini = $this->fila;
					
					
					//reseta detalle
					unset($this->array_clasificacion); // $foo is gone
					$this->array_clasificacion = array(); // $foo is here again
					
					$this->cont_tip = 0;
   	
	
     }

    function insertarMoneda($desc_moneda){
    	
		$this->fila++;
		$this->fila++;
    	
		
		$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$this->fila,$desc_moneda);
		$this->fila++;
		$this->ImprimeCabera($this->fila);
		
		
		$this->docexcel->setActiveSheetIndex(0)->mergeCells("B".($this->fila).":D".($this->fila));
		$this->docexcel->setActiveSheetIndex(0)->getStyle("B".($this->fila).":A".($this->fila))->applyFromArray($this->styleArrayMoneda);
					
		$this->fila++;
					
		
    }
	  		
	function imprimeDatos(){
		
		$datos = $this->datos_detalle;
		$config = $this->objParam->getParametro('config');
		$columnas = 0;
		$inicio_filas = 7;	
		
		//$this->ImprimeCabera($inicio_filas);
		
		
		$this->fila = $inicio_filas;
		$this->contador = 1;
		
		
		
		
		
		
		
		$this->array_detalle = array();
		$this->array_clasificacion = array();
		$this->array_tipo = array();
		
		//EStilos para categorias programaticas
		$this->styleArrayGroup = array(
							    'font'  => array('bold'  => true),
							     'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												       'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
								 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFCCFF')),
								 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
								     
								 );
						   
		$this->styleArrayTotal = array(
							    'font'  => array('bold'  => true),
							    'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFCCFF')),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
								);	
								
		//Estilos para partidas						
		$this->styleArrayGroupPar = array(
							     'font'  => array('bold'  => true),
							     'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP),
								 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '33FF66')),
								 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
								 );
						   
		$this->styleArrayTotalPar = array(
							    'font'  => array('bold'  => true),
							    'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
											    'color' => array('rgb' => '33FF66')),
								 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
								);	
								
		//Estilos para CLASES DE GASTO						
		$this->styleArrayGroupCg = array(
							     'font'  => array('bold'  => true),
							     'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												       'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
								 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFFF99')),
								 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
								 );
						   
		$this->styleArrayTotalCg = array(
							    'font'  => array('bold'  => true),
							    'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
											    'color' => array('rgb' => 'FFFF99')),
								 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
								);	
								
								
		//Estilos para MONEDA					
		$this->styleArrayMoneda = array(
							     'font'  => array('bold'  => true),
							     'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												       'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
								 'borders' => array(   'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
								 );						
														
		
		/////////////////////***********************************Detalle***********************************************
		
		$sw = true;
		
		//contadores 
		
		
		
		foreach($datos as $value) {
						
			//validamos agrupadores
			if($sw){
				$sw = false;
				$this->insertarMoneda($value['desc_moneda']);
				
				
				
				
				$this->fila_ini = $this->fila;
				$this->fila_fin = $this->fila;				
				$this->fila_ini_par = $this->fila;
				$this->fila_fin_par = $this->fila;				
				$this->fila_ini_cg = $this->fila;
				$this->fila_fin_cg = $this->fila;
			}
			else{
				
				 /////////////////////////////////////
				 // FIN DE GRUPO PRO CLASFICACION
				 //////////////////////////////////////				 
				 
                if($tmp_rec['id_clasificacion_raiz'] != $value['id_clasificacion_raiz']  ||  $tmp_rec['tipo'] != $value['tipo'] ||$tmp_rec['gestion_final'] != $value['gestion_final'] || $tmp_rec['id_moneda_dep'] != $value['id_moneda_dep']) {
					//si la categoria es distinta insertamos fila agrupadora					
				   $this->cerrarDetalle($tmp_rec);
				}

				////////////////////////////////
				//  FIN DEL GRUPO TIPO
				////////////////////////////////
				
				if($tmp_rec['tipo'] != $value['tipo']||$tmp_rec['gestion_final'] != $value['gestion_final'] || $tmp_rec['id_moneda_dep'] != $value['id_moneda_dep']){					
					 $this->cerrarClasificacion($tmp_rec);
				}
      
                //////////////////////////////
				//  FIN DEL GRUPO GESTION
				///////////////////////////////				
				
				if($tmp_rec['gestion_final'] != $value['gestion_final'] ||  $tmp_rec['id_moneda_dep'] != $value['id_moneda_dep']){
					$this->cerrarTipo($tmp_rec);
				}
				
				
				if($tmp_rec['id_moneda_dep'] != $value['id_moneda_dep']){
						
					//$this->fila = $this->fila + 5;
					
					
					
					$this->insertarMoneda($value['desc_moneda']);
					//reiniciamos agrupadores de celda
					$this->fila_fin_cg = $this->fila;
					$this->fila_ini_cg = $this->fila;
					
					
					//SI cerramos un categoria iniciamos tambien las partida
					//reiniciamos agrupadores de celda
					$this->fila_fin_par = $this->fila;
					$this->fila_ini_par = $this->fila;
					
					//reiniciamos las categorias porgramticas
					$this->fila_fin_ = $this->fila;
					$this->fila_ini = $this->fila;
				}
                

			} //FIN SW

			
			
			///////////////////////////////////
			//   IMPRIMIR FILAS
			///////////////////////////////////
			$this->imprimirFila($this->fila, $value);
			
			if(!$sw){
				$tmp_rec = $value;
			}
			
			$this->fila++;
			$this->contador++;
		}
		//************************************************Fin Detalle***********************************************
		
		///////////////////////
		// CIERRES FINALES
		/////////////////////

		 $this->cerrarDetalle($tmp_rec);
		 $this->cerrarClasificacion($tmp_rec);
		 $this->cerrarTipo($tmp_rec);
					
	
		
		
		//ajustar testo en beneficiario y glosa
		$this->docexcel->setActiveSheetIndex(0)->getStyle("G".($inicio_filas).":H".($fila+1))->getAlignment()->setWrapText(true);

		
		
		

	}

   
   function imprimeTitulo($sheet){
		$titulo = "CUADRO DE DEPRECIACIÓN Y ACTUALIZACIÓN DE ACTIVOS FIJOS";
        $date = new DateTime($this->objParam->getParametro('fecha_hasta'));
        $f=explode('-', $date->format('Y-m-d'));
        $mes = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
		$fechas = 'Al '.$f[2].' de '.$mes[(int)$f[1]].' de '.$f[0];
		
		
		
		//$sheet->setCellValueByColumnAndRow(0,1,$this->objParam->getParametro('titulo_rep'));
		$sheet->getStyle('C1')->getFont()->applyFromArray(array('bold'=>true,
															    'size'=>12,
															    'name'=>'Arial'));
																
		$sheet->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->setCellValue('C1',strtoupper($titulo));		
		$sheet->mergeCells('C1:P1');
		
		//DEPTOS TITLE
		$sheet->getStyle('C2')->getFont()->applyFromArray(array(
															    'bold'=>true,
															    'size'=>10,
															    'name'=>'Arial'));	
																															
		$sheet->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//$sheet->setCellValue('C2',strtoupper("ID ".$this->objParam->getParametro('id_movimiento')));
        $sheet->setCellValue('C2',$fechas);
		$sheet->mergeCells('C2:P2');
		$sheet->getStyle('C3')->getFont()->applyFromArray(array(
															    'bold'=>true,
															    'size'=>10,
															    'name'=>'Arial'));	
																															
		$sheet->getStyle('C3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->setCellValue('C3','(Expresado en bolivianos)');
		$sheet->mergeCells('C3:P3');
		
		
		// Add a drawing to the worksheet		
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo');
		$objDrawing->setDescription('Logo');
		$objDrawing->setPath(dirname(__FILE__).'/../../lib'.$_SESSION['_DIR_LOGO']);
		$objDrawing->setHeight(50);
		$objDrawing->setWorksheet($this->docexcel->setActiveSheetIndex(0));
				
		
	}

	
	
	function generarReporte(){
		
		
		$this->imprimeTitulo($this->docexcel->setActiveSheetIndex(0));
        if(!empty($this->datos_detalle)){ //fRnk: adicionado para evitar error: Invalid cell coordinate C
            $this->imprimeDatos();
        }

		//echo $this->nombre_archivo; exit;
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$this->docexcel->setActiveSheetIndex(0);
		$this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
		$this->objWriter->save($this->url_archivo);		
		
		
	}	
	
	 function currencyFormat($currency) {
	    if($currency == 'THB'):
	      $currencyFormat = "#,#0.## \à¸¿;[Red]-#,#0.## \à¸¿";
	    else:
	      $currencyFormat = "#,#0.## \$;[Red]-#,#0.## \$";
	    endif;
	    return $currencyFormat;
    }
	

}

?>