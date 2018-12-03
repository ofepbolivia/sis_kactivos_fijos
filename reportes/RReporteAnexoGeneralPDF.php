<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';

class RReporteAnexoGeneralPDF extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;		   

    function Header(){

    }    

    function  imprimeInforme()
    {

        $this->AddPage();                 
	    $this->SetMargins(10, 5, 5);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$url_imagen = dirname(__FILE__) . '/../../pxp/lib/images/Logo-BoA.png';					     
		$datos = $this->objParam->getParametro('informe');		
	    $fecha_ini = $this->objParam->getParametro('fecha_ini');
	    $fecha_fin = $this->objParam->getParametro('fecha_fin'); 
	    $rango = strtoupper($this->objParam->getParametro('nombre_periodo'));
	    $gestion = $this->objParam->getParametro('desc_gestion');
		$this->Ln(-20);
        $html = <<<EOF
		<style>
		table, th, td {   			
   			border-collapse: collapse;
   			font-family: "Calibri";
   			font-size: 10pt;   			   			
		}
		</style>
		<body>
		<table cellpadding="1">
        	<tr>
        		<th style="width: 20%" align="center" rowspan="3"><img src="$url_imagen" ></th>        		       		            
            	<th style="width: 70%" align="center" rowspan="3"><br><h3>ACTIVOS FIJOS REGISTRADOS DEL $fecha_ini AL $fecha_fin <br>
            	GESTION $gestion<br> INFORME $rango</h3></th>            	
        	</tr>
        </table>
EOF;

        $this->writeHTML($html);				
		$this->Ln();
		
        $tbl = '<table border="1" cellpadding="2">
                <tr style="font-size: 8pt; text-align: center; ">
                    <td style="width:3%;"><b>N°</b></td>
                    <td style="width:5%;"><b>N° DE PARTIDA</b></td>
                    <td style="width:25%;"><b>PARTIDA</b></td>
                    <td style="width:10%;"><b>REGISTRO EN EL SIGEP DEL '.$fecha_ini.' AL '.$fecha_fin.'</b></td>
                    <td style="width:10%;"><b>ACTIVOS FIJOS EN TRANSITO (PAGOS REALIZADOS EN EL SIGEP AL '.$rango.' '.$gestion.') QUE NO HAN SIDO DADOS DE ALTA</b></td>
                    <td style="width:10%;"><b>REVERSION/MODIFICACION ENTRE EL ERP Y SIGEP</b></td>
                    <td style="width:10%;"><b>ACTIVOS EN TRANSITO PERIODO ANTERIOR INGRESADOS AL ERP AL '.$rango.' '.$gestion.'</b></td>
                    <td style="width:10%;"><b>ACTIVOS REGISTRADOS EN EL ERP/SIGEP FUERA DE FECHA DEL PRESENTE INFORME</b></td>
                    <td style="width:10%;"><b>TOTAL GENERAL 100%</b></td>
                </tr>                
                <tr style="font-size: 8pt; text-align: center;">
                	<td style="width:3%;"></td>
                	<td style="width:5%;"></td>
                	<td style="width:25%;"></td>
                	<td style="width:10%;"></td>
                	<td style="width:10%;"><b>ANEXO Nº 1</b></td>
                	<td style="width:10%;"><b>ANEXO Nº 2</b></td>
                	<td style="width:10%;"><b>ANEXO Nº 3</b></td>
                	<td style="width:10%;"><b>ANEXO Nº 4</b></td>
                	<td style="width:10%;"></td>
                </tr>
                ';        
        $cont = 1;
		$total_sigep = 0;
		$total_importe_1 = 0;
		$total_importe_2 = 0;
		$total_importe_3 = 0;
		$total_importe_4 = 0;
		$total_general = 0;
        foreach( $datos as $record){
            $tbl .='<tr style="font-size: 8pt; ">
                <td style="width:3%; text-align: center;">'.$cont.'</td>	            
	            <td>&nbsp;'. $record["desc_codigo"].'</td>
	            <td>'. $record["desc_partida"].'</td>
	            <td style="text-align:right;">'. $record["importe_sigep"].'</td>
				<td style="text-align:right;">'. $record["importe_anexo1"].'</td>
				<td style="text-align:right;">'. $record["importe_anexo2"].'</td>
				<td style="text-align:right;">'. $record["importe_anexo3"].'</td>
				<td style="text-align:right;">'. $record["importe_anexo4"].'</td>
				<td style="text-align:right;">'. $record["importe_total"].'</td>	            
            </tr>';
			
		$total_sigep += $record["importe_sigep"];
		$total_importe_1 += $record["importe_anexo1"];
		$total_importe_2 += $record["importe_anexo2"];
		$total_importe_3 += $record["importe_anexo3"];
		$total_importe_4 += $record["importe_anexo4"];
		$total_general   += $record["importe_total"];			
            $cont++;
        }
        $tbl.='
			<tr style="font-size:10pt;">                
	            <td style="text-align:center;  width:309px;">TOTAL</td>
	            <td style="text-align:right; width:10%;">'. $total_sigep.'</td>
				<td style="text-align:right; width:10%;">'. $total_importe_1.'</td>
				<td style="text-align:right; width:10%;">'. $total_importe_2.'</td>
				<td style="text-align:right; width:10%;">'. $total_importe_3.'</td>
				<td style="text-align:right; width:10%;">'. $total_importe_4.'</td>
				<td style="text-align:right; width:10%;">'. $total_general.'</td>			
			</tr>
        </table>';    
        $this->Ln(5);    
        $this->writeHTML($tbl);
		
		$observ = strtoupper($this->objParam->getParametro('observaciones'));
		if($observ!='' || $observ !=null){
		$this->Ln(-5);			
		$tbl='<div><b><h4>OBSERVACIONES:</h4></b><br>
		<p style="font-size:8pt;">'.$observ.'</p>
		</div>';
		$this->writeHTML($tbl);		
		}
		$this->Ln();				

    }
    
	function  imprimeAnexo1()
	    {
		    $this->AddPage();                     
		    $this->SetMargins(2, 50, 2);
	        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
			$url_imagen = dirname(__FILE__) . '/../../pxp/lib/images/Logo-BoA.png';
			$datos = $this->objParam->getParametro('anexo1');								     
		    $rango = strtoupper($this->objParam->getParametro('nombre_periodo'));
		    $gestion = $this->objParam->getParametro('desc_gestion');	
					
	        $html = <<<EOF
			<style>
			table, th, td {   			
	   			border-collapse: collapse;
	   			font-family: "Calibri";
	   			font-size: 10pt;   			
			}
			</style>
			<body>
			<table cellpadding="1">
	        	<tr>
	        		<th style="width: 20%" align="center" rowspan="3"><img src="$url_imagen" ></th>	        		            
	            	<th style="width: 70%" align="center" rowspan="3"><br><h3>ACTIVOS FIJOS EN TRANSITO (PAGOS REALIZADOS EN EL SIGEP AL $rango $gestion)
	            	<br>ANEXO N° 1</h3></th>            	
	        	</tr>
	        </table>
EOF;
	
	        $this->writeHTML($html);					      					
			$this->Ln();
			
	        $tbl = '<table border="1" cellpadding="2">	               
                <tr style="font-size: 8pt; text-align: center;">
                	<td style="width:3%;"><b>N°</b></td>
                	<td style="width:5%;"><b>N° DE PARTIDA</b></td>
                	<td style="width:20%;"><b>PARTIDA</b></td>
                	<td style="width:7%;"><b>C31</b></td>
                	<td style="width:8%;"><b>MONTOS/CONTRATO DE COMPRA</b></td>
                	<td style="width:8%;"><b>ALTA EN EL ERP</b></td>
                    <td style="width:8%;"><b>MONTO EN TRANSITO</b></td>
                    <td style="width:8%;"><b>MONTO ACUMULADO PERIODOS ANTERIORES</b></td>
                    <td style="width:8%;"><b>MONTO EN EL PERIODO</b></td>
                    <td style="width:17%;"><b>OBSERVACIONES</b></td>
                    <td style="width:8%;"><b>UNIDAD SOLICITANTE</b></td>                	
                </tr>	              
	                ';  
					      
	        $cont = 1;
			
			$total_mon_cotrato = 0;
			$total_mon_alt_erp = 0;
			$total_mon_transi  = 0;
			$total_mon_pagado  = 0;
			$total_mon_actual  = 0;
			
			$total_grupo_con   = 0;
			$total_grupo_alt   = 0;
			$total_grupo_tra   = 0;
			$total_grupo_pag   = 0;
			$total_grupo_act   = 0;
			
   
    $estacion=array();    

    foreach($datos as $value){
    	$valor=$value['desc_codigo'];
         if(!in_array($valor, $estacion)){
             $estacion[]=$valor;
         }         
       }			
			
		foreach ($estacion as $value) {					
	        foreach( $datos as $record){	        	
	        	if($record["desc_codigo"]==$value){	        	
	            $tbl .='<tr style="font-size: 8pt;">
	                <td style="width:3%; text-align: center;">'.$cont.'</td>	            
		            <td style="width:5%;">&nbsp;'. $record["desc_codigo"].'</td>
		            <td style="width:20%;">'. $record["desc_nombre"].'</td>
		            <td style="text-align:center; width:7%;">'. $record["c31"].'</td>
					<td style="text-align:right; width:8%;">'. $record["monto_contrato"].'</td>
					<td style="text-align:right; width:8%;">'. $record["monto_alta"].'</td>
					<td style="text-align:right; width:8%;">'. $record["monto_transito"].'</td>
					<td style="text-align:right; width:8%;">'. $record["monto_pagado"].'</td>
					<td style="text-align:right; width:8%;">'. $record["monto_tercer"].'</td>
					<td style="text-align:right; width:17%;">'. $record["observaciones"].'</td>
					<td style="text-align:right; width:8%;">'. $record["nombre_unidad"].'</td>	            
	            </tr>';
				
			$total_mon_cotrato += $record["monto_contrato"];
			$total_mon_alt_erp += $record["monto_alta"];
			$total_mon_transi  += $record["monto_transito"];
			$total_mon_pagado  += $record["monto_pagado"];
			$total_mon_actual  += $record["importe_tercer"];
	            $cont++;
	        }
	       }
	        $tbl.='
				<tr style="font-size:10pt;">                
		            <td style="text-align:center;  width:341.5px;">TOTAL GRUPO '.$value.'</td>
		            <td style="text-align:right; width:8%;">'. $total_mon_cotrato.'</td>
					<td style="text-align:right; width:8%;">'. $total_mon_alt_erp.'</td>
					<td style="text-align:right; width:8%;">'. $total_mon_transi.'</td>
					<td style="text-align:right; width:8%;">'. $total_mon_pagado.'</td>					
					<td style="text-align:right; width:8%;">'. $total_mon_actual.'</td>
					<td style="text-align:right; width:17%;"></td>
					<td style="text-align:right; width:8%;"></td>			
				</tr>				
	        	';    	        
			
			$total_grupo_con += $total_mon_cotrato;
			$total_grupo_alt += $total_mon_alt_erp;
			$total_grupo_tra += $total_mon_transi;
			$total_grupo_pag += $total_mon_pagado;
			$total_grupo_act += $total_mon_actual;			
			
	       }

	        $tbl.='
				<tr style="font-size:10pt;">                
		            <td style="text-align:center;  width:341.5px;">TOTAL ANEXO 1 </td>
		            <td style="text-align:right; width:8%;">'. $total_grupo_con.'</td>
					<td style="text-align:right; width:8%;">'. $total_grupo_alt.'</td>
					<td style="text-align:right; width:8%;">'. $total_grupo_tra.'</td>
					<td style="text-align:right; width:8%;">'. $total_grupo_pag.'</td>
					<td style="text-align:right; width:8%;">'. $total_grupo_act.'</td>
					<td style="text-align:right; width:17%;"></td>
					<td style="text-align:right; width:8%;"></td>							
				</tr>
	        </table>';    
			
	        $this->Ln(5);    
	        $this->writeHTML($tbl);				
	
	   }
	   	    	
		function imprimeAnexo2()
		{
	        $this->AddPage();                 
		    $this->SetMargins(2, 5, 2);
	        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
			$url_imagen = dirname(__FILE__) . '/../../pxp/lib/images/Logo-BoA.png';		      
			$datos = $this->objParam->getParametro('anexo2');		     
		    $rango = strtoupper($this->objParam->getParametro('nombre_periodo'));
		    $gestion = $this->objParam->getParametro('desc_gestion');
		    $this->Ln(-45);
	        $html = <<<EOF
			<style>
			table, th, td {   			
	   			border-collapse: collapse;
	   			font-family: "Calibri";
	   			font-size: 10pt;   			
			}
			</style>
			<body>
			<table cellpadding="1">
	        	<tr>
	        		<th style="width: 20%" align="center" rowspan="3"><img src="$url_imagen" ></th>	        		            
	            	<th style="width: 70%" align="center" rowspan="3"><br><h3>REVERSION / MODIFICACION ENTRE EL ERP Y SIGEP	            	
	            	<br>ANEXO N° 2</h3></th>            	
	        	</tr>
	        </table>
EOF;
	
	        $this->writeHTML($html);
			$this->Ln();
			
	        $tbl = '<table border="1" cellpadding="2">
                <tr style="font-size: 8pt; text-align: center;">
                	<td style="width:3%;"><b>Nº</b></td>
                	<td style="width:5%;"><b>Nº PARTIDA</b></td>
                	<td style="width:25%;"><b>PARTIDA</b></td>
                	<td style="width:10%;"><b>C31</b></td>
                	<td style="width:10%;"><b>MONTO SIGEP</b></td>
                	<td style="width:10%;"><b>MONTO ERP</b></td>
                	<td style="width:10%;"><b>DIFERENCIA</b></td>
                	<td style="width:25%;"><b>OBSERVACIONES</b></td>                	
                </tr>';
	        $cont = 1;
		
			$total_mon_sigep   = 0;
			$total_mon_erp     = 0;
			$total_mon_dife    = 0;						
			
			$total_grupo_sigep = 0;
			$total_grupo_erp   = 0;
			$total_grupo_dife  = 0;			
			
   
		    $estacion=array();    
		
		    foreach($datos as $value){
		    	$valor=$value['desc_codigo'];
		         if(!in_array($valor, $estacion)){
		             $estacion[]=$valor;
		         }         
		       }			
					
				foreach ($estacion as $value) {					
			        foreach( $datos as $record){	        	
			        	if($record["desc_codigo"]==$value){	        	
			            $tbl .='<tr style="font-size: 8pt;">
			                <td style="width:3%; text-align: center;">'.$cont.'</td>	            
				            <td style="width:5%;">&nbsp;'. $record["desc_codigo"].'</td>
				            <td style="width:25%;">'. $record["desc_nombre"].'</td>
				            <td style="text-align:center; width:10%;">'. $record["c31"].'</td>
							<td style="text-align:right; width:10%;">'. $record["monto_sigep"].'</td>
							<td style="text-align:right; width:10%;">'. $record["monto_erp"].'</td>
							<td style="text-align:right; width:10%;">'. $record["diferencia"].'</td>														
							<td style="text-align:right; width:25%;">'. $record["observaciones"].'</td>							
			            </tr>';
						
					$total_mon_sigep += $record["monto_sigep"];
					$total_mon_erp 	 += $record["monto_erp"];
					$total_mon_dife  += $record["diferencia"];
										
			            $cont++;
			        }
			       }
			        $tbl.='
						<tr style="font-size:10pt;">                
				            <td style="text-align:center;  width:419.5px;">TOTAL GRUPO '.$value.'</td>
				            <td style="text-align:right; width:10%;">'. $total_mon_sigep.'</td>
							<td style="text-align:right; width:10%;">'. $total_mon_erp.'</td>							
							<td style="text-align:right; width:10%;">'. $total_mon_dife.'</td>							
							<td style="text-align:right; width:25%;"></td>			
						</tr>				
			        	';    	        
					
					$total_grupo_sigep += $total_mon_sigep;
					$total_grupo_erp   += $total_mon_erp;
					$total_grupo_dife  += $total_mon_dife;
			       }
		
			        $tbl.='
						<tr style="font-size:10pt;">                
				            <td style="text-align:center;  width:419.5px;">TOTAL ANEXO 2 </td>
				            <td style="text-align:right; width:10%;">'. $total_grupo_sigep.'</td>
							<td style="text-align:right; width:10%;">'. $total_grupo_erp.'</td>
							<td style="text-align:right; width:10%;">'. $total_grupo_dife.'</td>							
							<td style="text-align:right; width:25%;"></td>	
						</tr>
			        </table>';    
			        $this->Ln(5);    
			        $this->writeHTML($tbl);	
					
		}
	
		function imprimeAnexo3()
		{
	        $this->AddPage();                 
		    $this->SetMargins(2, 5, 2);
	        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
			$url_imagen = dirname(__FILE__) . '/../../pxp/lib/images/Logo-BoA.png';		      
			$datos = $this->objParam->getParametro('anexo3');					     
		    $rango = strtoupper($this->objParam->getParametro('nombre_periodo'));
		    $gestion = $this->objParam->getParametro('desc_gestion');
		    
	        $html = <<<EOF
			<style>
			table, th, td {   			
	   			border-collapse: collapse;
	   			font-family: "Calibri";
	   			font-size: 10pt;   			
			}
			</style>
			<body>
			<table cellpadding="1">
	        	<tr>
	        		<th style="width: 20%" align="center" rowspan="3"><img src="$url_imagen" ></th>	        		            
	            	<th style="width: 70%" align="center" rowspan="3"><br><h3>ACTIVOS FIJOS EN TRANSITO PERIODO ANTERIOR
	            	INGRESADOS AL ERP AL $rango $gestion	            	
	            	<br>ANEXO N° 3</h3></th>            	
	        	</tr>
	        </table>
EOF;
	
	        $this->writeHTML($html);
			$this->Ln();
			
	        $tbl = '<table border="1" cellpadding="2">
                <tr style="font-size: 8pt; text-align: center;">
                	<td style="width:3%;"><b>Nº</b></td>
                	<td style="width:5%;"><b>Nº PARTIDA</b></td>
                	<td style="width:25%;"><b>PARTIDA</b></td>
                	<td style="width:10%;"><b>C31</b></td>
                	<td style="width:30%;"><b>DETALLE</b></td>
                	<td style="width:12%;"><b>MONTO</b></td>                	
                	<td style="width:15%;"><b>UNIDAD SOLICITANTE</b></td>                	
                </tr>';
	        $cont = 1;
		
			$total_monto  	   = 0;
			$total_grupo_mon   = 0;			
			
   
		    $estacion=array();    
		
		    foreach($datos as $value){
		    	$valor=$value['desc_codigo'];
		         if(!in_array($valor, $estacion)){
		             $estacion[]=$valor;
		         }         
		       }			
					
				foreach ($estacion as $value) {					
			        foreach( $datos as $record){	        	
			        	if($record["desc_codigo"]==$value){	        	
			            $tbl .='<tr style="font-size: 8pt;">
			                <td style="width:3%; text-align: center;">'.$cont.'</td>	            
				            <td style="width:5%;">&nbsp;'. $record["desc_codigo"].'</td>
				            <td style="width:25%;">'. $record["desc_nombre"].'</td>
				            <td style="text-align:center; width:10%;">'. $record["c31"].'</td>
							<td style="text-align:right; width:30%;">'. $record["detalle_c31"].'</td>
							<td style="text-align:right; width:12%;">'. $record["monto_erp"].'</td>																				
							<td style="text-align:right; width:15%;">'. $record["observaciones"].'</td>							
			            </tr>';
						
					$total_monto += $record["monto_erp"];					
										
			            $cont++;
			        }
			       }
			        $tbl.='
						<tr style="font-size:10pt;">                
				            <td style="text-align:center;  width:712.5px;">TOTAL GRUPO '.$value.'</td>
				            <td style="text-align:right; width:12%;">'. $total_monto.'</td>														
							<td style="text-align:right; width:15%;"></td>			
						</tr>				
			        	';    	        
					
					$total_grupo_mon += $total_monto;					
			       }
		
			        $tbl.='
						<tr style="font-size:10pt;">                
				            <td style="text-align:center;  width:712.5px;">TOTAL ANEXO 3 </td>
				            <td style="text-align:right; width:12%;">'. $total_grupo_mon.'</td>													
							<td style="text-align:right; width:15%;"></td>	
						</tr>
			        </table>';    
			        $this->Ln(5);    
			        $this->writeHTML($tbl);	
					
		}
		function imprimeAnexo4()
		{
	        $this->AddPage();		                                 
		    $this->SetMargins(2, 5, 2);
	        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
			$url_imagen = dirname(__FILE__) . '/../../pxp/lib/images/Logo-BoA.png';		      
			$datos = $this->objParam->getParametro('anexo4');								     
		    $rango = strtoupper($this->objParam->getParametro('nombre_periodo'));
		    $gestion = $this->objParam->getParametro('desc_gestion');			
			
	        $html = <<<EOF
			<style>
			table, th, td {   			
	   			border-collapse: collapse;
	   			font-family: "Calibri";
	   			font-size: 10pt;   			
			}
			</style>
			<body>
			<table cellpadding="1">
	        	<tr>	
	        		<th style="width: 20%" align="center" rowspan="3"><img src="$url_imagen" ></th>        		            
	            	<th style="width: 70%" align="center" rowspan="3"><br><h3>ACTIVOS REGISTRADOS EN EL ERP/SIGEP FUERA DE FECHA DEL PRESENTE INFORME 	            	
	            	<br>ANEXO N° 4</h3></th>            	
	        	</tr>
	        </table>
EOF;
	
	        $this->writeHTML($html);
			$this->Ln();
			
	        $tbl = '<table border="1" cellpadding="2">
                <tr style="font-size: 8pt; text-align: center;">
                	<td style="width:3%;"><b>Nº</b></td>
                	<td style="width:5%;"><b>Nº PARTIDA</b></td>
                	<td style="width:25%;"><b>PARTIDA</b></td>
                	<td style="width:10%;"><b>C31</b></td>
                	<td style="width:10%;"><b>MONTO SIGEP</b></td>
                	<td style="width:10%;"><b>MONTO ERP</b></td>                	
                	<td style="width:10%;"><b>DIFERENCIA</b></td>
                	<td style="width:26%;"><b>OBSERVACIONES</b></td>                	
                </tr>';
	        $cont = 1;
		
			$total_mon_sigep   = 0;
			$total_mon_erp     = 0;
			$total_mon_dife    = 0;						
			
			$total_grupo_sigep = 0;
			$total_grupo_erp   = 0;
			$total_grupo_dife  = 0;			
			
   
		    $estacion=array();    
		
		    foreach($datos as $value){
		    	$valor=$value['desc_codigo'];
		         if(!in_array($valor, $estacion)){
		             $estacion[]=$valor;
		         }         
		       }			
					
				foreach ($estacion as $value) {				
			        foreach( $datos as $record){	        	
			        	if($record["desc_codigo"]==$value){	        	
			            $tbl .='<tr style="font-size: 8pt;">
			                <td style="width:3%; text-align: center;">'.$cont.'</td>	            
				            <td style="width:5%;">&nbsp;'. $record["desc_codigo"].'</td>
				            <td style="width:25%">'. $record["desc_nombre"].'</td>
				            <td style="text-align:center; width:10%;">'. $record["c31"].'</td>
							<td style="text-align:right; width:10%;">'. $record["monto_sigep"].'</td>
							<td style="text-align:right; width:10%;">'. $record["monto_erp"].'</td>
							<td style="text-align:right; width:10%;">'. $record["diferencia"].'</td>																				
							<td style="text-align:right; width:26%;">'. $record["observaciones"].'</td>							
			            </tr>';
						
					$total_mon_sigep += $record["monto_sigep"];
					$total_mon_erp   += $record["monto_erp"];
					$total_mon_dife  += $record["diferencia"];					
										
			            $cont++;
			        }
			       }					
			        $tbl.='
						<tr style="font-size:10pt;">                
				            <td style="text-align:center;  width:419.5px;">TOTAL GRUPO '.$value.'</td>
				            <td style="text-align:right; width:10%;">'. $total_mon_sigep.'</td>
				            <td style="text-align:right; width:10%;">'. $total_mon_erp.'</td>
				            <td style="text-align:right; width:10%;">'. $total_mon_dife.'</td>														
							<td style="text-align:right; width:26%;"></td>			
						</tr>				
			        	';    	        
					
					$total_grupo_sigep += $total_mon_sigep;
					$total_grupo_erp   += $total_mon_erp;
					$total_grupo_dife  += $total_mon_dife;											
			       }
		
			        $tbl.='
						<tr style="font-size:10pt;">                
				            <td style="text-align:center;  width:419.5px;">TOTAL ANEXO 4 </td>
				            <td style="text-align:right; width:10%;">'. $total_grupo_sigep.'</td>
				            <td style="text-align:right; width:10%;">'. $total_grupo_erp.'</td>
				            <td style="text-align:right; width:10%;">'. $total_grupo_dife.'</td>
							<td style="text-align:right; width:26%;"></td>	
						</tr>
			        </table>';    
			        $this->Ln(5);    
			        $this->writeHTML($tbl);				        

		}		
}
?>