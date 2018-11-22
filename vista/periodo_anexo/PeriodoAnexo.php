<?php
/**
*@package pXP
*@file gen-PeriodoAnexo.php
*@author  (ivaldivia)
*@date 19-10-2018 13:53:35
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.PeriodoAnexo=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;

    	//llama al constructor de la clase padre
		Phx.vista.PeriodoAnexo.superclass.constructor.call(this,config);
		this.init();

		/*AGREGACION DE BOTONES*/

		this.addButton('btnsubir_archivo',
				{
				//grupo: [0],
				text: 'Cargar Archivo',
				iconCls: 'bupload',
				disabled: true,
				handler: this.onButtonUpload,
				tooltip: '<b>Cargar Archivo</b><br/>Carga un Archivo del tipo Excel.'
				}
		);
		this.addButton('btnVentana',
						{
					text: 'Detalle Excel Generado',
				iconCls: 'binfo',
				disabled: true,
				handler: this.onButtonExcel,
				tooltip: '<b>Detalle Archivo Excel</b></br>Muestra el Detalle Excel cargado.'
			}
		);
		this.addButton('btnquitar_archivo',
				{
				//grupo: [0],
				text: 'Eliminar Excel',
				iconCls: 'bcancelfile',
				disabled: true,
				handler: this.onButtonEliminar,
				tooltip: '<b>Eliminar Archivo</b><br/>Eliminar el Archivo Excel.'
				}
		);

		this.addButton('btnInsertar_periodo',
				{
				//grupo: [0],
				text: 'Insertar Periodo',
				iconCls: 'bsubir',
				disabled: true,
				handler: this.onButtonInsertar,
				tooltip: '<b>Insertar Partida Periodo</b><br/>Insertar Partida Periodo.'
				}
		);



		this.addButton('btngenerar_datos', {
				//grupo: [0],
				text: 'Generar Datos',
				iconCls: 'bdocuments',
				disabled: true,
				handler:this.generarAnexos,
				tooltip: '<b>Generar Datos</b><br/>Genera los Datos del Archivo Excel.'
		});

		this.addButton('btnfinalizado', {
				//grupo: [0],
				text: 'Finalizar',
				iconCls: 'bassign',
				disabled: true,
				handler:this.Finalizar,
				tooltip: '<b>Finalizar</b><br/>Finalizar.'
		});


		this.addButton('btnreporte_anexo1',
				{
				//grupo: [0],
				text: 'Anexo 1',
				iconCls: 'blist',
				disabled: true,
				handler: this.onButtonAnex1,
				tooltip: '<b>Agregrar Anexo</b><br/>Agregar Anexo 1.'
				}
		);

		this.addButton('btnreporte_anexo2',
				{
				//grupo: [0],
				text: 'Anexo 2',
				iconCls: 'blist',
				disabled: true,
				handler: this.onButtonAnex2,
				tooltip: '<b>Agregrar Anexo</b><br/>Agregar Anexo 2.'
				}
		);

		this.addButton('btnreporte_anexo3',
				{
				//grupo: [0],
				text: 'Anexo 3',
				iconCls: 'blist',
				disabled: true,
				handler: this.onButtonAnex3,
				tooltip: '<b>Agregrar Anexo</b><br/>Agregrar Anexo 3.'
				}
		);

		this.addButton('btnreporte_anexo4',
				{
				//grupo: [0],
				text: 'Anexo 4',
				iconCls: 'blist',
				disabled: true,
				handler: this.onButtonAnex4,
				tooltip: '<b>Agregrar Anexo</b><br/>Agregrar Anexo 4.'
				}
		);

		this.addButton('btnreporte_general',
				{
				//grupo: [0],
				text: 'Reporte General',
				iconCls: 'bexcel',
				disabled: true,
				handler: this.onButtonReporte,
				tooltip: '<b>Reporte General</b><br/>Reporte General de los Anexos.'
				}
		);


		/*-------------------------------------------------------------------------*/





		this.load({params:{start:0, limit:this.tam_pag}})
	},

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_periodo_anexo'
			},
			type:'Field',
			form:true
		},
		{
			config:{
				name: 'nombre_periodo',
				fieldLabel: 'Nombre Periodo',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:200
			},
				type:'TextField',
				filters:{pfiltro:'perane.nombre_periodo',type:'string'},
				id_grupo:1,
				grid:true,
				form:true,
				bottom_filter:true
		},
		{
			config: {
				name: 'id_gestion',
				fieldLabel: 'Gestion',
				allowBlank: false,
				emptyText: 'Gestion...',
				store: new Ext.data.JsonStore({
					url: '../../sis_parametros/control/Gestion/listarGestion',
					id: 'id_gestion',
					root: 'datos',
					sortInfo: {
						field: 'gestion',
						direction: 'DESC'
					},
					totalProperty: 'total',
					fields: ['id_gestion', 'gestion'],
					remoteSort: true,
					baseParams: {par_filtro: 'gestion'}
				}),
				valueField: 'id_gestion',
				displayField: 'gestion',
				gdisplayField: 'desc_gestion',
				hiddenName: 'id_gestion',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '80%',
				gwidth: 100,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['desc_gestion']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'gestion',type: 'string'},
			grid: true,
			form: true
		},
		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 70,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'perane.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},

		{
			config:{
				name: 'fecha_ini',
				fieldLabel: 'Fecha Inicial',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'perane.fecha_ini',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'fecha_fin',
				fieldLabel: 'Fecha Final',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'perane.fecha_fin',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'observaciones',
				fieldLabel: 'Observaciones',
				allowBlank: true,
				anchor: '80%',
				gwidth: 400,
				maxLength:200
			},
				type:'TextArea',
				filters:{pfiltro:'perane.observaciones',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'estado',
				fieldLabel: 'Estado',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:20,
				renderer: function (value, p, record) {
						if (record.data['estado'] == 'Borrador') {
								return String.format('<div><b><font color="#FB3E3E"><i style="font-size:20px;" class="fa fa-eraser" aria-hidden="true"></i> {0}</font></b></div>', value);
						} else if (record.data['estado'] == 'Cargado') {
								return String.format('<div><b><font color="blue"><i style="font-size:20px;" class="fa fa-upload" aria-hidden="true"></i> {0}</font></b></div>', value);
						} else if (record.data['estado'] == 'Insertado') {
								return String.format('<div><b><font color="#ad00ff"><i style="font-size:20px;" class="fa fa-chevron-up" aria-hidden="true"></i> {0}</font></b></div>', value);
						} else if (record.data['estado'] == 'Generado') {
								return String.format('<div><b><font color="green"><i style="font-size:20px;" class="fa fa-check-circle" aria-hidden="true"></i> {0}</font></b></div>', value);
						} else if (record.data['estado'] == 'Finalizado') {
								return String.format('<div><b><font color="#c400ff"><i style="font-size:20px;" class="fa fa-thumbs-up" aria-hidden="true"></i> {0}</font></b></div>', value);
						}
				}
			},
				type:'Field',
				filters:{pfiltro:'perane.estado',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
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
				filters:{pfiltro:'perane.fecha_reg',type:'date'},
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
				filters:{pfiltro:'perane.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'perane.usuario_ai',type:'string'},
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
				filters:{pfiltro:'perane.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'Periodo Anexo',
	ActSave:'../../sis_kactivos_fijos/control/PeriodoAnexo/insertarPeriodoAnexo',
	ActDel:'../../sis_kactivos_fijos/control/PeriodoAnexo/eliminarPeriodoAnexo',
	ActList:'../../sis_kactivos_fijos/control/PeriodoAnexo/listarPeriodoAnexo',
	id_store:'id_periodo_anexo',
	fields: [
		{name:'id_periodo_anexo', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'nombre_periodo', type: 'string'},
		{name:'fecha_ini', type: 'date',dateFormat:'Y-m-d'},
		{name:'fecha_fin', type: 'date',dateFormat:'Y-m-d'},
		{name:'id_gestion', type: 'numeric'},
		{name:'observaciones', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'desc_gestion', type: 'string'},
		{name:'estado', type: 'string'},

	],
	sortInfo:{
		field: 'id_periodo_anexo',
		direction: 'DESC'
	},
	bdel:true,
	//bsave:true,

	tabsouth :[
		{
			url:'../../../sis_kactivos_fijos/vista/partida_periodo/PartidaPeriodo.php',
			title:'Partida Periodo',
			height:'50%',
			cls:'PartidaPeriodo'
		}
	],

	preparaMenu: function () {
			var rec = this.sm.getSelected();
			var tb = this.tbar;
			//this.getBoton('btnBoleto').enable();
			if(rec !== '') {
				if(rec.data.estado == 'Borrador'){
						this.getBoton('btnreporte_anexo1').disable();
						this.getBoton('btnreporte_anexo2').disable();
						this.getBoton('btnreporte_anexo3').disable();
						this.getBoton('btnreporte_anexo4').disable();
						this.getBoton('btnsubir_archivo').enable();
						this.getBoton('btngenerar_datos').disable();
						this.getBoton('btnInsertar_periodo').disable();
						this.getBoton('btnquitar_archivo').disable();
						this.getBoton('btnVentana').disable();
						this.getBoton('btnfinalizado').disable();
						this.getBoton('btnreporte_general').enable();
						Phx.vista.PeriodoAnexo.superclass.preparaMenu.call(this);
						tb.items.get('b-edit-' + this.idContenedor).enable();
					}

					if(rec.data.estado == 'Cargado'){
							this.getBoton('btnreporte_anexo1').disable();
							this.getBoton('btnreporte_anexo2').disable();
							this.getBoton('btnreporte_anexo3').disable();
							this.getBoton('btnreporte_anexo4').disable();
							this.getBoton('btnsubir_archivo').disable();
							this.getBoton('btngenerar_datos').enable();
							this.getBoton('btnquitar_archivo').enable();
							this.getBoton('btnInsertar_periodo').enable();
							this.getBoton('btnVentana').enable();
							this.getBoton('btnfinalizado').disable();
							this.getBoton('btnreporte_general').enable();
							Phx.vista.PeriodoAnexo.superclass.preparaMenu.call(this);
							tb.items.get('b-edit-' + this.idContenedor).enable();
						}

						if(rec.data.estado == 'Insertado'){
								this.getBoton('btnreporte_anexo1').disable();
								this.getBoton('btnreporte_anexo2').disable();
								this.getBoton('btnreporte_anexo3').disable();
								this.getBoton('btnreporte_anexo4').disable();
								this.getBoton('btnsubir_archivo').disable();
								this.getBoton('btngenerar_datos').enable();
								this.getBoton('btnquitar_archivo').enable();
								this.getBoton('btnInsertar_periodo').disable();
								this.getBoton('btnVentana').enable();
								this.getBoton('btnfinalizado').disable();
								this.getBoton('btnreporte_general').disable();
								Phx.vista.PeriodoAnexo.superclass.preparaMenu.call(this);
								tb.items.get('b-edit-' + this.idContenedor).enable();
							}

						if(rec.data.estado == 'Generado'){
								this.getBoton('btnreporte_anexo1').enable();
								this.getBoton('btnreporte_anexo2').enable();
								this.getBoton('btnreporte_anexo3').enable();
								this.getBoton('btnreporte_anexo4').enable();
								this.getBoton('btnsubir_archivo').disable();
								this.getBoton('btngenerar_datos').disable();
								this.getBoton('btnInsertar_periodo').disable();
								this.getBoton('btnquitar_archivo').disable();
								this.getBoton('btnVentana').enable();
								this.getBoton('btnfinalizado').enable();
								this.getBoton('btnreporte_general').disable();
								Phx.vista.PeriodoAnexo.superclass.preparaMenu.call(this);
								tb.items.get('b-edit-' + this.idContenedor).enable();
							}

							if(rec.data.estado == 'Finalizado'){
									this.getBoton('btnreporte_anexo1').enable();
									this.getBoton('btnreporte_anexo2').enable();
									this.getBoton('btnreporte_anexo3').enable();
									this.getBoton('btnreporte_anexo4').enable();
									this.getBoton('btnsubir_archivo').disable();
									this.getBoton('btngenerar_datos').disable();
									this.getBoton('btnInsertar_periodo').disable();
									this.getBoton('btnquitar_archivo').disable();
									this.getBoton('btnVentana').enable();
									this.getBoton('btnfinalizado').disable();
									this.getBoton('btnreporte_general').enable();
									Phx.vista.PeriodoAnexo.superclass.preparaMenu.call(this);
									tb.items.get('b-edit-' + this.idContenedor).enable();
								}


				}
			},

			liberaMenu : function(){
					var rec = this.sm.getSelected();
							Phx.vista.PeriodoAnexo.superclass.liberaMenu.call(this);
			},

			onButtonUpload: function () {
	        var rec=this.sm.getSelected();
	        Phx.CP.loadWindows('../../../sis_kactivos_fijos/vista/periodo_anexo/PeriodoAnexoExcel.php',
	            '<p style="font-size:15px;">Subir Archivo Excel <i style="color:#005B00; font-size:20px;" class="fa fa-file-excel-o" aria-hidden="true"></i></p>',
	            {
	                modal:true,
	                width:450,
	                height:200
	            },rec.data,this.idContenedor,'ConsumoPeriodoAnexo')
	    },


	onButtonAnex1: function() {
	 //var titulo = 'Anexo 1';
	 var rec = {maestro: this.sm.getSelected().data,anexo:'anexo1', codigo:'1'}
	 rec.an1='especifico';
	 Phx.CP.loadWindows('../../../sis_kactivos_fijos/vista/anexo/Anexo.php',
			 '<center style="font-size:20px;">REGISTRAR <b style="color:green; font-size:20px;">ANEXO 1</b></center>',
			 {
					 width:1200,
					 height:600
			 },

			 rec,
			 this.idContenedor,
			 'Anexo');
	},

	onButtonAnex2: function() {

	 var rec = {maestro: this.sm.getSelected().data,anexo:'anexo2', codigo:'2'}
	 rec.an2='especifico';
	 Phx.CP.loadWindows('../../../sis_kactivos_fijos/vista/anexo/Anexo2.php',
			 '<center style="font-size:20px;">REGISTRAR <b style="color:red; font-size:20px;">ANEXO 2</b></center>',
			 {
					 width:1200,
					 height:600
			 },
			 rec,
			 this.idContenedor,
			 'Anexo');
	},

	onButtonAnex3: function() {

	 var rec = {maestro: this.sm.getSelected().data,anexo:'anexo3',codigo:'3'}
	 rec.an3='especifico';
	 Phx.CP.loadWindows('../../../sis_kactivos_fijos/vista/anexo/Anexo3.php',
			 '<center style="font-size:20px;">REGISTRAR <b style="color:#FF9300; font-size:20px;">ANEXO 3</b></center>',
			 {
					 width:1200,
					 height:600
			 },
			 rec,
			 this.idContenedor,
			 'Anexo');
	},

	onButtonAnex4: function() {

	 var rec = {maestro: this.sm.getSelected().data,anexo:'anexo4',codigo:'4'}
	 rec.an4='especifico';
	 Phx.CP.loadWindows('../../../sis_kactivos_fijos/vista/anexo/Anexo4.php',
			 '<center style="font-size:20px;">REGISTRAR <b style="color:#955CFF; font-size:20px;">ANEXO 4</b></center>',
			 {
					 width:1200,
					 height:600
			 },
			 rec,
			 this.idContenedor,
			 'Anexo');
	},


	onButtonEliminar: function(){
		Phx.CP.loadingShow();
		var d = this.sm.getSelected().data;
		Ext.Ajax.request({
						url:'../../sis_kactivos_fijos/control/PeriodoAnexo/eliminarArchivoExcel',
						params:{id_periodo_anexo:d.id_periodo_anexo},
						success:this.successAnularExcel,
						failure: this.conexionFailure,
						timeout:this.timeout,
						scope:this
		});
	},
	successAnularExcel:function(resp){
			Phx.CP.loadingHide();
			var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
			if(!reg.ROOT.error){
					this.reload();
			}
	},

	Finalizar: function(){
		if(confirm('Esta seguro de finalizar')){
		Phx.CP.loadingShow();
		var d = this.sm.getSelected().data;
		Ext.Ajax.request({
						url:'../../sis_kactivos_fijos/control/PeriodoAnexo/Finalizar',
						params:{id_periodo_anexo:d.id_periodo_anexo},
						success:this.successFinalizar,
						failure: this.conexionFailure,
						timeout:this.timeout,
						scope:this
		});
	}},
	successFinalizar:function(resp){
			Phx.CP.loadingHide();
			var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
			if(!reg.ROOT.error){
					this.reload();
			}
	},

	onButtonInsertar: function(){
		Phx.CP.loadingShow();
		var d = this.sm.getSelected().data;
		Ext.Ajax.request({
						url:'../../sis_kactivos_fijos/control/PeriodoAnexo/insertarPartidaPeriodo',
						params:{id_periodo_anexo:d.id_periodo_anexo},
						success:this.successInsertar,
						failure: this.conexionFailure,
						timeout:this.timeout,
						scope:this
		});
	},
	successInsertar:function(resp){
			Phx.CP.loadingHide();
			var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
			if(!reg.ROOT.error){
					this.reload();
			}
	},

	onButtonReporte: function() {
		Phx.CP.loadingShow();
		var d = this.sm.getSelected().data;
		console.log('codigo:',d.id_periodo_anexo);

		Ext.Ajax.request({
						url:'../../sis_kactivos_fijos/control/PeriodoAnexo/reporteGeneral',
						params:{//id_anexo:d.id_anexo,
										id_periodo_anexo:d.id_periodo_anexo,
										nombre_periodo:d.nombre_periodo,
										observaciones:d.observaciones,
										fecha_ini:d.fecha_ini.dateFormat('d/m/Y'),
										fecha_fin:d.fecha_fin.dateFormat('d/m/Y'),
										desc_gestion: d.desc_gestion

										/*numero:d.numero,
										fecha:d.fecha.dateFormat('d/m/Y'),
										fecha_ini:d.fecha_ini.dateFormat('d/m/Y'),
										fecha_fin:d.fecha_fin.dateFormat('d/m/Y'),
										codigo:d.codigo,
										ruta:d.ruta,
										office_id:d.office_id,
										codigo_largo:d.codigo_largo*/

									},
						success: this.successExport,
						failure: this.conexionFailure,
						timeout:this.timeout,
						scope:this
		});
		console.log('LLEGA EL DATO',d.observaciones);
	},


	onButtonExcel: function(){

			//Phx.vista.ArchivoAcmDet.superclass.onButtonAcm.call(this);
	            var rec = {maestro: this.sm.getSelected().data}
	            rec.excel='especifico';
	            console.log('VALOR',	rec.excel);
	            Phx.CP.loadWindows('../../../sis_kactivos_fijos/vista/detalle_sigep/DetalleSigep.php',
	                'Detalle Excel generado',
	                {
	                    width:1200,
	                    height:600
	                },
	                rec,
	                this.idContenedor,
	                'DetalleSigep');

	        },

					onButtonEdit : function () {
				    Phx.vista.PeriodoAnexo.superclass.onButtonEdit.call(this);


			    },
	generarAnexos: function () {
		var rec=this.sm.getSelected();
	        Phx.CP.loadingShow();
	        Ext.Ajax.request({
	            url: '../../sis_kactivos_fijos/control/Anexo/generarAnexos',
	            params: {
	                id_periodo_anexo: rec.data.id_periodo_anexo,
	                fecha_ini: rec.data.fecha_ini.toLocaleDateString(),
	                fecha_fin: rec.data.fecha_fin.toLocaleDateString(),
	                id_gestion: rec.data.id_gestion
	            },
	            success: this.successRep,
	            failure: this.conexionFailure,
	            timeout: this.timeout,
	            scope: this
	        });
	},
	successRep:function(resp){
	    Phx.CP.loadingHide();
	    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
	    if(!reg.ROOT.error){
	        this.reload();
	    }else{
	        alert('Ocurrió un error durante el proceso')
	    }
	},
	onButtonEdit : function () {
		Phx.vista.PeriodoAnexo.superclass.onButtonEdit.call(this);		
		this.Cmp.estado.hide();
	}	

})
</script>
