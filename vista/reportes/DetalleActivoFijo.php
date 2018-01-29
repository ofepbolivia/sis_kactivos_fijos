<?php
/**
 *@package pXP
 *@file    ComprasGestion.php
 *@author  Franklin Espinoza Alvarez
 *@date    23-01-2018
 *@description Archivo con la interfaz para generación de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.DetalleActivoFijo = Ext.extend(Phx.frmInterfaz, {

        Atributos : [

            {
                config:{
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: true,
                    anchor: '44.5%',
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
                    allowBlank: true,
                    anchor: '44.5%',
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
                    anchor : '50%',
                    valueField : 'id',
                    displayField : 'valor'
                },
                type : 'ComboBox',
                id_grupo : 0,
                form : true
            },

            {
                config : {
                    name : 'estado',
                    fieldLabel : 'Estado',
                    allowBlank : false,
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
                    anchor : '50%',
                    gwidth : 150,
                    minChars : 2,
                    renderer : function(value, p, record) {
                        return String.format('{0}', record.data['descripcion']);
                    }
                },
                type : 'ComboBox',
                id_grupo : 0,
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
                    anchor : '50%',
                    valueField : 'tipo',
                    displayField : 'valor'
                },
                type : 'ComboBox',
                id_grupo : 0,
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
                    anchor : '50%',
                    valueField : 'tipo',
                    displayField : 'valor'
                },
                type : 'ComboBox',
                id_grupo : 0,
                form : true
            }
        ],
        title : 'Reporte General Activos',
        ActSave : '../../sis_kactivos_fijos/control/ActivoFijo/comprasXgestion',
        timeout : 1500000,

        topBar : true,
        botones : false,
        labelSubmit : 'Imprimir',
        tooltipSubmit : '<b>Generar Reporte Libro Bancos</b>',

        constructor : function(config) {
            Phx.vista.DetalleActivoFijo.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
        },

        iniciarEventos:function(){
            /*this.cmpFormatoReporte = this.getComponente('formato_reporte');
             this.cmpFechaIni = this.getComponente('fecha_ini');
             this.cmpFechaFin = this.getComponente('fecha_fin');
             this.cmpIdCuentaBancaria = this.getComponente('id_cuenta_bancaria');
             this.cmpEstado = this.getComponente('estado');
             this.cmpTipo = this.getComponente('tipo');
             this.cmpNombreBanco = this.getComponente('nombre_banco');
             this.cmpNroCuenta = this.getComponente('nro_cuenta');

             this.getComponente('finalidad').hide(true);
             this.cmpNroCuenta.hide(true);
             this.getComponente('id_finalidad').on('change',function(c,r,n){
             this.getComponente('finalidad').setValue(c.lastSelectionText);
             },this);

             this.cmpIdCuentaBancaria.on('select',function(c,r,n){
             this.cmpNombreBanco.setValue(r.data.nombre_institucion);
             this.cmpNroCuenta.setValue(c.lastSelectionText);
             this.getComponente('id_finalidad').reset();
             this.getComponente('id_finalidad').store.baseParams={id_cuenta_bancaria:c.value, vista: 'reporte'};
             this.getComponente('id_finalidad').modificado=true;
             },this);*/
        },

        onSubmit:function(o){
            /*if(this.cmpFormatoReporte.getValue()==2){
             var data = 'FechaIni=' + this.cmpFechaIni.getValue().format('d-m-Y');
             data = data + '&FechaFin=' + this.cmpFechaFin.getValue().format('d-m-Y');
             data = data + '&IdCuentaBancaria=' + this.cmpIdCuentaBancaria.getValue();
             data = data + '&Estado=' + this.cmpEstado.getValue();
             data = data + '&Tipo=' + this.cmpTipo.getValue();
             data = data + '&NombreBanco=' + this.cmpNombreBanco.getValue();
             data = data + '&NumeroCuenta=' + this.cmpNroCuenta.getValue();

             console.log(data);
             window.open('http://sms.obairlines.bo/LibroBancos/Home/VerLibroBancos?'+data);
             //window.open('http://localhost:2309/Home/VerLibroBancos?'+data);
             }else{
             Phx.vista.ReporteLibroBancos.superclass.onSubmit.call(this,o);
             }*/
            Phx.vista.DetalleActivoFijo.superclass.onSubmit.call(this,o);
        },

        tipo : 'reporte',
        clsSubmit : 'bprint',

        Grupos : [{
            layout : 'column',
            labelAlign: 'top',
            border : false,
            items : [{
                xtype : 'fieldset',
                layout : 'form',
                border : true,
                title : 'Reporte Compras x Gestión',
                bodyStyle : 'padding:0 10px 0;',
                columnWidth : .40,
                items : [],
                id_grupo : 0/*,
                 collapsible : true*/
            }]
        }]
    })
</script>