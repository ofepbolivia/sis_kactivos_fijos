<?php
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Ext.define('Phx.vista.ParametrosBase', {
	extend: 'Ext.util.Observable',
	rutaReporte: '',
	claseReporte: '',
	titleReporte: '',
	constructor: function(config){
		Ext.apply(this,config);
		this.callParent(arguments);
		this.panel = Ext.getCmp(this.idContenedor);
		this.createComponents();
		this.definirEventos();
		this.cargaReportes();
		this.layout();
		this.render();		

		//Eventos
		var date = new Date();
		//this.cmbResponsable.store.baseParams.fecha = date.dateFormat('d/m/Y');
		this.cmbResponsable.store.baseParams.estado_func = 'act_desc';
	},
	createComponents: function(){
		this.cmbReporte = new Ext.form.ComboBox({
			fieldLabel: 'Reporte',
			triggerAction: 'all',
		    lazyRender:true,
		    allowBlank: false,
		    mode: 'local',
		    anchor: '100%',
		    store: new Ext.data.ArrayStore({
		        id: '',
		        fields: [
		            'key',
		            'value'
		        ],
		        data: []
		    }),
		    valueField: 'key',
		    displayField: 'value',
		    style: this.setBackgroundColor('cmbResponsable')
		});
		this.dteFechaDesde = new Ext.form.DateField({
			id: this.idContenedor+'_dteFechaDesde',
			fieldLabel: 'Fecha',
			vtype: 'daterange',
			endDateField: this.idContenedor+'_dteFechaHasta',
			style: this.setBackgroundColor('dteFechaDesde')
		});
		this.lblDesde = new Ext.form.Label({
			text: 'Desde: '
		});
		this.dteFechaHasta = new Ext.form.DateField({
			id: this.idContenedor+'_dteFechaHasta',
			fieldLabel: 'Hasta',
			vtype: 'daterange',
			startDateField: this.idContenedor+'_dteFechaDesde',
			style: this.setBackgroundColor('dteFechaHasta')
		});
		this.lblHasta = new Ext.form.Label({
			text: 'Hasta: '
		});
		this.cmbTipoRep = new Ext.form.ComboBox({
			name : 'tipo_repo',
			fieldLabel : 'Reporte Actu/Gant',
			allowBlank : true,
			triggerAction : 'all',
			lazyRender : true,
			mode : 'local',
			store : new Ext.data.ArrayStore({
				fields : ['tipo', 'valor'],
				data : [['gepa', 'Formato Reporte Pasada'], ['geac', 'Formato Reporte Actual']]
			}),
			anchor : '40%',
			valueField : 'tipo',
			displayField : 'valor',
			style: this.setBackgroundColor('cmbTipoRep')
		});
		this.cmbPeriodo = new Ext.form.ComboBox({
			name : 'actu_perido',
			fieldLabel : 'Perido',
			allowBlank : true,
			triggerAction : 'all',
			lazyRender : true,
			mode : 'local',
			store : new Ext.data.ArrayStore({
				fields : ['tipo', 'valor'],
				data : [['mes', 'Mensual'], ['anio', 'Anual']]
			}),
			anchor : '40%',
			valueField : 'tipo',
			displayField : 'valor',
			style: this.setBackgroundColor('cmbPeriodo')
		});				
		this.cmpFechas = new Ext.form.CompositeField({
        	fieldLabel: 'Fechas',
        	items: [this.lblDesde,this.dteFechaDesde,this.lblHasta,this.dteFechaHasta]
        });
		this.cmbClasificacion = new Ext.form.ComboBox({
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
                fields: ['id_clasificacion', 'clasificacion', 'id_clasificacion_fk'],
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
            anchor: '100%',
            minChars: 2,
            style: this.setBackgroundColor('cmbClasificacion')
		});
		this.cmbClasificacionMulti = new Ext.form.AwesomeCombo({
			fieldLabel: 'Clasificación Multiple',
			emptyText: 'Elija una opción...',
            anchor: '100%',
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
                tpl: new Ext.XTemplate([
                        '<tpl for=".">',
                        '<div class="x-combo-list-item">',
                        '<div class="awesomecombo-item {checked}">',
                        '<p><b>Código: {codigo}</b></p>',
                        '</div><p><b>Nombre: </b> <span style="color: green;">{nombre}</span></p>',
                        '</div></tpl>'
                    ]),
            valueField: 'id_clasificacion',
            displayField: 'clasificacion',                
            typeAhead: false,
            triggerAction: 'all',
            lazyRender:true,
            mode:'remote',               
            pageSize:15,
            queryDelay:1000,
            width:654,
            minChars:2,
            style: this.setBackgroundColor('cmbClasificacionMulti'),
            enableMultiSelect:true
		});
		this.cmbTipoMov = new Ext.form.ComboBox({
			name : 'total_consol',
			fieldLabel : 'Agrupador',
			allowBlank : false,
			triggerAction : 'all',
			lazyRender : true,
			mode : 'local',
			store : new Ext.data.ArrayStore({
				fields : ['tipo', 'valor'],
				data : [['deta', 'Detallado'], ['consoli', 'Consolidado']]
			}),
			anchor : '50%',
			valueField : 'tipo',
			displayField : 'valor',
			style: this.setBackgroundColor('cmbTipoMov')
		});
		this.cmbBajaReti = new Ext.form.ComboBox({
			name : 'baja_retiro',
			fieldLabel : 'baja/alta',
			allowBlank : false,
			triggerAction : 'all',
			lazyRender : true,
			mode : 'local',
			store : new Ext.data.ArrayStore({
				fields : ['tipo', 'valor'],
				data : [['alta', 'altas_intangibles'],['baj_ret', 'bajas_retiros']]
			}),
			anchor : '50%',
			valueField : 'tipo',
			displayField : 'valor',
			style: this.setBackgroundColor('cmbBajaReti')
		});
		this.cmbUbiacion = new Ext.form.ComboBox({
			name : 'ubicacion',
			fieldLabel : 'Ubicacion A.F',
			allowBlank : false,
			triggerAction : 'all',
			lazyRender : true,
			mode : 'local',
			store : new Ext.data.ArrayStore({
				fields : ['tipo', 'valor'],
				data : [['nacional', 'nacional'],['internaci', 'internacional'],['ambos','Ambos']]
			}),
			anchor : '50%',
			valueField : 'tipo',
			displayField : 'valor',
			style: this.setBackgroundColor('cmbUbiacion')
		});				
		this.cmbEstadoDepre = new Ext.form.ComboBox({
			name : 'estado_depre',
			fieldLabel : 'Estado/Depre',
			allowBlank : false,
			triggerAction : 'all',
			lazyRender : true,
			mode : 'local',
			store : new Ext.data.ArrayStore({
				fields : ['tipo','valor'],
				data : [['alta','Alta'],['reval','Revalorizado'],['ajuste','Ajuste'],['mejora','Mejora'],['','Todos']]
			}),
			anchor : '40%',
			valueField : 'tipo',
			displayField : 'valor',
			style : this.setBackgroundColor('cmbEstadoDepre')
		});		 				
		this.cmbActivo = new Ext.form.ComboBox({
			fieldLabel: 'Activo Fijo',
			anchor: '100%',
			allowBlank: true,
            emptyText: 'Elija un activo fijo...',
            store: new Ext.data.JsonStore({
                //url: '../../sis_kactivos_fijos/control/ActivoFijo/ListarActivoFijo',
                url:'../../sis_kactivos_fijos/control/ActivoFijo/listarAF',
                id: 'id_activo_fijo',
                root: 'datos',
                sortInfo: {
                    field: 'codigo',
                    direction: 'ASC'
                },
                totalProperty: 'total',
                fields: ['id_activo_fijo', 'denominacion', 'codigo'],
                remoteSort: true,
                baseParams: {
                    par_filtro: 'afij.denominacion#afij.codigo'
                }
            }),
            valueField: 'id_activo_fijo',
            displayField: 'denominacion',
            typeAhead: false,
            triggerAction: 'all',
            lazyRender: true,
            mode: 'remote',
            pageSize: 15,
            queryDelay: 1000,
            anchor: '100%',
            minChars: 2,
            tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>Código:</b> {codigo}</p><p>{denominacion}</p> </div></tpl>',
            style: this.setBackgroundColor('cmbActivo')
		});


		this.txtDenominacion = new Ext.form.TextField({
			fieldLabel: 'Denominación',
			width: '100%',
			style: this.setBackgroundColor('txtDenominacion')
		});
		this.dteFechaCompra = new Ext.form.DateField({
			fieldLabel: 'Fecha Compra Inf.',
			//format: 'd/m/Y', 
			dateFormat:'Y-m-d',
			style: this.setBackgroundColor('dteFechaCompra')
		});
		this.dteFechaCompraMax = new Ext.form.DateField({
			fieldLabel: 'Fecha Compra Sup.',
			//format: 'd/m/Y', 
			dateFormat:'Y-m-d',
			style: this.setBackgroundColor('dteFechaCompraMax')
		});
		this.lblFechaCompraInf = new Ext.form.Label({
			text: '>= '
		});
		this.lblFechaCompraSup = new Ext.form.Label({
			text: '<= '
		});
		this.cmpFechaCompra = new Ext.form.CompositeField({
        	fieldLabel: 'Fecha Compra ',
        	items: [this.lblFechaCompraInf,this.dteFechaCompra,this.lblFechaCompraSup,this.dteFechaCompraMax]
        });
		this.dteFechaIniDep = new Ext.form.DateField({
			fieldLabel: 'Fecha Ini.Dep.',
			format: 'd/m/Y', 
			style: this.setBackgroundColor('dteFechaIniDep')
		});
		this.cmbEstado = new Ext.form.ComboBox({
			fieldLabel: 'Estado Del Activo',
			anchor: '100%',
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
				fields : ['id_catalogo','codigo','descripcion'],
				remoteSort : true,
				baseParams : {
					par_filtro : 'descripcion',
					cod_subsistema:'KAF',
					catalogo_tipo:'tactivo_fijo__estado'
				}
			}),
			valueField: 'codigo',
			displayField: 'descripcion',
			forceSelection:true,
			typeAhead: false,
			triggerAction: 'all',
			lazyRender:true,
			mode:'remote',
			pageSize:10,
			queryDelay:1000,
			width:250,
			minChars:2,
			style: this.setBackgroundColor('cmbEstado')
		});
		this.cmbCentroCosto = new Ext.form.ComboBox({
			fieldLabel: 'Centro Costo',
			anchor: '100%',
			style: this.setBackgroundColor('cmbCentroCosto')
		});
		this.txtUbicacionFisica = new Ext.form.TextField({
			fieldLabel: 'Ubicación Física',
			width: '100%',
			maxLength: 1000,
			style: this.setBackgroundColor('txtUbicacionFisica')
		});
		this.cmbTipo = new Ext.form.ComboBox({
			fieldLabel: 'Tipo',
			emptyText: 'Elija un tipo...',
			lazyRender:true,
		    allowBlank: true,
		    mode: 'local',
		    anchor: '100%',
		    store: new Ext.data.ArrayStore({
		        id: '',
		        fields: [
		            'key',
		            'value'
		        ],
		        data: [['resp','Por Responsable'],['lug','Por Lugar'], ['lug_fun','Por Lugar Funcionario']]
		    }),
		    valueField: 'key',
		    displayField: 'value',
		    style: this.setBackgroundColor('cmbTipo')
		});
		this.cmbOficina = new Ext.form.ComboBox({
			fieldLabel: 'Oficina',
			anchor: '100%',
			emptyText: 'Elija una opción...',
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
                baseParams:{par_filtro:'ofi.codigo#ofi.nombre'}
            }),
            valueField: 'id_oficina',
			displayField: 'nombre',
			forceSelection: false,
			typeAhead: false,
			triggerAction: 'all',
			lazyRender: true,
			mode: 'remote',
			pageSize: 15,
			queryDelay: 1000,
			minChars: 2,
			style: this.setBackgroundColor('cmbOficina')
		});

		this.cmbResponsable = new Ext.form.ComboBox({
			fieldLabel: 'Responsable',
			anchor: '100%',
			emptyText: 'Elija un funcionario...',
			store: new Ext.data.JsonStore({  
				url: '../../sis_organigrama/control/Funcionario/listarFuncionarioCargo',
				id: 'id_uo',
				root: 'datos',
				sortInfo:{
					field: 'desc_funcionario1', 
					direction: 'ASC'
				},
				totalProperty: 'total',
				fields: ['id_funcionario','id_uo','codigo','nombre_cargo','desc_funcionario1','email_empresa','id_lugar','id_oficina','lugar_nombre','oficina_nombre'],
				// turn on remote sorting
				remoteSort: true,
				baseParams: {par_filtro:'desc_funcionario1#email_empresa#codigo#nombre_cargo'}
				
			}),
			style: this.setBackgroundColor('cmbResponsable'),
			valueField: 'id_funcionario',
			displayField: 'desc_funcionario1',
			tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>{desc_funcionario1}</b></p><p>{codigo}</p><p>{nombre_cargo}</p><p>{email_empresa}</p><p>{oficina_nombre} - {lugar_nombre}</p> </div></tpl>',
			forceSelection: true,
			typeAhead: false,
			triggerAction: 'all',
			lazyRender: true,
			mode: 'remote',
			pageSize: 10,
			queryDelay: 1000,
			width: 250,
			listWidth: '280',
			minChars: 2
		});
		this.txtObservaciones = new Ext.form.TextField({
			fieldLabel: 'Observaciones',
			width: '100%',
			style: this.setBackgroundColor('txtObservaciones')
		});
		this.cmbUnidSolic = new Ext.form.ComboBox({
			fieldLabel: 'Unidad Solicitante',
			anchor: '100%',
			style: this.setBackgroundColor('cmbUnidSolic')
		});
		this.cmbResponsableCompra = new Ext.form.ComboBox({
			fieldLabel: 'Responsable Compra',
			anchor: '100%',
			style: this.setBackgroundColor('cmbResponsableCompra')
		});
		this.cmbLugar = new Ext.form.ComboBox({
			fieldLabel: 'Lugar',
			anchor: '100%',
			emptyText: 'Elija una opción...',
			store: new Ext.data.JsonStore({
                url: '../../sis_parametros/control/Lugar/listarLugar',
                id: 'id_lugar',
                root: 'datos',
                fields: ['id_lugar','codigo','nombre'],
                totalProperty: 'total',
                sortInfo: {
                    field: 'codigo',
                    direction: 'ASC'
                },
                baseParams:{par_filtro:'lug.codigo#lug.nombre', es_regional: 'si'}
            }),
            valueField: 'id_lugar',
			displayField: 'nombre',
			forceSelection: false,
			typeAhead: false,
			triggerAction: 'all',
			lazyRender: true,
			mode: 'remote',
			pageSize: 15,
			queryDelay: 1000,
			minChars: 2,
			style: this.setBackgroundColor('cmbLugar')
		});
		this.radGroupTangible = new Ext.form.RadioGroup({
			fieldLabel: '1',
			items: [
				{boxLabel: 'Tangibles', name: 'rb-auto', inputValue: 'tangible'},
                {boxLabel: 'Intangibles', name: 'rb-auto', inputValue: 'intangible'},
                {boxLabel: 'Ambos', name: 'rb-auto', inputValue: 'ambos', checked: true}
            ]
		});
		this.radGroupTransito = new Ext.form.RadioGroup({
			fieldLabel: '2',
			items: [
				{boxLabel: 'Activos', name: 'rb-auto1', inputValue: 'af'},
                {boxLabel: 'En tránsito', name: 'rb-auto1', inputValue: 'tra'},
                {boxLabel: 'Ambos', name: 'rb-auto1', inputValue: 'ambos', checked: true}
            ]
		});
		this.radGroupEstadoMov = new Ext.form.RadioGroup({
			fieldLabel: '3',
			items: [
				{boxLabel: 'Sólo Procesos Finalizados', name: 'rb-auto2', inputValue: 'finalizado'},
                {boxLabel: 'Todos', name: 'rb-auto2', inputValue: 'todos', checked: true}
            ]
		});
        this.cmbDepto = new Ext.form.AwesomeCombo({
            fieldLabel: 'Dpto.',
            emptyText: 'Seleccione un depto....',
            anchor: '100%',
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
                    par_filtro:'DEPPTO.codigo#DEPPTO.nombre', deptos: 'todos'
                }
            }),
            valueField: 'id_depto',
            displayField: 'nombre',
            //tpl: '<tpl for="."><div class="x-combo-list-item"><p style="color: green;">Nombre: {nombre}</p><p>Código: {codigo}</p></div></tpl>',
            tpl: new Ext.XTemplate([
                '<tpl for=".">',
                '<div class="x-combo-list-item">',
                '<div class="awesomecombo-item {checked}">',
                '<p><b>Código: {codigo}</b></p>',
                '</div><p><b>Nombre: </b> <span style="color: green;">{nombre}</span></p>',
                '</div></tpl>'
            ]),
            forceSelection: true,
            typeAhead: false,
            triggerAction: 'all',
            lazyRender: true,
            mode: 'remote',
            pageSize: 10,
            queryDelay: 1000,
            gwidth: 250,
            minChars: 2,
            style: this.setBackgroundColor('cmbDepto')
        });

		//(F.E.A)
        this.descNombre = new Ext.form.ComboBox({
            name : 'desc_nombre',
            fieldLabel : 'Desc. / Nombre',
            triggerAction : 'all',
            lazyRender : true,
            mode : 'local',
            store : new Ext.data.ArrayStore({
                fields : ['tipo', 'valor'],
                data : [['desc', 'Descripción'], ['nombre', 'Nombre'], ['ambos','Nombre/Desc.']]
            }),
            anchor : '50%',
            valueField : 'tipo',
            displayField : 'valor',
            msgTarget: 'side',
            style: this.setBackgroundColor('cmbClasificacion')
        });
        this.cmbInventa = new Ext.form.ComboBox({
            name : 'inventario',
            fieldLabel : 'Inventario (si/no)',
            triggerAction : 'all',
            lazyRender : true,
            mode : 'local',
            store : new Ext.data.ArrayStore({
                fields : ['tipo', 'valor'],
                data : [['1', 'SI'], ['2', 'NO']]
            }),
            anchor : '50%',
            valueField : 'tipo',
            displayField : 'valor',
            msgTarget: 'side',
            style: this.setBackgroundColor('cmbInventa')
        });        

		this.cmbDeposito = new Ext.form.ComboBox({
			fieldLabel: 'Deposito',
			anchor: '100%',
			emptyText: 'Elija una opción...',
			store: new Ext.data.JsonStore({
                url: '../../sis_kactivos_fijos/control/Deposito/listarDeposito',
                id: 'id_deposito',
                root: 'datos',
                fields: ['id_deposito','codigo','nombre'],
                totalProperty: 'total',
                sortInfo: {
                    field: 'codigo',
                    direction: 'ASC'
                },
                baseParams:{par_filtro:'dep.codigo#dep.nombre'}
            }),
			valueField: 'id_deposito',
			displayField: 'nombre',
			forceSelection: false,
			typeAhead: false,
			triggerAction: 'all',
			lazyRender: true,
			mode: 'remote',
			pageSize: 15,
			queryDelay: 1000,
			gwidth: 150,
			minChars: 2,
			style: this.setBackgroundColor('cmbDeposito')
		});

		this.cmbMoneda = new Ext.form.ComboBox({
			fieldLabel: 'Moneda',
			anchor: '100%',
			emptyText: 'Elija una moneda...',
			store: new Ext.data.JsonStore({
                url: '../../sis_kactivos_fijos/control/MonedaDep/listarMonedaDep',
                id: 'id_moneda_dep',
                root: 'datos',
                fields: ['id_moneda_dep','desc_moneda','descripcion'],
                totalProperty: 'total',
                sortInfo: {
                    field: 'descripcion',
                    direction: 'ASC'
                },
                baseParams:{par_filtro:'mod.descripcion'}
            }),
			valueField: 'id_moneda_dep',
			displayField: 'desc_moneda',
			forceSelection: false,
			typeAhead: false,
			triggerAction: 'all',
			lazyRender: true,
			mode: 'remote',
			pageSize: 15,
			queryDelay: 1000,
			gwidth: 150,
			minChars: 2,
			allowBlank: false,
			style: this.setBackgroundColor('cmbMoneda')
		});
		//Para el rango de montos
		this.lblMontoInf = new Ext.form.Label({
			text: '>= '
		});
		this.txtMontoInf = new Ext.form.NumberField({
			allowDecimals: true,
			decimalPrecision: 2,
			style: this.setBackgroundColor('txtMontoInf')
		});
		this.lblMontoSup = new Ext.form.Label({
			text: 'y <= '
		});
		this.txtMontoSup = new Ext.form.NumberField({
			allowDecimals: true,
			decimalPrecision: 2,
			style: this.setBackgroundColor('txtMontoSup')
		});
		this.cmpMontos = new Ext.form.CompositeField({
        	fieldLabel: 'Importe Compra ',
        	items: [this.lblMontoInf,this.txtMontoInf,this.lblMontoSup,this.txtMontoSup]
        });
        //Num. C31
        this.txtNroCbteAsociado = new Ext.form.TextField({
			fieldLabel: 'C31',
			width: '100%',
			style: this.setBackgroundColor('txtNroCbteAsociado')
		});
		//Depreciación: totales o totales + detalle
		this.radGroupDeprec = new Ext.form.RadioGroup({
			fieldLabel: 'Nivel',
			items: [
				{boxLabel: 'Sólo Totales', name: 'rb-auto3', inputValue: 'clasif'},
                {boxLabel: 'Reporte Ministerio', name: 'rb-auto3', inputValue: 'ministerio'},                
                {boxLabel: 'Detallado', name: 'rb-auto3', inputValue: 'completo', checked: true}                
            ]
		});
	},
	layout: function(){
		//Fieldsets
		this.fieldSetGeneral = new Ext.form.FieldSet({
        	collapsible: true,
        	title: 'General',
        	items: [this.cmbTipoRep,this.cmbPeriodo,this.cmpFechas,this.cmbClasificacion,this.cmbClasificacionMulti,this.cmbTipoMov,this.cmbBajaReti,this.cmbUbiacion,this.cmbEstadoDepre,this.cmbActivo,this.txtDenominacion,this.cmbMoneda,this.cmpFechaCompra,this.cmpMontos,this.cmbTipo,this.cmbLugar,this.txtNroCbteAsociado,
        		this.dteFechaIniDep,this.cmbEstado,this.cmbCentroCosto,this.txtUbicacionFisica,
				this.cmbOficina,this.cmbResponsable,this.cmbDepto, this.cmbInventa, this.descNombre, this.cmbDeposito,this.radGroupDeprec]
        });

        this.fieldSetIncluir = new Ext.form.FieldSet({
        	collapsible: true,
        	title: 'Incluir Activos Fijos',
        	items: [this.radGroupTangible,this.radGroupTransito,this.radGroupEstadoMov]
        });

        this.fieldSetCompra = new Ext.form.FieldSet({
        	xtype: 'fieldset',
        	collapsible: true,
        	title: 'Compra',
        	items: [this.cmbUnidSolic,this.cmbResponsableCompra]
        });

		me = this;
		//Formulario
		this.formParam = new Ext.form.FormPanel({
            layout: 'form',
            autoScroll: true,
            items: [/*{
            	xtype: 'fieldset',
            	title: 'Reporte',
            	items: [this.cmbReporte]
            },*/this.fieldSetGeneral, this.fieldSetIncluir, this.fieldSetCompra],

            tbar: [
                {xtype:'button', text:'<i class="fa fa-print" aria-hidden="true"></i> Generar en Pantalla', tooltip: 'Generar el reporte', handler: this.onSubmit, scope: this},'-',
                {
		        	xtype:'button', 
		        	text:'<i class="fa fa-file-excel-o" aria-hidden="true"></i> Reporte XLS', 
		        	tooltip: 'reporte excel',
		        	grupo:[0,4],
		        	argument: {
		        		'news':true,
		        		def: 'csv'
		        	},
		        	handler: this.onReporteDep,
		        	scope: this
            	},'-',                
				{
            	xtype:'button', 
            	text:'<i class="fa fa-file-pdf-o" aria-hidden="true"></i> Reporte PDF', 
            	tooltip: 'reporte pdf',
            	grupo:[0,4],            	
                argument: {
                    'news': true,
                    def: 'pdf'
                },
                handler: this.onReporteDep,            	
            	scope: this            		
            	},'-',			
                {xtype:'button', text:'<i class="fa fa-undo" aria-hidden="true"></i> Reset', tooltip: 'Resetear los parámetros', handler: this.onReset, scope: this},
				'-',
                {
		        	xtype:'button', 
		        	text:'<i class="fa fa-file-excel-o" aria-hidden="true"></i> Reporte XLS', 
		        	tooltip: 'reporte excel',
		        	grupo:[0,4],
		        	argument: {
		        		'news':true,
		        		def: 'csv'
		        	},
		        	handler: this.onReporteDepPe,
		        	scope: this
            	},'-',                
				{
            	xtype:'button', 
            	text:'<i class="fa fa-file-pdf-o" aria-hidden="true"></i> Reporte PDF', 
            	tooltip: 'reporte pdf',
            	grupo:[0,4],            	
                argument: {
                    'news': true,
                    def: 'pdf'
                },
                handler: this.onReporteDepPe,            	
            	scope: this            		
            	}				               				
		      ]
			}); 

		//Contenedor
		this.viewPort = new Ext.Container({
            layout: 'border',
            width: '80%',
            autoScroll: true,
            items: [{
            	region: 'west',
            	collapsible: true,
            	width: '70%',
            	split: true,
            	title: 'Parámetros',
            	items: this.formParam
            },{
            	xtype: 'panel',
            	region: 'center',
            	id: this.idContenedor+'_centerPanelAF'
            }]
        });
	},

	onReporteDep: function (cmp, event) {

		var parametros = this.getParams();
		parametros.tipo = cmp.argument.def;
		Phx.CP.loadingShow();
		Ext.Ajax.request({
			url:'../../sis_kactivos_fijos/control/Reportes/reporteDepreciacion',
			params: parametros,
			success: this.successExport,
			failure: this.conexionFailure,
			timeout:this.timeout,
			scope:this
		});
	},
	onReporteDepPe: function (cmp, event) {

		var parametros = this.getParams();
		parametros.tipo = cmp.argument.def;
		Phx.CP.loadingShow();
		Ext.Ajax.request({
			url:'../../sis_kactivos_fijos/control/Reportes/reporteDepreciacionPeriodo',
			params: parametros,
			success: this.successExport,
			failure: this.conexionFailure,
			timeout:this.timeout,
			scope:this
		});
	}, 	
	render: function(){
		this.panel.add(this.viewPort);
        this.panel.doLayout();
        this.addEvents('init'); 
	},
	onReset: function(){
		this.cmbTipoRep.setValue('');
		this.dteFechaDesde.setValue('');
		this.dteFechaHasta.setValue('');
		this.cmbActivo.setValue('');
		this.cmbClasificacion.setValue('');		
		this.cmbClasificacionMulti.reset();					
		this.cmbTipoMov.setValue('');
		this.cmbBajaReti.setValue('');
		this.cmbUbiacion.setValue('');
		this.cmbEstadoDepre.setValue('');				
		this.txtDenominacion.setValue('');
		this.dteFechaCompra.setValue('');
		this.dteFechaIniDep.setValue('');
		this.cmbEstado.setValue('');
		this.cmbCentroCosto.setValue('');
		this.txtUbicacionFisica.setValue('');
		this.cmbOficina.setValue('');
		this.cmbResponsable.setValue('');
		this.cmbUnidSolic.setValue('');
		this.cmbResponsableCompra.setValue('');
		this.cmbLugar.setValue('');
		this.radGroupTransito.setValue('ambos');
		this.radGroupTangible.setValue('ambos');
		this.radGroupEstadoMov.setValue('todos');
		this.cmbDepto.setValue('');
		this.descNombre.setValue('');
		this.cmbInventa.setValue('');
		this.cmbDeposito.setValue('');
		this.cmbMoneda.setValue('');
		this.moneda='';
		this.dteFechaCompraMax.setValue('');
		this.txtMontoInf.setValue('');
		this.txtMontoSup.setValue('');
		this.txtNroCbteAsociado.setValue('');
		this.radGroupDeprec.setValue('completo');
		this.cmbTipo.setValue('');
		this.cmbPeriodo.setValue('');

		this.cmbClasificacion.selectedIndex=-1;
		this.cmbClasificacionMulti.selectedIndex=-1;

	},
	onSubmit: function(){
		if(this.formParam.getForm().isValid()){
			if(this.cmbReporte.getValue()){
				//Consulta de las fecha de depreciación los departamentos seleccionados
				this.verificarDepreciacionDepto();

				/*var win = Phx.CP.loadWindows(
					this.rutaReporte,
	                this.titleReporte, {
	                    width: 870,
	                    height : 620
	                }, { 
	                    paramsRep: this.getParams()
	                },
	                this.idContenedor,
	                this.claseReporte
	            );*/

			}
		}
	},
	getParams: function(){
		//Fechas
		var _fecha_desde,
			_fecha_hasta,
			_fecha_compra,
			_fecha_ini_dep,
			_id_clasificacion,					
			_fecha_compra_max;

		//Clasificación (por el problema de que getvalue devuelve la descripción y no el valor ¡?¡??)
		if(this.cmbClasificacion.selectedIndex!=-1){						
			_id_clasificacion = this.cmbClasificacion.store.getAt(this.cmbClasificacion.selectedIndex).id;			
		}
		if(this.dteFechaDesde.getValue()) _fecha_desde = this.dteFechaDesde.getValue().dateFormat('Y-m-d');
		if(this.dteFechaHasta.getValue()) _fecha_hasta = this.dteFechaHasta.getValue().dateFormat('Y-m-d');
		if(this.dteFechaCompra.getValue()) _fecha_compra = this.dteFechaCompra.getValue().dateFormat('Y-m-d');
		if(this.dteFechaIniDep.getValue()) _fecha_ini_dep = this.dteFechaIniDep.getValue().dateFormat('Y-m-d');
		if(this.dteFechaCompraMax.getValue()) _fecha_compra_max = this.dteFechaCompraMax.getValue().dateFormat('Y-m-d');

		//Parametros
		var params = {
			titleReporte: this.titleReporte,
			reporte: this.cmbReporte.getValue(),
			fecha_desde: _fecha_desde,
			fecha_hasta: _fecha_hasta,
			id_activo_fijo: this.cmbActivo.getValue(),
			id_clasificacion: _id_clasificacion,
			denominacion: this.txtDenominacion.getValue(),
			fecha_compra: _fecha_compra,
			fecha_ini_dep: _fecha_ini_dep,
			estado: this.cmbEstado.getValue(),
			id_centro_costo: this.cmbCentroCosto.getValue(),
			ubicacion: this.txtUbicacionFisica.getValue(),
			id_oficina: this.cmbOficina.getValue(),
			id_funcionario: this.cmbResponsable.getValue(),
			id_uo: this.cmbUnidSolic.getValue(),
			id_funcionario_compra: this.cmbResponsableCompra.getValue(),
			id_lugar: this.cmbLugar.getValue(),
			af_transito: this.radGroupTransito.getValue().inputValue,
			af_tangible: this.radGroupTangible.getValue().inputValue,
			af_estado_mov: this.radGroupEstadoMov.getValue().inputValue,
			id_depto: this.cmbDepto.getValue(),
			desc_nombre: this.descNombre.getValue(),
			inventario: this.cmbInventa.getValue(),
			id_deposito: this.cmbDeposito.getValue(),
			id_moneda: this.cmbMoneda.getValue(),
			desc_moneda: this.moneda,
			monto_inf: this.txtMontoInf.getValue(),
			monto_sup: this.txtMontoSup.getValue(),
			nro_cbte_asociado: this.txtNroCbteAsociado.getValue(),
			fecha_compra_max: _fecha_compra_max,
			af_deprec: this.radGroupDeprec.getValue().inputValue,
			tipo: this.cmbTipo.getValue(),
			id_clasificacion_multi: this.cmbClasificacionMulti.getValue(),
			total_consol : this.cmbTipoMov.getValue(),
			baja_retiro : this.cmbBajaReti.getValue(),
			ubi_nac_inter: this.cmbUbiacion.getValue(),
			estado_depre : this.cmbEstadoDepre.getValue(),
			tipo_repo : this.cmbTipoRep.getValue(),
			actu_perido:this.cmbPeriodo.getValue(),		
		};

		Ext.apply(params,this.getExtraParams());

		return params;
	},
	cargaReportes: function(){

	},
	definirParametros: function(){

	},
	definirEventos: function(){
		//Reporte
		this.cmbReporte.on('select',function(combo,record,index){
			this.onReset();
		},this);
		//Moneda
		this.cmbMoneda.on('select',function(combo,record,index){
			this.moneda = record.data.moneda
		}, this);
	},
	verificarDepreciacionDepto: function(){
		var deptos = '%';
		if(this.cmbDepto.getValue()){
			deptos = this.cmbDepto.getValue();
		}
		var obj = {deptos: deptos};

		Ext.Ajax.request({
            url: '../../sis_kactivos_fijos/control/Reportes/listarDepreciacionDeptoFechas',
            params: obj,
            isUpload: false,
            success: function(data,b,c){
            	//console.log('respuesta',data,b,c);
            	var reg = Ext.util.JSON.decode(Ext.util.Format.trim(data.responseText));
            	var mensaje = '';
            	Ext.iterate(reg.datos, function(obj){
            		mensaje+='Depto.: '+obj.desc_depto + ' -> Ultimo periodo depreciación: '+obj.fecha_max_dep+', ';
            	},this);

            	if(mensaje==''){
            		mensaje='No se generó ninguna depreciación para el Depto. seleccionado.';
            	}

            	Ext.MessageBox.confirm('Últimas Depreciaciones realizadas',mensaje+'¿Desea generar el reporte de todas formas?',function(resp){
            		if(resp=='yes'){
            			var win = Phx.CP.loadWindows(
							this.rutaReporte,
			                this.titleReporte, {
			                    width: 870,
			                    height : 620
			                }, { 
			                    paramsRep: this.getParams()
			                },
			                this.idContenedor,
			                this.claseReporte
			            );
            		}
            	},this);
            	
            },
            argument: this.argumentSave,
            failure: this.conexionFailure,
            timeout: this.timeout,
            scope: this
        });
	},
	inicializarParametros: function(){
		this.configElement(this.cmbTipoRep,false,true);
		this.configElement(this.dteFechaDesde,false,true);
		this.configElement(this.dteFechaHasta,false,true);
		this.configElement(this.cmbActivo,false,true);
		this.configElement(this.cmbClasificacion,false,true);
		this.configElement(this.cmbClasificacionMulti,false,true);
		this.configElement(this.cmbEstadoDepre,false,true);
		this.configElement(this.cmbTipoMov,false,true);
		this.configElement(this.cmbBajaReti,false,true);
		this.configElement(this.cmbUbiacion,false,true);				
		this.configElement(this.txtDenominacion,false,true);
		this.configElement(this.dteFechaCompra,false,true);
		this.configElement(this.dteFechaIniDep,false,true);
		this.configElement(this.cmbEstado,false,true);
		this.configElement(this.cmbCentroCosto,false,true);
		this.configElement(this.txtUbicacionFisica,false,true);
		this.configElement(this.cmbOficina,false,true);
		this.configElement(this.cmbResponsable,false,true);
		this.configElement(this.cmbUnidSolic,false,true);
		this.configElement(this.cmbResponsableCompra,false,true);
		this.configElement(this.cmbLugar,false,true);
		this.configElement(this.radGroupTransito,false,true);
		this.configElement(this.radGroupTangible,false,true);
		this.configElement(this.cmbDepto,false,true);
		this.configElement(this.descNombre,false,true);
		this.configElement(this.cmbInventa,false,true);
		this.configElement(this.cmbDeposito,false,true);
		this.configElement(this.lblDesde,false,true);
		this.configElement(this.lblHasta,false,true);
		this.configElement(this.cmbPeriodo,false,true);
		this.configElement(this.cmpFechas,false,true);
		this.configElement(this.txtMontoInf,false,true);
		this.configElement(this.txtMontoSup,false,true);
		this.configElement(this.lblMontoInf,false,true);
		this.configElement(this.lblMontoSup,false,true);
		this.configElement(this.txtNroCbteAsociado,false,true);
		this.configElement(this.cmpMontos,false,true);
		this.configElement(this.cmbMoneda,false,true);
		this.configElement(this.radGroupEstadoMov,false,true);
		this.configElement(this.cmpFechaCompra,false,true);
		this.configElement(this.radGroupDeprec,false,true);
		this.configElement(this.cmbTipo,false,true);

		this.configElement(this.fieldSetGeneral,false,true);
		this.configElement(this.fieldSetIncluir,false,true);
		this.configElement(this.fieldSetCompra,false,true);
	},
	configElement: function(elm,disable,allowBlank){
		//elm.setDisabled(disable);
		elm.setVisible(disable);
		elm.allowBlank = allowBlank;
	},
	successExport: function(resp){
    	//Método para abrir el archivo generado
    	Phx.CP.loadingHide();
        var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
        var nomRep = objRes.ROOT.detalle.archivo_generado;
        if(Phx.CP.config_ini.x==1){  			
        	nomRep = Phx.CP.CRIPT.Encriptar(nomRep);
            }
            window.open('../../../lib/lib_control/Intermediario.php?r='+nomRep+'&t='+new Date().toLocaleTimeString())
        },
        setBackgroundColor: function(elm){
            return String.format('background-color: {0}; background-image: none;', this.setPersonalBackgroundColor(elm));
        },
        setPersonalBackgroundColor: function(elm){
            //Para sobreescribir
            return '#FFF';
        }
    });
</script>