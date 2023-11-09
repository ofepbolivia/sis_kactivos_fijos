<?php
/**
*@package pXP
*@file gen-SistemaDist.php
*@author  (rarteaga)
*@date 20-09-2011 10:22:05
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/
include_once ('../../../media/styles.php');
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.MovimientoPrincipal = {
    bsave:false,
    require:'../../../sis_kactivos_fijos/vista/movimiento/Movimiento.php',
    requireclase:'Phx.vista.Movimiento',
    title:'Movimientos',
   //fRnk: anadir html to pdf-> title2:'<span style="font-size: 12px">Tipo de Proceso: "Todos"</span>',
    nombreVista: 'MovimientoPrincipal',

    gruposBarraTareas:[
		{name:'Todos',title:'<h1 align="center"><i class="fa fa-bars"></i> Todos</h1>',grupo:0,height:0},
	   	{name:'Altas',title:'<h1 align="center"><i class="fa fa-thumbs-o-up"></i> Altas</h1>',grupo:1,height:0},
       	{name:'Bajas/Retiros',title:'<H1 align="center"><i class="fa fa-thumbs-o-down"></i> Bajas y Retiros</h1>',grupo:2,height:0},
       	{name:'Revalorizaciones/Mejoras',title:'<H1 align="center"><i class="fa fa-plus-circle"></i> Revaloriz/Ajustes</h1>',grupo:3,height:0},
       	{name:'Asignaciones/Devoluciones',title:'<H1 align="center"><i class="fa fa-user-plus"></i> Asig/Devol/Transf</h1>',grupo:4,height:0},
       	{name:'Depreciaciones',title:'<H1 align="center"><i class="fa fa-calculator"></i> Depreciaciones</h1>',grupo:5,height:0},
        {name:'Desglose/División',title:'<H1 align="center"><i class="fa fa-calculator"></i> Desglose, división e intercambio de partes</h1>',grupo:6,height:0}
    ],

    actualizarSegunTab: function(name, indice){
    	if(indice==0){//fRnk: títulos específicos de los reportes de los movimientos HR01314
    		this.filterMov='%';
            this.title='Movimientos de Activos Fijos<br><br><span style="font-size: 12px">Tipo de Proceso: "Todos"</span>';
    	} else if(indice==1){
    		this.filterMov='alta';
            this.title='Movimientos de Activos Fijos<br><br><span style="font-size: 12px">Tipo de Proceso: "Altas"</span>';
    	} else if(indice==2){
    		this.filterMov='baja,retiro';
            this.title='Movimientos de Activos Fijos<br><br><span style="font-size: 12px">Tipo de Proceso: "Bajas y Retiros"</span>';
    	} else if(indice==3){
    		this.filterMov='reval,ajuste,mejora,transito';
            this.title='Movimientos de Activos Fijos<br><br><span style="font-size: 12px">Tipo de Proceso: "Revalorizaciones y Ajustes"</span>';
    	} else if(indice==4){
    		this.filterMov='asig,devol,transf,tranfdep';
            this.title='Movimientos de Activos Fijos<br><br><span style="font-size: 12px">Tipo de Proceso: "Asignaciones, Devoluciones y Transferencias"</span>';
            this.getBoton('btnReporte').setVisible(false);
    	} else if(indice==5){
    		this.filterMov='deprec,actua';
            this.title='Movimientos de Activos Fijos<br><br><span style="font-size: 12px">Tipo de Proceso: "Depreciaciones"</span>';
    	} else if(indice==6){
            this.filterMov='divis,desgl,intpar';
            this.title='Movimientos de Activos Fijos<br><br><span style="font-size: 12px">Tipo de Proceso: "Desglose, División e intercambio de partes"</span>';
        }
    	this.store.baseParams.cod_movimiento = this.filterMov;
        this.store.baseParams.id_movimiento = this.maestro.lnk_id_movimiento;
        this.store.baseParams.tipo_interfaz = this.nombreVista;
    	//this.getBoton('btnReporte').show();
    	this.load({params:{start:0, limit:this.tam_pag}});
    },
    bnewGroups: [0,1,2,3,4,5,6],
    beditGroups: [0,1,2,3,4,5,6],
    bdelGroups:  [0,1,2,3,4,5,6],
    bactGroups:  [0,1,2,3,4,5,6],
    btestGroups: [0,1,2,3,4,5,6],
    bexcelGroups: [0,1,2,3,4,5,6],



    constructor: function(config) {

        Phx.vista.MovimientoPrincipal.superclass.constructor.call(this,config);
        this.maestro = config;
        this.init();
        this.load({
            params:{
                start:0,
                limit:this.tam_pag,
                id_movimiento: this.maestro.lnk_id_movimiento,
                tipo_interfaz: this.nombreVista
            }
        });




        me = this;
        this.addButton('btnAsignacion',
            {
                iconCls: 'bexcel',
                xtype: 'splitbutton',
                grupo: [0,4],
                tooltip: '<b>Reporte de Asig./Trans./Devol. A.F.</b><br>Reporte de Asignación, Transferencia, Devolución de Activos Fijos.',
                text: 'Reporte A/T/D',
                //handler: this.onButtonExcel,
                argument: {
                    'news': true,
                    def: 'reset'
                },
                scope: me,
                menu: [{
                    text: 'Reporte CSV',
                    iconCls: 'bexcel',
                    argument: {
                        'news': true,
                        def: 'csv'
                    },
                    handler: me.onButtonATDExcel,
                    scope: me
                }, {
                    text: 'Reporte PDF',
                    iconCls: 'bpdf',
                    argument: {
                        'news': true,
                        def: 'pdf'
                    },
                    handler: me.onButtonATDPdf,
                    scope: me
                }]
            }
        );

        //Evento para store de motivos
        this.Cmp.id_movimiento_motivo.getStore().on('load', function(store, records, options){
            if(store.getCount()==1){
                var data = records[0].id;
                this.Cmp.id_movimiento_motivo.setValue(records[0].id);
            }
        },this);

        //Add handler to id_cat_movimiento field
        this.Cmp.id_cat_movimiento.on('select', function(cmp,rec,el){
            //Habilita los campos
        	this.habilitarCampos(rec.data.codigo);
        	this.Cmp.id_movimiento_motivo.reset();
            this.Cmp.id_movimiento_motivo.modificado=true;
            this.Cmp.id_movimiento_motivo.store.baseParams.id_cat_movimiento=rec.data.id_catalogo;

             //Carga el store del combo de motivos
            this.Cmp.id_movimiento_motivo.getStore().load({
                params:{
                    start:0,
                    limit:this.tam_pag
                }
            });
            //(f.e.a) deshabilitamos el deposito
            this.Cmp.id_deposito.setDisabled(true);
            //(f.e.a) definimos funcionario activos para asignacion y transferencia e inactivos para devoluciones y transferencias de  inactivos a activos
            if(rec.data.codigo == 'asig'){
                this.Cmp.id_funcionario.store.baseParams.estado_func = 'activo';
            }else if(rec.data.codigo == 'transf'){
                this.Cmp.id_funcionario.store.baseParams.estado_func = 'act_desc';
                this.Cmp.id_funcionario_dest.store.baseParams.estado_func = 'activo';
            }else if(rec.data.codigo == 'devol'){
                this.Cmp.id_funcionario.store.baseParams.estado_func = 'act_desc';
            }
        }, this);
        //(f.e.a)Que el campo deposito dependa de depto.
        this.Cmp.id_depto.on('select', function(cmp,rec,el){
            this.Cmp.id_deposito.setDisabled(false);
            this.Cmp.id_deposito.reset();
            this.Cmp.id_deposito.modificado=true;
            this.Cmp.id_deposito.store.baseParams.id_depto=rec.data.id_depto;
        }, this);

        //Add handler to id_cat_movimiento field
        this.Cmp.id_depto_dest.on('select', function(cmp,rec,el){
        	this.Cmp.id_deposito_dest.reset();
            this.Cmp.id_deposito_dest.modificado=true;
            this.Cmp.id_deposito_dest.store.baseParams.id_depto=rec.data.id_depto;
        }, this);

        this.addButton('ant_estado',{grupo: [0,1,2,3,4,5,6],argument: {estado: 'anterior'},text:'Anterior',iconCls: 'batras',disabled:true,handler:this.antEstado,tooltip: '<b>Pasar al Anterior Estado</b>'});
        this.addButton('sig_estado',{text:'Siguiente',iconCls: 'badelante',disabled:true,handler:this.sigEstado,tooltip: '<b>Pasar al Siguiente Estado</b><br/>Si el estado requiere Firma Digital, primero debe firmar el documento para que se habilite.',grupo: [0,1,2,3,4,5,6],});
		this.addButton('diagrama_gantt',{text:'Gant',iconCls: 'bgantt',disabled:true,handler:diagramGantt,tooltip: '<b>Diagrama Gantt del proceso</b>',grupo: [0,1,2,3,4,5,6],});
		this.addButton('btnChequeoDocumentosWf',
            {
                text: 'Doc. Movimiento',
                iconCls: 'bchecklist',
                disabled: true,
                handler: this.loadCheckDocumentosPlanWf,
                tooltip: '<b>Documentos de la Solicitud</b><br/>Subir los documetos requeridos en la solicitud seleccionada.',
                grupo: [0,1,2,3,4,5,6],
            }
        );

        this.addButton('btnChequeoDocumentosAF',{
          grupo:[0,1,2,3,4], text: 'Doc. Proceso de Compra ',
          iconCls: 'bchecklist',
          disabled: true,
          handler: this.loadCheckDocumentosSol,
          tooltip: '<b>Documentos del Proceso</b><br/>Subir los documetos requeridos en el proceso seleccionada.'
        });

        //fRnk: add button for Firma Digital
        this.addButton('btnFirmaDigital',{
            grupo:[0,1,2,3,4],
            text: 'Firma Digital',
            iconCls: 'blist-firma-digital',
            disabled: true,
            handler: this.onOpenPendientesFirmaD,
            tooltip: '<b>Pendientes de Firma Digital</b><br/>Procesos pendientes de Firma Digital.'
        });

        function diagramGantt(){
            var data=this.sm.getSelected().data.id_proceso_wf;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_workflow/control/ProcesoWf/diagramaGanttTramite',
                params:{'id_proceso_wf':data},
                success:this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        }



    },

	onButtonATDExcel: function () {

			var rec=this.sm.getSelected();
			Phx.CP.loadingShow();
			Ext.Ajax.request({
				url:'../../sis_kactivos_fijos/control/Movimiento/generarReporteAsig_Trans_DevAFXls',
				params:{'id_movimiento':rec.data.id_movimiento},
				success: this.successExport,
				failure: this.conexionFailure,
				timeout:this.timeout,
				scope:this
			});

	},

    onButtonATDPdf:function(){
        var rec=this.sm.getSelected();
        Phx.CP.loadingShow();
        Ext.Ajax.request({
            url:'../../sis_kactivos_fijos/control/Movimiento/generarReporteMovimientoUpdate',
            params:{
                'id_movimiento':rec.data.id_movimiento,
                'num_tramite':rec.data.num_tramite,
                'nombre_archivo':rec.data.nombre_archivo,
                'firma_digital':rec.data.firma_digital
            },
            success: this.successExport,
            failure: this.conexionFailure,
            timeout:this.timeout,
            scope:this
        });
    },


    openMovimientos: function(){
    	Phx.CP.loadWindows('../../../sis_kactivos_fijos/vista/movimiento/MovimientoGral.php',
            'Movimientos',
            {
                width:'50%',
                height:'85%'
            },
            {},
            this.idContenedor,
            'MovimientoGral'
        )
    },

    habilitarCampos: function(mov, estado='borrador'){
        this.Cmp.id_cat_movimiento.disable();
        if(estado!='borrador'){ //fRnk: adicionado para evitar la edición en un estado diferente de borrador HR1436
            this.Cmp.id_cat_movimiento.disable();
            this.Cmp.fecha_mov.disable();
            this.Cmp.glosa.disable();
            this.Cmp.id_depto.disable();
            this.Cmp.direccion.disable();
            this.Cmp.fecha_hasta.disable();
            this.Cmp.id_funcionario.disable();
            this.Cmp.id_oficina.disable();
            this.Cmp.id_persona.disable();
            this.Cmp.id_depto_dest.disable();
            this.Cmp.id_deposito_dest.disable();
            this.Cmp.id_funcionario_dest.disable();
            this.Cmp.id_movimiento_motivo.disable();
            this.Cmp.prestamo.disable();
            this.Cmp.fecha_dev_prestamo.disable();
            this.Cmp.tipo_asig.disable();
            this.Cmp.id_deposito.disable();
            this.Cmp.tipo_movimiento.disable();
            this.Cmp.nro_documento.disable();
            this.Cmp.tipo_documento.disable();
            this.Cmp.tipo_drepeciacion.disable();
        }else{
            this.Cmp.fecha_mov.enable();
            this.Cmp.glosa.enable();
            this.Cmp.id_depto.enable();
            this.Cmp.direccion.enable();
            this.Cmp.fecha_hasta.enable();
            this.Cmp.id_funcionario.enable();
            this.Cmp.id_oficina.enable();
            this.Cmp.id_persona.enable();
            this.Cmp.id_depto_dest.enable();
            this.Cmp.id_deposito_dest.enable();
            this.Cmp.id_funcionario_dest.enable();
            this.Cmp.id_movimiento_motivo.enable();
            this.Cmp.prestamo.enable();
            this.Cmp.fecha_dev_prestamo.enable();
            this.Cmp.tipo_asig.enable();
            this.Cmp.id_deposito.enable();
            this.Cmp.tipo_movimiento.enable();
            this.Cmp.nro_documento.enable();
            this.Cmp.tipo_documento.enable();
            if(dataDep!=null){//fRnk: para resolver problema de la HR01436
                var sel=-1; var indice=0;
                dataDep.forEach((item) => {if(item[1]==this.Cmp.tipo_drepeciacion.value) sel=indice;indice++;})
                if(sel!=-1){
                    this.Cmp.tipo_drepeciacion.value=dataDep[sel][0];
                }
            }
            this.Cmp.tipo_drepeciacion.enable();
        }
    	var swTipoMovimiento=false,swDeposito=false,swDireccion=false,swFechaHasta=false,swFuncionario=false,swOficina=false,swPersona=false,h=600,w=600,swDeptoDest=false,swDepositoDest=false,swFuncionarioDest=false,swCatMovMotivo=false,swPrestamo=false,swTipoAsig=false, swNroDoc=false,swTiDoc=false,swTipoDepre=false;
      console.log('MUESTRA LO SIGUIENTE:',this.Cmp.glosa);
    	//Muesta y habilita los campos basicos
    	this.Cmp.fecha_mov.setVisible(true);
    	this.Cmp.glosa.setVisible(true);
        this.Cmp.id_depto.setVisible(true);


    	this.form.getForm().clearInvalid();

    	//Muestra y habilita los campos especificos por tipo de movimiento
    	if(mov=='alta'){
    		swDireccion=false;
    		swFechaHasta=false;
    		swFuncionario=false;
    		swOficina=false;
    		swPersona=false;
    		swDeptoDest=false;
    		swDepositoDest=false;
    		swFuncionarioDest=false;
    		swCatMovMotivo=true;
        swTipoMovimiento=true;
    		h=300;
    	} else if(mov=='asig'){
    		swDireccion=true;
    		swFechaHasta=false;
    		swFuncionario=true;
    		swOficina=true;
    		swPersona=false; //fRnk: oculto custodio, Préstamo y Fecha Dev.Préstamo, a petición de la gestora
    		swDeptoDest=false;
    		swDepositoDest=false;
    		swFuncionarioDest=false;
    		swCatMovMotivo=true;
            swPrestamo=false;
            swTipoMovimiento=false;
    		h=370;
    	} else if(mov=='baja'||mov=='retiro'){
    		swDireccion=false;
    		swFechaHasta=false;
    		swFuncionario=false;
    		swOficina=false;
    		swPersona=false;
    		swDeptoDest=false;
    		swDepositoDest=false;
    		swFuncionarioDest=false;
    		swCatMovMotivo=true;
            swNroDoc=true;
        swTipoMovimiento=false;
    		h=370;
    	} else if(mov=='deprec'){
    		swDireccion=false;
    		swFechaHasta=true;
    		swFuncionario=false;
    		swOficina=false;
    		swPersona=false;
    		swDeptoDest=false;
    		swDepositoDest=false;
    		swFuncionarioDest=false;
    		swCatMovMotivo=true;
        swTipoMovimiento=false;
        swTipoAsig=true;
        swTipoDepre=true;
    		h=370;
    	} else if(mov=='desuso'){
    		swDireccion=false;
    		swFechaHasta=false;
    		swFuncionario=false;
    		swOficina=false;
    		swPersona=false;
    		swDeptoDest=false;
    		swDepositoDest=false;
    		swFuncionarioDest=false;
    		swCatMovMotivo=true;
        swTipoMovimiento=false;
    		h=600;
    	} else if(mov=='devol'){
    		swDireccion=true;
    		swFechaHasta=false;
    		swFuncionario=true;
    		swOficina=false;
    		swPersona=false; //fRnk: oculto custodio, a petición de la gestora
    		swDeptoDest=false;
    		swDepositoDest=false;
    		swFuncionarioDest=false;
    		swCatMovMotivo=true;
            swTipoAsig=true;
            swTipoMovimiento=false;
    		h=370;
            swDeposito=true;
    	} else if(mov=='ajuste'){
    		swDireccion=false;
    		swFechaHasta=false;
    		swFuncionario=false;
    		swOficina=false;
    		swPersona=false;
    		swDeptoDest=false;
    		swDepositoDest=false;
    		swFuncionarioDest=false;
    		swCatMovMotivo=true;
            swTipoMovimiento=false;
            swNroDoc=true;
            swTiDoc=true;
    		h=370;
    	} else if(mov=='reval'||mov=='mejora'){
    		swDireccion=false;
    		swFechaHasta=false;
    		swFuncionario=false;
    		swOficina=false;
    		swPersona=false;
    		swDeptoDest=false;
    		swDepositoDest=false;
    		swFuncionarioDest=false;
    		swCatMovMotivo=true;
            swNroDoc=true;
        swTipoMovimiento=false;
    		h=250;
    	} else if(mov=='transf'){
    		swDireccion=true;
    		swFechaHasta=false;
    		swFuncionario=true;
    		swOficina=true;
    		swPersona=false; //fRnk: oculto custodio, Préstamo y Fecha Dev.Préstamo, a petición de la gestora
    		swDeptoDest=false;
    		swDepositoDest=false;
    		swFuncionarioDest=true;
    		swCatMovMotivo=true;
            swTipoAsig=true;
            swTipoMovimiento=false;
    		h=410;
    	} else if(mov=='tranfdep'){
    		swDireccion=false;
    		swFechaHasta=false;
    		swFuncionario=false;
    		swOficina=true;
    		swPersona=false;
    		swDeptoDest=true;
    		swDepositoDest=true;
    		swFuncionarioDest=false;
    		swCatMovMotivo=true;
        swTipoMovimiento=false;
    		h=370;
    	} else if(mov=='actua'){
    		swDireccion=false;
    		swFechaHasta=true;
    		swFuncionario=false;
    		swOficina=false;
    		swPersona=false;
    		swDeptoDest=false;
    		swDepositoDest=false;
    		swFuncionarioDest=false;
    		swCatMovMotivo=true;
        swTipoMovimiento=false;
        swTipoDepre=true;
    		h=370;
    	} else if(mov=='divis'||mov=='desgl'||mov=='intpar'){
            swDireccion=false;
            swFechaHasta=false;
            swFuncionario=false;
            swOficina=false;
            swPersona=false;
            swDeptoDest=false;
            swDepositoDest=false;
            swFuncionarioDest=false;
            swCatMovMotivo=true;
            swTipoMovimiento=false;
            h=370;
        } else if(mov=='transito'){
            swDireccion=false;
            swFechaHasta=false;
            swFuncionario=false;
            swOficina=false;
            swPersona=false;
            swDeptoDest=false;
            swDepositoDest=false;
            swFuncionarioDest=false;
            swCatMovMotivo=true;
            swTipoMovimiento=false;
            h=370;
        }

    	//Enable/disable user controls based on mov type
    	this.Cmp.direccion.setVisible(swDireccion);
    	this.Cmp.fecha_hasta.setVisible(swFechaHasta);
    	this.Cmp.id_funcionario.setVisible(swFuncionario);
    	this.Cmp.id_oficina.setVisible(swOficina);
    	this.Cmp.id_persona.setVisible(swPersona);
    	this.Cmp.id_depto_dest.setVisible(swDeptoDest);
    	this.Cmp.id_deposito_dest.setVisible(swDepositoDest);
    	this.Cmp.id_funcionario_dest.setVisible(swFuncionarioDest);
    	this.Cmp.id_movimiento_motivo.setVisible(swCatMovMotivo);
        this.Cmp.prestamo.setVisible(swPrestamo);
        this.Cmp.fecha_dev_prestamo.setVisible(swPrestamo);
        this.Cmp.tipo_asig.setVisible(swTipoAsig);
        //(f.e.a)Habilitando campo deposito
        this.Cmp.id_deposito.setVisible(swDeposito);
        this.Cmp.tipo_movimiento.setVisible(swTipoMovimiento);
        //(breydi.vasquez 14/01/2020)Habilitando campo deposito
        this.Cmp.nro_documento.setVisible(swNroDoc);
        this.Cmp.tipo_documento.setVisible(swTiDoc);
        this.Cmp.tipo_drepeciacion.setVisible(swTipoDepre)

    	//Set required or not
    	this.Cmp.direccion.allowBlank=!swDireccion;
    	this.Cmp.fecha_hasta.allowBlank=!swFechaHasta;
    	this.Cmp.id_funcionario.allowBlank=!swFuncionario;
    	this.Cmp.id_oficina.allowBlank=!swOficina;
    	//this.Cmp.id_persona.allowBlank=!swPersona;
    	this.Cmp.id_depto_dest.allowBlank=!swDeptoDest;
    	this.Cmp.id_deposito_dest.allowBlank=!swDepositoDest;
    	this.Cmp.id_funcionario_dest.allowBlank=!swFuncionarioDest;
    	this.Cmp.id_movimiento_motivo.allowBlank=!swCatMovMotivo;
        this.Cmp.tipo_asig.allowBlank=!swTipoAsig;
        //(f.e.a) Haciendo el campo exiguible, y de entrada deshabilitado
        this.Cmp.id_deposito.allowBlank=!swDeposito;
        this.Cmp.tipo_drepeciacion.allowBlank=!swTipoDepre
    	//Resize window
    	this.window.setSize(w,h);
    },

    onButtonEdit: function() {
    	Phx.vista.Movimiento.superclass.onButtonEdit.call(this);
    	var data = this.getSelectedData();
        if(!['borrador', 'finalizado'].includes(data.estado)){
            alert('Para modificar el documento, debe estar en estado BORRADOR.');
        }
    	this.habilitarCampos(data.cod_movimiento, data.estado);

    },

    south: {
		url: '../../../sis_kactivos_fijos/vista/movimiento_af/MovimientoAf.php',
		title: 'Detalle de Movimiento',
		height: '50%',
		cls: 'MovimientoAf'
	},

 loadCheckDocumentosSol:function() {
     var rec=this.sm.getSelected();
     rec.data.id_proceso_wf = rec.data.id_proceso_wf_doc;
     rec.data.nombreVista = this.nombreVista;
     var proceso = rec.data.id_proceso_wf_doc;
     if (proceso == null) {
       alert('El Movimiento no tiene documetos de preingreso');
     }
     else {
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
   this.reload();
 },
    onOpenPendientesFirmaD:function() {
        var data = {
            tipo_interfaz: this.nombreVista
        };
        Phx.CP.loadWindows('../../../sis_kactivos_fijos/vista/movimiento/Obs.php',
            'Pendientes de Firma Digital',
            {
                width:'80%',
                height:'70%'
            },
            data,
            this.idContenedor,
            'Obs'
        )
    },

	liberaMenu:function(){
        var tb = Phx.vista.Movimiento.superclass.liberaMenu.call(this);
        if(tb){
            this.getBoton('btnReporte').disable();
            this.getBoton('btnReporteDep').disable();
            this.getBoton('ant_estado').disable();
	        this.getBoton('sig_estado').disable();
            this.getBoton('btnChequeoDocumentosWf').disable();
	        this.getBoton('btnChequeoDocumentosAF').disable();
	        this.getBoton('btnFirmaDigital').disable();
	        this.getBoton('diagrama_gantt').disable();
	        this.getBoton('btnAsignacion').disable();
        }
       return tb
    },
    preparaMenu:function(n){
    	var tb = Phx.vista.Movimiento.superclass.preparaMenu.call(this);
      	var data = this.getSelectedData();
      	var tb = this.tbar;

        this.getBoton('btnChequeoDocumentosWf').enable();
        this.getBoton('btnChequeoDocumentosAF').enable();
        this.getBoton('diagrama_gantt').enable();
        this.getBoton('btnAsignacion').disable();//fRnk: añadido porque da error con movimientos sin asig,transf,devol
        if(data.cod_movimiento != 'asig' && data.cod_movimiento != 'transf' && data.cod_movimiento != 'devol'){
            this.getBoton('btnReporte').enable();
        }
		if(data.cod_movimiento == 'asig' || data.cod_movimiento == 'transf' || data.cod_movimiento == 'devol'){
            this.getBoton('btnAsignacion').enable();
        }
        if(data.cod_movimiento == 'asig' || data.cod_movimiento == 'alta') {//fRnk: añadir más estados para que el botón de firma digital se habilite
            this.getBoton('btnFirmaDigital').enable();
        }

		//Enable/disable WF buttons by status
        this.getBoton('ant_estado').enable();
        this.getBoton('sig_estado').enable();

        if(data.cod_movimiento == 'alta' && data.estado=='aprobado'){//fRnk: específico para movimiento de alta y estado aprobado
            this.getBoton('ant_estado').disable();
        }

        if(data.estado=='borrador'){
        	this.getBoton('ant_estado').disable();
        } else {
            //Deshabilita el botón siguiente cuando no está en borrador para la vista transaccional, porque las aprobaciones se deben hacer por la interfaz de VoBo
            if(this.nombreVista=='MovimientoPrincipal'){
                this.getBoton('sig_estado').disable();
            }

            if(this.nombreVista=='MovimientoVb'){ //fRnk: condición añadida para firma digital
                //debugger;
                if(data.firma_digital=='si' && (data.firmado==null || data.firmado=='no')){
                    this.getBoton('sig_estado').disable();
                }
                if(data.firma_digital=='si' && data.firmado=='si'){
                    this.getBoton('sig_estado').enable();
                }
            }
        }

        if(data.estado=='finalizado'||data.estado=='cancelado'){
        	this.getBoton('ant_estado').disable();
        	this.getBoton('sig_estado').disable();
        }

        if(data.cod_movimiento=='deprec'  || data.cod_movimiento=='actua'){
        	this.getBoton('btnReporteDep').enable();
        }
        else{
        	this.getBoton('btnReporteDep').disable();
        }

        return tb;
    },
    antEstado:function(){
        var rec=this.sm.getSelected();
            Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/AntFormEstadoWf.php',
            'Estado de Wf',
            {
                modal:true,
                width:450,
                height:250
            }, {data:rec.data}, this.idContenedor,'AntFormEstadoWf',
            {
                config:[{
                  event:'beforesave',
                  delegate: this.onAntEstado,
                }
            ],
            scope:this
        })
    },
    onAntEstado:function(wizard,resp){

        Phx.CP.loadingShow();
        Ext.Ajax.request({
            url:'../../sis_kactivos_fijos/control/Movimiento/anteriorEstadoMovimiento',
            params:{
                    id_proceso_wf:resp.id_proceso_wf,
                    id_estado_wf:resp.id_estado_wf,
                    obs:resp.obs,
                    firma_digital:resp.data.firma_digital, //fRnk: adicionado para enviar si el estado seleccionado tiene Firma Digital
                    id_movimiento:resp.data.id_movimiento
             },
            argument:{wizard:wizard},
            success:this.successWizard,
            failure: this.conexionFailure,
            timeout:this.timeout,
            scope:this
        });
    },
    sigEstado:function(){
		var rec=this.sm.getSelected();

		this.objWizard = Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/FormEstadoWf.php',
	        'Estado de Wf',
	        {
	            modal:true,
	            width:700,
	            height:450
	        }, {data:{
	               id_estado_wf:rec.data.id_estado_wf,
	               id_proceso_wf:rec.data.id_proceso_wf,
	               fecha_ini:rec.data.fecha_mov,
	            }}, this.idContenedor,'FormEstadoWf',
	        {
	            config:[{
                  event:'beforesave',
                  delegate: this.onSaveWizard,

                }],
	            scope:this
	        });
    },
    onSaveWizard:function(wizard,resp){
        //fRnk: adicionado para enviar si el estado seleccionado tiene Firma Digital
        var firma_digital='';
        try {
            resp.estados.forEach((item) => {
                if(resp.id_tipo_estado == item.json.id_tipo_estado){
                    firma_digital = item.json.firma_digital;
                }
            });
        } catch(err) {}

        Phx.CP.loadingShow();
        Ext.Ajax.request({
            url:'../../sis_kactivos_fijos/control/Movimiento/siguienteEstadoMovimiento',
            params:{
                id_proceso_wf_act:  resp.id_proceso_wf_act,
                id_estado_wf_act:   resp.id_estado_wf_act,
                id_tipo_estado:     resp.id_tipo_estado,
                id_funcionario_wf:  resp.id_funcionario_wf,
                id_depto_wf:        resp.id_depto_wf,
                obs:                resp.obs,
                json_procesos:      Ext.util.JSON.encode(resp.procesos),
                firma_digital:      firma_digital
                },
            success:this.successWizard,
            failure: this.conexionFailure,
            argument:{wizard:wizard},
            timeout:this.timeout,
            scope:this
        });
    },
    successWizard:function(resp){
        Phx.CP.loadingHide();
        resp.argument.wizard.panel.destroy()
        this.reload();
    },
    loadCheckDocumentosPlanWf:function() {
        var rec=this.sm.getSelected();
        rec.data.nombreVista = this.nombreVista;
        console.log('RESPUESTA:',rec.data);
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
    },

    onButtonNew: function() {

    	this.hideFields();
    	this.window.setSize(600,130);
    	Phx.vista.Movimiento.superclass.onButtonNew.call(this);

    },

    hideFields: function() {
        this.Cmp.id_cat_movimiento.enable();
    	this.Cmp.estado.hide();
    	this.Cmp.codigo.hide();
    	this.Cmp.fecha_mov.hide();
    	this.Cmp.glosa.hide();
    	this.Cmp.id_depto.hide();
    	this.Cmp.id_oficina.hide();
    	this.Cmp.direccion.hide();
    	this.Cmp.fecha_hasta.hide();
    	this.Cmp.id_funcionario.hide();
    	this.Cmp.id_persona.hide();
    	this.Cmp.id_depto_dest.hide();
    	this.Cmp.id_deposito_dest.hide();
    	this.Cmp.id_funcionario_dest.hide();
    	this.Cmp.id_movimiento_motivo.hide();
        this.Cmp.prestamo.hide();
        this.Cmp.fecha_dev_prestamo.hide();
        this.Cmp.tipo_asig.hide();
        //(f.e.a)Ocultando el campo deposito
        this.Cmp.id_deposito.hide();
        this.Cmp.tipo_movimiento.hide();
        this.Cmp.id_int_comprobante_aitb.hide();
        this.Cmp.nro_documento.hide();
        this.Cmp.tipo_documento.hide();
        this.Cmp.tipo_drepeciacion.hide();
    }  ,
    arrayDefaultColumHidden:['fecha_reg','usr_reg','fecha_mod','usr_mod','fecha_hasta','id_proceso_wf','id_estado_wf','id_funcionario','estado_reg','id_usuario_ai','usuario_ai','direccion','id_oficina'],
	rowExpander: new Ext.ux.grid.RowExpander({
	        tpl : new Ext.Template(
	            '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Usuario Registro:&nbsp;&nbsp;</b> {usr_reg}</p>',
	            '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha Registro:&nbsp;&nbsp;</b> {fecha_reg}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Nro Documento:&nbsp;&nbsp;</b> {nro_documento}</p>',
	            '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Usuario Modificación:&nbsp;&nbsp;</b> {usr_mod}</p>',
	            '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha Modificación:&nbsp;&nbsp;</b> {fecha_mod}</p>'
	        )
    })


};
</script>
