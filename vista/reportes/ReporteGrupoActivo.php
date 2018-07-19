<?php

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ReporteGrupoActivo = Ext.extend(Phx.frmInterfaz, {

        Grupos : [{
            layout : 'column',
            items : [{
                xtype : 'fieldset',
                layout : 'form',
                border : true,
                title : 'Datos para el reporte',
                bodyStyle : 'padding:0 10px 0;',
                columnWidth : '200px',
                items : [],
                id_grupo : 0,
                collapsible : true
            }]
        }],



        constructor : function(config) {
            Phx.vista.ReporteGrupoActivo.superclass.constructor.call(this, config);

            this.tbar.items.items.shift();
            //this.tooltipSubmit.destroy();
            //console.log(typeof(t));
            this.init();

            this.addButton('btnImp', {
                text : 'impReporte',
                id_grupo:0,
                iconCls : 'bprint',
                disabled : false,
                //id_grupo : 0,
                menu: [{
                    text: 'Activos por Grupo',
                    //iconCls: 'bpdf',
                    argument: {
                        'news': true,
                        def: 'pdf'
                    },
                    handler:this.imprimirTotales,
                    scope: this
                }, {
                    text: 'Activos en Detalle',
                    //iconCls: 'bpdf',
                    argument: {
                        'news': true,
                        def: 'csv'
                    },
                    handler: this.imprimirDetalle,
                    scope:this
                }],
                tooltip : '<b>Reporte</b><br/>Imprimir Reporte Activo Fijo'
            });


            //console.log('barra',this);

        },

        Atributos : [

            {
                config:{
                    name:'id_clasificacion',
                    fieldLabel:'Activo',
                    allowBlank:false,
                    emptyText:'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_kactivos_fijos/control/ActivoFijo/ListaDetActivo',
                        id: 'id_clasificacion',
                        root: 'datos',
                        sortInfo:{
                            field: 'codigo',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_clasificacion','codigo','nombre'],
                        remoteSort: true,
                        baseParams:{par_filtro:'cla.codigo#nombre'}
                    }),
                    valueField: 'id_clasificacion',
                    displayField: 'nombre',
                    //tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color: black">{codigo}: {nombre}</p></div></tpl>',
                    tpl: new Ext.XTemplate([
                        '<tpl for=".">',
                        '<div class="x-combo-list-item">',
                        '<div class="awesomecombo-item {checked}">',
                        '<p><b>Código: {codigo}</b></p>',
                        '</div><p><b>Nombre: </b> <span style="color: green;">{nombre}</span></p>',
                        '</div></tpl>'
                    ]),
                    hiddenName: 'id_clasificacion',
                    //forceSelection:true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender:true,
                    //mode:'remote',
                    pageSize:15,
                    queryDelay:1000,
                    listWidth:600,
                    resizable:true,
                    anchor:'120%',
                    minChars:2,
                    enableMultiSelect:true,


                },
                type:'AwesomeCombo',
                form:true
            },
            {
                config : {
                    name : 'formato_reporte',
                    fieldLabel : 'Formato Reporte',
                    allowBlank : false,
                    forceSelection:true,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store : new Ext.data.ArrayStore({
                        fields : ['tipo', 'valor'],
                        data : [['pdf', 'PDF'], ['excel', 'EXCEL']]
                    }),
                    anchor : '50%',
                    valueField : 'tipo',
                    displayField : 'valor'
                },
                type : 'ComboBox',
                id_grupo : 0,
                form : true
            },
        ],


        topBar : true,
        imprimirTotales: function(){
            if(this.Cmp.id_clasificacion.getValue()==''||this.Cmp.formato_reporte.getValue()==''){
                alert('Seleccione Dato')
            }else{
                //console.log(this.Cmp.formato_reporte.getValue());
                Phx.CP.loadingShow();

                Ext.Ajax.request({
                    url: '../../sis_kactivos_fijos/control/ActivoFijo/ReporteDetalleActivos',
                    params: {id_clasificacion:this.Cmp.id_clasificacion.getValue(),formato_reporte:this.Cmp.formato_reporte.getValue()},
                    success: this.successExport,
                    argument: this.argumentSave,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });

            }},
        imprimirDetalle: function(){
            if(this.Cmp.id_clasificacion.getValue()==''|| this.Cmp.formato_reporte.getValue()==''){
                alert('Seleccione Dato')
            }else{
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url: '../../sis_kactivos_fijos/control/ActivoFijo/ReporteActivoEnDetalle',
                    params: {id_clasificacion:this.Cmp.id_clasificacion.getValue(),formato_reporte:this.Cmp.formato_reporte.getValue()},
                    success: this.successExport,
                    argument: this.argumentSave,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
            }},


//////////////////////////////




    });
</script>