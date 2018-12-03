<?php
/**
*@package pXP
*@file gen-Anexo.php
*@author  (ivaldivia)
*@date 22-10-2018 13:08:18
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>

Phx.vista.Anexo=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		this.baseParams = {id_periodo_anexo:this.maestro.id_periodo_anexo};


	//	console.log('LLEGA:',);
    	//llama al constructor de la clase padre
		Phx.vista.Anexo.superclass.constructor.call(this,config);
		this.grid.addListener('cellclick', this.oncellclick,this);
		this.init();
		var that = this;
	  this.store.baseParams.an3 = that.an3;
		this.store.baseParams.id_periodo_anexo = that.maestro.id_periodo_anexo;

		this.addButton('bcambiar_anexo',
				{
				//grupo: [0],
				text: 'Mover Anexo',
				iconCls: 'bactfil',
				disabled: true,
				handler: this.onButtonCambiar,
				tooltip: '<b>Cambiar Anexo</b><br/>Cambiar Tipo Anexo.'
				}
		);

		this.addButton('btn_agrupar',
				{
				//grupo: [0],
				text: 'Agrupar Anexo',
				iconCls: 'bcalculator',
				disabled: true,
				handler: this.onButtonAgrupar,
				tooltip: '<b>Agrupar Anexos</b><br/>Agrupar Anexos Seleccionados.'
				}
		);

		this.addButton('btn_reporte3',
				{
				//grupo: [0],
				text: 'Reporte Anexo 3',
				iconCls: 'bexcel',
				disabled: false,
				handler: this.onButtonReporte2,
				tooltip: '<b>Reporte Anexo 3</b><br/>Generar Reporte Anexo 3.'
				}
		);

		this.load({params:{start:0, limit:this.tam_pag}})
	},

	preparaMenu: function () {
			var rec = this.sm.getSelected();
			var tb = this.tbar;
			//this.getBoton('btnBoleto').enable();
			if(rec !== '') {

				this.getBoton('bcambiar_anexo').enable();
				this.getBoton('btn_agrupar').enable();
				Phx.vista.Anexo.superclass.preparaMenu.call(this);

				}
			},

			liberaMenu : function(){
					var rec = this.sm.getSelected();
							Phx.vista.Anexo.superclass.liberaMenu.call(this);
			},


	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_anexo'
			},
			type:'Field',
			form:true
		},
		{
				config: {
						name: 'control',
						fieldLabel: 'Seleccionado',
						allowBlank: true,
						anchor: '50%',
						gwidth: 80,
						maxLength: 3,
						renderer: function (value) {
								var checked = '';
								if (value == 'si') {
										checked = 'checked';
								}
								return String.format('<div style="vertical-align:middle;text-align:center;"><input style="height:40px;width:40px;" type="checkbox"  {0}></div>', checked);

						}
				},
				type: 'TextField',
				filters: {pfiltro: 'planc.impreso', type: 'string'},
				id_grupo: 0,
				grid: true,
				form: false
		},
		{
			config: {
				name: 'id_partida',
				fieldLabel: 'Cod - Nombre Partida',
				style: {
							background: '#FFF4E5'
					},
				allowBlank: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_kactivos_fijos/control/ClasificacionVariable/listarPartidas',
					id: 'id_partida',
					root: 'datos',
					sortInfo: {
						field: 'nombre_partida',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_partida', 'nombre_partida', 'codigo','sw_movimiento','tipo','gestion'],
					remoteSort: true,
					baseParams: {par_filtro: 'par.nombre_partida#codigo'}
				}),
				valueField: 'id_partida',
				displayField: 'nombre_partida',
				gdisplayField: 'nombre_partida',
				hiddenName: 'id_partida',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '80%',
				gwidth: 450,
				minChars: 2,
			    tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;">({codigo}) {nombre_partida}- {gestion}</p><p>Tipo: {sw_movimiento}<p>Rubro: {tipo}</div></tpl>',
				renderer : function(value, p, record) {
					var cadena = "<b style='color: red; font-weight: bold; font-size:12px;'>"+record.data['desc_codigo']+" </b>"+"- "+"<b style='font-size:12px; color:blue;'>"+record.data['desc_nombre']+"</b>";
					return String.format('{0}',cadena);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'par.nombre_partida#par.codigo',type: 'string'},
			grid: true,
			form: true,
			bottom_filter:true
		},
    {
			config:{
				name: 'c31',
				fieldLabel: 'C-31',
				style: {
							background: '#FFF4E5'
					},
				allowBlank: false,
				anchor: '80%',
				gwidth: 150,
				renderer:function (value,p,record){

						return  String.format('<b style="color:#6A0000; ">{0}</b>', record.data['c31']);

			},
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'anex.c31',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
				bottom_filter:true
		},
    {
			config:{
				name: 'detalle_c31',
				fieldLabel: 'Detalle',
				style: {
							background: '#FFF4E5'
					},
				allowBlank: true,
				anchor: '80%',
				gwidth: 200,
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
								return  String.format('<div style="color:#2400ff; font-size:12px;"><b>{0}</b></div>', record.data['detalle_c31']);
						}
						else{
								return '<hr><center><b><p style=" color:green; font-size:15px;">Total: </p></b></center>';
						}
				},
				maxLength:200
			},
				type:'TextField',
				filters:{pfiltro:'anex.detalle_c31',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
    {
			config:{
				name: 'monto_erp',
				fieldLabel: 'Monto',
				style: {
							background: '#FFF4E5'
					},
				allowBlank: true,
				anchor: '80%',
				gwidth: 200,
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style="color:#004DFF; font-weight: bold; text-align:right;"><b>{0}</b></div>', Ext.util.Format.number(value,'0.000,00/i'));
					}

					else{
						return  String.format('<hr><div style="font-size:15px; float:right; color:#004DFF;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_erp,'0.000,00/i'));
					}
				},
				maxLength:1310722
			},
				type:'NumberField',
				filters:{pfiltro:'anex.monto_erp',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
    {
			config: {
				name: 'id_uo',
				fieldLabel: 'Unidad Solicitante',
				style: {
							background: '#FFF4E5'
					},
				allowBlank: true,
				hidden:false,
				emptyText: 'Seleccione la Unidad...',
				store: new Ext.data.JsonStore({
					url: '../../sis_kactivos_fijos/control/ActivoFijo/listarAFUnidSol',
					id: 'id_uo',
					root: 'datos',
					sortInfo: {
						field: 'nombre_unidad',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_uo', 'nombre_unidad'],
					remoteSort: true,
					baseParams: {par_filtro: 'nombre_unidad'}
				}),
				valueField: 'id_uo',
				displayField: 'nombre_unidad',
				gdisplayField: 'nombre_unidad',
				hiddenName: 'nombre_unidad',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '80%',
				gwidth: 300,
				minChars: 2,
				renderer:function (value,p,record){
						return  String.format('<b style="color:#5B2C00; ">{0}</b>', record.data['nombre_unidad']);
			}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'nombre_unidad',type: 'string'},
			grid: true,
			form: true
		},
		{
			config: {
				name: 'tipo_anexo',
				fieldLabel: 'Tipo Anexo',
				anchor: '80%',
				tinit: false,
				allowBlank: false,
				origen: 'CATALOGO',
				gdisplayField: 'tipo_anexo',
				hiddenName: 'tipo_anexo',
				gwidth: 180,
				baseParams:{
					cod_subsistema:'KAF',
					catalogo_tipo:'anexo'
				},
				valueField: 'codigo'
			},
			type: 'ComboRec',
			id_grupo: 1,
			filters:{pfiltro:'anex.tipo_anexo',type:'numeric'},
			grid: false,
			form: true
		},
		{
			config: {
				name: 'id_periodo_anexo',
				fieldLabel: 'Periodo Anexo',
				allowBlank: false,
				emptyText: 'Elija en Periodo Anexo...',
				store: new Ext.data.JsonStore({
					url: '../../sis_kactivos_fijos/control/PeriodoAnexo/listarPeriodoAnexo',
					id: 'id_periodo_anexo',
					root: 'datos',
					sortInfo: {
						field: 'nombre_periodo',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_periodo_anexo','nombre_periodo'],
					remoteSort: true,
					baseParams: {par_filtro: 'id_periodo_anexo'}
				}),
				valueField: 'id_periodo_anexo',
				displayField: 'nombre_periodo',
				gdisplayField: 'nombre_periodo',
				hiddenName: 'id_periodo_anexo',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '80%',
				gwidth: 150,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['id_periodo_anexo']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'movtip.nombre',type: 'string'},
			grid: false,
			form: true
		},
		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'anex.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'anex.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:300
			},
				type:'TextField',
				filters:{pfiltro:'anex.usuario_ai',type:'string'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'usr_reg',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu1.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'anex.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'usr_mod',
				fieldLabel: 'Modificado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu2.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'anex.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'Anexo',
	ActSave:'../../sis_kactivos_fijos/control/Anexo/insertarAnexo2',
	ActDel:'../../sis_kactivos_fijos/control/Anexo/eliminarAnexo',
	ActList:'../../sis_kactivos_fijos/control/Anexo/listarAnexo2',
	id_store:'id_anexo',
	fields: [
		{name:'id_anexo', type: 'numeric'},
		{name:'id_partida', type: 'numeric'},
		{name:'tipo_anexo', type: 'numeric'},
		{name:'id_periodo_anexo', type: 'numeric'},
		{name:'monto_sigep', type: 'numeric'},
		{name:'total_erp', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'c31', type: 'string'},
		{name:'detalle_c31', type: 'string'},
		{name:'tipo_reg', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
    	{name:'id_uo', type: 'numeric'},
		{name:'desc_codigo', type: 'string'},
		{name:'desc_nombre', type: 'string'},
		{name:'nombre_unidad', type: 'string'},
		{name:'control', type: 'string'},
		{name:'seleccionado', type: 'string'},
		{name:'monto_erp', type: 'numeric'},
		{name:'nombre_periodo',type:'string'},
		{name:'nombre_partida',type:'string'}		

	],
	sortInfo:{
		field: 'id_anexo'
	},	
	bdel:true,
	//bsave:true,
	onButtonNew : function () {
		Phx.vista.Anexo.superclass.onButtonNew.call(this);		
		this.Cmp.id_partida.store.baseParams.id_gestion=this.maestro.id_gestion;
		console.log('EL THIS:',this.maestro);
		this.Cmp.tipo_anexo.setValue(this.codigo);
		this.Cmp.tipo_anexo.disable();
		this.Cmp.tipo_anexo.hide();
		this.Cmp.id_periodo_anexo.setValue(this.maestro.id_periodo_anexo);
		this.Cmp.id_periodo_anexo.disable();
	  this.Cmp.id_periodo_anexo.hide();
	},

	onButtonEdit : function () {
		Phx.vista.Anexo.superclass.onButtonEdit.call(this);
		this.Cmp.id_partida.store.baseParams.id_gestion=this.maestro.id_gestion;
		this.Cmp.tipo_anexo.setValue(this.codigo);
		this.Cmp.tipo_anexo.disable();
		this.Cmp.tipo_anexo.hide();
	},

	loadValoresIniciales: function () {
		this.Cmp.id_partida.store.setBaseParam('id_periodo_anexo',this.maestro.id_periodo_anexo);
		this.Cmp.id_partida.modificado = true;
		this.Cmp.id_partida.reset();
		
		Phx.vista.Anexo.superclass.loadValoresIniciales.call(this);
	},

	oncellclick : function(grid, rowIndex, columnIndex, e) {
		
			var record = this.store.getAt(rowIndex),
					fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name
			if(fieldName == 'control') {
					this.cambiarRevision(record);
			}
	},
	cambiarRevision: function(record){
			//Phx.CP.loadingShow();
			var d = record.data;
			Ext.Ajax.request({
					url:'../../sis_kactivos_fijos/control/Anexo/controlSeleccionado',
					params:{ id_anexo: d.id_anexo},
					success: this.successRevision,
					failure: this.conexionFailure,
					timeout: this.timeout,
					scope: this
			});
			//this.reload();
	},
	successRevision: function(resp){
			Phx.CP.loadingHide();
			var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
	},

	onButtonReporte2: function() {
		Phx.CP.loadingShow();
		var d = this.maestro;
		console.log('codigo:',d.id_periodo_anexo);

		Ext.Ajax.request({
						url:'../../sis_kactivos_fijos/control/PeriodoAnexo/reporteAnexo3',
						params:{
										id_periodo_anexo:d.id_periodo_anexo,
										nombre_periodo:d.nombre_periodo,
										desc_gestion:d.desc_gestion
									},
						success: this.successExport,
						failure: this.conexionFailure,
						timeout:this.timeout,
						scope:this
		});
		//console.log('LLEGA EL DATO ', d);
	},

	onButtonAgrupar: function(){
		Phx.CP.loadingShow();
		var d = this.sm.getSelected().data;
		Ext.Ajax.request({
						url:'../../sis_kactivos_fijos/control/Anexo/agruparAnexo',
						params:{id_anexo:d.id_anexo},
						success:this.successAgrupar,
						failure: this.conexionFailure,
						timeout:this.timeout,
						scope:this
		});
	},
	successAgrupar:function(resp){
			Phx.CP.loadingHide();
			var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
			if(!reg.ROOT.error){
					this.reload();
			}
	},

	onButtonCambiar: function () {
			var rec=this.sm.getSelected();
			Phx.CP.loadWindows('../../../sis_kactivos_fijos/vista/anexo/TipoAnexo.php',
					'<p style="font-size:15px;">Enviar al Tipo de Anexo <i style="color:red;" class="fa fa-arrows-alt" aria-hidden="true"></i></p>',
					{
							modal:true,
							width:450,
							height:200
					},rec.data,this.idContenedor,'AnexoTipo')
	},


	}
)
</script>
