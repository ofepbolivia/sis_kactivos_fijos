<style>
    body {
        background-color: #F0F0F0;
        font: normal 11px/13px arial, tahoma, helvetica, sans-serif;
    }

    .container {
        max-width: 500px;
        margin: auto;
        border: 1px solid #ccc;
        padding: 20px;
        background: #fff;
        border-radius: 4px;
    }

    .table {
        background-color: #fff;
    }

    .table thead {
        background-color: #D7D7D7;
    }

    .table thead th {
        border: 1px solid #ccc !important;
    }

    .table td {
        border: 1px solid #ccc;
    }

    .text-center {
        text-align: center;
    }

    .mb-30 {
        margin-bottom: 30px;
    }

    .bold {
        font-weight: bold;
    }
</style>
<div class="container">
    <h2 class="text-center mb-30">CERTIFICACIÓN DE FIRMAS DIGITALES</h2>
    <?php
    //fRnk: despliega firmadores del documento
    include(dirname(__FILE__) . '/../../lib/DatosGenerales.php');
    include(dirname(__FILE__) . "/../../lib/lib_modelo/conexion.php");

    try {
        $cone = new conexion();
        $link = $cone->conectarpdo();
        $sql = "SELECT cat.codigo
                FROM kaf.tmovimiento mov 
                INNER JOIN  param.tcatalogo cat on  cat.id_catalogo = mov.id_cat_movimiento
                WHERE mov.id_movimiento = " . $_GET['m'];
        $consulta = $link->query($sql);
        $consulta->execute();
        $data_cod = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($data_cod[0]['codigo'] == 'alta') {
            $sql = "SELECT  mov.num_tramite, mov.fecha_mov,  cat.descripcion as proceso
                    FROM kaf.tmovimiento mov 
                    INNER JOIN  param.tcatalogo cat on  cat.id_catalogo = mov.id_cat_movimiento
                    WHERE mov.id_movimiento=" . $_GET['m'];
        } else {
            $sql = "SELECT  mov.num_tramite, mov.fecha_mov, fun.desc_funcionario2, cat.descripcion as proceso
                FROM orga.vfuncionario fun inner join segu.tpersona per on  fun.id_persona = per.id_persona
                LEFT JOIN kaf.tmovimiento mov on fun.id_funcionario = mov.id_funcionario
                LEFT JOIN  param.tcatalogo cat on  cat.id_catalogo = mov.id_cat_movimiento
                where mov.id_movimiento = " . $_GET['m'];
        }

        $consulta = $link->query($sql);
        $consulta->execute();
        $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $fecha = strtotime($data[0]['fecha_mov']);
        $html = '<table class="mb-30" cellspacing="0" cellpadding="4">';
        $html .= '<tr><td class="bold">Tipo de Proceso:</td><td>' . $data[0]['proceso'] . '</td></tr>';
        $html .= '<tr><td class="bold">Trámite:</td><td>' . $data[0]['num_tramite'] . '</td></tr>';
        $html .= '<tr><td class="bold">Fecha:</td><td>' . date("d/m/Y", $fecha) . '</td></tr>';
        if ($data_cod[0]['codigo'] == 'asig') {
            $html .= '<tr><td class="bold">A/De:</td><td>' . $data[0]['desc_funcionario2'] . '</td></tr>';
        }
        $html .= '</table>';
        $html .= '<h4 style="font-size: 14px;margin-bottom: 5px">Firmas:</h4>';
        $sql = "SELECT
            te.codigo as estado,
            ewf.fecha_reg as fecha_ini,
            CAST(fir.fecha_firma AS date) as fecha,
            to_char(fir.fecha_firma, 'HH24:MI:SS') as hora,
            te.nombre_estado as nombre,
            usu.cuenta,
            fun.desc_funcionario1  as funcionario,
            fun.descripcion_cargo,
            fir.firmado
            FROM  wf.testado_wf ewf
            INNER JOIN wf.ttipo_estado te on ewf.id_tipo_estado = te.id_tipo_estado
            LEFT JOIN segu.tusuario usu on usu.id_usuario = ewf.id_usuario_reg
            LEFT JOIN orga.vfuncionario_cargo_lugar_todos fun on fun.id_funcionario = ewf.id_funcionario
            LEFT JOIN param.tdepto depto on depto.id_depto = ewf.id_depto
            LEFT JOIN kaf.tmovimiento mov on mov.id_proceso_wf = ewf.id_proceso_wf
            INNER JOIN kaf.tfirma_dig fir on fir.id_movimiento = mov.id_movimiento and fir.id_proceso_wf = mov.id_proceso_wf 
            AND fir.id_estado_wf = ewf.id_estado_wf
            WHERE mov.id_movimiento = " . $_GET['m'] . "
            AND te.codigo <> 'borrador'
            AND fir.firmado='si'
            AND fir.estado_reg='activo' 
            ORDER BY ewf.fecha_reg,ewf.id_estado_wf";
        $consulta = $link->query($sql);
        $consulta->execute();
        $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($data)) {
            $html .= '<table class="table" cellspacing="0" cellpadding="4">';
            $html .= '<thead><tr><th>Estado</th><th>Funcionario</th><th>Cargo</th><th>Fecha</th><th>Hora</th></tr></thead>';
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
        } else {
            $html .= '<div style="font-size: 14px;">El documento no presenta Firmas Digitales.</div>';
        }
        echo $html;
    } catch (Exception $ex) {
        var_dump($ex);
    }
    ?>
</div>