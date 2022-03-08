<?php
/**
 *@package pXP
 *@file    ActivosReporte.php
 *@author  Espinoza Alvarez Franklin
 *@date    01-12-2014
 *@description Archivo con la interfaz para generación de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.BajaRevalorizacion = Ext.extend(Phx.frmInterfaz, {

        Atributos : [

            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'gestion'
                },
                type:'Field',
                form:true
            },



            {
                config:{
                    name : 'id_gestion',
                    origen : 'GESTION',
                    fieldLabel : 'Gestion',
                    allowBlank : false,
                    width:230,
                    listWidth:'230'
                },
                type : 'ComboRec',
                id_grupo : 0,
                form : true
            }

        ],
        title : 'Reporte Activos Fijos Revalorizados con valor 1',
        ActSave : '../../sis_kactivos_fijos/control/Reporte/reporteBajaRevalorizacion',


        topBar : true,
        botones : false,
        labelSubmit : 'Imprimir',
        tooltipSubmit : '<b>Generar Reporte</b>',

        constructor : function(config) {
            Phx.vista.BajaRevalorizacion.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
        },

        iniciarEventos:function(){
            this.Cmp.id_gestion.on('select', function (cmb, record, index) {
                this.Cmp.gestion.setValue(this.Cmp.id_gestion.getRawValue());

            }, this);

        },

        onSubmit:function(o){
            Phx.vista.BajaRevalorizacion.superclass.onSubmit.call(this,o);

        },
        tipo : 'reporte',
        clsSubmit : 'bprint'
    })
</script>