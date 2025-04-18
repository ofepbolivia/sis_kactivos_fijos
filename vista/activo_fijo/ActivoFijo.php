<?php
/**
 *@package pXP
 *@file gen-ActivoFijo.php
 *@author  (admin)
 *@date 29-10-2015 03:18:45
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ActivoFijo = Ext.extend(Phx.gridInterfaz, {
        dblclickEdit: true,
        nombreVista: 'ActivoFijo',
        mainRegionPanel: {
            region:'west',
            collapsed: true,
            width: 250,
            title: 'Filtros',
            /*tools: [
                {id:'toggle'},{id:'close'},{id:'minimize'},{id:'maximize'},{id:'restore'},{id:'gear'},{id:'pin'},
                {id:'unpin'},{id:'right'},{id:'left'},{id:'up'},{id:'down'},{id:'refresh'},{id:'minus'},{id:'plus'},
                {id:'help'},{id:'search'},{id:'save'},{id:'print'}
            ],*/
            items: [
                new Ext.Panel({
                    id: 'af_filter_accordion',
                    region:'west',
                    margins:'5 0 5 5',
                    split:true,
                    width: 210,
                    layout:'accordion',
                    items: [
                        new Ext.Panel({
                            title: 'Clasificación',
                            cls:'empty',
                            autoScroll: true,
                            tools:[{
                                id:'refresh',
                                qtip: 'Actualizar',
                                handler: function(event, toolEl, panel){
                                    Ext.getCmp('tree_clasificacion_af').root.reload();
                                }
                            }],
                            items: [
                                new Ext.tree.TreePanel({
                                    id: 'tree_clasificacion_af',
                                    region: 'center',
                                    scale: 'large',
                                    singleClickExpand: true,
                                    rootVisible: false,
                                    root: new Ext.tree.AsyncTreeNode({
                                        text: 'Clasificación Activos Fijos',
                                        expandable: true
                                    }),
                                    animate: true,
                                    singleExpand: true,
                                    useArrows: true,
                                    autoScroll: true,
                                    loader: new Ext.tree.TreeLoader({
                                        url: '../../sis_kactivos_fijos/control/Clasificacion/listarClasificacionArb',
                                        clearOnLoad: true,
                                        baseParams: {
                                            start: 0,
                                            limit: 50,
                                            sort: 'claf.nombre',
                                            dir: 'ASC',
                                            id_clasificacion_fk: ''
                                        }
                                    }),
                                    containerScroll: true,
                                    border: false
                                })
                            ]
                        }),
                        new Ext.Panel({
                            id: 'af_filter_depto',
                            title: 'Departamentos',
                            autoScroll: true,
                            tools:[{
                                id:'refresh',
                                qtip: 'Actualizar',
                                handler: function(event, toolEl, panel){
                                    Ext.getCmp('af_filter_depto_cbo').store.reload();
                                }
                            }],
                            items: [
                                new Ext.list.ListView({
                                    id: 'af_filter_depto_cbo',
                                    scope: this,
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_parametros/control/Depto/listarDeptoFiltradoDeptoUsuario',
                                        id: 'id_depto',
                                        root: 'datos',
                                        fields: ['id_depto','codigo','nombre'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'codigo',
                                            direction: 'ASC'
                                        },
                                        baseParams:{
                                            start: 0,
                                            limit: 10,
                                            sort: 'codigo',
                                            dir: 'ASC',
                                            codigo_subsistema: 'KAF',
                                            par_filtro:'DEPPTO.codigo#DEPPTO.nombre'
                                        }
                                    }),
                                    singleSelect: true,
                                    emptyText: 'No existen departamentos habilitados',
                                    reserveScrollOffset: true,

                                    columns: [{
                                        //header: 'id_depto',
                                        width: 0.01,
                                        dataIndex: 'id_depto',
                                        hidden: true
                                    },{
                                        header: 'Código',
                                        width: .3,
                                        dataIndex: 'codigo'
                                    },{
                                        header: 'Nombre',
                                        width: .6,
                                        dataIndex: 'nombre'
                                    }]
                                })
                            ]
                        }),
                        new Ext.Panel({
                            id: 'af_filter_oficina',
                            title: 'Oficinas',
                            autoScroll: true,
                            cls:'empty',
                            tools:[{
                                id:'refresh',
                                qtip: 'Actualizar',
                                handler: function(event, toolEl, panel){
                                    Ext.getCmp('af_filter_oficina_cbo').store.reload();
                                }
                            }],
                            items: [
                                new Ext.list.ListView({
                                    id: 'af_filter_oficina_cbo',
                                    scope: this,
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_organigrama/control/Oficina/listarOficina',
                                        id: 'id_oficina',
                                        root: 'datos',
                                        fields: ['id_oficina','codigo','nombre'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'codigo',
                                            direction: 'ASC'
                                        },
                                        baseParams:{
                                            start: 0,
                                            limit: 10,
                                            sort: 'codigo',
                                            dir: 'ASC'
                                        }
                                    }),
                                    singleSelect: true,
                                    emptyText: 'No existen Oficina habilitadas',
                                    reserveScrollOffset: true,
                                    columns: [{
                                        //header: 'id_depto',
                                        width: 0.01,
                                        dataIndex: 'id_oficina',
                                        hidden: true
                                    },{
                                        header: 'Código',
                                        width: .3,
                                        dataIndex: 'codigo'
                                    },{
                                        header: 'Nombre',
                                        width: .6,
                                        dataIndex: 'nombre'
                                    }]
                                })
                            ]
                        }),
                        new Ext.Panel({
                            title: 'Organigrama',
                            cls:'empty',
                            autoScroll: true,
                            tools:[{
                                id:'refresh',
                                qtip: 'Actualizar',
                                handler: function(event, toolEl, panel){
                                    Ext.getCmp('tree_organigrama_af').root.reload();
                                }
                            }],
                            items: [
                                new Ext.tree.TreePanel({
                                    id: 'tree_organigrama_af',
                                    region: 'center',
                                    scale: 'large',
                                    singleClickExpand: true,
                                    rootVisible: false,
                                    root: new Ext.tree.AsyncTreeNode({
                                        text: 'Organigrama',
                                        expandable: true
                                    }),
                                    animate: true,
                                    singleExpand: true,
                                    useArrows: true,
                                    autoScroll: true,
                                    loader: new Ext.tree.TreeLoader({
                                        url: '../../sis_organigrama/control/EstructuraUo/listarEstructuraUo',
                                        clearOnLoad: true,
                                        baseParams: {
                                            start: 0,
                                            limit: 50,
                                            sort: 'uo.nombre',
                                            dir: 'ASC',
                                            id_uo: '',
                                            node: 'idXX',
                                            filtro:'inactivo',
                                            criterio_filtro_arb:'FASS'
                                        }
                                    }),
                                    containerScroll: true,
                                    border: false
                                })
                            ]
                        })]
                })
            ]
        },
        constructor: function(config) {
            this.maestro = config;
            //llama al constructor de la clase padre
            Phx.vista.ActivoFijo.superclass.constructor.call(this, config);
            /* fRnk: se quitó el filtro caracteristicas
            var cmbCaract = new Ext.form.ComboBox({
                name:'caract_val',
                fieldLabel:'Caracteristicas',
                allowBlank:true,
                emptyText:'Caracteristica...',
                store: new Ext.data.JsonStore({
                    url : '../../sis_kactivos_fijos/control/ActivoFijoCaract/listarCaractFiltro',
                    id: 'clave',
                    root: 'datos',
                    sortInfo:{
                        field: 'clave',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['clave'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams:{par_filtro:'clave'}
                }),
                valueField: 'clave',
                displayField: 'clave',
                forceSelection:false,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender:true,
                mode:'remote',
                pageSize:10,
                queryDelay:1000,
                minChars:2,
                width:130,
                listWidth:300
            });

            var txtFilter = new Ext.form.TextField({
                name: 'valor_filtro',
                emptyText: 'Valor...',
                width: 100
            });

            this.tbar.add(cmbCaract);
            this.tbar.add(txtFilter);

            //Agrega eventos a los componentes creados
            cmbCaract.on('select',function (combo, record, index){
                //Verifica que el campo de texto tenga algun valor
                if(cmbCaract.getValue()&&txtFilter.getValue()){
                    this.store.baseParams.caractFilter=cmbCaract.getValue();
                    this.store.baseParams.caractValue=txtFilter.getValue();
                    this.store.load({params:{start:0, limit:this.tam_pag}});
                } else {
                    this.store.baseParams.caractFilter='';
                    this.store.baseParams.caractValue='';
                    this.store.load({params:{start:0, limit:this.tam_pag}});
                }

            },this);
            txtFilter.on('blur',function (val){
                //Verifica que el campo de texto tenga algun valor
                if(cmbCaract.getValue()&&txtFilter.getValue()){
                    this.store.baseParams.caractFilter=cmbCaract.getValue();
                    this.store.baseParams.caractValue=txtFilter.getValue();
                    this.store.load({params:{start:0, limit:this.tam_pag}});
                } else {
                    this.store.baseParams.caractFilter='';
                    this.store.baseParams.caractValue='';
                    this.store.load({params:{start:0, limit:this.tam_pag}});
                }

            },this);*/

            this.init();
            //////////////////
            //Carga los datos
            //////////////////
            this.load({params: {
                    start: 0,
                    limit: this.tam_pag,
                    sort: 'id_activo_fijo',
                    dir: 'desc',
                    id_activo_fijo: this.maestro.lnk_id_activo_fijo
                }});
            //Button for select IDs
            // this.addButton('btnSelect', {
            //     text : 'Seleccionar todos',
            //     //iconCls : 'bpdf32',
            //     disabled : false,
            //     handler : this.obtenerCadenaIDs,
            //     tooltip : '<b>Seleccionar todos</b><br/>Selecciona todos los activos fijos según el filtro aplicado.'
            // });

            //Load data for Departamentos
            Ext.getCmp('af_filter_depto').on('activate',function(){
                Ext.getCmp('af_filter_depto_cbo').store.load();
            },this);
            //Load data for Oficinas
            Ext.getCmp('af_filter_oficina').on('activate',function(){
                Ext.getCmp('af_filter_oficina_cbo').store.load();
            },this);

            Ext.getCmp('af_filter_accordion').on('expand',function(){alert('evento')},this);

            Ext.getCmp('tree_clasificacion_af').loader.on('beforeload', function(treeLoader,node){
                Ext.apply(Ext.getCmp('tree_clasificacion_af').loader.baseParams,{
                    id_clasificacion: node.attributes['id_clasificacion']
                });
            },this);

            Ext.getCmp('tree_organigrama_af').loader.on('beforeload', function(treeLoader,node){
                Ext.apply(Ext.getCmp('tree_organigrama_af').loader.baseParams,{
                    id_uo: node.attributes['id_uo'],
                    node: node.attributes['id_uo']
                });
            },this);

            //Apply filter in main grid from Clasification
            Ext.getCmp('tree_clasificacion_af').on('click',function(node, e){
                this.filtrarGrid({
                    id_filter_panel: node.id,
                    col_filter_panel: 'id_clasificacion'
                });
            },this);

            //Apply filter in main grid from UO
            Ext.getCmp('tree_organigrama_af').on('click',function(node, e){
                this.filtrarGrid({
                    id_filter_panel: node.id,
                    col_filter_panel: 'id_uo'
                });
            },this);

            //Apply filter in main grid from Departamentos
            Ext.getCmp('af_filter_depto_cbo').addListener('selectionChange', function(cmp,cls){
                var data=cmp.store.data.items[cmp.last].data;
                this.filtrarGrid({
                    id_filter_panel: data.id_depto,
                    col_filter_panel: 'id_depto'
                });
            }, this);

            //Apply filter in main grid from Departamentos
            Ext.getCmp('af_filter_oficina_cbo').addListener('selectionChange', function(cmp,cls){
                if(cmp.store.data.items[cmp.last]&&cmp.store.data.items[cmp.last].data){
                    var data=cmp.store.data.items[cmp.last].data;
                    this.filtrarGrid({
                        id_filter_panel: data.id_oficina,
                        col_filter_panel: 'id_oficina'
                    });
                }
            }, this);


            this.detailsTemplate = new Ext.XTemplate(
                '<div class="details">',
                '<tpl for=".">',
                '<img src="{foto}" height="100" width="150"><div class="details-info">',
                '<b>Código: </b>',
                '<span>{codigo}</span>',
                '<br><b>Estado: </b>',
                '<span>{estado}</span>',
                '<br><b>Denominación: </b>',
                '<span>{denominacion}</span>',
                '<br><b>Fecha Compra: </b>',
                '<span>{fecha_compra}</span>',
                '<br><b>Proveedor: </b>',
                '<span>{proveedor}</span>',
                '<br><b>Responsable: </b>',
                '<span>{funcionario}</span>',
                '<br><b>Oficina: </b>',
                '<span>{oficina}</span>',
                '<br><b>Monto compra: </b>',
                '<span>{monto_compra}</span>',
                '<br><b>Fecha Última Deprec.: </b>',
                '<span>{fecha_ult_dep_real_af}</span>',
                '<br><b>Monto Vigente: </b>',
                '<span>{monto_vigente_real_af}</span>',
                '<br><b>Depreciación Acum.: </b>',
                '<span>{depreciacion_acum_real_af}</span>',
                '<br><b>Depreciación Periodo: </b>',
                '<span>{depreciacion_per_real_af}</span>',
                '<br><b>Vida útil: </b>',
                '<span>{vida_util_real_af}</span>',
                '<br><b>¿En depósito?: </b>',
                '<span>{en_deposito}</span>',
                '<br><b>Responsable Depósito: </b>',
                '<span>{resp_deposito}</span>',
                '<br><b>Depósito: </b>',
                '<span>{deposito}</span>',
                '</div>',
                '</tpl>',
                '</div>'
            );

            this.detailsTemplate.compile();

            this.addButton('btnHistoricoAF',
                {
                    //grupo: [0],
                    text: 'Registro A.F.(Rnv.)',
                    iconCls: 'bedit',
                    disabled: true,
                    handler: this.historicoAF,
                    tooltip: '<b>Registro A.F.(Rnv.)</b><br/>Para Renovación de Activos Intangibles.'
                }
            );

            this.addButton('btnChequeoDocumentosWf',
                {
                    grupo:[0,1,2],
                    text: 'Doc. Compra',
                    iconCls: 'bchecklist',
                    disabled: true,
                    handler: this.loadCheckDocumentosSolWf,
                    tooltip: '<b>Documentos de la Solicitud</b><br/>Subir los documetos requeridos en la solicitud seleccionada.'
                }
            );

            //Add button for upload Photo
            this.addButton('btnPhoto', {
                text : 'Subir Foto',
                iconCls : 'bupload',
                disabled : true,
                handler : this.subirFoto,
                tooltip : '<b>Foto</b><br/>Subir foto para el activo fijo'
            });

            //Add button for codification
            this.addButton('btnImpCodigo', {
                text : 'Imp Código',
                iconCls : 'bprintcheck',
                disabled : true,
                handler : this.impCodigo,
                tooltip : '<b>Código</b><br/>Imprimir el código del activo fijo'
            });

            this.addButton('btnHistorialDep', {
                text : 'Detalle Deprec.',
                iconCls : 'bgear',
                disabled : true,
                handler : this.abrirDetalleDep,
                tooltip : '<b>Detalle Depreciación</b><br/>Detalle completo de las depreciaciones mensuales realizadas'
            });


            //Add context menu
            this.grid.on('rowcontextmenu', function(grid, rowIndex, e) {
                e.stopEvent();
                var selModel = this.grid.getSelectionModel();
                if (!selModel.isSelected(rowIndex)) {
                    selModel.selectRow(rowIndex);
                    this.fireEvent('rowclick', this, rowIndex, e);
                }
                this.ctxMenu.showAt(e.getXY())
            }, this);

            //Selection button
            //this.getBoton('triguerreturn').hide();
            if(config.movimiento){
                this.getBoton('triguerreturn').show();
                this.getBoton('triguerreturn').enable();
            }

            //Creación de ventana para clonación
            var cant = new Ext.form.NumberField({
                fieldLabel: 'Cantidad',
                allowBlank: false,
                allowDecimals: false,
                allowNegative: false,
                minValue: 1
            });

            this.formClone = new Ext.form.FormPanel({
                id: this.idContenedor + '_af_form',
                items: [cant],
                padding: this.paddingForm,
                bodyStyle: this.bodyStyleForm,
                border: this.borderForm,
                frame: this.frameForm,
                autoScroll: false,
                autoDestroy: true,
                autoScroll: true,
                region: 'center'
            });

            this.submitClone = function(){
                if(this.formClone.getForm().isValid()){
                    Phx.CP.loadingShow();
                    var post = {
                        id_activo_fijo: this.idActivoFijoClone,
                        cantidad_clon: this.formClone.getForm().items.items[0].value
                    };
                    Ext.Ajax.request({
                        url: '../../sis_kactivos_fijos/control/ActivoFijo/clonarActivoFijo',
                        params: post,
                        isUpload: false,
                        success: function(a,b,c){
                            this.reload();
                            Phx.CP.loadingHide();
                            this.idActivoFijoClone=0;
                            this.afWindowClone.hide();
                        },
                        argument: this.argumentSave,
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });
                }
            }

            this.afWindowClone = new Ext.Window({
                width: 300,
                height: 120,
                modal: true,
                closeAction: 'hide',
                labelAlign: 'top',
                title: 'Clonar Activo Fijo:',
                bodyStyle: 'padding:5px',
                layout: 'border',
                items: [this.formClone],
                buttons: [{
                    text: 'Guardar',
                    handler: this.submitClone,
                    scope: this
                }, {
                    text: 'Declinar',
                    handler: function() {
                        this.afWindowClone.hide();
                    },
                    scope: this
                }]
            });

            this.cloneAF = function(){
                var data = this.sm.getSelected().data;
                this.idActivoFijoClone = data.id_activo_fijo;
                this.ctxMenu.hide();
                this.afWindowClone.setTitle('Clonar Activo Fijo: ' +data.codigo+' '+data.denominacion);
                this.afWindowClone.show();
            };

            this.ctxMenu = new Ext.menu.Menu({
                items: [{
                    handler: this.cloneAF,
                    icon: '../../../lib/imagenes/arrow-down.gif',
                    text: 'Clonar activo fijo',
                    scope: this
                }],
                scope: this
            });

            this.crearMenuMov();

        },
        Atributos: [{
            //configuracion del componente
            config: {
                labelSeparator: '',
                inputType: 'hidden',
                name: 'id_activo_fijo'
            },
            type: 'Field',
            form: true
        },
            {
                config: {
                    name: 'id_proceso_wf',
                    fieldLabel: 'id_proceso_wf',
                    inputType:'hidden'
                },
                type: 'Field',
                form: true
            },
            {
                config: {
                    name: 'codigo',
                    fieldLabel: 'Código',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 120,
                    maxLength: 50
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.codigo',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            }, {
                config: {
                    name: 'estado',
                    fieldLabel: 'Estado',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 15
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.estado',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'en_deposito',
                    fieldLabel: 'En Deposito?',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 75,
                    maxLength: 15
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.en_deposito',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: false
            }, {
                config: {
                    name: 'id_clasificacion',
                    fieldLabel: 'Clasificación',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_kactivos_fijos/control/Clasificacion/listarClasificacion',
                        id: 'id_clasificacion',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_clasificacion', 'nombre', 'codigo', 'clasificacion'],
                        remoteSort: true,
                        baseParams: {
                            par_filtro: 'cla.nombre#cla.codigo'
                        }
                    }),
                    valueField: 'id_clasificacion',
                    displayField: 'clasificacion',
                    gdisplayField: 'clasificacion',
                    hiddenName: 'id_clasificacion',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    anchor: '100%',
                    gwidth: 150,
                    minChars: 2,
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['clasificacion']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 0,
                filters: {
                    pfiltro: 'cla.nombre',
                    type: 'string'
                },
                grid: true,
                form: false,
                bottom_filter:true
            }, {
                config: {
                    name: 'cantidad_revaloriz',
                    fieldLabel: '#Reval.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 50,
                    maxLength: 4
                },
                type: 'NumberField',
                filters: {
                    pfiltro: 'afij.cantidad_revaloriz',
                    type: 'numeric'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'denominacion',
                    fieldLabel: 'Denominación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 250,
                    maxLength: 100
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.denominacion',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            },
            {
                config:{
                    name: 'nro_cbte_asociado',
                    fieldLabel: 'C31',
                    allowBlank: false,
                    anchor: '80%',
                    width: 70,
                    maxLength: 15
                },
                type:'TextField',
                filters:{pfiltro:'afij.nro_cbte_asociado',type:'string'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter:true
            },
            {
                config:{
                    name: 'tramite_compra',
                    fieldLabel: 'Nro. de Tramite de Compra',
                    allowBlank: false,
                    anchor: '100%',
                    gwidth: 180,
                    maxLength:255
                },
                type:'TextField',
                filters:{pfiltro:'afij.tramite_compra',type:'string'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter:true
            },
            {
                config:{
                    name: 'nombre_unidad',
                    fieldLabel: 'Unidad Solicitante',
                    allowBlank: false,
                    anchor: '100%',
                    gwidth: 180,
                    maxLength:255
                },
                type:'TextField',
                filters:{pfiltro:'uoac.nombre_unidad',type:'string'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter:true
            },
            {
                config: {
                    name: 'nro_serie',
                    fieldLabel: '# Serie',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 130,
                    maxLength: 50
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.nro_serie',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            }, {
                config: {
                    name: 'marca',
                    fieldLabel: 'Marca',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength: 50
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.marca',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            }, {
                config: {
                    name: 'cantidad_af',
                    fieldLabel: 'Cantidad',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength: 50
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.cantidad_af',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'id_unidad_medida',
                    fieldLabel: 'Unidad de Medida',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength: 50,
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['descripcion_unmed']);
                    }
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.id_unidad_medida',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            },
            {
                config: {
                    name: 'subtipo',
                    fieldLabel: 'Subtipo',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.ArrayStore({
                        id: 0,
                        fields: [
                            'myId',
                            'displayText'
                        ],
                        data: [[1, 'item1'], [2, 'item2']]
                    }),
                    valueField: 'myId',
                    displayField: 'displayText'
                },
                typeAhead: true,
                triggerAction: 'all',
                lazyRender:true,
                mode: 'local' ,
                type: 'ComboBox',
                id_grupo: 0,
                filters: {
                    pfiltro: 'kaf.nombre',
                    type: 'string'
                },
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'vida_util_real_af',
                    fieldLabel: 'Vida Útil',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 50,
                    maxLength: 4
                },
                type: 'NumberField',
                filters: {
                    pfiltro: 'afvi.vida_util_real_af',
                    type: 'numeric'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'depreciacion_per',
                    fieldLabel: 'Dep.Periodo',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 80,
                    maxLength: -5
                },
                type: 'NumberField',
                filters: {
                    pfiltro: 'afij.depreciacion_per',
                    type: 'numeric'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'depreciacion_acum',
                    fieldLabel: 'Dep.Acum.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: -5
                },
                type: 'NumberField',
                filters: {
                    pfiltro: 'afij.depreciacion_acum',
                    type: 'numeric'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'monto_vigente_real_af',
                    fieldLabel: 'Monto Vigente',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: -5
                },
                type: 'NumberField',
                filters: {
                    pfiltro: 'afvi.monto_vigente_real_af',
                    type: 'numeric'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'fecha_ult_dep',
                    fieldLabel: 'Ultima Dep.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function(value, p, record) {
                        return value ? value.dateFormat('d/m/Y') : ''
                    }
                },
                type: 'DateField',
                filters: {
                    pfiltro: 'afij.fecha_ult_dep',
                    type: 'date'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'id_funcionario',
                    fieldLabel: 'Responsable',
                    gwidth: 250,
                    gdisplayField: 'funcionario',
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['funcionario']);
                    }
                },
                type: 'ComboBox',
                filters: {
                    pfiltro: 'fun.desc_funcionario2',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            }, {
                config: {
                    name: 'id_persona',
                    fieldLabel: 'Custodio',
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['persona']);
                    }
                },
                type: 'Field',
                filters: {
                    pfiltro: 'per.nombre_completo2',
                    type: 'numeric'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'foto',
                    fieldLabel: 'foto',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 100
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.foto',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'id_proveedor',
                    fieldLabel: 'proveedor',
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['desc_proveedor']);
                    }
                },
                type: 'Field',
                id_grupo: 0,
                filters: {
                    pfiltro: 'pro.desc_proveedor',
                    type: 'string'
                },
                grid: true,
                form: true
            }, {
                config: {
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 10
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.estado_reg',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: false
            }, {
                config: {
                    name: 'fecha_compra',
                    fieldLabel: 'fecha_compra',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function(value, p, record) {
                        return value ? value.dateFormat('d/m/Y') : ''
                    }
                },
                type: 'DateField',
                filters: {
                    pfiltro: 'afij.fecha_compra',
                    type: 'date'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'id_cat_estado_fun',
                    fieldLabel: 'Estado fun.',
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['estado_fun']);
                    }
                },
                type: 'TextField',
                id_grupo: 0,
                filters: {
                    pfiltro: 'cat1.descripcion',
                    type: 'string'
                },
                grid: true,
                form: true
            }, {
                config: {
                    name: 'ubicacion',
                    fieldLabel: 'ubicacion',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 1000
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.ubicacion',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'documento',
                    fieldLabel: 'documento',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 100
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.documento',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            }, {
                config: {
                    name: 'observaciones',
                    fieldLabel: 'observaciones',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 5000
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.observaciones',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            }, {
                config: {
                    name: 'monto_rescate',
                    fieldLabel: 'monto_rescate',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 1179650
                },
                type: 'NumberField',
                filters: {
                    pfiltro: 'afij.monto_rescate',
                    type: 'numeric'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'id_deposito',
                    fieldLabel: 'Deposito',
                    gwidth: 220,
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['deposito']);
                    },

                },
                type: 'TextField',
                id_grupo: 1,
                filters: {
                    pfiltro: 'depaf.nombre',
                    type: 'string'
                },
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'resp_deposito',
                    fieldLabel: 'Responsable Deposito'
                },
                type: 'TextField',
                id_grupo: 0,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'monto_compra_orig',
                    fieldLabel: 'Costo AF',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100
                },
                type: 'NumberField',
                filters: {
                    pfiltro: 'afij.monto_compra_orig',
                    type: 'numeric'
                },
                id_grupo: 1,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'depreciacion_mes',
                    fieldLabel: 'depreciacion_mes',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: -5
                },
                type: 'NumberField',
                filters: {
                    pfiltro: 'afij.depreciacion_mes',
                    type: 'numeric'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'descripcion',
                    fieldLabel: 'descripcion',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 5000
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.descripcion',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            }, {
                config: {
                    name: 'id_moneda_orig',
                    fieldLabel: 'Moneda orig.',
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['desc_moneda_orig']);
                    }
                },
                type: 'TextField',
                id_grupo: 0,
                filters: {
                    pfiltro: 'mon.codigo',
                    type: 'string'
                },
                grid: true,
                form: true
            }, {
                config: {
                    name: 'fecha_ini_dep',
                    fieldLabel: 'fecha_ini_dep',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function(value, p, record) {
                        return value ? value.dateFormat('d/m/Y') : ''
                    }
                },
                type: 'DateField',
                filters: {
                    pfiltro: 'afij.fecha_ini_dep',
                    type: 'date'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'id_cat_estado_compra',
                    fieldLabel: 'Estado compra',
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['estado_compra']);
                    }
                },
                type: 'TextField',
                id_grupo: 0,
                filters: {
                    pfiltro: 'cat2.descripcion',
                    type: 'string'
                },
                grid: true,
                form: true
            }, {
                config: {
                    name: 'vida_util_original',
                    fieldLabel: 'vida_util_original',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'NumberField',
                filters: {
                    pfiltro: 'afij.vida_util_original',
                    type: 'numeric'
                },
                id_grupo: 1,
                grid: true,
                form: true
            }, {
                config: {
                    name: 'id_centro_costo',
                    fieldLabel: 'Centro costo',
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['centro_costo']);
                    }
                },
                type: 'TextField',
                id_grupo: 0,
                filters: {
                    pfiltro: 'cc.centro_costo',
                    type: 'string'
                },
                grid: true,
                form: true
            }, {
                config: {
                    name: 'id_oficina',
                    fieldLabel: 'Oficina',
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['oficina']);
                    }
                },
                type: 'TextField',
                id_grupo: 0,
                filters: {
                    pfiltro: 'ofi.nombre',
                    type: 'string'
                },
                grid: true,
                form: true
            }, {
                config: {
                    name: 'id_depto',
                    fieldLabel: 'departamento',
                    renderer: function(value, p, record) {
                        return String.format('{0}', record.data['departamento']);
                    }
                },
                type: 'TextField',
                id_grupo: 0,
                filters: {
                    pfiltro: 'dpto.nombre',
                    type: 'string'
                },
                grid: true,
                form: true
            }, {
                config: {
                    name: 'codigo_ant',
                    fieldLabel: 'Código Anterior',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 120,
                    maxLength: 50
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.codigo_ant',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: true,
                bottom_filter:true
            }, {
                config: {
                    name: 'caracteristicas',
                    fieldLabel: 'Caracteristicas',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 200,
                    maxLength: 50
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.caracteristicas',
                    type: 'string'
                },
                id_grupo: 1,
                grid: false,
                form: true,
                bottom_filter:true
            }, {
                config: {
                    name: 'fecha_inicio',
                    fieldLabel: 'fecha Inicio',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y') : ''
                    }
                },
                type: 'DateField',
                filters: {
                    pfiltro: 'afij.fecha_inicio',
                    type: 'date'
                },
                id_grupo: 1,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'fecha_fin',
                    fieldLabel: 'fecha Fin',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y') : ''
                    }
                },
                type: 'DateField',
                filters: {
                    pfiltro: 'afij.fecha_fin',
                    type: 'date'
                },
                id_grupo: 1,
                grid: true,
                form: true
            },{
                config: {
                    name: 'usr_reg',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'Field',
                filters: {
                    pfiltro: 'usu1.cuenta',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: false
            }, {
                config: {
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function(value, p, record) {
                        return value ? value.dateFormat('d/m/Y H:i:s') : ''
                    }
                },
                type: 'DateField',
                filters: {
                    pfiltro: 'afij.fecha_reg',
                    type: 'date'
                },
                id_grupo: 1,
                grid: true,
                form: false
            }, {
                config: {
                    name: 'usuario_ai',
                    fieldLabel: 'Funcionaro AI',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 300
                },
                type: 'TextField',
                filters: {
                    pfiltro: 'afij.usuario_ai',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: false
            }, {
                config: {
                    name: 'id_usuario_ai',
                    fieldLabel: 'Funcionaro AI',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'Field',
                filters: {
                    pfiltro: 'afij.id_usuario_ai',
                    type: 'numeric'
                },
                id_grupo: 1,
                grid: false,
                form: false
            }, {
                config: {
                    name: 'usr_mod',
                    fieldLabel: 'Modificado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'Field',
                filters: {
                    pfiltro: 'usu2.cuenta',
                    type: 'string'
                },
                id_grupo: 1,
                grid: true,
                form: false
            }, {
                config: {
                    name: 'fecha_mod',
                    fieldLabel: 'Fecha Modif.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function(value, p, record) {
                        return value ? value.dateFormat('d/m/Y H:i:s') : ''
                    }
                },
                type: 'DateField',
                filters: {
                    pfiltro: 'afij.fecha_mod',
                    type: 'date'
                },
                id_grupo: 1,
                grid: true,
                form: false
            }],
        tam_pag: 50,
        title: 'Activos Fijos',
        ActSave: '../../sis_kactivos_fijos/control/ActivoFijo/insertarActivoFijo',
        ActDel: '../../sis_kactivos_fijos/control/ActivoFijo/eliminarActivoFijo',
        ActList: '../../sis_kactivos_fijos/control/ActivoFijo/listarActivoFijo',
        id_store: 'id_activo_fijo',
        fields: [{name: 'id_activo_fijo',type: 'numeric'},
            {name: 'id_persona',type: 'numeric'},
            {name: 'cantidad_revaloriz',type: 'numeric'},
            {name: 'foto',type: 'string'},
            {name: 'id_proveedor',type: 'numeric'},
            {name: 'estado_reg',type: 'string'},
            {name: 'fecha_compra',type: 'date',dateFormat: 'Y-m-d'},
            {name: 'monto_vigente',type: 'numeric'},
            {name: 'id_cat_estado_fun',type: 'numeric'},
            {name: 'ubicacion',type: 'string'},
            {name: 'vida_util',type: 'numeric'},
            {name: 'documento',type: 'string'},
            {name: 'observaciones',type: 'string'},
            {name: 'fecha_ult_dep',type: 'date',dateFormat: 'Y-m-d'},
            {name: 'monto_rescate',type: 'numeric'},
            {name: 'denominacion',type: 'string'},
            {name: 'id_funcionario',type: 'numeric'},
            {name: 'id_deposito',type: 'numeric'},
            {name: 'monto_compra',type: 'numeric'},
            {name: 'id_moneda',type: 'numeric'},
            {name: 'depreciacion_mes',type: 'numeric'},
            {name: 'codigo',type: 'string'},
            {name: 'descripcion',type: 'string'},
            {name: 'id_moneda_orig',type: 'numeric'},
            {name: 'fecha_ini_dep',type: 'date',dateFormat: 'Y-m-d'},
            {name: 'id_cat_estado_compra',type: 'numeric'},
            {name: 'depreciacion_per',type: 'numeric'},
            {name: 'vida_util_original',type: 'numeric'},
            {name: 'depreciacion_acum',type: 'numeric'},
            {name: 'estado',type: 'string'},
            {name: 'id_clasificacion',type: 'numeric'},
            {name: 'id_centro_costo',type: 'numeric'},
            {name: 'id_oficina',type: 'numeric'},
            {name: 'id_depto',type: 'numeric'},
            {name: 'id_usuario_reg',type: 'numeric'},
            {name: 'fecha_reg',type: 'date',dateFormat: 'Y-m-d H:i:s.u'}, {name: 'usuario_ai',type: 'string'},
            {name: 'id_usuario_ai',type: 'numeric'}, {name: 'id_usuario_mod',type: 'numeric'}, {name: 'fecha_mod',type: 'date',dateFormat: 'Y-m-d H:i:s.u'}, {name: 'usr_reg',type: 'string'},
            {name: 'usr_mod',type: 'string'}, {name: 'persona',type: 'string'},
            {name: 'desc_proveedor',type: 'string'},
            {name: 'estado_fun',type: 'string'},
            {name: 'estado_compra',type: 'string'}, {name: 'clasificacion',type: 'string'},
            {name: 'centro_costo',type: 'string'},
            {name: 'oficina',type: 'string'},
            {name: 'depto',type: 'string'},
            {name: 'funcionario',type: 'string'},
            {name: 'deposito',type: 'string'}, {name: 'deposito_cod',type: 'string'},
            {name: 'desc_moneda_orig',type: 'string'},
            {name: 'en_deposito',type: 'string'},{name: 'extension',type: 'string'},
            {name: 'codigo_ant',type: 'string'},{name: 'marca',type: 'string'},
            {name: 'nro_serie',type: 'string'},
            {name: 'caracteristicas',type: 'string'},
            'monto_compra_orig','desc_proyecto','id_proyecto',
            'monto_vigente_real_af','vida_util_real_af','fecha_ult_dep_real_af','depreciacion_acum_real_af','depreciacion_per_real_af','tipo_activo','depreciable','cantidad_af','id_unidad_medida','codigo_unmed',
            {name:'descripcion_unmed',type:'string'},
            {name:'monto_compra_orig_100',type:'numeric'},
            {name:'nro_cbte_asociado',type:'string'},
            {name:'fecha_cbte_asociado',type:'date',dateFormat: 'Y-m-d'},
            {name:'vida_util_original_anios',type:'numeric'},
            {name:'prestamo',type:'string'},
            {name:'fecha_dev_prestamo',type:'date',dateFormat: 'Y-m-d'},
            {name:'fecha_asignacion',type:'date',dateFormat: 'Y-m-d'},
            {name:'tramite_compra', type:'string'},
            {name:'id_proceso_wf', type:'numeric'},
            {name:'subtipo', type:'string'},
            {name:'nombre_unidad',type:'string'},
            {name:'id_uo',type:'string'},
            {name:'departamento', type:'string'},
            {name: 'fecha_inicio', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'fecha_fin', type: 'date', dateFormat: 'Y-m-d'},
            {name:'resp_deposito', type:'string'},

        ],
        arrayDefaultColumHidden: ['fecha_reg', 'usr_reg', 'fecha_mod', 'usr_mod', 'estado_reg', 'id_usuario_ai', 'usuario_ai', 'id_persona', 'foto', 'id_proveedor', 'fecha_compra', 'id_cat_estado_fun', 'ubicacion', 'documento', 'observaciones', 'monto_rescate', 'id_deposito', 'monto_compra', 'id_moneda', 'depreciacion_mes', 'descripcion', 'id_moneda_orig', 'fecha_ini_dep', 'id_cat_estado_compra', 'vida_util_original', 'id_centro_costo', 'id_oficina', 'id_depto', 'fecha_inicio', 'fecha_fin'],
        sortInfo: {
            field: 'id_activo_fijo',
            direction: 'DESC'
        },
        bdel: true,
        //'<img src="../../../sis_kactivos_fijos/upload/{foto}" height="100" width="150">',
        bsave: true,
        rowExpander: new Ext.ux.grid.RowExpander({
            tpl: new Ext.Template('<br>', '<table><tr><td rowspan="5"><img src="{foto}" height="100" width="150"></td></tr><tr><td colspan ="2"><b>Descripción:</b> {descripcion}</td></tr><tr><td><b>Responsable:</b> {funcionario}</td><td><b>Fecha Ini. Dep.:</b> {fecha_ini_dep}</td></tr><tr><td><b>Ubicación:</b> {ubicacion}</td><td><b>Documento:</b> {documento}</td></tr><tr><td><b>Oficina:</b> {oficina}</td><td><b>Estado funcional:</b> {estado_fun}</td></tr></table>')
        }),
        bodyStyleForm: 'padding:5px;',
        borderForm: true,
        frameForm: false,
        paddingForm: '5 5 5 5',

        onButtonDel: function() {
            data = this.sm.getSelected().data;
            if (data.estado == 'eliminado'){
                alert('El activo con codigo: '+data.codigo+ ' tiene estado eliminado');
            }else {
                this.crearVentanaEliminar();
                this.abrirVentanaDel('del');
                this.actualizarSegunClasificacion(data.tipo_activo, data.depreciable);
                Ext.getCmp(this.idContenedor + '_codigo_ant').disable();
            }
        },

        crearVentanaEliminar: function() {
            if(this.afWindow){
                this.form.destroy();
                this.afWindow.destroy();
            }

            this.form = new Ext.form.FormPanel({
                id: this.idContenedor + '_af_form',
                items: [{
                    region: 'center',
                    layout: 'column',
                    border: false,
                    autoScroll: false,
                    items: [{
                        xtype: 'tabpanel',
                        plain: true,
                        activeTab: 0,
                        height: 515,
                        deferredRender: false,
                        defaults: {
                            bodyStyle: 'padding:10px'
                        },
                        items: [{
                            title: 'Motivo',
                            layout: 'form',
                            defaults: {
                                width: 200
                            },
                            autoScroll: false,
                            defaultType: 'textfield',
                            items: [{
                                name: 'id_activo_fijo',
                                hidden: true,
                                id: this.idContenedor + '_id_activo_fijo'
                            },
                                {
                                    fieldLabel: 'Código',
                                    name: 'codigo',
                                    disabled: true,
                                    id: this.idContenedor+'_codigo'
                                },
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Registro Motivo',
                                    name: 'motivo',
                                    id: this.idContenedor + '_motivo'
                                }
                                ]
                        }]
                    }]
                }],
                //fileUpload: me.fileUpload,
                padding: this.paddingForm,
                bodyStyle: this.bodyStyleForm,
                border: this.borderForm,
                frame: this.frameForm,
                autoScroll: false,
                autoDestroy: true,
                autoScroll: false,
                region: 'center'
            });

            this.afWindow = new Ext.Window({
                width: 500,
                height: 300,
                modal: true,
                closeAction: 'hide',
                labelAlign: 'top',
                title: 'Activos Fijos',
                bodyStyle: 'padding:5px',
                layout: 'border',
                items: [{
                    items: [{
                        id: 'img-detail-panel',
                        region: 'north'
                    }, {
                        id: 'img-qr-panel'+this.idContenedor,
                        region: 'center'
                    }]
                },this.form],
                buttons: [{
                    text: 'Guardar',
                    handler: this.onSubmitDel,
                    scope: this
                }, {
                    text: 'Declinar',
                    handler: function() {
                        this.afWindow.hide();
                    },
                    scope: this
                }]
            });
        },
        abrirVentanaDel: function(){
            this.cargaFormulario(this.sm.getSelected().data);
            data = this.sm.getSelected().data;
            //Renderea la imagen, abre la ventana
            this.afWindow.show();
        },

        crearVentana: function() {
            if(this.afWindow){
                this.form.destroy();
                this.afWindow.destroy();
            }
            this.form = new Ext.form.FormPanel({
                id: this.idContenedor + '_af_form',
                items: [{
                    region: 'center',
                    layout: 'column',
                    border: false,
                    autoScroll: true,
                    items: [{
                        xtype: 'tabpanel',
                        plain: true,
                        activeTab: 0,
                        height: 515,
                        deferredRender: false,
                        defaults: {
                            bodyStyle: 'padding:10px'
                        },
                        items: [{
                            title: 'Principal',
                            layout: 'form',
                            defaults: {
                                width: 400
                            },
                            autoScroll: true,
                            defaultType: 'textfield',
                            items: [{
                                name: 'id_activo_fijo',
                                hidden: true,
                                id: this.idContenedor+'_id_activo_fijo'
                            },{
                                name: 'foto',
                                hidden: true,
                                id: this.idContenedor+'_foto'
                            },{
                                fieldLabel: 'Código',
                                name: 'codigo',
                                disabled: true,
                                id: this.idContenedor+'_codigo'
                            }, {
                                fieldLabel: 'Estado',
                                name: 'estado',
                                disabled: true,
                                id: this.idContenedor+'_estado'
                            }, {
                                xtype: 'compositefield',
                                fieldLabel: 'Revalorizado',
                                //msgTarget: 'side',
                                anchor: '-20',
                                disabled: true,
                                /* defaults: {
                                     flex: 1
                                 },*/
                                items: [{
                                    xtype: 'checkbox',
                                    name: 'reval',
                                    width: 10,
                                    disabled: true,
                                    id: this.idContenedor+'_reval'
                                }, {
                                    xtype: 'numberfield',
                                    name: 'cantidad_revaloriz',
                                    width: 30,
                                    disabled: true,
                                    id: this.idContenedor+'_cantidad_revaloriz'
                                }]
                            }, {
                                xtype: 'combo',
                                fieldLabel: '*Depto.',
                                name: 'id_depto',
                                allowBlank: false,
                                id: this.idContenedor+'_id_depto',
                                emptyText: 'Elija un Departamento',
                                store: new Ext.data.JsonStore({
                                    url: '../../sis_parametros/control/Depto/listarDeptoFiltradoDeptoUsuario',
                                    id: 'id_depto',
                                    root: 'datos',
                                    fields: ['id_depto','codigo','nombre'],
                                    totalProperty: 'total',
                                    sortInfo: {
                                        field: 'codigo',
                                        direction: 'ASC'
                                    },
                                    baseParams:{
                                        start: 0,
                                        limit: 10,
                                        sort: 'codigo',
                                        dir: 'ASC',
                                        codigo_subsistema: 'KAF',
                                        par_filtro:'DEPPTO.codigo#DEPPTO.nombre'
                                    }
                                }),
                                valueField: 'id_depto',
                                displayField: 'nombre',
                                gdisplayField: 'depto',
                                mode: 'remote',
                                triggerAction: 'all',
                                lazyRender: true,
                                pageSize: 15
                            }, {
                                xtype: 'combo',
                                fieldLabel: '*Clasificación',
                                name: 'id_clasificacion',
                                allowBlank: false,
                                id: this.idContenedor+'_id_clasificacion',
                                emptyText: 'Elija la Clasificación',
                                store: new Ext.data.JsonStore({
                                    url: '../../sis_kactivos_fijos/control/Clasificacion/ListarClasificacionTree',
                                    id: 'id_clasificacion',
                                    root: 'datos',
                                    sortInfo: {
                                        field: 'orden',
                                        direction: 'ASC'
                                    },
                                    totalProperty: 'total',
                                    fields: ['id_clasificacion','clasificacion', 'id_clasificacion_fk','tipo_activo','depreciable','vida_util'],
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
                                minChars: 2
                            }, {
                                xtype: 'compositefield',
                                fieldLabel: '*Vida útil inicial',
                                items: [{
                                    xtype: 'label',
                                    text: 'Meses'
                                }, {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Vida útil inicial (meses)',
                                    name: 'vida_util_original',
                                    width: 60,
                                    allowBlank: false,
                                    id: this.idContenedor+'_vida_util_original'
                                }, {
                                    xtype: 'label',
                                    text: 'Años'
                                }, {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Vida útil inicial (años)',
                                    name: 'vida_util_original_anios',
                                    width: 60,
                                    allowBlank: false,
                                    id: this.idContenedor+'_vida_util_original_anios'
                                }]
                            }, {
                                fieldLabel: '#Serie',
                                name: 'nro_serie',
                                allowBlank: true,
                                id: this.idContenedor+'_nro_serie'
                            }, {
                                fieldLabel: 'Marca',
                                name: 'marca',
                                allowBlank: true,
                                id: this.idContenedor+'_marca'
                            }, {
                                fieldLabel: '*Denominación',
                                name: 'denominacion',
                                allowBlank: false,
                                id: this.idContenedor+'_denominacion'
                            }, {
                                xtype: 'textarea',
                                fieldLabel: '*Descripción',
                                name: 'descripcion',
                                allowBlank: false,
                                id: this.idContenedor+'_descripcion'
                            }, {
                                xtype: 'numberfield',
                                fieldLabel: '*Cantidad',
                                width: 60,
                                name: 'cantidad_af',
                                allowBlank: false,
                                id: this.idContenedor+'_cantidad_af'
                            }, {
                                xtype: 'combo',
                                fieldLabel: '*Unidad de Medida',
                                name: 'id_unidad_medida',
                                //hiddenName: 'id_cat_estado_fun',
                                allowBlank: false,
                                id: this.idContenedor+'_id_unidad_medida',
                                emptyText: 'Elija una opción',
                                store: new Ext.data.JsonStore({
                                    url: '../../sis_parametros/control/UnidadMedida/listarUnidadMedida',
                                    id: 'id_unidad_medida',
                                    root: 'datos',
                                    fields: ['id_unidad_medida','codigo','descripcion'],
                                    totalProperty: 'total',
                                    sortInfo: {
                                        field: 'codigo',
                                        direction: 'ASC'
                                    },
                                    baseParams:{
                                        start: 0,
                                        limit: 10,
                                        sort: 'descripcion',
                                        dir: 'ASC'
                                    }
                                }),
                                valueField: 'id_unidad_medida',
                                hiddenValue: 'id_unidad_medida',
                                displayField: 'descripcion',
                                gdisplayField: 'descripcion_unmed',
                                mode: 'remote',
                                triggerAction: 'all',
                                lazyRender: true,
                                pageSize: 15,
                                tpl : '<tpl for="."><div class="x-combo-list-item"><p>{codigo} - {descripcion}</p></div></tpl>',
                            }, {
                                xtype: 'combo',
                                fieldLabel: '*Estado funcional Actual',
                                name: 'id_cat_estado_fun',
                                //hiddenName: 'id_cat_estado_fun',
                                allowBlank: false,
                                id: this.idContenedor+'_id_cat_estado_fun',
                                emptyText: 'Elija una opción',
                                store: new Ext.data.JsonStore({
                                    url: '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
                                    id: 'id_catalogo',
                                    root: 'datos',
                                    fields: ['id_catalogo','codigo','descripcion'],
                                    totalProperty: 'total',
                                    sortInfo: {
                                        field: 'codigo',
                                        direction: 'ASC'
                                    },
                                    baseParams:{
                                        start: 0,
                                        limit: 10,
                                        sort: 'descripcion',
                                        dir: 'ASC',
                                        par_filtro:'cat.descripcion',
                                        cod_subsistema:'KAF',
                                        catalogo_tipo:'tactivo_fijo__id_cat_estado_fun'
                                    }
                                }),
                                valueField: 'id_catalogo',
                                hiddenValue: 'id_catalogo',
                                displayField: 'descripcion',
                                gdisplayField: 'estado_fun',
                                mode: 'remote',
                                triggerAction: 'all',
                                lazyRender: true,
                                pageSize: 15,
                                tpl : '<tpl for="."><div class="x-combo-list-item"><p>{codigo} - {descripcion}</p></div></tpl>',
                            }, {
                                xtype: 'textfield',
                                fieldLabel: 'Código Ant.',
                                name: 'codigo_ant',
                                id: this.idContenedor+'_codigo_ant'
                            }/*, {
                                xtype: 'textarea',
                                fieldLabel: 'Caracteristicas',
                                name: 'observaciones',
                                id: this.idContenedor+'_caracteristicas'
                            }*/, {
                                xtype: 'textarea',
                                fieldLabel: 'Observaciones',
                                name: 'observaciones',
                                id: this.idContenedor+'_observaciones'
                            }]
                        }, {
                            title: 'Ubicación Física',
                            layout: 'form',
                            defaults: {
                                width: 400
                            },
                            defaultType: 'textfield',
                            items: [{
                                xtype: 'datefield',
                                fieldLabel: 'Fecha Asignación',
                                name: 'fecha_asignacion',
                                disabled: true,
                                id: this.idContenedor+'_fecha_asignacion'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: 'Préstamo',
                                name: 'prestamo',
                                disabled: true,
                                id: this.idContenedor+'_prestamo'
                            }, {
                                xtype: 'datefield',
                                fieldLabel: 'Fecha Devolución Préstamo',
                                name: 'fecha_dev_prestamo',
                                disabled: true,
                                id: this.idContenedor+'_fecha_dev_prestamo'
                            }, {
                                xtype: 'combo',
                                fieldLabel: '*Depósito',
                                name: 'id_deposito',
                                allowBlank: false,
                                id: this.idContenedor+'_id_deposito',
                                emptyText: 'Elija el depósito',
                                store: new Ext.data.JsonStore({
                                    url: '../../sis_kactivos_fijos/control/Deposito/listarDeposito',
                                    id: 'id_deposito',
                                    root: 'datos',
                                    fields: ['id_deposito','id_funcionario','id_oficina','ubicacion','codigo','nombre','depto','depto_cod','funcionario','oficina_cod','oficina'],
                                    totalProperty: 'total',
                                    sortInfo: {
                                        field: 'codigo',
                                        direction: 'ASC'
                                    },
                                    baseParams:{
                                        start: 0,
                                        limit: 10,
                                        sort: 'codigo',
                                        dir: 'ASC',
                                        par_filtro:'depaf.codigo#depaf.nombre'
                                    }
                                }),
                                valueField: 'id_deposito',
                                displayField: 'nombre',
                                gdisplayField: 'deposito',
                                mode: 'remote',
                                triggerAction: 'all',
                                lazyRender: true,
                                pageSize: 15
                            }, {
                                xtype: 'combo',
                                fieldLabel: 'Oficina',
                                name: 'id_oficina',
                                allowBlank: false,
                                disabled: true,
                                id: this.idContenedor+'_id_oficina',
                                store: new Ext.data.JsonStore({}),
                                valueField: 'id_oficina',
                                displayField: 'nombre',
                                gdisplayField: 'oficina',
                                pageSize: 15
                            }, {
                                xtype: 'combo',
                                fieldLabel: 'Responsable',
                                name: 'id_funcionario',
                                allowBlank: false,
                                disabled: true,
                                id: this.idContenedor+'_id_funcionario',
                                store: new Ext.data.JsonStore({}),
                                valueField: 'id_funcionario',
                                displayField: 'nombre',
                                gdisplayField: 'funcionario'
                            }, {
                                fieldLabel: 'Custodio',
                                name: 'id_persona',
                                disabled: true,
                                id: this.idContenedor+'_id_persona'
                            }, {
                                xtype: 'textarea',
                                fieldLabel: 'Ubicación',
                                name: 'ubicacion',
                                id: this.idContenedor+'_ubicacion',
                                disabled: true
                            }]
                        }, {
                            title: 'Datos Compra',
                            layout: 'form',
                            defaults: {
                                width: 400
                            },
                            defaultType: 'textfield',
                            items: [{
                                xtype: 'combo',
                                fieldLabel: 'Proveedor',
                                name: 'id_proveedor',
                                allowBlank: true,
                                id: this.idContenedor+'_id_proveedor',
                                emptyText: 'Elija el Proveedor',
                                store: new Ext.data.JsonStore({
                                    url: '../../sis_parametros/control/Proveedor/listarProveedorCombos',
                                    id: 'id_proveedor',
                                    root: 'datos',
                                    fields: ['id_proveedor','desc_proveedor'],
                                    totalProperty: 'total',
                                    sortInfo: {
                                        field: 'desc_proveedor',
                                        direction: 'ASC'
                                    },
                                    baseParams:{
                                        start: 0,
                                        limit: 10,
                                        sort: 'desc_proveedor',
                                        dir: 'ASC',
                                        par_filtro:'provee.desc_proveedor'
                                    }
                                }),
                                valueField: 'id_proveedor',
                                displayField: 'desc_proveedor',
                                gdisplayField: 'desc_proveedor',
                                mode: 'remote',
                                triggerAction: 'all',
                                lazyRender: true,
                                pageSize: 15,
                                minChars : 2,
                                queryDelay : 1000,
                                //valueNotFoundText: 'UNIDAD INEXISTENTE',
                                pageSize: 15
                            }, {
                                xtype: 'datefield',
                                fieldLabel: '*Fecha Compra',
                                name: 'fecha_compra',
                                allowBlank: false,
                                id: this.idContenedor+'_fecha_compra'
                            }, {
                                fieldLabel: 'Documento',
                                name: 'documento',
                                id: this.idContenedor+'_documento'
                            },{
                                xtype: 'compositefield',
                                fieldLabel: '*Importe',
                                // msgTarget: 'side',
                                anchor: '-20',
                                /* defaults: {
                                     flex: 1
                                 },*/
                                items: [{
                                    xtype: 'label',
                                    text: 'Costo AF'
                                }, {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Monto compra 87',
                                    name: 'monto_compra_orig',
                                    allowBlank: false,
                                    id: this.idContenedor+'_monto_compra_orig',
                                    width: 127
                                }, {
                                    xtype: 'combo',
                                    fieldLabel: 'Moneda',
                                    name: 'id_moneda_orig',
                                    allowBlank: false,
                                    width: 50,
                                    listWidth: 50,
                                    id: this.idContenedor+'_id_moneda_orig',
                                    emptyText: 'Elija la moneda de compra',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_parametros/control/Moneda/listarMoneda',
                                        id: 'id_moneda',
                                        root: 'datos',
                                        fields: ['id_moneda','codigo','moneda'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'moneda',
                                            direction: 'ASC'
                                        },
                                        baseParams:{
                                            start: 0,
                                            limit: 10,
                                            sort: 'moneda',
                                            dir: 'ASC',
                                            par_filtro:'moneda.moneda'
                                        }
                                    }),
                                    valueField: 'id_moneda',
                                    displayField: 'codigo',
                                    gdisplayField: 'desc_moneda_orig',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    lazyRender: true,
                                    pageSize: 15
                                }, {
                                    xtype: 'label',
                                    text: 'Valor Compra'
                                }, {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Monto compra 100',
                                    name: 'monto_compra_orig_100',
                                    allowBlank: false,
                                    id: this.idContenedor+'_monto_compra_orig_100',
                                    width: 127
                                }]
                            }, {
                                xtype: 'combo',
                                fieldLabel: '*Estado Activo Compra',
                                name: 'id_cat_estado_compra',
                                allowBlank: false,
                                id: this.idContenedor+'_id_cat_estado_compra',
                                emptyText: 'Elija una opción',
                                store: new Ext.data.JsonStore({
                                    url: '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
                                    id: 'id_catalogo',
                                    root: 'datos',
                                    fields: ['id_catalogo','codigo','descripcion'],
                                    totalProperty: 'total',
                                    sortInfo: {
                                        field: 'descripcion',
                                        direction: 'ASC'
                                    },
                                    baseParams:{
                                        start: 0,
                                        limit: 10,
                                        sort: 'descripcion',
                                        dir: 'ASC',
                                        par_filtro:'cat.descripcion',
                                        cod_subsistema:'KAF',
                                        catalogo_tipo:'tactivo_fijo__id_cat_estado_compra'
                                    }
                                }),
                                valueField: 'id_catalogo',
                                displayField: 'descripcion',
                                gdisplayField: 'estado_compra',
                                mode: 'remote',
                                triggerAction: 'all',
                                lazyRender: true,
                                pageSize: 15
                            }, {
                                xtype: 'textfield',
                                fieldLabel: 'Nro.Cbte Asociado',
                                name: 'nro_cbte_asociado',
                                allowBlank: true,
                                id: this.idContenedor+'_nro_cbte_asociado',
                                width: 140
                            }, {
                                xtype: 'datefield',
                                fieldLabel: 'Fecha.Cbte Asociado',
                                name: 'fecha_cbte_asociado',
                                allowBlank: true,
                                id: this.idContenedor+'_fecha_cbte_asociado',
                                width: 140
                            },{
                                xtype: 'textfield',
                                fieldLabel: 'Nro. de Trámite de Compra',
                                name: 'tramite_compra',
                                allowBlank: true,
                                id: this.idContenedor+'_tramite_compra',
                                width: 140
                            },{
                                xtype: 'combo',
                                fieldLabel: 'Subtipo',
                                name: 'subtipo',
                                allowBlank: true,
                                mode: 'local',
                                triggerAction: 'all',
                                emptyText: 'Elija una opción',
                                id: this.idContenedor+'_subtipo',
                                emptyText: 'Elija una opción',
                                store: new Ext.data.ArrayStore({
                                    id: 0,
                                    fields: ['subtipo'],
                                    data: [ ['Ninguno'], ['Leasing']]
                                }),
                                valueField: 'subtipo',
                                displayField: 'subtipo',
                                hidden: true //fRnk: ocultado
                            },
                                {
                                    xtype: 'combo',
                                    fieldLabel: 'Unidad Solicitante',
                                    name: 'id_uo',
                                    allowBlank: true,
                                    id: this.idContenedor+'_id_uo',
                                    emptyText: 'Elija el Proveedor',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_kactivos_fijos/control/ActivoFijo/listarAFUnidSol',
                                        id: 'id_uo',
                                        root: 'datos',
                                        fields: ['id_uo','nombre_unidad'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'nombre_unidad',
                                            direction: 'ASC'
                                        },
                                        baseParams:{
                                            start: 0,
                                            limit: 10,
                                            sort: 'nombre_unidad',
                                            dir: 'ASC',
                                            par_filtro:'uo.nombre_unidad'
                                        }
                                    }),
                                    valueField: 'id_uo',
                                    displayField: 'nombre_unidad',
                                    gdisplayField: 'nombre_unidad',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    lazyRender: true,
                                    pageSize: 15,
                                    minChars : 2,
                                    queryDelay : 1000,
                                    //valueNotFoundText: 'PROVEEDOR INEXISTENTE',
                                    pageSize: 15
                                },
                                {
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha Inicio',
                                    name: 'fecha_inicio',
                                    allowBlank: true,
                                    id: 'fecha_inicio',
                                    width: 140
                                },
                                {
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha Fin',
                                    name: 'fecha_fin',
                                    allowBlank: true,
                                    id: 'fecha_fin',
                                    width: 140
                                }
                            ]
                        }, {
                            title: 'Datos Depreciación',
                            layout: 'form',
                            defaults: {
                                width: 400
                            },
                            defaultType: 'textfield',
                            items: [{
                                xtype: 'datefield',
                                fieldLabel: '*Fecha inicio Dep/Act',
                                qtip:'Fecha de inicio de depreciación o de actualización',
                                name: 'fecha_ini_dep',
                                allowBlank: false,
                                id: this.idContenedor+'_fecha_ini_dep'
                            }, {
                                fieldLabel: 'Monto Vigente',
                                name: 'monto_vigente_real_af',
                                disabled: true,
                                id: this.idContenedor+'_monto_vigente_real_af'
                            }, {
                                fieldLabel: 'Depreciación Acumulada',
                                name: 'depreciacion_acum_real_af',
                                disabled: true,
                                id: this.idContenedor+'_depreciacion_acum_real_af'
                            }, {
                                fieldLabel: 'Depreciación Periodo',
                                name: 'depreciacion_per_real_af',
                                disabled: true,
                                id: this.idContenedor+'_depreciacion_per_real_af'
                            }, {
                                fieldLabel: 'Depreciación Mes',
                                name: 'depreciacion_mes',
                                disabled: true,
                                id: this.idContenedor+'_depreciacion'
                            }, {
                                xtype: 'datefield',
                                fieldLabel: 'Fecha última Depreciación',
                                name: 'fecha_ult_dep_real_af',
                                disabled: true,
                                id: this.idContenedor+'_fecha_ult_dep_real_af'
                            }, {
                                fieldLabel: 'Vida Útil restante',
                                name: 'vida_util_real_af',
                                disabled: true,
                                id: this.idContenedor+'_vida_util_real_af'
                            }, {
                                fieldLabel: 'Monto de rescate',
                                name: 'monto_rescate',
                                allowBlank: true,
                                id: this.idContenedor+'_monto_rescate',
                                emptyText: '1',
                                disabled: true
                            },

                                {
                                    xtype: 'combo',
                                    name:'id_proyecto',
                                    id: this.idContenedor+'_id_proyecto',
                                    qtip: 'Proyecto o aplicación del activo fijo, se utliza para cargar los gastos  de depreciación (Determinar los centro de costos)',
                                    fieldLabel:'*Proyecto / Aplicación',
                                    allowBlank:false,
                                    emptyText:'Proyecto...',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_parametros/control/Proyecto/ListarProyecto',
                                        id: 'id_proyecto',
                                        root: 'datos',
                                        sortInfo:{
                                            field: 'codigo_proyecto',
                                            direction: 'ASC'
                                        },
                                        totalProperty: 'total',
                                        fields: ['id_proyecto','codigo_proyecto','nombre_proyecto'],
                                        // turn on remote sorting
                                        remoteSort: true,
                                        baseParams:{par_filtro:'codigo_proyecto#nombre_proyecto'}
                                    }),
                                    valueField: 'id_proyecto',
                                    displayField: 'codigo_proyecto',
                                    gdisplayField:'desc_proyecto',//mapea al store del grid
                                    tpl:'<tpl for="."><div class="x-combo-list-item"><p>{codigo_proyecto}</p><p>{nombre_proyecto}</p> </div></tpl>',
                                    hiddenName: 'id_proyecto',
                                    forceSelection:true,
                                    typeAhead: true,
                                    triggerAction: 'all',
                                    lazyRender:true,
                                    mode:'remote',
                                    pageSize:10,
                                    queryDelay:1000,
                                    minChars:2
                                }

                            ]
                        }]
                    }]
                }],
                //fileUpload: me.fileUpload,
                padding: this.paddingForm,
                bodyStyle: this.bodyStyleForm,
                border: this.borderForm,
                frame: this.frameForm,
                autoScroll: false,
                autoDestroy: true,
                autoScroll: true,
                region: 'center'
            });

            this.afWindow = new Ext.Window({
                width: 800,
                height: 620,
                modal: true,
                closeAction: 'hide',
                labelAlign: 'top',
                title: 'Activos Fijos',
                bodyStyle: 'padding:5px',
                layout: 'border',
                items: [{
                    region: 'west',
                    split: true,
                    width: 200,
                    minWidth: 150,
                    maxWidth: 250,
                    items: [{
                        id: 'img-detail-panel',
                        region: 'north'
                    }, {
                        id: 'img-qr-panel'+this.idContenedor,
                        region: 'center'
                    }]
                },this.form],
                buttons: [{
                    text: 'Guardar',
                    handler: this.onSubmit,
                    scope: this
                }, {
                    text: 'Declinar',
                    handler: function() {
                        this.afWindow.hide();
                    },
                    scope: this
                }]
            });

            //Events
            //Clasificación
            Ext.getCmp(this.idContenedor+'_id_clasificacion').on('exception',this.conexionFailure,this);

            Ext.getCmp(this.idContenedor+'_id_clasificacion').on('select',function(cmp,rec,index){
                if(rec.data.depreciable == 'si'){
                    Ext.getCmp(this.idContenedor+'_vida_util_original').setValue(rec.data.vida_util);
                    Ext.getCmp(this.idContenedor+'_vida_util_real_af').setValue(rec.data.vida_util);
                    Ext.getCmp(this.idContenedor+'_monto_rescate').setValue(rec.data.monto_residual);
                    //Convierte a años
                    Ext.getCmp(this.idContenedor+'_vida_util_original_anios').setValue(this.convertirVidaUtil(rec.data.vida_util));
                } else {
                    Ext.getCmp(this.idContenedor+'_vida_util_original').allowBlank = true;
                    Ext.getCmp(this.idContenedor+'_vida_util_original_anios').allowBlank = true;
                    Ext.getCmp(this.idContenedor+'_vida_util_original').setValue('')
                    Ext.getCmp(this.idContenedor+'_vida_util_original_anios').setValue('')
                }
                this.actualizarSegunClasificacion(rec.data.tipo_activo, rec.data.depreciable);

            },this);
            //Vida util
            Ext.getCmp(this.idContenedor+'_vida_util_original').on('blur',function(cmp,rec,index){
                Ext.getCmp(this.idContenedor+'_vida_util_real_af').setValue(Ext.getCmp(this.idContenedor+'_vida_util_original').getValue());
                //Convierte a años
                Ext.getCmp(this.idContenedor+'_vida_util_original_anios').setValue(this.convertirVidaUtil(Ext.getCmp(this.idContenedor+'_vida_util_original').getValue()));

            },this);
            //Vida util años
            Ext.getCmp(this.idContenedor+'_vida_util_original_anios').on('blur',function(cmp,rec,index){
                //Convertir a meses
                Ext.getCmp(this.idContenedor+'_vida_util_original').setValue(this.convertirVidaUtil(Ext.getCmp(this.idContenedor+'_vida_util_original_anios').getValue(),'anios'));
                Ext.getCmp(this.idContenedor+'_vida_util_real_af').setValue(Ext.getCmp(this.idContenedor+'_vida_util_original').getValue());
            },this);

            //Denominación
            Ext.getCmp(this.idContenedor+'_denominacion').on('blur',function(cmp){
                if(Ext.getCmp(this.idContenedor+'_descripcion').getValue()==''){
                    Ext.getCmp(this.idContenedor+'_descripcion').setValue(Ext.getCmp(this.idContenedor+'_denominacion').getValue());
                }
            },this);



            //Depto
            Ext.getCmp(this.idContenedor+'_id_depto').on('select',function(cmp,rec,index){
                var obj = {
                    start: 0,
                    limit: 50,
                    sort: 'claf.nombre',
                    dir: 'ASC',
                    id_depto: rec.data.id_depto
                };
                Ext.getCmp(this.idContenedor+'_id_deposito').reset();
                Ext.getCmp(this.idContenedor+'_id_deposito').modificado=true;
                Ext.getCmp(this.idContenedor+'_id_deposito').store.baseParams.id_depto=rec.data.id_depto;


            },this);
            //Deposito
            Ext.getCmp(this.idContenedor+'_id_deposito').on('select',function(cmp,rec,index){
                //Setear oficina
                rec1 = new Ext.data.Record({nombre: rec.data.oficina, 'id_oficina': rec.data.id_oficina },rec.data.id_oficina);
                Ext.getCmp(this.idContenedor+'_id_oficina').store.add(rec1);
                Ext.getCmp(this.idContenedor+'_id_oficina').store.commitChanges();
                Ext.getCmp(this.idContenedor+'_id_oficina').modificado = true;
                Ext.getCmp(this.idContenedor+'_id_oficina').setValue(rec.data.id_oficina);
                //Setear responsable
                rec1 = new Ext.data.Record({nombre: rec.data.funcionario, 'id_funcionario': rec.data.id_funcionario },rec.data.id_funcionario);
                Ext.getCmp(this.idContenedor+'_id_funcionario').store.add(rec1);
                Ext.getCmp(this.idContenedor+'_id_funcionario').store.commitChanges();
                Ext.getCmp(this.idContenedor+'_id_funcionario').modificado = true;
                Ext.getCmp(this.idContenedor+'_id_funcionario').setValue(rec.data.id_funcionario);
                //Setear Ubicación
                Ext.getCmp(this.idContenedor+'_ubicacion').setValue(rec.data.ubicacion);
            },this);
            //Monto Compra
            Ext.getCmp(this.idContenedor+'_monto_compra_orig').on('blur', function(a,b,c){
                Ext.getCmp(this.idContenedor+'_monto_vigente_real_af').setValue(Ext.getCmp(this.idContenedor+'_monto_compra_orig').getValue());
                Ext.getCmp(this.idContenedor+'_depreciacion_acum_real_af').setValue('0.00');
                Ext.getCmp(this.idContenedor+'_depreciacion_per_real_af').setValue('0.00');
                Ext.getCmp(this.idContenedor+'_depreciacion').setValue('0.00');
            },this);

            //}
        },
        abrirVentana: function(tipo){
            var data;
            if(tipo=='edit'){
                //Carga datos
                var ob = this.sm.getSelected().data;
                    ob.edit_af = true;
                this.cargaFormulario(ob);
                data = this.sm.getSelected().data;

            } else {
                //Inicializa el formulario
                this.form.getForm().reset();
                this.cargarValoresDefecto();
                data = {foto:'./../../../uploaded_files/sis_kactivos_fijos/ActivoFijo/default.jpg',codigo:''}
            }
            //Renderea la imagen, abre la ventana
            this.afWindow.show();
            this.renderFoto(data);
        },
        cargaFormulario: function(data){
            var obj,key,objsec,keysec;
            Ext.each(this.form.getForm().items.keys, function(element, index){
                obj = Ext.getCmp(element);
                if(obj.items){
                    Ext.each(obj.items.items, function(elm, b, c){
                        if(elm.getXType()=='combo'&&elm.mode=='remote'&&elm.store!=undefined){
                            if (!elm.store.getById(data[elm.name])) {
                                var rec = new Ext.data.Record({[elm.displayField]: data[elm.gdisplayField], [elm.valueField]: data[elm.name] },data[elm.name]);
                                elm.store.add(rec);
                                elm.store.commitChanges();
                                elm.modificado = true;
                            }
                        }
                        elm.setValue(data[elm.name]);
                    },this);
                } else {
                    key = element.replace(this.idContenedor+'_','');
                    if(obj.getXType()=='combo'&&obj.mode=='remote'&&obj.store!=undefined){
                        if (!obj.store.getById(data[key])) {
                            var rec = new Ext.data.Record({[obj.displayField]: data[obj.gdisplayField], [obj.valueField]: data[key] },data[key]);
                            obj.store.add(rec);
                            obj.store.commitChanges();
                            obj.modificado = true;
                            //console.log('key:'+key,',gdisplayField:'+obj.gdisplayField,',data[obj.gdisplayField]:'+data[obj.gdisplayField],',obj.valueField:'+obj.valueField,',data[key]:'+data[key]);
                            //console.log(rec,obj.store, data[key],obj.valueField);
                        }

                    }
                    obj.setValue(data[key]);
                }
            },this);

        },
        renderFoto: function(data){
            var detailEl = Ext.getCmp('img-detail-panel').body;
            this.detailsTemplate.overwrite(detailEl, data);
            detailEl.slideIn('l', {stopFx:true,duration:.3});

            var qrcode = new QRCode('img-qr-panel'+this.idContenedor, {
                text: data.codigo,
                width: 128,
                height: 128,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
            if(data.codigo==''){
                qrcode.clear();
            } else {
                qrcode.makeCode(data.codigo);
            }
        },
        cargarValoresDefecto: function(){
            //fRnk: por defecto "Ninguno"
            Ext.getCmp(this.idContenedor+'_subtipo').setValue('Ninguno');
            Ext.getCmp(this.idContenedor+'_monto_rescate').setValue(1);
        },
        onSubmit: function(o,x,force){
            var formData;
            if(this.form.getForm().isValid()){
                //Phx.CP.loadingShow();
                formData = this.dataSubmit();
                console.log(formData);
                //agregado breydi vasquez 18/03/2020
                Ext.Ajax.request({
                                url:'../../sis_kactivos_fijos/control/ActivoFijo/verificarNoTramiteCompra',
                                params:{tramite_compra: formData.tramite_compra, id_preingreso: '',id_activo_fijo:formData.id_activo_fijo},
                                success: function(resp) {
                                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                                    if (reg.ROOT.datos.existe == 'si') {
                                        Ext.Msg.confirm('Mensaje', 'EL N° DE TRAMITE: '+formData.tramite_compra+' YA ESTA REGISTRADO CON UN ACTIVO FIJO DE ALTA',
                                        function(btn) {
                                            if (btn=="yes"){
                                                this.registrar()
                                            }
                                        }, this);

                                    }else{
                                        this.registrar()
                                    }
                                },
                                failure: this.conexionFailure,
                                timeout:this.timeout,
                                scope:this
                            });
            } else {
                Ext.MessageBox.alert('Validación','Existen datos obligatorios (*) sin información en el formulario. Complete la información y vuelva a intentarlo.'); //fRnk: mod HR915
            }
        },
        registrar: function(){
            var formData = this.dataSubmit();
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                    url: '../../sis_kactivos_fijos/control/ActivoFijo/insertarActivoFijo',
                    params: this.dataSubmit,
                    isUpload: false,
                    success: function(a,b,c){
                        this.store.rejectChanges();
                        Phx.CP.loadingHide();
                        this.afWindow.hide();
                        this.reload();
                    },
                    argument: this.argumentSave,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
        },
        onSubmitDel: function (o, x, force){
            Phx.CP.loadingShow();
            formData = this.dataSubmit();
            Ext.Ajax.request({
                url: '../../sis_kactivos_fijos/control/ActivoFijo/eliminarActivoFijo',
                params: this.dataSubmit,
                isUpload: false,
                success: function(a,b,c){
                    this.store.rejectChanges();
                    Phx.CP.loadingHide();
                    this.afWindow.hide();
                    this.reload();
                },
                argument: this.argumentSave,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },


        dataSubmit: function(){
            var submit={};
            Ext.each(this.form.getForm().items.keys, function(element, index){
                obj = Ext.getCmp(element);
                if(obj.items){
                    Ext.each(obj.items.items, function(elm, ind){
                        submit[elm.name]=elm.getValue();
                    },this)
                } else {
                    submit[obj.name]=obj.getValue();
                    if(obj.name=='id_clasificacion'){
                        if(obj.selectedIndex!=-1){
                            submit[obj.name]=obj.store.getAt(obj.selectedIndex).id;
                        }
                    }
                }
            },this);
            return submit;
        },

        filtrarGrid: function(data){
            Ext.apply(this.grid.store.baseParams,data);
            this.load();
        },

        refreshClasif: function(){

        },

        impCodigo: function(){
            var rec = this.sm.getSelected();
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_kactivos_fijos/control/ActivoFijo/impCodigoActivoFijo',
                params: { 'id_activo_fijo' : rec.data.id_activo_fijo},
                success : this.successExport,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });


        },
        loadCheckDocumentosSolWf:function() {
            var rec=this.sm.getSelected();
            rec.data.nombreVista = this.nombreVista;
            if(rec.data.id_proceso_wf==null){
                alert('El activo Fijo con codigo: '+rec.data.codigo+' no presenta documentacion \ndebido a que no fue ingresado mediante preingreso');
            }else{
                Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
                    'Chequear documento del WF',
                    {
                        width:'90%',
                        height:500
                    },
                    rec.data,
                    this.idContenedor,
                    'DocumentoWf'
                )
            }
        },

        preparaMenu : function(n) {
            var tb = Phx.vista.ActivoFijo.superclass.preparaMenu.call(this);
            var data = this.getSelectedData();
            this.getBoton('btnPhoto').enable();
            this.getBoton('btnHistorialDep').enable();
            this.getBoton('btnChequeoDocumentosWf').enable();
            if(data.estado=='alta') {
                this.getBoton('btnImpCodigo').enable();
            }
            else{
                this.getBoton('btnImpCodigo').disable();
            }
            if (data.tipo_activo == 'intangible') {
                this.getBoton('btnHistoricoAF').enable();
            }
            else {
                this.getBoton('btnHistoricoAF').disable();
            }
            return tb;
        },

        liberaMenu : function() {
            var tb = Phx.vista.ActivoFijo.superclass.liberaMenu.call(this);
            this.getBoton('btnImpCodigo').disable();
            this.getBoton('btnPhoto').disable();
            this.getBoton('btnHistorialDep').disable();
            this.getBoton('btnChequeoDocumentosWf').disable();
            this.getBoton('btnHistoricoAF').disable();
            return tb;
        },

        onButtonNew: function() {

            this.crearVentana();
            this.abrirVentana('new');
            var data = this.getSelectedData();
            Ext.getCmp(this.idContenedor+'_fecha_ini_dep').enable();
            Ext.getCmp(this.idContenedor+'_id_moneda_orig').enable();
            Ext.getCmp(this.idContenedor+'_monto_compra_orig').enable();
            Ext.getCmp(this.idContenedor+'_monto_compra_orig_100').enable();
            Ext.getCmp(this.idContenedor+'_monto_rescate').setValue(1);
            Ext.getCmp(this.idContenedor+'_monto_rescate').disable(); //fRnk: modificado, deshabilitado
            Ext.getCmp(this.idContenedor+'_vida_util_real_af').disable();
            Ext.getCmp(this.idContenedor+'_vida_util_original').enable();
            Ext.getCmp(this.idContenedor+'_id_depto').enable();
            Ext.getCmp(this.idContenedor+'_id_clasificacion').enable();
            Ext.getCmp(this.idContenedor+'_id_deposito').enable();
            Ext.getCmp(this.idContenedor+'_vida_util_original').clearInvalid();
            Ext.getCmp(this.idContenedor+'_vida_util_original_anios').clearInvalid();
            Ext.getCmp(this.idContenedor+'_cantidad_af').clearInvalid();
            if(data.tipo_activo== 'tangible'){

                Ext.getCmp('fecha_inicio').disable();
                Ext.getCmp('fecha_fin').disable();

            } else {

                Ext.getCmp('fecha_inicio').enable();
                Ext.getCmp('fecha_fin').enable();
            }

        },
        onButtonEdit: function() {

            this.crearVentana();
            this.abrirVentana('edit');
            var data = this.getSelectedData();
            this.getBoton('btnPhoto').enable();
            Ext.getCmp(this.idContenedor+'_vida_util_real_af').disable();

            if(data.estado!='registrado') {
                Ext.getCmp(this.idContenedor+'_fecha_ini_dep').disable();
                Ext.getCmp(this.idContenedor+'_id_moneda_orig').disable();
                Ext.getCmp(this.idContenedor+'_monto_compra_orig').disable();
                Ext.getCmp(this.idContenedor+'_monto_compra_orig_100').disable();
                Ext.getCmp(this.idContenedor+'_monto_rescate').disable();
                Ext.getCmp(this.idContenedor+'_vida_util_original').disable();
                Ext.getCmp(this.idContenedor+'_id_depto').disable();
                Ext.getCmp(this.idContenedor+'_id_clasificacion').disable();
                Ext.getCmp(this.idContenedor+'_id_deposito').disable();
            } else {
                Ext.getCmp(this.idContenedor+'_fecha_ini_dep').enable();
                Ext.getCmp(this.idContenedor+'_id_moneda_orig').enable();
                Ext.getCmp(this.idContenedor+'_monto_compra_orig').enable();
                Ext.getCmp(this.idContenedor+'_monto_compra_orig_100').enable();
                Ext.getCmp(this.idContenedor+'_monto_rescate').disable();
                Ext.getCmp(this.idContenedor+'_vida_util_original').enable();
                Ext.getCmp(this.idContenedor+'_id_depto').enable();
                Ext.getCmp(this.idContenedor+'_id_clasificacion').enable();
                Ext.getCmp(this.idContenedor+'_id_deposito').enable();
            }
            if(data.tipo_activo== 'tangible'){

                Ext.getCmp('fecha_inicio').disable();
                Ext.getCmp('fecha_fin').disable();

            } else {

                Ext.getCmp('fecha_inicio').enable();
                Ext.getCmp('fecha_fin').enable();
            }
            //diapra eventos de clasificaciones selecionada
            this.actualizarSegunClasificacion(data.tipo_activo, data.depreciable);
            Ext.getCmp(this.idContenedor+'_monto_rescate').disable(); //fRnk: monto_rescate siempre deshabilitado
        },

        obtenerCadenaIDs: function(){
            var rec = this.sm.getSelected();
            Ext.Msg.confirm('Confirmación', '¿Está seguro de seleccionar todos los Activos Fijos?', function(btn) {
                if (btn == "yes") {
                    Phx.CP.loadingShow();
                    var obj = {
                        start: 0,
                        limit: 50,
                        sort: 'claf.nombre',
                        dir: 'ASC'
                    };
                    Ext.apply(obj,this.grid.store.baseParams);
                    Ext.Ajax.request({
                        url: '../../sis_kactivos_fijos/control/ActivoFijo/seleccionarActivosFijos',
                        method: 'post',
                        params: obj,
                        success: this.successSave,
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });
                }
            },this);
        },

        btriguerreturn:false,

        actualizarSegunClasificacion: function(tipo_activo, depreciable){
            // console.log('actualizar',tipo_activo, depreciable)
            if(tipo_activo == 'tangible'){
                Ext.getCmp(this.idContenedor + 'af_pestaña_hist').disable();
               Ext.getCmp(this.idContenedor+'_id_deposito').enable();
                Ext.getCmp(this.idContenedor+'_nro_serie').enable();
                Ext.getCmp(this.idContenedor+'_marca').enable();
                Ext.getCmp('fecha_inicio').disable();
                Ext.getCmp('fecha_fin').disable();

            } else {
                Ext.getCmp(this.idContenedor + 'af_pestaña_hist').enable();
                Ext.getCmp(this.idContenedor+'_id_deposito').disable();
                Ext.getCmp(this.idContenedor+'_nro_serie').disable();
                Ext.getCmp(this.idContenedor+'_marca').disable();
                Ext.getCmp('fecha_inicio').enable();
                Ext.getCmp('fecha_fin').enable();
            }

            if(depreciable == 'si'){
                Ext.getCmp(this.idContenedor+'_vida_util_original').enable();
                Ext.getCmp(this.idContenedor+'_vida_util_original_anios').enable();
                Ext.getCmp(this.idContenedor+'_vida_util_real_af').disable();
                Ext.getCmp(this.idContenedor+'_monto_rescate').disable();
            } else {
                Ext.getCmp(this.idContenedor+'_vida_util_original').disable();
                Ext.getCmp(this.idContenedor+'_vida_util_original_anios').disable();
                Ext.getCmp(this.idContenedor+'_vida_util_real_af').disable();
                Ext.getCmp(this.idContenedor+'_monto_rescate').disable();
            }

            Ext.getCmp(this.idContenedor + '_estado').disable();
            Ext.getCmp(this.idContenedor + '_reval').disable();
            Ext.getCmp(this.idContenedor + '_cantidad_revaloriz').disable();
            Ext.getCmp(this.idContenedor + '_id_depto').disable();
            Ext.getCmp(this.idContenedor + '_id_clasificacion').disable();
            Ext.getCmp(this.idContenedor + '_vida_util_original').disable();
            Ext.getCmp(this.idContenedor + '_vida_util_original_anios').disable();
            Ext.getCmp(this.idContenedor + '_nro_serie').disable();
            Ext.getCmp(this.idContenedor + '_marca').disable();
            Ext.getCmp(this.idContenedor + '_denominacion').disable();
            Ext.getCmp(this.idContenedor + '_descripcion').disable();
            Ext.getCmp(this.idContenedor + '_cantidad_af').disable();
            Ext.getCmp(this.idContenedor + '_id_unidad_medida').disable();
            Ext.getCmp(this.idContenedor + '_id_cat_estado_fun').disable();
            Ext.getCmp(this.idContenedor + '_codigo_ant').disable();
            Ext.getCmp(this.idContenedor + '_observaciones').disable();
            Ext.getCmp(this.idContenedor + '_fecha_asignacion').disable();
            Ext.getCmp(this.idContenedor + '_prestamo').disable();
            Ext.getCmp(this.idContenedor + '_fecha_dev_prestamo').disable();
            Ext.getCmp(this.idContenedor + '_id_deposito').disable();
            Ext.getCmp(this.idContenedor + '_id_oficina').disable();
            Ext.getCmp(this.idContenedor + '_id_funcionario').disable();
            Ext.getCmp(this.idContenedor + '_id_persona').disable();
            Ext.getCmp(this.idContenedor + '_ubicacion').disable();
            Ext.getCmp(this.idContenedor + '_fecha_ini_dep').disable();
            Ext.getCmp(this.idContenedor + '_monto_vigente_real_af').disable();
            Ext.getCmp(this.idContenedor + '_depreciacion_acum_real_af').disable();
            Ext.getCmp(this.idContenedor + '_depreciacion_per_real_af').disable();
            Ext.getCmp(this.idContenedor + '_depreciacion').disable();
            Ext.getCmp(this.idContenedor + '_fecha_ult_dep_real_af').disable();
            Ext.getCmp(this.idContenedor + '_vida_util_real_af').disable();
            Ext.getCmp(this.idContenedor + '_monto_rescate').disable();
            Ext.getCmp(this.idContenedor + '_id_proyecto').disable();
        },


        subirFoto: function(){
            var rec = this.sm.getSelected();
            Phx.CP.loadWindows('../../../sis_kactivos_fijos/vista/activo_fijo/SubirFoto.php',
                'Subir Foto',
                {
                    modal:true,
                    width:450,
                    height:150
                }, rec.data, this.idContenedor, 'SubirFoto');

        },

        tabeast: [/*fRnk: se quitaron los tabs, Caracteristicas y Centros de Costo
        {
            url: '../../../sis_kactivos_fijos/vista/activo_fijo_caract/ActivoFijoCaract.php',
            title: 'Caracteristicas',
            width: '35%',
            cls: 'ActivoFijoCaract'
        },*/
            {
                url: '../../../sis_kactivos_fijos/vista/movimiento/MovimientoPorActivo.php',
                title: 'Movimientos',
                width: '35%',
                cls: 'MovimientoPorActivo'
            },
            {
                url: '../../../sis_kactivos_fijos/vista/activo_fijo_valores/ActivoFijoValoresDepPrin.php',
                title: 'Depreciaciones/Actualizaciones',
                cls: 'ActivoFijoValoresDepPrin'
            },
            /*{
                url: '../../../sis_kactivos_fijos/vista/tipo_prorrateo/TipoProrrateo.php',
                title: 'Centros de Costo',
                cls: 'TipoProrrateo'
            },*/
            {
                url: '../../../sis_kactivos_fijos/vista/activo_fijo_modificacion/ActivoFijoModificacion.php',
                title: 'Modificaciones',
                cls: 'ActivoFijoModificacion'
            }

        ],
        abrirMovimientosRapido: function(tipoMov,title){
            var params={};
            //Obtiene los registros seleccionados de la grilla
            var rec = this.sm.getSelections();

            if(this.sm.getCount()>0){
                var win = Phx.CP.loadWindows('../../../sis_kactivos_fijos/vista/movimiento/MovimientoRapido.php',
                    title, {
                        width: 870,
                        height : 620,
                        buttons: [],
                        bbar: []
                    }, {
                        dataAf: rec,
                        tipoMov: tipoMov,
                        title: title
                    },
                    this.idContenedor,
                    'MovimientoRapido'
                );
            } else {
                Ext.MessageBox.alert('Información','Debe seleccionar almenos un Registro para continuar');
            }
        },
        crearMenuMov: function(){
            //Creación de menúes
            this.mnuMov = new Ext.menu.Item({
                text: 'Generar Movimiento Af',
                icon:'../../../lib/imagenes/gear.png',
                menu:{}
            });
            this.mnuMovAsig = new Ext.menu.Item({
                text: 'Asignación',
                icon:'../../../lib/imagenes/gear.png',
                handler: function(){
                    this.abrirMovimientosRapido('asig','Asignación');
                },
                scope: this
            });
            this.mnuMovTransf = new Ext.menu.Item({
                text: 'Transferencia',
                icon:'../../../lib/imagenes/gear.png',
                handler: function(){
                    this.abrirMovimientosRapido('transf','Transferencia');
                },
                scope: this
            });
            this.mnuMovDevol = new Ext.menu.Item({
                text: 'Devolución',
                icon:'../../../lib/imagenes/gear.png',
                handler: function(){
                    this.abrirMovimientosRapido('devol','Devolución');
                },
                scope: this
            });

            //Adición al menú contextual
            this.mnuMov.menu.addItem(this.mnuMovAsig);
            this.mnuMov.menu.addItem(this.mnuMovTransf);
            this.mnuMov.menu.addSeparator()
            this.mnuMov.menu.addItem(this.mnuMovDevol);
            this.ctxMenu.addItem(this.mnuMov);
        },
        convertirVidaUtil(cantidad,tipo='mes'){
            var valor=0;
            if(tipo=='anios'){
                //Convierte de años a meses
                valor = Ext.util.Format.round(cantidad * 12,0);
            } else {
                //Convierte de meses a años
                valor = Ext.util.Format.round(cantidad / 12,2);
            }
            return valor;
        },
        abrirDetalleDep: function(){
            var rec = this.sm.getSelections();
            var win = Phx.CP.loadWindows(
                '../../../sis_kactivos_fijos/vista/activo_fijo_valores/ActivoFijoValoresHist.php',
                'Detalle depreciación', {
                    width: '95%',
                    height: '95%'
                }, rec,
                this.idContenedor,
                'ActivoFijoValoresHist'
            );
        },
        historicoAF: function () {
            var data = this.getSelectedData();
            this.crearVentanaHist(data);
            this.abrirVentana('edit');
            // if (tipo_activo = 'intangible'){
            //     this.abrirVentana('edit');
            // },
            //this.abrirVentana('new');


            this.actualizarSegunClasificacion(data.tipo_activo, data.depreciable);
        },
        crearVentanaHist: function (data) {
            console.log('crearVentanaHist', data);
            if (this.afWindow) {
                this.form.destroy();
                this.afWindow.destroy();
            }
            this.form = new Ext.form.FormPanel({
                id: this.idContenedor + '_af_form',
                items: [{
                    region: 'center',
                    layout: 'column',
                    border: false,
                    autoScroll: true,
                    items: [{
                        xtype: 'tabpanel',
                        plain: true,
                        activeTab: 0,
                        height: 515,
                        deferredRender: false,
                        defaults: {
                            bodyStyle: 'padding:10px'
                        },
                        items: [
                            {
                                title: 'Datos Compra',
                                layout: 'form',
                                defaults: {
                                    width: 400
                                },
                                defaultType: 'textfield',
                                items: [{
                                    xtype: 'combo',
                                    fieldLabel: 'Proveedor',
                                    name: 'id_proveedor',
                                    allowBlank: true,
                                    id: this.idContenedor + '_id_proveedor',
                                    emptyText: 'Elija el Proveedor',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_parametros/control/Proveedor/listarProveedorCombos',
                                        id: 'id_proveedor',
                                        root: 'datos',
                                        fields: ['id_proveedor', 'desc_proveedor'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'desc_proveedor',
                                            direction: 'ASC'
                                        },
                                        baseParams: {
                                            start: 0,
                                            limit: 10,
                                            sort: 'desc_proveedor',
                                            dir: 'ASC',
                                            par_filtro: 'provee.desc_proveedor'
                                        }
                                    }),
                                    valueField: 'id_proveedor',
                                    displayField: 'desc_proveedor',
                                    gdisplayField: 'desc_proveedor',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    lazyRender: true,
                                    pageSize: 15,
                                    minChars: 2,
                                    queryDelay: 1000,
                                    valueNotFoundText: 'PROVEEDOR INEXISTENTE',
                                    pageSize: 15
                                }, {
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha Compra',
                                    name: 'fecha_compra',
                                    allowBlank: false,
                                    id: this.idContenedor + '_fecha_compra'
                                }, {
                                    fieldLabel: 'Documento',
                                    name: 'documento',
                                    id: this.idContenedor + '_documento'
                                }, {
                                    xtype: 'compositefield',
                                    fieldLabel: 'Importe',
                                    // msgTarget: 'side',
                                    anchor: '-20',
                                    /* defaults: {
                                    flex: 1
                                },*/
                                    items: [{
                                        xtype: 'label',
                                        text: 'Costo AF'
                                    }, {
                                        xtype: 'numberfield',
                                        fieldLabel: 'Monto compra 87',
                                        name: 'monto_compra_orig',
                                        allowBlank: false,
                                        id: this.idContenedor + '_monto_compra_orig',
                                        width: 127
                                    }, {
                                        xtype: 'combo',
                                        fieldLabel: 'Moneda',
                                        name: 'id_moneda_orig',
                                        allowBlank: false,
                                        width: 50,
                                        listWidth: 50,
                                        id: this.idContenedor + '_id_moneda_orig',
                                        emptyText: 'Elija la moneda de compra',
                                        store: new Ext.data.JsonStore({
                                            url: '../../sis_parametros/control/Moneda/listarMoneda',
                                            id: 'id_moneda',
                                            root: 'datos',
                                            fields: ['id_moneda', 'codigo', 'moneda'],
                                            totalProperty: 'total',
                                            sortInfo: {
                                                field: 'moneda',
                                                direction: 'ASC'
                                            },
                                            baseParams: {
                                                start: 0,
                                                limit: 10,
                                                sort: 'moneda',
                                                dir: 'ASC',
                                                par_filtro: 'moneda.moneda'
                                            }
                                        }),
                                        valueField: 'id_moneda',
                                        displayField: 'codigo',
                                        gdisplayField: 'desc_moneda_orig',
                                        mode: 'remote',
                                        triggerAction: 'all',
                                        lazyRender: true,
                                        pageSize: 15
                                    }, {
                                        xtype: 'label',
                                        text: 'Valor Compra'
                                    }, {
                                        xtype: 'numberfield',
                                        fieldLabel: 'Monto compra 100',
                                        name: 'monto_compra_orig_100',
                                        allowBlank: false,
                                        id: this.idContenedor + '_monto_compra_orig_100',
                                        width: 127
                                    }]
                                }, {
                                    xtype: 'combo',
                                    fieldLabel: 'Estado Activo Compra',
                                    name: 'id_cat_estado_compra',
                                    allowBlank: false,
                                    id: this.idContenedor + '_id_cat_estado_compra',
                                    emptyText: 'Elija una opción',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
                                        id: 'id_catalogo',
                                        root: 'datos',
                                        fields: ['id_catalogo', 'codigo', 'descripcion'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'descripcion',
                                            direction: 'ASC'
                                        },
                                        baseParams: {
                                            start: 0,
                                            limit: 10,
                                            sort: 'descripcion',
                                            dir: 'ASC',
                                            par_filtro: 'cat.descripcion',
                                            cod_subsistema: 'KAF',
                                            catalogo_tipo: 'tactivo_fijo__id_cat_estado_compra'
                                        }
                                    }),
                                    valueField: 'id_catalogo',
                                    displayField: 'descripcion',
                                    gdisplayField: 'estado_compra',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    lazyRender: true,
                                    pageSize: 15
                                }, {
                                    xtype: 'textfield',
                                    fieldLabel: 'Nro.Cbte Asociado',
                                    name: 'nro_cbte_asociado',
                                    allowBlank: true,
                                    id: this.idContenedor + '_nro_cbte_asociado',
                                    width: 140
                                }, {
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha.Cbte Asociado',
                                    name: 'fecha_cbte_asociado',
                                    allowBlank: true,
                                    id: this.idContenedor + '_fecha_cbte_asociado',
                                    width: 140
                                }, {
                                    xtype: 'textfield',
                                    fieldLabel: 'Nro. de Tramite de Compra',
                                    name: 'tramite_compra',
                                    allowBlank: true,
                                    id: this.idContenedor + '_tramite_compra',
                                    width: 140
                                }, {
                                    xtype: 'combo',
                                    fieldLabel: 'Subtipo',
                                    name: 'subtipo',
                                    allowBlank: true,
                                    mode: 'local',
                                    triggerAction: 'all',
                                    emptyText: 'Elija una opción',
                                    id: this.idContenedor + '_subtipo',
                                    emptyText: 'Elija una opción',
                                    store: new Ext.data.ArrayStore({
                                        id: 0,
                                        fields: ['subtipo'],
                                        data: [['Ninguno'], ['Leasing']]
                                    }),
                                    valueField: 'subtipo',
                                    displayField: 'subtipo'

                                },
                                    {
                                        xtype: 'combo',
                                        fieldLabel: 'Unidad Solicitante',
                                        name: 'id_uo',
                                        allowBlank: true,
                                        id: this.idContenedor + '_id_uo',
                                        emptyText: 'Elija el Proveedor',
                                        store: new Ext.data.JsonStore({
                                            url: '../../sis_kactivos_fijos/control/ActivoFijo/listarAFUnidSol',
                                            id: 'id_uo',
                                            root: 'datos',
                                            fields: ['id_uo', 'nombre_unidad'],
                                            totalProperty: 'total',
                                            sortInfo: {
                                                field: 'nombre_unidad',
                                                direction: 'ASC'
                                            },
                                            baseParams: {
                                                start: 0,
                                                limit: 10,
                                                sort: 'nombre_unidad',
                                                dir: 'ASC',
                                                par_filtro: 'uo.nombre_unidad'
                                            }
                                        }),
                                        valueField: 'id_uo',
                                        displayField: 'nombre_unidad',
                                        gdisplayField: 'nombre_unidad',
                                        mode: 'remote',
                                        triggerAction: 'all',
                                        lazyRender: true,
                                        pageSize: 15,
                                        minChars: 2,
                                        queryDelay: 1000,
                                        valueNotFoundText: 'PROVEEDOR INEXISTENTE',
                                        pageSize: 15
                                    },
                                    {
                                        xtype: 'datefield',
                                        fieldLabel: 'Fecha Inicio',
                                        name: 'fecha_inicio',
                                        allowBlank: true,
                                        id: 'fecha_inicio',
                                        width: 140
                                    },
                                    {
                                        xtype: 'datefield',
                                        fieldLabel: 'Fecha Fin',
                                        name: 'fecha_fin',
                                        allowBlank: true,
                                        id: 'fecha_fin',
                                        width: 140
                                    },
                                    {
                                        xtype: 'numberfield',
                                        hidden:true,
                                        value:0,
                                        name: 'renova',
                                        allowBlank: true,
                                        id: 'renova',
                                        width: 140
                                    }

                                ]
                            },
                            {//inicia toda la pestaña
                                //id: 'af_pestaña_hist',
                                id: this.idContenedor + 'af_pestaña_hist',
                                title: 'Histórico A.F.',
                                region: 'center',
                                //layout: 'column',
                                border: false,
                                defaults: {
                                    width: 950
                                },
                                // width: 550,
                                // height: 100,
                                autoScroll: true,
                                items: [
                                    new Ext.Panel({
                                        //     new Ext.grid.Panel({
                                        // id: 'af_filter_depto',
                                        id: 'af_filter_hist',
                                        autoScroll: true,
                                        border: true,
                                        width: 950,
                                        height: 420,

                                        tools: [{
                                            id: 'refresh',
                                            qtip: 'Actualizar',
                                            handler: function (event, toolEl, panel) {
                                                Ext.getCmp('af_filter_af_list').store.reload();

                                            }
                                        }],
                                        items: [
                                            // new Ext.list.ListView({
                                            new Ext.grid.GridPanel({
                                                id: 'af_filter_af_list',
                                                scope: this,
                                                //border: true,
                                                //width: '100%',
                                                // autoScroll: true,

                                                store: new Ext.data.JsonStore({
                                                    url: '../../sis_kactivos_fijos/control/ActivoFijo/listarActivoFijoHistorico',
                                                    id: 'id_activo_fijo_hist',
                                                    root: 'datos',
                                                    fields: ['id_activo_fijo_hist', 'codigo_hist', 'denominacion_hist', 'descripcion_hist',
                                                        'cantidad_af_hist', 'documento_hist', 'fecha_compra_hist',
                                                        // {name: 'fecha_compra_hist', type: 'date', dateFormat: 'Y-m-d'},

                                                        'fecha_ini_dep_hist', 'monto_compra_hist',
                                                        'monto_compra_orig_hist', 'monto_compra_orig_100_hist', 'observaciones_hist', 'tipo_reg_hist', 'tramite_compra_hist',
                                                        'ubicacion_hist', 'vida_util_original_hist', 'id_activo_fijo', 'depto', 'clasificacion',
                                                        'estado_hist', 'oficina', 'funcionario', 'desc_proveedor', 'nro_cbte_asociado_hist',
                                                        {
                                                            name: 'fecha_cbte_asociado_hist',
                                                            type: 'date',
                                                            dateFormat: 'Y-m-d'
                                                        },
                                                        'nombre_unidad', 'desc_proyecto', 'desc_moneda_orig',
                                                        'fecha_inicio', 'fecha_fin', 'fecha_mod', 'fecha_reg',
                                                        // {name: 'fecha_inicio', type: 'date', dateFormat: 'Y-m-d'},
                                                        // {name: 'fecha_fin', type: 'date', dateFormat: 'Y-m-d'},
                                                        // {name: 'fecha_mod', type: 'date',dateFormat:'Y-m-d'},
                                                        // {name: 'fecha_reg', type: 'date',dateFormat:'Y-m-d'},
                                                        'estado_reg', {name:'usr_reg', type: 'string'}, {name:'usr_mod', type: 'string'}],

                                                    totalProperty: 'total',
                                                    sortInfo: {
                                                        field: 'fecha_reg',
                                                        direction: 'DESC'
                                                    },

                                                    remoteSort: true, //para que aparezca directo los datos en la grilla
                                                    baseParams: {
                                                        start: 0,
                                                        limit: 50,
                                                        sort: 'codigo_hist',
                                                        dir: 'ASC',
                                                        codigo_subsistema: 'KAF',
                                                        par_filtro: 'afh.codigo_hist#afh.denominacion_hist#afh.descripcion_hist',
                                                        id_activo_fijo: data.id_activo_fijo
                                                    }

                                                }),

                                                singleSelect: true,
                                                emptyText: 'No existen Activos Fijos pasados',
                                                reserveScrollOffset: true,

                                                columns: [
                                                    new Ext.grid.RowNumberer(),
                                                    // {
                                                    //     //header: 'id_depto',
                                                    //     width: 20,
                                                    //     dataIndex: 'id_activo_fijo_hist',
                                                    //     hidden: true
                                                    // },
                                                    {
                                                        header: 'Detalle',
                                                        width: 230,
                                                        //dataIndex: 'codigo_hist',
                                                        renderer: function (value, p, record) {
                                                            return '<div class="x-combo-list-item">' +
                                                                '<p><b>Código: <span style="color:#274d80;">' + record.data['codigo_hist'] + '</span></b></p>' +
                                                                '<p><b>Denominación: <span style="color:#274d80;">' + record.data['denominacion_hist'] + '</span></b></p>' +
                                                                //'<p><b>Descripción: <span style="color:#274d80;">' + record.data['descripcion'] + '</span></b></p>' +
                                                                '<p><b>Fecha Compra: </b><span style="color:#274d80;" >' + record.data['fecha_compra_hist'] + '</span></p>' +
                                                                '<p><b>Fecha Inicio: </b><span style="color:#274d80;">' + record.data['fecha_inicio']+ '</span></p>' +
                                                                '<p><b>Fecha Fin: </b><span style="color:#274d80;">' + record.data['fecha_fin']+ '</span></p>'+

                                                                '</div>'
                                                        }
                                                    },

                                                    // {
                                                    //     header: 'Fechas',
                                                    //     width: 200,
                                                    //     renderer: function (value, p, record) {
                                                    //         return '<div class="x-combo-list-item">' +
                                                    //             '<p><b>Fecha Compra: </b><span style="color:#274d80;" >' + record.data['fecha_compra_hist'].dateFormat('d/m/Y') + '</span></p>' +
                                                    //             '<p><b>Fecha Inicio: </b><span style="color:#274d80;">' + record.data['fecha_inicio'] + '</span></p>' +
                                                    //             '<p><b>Fecha Fin: </b><span style="color:#274d80;">' + record.data['fecha_fin'] + '</span></p>' +
                                                    //             '</div>'
                                                    //     }
                                                    // },
                                                    {
                                                        header: 'Importe',
                                                        width: 150,
                                                        renderer: function (value, p, record) {
                                                            return '<div class="x-combo-list-item">' +
                                                                '<p><b>Moneda: </b><span style="color:#274d80;">' + record.data['desc_moneda_orig'] + '</span></p>' +
                                                                '<p><b>Monto 87%: </b><span style="color:#274d80;">' + record.data['monto_compra_orig_hist'] + '</span></p>' +
                                                                '<p><b>Monto 100%: </b><span style="color:#274d80;">' + record.data['monto_compra_orig_100_hist'] + '</span></p>' +
                                                                '</div>'
                                                        }
                                                    },
                                                    {
                                                        header: 'Datos',
                                                        width: 205,
                                                        renderer: function (value, p, record) {
                                                            return '<div class="x-combo-list-item">' +
                                                                '<p><b>Proveedor: </b><span style="color:#274d80;">' + record.data['desc_proveedor'] + '</span></p>' +
                                                                '<p><b>Unidad Solicitante: </b><span style="color:#274d80;">' + record.data['nombre_unidad'] + '</span></p>' +
                                                                '<p><b>Documento: </b><span style="color:#274d80;">' + record.data['documento_hist'] + '</span></p>' +
                                                                '<p><b>Nro. de Tramite de Compra: </b><span style="color:#274d80;">' + record.data['tramite_compra_hist'] + '</span></p>' +
                                                                '</div>'
                                                        }
                                                    },
                                                    {
                                                        header: 'Cbte',
                                                        width: 180,
                                                        renderer: function (value, p, record) {
                                                            return '<div class="x-combo-list-item">' +
                                                                '<p><b>Nro Cbte Asociado: </b><span style="color:#274d80;">' + record.data['nro_cbte_asociado_hist'] + '</span></p>' +
                                                                '<p><b>Fecha Cbte Asociado: </b><span style="color:#274d80;">' + record.data['fecha_cbte_asociado_hist'].dateFormat('d/m/Y') + '</span></p>' +
                                                                '</div>'
                                                        }
                                                    },
                                                    {
                                                        header: 'Datos de Registro',
                                                        width: 150,
                                                        renderer: function (value, p, record) {
                                                            return '<div class="x-combo-list-item">' +
                                                                '<p><b>Creado por: </b><span style="color:#274d80;">' + record.data['usr_reg'] + '</span></p>' +
                                                                '<p><b>Fecha creación: </b><span style="color:#274d80;">' + record.data['fecha_reg'] + '</span></p>' +
                                                                '<p><b>Modificado por: </b><span style="color:#274d80;">' + record.data['usr_mod'] + '</span></p>' +
                                                                '<p><b>Fecha Modif.: </b><span style="color:#274d80;">' + record.data['fecha_mod']+ '</span></p>' +
                                                                '</div>'
                                                        }
                                                    }

                                                ],


                                            })//
                                        ]
                                    })

                                ]


                            },//cierra toda la pestaña

                            {
                                title: 'Principal',
                                layout: 'form',
                                defaults: {
                                    width: 400
                                },
                                autoScroll: true,
                                defaultType: 'textfield',

                                items: [{
                                    name: 'id_activo_fijo',
                                    hidden: true,
                                    id: this.idContenedor + '_id_activo_fijo'
                                }, {
                                    name: 'foto',
                                    hidden: true,
                                    id: this.idContenedor + '_foto'
                                }, {
                                    fieldLabel: 'Código',
                                    name: 'codigo',
                                    disabled: true,
                                    id: this.idContenedor + '_codigo'
                                }, {
                                    fieldLabel: 'Estado',
                                    name: 'estado',
                                    disabled: true,
                                    id: this.idContenedor + '_estado'
                                }, {
                                    xtype: 'compositefield',
                                    fieldLabel: 'Revalorizado',
                                    //msgTarget: 'side',
                                    anchor: '-20',
                                    disabled: true,
                                    /* defaults: {
                                        flex: 1
                                    },*/
                                    items: [{
                                        xtype: 'checkbox',
                                        name: 'reval',
                                        width: 10,
                                        disabled: true,
                                        id: this.idContenedor + '_reval'
                                    }, {
                                        xtype: 'numberfield',
                                        name: 'cantidad_revaloriz',
                                        width: 30,
                                        disabled: true,
                                        id: this.idContenedor + '_cantidad_revaloriz'
                                    }]
                                }, {
                                    xtype: 'combo',
                                    fieldLabel: 'Depto.',
                                    name: 'id_depto',
                                    allowBlank: false,
                                    id: this.idContenedor + '_id_depto',
                                    emptyText: 'Elija un Departamento',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_parametros/control/Depto/listarDeptoFiltradoDeptoUsuario',
                                        id: 'id_depto',
                                        root: 'datos',
                                        fields: ['id_depto', 'codigo', 'nombre'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'codigo',
                                            direction: 'ASC'
                                        },
                                        baseParams: {
                                            start: 0,
                                            limit: 10,
                                            sort: 'codigo',
                                            dir: 'ASC',
                                            codigo_subsistema: 'KAF',
                                            par_filtro: 'DEPPTO.codigo#DEPPTO.nombre'
                                        }
                                    }),
                                    valueField: 'id_depto',
                                    displayField: 'nombre',
                                    gdisplayField: 'depto',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    lazyRender: true,
                                    pageSize: 15
                                }, {
                                    xtype: 'combo',
                                    fieldLabel: 'Clasificación',
                                    name: 'id_clasificacion',
                                    allowBlank: false,
                                    id: this.idContenedor + '_id_clasificacion',
                                    emptyText: 'Elija la Clasificación',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_kactivos_fijos/control/Clasificacion/ListarClasificacionTree',
                                        id: 'id_clasificacion',
                                        root: 'datos',
                                        sortInfo: {
                                            field: 'orden',
                                            direction: 'ASC'
                                        },
                                        totalProperty: 'total',
                                        fields: ['id_clasificacion', 'clasificacion', 'id_clasificacion_fk', 'tipo_activo', 'depreciable', 'vida_util'],
                                        remoteSort: true,
                                        baseParams: {
                                            par_filtro: 'claf.clasificacion'
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
                                    minChars: 2
                                }, {
                                    xtype: 'compositefield',
                                    fieldLabel: 'Vida útil inicial',
                                    items: [{
                                        xtype: 'label',
                                        text: 'Meses'
                                    }, {
                                        xtype: 'numberfield',
                                        fieldLabel: 'Vida útil inicial (meses)',
                                        name: 'vida_util_original',
                                        width: 60,
                                        allowBlank: false,
                                        id: this.idContenedor + '_vida_util_original'
                                    }, {
                                        xtype: 'label',
                                        text: 'Años'
                                    }, {
                                        xtype: 'numberfield',
                                        fieldLabel: 'Vida útil inicial (años)',
                                        name: 'vida_util_original_anios',
                                        width: 60,
                                        allowBlank: false,
                                        id: this.idContenedor + '_vida_util_original_anios'
                                    }]
                                }, {
                                    fieldLabel: '#Serie',
                                    name: 'nro_serie',
                                    allowBlank: true,
                                    id: this.idContenedor + '_nro_serie'
                                }, {
                                    fieldLabel: 'Marca',
                                    name: 'marca',
                                    allowBlank: true,
                                    id: this.idContenedor + '_marca'
                                }, {
                                    fieldLabel: 'Denominación',
                                    name: 'denominacion',
                                    allowBlank: false,
                                    id: this.idContenedor + '_denominacion'
                                }, {
                                    xtype: 'textarea',
                                    fieldLabel: 'Descripción',
                                    name: 'descripcion',
                                    allowBlank: false,
                                    id: this.idContenedor + '_descripcion'
                                }, {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Cantidad',
                                    width: 60,
                                    name: 'cantidad_af',
                                    allowBlank: false,
                                    id: this.idContenedor + '_cantidad_af'
                                }, {
                                    xtype: 'combo',
                                    fieldLabel: '*Unidad de Medida',
                                    name: 'id_unidad_medida',
                                    //hiddenName: 'id_cat_estado_fun',
                                    allowBlank: false,
                                    id: this.idContenedor + '_id_unidad_medida',
                                    emptyText: 'Elija una opción',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_parametros/control/UnidadMedida/listarUnidadMedida',
                                        id: 'id_unidad_medida',
                                        root: 'datos',
                                        fields: ['id_unidad_medida', 'codigo', 'descripcion'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'codigo',
                                            direction: 'ASC'
                                        },
                                        baseParams: {
                                            start: 0,
                                            limit: 10,
                                            sort: 'descripcion',
                                            dir: 'ASC'
                                        }
                                    }),
                                    valueField: 'id_unidad_medida',
                                    hiddenValue: 'id_unidad_medida',
                                    displayField: 'descripcion',
                                    gdisplayField: 'descripcion_unmed',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    lazyRender: true,
                                    pageSize: 15,
                                    tpl: '<tpl for="."><div class="x-combo-list-item"><p>{codigo} - {descripcion}</p></div></tpl>',
                                }, {
                                    xtype: 'combo',
                                    fieldLabel: 'Estado funcional Actual',
                                    name: 'id_cat_estado_fun',
                                    //hiddenName: 'id_cat_estado_fun',
                                    allowBlank: false,
                                    id: this.idContenedor + '_id_cat_estado_fun',
                                    emptyText: 'Elija una opción',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_parametros/control/Catalogo/listarCatalogoCombo',
                                        id: 'id_catalogo',
                                        root: 'datos',
                                        fields: ['id_catalogo', 'codigo', 'descripcion'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'codigo',
                                            direction: 'ASC'
                                        },
                                        baseParams: {
                                            start: 0,
                                            limit: 10,
                                            sort: 'descripcion',
                                            dir: 'ASC',
                                            par_filtro: 'cat.descripcion',
                                            cod_subsistema: 'KAF',
                                            catalogo_tipo: 'tactivo_fijo__id_cat_estado_fun'
                                        }
                                    }),
                                    valueField: 'id_catalogo',
                                    hiddenValue: 'id_catalogo',
                                    displayField: 'descripcion',
                                    gdisplayField: 'estado_fun',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    lazyRender: true,
                                    pageSize: 15,
                                    tpl: '<tpl for="."><div class="x-combo-list-item"><p>{codigo} - {descripcion}</p></div></tpl>',
                                }, {
                                    xtype: 'textfield',
                                    fieldLabel: 'Codigo Ant.',
                                    name: 'codigo_ant',
                                    id: this.idContenedor + '_codigo_ant'
                                }/*, {
                                xtype: 'textarea',
                                fieldLabel: 'Caracteristicas',
                                name: 'observaciones',
                                id: this.idContenedor+'_caracteristicas'
                            }*/, {
                                    xtype: 'textarea',
                                    fieldLabel: 'Observaciones',
                                    name: 'observaciones',
                                    id: this.idContenedor + '_observaciones'
                                }]
                            },


                            {
                                title: 'Ubicación Física',
                                layout: 'form',
                                defaults: {
                                    width: 400
                                },
                                defaultType: 'textfield',
                                items: [{
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha Asignación',
                                    name: 'fecha_asignacion',
                                    disabled: true,
                                    id: this.idContenedor + '_fecha_asignacion'
                                }, {
                                    xtype: 'textfield',
                                    fieldLabel: 'Préstamo',
                                    name: 'prestamo',
                                    disabled: true,
                                    id: this.idContenedor + '_prestamo'
                                }, {
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha Devolución Préstamo',
                                    name: 'fecha_dev_prestamo',
                                    disabled: true,
                                    id: this.idContenedor + '_fecha_dev_prestamo'
                                }, {
                                    xtype: 'combo',
                                    fieldLabel: 'Depósito',
                                    name: 'id_deposito',
                                    allowBlank: true,
                                    id: this.idContenedor + '_id_deposito',
                                    emptyText: 'Elija el depósito',
                                    store: new Ext.data.JsonStore({
                                        url: '../../sis_kactivos_fijos/control/Deposito/listarDeposito',
                                        id: 'id_deposito',
                                        root: 'datos',
                                        fields: ['id_deposito', 'id_funcionario', 'id_oficina', 'ubicacion', 'codigo', 'nombre', 'depto', 'depto_cod', 'funcionario', 'oficina_cod', 'oficina'],
                                        totalProperty: 'total',
                                        sortInfo: {
                                            field: 'codigo',
                                            direction: 'ASC'
                                        },
                                        baseParams: {
                                            start: 0,
                                            limit: 10,
                                            sort: 'codigo',
                                            dir: 'ASC',
                                            par_filtro: 'depaf.codigo#depaf.nombre'
                                        }
                                    }),
                                    valueField: 'id_deposito',
                                    displayField: 'nombre',
                                    gdisplayField: 'deposito',
                                    mode: 'remote',
                                    triggerAction: 'all',
                                    lazyRender: true,
                                    pageSize: 15
                                }, {
                                    xtype: 'combo',
                                    fieldLabel: 'Oficina',
                                    name: 'id_oficina',
                                    allowBlank: false,
                                    disabled: true,
                                    id: this.idContenedor + '_id_oficina',
                                    store: new Ext.data.JsonStore({}),
                                    valueField: 'id_oficina',
                                    displayField: 'nombre',
                                    gdisplayField: 'oficina',
                                    pageSize: 15
                                }, {
                                    xtype: 'combo',
                                    fieldLabel: 'Responsable',
                                    name: 'id_funcionario',
                                    allowBlank: false,
                                    disabled: true,
                                    id: this.idContenedor + '_id_funcionario',
                                    store: new Ext.data.JsonStore({}),
                                    valueField: 'id_funcionario',
                                    displayField: 'nombre',
                                    gdisplayField: 'funcionario'
                                }, {
                                    fieldLabel: 'Custodio',
                                    name: 'id_persona',
                                    disabled: true,
                                    id: this.idContenedor + '_id_persona'
                                }, {
                                    xtype: 'textarea',
                                    fieldLabel: 'Ubicación',
                                    name: 'ubicacion',
                                    id: this.idContenedor + '_ubicacion',
                                    disabled: true
                                }]
                            },

                            {
                                title: 'Datos Depreciación',
                                layout: 'form',
                                defaults: {
                                    width: 400
                                },
                                defaultType: 'textfield',
                                items: [{
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha inicio Dep/Act',
                                    qtip: 'Fecha de inicio de depreciación o de actualización',
                                    name: 'fecha_ini_dep',
                                    allowBlank: false,
                                    id: this.idContenedor + '_fecha_ini_dep'
                                }, {
                                    fieldLabel: 'Monto Vigente',
                                    name: 'monto_vigente_real_af',
                                    disabled: true,
                                    id: this.idContenedor + '_monto_vigente_real_af'
                                }, {
                                    fieldLabel: 'Depreciación Acumulada',
                                    name: 'depreciacion_acum_real_af',
                                    disabled: true,
                                    id: this.idContenedor + '_depreciacion_acum_real_af'
                                }, {
                                    fieldLabel: 'Depreciación Periodo',
                                    name: 'depreciacion_per_real_af',
                                    disabled: true,
                                    id: this.idContenedor + '_depreciacion_per_real_af'
                                }, {
                                    fieldLabel: 'Depreciación Mes',
                                    name: 'depreciacion_mes',
                                    disabled: true,
                                    id: this.idContenedor + '_depreciacion'
                                }, {
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha última Depreciación',
                                    name: 'fecha_ult_dep_real_af',
                                    disabled: true,
                                    id: this.idContenedor + '_fecha_ult_dep_real_af'
                                }, {
                                    fieldLabel: 'Vida Útil restante',
                                    name: 'vida_util_real_af',
                                    disabled: true,
                                    id: this.idContenedor + '_vida_util_real_af'
                                }, {
                                    fieldLabel: 'Monto de rescate',
                                    name: 'monto_rescate',
                                    allowBlank: true,
                                    id: this.idContenedor + '_monto_rescate'
                                },

                                    {
                                        xtype: 'combo',
                                        name: 'id_proyecto',
                                        id: this.idContenedor + '_id_proyecto',
                                        qtip: 'Proyecto o aplicación del activo fijo, se utliza para cargar los gastos  de depreciación (Determinar los centro de costos)',
                                        fieldLabel: 'Proyecto / Aplicación',
                                        allowBlank: false,
                                        emptyText: 'Proyecto...',
                                        store: new Ext.data.JsonStore({
                                            url: '../../sis_parametros/control/Proyecto/ListarProyecto',
                                            id: 'id_proyecto',
                                            root: 'datos',
                                            sortInfo: {
                                                field: 'codigo_proyecto',
                                                direction: 'ASC'
                                            },
                                            totalProperty: 'total',
                                            fields: ['id_proyecto', 'codigo_proyecto', 'nombre_proyecto'],
                                            // turn on remote sorting
                                            remoteSort: true,
                                            baseParams: {par_filtro: 'codigo_proyecto#nombre_proyecto'}
                                        }),
                                        valueField: 'id_proyecto',
                                        displayField: 'codigo_proyecto',
                                        gdisplayField: 'desc_proyecto',//mapea al store del grid
                                        tpl: '<tpl for="."><div class="x-combo-list-item"><p>{codigo_proyecto}</p><p>{nombre_proyecto}</p> </div></tpl>',
                                        hiddenName: 'id_proyecto',
                                        forceSelection: true,
                                        typeAhead: true,
                                        triggerAction: 'all',
                                        lazyRender: true,
                                        mode: 'remote',
                                        pageSize: 10,
                                        queryDelay: 1000,
                                        minChars: 2
                                    }

                                ]
                            }


                        ]


                    }]
                }
                ],
                //fileUpload: me.fileUpload,
                padding: this.paddingForm,
                bodyStyle: this.bodyStyleForm,
                border: this.borderForm,
                frame: this.frameForm,
                autoScroll: false,
                autoDestroy: true,
                //autoScroll: true,
                region: 'center'
            });

            this.afWindow = new Ext.Window({
                width: 1200,
                height: 620,
                modal: true,
                closeAction: 'hide',
                labelAlign: 'top',
                title: 'Renovación Activos Fijos',
                bodyStyle: 'padding:5px',
                layout: 'border',
                items: [{
                    region: 'west',
                    split: true,
                    width: 200,
                    minWidth: 150,
                    maxWidth: 250,
                    items: [{
                        id: 'img-detail-panel',
                        region: 'north'
                    }, {
                        id: 'img-qr-panel' + this.idContenedor,
                        region: 'center'
                    }]
                }, this.form],
                buttons: [{
                    text: 'Reporte Histórico',
                    tooltip: '<b>Reporte Histórico de Activos Intangibles</b>',
                    handler: this.onButtonHistorico,
                    scope: this
                },{
                    text: 'Guardar',
                    handler: this.onSubmit,
                    scope: this
                }, {
                    text: 'Declinar',
                    handler: function () {
                        this.afWindow.hide();
                    },
                    scope: this
                }]
            });

            //Events
            //Clasificación
            Ext.getCmp(this.idContenedor + '_id_clasificacion').on('exception', this.conexionFailure, this);

            Ext.getCmp(this.idContenedor + '_id_clasificacion').on('select', function (cmp, rec, index) {
                if (rec.data.depreciable == 'si') {
                    Ext.getCmp(this.idContenedor + '_vida_util_original').setValue(rec.data.vida_util);
                    Ext.getCmp(this.idContenedor + '_vida_util_real_af').setValue(rec.data.vida_util);
                    Ext.getCmp(this.idContenedor + '_monto_rescate').setValue(rec.data.monto_residual);
                    //Convierte a años
                    Ext.getCmp(this.idContenedor + '_vida_util_original_anios').setValue(this.convertirVidaUtil(rec.data.vida_util));
                } else {
                    Ext.getCmp(this.idContenedor + '_vida_util_original').allowBlank = true;
                    Ext.getCmp(this.idContenedor + '_vida_util_original_anios').allowBlank = true;
                    Ext.getCmp(this.idContenedor + '_vida_util_original').setValue('')
                    Ext.getCmp(this.idContenedor + '_vida_util_original_anios').setValue('')
                }
                this.actualizarSegunClasificacion(rec.data.tipo_activo, rec.data.depreciable);

            }, this);
            //Vida util
            Ext.getCmp(this.idContenedor + '_vida_util_original').on('blur', function (cmp, rec, index) {
                Ext.getCmp(this.idContenedor + '_vida_util_real_af').setValue(Ext.getCmp(this.idContenedor + '_vida_util_original').getValue());
                //Convierte a años
                Ext.getCmp(this.idContenedor + '_vida_util_original_anios').setValue(this.convertirVidaUtil(Ext.getCmp(this.idContenedor + '_vida_util_original').getValue()));

            }, this);
            //Vida util años
            Ext.getCmp(this.idContenedor + '_vida_util_original_anios').on('blur', function (cmp, rec, index) {
                //Convertir a meses
                Ext.getCmp(this.idContenedor + '_vida_util_original').setValue(this.convertirVidaUtil(Ext.getCmp(this.idContenedor + '_vida_util_original_anios').getValue(), 'anios'));
                Ext.getCmp(this.idContenedor + '_vida_util_real_af').setValue(Ext.getCmp(this.idContenedor + '_vida_util_original').getValue());
            }, this);

            //Denominación
            Ext.getCmp(this.idContenedor + '_denominacion').on('blur', function (cmp) {
                if (Ext.getCmp(this.idContenedor + '_descripcion').getValue() == '') {
                    Ext.getCmp(this.idContenedor + '_descripcion').setValue(Ext.getCmp(this.idContenedor + '_denominacion').getValue());
                }
            }, this);


            //Depto
            Ext.getCmp(this.idContenedor + '_id_depto').on('select', function (cmp, rec, index) {
                var obj = {
                    start: 0,
                    limit: 50,
                    sort: 'claf.nombre',
                    dir: 'ASC',
                    id_depto: rec.data.id_depto
                };
                Ext.getCmp(this.idContenedor + '_id_deposito').reset();
                Ext.getCmp(this.idContenedor + '_id_deposito').modificado = true;
                Ext.getCmp(this.idContenedor + '_id_deposito').store.baseParams.id_depto = rec.data.id_depto;


            }, this);
            //Deposito
            Ext.getCmp(this.idContenedor + '_id_deposito').on('select', function (cmp, rec, index) {
                //Setear oficina
                rec1 = new Ext.data.Record({
                    nombre: rec.data.oficina,
                    'id_oficina': rec.data.id_oficina
                }, rec.data.id_oficina);
                Ext.getCmp(this.idContenedor + '_id_oficina').store.add(rec1);
                Ext.getCmp(this.idContenedor + '_id_oficina').store.commitChanges();
                Ext.getCmp(this.idContenedor + '_id_oficina').modificado = true;
                Ext.getCmp(this.idContenedor + '_id_oficina').setValue(rec.data.id_oficina);
                //Setear responsable
                rec1 = new Ext.data.Record({
                    nombre: rec.data.funcionario,
                    'id_funcionario': rec.data.id_funcionario
                }, rec.data.id_funcionario);
                Ext.getCmp(this.idContenedor + '_id_funcionario').store.add(rec1);
                Ext.getCmp(this.idContenedor + '_id_funcionario').store.commitChanges();
                Ext.getCmp(this.idContenedor + '_id_funcionario').modificado = true;
                Ext.getCmp(this.idContenedor + '_id_funcionario').setValue(rec.data.id_funcionario);
                //Setear Ubicación
                Ext.getCmp(this.idContenedor + '_ubicacion').setValue(rec.data.ubicacion);
            }, this);
            //Monto Compra
            Ext.getCmp(this.idContenedor + '_monto_compra_orig').on('blur', function (a, b, c) {
                Ext.getCmp(this.idContenedor + '_monto_vigente_real_af').setValue(Ext.getCmp(this.idContenedor + '_monto_compra_orig').getValue());
                Ext.getCmp(this.idContenedor + '_depreciacion_acum_real_af').setValue('0.00');
                Ext.getCmp(this.idContenedor + '_depreciacion_per_real_af').setValue('0.00');
                Ext.getCmp(this.idContenedor + '_depreciacion').setValue('0.00');
            }, this);

            //Load data for pestaña de historicos
            Ext.getCmp('af_filter_hist').on('activate', function () {
                Ext.getCmp('af_filter_af_list').store.load();
            }, this);

            Ext.getCmp('af_filter_af_list').addListener('selectionChange', function (cmp, cls) {
                var data = cmp.store.data.items[cmp.last].data;
                this.filtrarGrid({
                    id_filter_panel: data.id_activo_fijo,
                    col_filter_panel: 'id_activo_fijo'
                });
            }, this);


            //activos fijos historicos
            // Ext.getCmp(this.idContenedor + 'af_pestaña_hist').on('select', function (cmp, rec, index) {
            // // af_filter_af_list af_pestaña_hist
            //     // Setear
            //     rec1 = new Ext.data.Record({
            //         nombre: rec.data.id_activo_fijo_hist,
            //         'id_activo_fijo_hist': rec.data.id_activo_fijo_hist
            //     }, rec.data.id_activo_fijo_hist);
            //     Ext.getCmp(this.idContenedor + '_id_activo_fijo').store.add(rec1);
            //     Ext.getCmp(this.idContenedor + '_id_activo_fijo').store.commitChanges();
            //     Ext.getCmp(this.idContenedor + '_id_activo_fijo').modificado = true;
            //     Ext.getCmp(this.idContenedor + '_id_activo_fijo').setValue(rec.data.id_activo_fijo);
            //
            // }, this);
            Ext.getCmp(this.idContenedor + 'af_pestaña_hist').on('select', function (cmp, rec, index) {
                //console.log('';

                var obj = {
                    start: 0,
                    limit: 50,
                    sort: 'afh.codigo',
                    dir: 'ASC',
                    id_activo_fijo: rec.data.id_activo_fijo
                };
                Ext.getCmp(this.idContenedor + '_id_activo_fijo').reset();
                //Ext.getCmp(this.id_activo_fijo.store.baseParams.id_activo_fijo_hist).modificado = true;
                Ext.getCmp(this.idContenedor + '_id_activo_fijo').modificado = true;
                Ext.getCmp(this.idContenedor + '_id_activo_fijo').store.baseParams.id_activo_fijo = rec.data.id_activo_fijo;

            }, this);

            //}
        },
        onButtonHistorico:function(){
            var rec=this.sm.getSelected();
            Ext.Ajax.request({
                url:'../../sis_kactivos_fijos/control/ActivoFijo/reporteHistoricoAF',
                params:{'id_activo_fijo':rec.data.id_activo_fijo,'estado':rec.data.estado},
                success: this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        }

    })
</script>
