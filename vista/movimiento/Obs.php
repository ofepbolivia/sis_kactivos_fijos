<?php
header("content-type: text/javascript; charset=UTF-8");
?>
<script src="../../../lib/ClienteRestJS/jquery-3.0.0.min.js"></script>
<script>
    var url = 'https://localhost:9000/';
    var endpoint_get_token = 'api/token/connected';
    var endpoint_post_pin = 'api/token/data';
    var endpoint_post_firmar_pdf = 'api/token/firmar_pdf';
    var slot = '';
    Phx.vista.Obs = Ext.extend(Phx.gridInterfaz, {
            constructor: function (config) {
                this.maestro = config.maestro;
                if (config.hasOwnProperty('idContenedorPadre')) {
                    this.paginaMaestro = Phx.CP.getPagina(config.idContenedorPadre);
                } else {
                    this.paginaMaestro = undefined;
                }

                Phx.vista.Obs.superclass.constructor.call(this, config);
                this.init();

                this.on('closepanel', function () {
                    this.paginaMaestro.reload();
                }, this);
                this.store.baseParams = {
                    todos: 0,
                    cod_movimiento: '%',
                    id_movimiento: '',
                    tipo_interfaz: config.tipo_interfaz
                };
                this.load({params: {start: 0, limit: this.tam_pag}});

                this.addButton('btnPdfFirmaDigital', {
                    text: 'Documento a Firmar',
                    iconCls: 'bpdf',
                    disabled: true,
                    handler: this.onButtonATDPdf,
                    tooltip: '<b>Documento a Firmar</b>'
                });

                this.addButton('btnFirmaDigital', {
                    text: 'Firmar',
                    iconCls: 'bfirma-digital',
                    disabled: false,
                    handler: this.onButtonFirmar,
                    tooltip: '<b>Firmar documento</b>'
                });
            },

            Atributos: [
                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_movimiento'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'cod_movimiento'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        name: 'id_cat_movimiento',
                        fieldLabel: 'Proceso',
                        tinit: false,
                        allowBlank: false,
                        origen: 'CATALOGO',
                        gdisplayField: 'movimiento',
                        hiddenName: 'id_cat_movimiento',
                        gwidth: 55,
                        baseParams: {
                            cod_subsistema: 'KAF',
                            catalogo_tipo: 'tmovimiento__id_cat_movimiento'
                        },
                        renderer: function (value, p, record) {
                            return record.data.movimiento;

                        },
                        valueField: 'id_catalogo'
                    },
                    type: 'ComboRec',
                    id_grupo: 0,
                    filters: {pfiltro: 'cat.descripcion', type: 'string'},
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'id_movimiento_motivo',
                        fieldLabel: 'Motivo',
                        allowBlank: true,
                        emptyText: 'Motivo...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_kactivos_fijos/control/MovimientoMotivo/listarMovimientoMotivo',
                            id: 'id_movimiento_motivo',
                            root: 'datos',
                            sortInfo: {
                                field: 'motivo',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_movimiento_motivo', 'motivo'],
                            remoteSort: true,
                            baseParams: {
                                par_filtro: 'motivo',
                                modulo: 'KAF'
                            }
                        }),
                        valueField: 'id_movimiento_motivo',
                        displayField: 'motivo',
                        gdisplayField: 'motivo',
                        hiddenName: 'id_movimiento_motivo',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 10,
                        queryDelay: 1000,
                        anchor: '95%',
                        gwidth: 200,
                        minChars: 2,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['movimiento_motivo']);
                        },
                        hidden: true
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    filters: {
                        pfiltro: 'mmov.motivo',
                        type: 'string'
                    },
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'nro_documento',
                        fieldLabel: 'Nro Documento',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 90
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'mov.nro_documento', type: 'string'},
                    id_grupo: 0,
                    grid: false,
                    form: true,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'tipo_documento',
                        fieldLabel: 'Tipo Documento',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 90,
                        triggerAction: 'all',

                        mode: 'local',
                        store: new Ext.data.ArrayStore({
                            fields: ['tipo', 'valor'],
                            data: [['tipo_documento_I', 'Nota Interna'],
                                ['tipo_documento_II', 'Informe'],
                                ['tipo_documento_III', 'Observacion Auditoria'],
                                ['tipo_documento_IV', 'Otros']]
                        }),
                        valueField: 'tipo',
                        displayField: 'valor'
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    grid: false,
                    form: true,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'num_tramite',
                        fieldLabel: 'Num.Trámite/Fecha',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 250,
                        maxLength: 200,
                        disabled: true,
                        renderer: function (value, p, record) {
                            var fecha_finalizacion = '';
                            if (record.data['fecha_finalizacion'] != null) {
                                fecha_finalizacion = record.data['fecha_finalizacion'].dateFormat('d/m/Y H:i:s');
                            }
                            if (record.data.tipo_movimiento == 'Transito') {
                                return '<tpl style="background-color:#FA5E5E; margin-top:0px; position:absolute; width:250px; height:45px; float:left;"><p><b>Fecha: </b> ' + record.data['fecha_mov'].dateFormat('d/m/Y') + '</p><p><b>Fecha finalizacion Mov. : </b> <font color="blue">' + fecha_finalizacion + '</font></p><p><b>Tramite: </b> <font color="blue">' + record.data['num_tramite'] + '</font></p><p><b>Estado: </b>' + record.data['estado'] + '</p></div></tpl>';
                            } else {
                                return '<tpl><p><b>Fecha: </b> ' + record.data['fecha_mov'].dateFormat('d/m/Y') + '</p><p><b>Fecha finalizacion Mov. : </b> <font color="blue">' + fecha_finalizacion + '</font></p><p><b>Tramite: </b> <font color="blue">' + record.data['num_tramite'] + '</font></p><p><b>Estado: </b>' + record.data['estado'] + '</p></div></tpl>';
                            }
                        }
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'mov.num_tramite', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'estado',
                        fieldLabel: 'Estado',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 90,
                        maxLength: 15,
                        disabled: true,
                        renderer: function (value, p, record) {
                            var result;
                            //if(value == "Borrador") {
                            result = "<div style='text-align:center'><img src = '../../../lib/imagenes/" + record.data.icono_estado + "' align='center' width='18' height='18' title='" + record.data.estado + "'/><br><u>" + record.data.estado + "</u></div>";
                            //}
                            return result;
                        }
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'mov.estado', type: 'string'},
                    id_grupo: 0,
                    grid: false,
                    form: true,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'tipo_drepeciacion',
                        fieldLabel: 'Tipo Depreciación',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 90,
                        triggerAction: 'all',

                        mode: 'local',
                        store: new Ext.data.ArrayStore({
                            fields: ['tipo', 'valor'],
                            data: [['deprec_ministerio', 'Depreciación Ministerio'],
                                ['deprec_impuesto', 'Depreciación Impuestos']]
                        }),
                        valueField: 'tipo',
                        displayField: 'valor'
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    grid: false,
                    form: true
                },
                {
                    config: {
                        name: 'fecha_mov',
                        fieldLabel: 'Fecha',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 70,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'mov.fecha_mov', type: 'date'},
                    id_grupo: 0,
                    grid: false,
                    form: true
                },
                {
                    config: {
                        name: 'id_depto',
                        fieldLabel: 'Dpto.',
                        allowBlank: false,
                        emptyText: 'Departamento...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_parametros/control/Depto/listarDepto',
                            id: 'id_depto',
                            root: 'datos',
                            sortInfo: {
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_depto', 'nombre', 'codigo'],
                            remoteSort: true,
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
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p>Nombre: {nombre}</p><p>Código: {codigo}</p></div></tpl>',
                        hiddenName: 'id_depto',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 10,
                        queryDelay: 1000,
                        anchor: '95%',
                        gwidth: 250,
                        minChars: 2,
                        renderer: function (value, p, record) {
                            //return String.format('{0}', record.data['depto']);
                            /*	if(record.data.tipo_movimiento=='Transito'){
                                        //return String.format('<div ><font weight="bold"; color="#ffffff";>{0}</font></div>',value);
                                        return '<tpl style="background-color:#FA5E5E; margin-top:0px; position:absolute; width:200px; height:45px; float:left;"><p><b>Fecha: </b> '+record.data['fecha_mov'].dateFormat('d/m/Y')+'</p><p><b>Tramite: </b> <font color="blue">'+record.data['num_tramite']+'</font></p><p><b>Estado: </b>'+record.data['estado']+'</p></div></tpl>';
                                 }*/
                            var desc;
                            var depre = '';
                            if (record.data['cod_movimiento'] == 'deprec' || record.data['cod_movimiento'] == 'actua') {
                                depre = '<p><b>Tipo Deprec/Act.</b> <font color="blue">' + record.data['tipo_drepeciacion'] + '</font></p>'
                            }
                            ;
                            if (record.data['cod_movimiento'] == 'transf' && record.data['tipo_movimiento'] == 'Transito') {
                                desc = '<tpl for="."><div style="background-color:#FA5E5E; margin-top:0px; position:absolute; width:300px; height:45px; float:left;"><p><b>Dpto.:</b> ' + record.data['depto'] + '</p><p><b>De:</b> <font color="blue">' + record.data['desc_funcionario2'] + '</font></p><p><b>A:</b> <u><font color="green">' + record.data['funcionario_dest'] + '</u></font></p></div></tpl>';

                            } else if (record.data['cod_movimiento'] == 'transf') {
                                desc = '<tpl for="."><div class="x-combo-list-item"><p><b>Dpto.:</b> ' + record.data['depto'] + '</p><p><b>De:</b> <font color="blue">' + record.data['desc_funcionario2'] + '</font></p><p><b>A:</b> <u><font color="green">' + record.data['funcionario_dest'] + '</u></font></p></div></tpl>';
                            } else if (record.data['cod_movimiento'] == 'asig' && record.data['tipo_movimiento'] == 'Transito') {
                                desc = '<tpl for="."><div style="background-color:#FA5E5E; margin-top:0px; position:absolute; width:300px; height:45px; float:left;"	><p><b>Dpto.:</b> ' + record.data['depto'] + '</p><p><b>A:</b> <u><font color="green">' + record.data['desc_funcionario2'] + '</u></font></p></div></tpl>';
                            } else if (record.data['cod_movimiento'] == 'asig') {
                                desc = '<tpl for="."><div class="x-combo-list-item"><p><b>Dpto.:</b> ' + record.data['depto'] + '</p><p><b>A:</b> <u><font color="green">' + record.data['desc_funcionario2'] + '</u></font></p></div></tpl>';
                            } else if (record.data['tipo_movimiento'] == 'Transito') {
                                desc = '<tpl for="."><div style="background-color:#FA5E5E; margin-top:0px; position:absolute; width:300px; height:45px; float:left;"><p><b>Dpto.:</b> ' + record.data['depto'] + '</p></div></tpl>';
                            } else {
                                desc = '<tpl for="."><div class="x-combo-list-item" ><p><b>Dpto.:</b> ' + record.data['depto'] + '</p>' + depre + '</div></tpl>';
                            }
                            return desc;
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    filters: {
                        pfiltro: 'dep.nombre',
                        type: 'string'
                    },
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'id_deposito',
                        fieldLabel: 'Deposito',
                        allowBlank: false,
                        emptyText: 'Elija un deposito...',
                        hidden: true,
                        store: new Ext.data.JsonStore({
                            url: '../../sis_kactivos_fijos/control/Deposito/listarDeposito',
                            id: 'id_deposito',
                            root: 'datos',
                            fields: ['id_deposito', 'codigo', 'nombre'],
                            totalProperty: 'total',
                            sortInfo: {
                                field: 'codigo',
                                direction: 'ASC'
                            },
                            baseParams: {par_filtro: 'dep.codigo#dep.nombre'}

                        }),
                        valueField: 'id_deposito',
                        displayField: 'nombre',
                        gdisplayField: 'deposito',
                        hiddenName: 'id_deposito',
                        forceSelection: false,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        anchor: '95%',
                        gwidth: 150,
                        minChars: 2,
                        disabled: true,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['deposito']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    filters: {pfiltro: 'depo.nombre', type: 'string'},
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'glosa',
                        fieldLabel: 'Glosa',
                        allowBlank: false,
                        anchor: '95%',
                        gwidth: 350,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_movimiento == 'Transito') {
                                return String.format('<div style="background-color:#FA5E5E; margin-top:0px; position:absolute; width:350px; height:45px; float:left;"><font>{0}</font></div>', value);
                            } else {
                                return String.format('<div><font>{0}</font></div>', value);
                            }
                        },
                        maxLength: 200
                    },
                    type: 'TextArea',
                    filters: {pfiltro: 'mov.glosa', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    form: true,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'resp_wf',
                        fieldLabel: 'Flujo con',
                        allowBlank: false,
                        anchor: '95%',
                        gwidth: 150,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_movimiento == 'Transito') {
                                return String.format('<div style="background-color:#FA5E5E; margin-top:0px; position:absolute; width:300px; height:45px; float:left;"><font>{0}</font></div>', value);
                            } else {
                                return String.format('<div><font>{0}</font></div>', value);
                            }
                        },
                        maxLength: 200
                    },
                    type: 'TextArea',
                    filters: {pfiltro: 'funwf.desc_funcionario2', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'fecha_hasta',
                        fieldLabel: 'Fecha Hasta',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        hidden: true,
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'mov.fecha_hasta', type: 'date'},
                    id_grupo: 0,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_proceso_wf'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_proceso_wf_doc'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_estado_wf'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        name: 'id_funcionario',
                        hiddenName: 'id_funcionario',
                        origen: 'FUNCIONARIO',
                        fieldLabel: 'Funcionario',
                        allowBlank: true,
                        gwidth: 200,
                        valueField: 'id_funcionario',
                        gdisplayField: 'desc_funcionario2',
                        baseParams: {fecha: new Date()},
                        hidden: true,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['desc_funcionario2']);
                        },
                    },
                    type: 'ComboRec',//ComboRec
                    id_grupo: 0,
                    filters: {pfiltro: 'fun.desc_funcionario2', type: 'string'},
                    grid: true,
                    form: true,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'id_persona',
                        hiddenName: 'id_persona',
                        origen: 'PERSONA',
                        fieldLabel: '¿Custodio?',
                        allowBlank: true,
                        gwidth: 200,
                        valueField: 'id_persona',
                        gdisplayField: 'custodio',
                        hidden: true,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['custodio']);
                        },
                    },
                    type: 'ComboRec',//ComboRec
                    id_grupo: 0,
                    filters: {pfiltro: 'per.nombre_completo2', type: 'string'},
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'id_oficina',
                        fieldLabel: 'Oficina',
                        allowBlank: true,
                        emptyText: 'Elija una opción...',
                        hidden: true,
                        store: new Ext.data.JsonStore({
                            url: '../../sis_organigrama/control/Oficina/listarOficina',
                            id: 'id_oficina',
                            root: 'datos',
                            fields: ['id_oficina', 'codigo', 'nombre'],
                            totalProperty: 'total',
                            sortInfo: {
                                field: 'codigo',
                                direction: 'ASC'
                            },
                            baseParams: {par_filtro: 'ofi.codigo#ofi.nombre'}
                        }),
                        valueField: 'id_oficina',
                        displayField: 'nombre',
                        gdisplayField: 'oficina',
                        hiddenName: 'id_oficina',
                        forceSelection: false,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        anchor: '95%',
                        gwidth: 150,
                        minChars: 2,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['oficina']);
                        },
                        listeners: {
                            select: function (combo, records) {
                                //fRnk: set dirección
                                Ext.getCmp('direccion').setValue(records.json.direccion);
                            }
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    filters: {pfiltro: 'ofi.nombre', type: 'string'},
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'direccion',
                        id: 'direccion',
                        fieldLabel: 'Dirección',
                        allowBlank: true,
                        anchor: '95%',
                        gwidth: 100,
                        maxLength: 500,
                        hidden: true,
                        disabled: true
                    },
                    type: 'TextArea',
                    filters: {pfiltro: 'mov.direccion', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'codigo',
                        fieldLabel: 'Codigo',
                        allowBlank: true,
                        gwidth: 100,
                        hidden: true
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'mov.codigo', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    form: true
                },

                {
                    config: {
                        name: 'id_depto_dest',
                        fieldLabel: 'Depto. Destino',
                        allowBlank: true,
                        emptyText: 'Departamento...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_parametros/control/Depto/listarDepto',
                            id: 'id_depto',
                            root: 'datos',
                            sortInfo: {
                                field: 'nombre',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_depto', 'nombre', 'codigo'],
                            remoteSort: true,
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
                        gdisplayField: 'depto_dest',
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p>Nombre: {nombre}</p><p>Código: {codigo}</p></div></tpl>',
                        hiddenName: 'id_depto_dest',
                        forceSelection: true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 10,
                        queryDelay: 1000,
                        anchor: '95%',
                        gwidth: 200,
                        minChars: 2,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['depto_dest']);
                        },
                        hidden: true
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    filters: {
                        pfiltro: 'depdest.nombre',
                        type: 'string'
                    },
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'id_deposito_dest',
                        fieldLabel: 'Deposito Destino',
                        allowBlank: true,
                        emptyText: 'Elija una opción...',
                        hidden: true,
                        store: new Ext.data.JsonStore({
                            url: '../../sis_kactivos_fijos/control/Deposito/listarDeposito',
                            id: 'id_deposito',
                            root: 'datos',
                            fields: ['id_deposito', 'codigo', 'nombre'],
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
                                id_depto: 0
                            }
                        }),
                        valueField: 'id_deposito',
                        displayField: 'nombre',
                        gdisplayField: 'deposito_dest',
                        hiddenName: 'id_deposito_dest',
                        forceSelection: false,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 15,
                        queryDelay: 1000,
                        anchor: '95%',
                        gwidth: 150,
                        minChars: 2,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['deposito_dest']);
                        }
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    filters: {pfiltro: 'depo.nombre', type: 'string'},
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'id_funcionario_dest',
                        hiddenName: 'id_funcionario_dest',
                        origen: 'FUNCIONARIO',
                        fieldLabel: 'Funcionario Dest.',
                        allowBlank: true,
                        gwidth: 200,
                        valueField: 'id_funcionario',
                        gdisplayField: 'funcionario_dest',
                        baseParams: {fecha: new Date()},
                        hidden: true,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['desc_funcionario2']);
                        },
                    },
                    type: 'ComboRec',//ComboRec
                    id_grupo: 0,
                    filters: {pfiltro: 'fundest.desc_funcionario2', type: 'string'},
                    grid: true,
                    form: true,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'id_int_comprobante',
                        fieldLabel: 'Cbte',
                        allowBlank: true,
                        hidden: true,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_movimiento == 'Transito') {
                                return String.format('<div style="background-color:#FA5E5E; margin-top:0px; position:absolute; width:150px; height:45px; float:left;"><font>{0}</font></div>', value);
                            } else {
                                return value == null ? '' : String.format('<div><font>{0}</font></div>', value);
                            }
                        },
                        gwidth: 40
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'mov.id_int_comprobante', type: 'numeric'},
                    id_grupo: 0,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'id_int_comprobante_aitb',
                        fieldLabel: 'Cbte AITB',
                        allowBlank: true,
                        hidden: true,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_movimiento == 'Transito') {
                                return String.format('<div style="background-color:#FA5E5E; margin-top:0px; position:absolute; width:50px; height:45px; float:left;"><font>{0}</font></div>', value);
                            } else {
                                return value == null ? '' : String.format('<div><font>{0}</font></div>', value);
                            }
                        },
                        gwidth: 70
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'mov.id_int_comprobante_aitb', type: 'numeric'},
                    id_grupo: 0,
                    grid: false,
                    form: true
                },
                {
                    config: {
                        name: 'prestamo',
                        fieldLabel: 'Préstamo',
                        anchor: '95%',
                        tinit: false,
                        allowBlank: true,
                        origen: 'CATALOGO',
                        gdisplayField: 'prestamo',
                        hiddenName: 'prestamo',
                        gwidth: 55,
                        baseParams: {
                            cod_subsistema: 'KAF',
                            catalogo_tipo: 'tclasificacion_variable__obligatorio'
                        },
                        valueField: 'codigo',
                        hidden: true
                    },
                    type: 'ComboRec',
                    id_grupo: 0,
                    filters: {pfiltro: 'mov.prestamo', type: 'string'},
                    grid: false,
                    form: true
                },
                {
                    config: {
                        name: 'fecha_dev_prestamo',
                        fieldLabel: 'Fecha Dev.Préstamo',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 70,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'mov.fecha_dev_prestamo', type: 'date'},
                    id_grupo: 0,
                    grid: false,
                    form: true
                },
                {
                    config: {
                        name: 'tipo_asig',
                        fieldLabel: 'Tipo',
                        anchor: '95%',
                        tinit: false,
                        allowBlank: true,
                        origen: 'CATALOGO',
                        gdisplayField: 'tipo_asig',
                        hiddenName: 'tipo_asig',
                        gwidth: 55,
                        baseParams: {
                            cod_subsistema: 'KAF',
                            catalogo_tipo: 'tmovimiento__tipo_asig'
                        },
                        valueField: 'codigo',
                        hidden: true
                    },
                    type: 'ComboRec',
                    id_grupo: 0,
                    grid: false,
                    form: true
                },
                {
                    config: {
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        allowBlank: true,
                        hidden: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 10
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'mov.estado_reg', type: 'string'},
                    id_grupo: 0,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'id_usuario_ai',
                        fieldLabel: '',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'mov.id_usuario_ai', type: 'numeric'},
                    id_grupo: 0,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'usr_reg',
                        fieldLabel: 'Creado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'usu1.cuenta', type: 'string'},
                    id_grupo: 0,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'fecha_reg',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            if (record.data.tipo_movimiento == 'Transito') {
                                return String.format('<div style="background-color:#FA5E5E; margin-top:0px; position:relative; width:100px; height:45px; float:left;"><font>{0}</font></div>', value);
                            } else {
                                return value ? value.dateFormat('d/m/Y H:i:s') : ''
                            }

                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'mov.fecha_reg', type: 'date'},
                    id_grupo: 0,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'usuario_ai',
                        fieldLabel: 'Funcionaro AI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 300
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'mov.usuario_ai', type: 'string'},
                    id_grupo: 0,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'fecha_mod',
                        fieldLabel: 'Fecha Modif.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y H:i:s') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'mov.fecha_mod', type: 'date'},
                    id_grupo: 0,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'usr_mod',
                        fieldLabel: 'Modificado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'usu2.cuenta', type: 'string'},
                    id_grupo: 0,
                    grid: false,
                    form: false
                },
                {
                    config: {
                        name: 'firmado',
                        fieldLabel: 'Firmado',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                    },
                    type: 'Field',
                    filters: {pfiltro: 'usu2.firmado', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    form: false
                }
            ],
            tam_pag: 50,
            title: 'Pendientes de Firma Digital',
            ActList: '../../sis_kactivos_fijos/control/Movimiento/listarMovimientoPendienteFirma',
            id_store: 'id_obs',
            fields: [
                {name: 'id_movimiento', type: 'numeric'},
                {name: 'direccion', type: 'string'},
                {name: 'fecha_hasta', type: 'date', dateFormat: 'Y-m-d'},
                {name: 'id_cat_movimiento', type: 'numeric'},
                {name: 'fecha_mov', type: 'date', dateFormat: 'Y-m-d'},
                {name: 'id_depto', type: 'numeric'},
                {name: 'id_proceso_wf', type: 'numeric'},
                {name: 'id_estado_wf', type: 'numeric'},
                {name: 'glosa', type: 'string'},
                {name: 'id_funcionario', type: 'numeric'},
                {name: 'estado', type: 'string'},
                {name: 'id_oficina', type: 'numeric'},
                {name: 'estado_reg', type: 'string'},
                {name: 'num_tramite', type: 'string'},
                {name: 'id_usuario_ai', type: 'numeric'},
                {name: 'id_usuario_reg', type: 'numeric'},
                {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'usuario_ai', type: 'string'},
                {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_mod', type: 'numeric'},
                {name: 'usr_reg', type: 'string'},
                {name: 'usr_mod', type: 'string'},
                {name: 'movimiento', type: 'string'},
                {name: 'cod_movimiento', type: 'string'},
                {name: 'icono', type: 'string'},
                {name: 'depto', type: 'string'},
                {name: 'cod_depto', type: 'string'},
                {name: 'id_responsable_depto', type: 'numeric'},
                {name: 'id_persona', type: 'numeric'},
                {name: 'responsable_depto', type: 'string'},
                {name: 'custodio', type: 'string'},
                {name: 'icono_estado', type: 'string'},
                {name: 'codigo', type: 'string'},
                {name: 'id_deposito', type: 'numeric'},
                {name: 'id_depto_dest', type: 'numeric'},
                {name: 'id_deposito_dest', type: 'numeric'},
                {name: 'id_funcionario_dest', type: 'numeric'},
                {name: 'id_movimiento_motivo', type: 'numeric'},
                {name: 'deposito', type: 'string'},
                {name: 'depto_dest', type: 'string'},
                {name: 'deposito_dest', type: 'string'},
                {name: 'funcionario_dest', type: 'string'},
                {name: 'motivo', type: 'string'},
                {name: 'desc_funcionario2', type: 'string'},
                'id_int_comprobante',
                'id_int_comprobante_aitb',
                {name: 'resp_wf', type: 'string'},
                {name: 'prestamo', type: 'string'},
                {name: 'fecha_dev_prestamo', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'firmado', type: 'string'},
                {name: 'id_proceso_wf_doc', type: 'numeric'},
                {name: 'nro_documento', type: 'string'},
                {name: 'tipo_documento', type: 'string'},
                {name: 'codigo_mov_motivo', type: 'string'},
                {name: 'fecha_finalizacion', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'tipo_drepeciacion', type: 'string'},
                {name: 'firmado', type: 'string'},
                {name: 'nombre_archivo', type: 'string'},
                {name: 'firma_digital', type: 'string'},
                {name: 'ci_login', type: 'string'}
            ],

            onButtonATDPdf: function () {
                var rec = this.sm.getSelected();
                Phx.CP.loadingShow();
                if (rec.data.cod_movimiento == 'alta') {
                    Ext.Ajax.request({
                        url: '../../sis_kactivos_fijos/control/Movimiento/generarReporteMovimiento',
                        params: {
                            'id_movimiento': rec.data.id_movimiento,
                            'nombre_archivo': rec.data.nombre_archivo,
                            'firma_digital': rec.data.firma_digital
                        },
                        success: this.successExport,
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });
                } else {
                    Ext.Ajax.request({
                        url: '../../sis_kactivos_fijos/control/Movimiento/generarReporteMovimientoUpdate',
                        params: {
                            'id_movimiento': rec.data.id_movimiento,
                            'num_tramite': rec.data.num_tramite,
                            'nombre_archivo': rec.data.nombre_archivo,
                            'firma_digital': rec.data.firma_digital
                        },
                        success: this.successExport,
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });
                }
            },

            onButtonFirmar: function () {
                /*Ext.Msg.prompt('Firmar Documento', 'Introduzca su PIN:', function (btn, text) {
                    if (btn == 'ok') {
                        Ext.Msg.alert(';)', ';) ' + text);
                    }
                });*/
                var mef = this;
                slot = '';
                Phx.CP.loadingShow();
                $.ajax({
                    type: 'GET',
                    url: url + endpoint_get_token,
                    contentType: 'application/json',
                    success: function (data) {
                        Phx.CP.loadingHide();
                        if (data !== null || data != '') {
                            if (data.finalizado === true && data.datos.tokens.length > 0) {
                                slot = data.datos.tokens[0].slot;
                                var simple = new Ext.FormPanel({
                                    labelWidth: 100,
                                    frame: true,
                                    bodyStyle: 'padding:5px 5px 0;',
                                    width: 330,
                                    height: 100,
                                    defaultType: 'textfield',
                                    items: [
                                        new Ext.form.TextField({
                                            name: 'pin',
                                            fieldLabel: 'Ingrese el PIN de su Token',
                                            allowBlank: false,
                                            anchor: '90%',
                                            maxLength: 20,
                                            inputType: 'password'
                                        }),
                                    ]
                                });
                                var win = new Ext.Window({
                                    title: 'Firmar Documento',
                                    width: 350,
                                    height: 120,
                                    modal: true,
                                    plain: true,
                                    items: simple,
                                    buttons: [{
                                        text: '<i class="fa fa-check"></i> Enviar',
                                        scope: mef,
                                        handler: function () {
                                            mef.guardarFirma(win, simple);
                                        }
                                    }, {
                                        text: '<i class="fa fa-times"></i> Declinar',
                                        handler: function () {
                                            win.hide();
                                        }
                                    }]
                                });
                                win.show();
                            } else {
                                alert('No se ha detectado su TOKEN, conéctelo y vuelva a intentarlo nuevamente.');
                            }
                        } else {
                            alert('No se ha detectado su TOKEN, conéctelo, inicie Jacobitus Total y vuelva a intentarlo nuevamente.');
                        }
                    }
                }).fail(function (jqXHR, textStatus) {
                    if (jqXHR.status === 0) {
                        alert('No se encontró Jacobitus Total, verifique que esté instalado e iniciado.');
                    } else if (jqXHR.status == 404) {
                        alert('Página solicitada no encontrada [404]');
                    } else if (jqXHR.status == 500) {
                        alert('Error Interno del Servidor [500].');
                    } else if (textStatus === 'parsererror') {
                        alert('Requested JSON parse failed.');
                    } else if (textStatus === 'timeout') {
                        alert('Time out error.');
                    } else if (textStatus === 'abort') {
                        alert('Ajax request aborted.');
                    } else {
                        alert('Error: ' + jqXHR.responseText);
                    }
                    Phx.CP.loadingHide();
                });

            },

            preparaMenu: function (n) {
                var data = this.getSelectedData();
                var tb = this.tbar;
                this.getBoton('btnPdfFirmaDigital').enable();
                this.getBoton('btnFirmaDigital').enable();
                Phx.vista.Obs.superclass.preparaMenu.call(this, n);
                return tb
            },
            liberaMenu: function () {
                var tb = Phx.vista.Obs.superclass.liberaMenu.call(this);
                this.getBoton('btnPdfFirmaDigital').disable();
                this.getBoton('btnFirmaDigital').disable();
                return tb
            },

            successObs: function (resp) {
                Phx.CP.loadingHide();
                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                if (!reg.ROOT.error) {
                    this.reload();
                }
            },

            guardarFirma: function (win, simple) {
                var me = this;
                var rec = this.sm.getSelected();
                var pin = simple.items.items[0].getValue();
                if (pin != '') {
                    var nuevoArchivo = '';
                    if (rec.data.nombre_archivo == null || rec.data.nombre_archivo == '') {
                        nuevoArchivo = rec.data.num_tramite + '-' + Date.now() + '.pdf';
                    }
                    $.ajax({
                        type: 'POST',
                        url: url + endpoint_post_pin,
                        data: JSON.stringify({
                            pin: pin,
                            slot: slot
                        }),
                        dataType: 'json',
                        contentType: 'application/json',
                        success: function (data2) {
                            if (data2.finalizado === true) {
                                //validación, hacer que sea configurable
                                var valido = true;
                                var login_session = Phx.CP.config_ini.nombre_usuario.trim();
                                if (rec.data.ci_login != data2.datos.data_token.data[1].titular.uidNumber) {
                                    alert('No es posible continuar, el CI del Firmador y el CI del titular del Token, no coinciden. "' + rec.data.ci_login + '" ' + String.fromCharCode(8800) + ' "' + data2.datos.data_token.data[1].titular.uidNumber + '"');
                                    valido = false;
                                }
                                if (login_session != data2.datos.data_token.data[1].titular.CN) {
                                    alert('No es posible continuar, el nombre del Firmador y el nombre del titular del Token, no coinciden. "' + login_session + '" ' + String.fromCharCode(8800) + ' "' + data2.datos.data_token.data[1].titular.CN + '"');
                                    valido = false;
                                }
                                if (valido) {
                                    var alias = data2.datos.data_token.data[1].alias;
                                    Ext.Ajax.request({
                                        url: '../../sis_kactivos_fijos/control/Movimiento/obtenerDocumentoFirma',
                                        params: {
                                            'id_movimiento': rec.data.id_movimiento,
                                            'nombre_archivo': rec.data.nombre_archivo,
                                            'firmado': rec.data.firmado,
                                            'nuevo_archivo': nuevoArchivo,
                                            'cod_movimiento': rec.data.cod_movimiento
                                        },
                                        success: function (response) {
                                            var archivoBase64 = response.responseText;
                                            $.ajax({
                                                type: 'POST',
                                                url: url + endpoint_post_firmar_pdf,
                                                data: JSON.stringify({
                                                    slot: slot,
                                                    pin: pin,
                                                    alias: alias,
                                                    pdf: archivoBase64
                                                }),
                                                dataType: 'json',
                                                contentType: 'application/json',
                                                success: function (data3) {
                                                    if (data3.finalizado === true) {
                                                        var pdfFirmadoBase64 = data3.datos.pdf_firmado;
                                                        var nombreArchivo = rec.data.nombre_archivo;
                                                        if (nuevoArchivo !== '') {
                                                            nombreArchivo = nuevoArchivo;
                                                        }
                                                        Ext.Ajax.request({
                                                            url: '../../sis_kactivos_fijos/control/Movimiento/firmarDocumento',
                                                            params: {
                                                                'nombre_archivo': nombreArchivo,
                                                                'firmado': rec.data.firmado,
                                                                'id_movimiento': rec.data.id_movimiento,
                                                                'pdf_firmado_base64': pdfFirmadoBase64
                                                            },
                                                            success: function (response) {
                                                                me.reload();
                                                                if (!pdfFirmadoBase64.startsWith('data:application/pdf;base64,')) {
                                                                    pdfFirmadoBase64 = 'data:application/pdf;base64,' + pdfFirmadoBase64;
                                                                }
                                                                const enlace = pdfFirmadoBase64;
                                                                const enlaceDescarga = document.createElement("a");
                                                                enlaceDescarga.href = enlace;
                                                                enlaceDescarga.download = nombreArchivo;
                                                                enlaceDescarga.click();
                                                            },
                                                            failure: me.conexionFailure,
                                                            timeout: me.timeout,
                                                            scope: me
                                                        });
                                                    } else {
                                                        alert('El documento no se pudo firmar, intentelo nuevamente.');
                                                    }
                                                }
                                            });
                                        },
                                        failure: me.conexionFailure,
                                        timeout: me.timeout,
                                        scope: me
                                    });
                                }
                            } else {
                                alert('No se ha podido iniciar la sesión de firma de documentos, verifique su PIN e intentelo nuevamente.');
                            }
                        }
                    });
                    win.hide();
                    this.reload();
                }
            },
            sortInfo: {
                field: 'id_obs',
                direction: 'ASC'
            },
            bnew: false,
            bedit: false,
            bdel: false,
            bsave: false
        }
    );

</script>
