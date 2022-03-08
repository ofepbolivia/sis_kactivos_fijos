<?php
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ParametrosRepSinAsignar = {
	require: '../../../sis_kactivos_fijos/vista/reportes/ParametrosBase.php',
	requireclase: 'Phx.vista.ParametrosBase',
	constructor: function(config){
		Phx.vista.ParametrosRepSinAsignar.superclass.constructor.call(this,config);
		this.definicionRutareporte();
		this.definirParametros();
		this.formParam.topToolbar.items.items[1].setVisible(false);
		this.formParam.topToolbar.items.items[2].setVisible(false);
		this.formParam.topToolbar.items.items[4].setVisible(false);
		this.formParam.topToolbar.items.items[5].setVisible(false);		
		this.formParam.topToolbar.items.items[7].setVisible(false);
		this.formParam.topToolbar.items.items[8].setVisible(false);		
		this.formParam.topToolbar.items.items[10].setVisible(false);
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

		//Oficina
		this.cmbDeposito.on('select',function(combo,record,index){
			this.repDeposito = record.data['nombre'];
		}, this);
	},
	definicionRutareporte: function(report){
		this.rutaReporte = '../../../sis_kactivos_fijos/vista/reportes/ReporteSinAsignar.php';
		this.claseReporte = 'ReporteSinAsignar';
		this.titleReporte = 'Reporte Activos Fijos Sin Asignar';
	},
	definirParametros: function(report){
		this.inicializarParametros();

		this.configElement(this.dteFechaDesde,false,true);
		this.configElement(this.dteFechaHasta,false,true);
		this.configElement(this.cmbActivo,false,true);

		this.configElement(this.cmbClasificacion,true,true);
		this.configElement(this.cmbClasificacionMulti,false,true);
		this.configElement(this.cmbTipoMov,false,true)		
		this.configElement(this.txtDenominacion,true,true);
		this.configElement(this.dteFechaCompra,true,true);
		this.configElement(this.dteFechaIniDep,true,true);
		this.configElement(this.cmbEstadoDepre,false,true);
		this.configElement(this.cmbEstado,true,true);
		this.configElement(this.cmbCentroCosto,false,true);
		this.configElement(this.txtUbicacionFisica,true,true);
		this.configElement(this.cmbOficina,true,true);
		this.configElement(this.cmbResponsable,false,true);
		this.configElement(this.cmbUnidSolic,false,true);
		this.configElement(this.cmbResponsableCompra,false,true);
		this.configElement(this.cmbLugar,false,true);
		this.configElement(this.radGroupTransito,false,true);
		this.configElement(this.radGroupTangible,true,true);
		this.configElement(this.cmbDepto,true,true);
		this.configElement(this.cmbDeposito,true,false);
		this.configElement(this.lblHasta,false,true);
		this.configElement(this.cmpFechas,false,true);
		this.configElement(this.txtMontoInf,true,true);
		this.configElement(this.txtMontoSup,true,true);
		this.configElement(this.lblMontoInf,true,true);
		this.configElement(this.lblMontoSup,true,true);
		this.configElement(this.txtNroCbteAsociado,true,true);
		this.configElement(this.cmpMontos,true,true);
		this.configElement(this.cmbMoneda,false,true);
		this.configElement(this.radGroupEstadoMov,false,true);
		this.configElement(this.cmpFechaCompra,true,true);

		this.configElement(this.fieldSetGeneral,true,true);
		this.configElement(this.fieldSetIncluir,true,true);
		this.configElement(this.fieldSetCompra,false,true);
	},
	onSubmit: function(){
		if(this.formParam.getForm().isValid()){
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
	},
	getExtraParams: function(){
		var params = {
			repResponsable: this.repResponsable,
			repCargo: this.repCargo,
			repDepto: this.repDepto,
			repOficina: this.repOficina,
			repDeposito: this.repDeposito
		}
		return params;
	},
	setPersonalBackgroundColor: function(elm){
    	//Para sobreescribir
    	var color='#FFF',
    		obligatorio='#ffffb3';

    	if(elm=='cmbDeposito'){
    		color = obligatorio;
    	}
    	return color;
    }

}
</script>