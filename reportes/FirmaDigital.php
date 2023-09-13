<style>
    body {
        background-color: #F0F0F0;
        font: normal 11px/13px arial, tahoma, helvetica, sans-serif;
    }

    .container {
        max-width: 500px;
        margin: auto;
    }

    table {
        background-color: #fff;
    }

    table td {
        border: 1px solid #ccc;
    }
</style>
<div class="container">
    <?php
    //fRnk: despliega firmadores del documento
    include(dirname(__FILE__) . '/../../lib/DatosGenerales.php');
    include(dirname(__FILE__) . "/../../lib/lib_modelo/conexion.php");

    try {
        $cone = new conexion();
        $link = $cone->conectarpdo();
        $sql = "SELECT
            te.codigo as estado,
            ewf.fecha_reg as fecha_ini,
            CAST(ewf.fecha_reg AS date) as fecha,
            to_char(ewf.fecha_reg, 'HH24:MI:SS') as hora,
            te.nombre_estado as nombre,
            usu.cuenta,
            fun.desc_funcionario1  as funcionario,
            fun.descripcion_cargo,
            fir.firmado
            FROM  wf.testado_wf ewf
            INNER JOIN  wf.ttipo_estado te on ewf.id_tipo_estado = te.id_tipo_estado
            LEFT JOIN   segu.tusuario usu on usu.id_usuario = ewf.id_usuario_reg
            LEFT JOIN  orga.vfuncionario_cargo_lugar_todos fun on fun.id_funcionario = ewf.id_funcionario
            LEFT JOIN  param.tdepto depto on depto.id_depto = ewf.id_depto
            LEFT JOIN  kaf.tmovimiento mov on mov.id_proceso_wf = ewf.id_proceso_wf
            LEFT JOIN kaf.tfirma_dig fir on fir.id_movimiento = mov.id_movimiento and fir.id_proceso_wf = mov.id_proceso_wf 
            AND fir.id_estado_wf = ewf.id_estado_wf
            WHERE 
            mov.id_movimiento = " . $_GET['m'] . "
            AND te.codigo <> 'borrador'
            AND fir.firmado='si'
            ORDER BY ewf.fecha_reg,ewf.id_estado_wf";
        $consulta = $link->query($sql);
        $consulta->execute();
        $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $html = '<table cellspacing="0" cellpadding="4">';
        $html .= '<tr style="background-color: #D7D7D7"><th>Estado</th><th>Funcionario</th><th>Cargo</th><th>Fecha</th><th>Hora</th></tr>';
        foreach ($data as $item) {
            $fecha = strtotime($item['fecha']);
            $html .= '<tr>';
            $html .= '<td>' . $item['nombre'] . '</td>';
            $html .= '<td>' . $item['funcionario'] . '</td>';
            $html .= '<td>' . $item['descripcion_cargo'] . '</td>';
            $html .= '<td>' . date("d/m/Y", $fecha) . '</td>';
            $html .= '<td>' . $item['hora'] . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        echo $html;
    } catch (Exception $ex) {
        var_dump($ex);
    }
    ?>
</div>