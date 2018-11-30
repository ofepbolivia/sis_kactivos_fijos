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
                        data : [['compras_gestion', 'Compras de Gestion'],
                                ['detalle_af', 'Detalle Activos Fijos'],
                                ['pendientes_aprobacion', 'Pendientes de Aprobación'],
                                ['sin_asignacion', 'Sin Asignación']]
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
                    allowBlank: false,
                    anchor: '54.5%',
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
                    allowBlank: false,
                    anchor: '54.5%',
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
                    anchor : '70%',
                    valueField : 'id',
                    displayField : 'valor'
                },
                type : 'ComboBox',
                id_grupo : 1,
                grid:true,
                form : true
            },
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
                    anchor : '70%',
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
            {  config: {             
                fieldLabel: 'Estado funcional Actual',
                name: 'id_cat_estado_fun',
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
                anchor : '70%',
                gwidth : 150,                
                tpl : '<tpl for="."><div class="x-combo-list-item"><p>{codigo} - {descripcion}</p></div></tpl>'
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
                    anchor : '70%',
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
                    anchor : '70%',
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
                        data : [['desc', 'Descripción'], ['nombre', 'Nombre'],['descnom','Nombre/Desc.']]
                    }),           
                    anchor : '70%',
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
                    allowBlank : false,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store : new Ext.data.ArrayStore({
                        fields : ['id', 'valor'],
                        data : [['1', 'Nacional'], ['2', 'Internacional'],['3', 'Ambos']]
                    }),
                    anchor : '70%',
                    valueField : 'id',
                    displayField : 'valor'
                },
                type : 'ComboBox',
                id_grupo : 1,
                grid:true,
                form : true
            },
 			{
                config : {
                    name : 'id_lugar',
                    fieldLabel : 'Estación',
                    allowBlank : true,
                    emptyText : 'Estación...',
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
                        baseParams:{par_filtro:'lug.codigo#lug.nombre', es_regional: 'si', _adicionar:'si'}
                    }),                    
                    tpl: new Ext.XTemplate([
                        '<tpl for=".">',
                        '<div class="x-combo-list-item">',
                        '<div class="awesomecombo-item {checked}">',
                        '<p><b>Código: {codigo}</b></p>',
                        '</div><p><b>Nombre: </b> <span style="color: green;">{nombre}</span></p>',
                        '</div></tpl>'
                    ]),
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
                    anchor : '70%',
                    enableMultiSelect: true
                },

                type : 'AwesomeCombo',
                id_grupo : 1,
                grid : true,
                form : true
            },
			{
				config: {
					name: 'id_oficina',
					fieldLabel: 'Oficina',
					allowBlank: true,
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
	                tpl: new Ext.XTemplate([
	                        '<tpl for=".">',
	                        '<div class="x-combo-list-item">',
	                        '<div class="awesomecombo-item {checked}">',
	                        '<p><b>Código: {codigo}</b></p>',
	                        '</div><p><b>Nombre: </b> <span style="color: green;">{nombre}</span></p>',
	                        '</div></tpl>'
	                    ]),					
					gdisplayField: 'oficina',
					hiddenName: 'id_oficina',
					forceSelection: false,
					typeAhead: false,
					triggerAction: 'all',
					lazyRender: true,
					mode: 'remote',
					pageSize: 15,
					queryDelay: 1000,
					anchor: '70%',
					gwidth: 150,
					minChars: 2,
					enableMultiSelect:true
				},
				type: 'AwesomeCombo',
				id_grupo: 2,
				filters: {pfiltro: 'ofi.nombre',type: 'string'},
				grid: true,
				form: true
			},            
			{
	            config:{
	                name:'id_clasificacion',
	                fieldLabel:'Clasificación Múltiple Activo',
	                allowBlank:true,
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
	                tpl: new Ext.XTemplate([
	                        '<tpl for=".">',
	                        '<div class="x-combo-list-item">',
	                        '<div class="awesomecombo-item {checked}">',
	                        '<p><b>Código: {codigo}</b></p>',
	                        '</div><p><b>Nombre: </b> <span style="color: green;">{nombre}</span></p>',
	                        '</div></tpl>'
	                    ]),
	                hiddenName: 'id_clasificacion',                
	                typeAhead: false,
	                triggerAction: 'all',
	                lazyRender:true,                
	                pageSize:15,
	                queryDelay:1000,
	                listWidth:400,
	                resizable:true,
	                anchor:'70%',
	                minChars:2,
	                gwidth : 150,
	                enableMultiSelect:true,	            	               
	            },
				type:'AwesomeCombo',			
				form:true,
				id_grupo : 2
	        },
			{
				config:{
					name: 'nro_cbte_asociado',
					fieldLabel: 'C31',
					allowBlank: true,
					anchor: '50%',
					gwidth: 100					
				},
					type:'TextField',					
					id_grupo:2,					
					form:true
			},
            {
                config : {
                    name : 'column_busque',
                    fieldLabel : 'Columna de Busqueda',
                    allowBlank : true,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store : new Ext.data.ArrayStore({
                        fields : ['id', 'valor'],
                        data : [['1', 'Valor comp al 87%'], ['2', 'Valor comp al 100%'],['3','Valor Actual']]
                    }),
                    anchor : '70%',
                    valueField : 'id',
                    displayField : 'valor'
                },
                type : 'ComboBox',
                id_grupo : 2,
                grid:true,
                form : true
            },			
            {
                config : {
                    name : 'valor_actual',
                    fieldLabel : 'Condicion de Busqueda',
                    allowBlank : true,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store : new Ext.data.ArrayStore({
                        fields : ['id', 'valor'],
                        data : [['1', 'Mayor igual'], ['2', 'Menor igual'],['3','Ambos']]
                    }),
                    anchor : '70%',
                    valueField : 'id',
                    displayField : 'valor'
                },
                type : 'ComboBox',
                id_grupo : 2,
                grid:true,
                form : true
            },			
			{	
				config:{
					name: 'txtMontoSup',
					fieldLabel: 'Valor de Busqueda >=',
					allowBlank: true,
					anchor: '50%',
					gwidth: 100,
					allowDecimals: true,
					decimalPrecision: 2					
					
				},
					type:'NumberField',					
					id_grupo:2,					
					form:true
			},			
			{	
				config:{
					name: 'txtMontoInf',					
					fieldLabel: 'Valor de Busqueda <=',								
					allowBlank: true,
					anchor: '50%',
					gwidth: 100,					
					allowDecimals: true,
					decimalPrecision: 2						
				},
					type:'NumberField',					
					id_grupo:2,					
					form:true
			},
            {
                config : {
                    name : 'id_depto',
					fieldLabel: 'Dpto. de Activos Fijos',
		            emptyText: 'Seleccione un depto....',                    
                    allowBlank : true,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store : new Ext.data.ArrayStore({
                        fields : ['id', 'valor'],
                        data : [['7', 'Unidad de Activos Fijos'], ['47', 'Unidad de Activos Fijos TI'],['3','Ambos']]
                    }),
                    anchor : '70%',
                    valueField : 'id',
                    displayField : 'valor'
                },
                type : 'ComboBox',
                id_grupo : 2,
                grid:true,
                form : true
            },			
			 {
					 config : {
					 name : 'activo_multi',					 
					 fieldLabel : 'Reporte Mult X Activo',
					 allowBlank : true,
					 triggerAction : 'all',
					 lazyRender : true,
					 mode : 'local',
				     store: new Ext.data.ArrayStore({
				        id: '',
				        fields: [
				            'key',
				            'value'
				        ],
				        data: [
				        ['acod','CODIGO'],
				        ['ades','DESCRIPCIÓN'],
				        ['aest','ESTADO'],
				        ['aesf', 'ESTADO FUNCIONAL'],
				        ['afec','FECHA COMPRA'],
				        ['amon','MONTO (87%)'],
				        ['aimp','IMPORTE (100%)'],
				        ['aval','VALOR ACTUAL'],
				        ['ac31','C-31'],
				        ['af31','FECHA COMP C31'],
				        ['aubi','UBICACION'],
				        ['ares','RESPONSABLE'],
				        ['auco','UNIDAD SOLICI.']			        
				        ]
				     }),
				     valueField: 'key',
				     displayField: 'value',					 
					 width : 200,
					 enableMultiSelect:true					 
			 	},
			 	type : 'AwesomeCombo',
			 	id_grupo : 0,
			 	form : true
			 },
			 {
					 config : {
					 name : 'gestion_multi',					 
					 fieldLabel : 'Reporte Mult X Gestion',
					 allowBlank : true,
					 triggerAction : 'all',
					 lazyRender : true,
					 mode : 'local',
				     store: new Ext.data.ArrayStore({
				        id: '',
				        fields: [
				            'key',
				            'value'				            
				        ],
				        data: [
				        ['gcod','CODIGO'],
				        ['gdes','DESCRIPCIÓN'],		
				        ['gfec','FECHA COMPRA'],		        				        				        				       
				        ['gnum','NUMERO DE COMPRA'],	
				        ['gf31','FECHA COMP C31'],
				        ['gfei','FECHA INI DEPRE'],			        
				        ['gvit','VIDA UTIL ORIGINAL'],
				        ['gviu','VIDA UTIL RESTANTE'],
				        ['gimp','IMPORTE 100%'],				        
				        ['gmon','MONTO 87%'],
				        ['guco','UNIDAD SOLICI.']				        
				        ]
				     }),				     
				     valueField: 'key',
				     displayField: 'value',					 
					 width : 200,
					 enableMultiSelect:true				 
			 	},
			 	type : 'AwesomeCombo',			 	
			 	id_grupo : 0,
			 	form : true
			 },
           {
                config : {
                    name : 'id_proveedor',
                    fieldLabel : 'Proveedor',
                    allowBlank : true,
                    emptyText : 'Estado...',
                    store : new Ext.data.JsonStore({
                        url: '../../sis_kactivos_fijos/control/ActivoFijo/proveedorActivo',
                        id : 'id_proveedor',
                        root : 'datos',
                        sortInfo : {
                            field : 'id_proveedor',
                            direction : 'ASC'
                        },
                        totalProperty : 'total',
                        fields : ['id_proveedor', 'provee'],
                        remoteSort : true,
                        baseParams : {
                            par_filtro : 'pro.desc_proveedor'                                                       
                        }
                    }),
                    valueField : 'idproveedor_',
                    displayField : 'provee',
                    gdisplayField : 'provee',
                    hiddenName : 'id_proveedor',
                    forceSelection : true,
                    typeAhead : false,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'remote',
                    pageSize : 10,
                    queryDelay : 1000,
                    anchor : '70%',
                    gwidth : 150,
                    minChars : 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['provee']);
                    }
                },
                type : 'ComboBox',
                id_grupo : 2,
                grid : true,
                form : true
            },
			{
				config:{
					name: 'nr_factura',
					fieldLabel: 'Nro Factura',
					allowBlank: true,
					anchor: '50%',
					gwidth: 100					
				},
					type:'TextField',					
					id_grupo:2,					
					form:true
			},
			{
				config:{
					name:'tramite_compra',
					fieldLabel:'Nro de Tramite de Compra',
					allowBlank:true,
					anchor:'50%',
					gwidth:100
				},
				type:'TextField',
				id_grupo:2,
				form:true
			},
			{
				config:{
					name:'nro_serie',
					fieldLabel:'Nro de Serie',
					allowBlank:true,
					anchor:'50%',
					gwidth:100
				},
				type:'TextField',
				id_grupo:2,
				form:true
			},{
                config : {
                    name : 'rep_pendiente_aprobacion',
                    fieldLabel : 'Reporte Mult Pendiente de Aprobación',
                    allowBlank : true,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store: new Ext.data.ArrayStore({
                        id: '',
                        fields: [
                            'key',
                            'value'
                        ],
                        data: [
                            ['pprc','Nº PROCESO COMPRA'],
                            ['pfpr','FECHA DE PROCESO'],
                            ['pglo','GLOSA'],
                            ['pnom','NOMBRE USUARIO'],
                            ['pdep','DEPARTAMENTO'],

                        ]
                    }),
                    valueField: 'key',
                    displayField: 'value',
                    width : 200,
                    enableMultiSelect:true
                },
                type : 'AwesomeCombo',
                id_grupo : 0,
                form : true
            },{
                config : {
                    name : 'rep_sin_asignacion',
                    fieldLabel : 'Reporte Mult Sin Asignacion',
                    allowBlank : true,
                    triggerAction : 'all',
                    lazyRender : true,
                    mode : 'local',
                    store: new Ext.data.ArrayStore({
                        id: '',
                        fields: [
                            'key',
                            'value'
                        ],
                        data: [
                            ['scod','CODIGO'],
                            ['sdes','DESCRIPCIÓN'],
                            ['sfea','FECHA DE ALTA'],
                            ['s100','MONTO 100%'],
                            ['sm87','MONTO 87%'],
                            ['suns','UNIDAD SOLICITANTE'],
                            ['sprc','Nº PROCESO COMPRA'],
                            ['sc31','C31'],

                        ]
                    }),
                    valueField: 'key',
                    displayField: 'value',
                    width : 200,
                    enableMultiSelect:true
                },
                type : 'AwesomeCombo',
                id_grupo : 0,
                form : true
            }
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
            this.getComponente('gestion_multi').setVisible(false);
            this.getComponente('activo_multi').setVisible(false);
            this.getComponente('rep_pendiente_aprobacion').setVisible(false);
            this.getComponente('rep_sin_asignacion').setVisible(false);
            this.getComponente('txtMontoInf').setVisible(false);
            this.getComponente('txtMontoSup').setVisible(false);
            this.getComponente('valor_actual').setVisible(false);                        
        },

        iniciarEventos:function(){        	        	
        	this.Cmp.configuracion_reporte.on('select',function(cmb,rec,ind){        		        		        	
        		if (rec.data.tipo == 'compras_gestion'){
		            this.ocultarComponente(this.Cmp.activo_multi);      			
		            this.mostrarComponente(this.Cmp.gestion_multi);
		            this.ocultarComponente(this.Cmp.rep_pendiente_aprobacion);
                    this.ocultarComponente(this.Cmp.rep_sin_asignacion);
                    //MOSTRAR CAMPOS
                    this.mostrarComponente(this.Cmp.tipo_reporte);
                    this.mostrarComponente(this.Cmp.estado);
                    this.mostrarComponente(this.Cmp.desc_nombre);
                    this.mostrarComponente(this.Cmp.ubicacion);
                    this.mostrarComponente(this.Cmp.nro_cbte_asociado);
                    this.mostrarComponente(this.Cmp.column_busque);
                    this.mostrarComponente(this.Cmp.id_proveedor);
                    this.mostrarComponente(this.Cmp.nr_factura);
                    this.mostrarComponente(this.Cmp.tramite_compra);
                    this.mostrarComponente(this.Cmp.nro_serie);
                    this.mostrarComponente(this.Cmp.id_oficina);
                    this.mostrarComponente(this.Cmp.id_clasificacion);
                    this.mostrarComponente(this.Cmp.id_lugar);
                    this.mostrarComponente(this.Cmp.tipo_activo);
		            this.Cmp.gestion_multi.getStore().each(function(rec){
		            	this.Cmp.gestion_multi.checkRecord(rec);
		            },this);		            		            		            								
				}else if(rec.data.tipo == 'detalle_af'){
                    this.mostrarComponente(this.Cmp.activo_multi);
                    this.ocultarComponente(this.Cmp.gestion_multi);
                    this.ocultarComponente(this.Cmp.rep_pendiente_aprobacion);
                    this.ocultarComponente(this.Cmp.rep_sin_asignacion);
                    //MOSTRAR CAMPOS
                    this.mostrarComponente(this.Cmp.tipo_reporte);
                    this.mostrarComponente(this.Cmp.estado);
                    this.mostrarComponente(this.Cmp.desc_nombre);
                    this.mostrarComponente(this.Cmp.ubicacion);
                    this.mostrarComponente(this.Cmp.nro_cbte_asociado);
                    this.mostrarComponente(this.Cmp.column_busque);
                    this.mostrarComponente(this.Cmp.id_proveedor);
                    this.mostrarComponente(this.Cmp.nr_factura);
                    this.mostrarComponente(this.Cmp.tramite_compra);
                    this.mostrarComponente(this.Cmp.nro_serie);
                    this.mostrarComponente(this.Cmp.id_oficina);
                    this.mostrarComponente(this.Cmp.id_clasificacion);
                    this.mostrarComponente(this.Cmp.id_lugar);
                    this.mostrarComponente(this.Cmp.tipo_activo);
                    this.Cmp.activo_multi.getStore().each(function(rec){
                        this.Cmp.activo_multi.checkRecord(rec);
                    },this);
                }else if(rec.data.tipo == 'pendientes_aprobacion'){
                    this.ocultarComponente(this.Cmp.activo_multi);
                    this.ocultarComponente(this.Cmp.gestion_multi);
                    this.mostrarComponente(this.Cmp.rep_pendiente_aprobacion);
                    this.ocultarComponente(this.Cmp.rep_sin_asignacion);
                    //OCULTAR CAMPOS
                    this.ocultarComponente(this.Cmp.tipo_reporte);
                    this.ocultarComponente(this.Cmp.estado);
                    this.ocultarComponente(this.Cmp.desc_nombre);
                    this.ocultarComponente(this.Cmp.ubicacion);
                    this.ocultarComponente(this.Cmp.nro_cbte_asociado);
                    this.ocultarComponente(this.Cmp.column_busque);
                    this.ocultarComponente(this.Cmp.id_proveedor);
                    this.ocultarComponente(this.Cmp.nr_factura);
                    this.ocultarComponente(this.Cmp.tramite_compra);
                    this.ocultarComponente(this.Cmp.nro_serie);
                    this.ocultarComponente(this.Cmp.id_oficina);
                    this.ocultarComponente(this.Cmp.id_clasificacion);
                    this.ocultarComponente(this.Cmp.id_lugar);
                    this.ocultarComponente(this.Cmp.tipo_activo);

                    this.Cmp.rep_pendiente_aprobacion.getStore().each(function(rec){
                        this.Cmp.rep_pendiente_aprobacion.checkRecord(rec);
                    },this);
                }else if(rec.data.tipo == 'sin_asignacion'){
                    this.ocultarComponente(this.Cmp.activo_multi);
                    this.ocultarComponente(this.Cmp.gestion_multi);
                    this.ocultarComponente(this.Cmp.rep_pendiente_aprobacion);
                    this.mostrarComponente(this.Cmp.rep_sin_asignacion);
                    //OCULTAR CAMPOS
                    this.ocultarComponente(this.Cmp.tipo_reporte);
                    this.ocultarComponente(this.Cmp.estado);
                    this.ocultarComponente(this.Cmp.desc_nombre);
                    this.ocultarComponente(this.Cmp.ubicacion);
                    this.ocultarComponente(this.Cmp.nro_cbte_asociado);
                    this.ocultarComponente(this.Cmp.column_busque);
                    this.ocultarComponente(this.Cmp.id_proveedor);
                    this.ocultarComponente(this.Cmp.nr_factura);
                    this.ocultarComponente(this.Cmp.tramite_compra);
                    this.ocultarComponente(this.Cmp.nro_serie);
                    this.ocultarComponente(this.Cmp.id_oficina);
                    this.ocultarComponente(this.Cmp.id_clasificacion);
                    this.ocultarComponente(this.Cmp.id_lugar);
                    this.ocultarComponente(this.Cmp.tipo_activo);

                    this.Cmp.rep_sin_asignacion.getStore().each(function(rec){
                        this.Cmp.rep_sin_asignacion.checkRecord(rec);
                    },this);
                }
            },this);
        	
        	this.Cmp.column_busque.on('select',function(cmb,rec,ind){
        		if(rec.data.id in ['1','2','3']){
        			this.mostrarComponente(this.Cmp.valor_actual);
        		}
        	},this);
        	this.Cmp.valor_actual.on('select',function(cmb,rec,ind){
				if (rec.data.id == '1'){
        			this.mostrarComponente(this.Cmp.txtMontoSup);
        			this.ocultarComponente(this.Cmp.txtMontoInf);
        		}else if(rec.data.id == '2'){
        			this.mostrarComponente(this.Cmp.txtMontoInf);
        			this.ocultarComponente(this.Cmp.txtMontoSup);
        		}else{
        			this.mostrarComponente(this.Cmp.txtMontoInf);
        			this.mostrarComponente(this.Cmp.txtMontoSup);
        		}        		
        	},this);
        	//PARA RESET DE CAMPOS

            this.Cmp.configuracion_reporte.on('select', function (cmp, rec, ind) {
                if ((rec.data.tipo == 'pendientes_aprobacion') || (rec.data.tipo == 'sin_asignacion')){
                    this.Cmp.tipo_reporte.reset();
                    this.Cmp.tipo_reporte.modificado = true;
                    this.Cmp.estado.reset();
                    this.Cmp.estado.modificado = true;
                    this.Cmp.desc_nombre.reset();
                    this.Cmp.desc_nombre.modificado = true;
                    this.Cmp.ubicacion.reset();
                    this.Cmp.ubicacion.modificado = true;
                    this.Cmp.nro_cbte_asociado.reset();
                    this.Cmp.nro_cbte_asociado.modificado = true;
                    this.Cmp.column_busque.reset();
                    this.Cmp.column_busque.modificado = true;
                    this.Cmp.id_depto.reset();
                    this.Cmp.id_depto.modificado = true;
                    this.Cmp.id_proveedor.reset();
                    this.Cmp.id_proveedor.modificado = true;
                    this.Cmp.nr_factura.reset();
                    this.Cmp.nr_factura.modificado = true;
                    this.Cmp.tramite_compra.reset();
                    this.Cmp.tramite_compra.modificado = true;
                    this.Cmp.nro_serie.reset();
                    this.Cmp.nro_serie.modificado = true;
                    this.Cmp.id_oficina.reset();
                    this.Cmp.id_oficina.modificado = true;
                    this.Cmp.id_clasificacion.reset();
                    this.Cmp.id_clasificacion.modificado = true;
                    this.Cmp.id_lugar.reset();
                    this.Cmp.id_lugar.modificado = true;
                    this.Cmp.tipo_activo.reset();
                    this.Cmp.tipo_activo.modificado = true;
                    this.Cmp.nro_cbte_asociado.reset();
                    this.Cmp.nro_cbte_asociado.modificado = true;

                }

            }, this);
        },

        onSubmit:function(o){        	
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
                    autoWidth:true,
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
                    columnWidth: .60,
                    border: false,
                    //split: true,
                    layout: 'anchor',
                    autoScroll: true,
                    autoHeight: true,                    
                    collapseFirst : false,
                    collapsible: false,
                    anchor: '100%',
                    bodyStyle: 'margin: 1% 1% 0 10%;',

                    items:[
                        {                        	        
                            bodyStyle: 'padding-right:5px;',                            
                            border: false,
                            layout: 'anchor',                                                      
                            items:[
                                {
                                    xtype: 'fieldset',                                    
                                    layout: 'column',                                                                                                            
                                    border: true,
                                    title: 'Reporte Compras x Gestión',
                                    bodyStyle: 'padding: 5px 5px 0 5px;',
                                    bodyStyle:'margin: 0 0 0 0',                                    
                                    items: [
						                {
						                    xtype: 'fieldset',
						                    border: false,                    						                    						                   						                     						                    
						                    layout: 'form',							                    					                    
						                    bodyStyle : 'padding : 0 0px 0 0;',
                                            columnWidth : 0.40,
						                    items: [],
						                    id_grupo: 1
						                },
						                {
						                    xtype: 'fieldset',
						                    border: false,							                    					                   
						                    bodyStyle : 'padding : 0 50px 0 0;',
						                    //bodyStyle : 'margin: 0 60 0 0',
                                            columnWidth : 0.60,                    						                    	                    						                    						                    						                   
						                    layout: 'form',
						                    items: [],
						                    id_grupo: 2
						                } 
						             ],                                   
                                }
                            ]
                        }
                    ]
                }
            ]
        }]
    })   
</script>
