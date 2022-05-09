<?php
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ParametrosRepDepreciacion = {
        require: '../../../sis_kactivos_fijos/vista/reportes/ParametrosBase.php',
        requireclase: 'Phx.vista.ParametrosBase',
        constructor: function(config){
            Phx.vista.ParametrosRepDepreciacion.superclass.constructor.call(this,config);
            this.definicionRutareporte();
            this.definirParametros();
            this.formParam.topToolbar.items.items[0].setVisible(false);
			this.formParam.topToolbar.items.items[1].setVisible(false);
			this.formParam.topToolbar.items.items[2].setVisible(false);
			this.formParam.topToolbar.items.items[3].setVisible(false);
			this.formParam.topToolbar.items.items[4].setVisible(false);
			this.formParam.topToolbar.items.items[5].setVisible(false);
			this.formParam.topToolbar.items.items[6].setVisible(false);
			this.formParam.topToolbar.items.items[7].setVisible(false);
			this.formParam.topToolbar.items.items[8].setVisible(false);
			this.formParam.topToolbar.items.items[9].setVisible(false);
			this.formParam.topToolbar.items.items[10].setVisible(false);
      this.formParam.topToolbar.items.items[12].setVisible(false);
      this.formParam.topToolbar.items.items[13].setVisible(false);
      this.formParam.topToolbar.items.items[14].setVisible(false);

            this.formParam.topToolbar.insert(0,'-',{
                xtype: 'splitbutton',
                grupo: [0,4],
                tooltip: '<b>Reporte Depreciacion A.F.</b><br>Podemos generar reporte de depreciacion de formato PDF y EXCEL.',
                text:'<i class="fa fa-file" aria-hidden="true"></i> Tipo Reporte',
                scope: me,
                menu: [{
                    text: 'Detalle Depreciacion',
                    iconCls: 'bedit',
                    argument: {
                        'news': true,
                        def: 'edit'
                    },
                    handler: me.detalleDepreciacion,
                    scope: me
                },
                // {
                //     text: 'Periodo Depreciacion',
                //     iconCls: 'bedit',
                //     argument: {
                //         'news': true,
                //         def: 'edit'
                //     },
                //     handler: me.detalleDepPeriodo,
                //     scope: me
                // }
              ]
            }
            );
            this.formParam.topToolbar.doLayout();
		//Eventos
		this.definirEventos();

	},
	definirEventos: function(){
		this.cmbActivo.on('select',function(){
			this.cmbClasificacion.setValue('');
		},this);
		this.cmbClasificacion.on('select',function(){
		    var definido = this.cmbTipoMov.store.data.items[0].data.tipo;
            var id_clasdificacion = this.cmbClasificacion.getValue();
			this.cmbActivo.setValue('');
			this.cmbClasificacionMulti.reset()
			// this.configElement(this.cmbClasificacionMulti,false,true);
            this.cmbTipoMov.setValue(definido);
            console.log('bbbbbb',this.cmbTipoMov.hidden );
            if (this.cmbTipoMov.hidden == false) {

                (id_clasdificacion == 133)? this.configElement(this.cmbBajaReti, true, true):this.configElement(this.cmbBajaReti, false, true);
            }

		},this);
		this.cmbClasificacionMulti.on('select',function(){
		    var definido = this.cmbTipoMov.store.data.items[0].data.tipo;
		    var id_clasdificacion = this.cmbClasificacionMulti.getValue();
			this.cmbClasificacion.reset();
			// this.configElement(this.cmbClasificacion,false,true);
			this.cmbTipoMov.setValue(definido);
            if (this.cmbTipoMov.hidden == false) {

                (id_clasdificacion == 133)? this.configElement(this.cmbBajaReti, true, true):this.configElement(this.cmbBajaReti, false, true);
            }

		},this);
		this.cmbTipoMov.on('select',function(cmb,record,index){
			if(record.data.tipo=='consoli'){
				this.cmbEstadoDepre.setVisible(false);
				this.cmbEstadoDepre.setValue('');
			}else{
				this.cmbEstadoDepre.setVisible(true);
				this.cmbEstadoDepre.setValue('');
			}
		},this);
		this.cmbTipoRep.on('select',function(cmb,record,index){
			if(record.data.tipo=='geac'){
				this.cmbPeriodo.setVisible(true);
				this.cmbPeriodo.setValue('');
				this.configElement(this.cmbPeriodo,true,false);
			}else{
				this.configElement(this.cmbPeriodo,false,true);
				this.cmbPeriodo.setVisible(false);
				this.cmbPeriodo.setValue('');
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

        this.radGroupDeprec.on('change', function () {
            var definido = this.cmbTipoMov.store.data.items[0].data.tipo;
            this.cmbTipoMov.setValue(definido);
        },this);
        },
        definicionRutareporte: function(report){
            this.rutaReporte = '../../../sis_kactivos_fijos/vista/reportes/ReporteDepreciacion.php';
            this.claseReporte = 'ReporteDepreciacion';
            this.titleReporte = 'Reporte Detalle Depreciación';
        },
        definirParametros: function(report){
            this.inicializarParametros();
			this.configElement(this.cmbTipoRep,false,false);
            this.configElement(this.dteFechaDesde,false,true);
            this.configElement(this.dteFechaHasta,false,false);
            this.configElement(this.cmbActivo,false,true);

		this.configElement(this.cmbClasificacion,false,true);
		this.configElement(this.cmbClasificacionMulti,false,true);
		this.configElement(this.cmbTipoMov,false,false);
		this.configElement(this.txtDenominacion,false,true);
		this.configElement(this.dteFechaCompra,true,true);
		this.configElement(this.dteFechaIniDep,false,true);
		this.configElement(this.cmbEstadoDepre,false,true);
		this.configElement(this.cmbTipoRep,false,true);
		this.configElement(this.cmbEstado,false,true);
		this.configElement(this.cmbCentroCosto,false,true);
		this.configElement(this.txtUbicacionFisica,false,true);
		this.configElement(this.cmbOficina,false,true);
		this.configElement(this.cmbResponsable,false,true);
		this.configElement(this.cmbUnidSolic,false,true);
		this.configElement(this.cmbResponsableCompra,false,true);
		this.configElement(this.cmbLugar,false,true);
		this.configElement(this.radGroupTransito,false,true);
		this.configElement(this.radGroupTangible,false,true);
		this.configElement(this.cmbDepto,false,true);
		this.configElement(this.descNombre,false,true);
		this.configElement(this.cmpUbicacion,false,true);
		this.configElement(this.cmbBajaReti,false,true);
		this.configElement(this.cmbUbiacion,false,true);
		this.configElement(this.cmbDeposito,false,true);
		this.configElement(this.lblDesde,false,true);
		this.configElement(this.lblHasta,true,true);
		this.configElement(this.cmpFechas,false,true);
		this.configElement(this.txtMontoInf,true,true);
		this.configElement(this.txtMontoSup,true,true);
		this.configElement(this.lblMontoInf,true,true);
		this.configElement(this.lblMontoSup,true,true);
		this.configElement(this.txtNroCbteAsociado,false,true);
		this.configElement(this.cmpMontos,false,true);
		this.configElement(this.cmbMoneda,false,true);
		this.configElement(this.radGroupEstadoMov,false,true);
		this.configElement(this.cmpFechaCompra,false,true);
		this.configElement(this.radGroupDeprec,false,true);


            this.configElement(this.fieldSetGeneral,true,true);
            this.configElement(this.fieldSetIncluir,true,true);
            this.configElement(this.fieldSetCompra,false,true);
        },
        onSubmit: function(){
            var parametros = this.getParams();
            parametros.desc_nombre =  this.descNombre.getValue();
            if(this.formParam.getForm().isValid()){
                var win = Phx.CP.loadWindows(
                    this.rutaReporte,
                    this.titleReporte, {
                        width: 870,
                        height : 620
                    }, {
                        paramsRep: parametros
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
		},
		detalleDepreciacion:function(cmp,event){
			//this.cmbTipoRep.setVisible(true);
			this.configElement(this.cmbTipoRep,true,false);
			this.cmbPeriodo.setVisible(false);
			this.cmbTipoMov.setVisible(true);
			this.cmbEstadoDepre.setVisible(true);
			this.cmbTipoRep.setVisible(true);
			this.cmbEstado.setVisible(true);
			this.dteFechaHasta.setVisible(true);
			this.cmpFechas.setVisible(true);
			this.cmbClasificacion.setVisible(true);
			this.cmbClasificacionMulti.setVisible(true);
			this.cmbMoneda.setVisible(false);
			this.cmpMontos.setVisible(false);
			this.dteFechaIniDep.setVisible(false);
			this.descNombre.setVisible(true);
			this.cmpUbicacion.setVisible(true);
			this.cmbBajaReti.setVisible(false);
			this.cmbUbiacion.setVisible(true);
			this.radGroupDeprec.setVisible(true);
			this.radGroupTangible.setVisible(true);
			this.cmbEstado.setValue('');
			cmp.scope.formParam.topToolbar.items.items[0].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[1].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[2].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[3].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[4].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[5].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[6].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[7].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[8].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[9].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[10].setVisible(false);
			cmp.scope.formParam.topToolbar.items.items[11].setVisible(false);
			cmp.scope.formParam.topToolbar.items.items[12].setVisible(false);
      cmp.scope.formParam.topToolbar.items.items[13].setVisible(true);
      cmp.scope.formParam.topToolbar.items.items[14].setVisible(true);
      cmp.scope.formParam.topToolbar.items.items[15].setVisible(true);
      cmp.scope.formParam.topToolbar.items.items[16].setVisible(true);

		},
		detalleDepPeriodo:function(cmp,event){
	      this.cmbTipoRep.setValue('');
	      this.cmbPeriodo.setValue('');
	      this.cmbTipoMov.setValue('');
	      this.cmbEstadoDepre.setValue('');
	      this.cmbTipoRep.setValue('');
	      this.dteFechaHasta.setValue('');
	      this.cmbClasificacion.setValue('');
	      this.cmbClasificacionMulti.reset();
	      this.cmbMoneda.setValue('');
	      this.dteFechaIniDep.setValue('');
	      this.descNombre.setValue('');
	      this.cmpUbicacion.setValue('');
	      this.cmbBajaReti.setValue('');
	      this.cmbUbiacion.setValue('');
	      this.radGroupDeprec.setValue('completo');
	      this.radGroupTangible.setValue('ambos');
	      this.cmbEstado.setValue('');

			this.cmbTipoRep.setVisible(false);
			this.configElement(this.cmbPeriodo,true,false);
			this.cmbTipoMov.setVisible(false);
			this.cmbEstadoDepre.setVisible(false);
			this.cmbTipoRep.setVisible(false);
			this.dteFechaHasta.setVisible(true);
			this.cmbBajaReti.setVisible(false);
			this.cmbUbiacion.setVisible(true);
            this.cmpFechas.setVisible(true);
			this.cmbClasificacion.setVisible(true);
			this.cmbClasificacionMulti.setVisible(true);
			this.cmbMoneda.setVisible(false);
			this.cmpMontos.setVisible(false);
			this.dteFechaIniDep.setVisible(false);
			this.descNombre.setVisible(true);
			this.cmpUbicacion.setVisible(true);
			this.radGroupDeprec.setVisible(true);
			this.radGroupTangible.setVisible(true);
			this.cmbEstado.setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[0].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[1].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[2].setVisible(false);
			cmp.scope.formParam.topToolbar.items.items[3].setVisible(false);
			cmp.scope.formParam.topToolbar.items.items[4].setVisible(false);
			cmp.scope.formParam.topToolbar.items.items[5].setVisible(false);
			cmp.scope.formParam.topToolbar.items.items[6].setVisible(false);
			cmp.scope.formParam.topToolbar.items.items[7].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[8].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[9].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[10].setVisible(true);
			cmp.scope.formParam.topToolbar.items.items[12].setVisible(true);
      cmp.scope.formParam.topToolbar.items.items[13].setVisible(false);
      cmp.scope.formParam.topToolbar.items.items[14].setVisible(false);
      cmp.scope.formParam.topToolbar.items.items[15].setVisible(false);
      cmp.scope.formParam.topToolbar.items.items[16].setVisible(false);

		}

    }
</script>
