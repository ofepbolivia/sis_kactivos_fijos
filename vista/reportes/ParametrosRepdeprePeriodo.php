<?php
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ParametrosRepdeprePeriodo = {
        require: '../../../sis_kactivos_fijos/vista/reportes/ParametrosBase.php',
        requireclase: 'Phx.vista.ParametrosBase',
        constructor: function(config){
            Phx.vista.ParametrosRepdeprePeriodo.superclass.constructor.call(this,config);
           
            this.definirParametros();
            this.formParam.topToolbar.items.items[2].setVisible(false);            
            this.formParam.topToolbar.items.items[0].setVisible(false);
            this.formParam.topToolbar.items.items[6].setVisible(false);            
            this.formParam.topToolbar.add('-',{               
                xtype: 'splitbutton',
                grupo: [0,4],
                tooltip: '<b>Reporte Depreciacion A.F.</b><br>Podemos generar reporte de depreciacion de formato PDF y EXCEL.',
                text:'<i class="fa fa-file-excel-o" aria-hidden="true"></i> Reporte Depreciacion Periodo',
                scope: me,
                menu: [{
                    text: 'Reporte XLS',
                    iconCls: 'bexcel',
                    argument: {
                        'news': true,
                        def: 'csv'
                    },
                    handler: me.onReporteDepPe,
                    scope: me
                }, {
                    text: 'Reporte PDF',
                    iconCls: 'bpdf',
                    argument: {
                        'news': true,
                        def: 'pdf'
                    },
                    handler: me.onReporteDepPe,
                    scope: me
                }]
            });
            this.formParam.topToolbar.doLayout();			

		//Eventos
		this.definirEventos();
		
	},
	definirEventos: function(){
		this.cmbActivo.on('select',function(){
			this.cmbClasificacion.setValue('');
		},this);
		this.cmbClasificacion.on('select',function(){			
			this.cmbActivo.setValue('');
			this.configElement(this.cmbClasificacionMulti,false,true);									
		},this);
		this.cmbClasificacionMulti.on('select',function(){
			this.configElement(this.cmbClasificacion,false,true);
		},this);
		this.cmbTipoMov.on('select',function(cmb,record,index){
			if(record.data.tipo=='consoli'){						
				this.cmbEstadoDepre.setVisible(false);
			}else{
				this.cmbEstadoDepre.setVisible(true);
			}
		},this);
		//Responsable
		this.cmbResponsable.on('select',function(combo,record,index){
			this.repResponsable = record.data['desc_funcionario1'];
			this.repCargo = record.data['nombre_cargo'];
		}, this);


            //Depto
            this.repDepto = '%'
            this.cmbDepto.on('select',function(combo,record,index){
                this.repDepto = record.data['nombre'];
            }, this);

            //Oficina
            this.repOficina = '%'
            this.cmbOficina.on('select',function(combo,record,index){
                this.repOficina = record.data['nombre'];
            }, this);
        },
        definirParametros: function(report){
            this.inicializarParametros();
			//this.configElement(this.cmbTipoRep,false,true);
            this.configElement(this.dteFechaDesde,false,true);
            this.configElement(this.dteFechaHasta,true,false);
            this.configElement(this.cmbActivo,false,true);

		this.configElement(this.cmbClasificacion,true,true);
		this.configElement(this.cmbClasificacionMulti,true,true);		
		this.configElement(this.cmbTipoMov,false,true)					
		this.configElement(this.txtDenominacion,false,true);
		this.configElement(this.dteFechaCompra,true,true);
		this.configElement(this.dteFechaIniDep,true,true);
		this.configElement(this.cmbEstadoDepre,false,true);
		this.configElement(this.cmbEstado,false,true);
		this.configElement(this.cmbCentroCosto,false,true);
		this.configElement(this.txtUbicacionFisica,false,true);
		this.configElement(this.cmbOficina,false,true);
		this.configElement(this.cmbResponsable,false,true);
		this.configElement(this.cmbUnidSolic,false,true);
		this.configElement(this.cmbResponsableCompra,false,true);
		this.configElement(this.cmbLugar,false,true);
		this.configElement(this.radGroupTransito,false,true);
		this.configElement(this.radGroupTangible,true,true);
		this.configElement(this.cmbDepto,false,true);
		this.configElement(this.descNombre,true,true);
		this.configElement(this.cmbDeposito,false,true);
		this.configElement(this.lblDesde,false,true);
		this.configElement(this.lblHasta,true,true);
		this.configElement(this.cmpFechas,true,true);
		this.configElement(this.txtMontoInf,true,true);
		this.configElement(this.txtMontoSup,true,true);
		this.configElement(this.lblMontoInf,true,true);
		this.configElement(this.lblMontoSup,true,true);
		this.configElement(this.txtNroCbteAsociado,false,true);
		this.configElement(this.cmpMontos,true,true);
		this.configElement(this.cmbMoneda,true,false);
		this.configElement(this.radGroupEstadoMov,false,true);
		this.configElement(this.cmpFechaCompra,false,true);
		this.configElement(this.radGroupDeprec,true,true);


            this.configElement(this.fieldSetGeneral,true,true);
            this.configElement(this.fieldSetIncluir,true,true);
            this.configElement(this.fieldSetCompra,false,true);
        },

        getExtraParams: function(){
            var params = {
                repResponsable: this.repResponsable,
                repCargo: this.repCargo,
                repDepto: this.repDepto,
                repOficina: this.repOficina
            }
            return params;
        },
        setPersonalBackgroundColor: function(elm){
            //Para sobreescribir
            var color='#FFF',
                obligatorio='#ffffb3';

            if(elm=='dteFechaHasta'||elm=='cmbMoneda'){
                color = obligatorio;
            }
            return color;
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
	}        

    }
</script>