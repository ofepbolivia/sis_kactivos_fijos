<?php
/**
*@package pXP
*@file ClasificacionPartida.php
*@author  (BVP)
*@date 29-10-2018 09:34:29
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ClasificacionPartida=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		this.initButtons=['-','Gestión: ',this.cmbGestion,'-'];
    	//llama al constructor de la clase padre
		Phx.vista.ClasificacionPartida.superclass.constructor.call(this,config);		
        this.addButton('btnMigClaPar', {            
            text: 'Migrar Partidas',
            iconCls: 'bchecklist',
            disabled: false,
            handler: this.MigrarPartidas,
            tooltip: '<b>Migrar Partidas</b><br/>Duplicar las partidas para la siguiente gestión'
        });		
		this.init();
		this.cmbGestion.on('select', this.capturarEventos, this);
		this.iniciarEventos();		
	},
	cmbGestion:new Ext.form.ComboBox({
				fieldLabel: 'Gestion',
				allowBlank: true,
				emptyText:'Gestion...',
				store:new Ext.data.JsonStore(
				{
					url: '../../sis_parametros/control/Gestion/listarGestion',
					id: 'id_gestion',
					root: 'datos',
					sortInfo:{
						field: 'gestion',
						direction: 'DESC'
					},
					totalProperty: 'total',
					fields: ['id_gestion','gestion'],
					// turn on remote sorting
					remoteSort: true,
					baseParams:{par_filtro:'gestion'}
				}),
				valueField: 'id_gestion',
				triggerAction: 'all',
				displayField: 'gestion',
			    hiddenName: 'id_gestion',
    			mode:'remote',
				pageSize:50,
				queryDelay:500,
				listWidth:'280',
				width:80
			}),
    capturarEventos: function () {  
    	if (this.maestro==undefined){
    		alert('Seleccione una clasificacion');
    		}else{	
        this.store.baseParams.id_gestion=this.cmbGestion.getValue();
        this.load({params:{start:0, limit:this.tam_pag}});
       }
    },
    iniciarEventos : function () {    	    			 			
	this.getComponente('id_gestion').on('select',function(c,r,n){		
	this.Cmp.id_partida.reset();
	this.Cmp.id_partida.modificado = true;																						 		     
    this.Cmp.id_partida.store.baseParams.id_gestion=r.data.id_gestion;            	           
    	              				    	          		    		    		    		    					    		    		 																												
	 },this);	 		 		             					
	},    				
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_clasificacion_partida'
			},
			type:'Field',
			form:true 
		},
		{
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_clasificacion'
			},
			type:'Field',
			form:true 
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
				gdisplayField: 'gestion',
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
			grid: false,
			form: true
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
	 				renderer: function(value, p, record){
		            	   return String.format('{0}',record.data['dec_par']);
	                },		            
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
				name: 'tipo_reg',
				fieldLabel: 'Tipo',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				renderer:function (value, p, record){					
					if(value == 'directo'){
						return String.format('<font color="blue">{0}</font>', value);
					}
					else{
						return String.format('<font color="red">{0}</font>', value);
					}
					
				},
	   			maxLength:10
			},
			type:'TextField',
			id_grupo:1,
			grid:true,
			form:false			
		},
	    {
			config:{
				name: 'gestion',
				fieldLabel: 'Gestion',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100	   			
			},
			type:'NumberField',
			id_grupo:1,
			grid:true,
			form:false			
		},		
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: '',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'clapa.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'clapa.usuario_ai',type:'string'},
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
				filters:{pfiltro:'clapa.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
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
				filters:{pfiltro:'clapa.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'Variables',
	ActSave:'../../sis_kactivos_fijos/control/ClasificacionVariable/insertarClasificacionPartida',
	ActDel:'../../sis_kactivos_fijos/control/ClasificacionVariable/eliminarClasificacionPartida',
	ActList:'../../sis_kactivos_fijos/control/ClasificacionVariable/listarClasificacionPartida',
	id_store:'id_clasificacion_partida',
	fields: [
		{name:'id_clasificacion_partida', type: 'numeric'},
		{name:'id_clasificacion', type: 'numeric'},
		{name:'id_partida', type: 'string'},
		{name:'id_gestion', type: 'numeric'},
		{name:'gestion', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		'dec_par',
		'tipo_reg',
		
		
	],
	sortInfo:{
		field: 'id_clasificacion_partida',
		direction: 'ASC'
	},
	bdel:true,
	bsave:false,
	btest:false,
	onReloadPage:function(m,a,b){		
		this.maestro=m;		
		this.store.baseParams={id_clasificacion:this.maestro.id_clasificacion};			
		this.load({params:{start:0, limit:50}})
		
	},
	
	loadValoresIniciales:function(){				
		Phx.vista.ClasificacionPartida.superclass.loadValoresIniciales.call(this);
		this.getComponente('id_clasificacion').setValue(this.maestro.id_clasificacion);							
	},
/*	preparaMenu:function(){
		var rec = this.sm.getSelected();
		var tb = this.tbar;
		if(rec.data.tipo_reg != 'indirecto'){
			Phx.vista.ClasificacionPartida.superclass.preparaMenu.call(this);
		}
		else{
			 this.getBoton('edit').disable();
			 this.getBoton('del').disable();
		}
	},*/

    MigrarPartidas: function () {
        if (this.cmbGestion.getValue()) {
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_kactivos_fijos/control/ClasificacionVariable/clonarClasificacionPartidaGestion',
                params: {
                    id_gestion: this.cmbGestion.getValue()
                },
                success: this.successRep,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        }
        else {
            alert('Primero debe selecionar la gestion origen');
        }

    },
   successRep:function(resp){
        Phx.CP.loadingHide();
        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
        if(!reg.ROOT.error){
            this.reload();
            alert(reg.ROOT.datos.observaciones)
        }else{
            alert('Ocurrió un error durante el proceso')
        }
	}
    	    	
})
</script>
		
		