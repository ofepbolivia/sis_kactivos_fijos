<?php
/**
*@package pXP
*@file MovimientoTipoCat.php
*@author  RCM
*@date 15/08/2017
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.MovimientoTipoCat = {    
    bsave: false,
    bnew: false,
    bedit: false,
    bdel: false,
    require: '../../../sis_parametros/vista/catalogo/Catalogo.php',
    requireclase: 'Phx.vista.Catalogo',
    
    constructor: function(config) {
        this.Atributos[2].grid=false;
        this.Atributos[3].grid=false;
        Phx.vista.MovimientoTipoCat.superclass.constructor.call(this,config);
        this.maestro = config;

        this.init();
        this.load({
            params:{
                start: 0,
                limit: this.tam_pag,
                catalogoTipo: 'tmovimiento__id_cat_movimiento'
            }
        });
        this.addButton('btnReporte',{ //fRnk: nuevo reporte "Tipos de movimientos"
            grupo: [0],
            text :'Reporte',
            iconCls : 'bpdf32',
            disabled: false,
            handler : this.onButtonReport,
            tooltip : '<b>Reporte de Motivos</b><br/><span>Reporte de Motivos de Movimiento</span>'
        });
    },

    east: {
        url: '../../../sis_kactivos_fijos/vista/movimiento_motivo/MovimientoMotivo.php',
        title: 'Motivos',
        width: '30%',
        cls: 'MovimientoMotivo'
    },
    onButtonReport:function(){
        //fRnk: nuevo reporte HR1341
        Phx.CP.loadingShow();
        Ext.Ajax.request({
            url:'../../sis_kactivos_fijos/control/MovimientoMotivo/generaReporteMotivos',
            params:{id:1},
            success: this.successExport,
            failure: this.conexionFailure,
            timeout:this.timeout,
            scope:this
        });
    },
    
};
</script>
