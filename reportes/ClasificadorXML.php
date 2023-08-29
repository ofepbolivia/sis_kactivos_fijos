<?php
//fRnk: para plantilla de importación excel
include(dirname(__FILE__) . '/../../lib/DatosGenerales.php');
include(dirname(__FILE__) . "/../../lib/lib_modelo/conexion.php");

$xml = new SimpleXMLElement('<xml/>');
try {
    $cone = new conexion();
    $link = $cone->conectarpdo();
    if (isset($_GET['proveedor']))
        $sql = "SELECT DISTINCT rotulo_comercial AS nombre, nit AS codigo FROM param.tproveedor WHERE rotulo_comercial<>''";
    else
        $sql = "SELECT DISTINCT nombre, codigo_completo_tmp AS codigo FROM kaf.tclasificacion";
    $consulta = $link->query($sql);
    $consulta->execute();
    $data = $consulta->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as $item) {
        $node = $xml->addChild('item');
        $node->addChild('codigo', $item["codigo"]);
        $node->addChild('nombre', $item["nombre"]);
    }
    Header('Content-type: text/xml');
    print($xml->asXML());
} catch (Exception $ex) {
    var_dump($ex);
}

