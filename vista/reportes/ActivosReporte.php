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
    Phx.vista.ActivosReporte = Ext.extend(Phx.frmInterfaz, {

        Atributos : [
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'desc_tipo'
                },
                type:'Field',
                form:true
            },

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
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'periodo'
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
            },
            {
                config:{
                    name : 'id_periodo',
                    origen : 'PERIODO',
                    fieldLabel : 'Periodo',
                    allowBlank : true,
                    pageSize:12,
                    width:230,
                    listWidth:'230',
                    disabled:true
                },
                type : 'ComboRec',
                id_grupo : 0,
                form : true
            },
            {
                config: {
                    name: 'clasificacion',
                    fieldLabel: 'Clasificacion',
                    typeAhead: false,
                    forceSelection: true,
                    hiddenName: 'clasificacion',
                    allowBlank: true,
                    emptyText: 'Elija una Opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_kactivos_fijos/control/Clasificacion/listarClasificacion',
                        id: 'clasificacion',
                        root: 'datos',
                        sortInfo: {
                            field: 'codigo',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['codigo','descripcion', 'nombre', 'tipo'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'claf.codigo#claf.descripcion#claf.nombre'}
                    }),
                    valueField: 'codigo',
                    displayField: 'descripcion',
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 200,
                    listWidth:230,
                    minChars: 3,
                    resizable:true,
                    listWidth:'350',
                    width:230,
                    tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>Nivel:</b><strong style= "color : green;"> {nombre}</strong></p><p><b>Nombre:</b><strong style= "color : green;"> {descripcion}</strong></p> <p><b>Codigo:</b><strong style= "color : green;"> {codigo}</strong></p></div></tpl>'
                },
                type: 'ComboBox',
                id_grupo: 0,
                form: true
            },
            {
                config:{
                    name:'revalorizaciones',
                    fieldLabel:'Depreciaciones',
                    typeAhead: true,
                    allowBlank:true,
                    triggerAction: 'all',
                    emptyText:'Elija una Opción...',
                    selectOnFocus:true,
                    mode:'local',
                    store:new Ext.data.ArrayStore({
                        fields: ['ID', 'valor'],
                        data :	[['todo','Activos Fijos Ajustes y Revalorizaciones'],
                                 ['activos','Activos Fijos'],
                                 ['activos_ajustes','Activos Fijos y Ajustes'],
                                 ['ajustes','Ajustes'],
                                 ['revalorizaciones','Revalorizaciones'],
                                 ['activos_ajustados','Activos Ajustados'],
                                 ['activos_revalorizados','Activos Revalorizados'],
                                 ['activos_renovados','Activos Renovados'],
                                 ['renovaciones','Renovaciones'],
                                ]
                    }),
                    valueField:'ID',
                    displayField:'valor',
                    width:230

                },
                type:'ComboBox',
                id_grupo:0,
                form:true
            },
            {
                config:{
                    name:'nombre_desc',
                    fieldLabel:'Nombre/Descrip',
                    typeAhead: true,
                    allowBlank:true,
                    triggerAction: 'all',
                    emptyText:'Elija una Opción...',
                    selectOnFocus:true,
                    mode:'local',
                    store:[
                        'nombre',
                        'descripcion'
                    ],
                    valueField:'ID',
                    displayField:'valor',
                    width:150

                },
                type:'ComboBox',
                id_grupo:0,
                form:true
            },
            {
                config:{
                    name:'formato_reporte',
                    fieldLabel:'Formato del Reporte',
                    typeAhead: true,
                    allowBlank:true,
                    triggerAction: 'all',
                    emptyText:'Formato...',
                    selectOnFocus:true,
                    mode:'local',
                    store:new Ext.data.ArrayStore({
                        fields: ['ID', 'valor'],
                        data :	[['1','PDF'],
                            ['2','Excel']]
                    }),
                    valueField:'ID',
                    displayField:'valor',
                    width:200,

                },
                type:'ComboBox',
                id_grupo:0,
                form:true
            },
            {
                config:{
                    name:'tipo',
                    fieldLabel:'Tipo de Reporte',
                    typeAhead: true,
                    allowBlank:true,
                    triggerAction: 'all',
                    emptyText:'Tipo...',
                    selectOnFocus:true,
                    mode:'local',
                    store:[
                        'consolidado',
                        'totales'
                    ],
                    valueField:'ID',
                    displayField:'valor',
                    width:200,

                },
                type:'ComboBox',
                id_grupo:0,
                form:true
            },
            {
                config:{
                    name:'regionales',
                    fieldLabel:'Regionales',
                    typeAhead: false,
                    allowBlank:false,
                    triggerAction: 'all',
                    emptyText:'Tipo...',
                    selectOnFocus:true,
                    mode:'local',
                    store:[
                        'todo',
                        'nacionales',
                        'internacionales'
                    ],
                    valueField:'ID',
                    displayField:'valor',
                    width:200,

                },
                type:'ComboBox',
                id_grupo:0,
                form:true
            }
            ],
        title : 'Reporte Detalle Depreciación',
        ActSave : '../../sis_kactivos_fijos/control/Reporte/reporteDepreciacionXLS',
        

        topBar : true,
        botones : false,
        labelSubmit : 'Imprimir',
        tooltipSubmit : '<b>Generar Reporte Detalle Depreciación</b>',

        constructor : function(config) {
            Phx.vista.ActivosReporte.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
        },

        iniciarEventos:function(){

            this.Cmp.revalorizaciones.on('select', function (cmb, record, index) {
                this.Cmp.desc_tipo.setValue(this.Cmp.revalorizaciones.getRawValue());
                this.Cmp.id_periodo.setDisabled(false);
                this.Cmp.id_periodo.store.baseParams.id_gestion = record.data.id_gestion;
                this.Cmp.id_periodo.modificado = true;

            }, this);



            this.Cmp.id_gestion.on('select', function (cmb, record, index) {
                this.Cmp.gestion.setValue(this.Cmp.id_gestion.getRawValue());
                this.Cmp.id_periodo.reset();
                this.Cmp.id_periodo.setDisabled(false);
                this.Cmp.id_periodo.store.baseParams.id_gestion = record.data.id_gestion;
                this.Cmp.id_periodo.modificado = true;

            }, this);

            this.Cmp.id_periodo.on('select', function (cmb, record, index) {
                this.Cmp.periodo.setValue(this.Cmp.id_periodo.getRawValue());


            }, this);

        },

        onSubmit:function(o){


                Phx.vista.ActivosReporte.superclass.onSubmit.call(this,o);

        },

        tipo : 'reporte',
        clsSubmit : 'bprint'
    })
</script>