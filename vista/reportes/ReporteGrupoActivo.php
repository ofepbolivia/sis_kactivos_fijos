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
			this.addButton('btnImp1',{
				text:'Activo Grupo',
				iconCls : 'bprint',
				disabled : false,
				handler: this.imprimirTotales,
				title :'Reporte Activos por Grupo'
			});
			this.addButton('btnImp2',{
			text:'Activo Detalle',
			iconCls : 'bprint',
			disabled : false,
			handler: this.imprimirDetalle,
			title: 'Reporte Activos en Detalle'
			});
        },

        Atributos : [

            {
                config:{
                    name:'id_clasificacion',
                    fieldLabel:'Activo',
                    allowBlank:false,
                    emptyText:'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_kactivos_fijos/control/Clasificacion/listarClasificacionActivo',
                        id: 'id_clasificacion',
                        root: 'datos',
                        sortInfo: {
                            field: 'orden',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_clasificacion','clasificacion', 'id_clasificacion_fk'],
                        remoteSort: true,
                        baseParams: {
                            par_filtro:'claf.clasificacion'
                        }
                    }),
                    valueField: 'id_clasificacion',
                    displayField: 'clasificacion',
                    gdisplayField: 'clasificacion',
                    hiddenName: 'id_clasificacion',
                    mode: 'remote',
                    triggerAction: 'all',
                    typeAhead: false,
                    lazyRender: true,
                    pageSize: 15,
                    queryDelay: 1000,
                    minChars: 2,
                    ancho:'100%',
                    width:400,                    
                    lastWidth:350,                               
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
                alert('Debe seleccionar alguno de los dos criterios.')
            }else{                
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
                alert('Debe seleccionar alguno de los dos criterios.')
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