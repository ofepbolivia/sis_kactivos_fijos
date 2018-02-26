<?php
/**
 *@package pXP
 *@file    ReporteGlobalAF.php
 *@author  Franklin Espinoza Alvarez
 *@date    23-01-2018
 *@description Archivo con la interfaz para generación de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ReporteGlobalAF = Ext.extend(Phx.frmInterfaz, {

        Atributos : [

            {
                config : {
                    name : 'configuracion_reporte',
                    fieldLabel : 'Configuración Reporte',
                    allowBlank : false,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store : new Ext.data.ArrayStore({
                        fields : ['tipo', 'valor'],
                        data : [['compras_gestion', 'Compras de Gestion'], ['detalle_af', 'Detalle Activos Fijos']]
                    }),
                    anchor : '100%',
                    valueField : 'tipo',
                    displayField : 'valor'
                },
                type : 'ComboBox',
                id_grupo : 0,
                form : true
            },

            {
                config:{
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: true,
                    anchor: '44.5%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'fecha_ini',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'fecha_fin',
                    fieldLabel: 'Fecha Fin',
                    allowBlank: true,
                    anchor: '44.5%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'fecha_fin',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config : {
                    name : 'tipo_reporte',
                    fieldLabel : 'Tipo Reporte',
                    allowBlank : false,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store : new Ext.data.ArrayStore({
                        fields : ['id', 'valor'],
                        data : [['1', 'Consolidado'], ['2', 'Totales']]
                    }),
                    anchor : '50%',
                    valueField : 'id',
                    displayField : 'valor'
                },
                type : 'ComboBox',
                id_grupo : 1,
                grid:true,
                form : true
            },

            /*{
                config:{
                    name : 'id_clasificacion',
                    fieldLabel: 'Clasificación',
                    anchor: '100%',
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_kactivos_fijos/control/Clasificacion/ListarClasificacionTree',
                        id: 'id_clasificacion',
                        root: 'datos',
                        sortInfo: {
                            field: 'orden',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_clasificacion', 'clasificacion', 'id_clasificacion_fk', 'nivel'],
                        remoteSort: true,
                        baseParams: {
                            par_filtro: 'claf.clasificacion'
                        }
                    }),
                    valueField: 'id_clasificacion',
                    displayField: 'clasificacion',
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    minChars: 2,
                    tpl:['<tpl for=".">',
                            '<tpl if="{nivel} == 1">',
                                '<div class="x-combo-list-item"><p>{clasificacion}</p></div>',
                            '</tpl>',
                        '</tpl>'],
                },
                type : 'ComboBox',
                id_grupo : 1,
                grid:true,
                form : true
            },*/

            {
                config : {
                    name : 'estado',
                    fieldLabel : 'Estado',
                    allowBlank : true,
                    emptyText : 'Estado...',
                    store : new Ext.data.JsonStore({
                        url : '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
                        id : 'id_catalogo',
                        root : 'datos',
                        sortInfo : {
                            field : 'codigo',
                            direction : 'ASC'
                        },
                        totalProperty : 'total',
                        fields : ['id_catalogo', 'codigo', 'descripcion'],
                        remoteSort : true,
                        baseParams : {
                            par_filtro : 'descripcion',
                            cod_subsistema:'KAF',
                            catalogo_tipo:'tactivo_fijo__estado'
                        }
                    }),
                    valueField : 'codigo',
                    displayField : 'descripcion',
                    gdisplayField : 'descripcion',
                    hiddenName : 'id_catalogo',
                    forceSelection : true,
                    typeAhead : false,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'remote',
                    pageSize : 10,
                    queryDelay : 1000,
                    anchor : '50%',
                    gwidth : 150,
                    minChars : 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['descripcion']);
                    }
                },
                type : 'ComboBox',
                id_grupo : 1,
                grid : true,
                form : true
            },

            {
                config : {
                    name : 'formato_reporte',
                    fieldLabel : 'Formato Reporte',
                    allowBlank : false,
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
                id_grupo : 1,
                form : true
            },


            {
                config : {
                    name : 'tipo_activo',
                    fieldLabel : 'Tipo Activo',
                    allowBlank : false,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store : new Ext.data.ArrayStore({
                        fields : ['id', 'valor'],
                        data : [['1', 'Activos Fijos'], ['2', 'Activos Intangibles'], ['3', 'Todos']]
                    }),
                    anchor : '50%',
                    valueField : 'id',
                    displayField : 'valor'
                },
                type : 'ComboBox',
                id_grupo : 1,
                form : true
            },
            {
                config : {
                    name : 'desc_nombre',
                    fieldLabel : 'Descripcion / Nombre',
                    allowBlank : false,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store : new Ext.data.ArrayStore({
                        fields : ['tipo', 'valor'],
                        data : [['desc', 'Descripción'], ['nombre', 'Nombre']]
                    }),
                    anchor : '50%',
                    valueField : 'tipo',
                    displayField : 'valor'
                },
                type : 'ComboBox',
                id_grupo : 1,
                form : true
            },
            {
                config : {
                    name : 'ubicacion',
                    fieldLabel : 'Ubicación',
                    allowBlank : true,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store : new Ext.data.ArrayStore({
                        fields : ['id', 'valor'],
                        data : [['1', 'Nacional'], ['2', 'Internacional'],['3', 'Ambos']]
                    }),
                    anchor : '50%',
                    valueField : 'id',
                    displayField : 'valor'
                },
                type : 'ComboBox',
                id_grupo : 1,
                grid:true,
                form : true
            },/*,
            {
                config : {
                    name : 'financiador',
                    fieldLabel : 'Financiador',
                    allowBlank : false,
                    emptyText : 'Financiador...',
                    disabled: true,
                    store : new Ext.data.JsonStore({
                        url : '../../sis_parametros/control/Financiador/listarFinanciador',
                        id : 'id_financiador',
                        root : 'datos',
                        sortInfo : {
                            field : 'codigo_financiador',
                            direction : 'ASC'
                        },
                        totalProperty : 'total',
                        fields : ['id_financiador', 'codigo_financiador', 'nombre_financiador', 'descripcion_financiador'],
                        remoteSort : true,
                        baseParams : {
                            par_filtro : 'codigo_financiador#descripcion_financiador',
                            cod_subsistema:'KAF'
                        }
                    }),
                    valueField : 'id_financiador',
                    displayField : 'nombre_financiador',
                    gdisplayField : 'nombre_financiador',
                    hiddenName : 'id_financiador',
                    forceSelection : true,
                    typeAhead : false,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'remote',
                    pageSize : 10,
                    queryDelay : 1000,
                    anchor : '50%',
                    gwidth : 150,
                    minChars : 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['descripcion']);
                    }
                },
                type : 'ComboBox',
                id_grupo : 2,
                grid : true,
                form : true
            },
            {
                config : {
                    name : 'programa',
                    fieldLabel : 'Programa',
                    allowBlank : false,
                    emptyText : 'Programa...',
                    disabled: true,
                    store : new Ext.data.JsonStore({
                        url : '../../sis_parametros/control/Programa/listarPrograma',
                        id : 'id_programa',
                        root : 'datos',
                        sortInfo : {
                            field : 'codigo_programa',
                            direction : 'ASC'
                        },
                        totalProperty : 'total',
                        fields : ['id_programa', 'codigo_programa', 'nombre_programa', 'descripcion_programa'],
                        remoteSort : true,
                        baseParams : {
                            par_filtro : 'descripcion',
                            cod_subsistema:'KAF'
                        }
                    }),
                    valueField : 'id_programa',
                    displayField : 'nombre_programa',
                    gdisplayField : 'nombre_programa',
                    hiddenName : 'id_programa',
                    forceSelection : true,
                    typeAhead : false,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'remote',
                    pageSize : 10,
                    queryDelay : 1000,
                    anchor : '50%',
                    gwidth : 150,
                    minChars : 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['descripcion']);
                    }
                },
                type : 'ComboBox',
                id_grupo : 2,
                grid : true,
                form : true
            },
            {
                config : {
                    name : 'proyecto',
                    fieldLabel : 'Proyecto',
                    allowBlank : false,
                    emptyText : 'Proyecto...',
                    disabled: true,
                    store : new Ext.data.JsonStore({
                        url : '../../sis_parametros/control/Proyecto/listarProyecto',
                        id : 'id_proyecto',
                        root : 'datos',
                        sortInfo : {
                            field : 'codigo_proyecto',
                            direction : 'ASC'
                        },
                        totalProperty : 'total',
                        fields : ['id_proyecto', 'codigo_proyecto', 'nombre_proyecto', 'descripcion_proyecto'],
                        remoteSort : true,
                        baseParams : {
                            par_filtro : 'codigo_proyecto#nombre_proyecto',
                            cod_subsistema:'KAF'
                        }
                    }),
                    valueField : 'id_proyecto',
                    displayField : 'nombre_proyecto',
                    gdisplayField : 'nombre_proyecto',
                    hiddenName : 'id_proyecto',
                    forceSelection : true,
                    typeAhead : false,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'remote',
                    pageSize : 10,
                    queryDelay : 1000,
                    anchor : '50%',
                    gwidth : 150,
                    minChars : 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['descripcion']);
                    }
                },
                type : 'ComboBox',
                id_grupo : 2,
                grid : true,
                form : true
            },
            {
                config : {
                    name : 'actividad',
                    fieldLabel : 'Actividad',
                    allowBlank : false,
                    emptyText : 'Actividad...',
                    disabled: true,
                    store : new Ext.data.JsonStore({
                        url : '../../sis_parametros/control/Actividad/listarActividad',
                        id : 'id_actividad',
                        root : 'datos',
                        sortInfo : {
                            field : 'codigo_actividad',
                            direction : 'ASC'
                        },
                        totalProperty : 'total',
                        fields : ['id_actividad', 'codigo_actividad', 'nombre_actividad', 'descripcion_actividad'],
                        remoteSort : true,
                        baseParams : {
                            par_filtro : 'descripcion',
                            cod_subsistema:'KAF',
                            catalogo_tipo:'tactivo_fijo__estado'
                        }
                    }),
                    valueField : 'codigo_actividad',
                    displayField : 'descripcion_actividad',
                    gdisplayField : 'descripcion_actividad',
                    hiddenName : 'id_actividad',
                    forceSelection : true,
                    typeAhead : false,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'remote',
                    pageSize : 10,
                    queryDelay : 1000,
                    anchor : '50%',
                    gwidth : 150,
                    minChars : 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['descripcion']);
                    }
                },
                type : 'ComboBox',
                id_grupo : 2,
                grid : true,
                form : true
            },*/

        ],
        title : 'Reporte Global Activos Fijos',
        ActSave : '../../sis_kactivos_fijos/control/ActivoFijo/reportesAFGlobal',
        timeout : 1500000,

        topBar : true,
        botones : false,
        labelSubmit : 'Imprimir',
        tooltipSubmit : '<b>Estimado usuario</b><br>Eliga los campos necesario e imprima su reporte.',

        constructor : function(config) {
            Phx.vista.ReporteGlobalAF.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
        },

        iniciarEventos:function(){
            /*var that = this;
            this.Cmp.configuracion_reporte.on('select',function (cmb, rec, ind) {
                console.log('tipo',that.Cmp.desc_nombre);
            });*/
            /*this.cmpFormatoReporte = this.getComponente('formato_reporte');
            this.cmpFechaIni = this.getComponente('fecha_ini');
            this.cmpFechaFin = this.getComponente('fecha_fin');
            this.cmpIdCuentaBancaria = this.getComponente('id_cuenta_bancaria');
            this.cmpEstado = this.getComponente('estado');
            this.cmpTipo = this.getComponente('tipo');
            this.cmpNombreBanco = this.getComponente('nombre_banco');
            this.cmpNroCuenta = this.getComponente('nro_cuenta');

            this.getComponente('finalidad').hide(true);
            this.cmpNroCuenta.hide(true);
            this.getComponente('id_finalidad').on('change',function(c,r,n){
                this.getComponente('finalidad').setValue(c.lastSelectionText);
            },this);

            this.cmpIdCuentaBancaria.on('select',function(c,r,n){
                this.cmpNombreBanco.setValue(r.data.nombre_institucion);
                this.cmpNroCuenta.setValue(c.lastSelectionText);
                this.getComponente('id_finalidad').reset();
                this.getComponente('id_finalidad').store.baseParams={id_cuenta_bancaria:c.value, vista: 'reporte'};
                this.getComponente('id_finalidad').modificado=true;
            },this);*/
        },

        onSubmit:function(o){
            /*if(this.cmpFormatoReporte.getValue()==2){
                var data = 'FechaIni=' + this.cmpFechaIni.getValue().format('d-m-Y');
                data = data + '&FechaFin=' + this.cmpFechaFin.getValue().format('d-m-Y');
                data = data + '&IdCuentaBancaria=' + this.cmpIdCuentaBancaria.getValue();
                data = data + '&Estado=' + this.cmpEstado.getValue();
                data = data + '&Tipo=' + this.cmpTipo.getValue();
                data = data + '&NombreBanco=' + this.cmpNombreBanco.getValue();
                data = data + '&NumeroCuenta=' + this.cmpNroCuenta.getValue();

                console.log(data);
                window.open('http://sms.obairlines.bo/LibroBancos/Home/VerLibroBancos?'+data);
                //window.open('http://localhost:2309/Home/VerLibroBancos?'+data);				
            }else{
                Phx.vista.ReporteLibroBancos.superclass.onSubmit.call(this,o);
            }*/
            Phx.vista.ReporteGlobalAF.superclass.onSubmit.call(this,o);
        },

        tipo : 'reporte',
        clsSubmit : 'bprint',

        Grupos : [{
            layout : 'column',
            labelAlign: 'top',
            border : false,
            autoScroll: true,
            //frame:true,
            //bodyStyle: 'padding-right:5px;',
            items : [
                {
                    columnWidth: .25,
                    border: false,
                    //split: true,
                    layout: 'anchor',
                    autoScroll: true,
                    autoHeight: true,
                    collapseFirst : false,
                    collapsible: false,
                    anchor: '100%',
                    //bodyStyle: 'padding-right:20px;',
                    items:[
                        {
                            anchor: '100%',
                            bodyStyle: 'padding-right:5px;',
                            autoHeight: true,
                            border: false,
                            items:[
                                {
                                    xtype: 'fieldset',
                                    layout: 'form',
                                    border: true,
                                    title: 'Eliga un tipo de Reporte',
                                    //bodyStyle: 'padding: 5px 10px 10px 10px;',

                                    items: [],
                                    id_grupo: 0
                                }
                            ]
                        }
                    ]
                },
                {
                    columnWidth: .40,
                    border: false,
                    //split: true,
                    layout: 'anchor',
                    autoScroll: true,
                    autoHeight: true,
                    collapseFirst : false,
                    collapsible: false,
                    anchor: '100%',

                    items:[
                        {
                            anchor: '100%',
                            bodyStyle: 'padding-right:5px;padding-left:10px;',
                            autoHeight: true,
                            border: false,
                            items:[
                                {
                                    xtype: 'fieldset',
                                    layout: 'form',
                                    border: true,
                                    title: 'Reporte Compras x Gestión',
                                    bodyStyle: 'padding: 5px 10px 10px 10px;',

                                    items: [],
                                    id_grupo: 1
                                }
                            ]
                        }
                    ]
                }/*,

                {
                    columnWidth: .39,
                    border: false,
                    //split: true,
                    layout: 'anchor',
                    autoScroll: true,
                    autoHeight: true,
                    collapseFirst : false,
                    collapsible: false,
                    anchor: '100%',

                    items:[
                        {
                            anchor: '100%',
                            bodyStyle: 'padding-right:10px;padding-left:5px;',
                            autoHeight: true,
                            border: false,
                            items:[
                                {
                                    xtype: 'fieldset',
                                    layout: 'form',
                                    border: true,
                                    title: 'Datos Estructura Programatica',
                                    bodyStyle: 'padding: 5px 10px 10px 10px;',

                                    items: [],
                                    id_grupo: 2
                                }
                            ]
                        }
                    ]
                }*/
            ]
        }]
    })
</script>