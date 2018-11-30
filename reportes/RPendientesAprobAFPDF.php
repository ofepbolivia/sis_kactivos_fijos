<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
require_once(dirname(__FILE__) . '/../../lib/tcpdf/tcpdf_barcodes_2d.php');
class RPendientesAprobAFPDF extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
    var $sum=0;

    function Header() {
        $this->Ln(3);

        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 16,5,30,20);
        $this->ln(5);
        $this->SetMargins(12, 40, 2);
        $this->SetFont('','B',10);
        $this->Cell(0,5,'DEPARTAMENTO ACTIVOS FIJOS',0,1,'C');
        $this->Cell(0,5,'ACTIVOS FIJOS PENDIENTES DE APROBACIÓN',0,1,'C');
        $this->Cell(0,5,'Del: '.$this->objParam->getParametro('fecha_ini').' Al '.$this->objParam->getParametro('fecha_fin').' Estado: PENDIENTE',0,1,'C');

        $this->SetFont('','',7);
        $this->ln(5);

        $control = $this->objParam->getParametro('rep_pendiente_aprobacion');
        $this->columnsGrid($control);
    }

    //start BVP
    public function columnsGrid($tipo){

//        var_dump($tipo);exit;

        $hiddes = explode(',', $tipo);
        $paprc = '';
        $pafpr = '';
        $paglo = '';
        $panom = '';
        $padep = '';


        //widths
        $tam1=20;
        $tam2=20;
        $tam3=60;
        $tam4=40;
        $tam5=40;


        $num = 0;
        $total = 0;

        for ($i=0; $i <count($hiddes) ; $i++) {
            switch ($hiddes[$i]) {
                case 'pprc': $paprc = 'prc'; break;
                case 'pfpr': $pafpr = 'fpr'; break;
                case 'pglo': $paglo = 'glo'; break;
                case 'pnom': $panom = 'nom'; break;
                case 'pdep': $padep = 'dep'; break;

            }
        }

        if ($paprc=='') {
            $tam1 = 0;
        }if ($pafpr=='') {
            $tam2 = 0;
        }if ($paglo=='') {
            $tam3 = 0;
        }if ($panom=='') {
            $tam4 = 0;
        }if ($padep=='') {
            $tam5 = 0;
        }

        //tomamos los tamanios de las columnas no mostradas y las distribuimos a las otras presentes
        $xpage = 170;//∑ tam^n ai = an
        $cont = 0;
        $resul = $tam1+$tam2+$tam3+$tam4+$tam5;
        $alca = $xpage - $resul;
        $n = count($hiddes);
        //distribucion de tamanios
        if($alca>0){
            $total = $alca/$n;
            while ($resul<$xpage) {
                $cont += 0.001;
                $resul += 1;
            }
            $total += $cont;
        }else{
            $total= 0;
        }
        $hGlobal=7;

        $this->SetFontSize(7);
        $this->SetFont('', 'B');

        //$this->Ln(6); no si nesto
        $this->MultiCell(8, $hGlobal,'Nº',1,'C',false,0,'','',true,0,false,true,0,'T',false);
        ($paprc=='prc')?$this->MultiCell($tam1+$total, $hGlobal, 'Nº PROCESO COMPRA',1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
        ($pafpr=='fpr')?$this->MultiCell($tam2+$total, $hGlobal, 'FECHA DE PROCESO', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
        ($paglo=='glo')?$this->MultiCell($tam3+$total, $hGlobal, 'GLOSA', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
        ($panom=='nom')?$this->MultiCell($tam4+$total, $hGlobal, 'NOMBRE USUARIO', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
        ($padep=='dep')?$this->MultiCell($tam5+$total, $hGlobal, 'DEPARTAMENTO', 1,'C',false,0,'','',true,0,false,true,0,'T',false):'';
        }

    function setDatos($datos) {

         $this->datos = $datos;

//      var_dump($datos);exit;
    }

    function  generarReporte()
    {
        $this->AddPage();
        $this->SetMargins(12, 40, 2);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->Ln();
        //variables para la tabla

        $i=1;
        $contador = 1;
        $tipo = $this->objParam->getParametro('tipo_reporte');
        $select = $this->objParam->getParametro('rep_pendiente_aprobacion');


        $hiddes = explode(',', $select);

        $paprc = '';
        $pafpr = '';
        $paglo = '';
        $panom = '';
        $padep = '';

        $tam1=20;
        $tam2=20;
        $tam3=60;
        $tam4=40;
        $tam5=40;


        //asigna a cada variable su valor recibido desde la vista
        for ($j=0; $j <count($hiddes) ; $j++) {
            switch ($hiddes[$j]) {
                case 'pprc': $paprc = 'prc'; break;
                case 'pfpr': $pafpr = 'fpr'; break;
                case 'pglo': $paglo = 'glo'; break;
                case 'pnom': $panom = 'nom'; break;
                case 'pdep': $padep = 'dep'; break;
            }
        }
        if ($paprc=='') {
            $tam1 = 0;
        }if ($pafpr=='') {
            $tam2 = 0;
        }if ($paglo=='') {
            $tam3 = 0;
        }if ($panom=='') {
            $tam4 = 0;
        }if ($padep=='') {
            $tam5 = 0;
        }

        $xpage = 170;//∑ tam^n ai = an
        $cont = 0;
        $resul = $tam1+$tam2+$tam3+$tam4+$tam5;
        $alca = $xpage - $resul;
        $n = count($hiddes);

        if($alca>0){
            $total = $alca/$n;
            while ($resul<$xpage) {
                $cont += 0.001;
                $resul += 1;
            }
            $total += $cont;
        }else{
            $total= 0;
        }

        //arreglo para tablewidths estatica
        $datos = array('t1'=>8,
            'prc'=>$tam1+$total,
            'fpr'=>$tam2+$total,
            'glo'=>$tam3+$total,
            'nom'=>$tam4+$total,
            'dep'=>$tam5+$total);

        $this->tablewidths=$this->filterArray($datos);
        $tablenums0=array('t1'=>0,'prc'=>0,'fpr'=>0,'glo'=>0,'nom'=>0,'dep'=>0);  //1
        $tablenums1=array('t1'=>0,'prc'=>0,'fpr'=>0,'glo'=>0,'nom'=>0,'dep'=>0);  //2
        $tablenums0Real = $this->filterArray($tablenums0);
        $tablenums1Real = $this->filterArray($tablenums1);
        $this->tablealigns=array('C','C','R','L','C','C');
//para el detalle
        foreach($this->datos as $record){
//var_dump($this->datos);exit;

                $this->SetFont('','',7);

                    $this->tableborders = array('RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB', 'RLTB');
//                    $this->tablenumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 2,0);
                    $this->tablenumbers =$tablenums0Real;
                    $RowArray = array(
//
                        's0' => $i,
                        's1' => $record['nro_tramite'],
                        's2' => $record['fecha_ini'] == '-' ? '-' : date("d/m/Y", strtotime($record['fecha_ini'])),
                        's3' => $record['glosa'],
                        's4' => $record['funcionario'],
                        's5' => $record['depto']

                    );
                    if ($padep==''){
                        unset($RowArray['s1']);
                    }if ($pafpr==''){
                        unset($RowArray['s2']);
                    }if ($paglo=='') {
                        unset($RowArray['s3']);
                    }if ($panom=='') {
                        unset($RowArray['s4']);
                    }if ($padep=='') {
                        unset($RowArray['s5']);
                    }
                    $this->MultiRow($RowArray);


                    $i++;

            }

    }
    function filterArray($table){

        $resp = array();
        $control = $this->objParam->getParametro('rep_pendiente_aprobacion');
        $hiddes = explode(',', $control);
        $paprc = '';
        $pafpr = '';
        $paglo = '';
        $panom = '';
        $padep = '';

        //asigna a cada variable su valor recibido desde la vista
        for ($j=0; $j <count($hiddes) ; $j++) {
            switch ($hiddes[$j]) {
                case 'pprc': $paprc = 'prc'; break;
                case 'pfpr': $pafpr = 'fpr'; break;
                case 'pglo': $paglo = 'glo'; break;
                case 'pnom': $panom = 'nom'; break;
                case 'pdep': $padep = 'dep'; break;
            }
        }

        $proces = $table;



        foreach ($proces as $key => $value) {
            if($paprc==''){
                unset($proces['prc']);
            }
            if($pafpr==''){
                unset($proces['fpr']);
            }
            if($paglo==''){
                unset($proces['glo']);
            }
            if($panom==''){
                unset($proces['nom']);
            }
            if($padep==''){
                unset($proces['dep']);
            }
        }
        $resp=array();
        foreach ($proces as $value) {
            array_push($resp,$value);
        }
//        var_dump($resp);exit;
        return  $resp;

    } //endBVP
}
?>
