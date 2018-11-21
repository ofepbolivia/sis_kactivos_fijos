<?php
/**
*@package pXP
*@file gen-PartidaPeriodo.php
*@author  (ivaldivia)
*@date 19-10-2018 14:37:17
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.PartidaPeriodo=Ext.extend(Phx.gridInterfaz,{


	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.PartidaPeriodo.superclass.constructor.call(this,config);
		this.init();
		//this.reload();
	//	this.load({params:{start:0, limit:this.tam_pag}})

	},


	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_partida_periodo'
			},
			type:'Field',
			form:true
		},
     	{
            config: {
                name: 'id_partida',
                fieldLabel: 'Partidas',
                allowBlank: true,
                emptyText: 'Elija una opción...',
                store: new Ext.data.JsonStore({
                    url: '../../sis_kactivos_fijos/control/ClasificacionVariable/listarPartidas',
                    id: 'id_partida',
                    root: 'datos',
                    sortInfo: {
                        field: 'id_partida',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_partida', 'nombre_partida', 'codigo','sw_movimiento','tipo','gestion'],
                    remoteSort: true,
                    baseParams: {
                        par_filtro: 'par.nombre_partida#codigo'
                    }
                }),
                tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;">({codigo}) {nombre_partida}- {gestion}</p><p>Tipo: {sw_movimiento}<p>Rubro: {tipo}</div></tpl>',
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
                anchor: '90%',
                gwidth: 250,
                minChars: 2,
				tpl:'<tpl for="."><div class="x-combo-list-item"><p style="color:green;">({codigo}) {nombre_partida}- {gestion}</p><p>Tipo: {sw_movimiento}<p>Rubro: {tipo}</div></tpl>',
		        renderer : function(value, p, record) {
		          if(record.data.tipo_reg != 'summary'){
		            return  String.format("<b style='color: red; font-weight:bold; font-size:12px;'>"+record.data['desc_codigo']+" </b>"+"- "+"<b style='font-size:12px; color:blue;'>"+record.data['desc_partida']+"</b>");
		
		          }
		          else{
		            return '<hr><b><p style="font-size:20px; float:right; color:green; border-top:2px;">Totales: &nbsp;&nbsp; </p></b>';
		          }
		
		
		        }               
            },
            type: 'ComboBox',
            id_grupo: 0,
            filters: {
                pfiltro: 'par.nombre_partida#par.codigo',
                type: 'string'
            },
            grid: true,
            form: true,
            bottom_filter:true
        },
		{
			config:{
				name: 'importe_sigep',
				fieldLabel: 'Importe Sigep',
				allowBlank: true,
				anchor: '80%',
				gwidth: 160,
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style="color:#004DFF; text-align:right; font-size:12px; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0.000,00/i'));
					}

					else{
						return  String.format('<hr><div style="font-size:20px; float:right; color:#004DFF;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_sigep,'0.000,00/i'));
					}
				},
				maxLength:1310722
			},
				type:'NumberField',
				filters:{pfiltro:'parper.importe_sigep',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'importe_anexo1',
				fieldLabel: 'Importe Anexo 1',
				allowBlank: true,
				anchor: '80%',
				gwidth: 160,
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style="color:green; text-align:right; font-size:12px; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0.000,00/i'));
					}

					else{
						return  String.format('<hr><div style="font-size:20px; float:right; color:green;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_anex1,'0.000,00/i'));
					}
				},
				maxLength:1310722
			},
				type:'NumberField',
				filters:{pfiltro:'parper.importe_anexo1',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'importe_anexo2',
				fieldLabel: 'Importe Anexo 2',
				allowBlank: true,
				anchor: '80%',
				gwidth: 160,
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style="color:red; text-align:right; font-size:12px; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0.000,00/i'));
					}

					else{
						return  String.format('<hr><div style="font-size:20px; float:right; color:red;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_anex2,'0.000,00/i'));
					}
				},
				maxLength:1310722
			},
				type:'NumberField',
				filters:{pfiltro:'parper.importe_anexo2',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'importe_anexo3',
				fieldLabel: 'Importe Anexo 3',
				allowBlank: true,
				anchor: '80%',
				gwidth: 160,
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style="color:#FF9300; text-align:right; font-size:12px; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0.000,00/i'));
					}

					else{
						return  String.format('<hr><div style="font-size:20px; float:right; color:#FF9300"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_anex3,'0.000,00/i'));
					}
				},
				maxLength:1310722
			},
				type:'NumberField',
				filters:{pfiltro:'parper.importe_anexo3',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'importe_anexo4',
				fieldLabel: 'Importe Anexo 4',
				allowBlank: true,
				anchor: '80%',
				gwidth: 160,
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style="color:#955CFF; text-align:right; font-size:12px; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0.000,00/i'));
					}

					else{
						return  String.format('<hr><div style="font-size:20px; float:right; color:#955CFF;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_anex4,'0.000,00/i'));
					}
				},
				maxLength:1310722
			},
				type:'NumberField',
				filters:{pfiltro:'parper.importe_anexo4',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'importe_anexo5',
				fieldLabel: 'Importe Anexo 5',
				allowBlank: true,
				anchor: '80%',
				gwidth: 160,
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style=" text-align:right; font-size:12px; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0.000,00/i'));
					}

					else{
						return  String.format('<hr><div style="font-size:20px; float:right; "><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_anex5,'0.000,00/i'));
					}
				},
				maxLength:1310722
			},
				type:'NumberField',
				filters:{pfiltro:'parper.importe_anexo5',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'importe_total',
				fieldLabel: 'Importe Total',
				allowBlank: true,
				anchor: '80%',
				gwidth: 160,
				renderer:function (value,p,record){
					if(record.data.tipo_reg != 'summary'){
						return  String.format('<div style="color:#9B00FF; text-align:right; font-size:12px; font-weight:bold;"><b>{0}</b></div>', Ext.util.Format.number(value,'0.000,00/i'));
					}

					else{
						return  String.format('<hr><div style="font-size:20px; float:right; color:#9B00FF;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_importe,'0.000,00/i'));
					}
				},
				maxLength:1310722
			},
				type:'NumberField',
				filters:{pfiltro:'parper.importe_total',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
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
				filters:{pfiltro:'parper.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config: {
				name: 'id_periodo_anexo',
				fieldLabel: 'Periodo Anexo',
				allowBlank: true,
				emptyText: 'Seleccione al Periodo...',
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
					baseParams: {par_filtro: 'nombre_periodo'}
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
			filters: {pfiltro: 'nombre_periodo',type: 'string'},
			grid: false,
			form: true
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
				filters:{pfiltro:'parper.fecha_reg',type:'date'},
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
				filters:{pfiltro:'parper.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'parper.usuario_ai',type:'string'},
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
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'parper.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'Partida Periodo',
	ActSave:'../../sis_kactivos_fijos/control/PartidaPeriodo/insertarPartidaPeriodo',
	ActDel:'../../sis_kactivos_fijos/control/PartidaPeriodo/eliminarPartidaPeriodo',
	ActList:'../../sis_kactivos_fijos/control/PartidaPeriodo/listarPartidaPeriodo',
	id_store:'id_partida_periodo',
	fields: [
		{name:'id_partida_periodo', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'id_periodo_anexo', type: 'numeric'},
		{name:'id_partida', type: 'numeric'},
		{name: 'tipo_reg', type: 'string'},
		{name:'importe_sigep', type: 'numeric'},
		{name:'total_sigep', type: 'numeric'},
		{name:'importe_anexo1', type: 'numeric'},
		{name:'total_anex1', type: 'numeric'},
		{name:'importe_anexo2', type: 'numeric'},
		{name:'total_anex2', type: 'numeric'},
		{name:'importe_anexo3', type: 'numeric'},
		{name:'total_anex3', type: 'numeric'},
		{name:'importe_anexo4', type: 'numeric'},
		{name:'total_anex4', type: 'numeric'},
		{name:'importe_anexo5', type: 'numeric'},
		{name:'total_anex5', type: 'numeric'},
		{name:'importe_total', type: 'numeric'},
		{name:'total_importe', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'desc_partida', type: 'string'},
		{name:'desc_codigo', type: 'string'},


	],
	sortInfo:{
		field: 'desc_codigo',
		direction: 'ASC'
	},
	bdel:true,
	//bsave:true,

	onReloadPage:function (m) {
		this.maestro = m;
		//this.store.baseParams=m;
		this.store.baseParams = {id_periodo_anexo:this.maestro.id_periodo_anexo};
		this.Cmp.id_partida.store.setBaseParam('gestion',this.maestro.id_gestion);
		this.Cmp.id_partida.modificado = true;
		this.Cmp.id_partida.reset();
		this.load( { params: { start:0, limit: this.tam_pag } });
	},


	loadValoresIniciales:function () {
		console.log("LLEGA",this.Cmp.id_periodo_anexo);
		this.Cmp.id_periodo_anexo.setValue(this.maestro.id_periodo_anexo);
		Phx.vista.PartidaPeriodo.superclass.loadValoresIniciales.call(this);
	},
	onButtonNew : function () {
		Phx.vista.PartidaPeriodo.superclass.onButtonNew.call(this);
		this.Cmp.id_partida.store.baseParams.id_gestion=this.maestro.id_gestion;
		this.Cmp.importe_anexo1.hide();
		this.Cmp.importe_anexo2.hide();
		this.Cmp.importe_anexo3.hide();
		this.Cmp.importe_anexo4.hide();
		this.Cmp.importe_anexo5.hide();
		this.Cmp.importe_total.hide();
		this.Cmp.id_periodo_anexo.hide();

	}	

	}
);
</script>
