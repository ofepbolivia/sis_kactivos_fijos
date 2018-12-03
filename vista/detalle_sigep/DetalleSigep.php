<?php
/**
*@package pXP
*@file gen-DetalleSigep.php
*@author  (ivaldivia)
*@date 25-10-2018 15:35:31
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.DetalleSigep=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.DetalleSigep.superclass.constructor.call(this,config);
		this.init();
		var that = this;
	  this.store.baseParams.excel = that.excel;
	  this.store.baseParams.id_periodo_anexo = that.maestro.id_periodo_anexo;

		//this.Cmp.id_periodo_anexo.store.setBaseParam('id_periodo_anexo',this.maestro.id_periodo_anexo);
		//console.log('EL BASE PARAMS ES:',this.Cmp.id_periodo_anexo);
		this.load({params:{start:0, limit:this.tam_pag}});
		
		this.addButton('btnRepDetSigep',
				{				
				text: 'Reporte Sigep',
				iconCls: 'bpdf32',
				disabled: false,
				handler: this.repSigep,
				tooltip: '<b>Reporte Sigep</b><br/>Reporte Detalle Sigep '
				}
		);			
	},

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_detalle_sigep'
			},
			type:'Field',
			form:true
		},
		{
			config:{
				name: 'nro_partida',
				fieldLabel: 'Nro. Partida',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:200
			},
				type:'TextField',
				filters:{pfiltro:'detsig.nro_partida',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
				bottom_filter:true
		},
		{
			config:{
				name: 'c31',
				fieldLabel: 'Preventivo',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
								return  String.format('<div>{0}</div>', record.data['c31']);
						}
						else{
								return '<hr><b><p style="font-size:15px; color:red; text-align:right;">Total: </p></b>';
						}
				},
				maxLength:200
			},
				type:'TextField',
				filters:{pfiltro:'detsig.c31',type:'string'},
				id_grupo:1,
				grid:true,
				bottom_filter:true,
				form:true
		},
		{
			config:{
				name: 'monto_sigep',
				fieldLabel: 'Monto Sigep',
				allowBlank: false,
				anchor: '80%',
				gwidth: 150,
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style="color:#004DFF; text-align:right;"><b>{0}</b></div>', Ext.util.Format.number(value,'0.000,00/i'));
					}

					else{
						return  String.format('<hr><div style="font-size:15px; float:right; color:#004DFF;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_sigep,'0.000,00/i'));
					}
				},
				maxLength:1310722
			},
				type:'NumberField',
				filters:{pfiltro:'detsig.monto_sigep',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config: {
				name: 'id_periodo_anexo',
				fieldLabel: 'id_periodo_anexo',
				allowBlank: true,
				//hidden:true,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_kactivos_fijos/control/PeriodoAnexo/listarPeriodoAnexo',
					id: 'id_periodo_anexo',
					root: 'datos',
					sortInfo: {
						field: 'nombre_periodo',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_periodo_anexo', 'nombre_periodo'],
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
				anchor: '100%',
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
			form: false
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
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'detsig.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'detsig.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'detsig.usuario_ai',type:'string'},
				id_grupo:1,
				grid:true,
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
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'detsig.estado_reg',type:'string'},
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
				filters:{pfiltro:'detsig.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'Detalle Sigep',
	ActSave:'../../sis_kactivos_fijos/control/DetalleSigep/insertarDetalleSigep',
	ActDel:'../../sis_kactivos_fijos/control/DetalleSigep/eliminarDetalleSigep',
	ActList:'../../sis_kactivos_fijos/control/DetalleSigep/listarDetalleSigep',
	id_store:'id_detalle_sigep',
	fields: [
		{name:'id_detalle_sigep', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'nro_partida', type: 'string'},
		{name:'c31', type: 'string'},
		{name: 'tipo_reg', type: 'string'},
		{name:'monto_sigep', type: 'numeric'},
		{name:'total_sigep', type: 'numeric'},
		{name:'id_periodo_anexo', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},


	],
	sortInfo:{
		field: 'id_detalle_sigep',
		direction: 'ASC'
	},
	bdel:false,
	bsave:false,
	bedit:false,
	bnew:false,
	btest:false,
	
	repSigep:function(){
    var rec = this.maestro;    
		Ext.Ajax.request({
			url:'../../sis_kactivos_fijos/control/DetalleSigep/repDetaSigep',
			params:{'id_periodo_anexo':rec.id_periodo_anexo,
					'fecha_ini':rec.fecha_ini.dateFormat('d/m/Y'),
					'fecha_fin':rec.fecha_fin.dateFormat('d/m/Y')
			},
			success: this.successExport,
			failure: this.conexionFailure,
			timeout:this.timeout,
			scope:this
		});
	}	
	
	}
)
</script>
