<?php
header("content-type: text/javascript; charset=UTF-8");
//fRnk: nueva vista reporte Documentos con Firma Digital HR01318
?>
<script>
    Phx.vista.DocumentosFirmaDigital = Ext.extend(Phx.frmInterfaz, {
        Atributos: [
            {
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'tipo'
                },
                type: 'Field',
                form: true
            },
            {
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'desc_tipo'
                },
                type: 'Field',
                form: true
            },
            {
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'gestion'
                },
                type: 'Field',
                form: true
            },
            {
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'periodo'
                },
                type: 'Field',
                form: true
            },
            {
                config: {
                    name: 'fecha_desde',
                    fieldLabel: 'Fecha desde',
                    allowBlank: false,
                    anchor: '44.5%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'fecha_desde', type: 'date'},
                id_grupo: 0,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'fecha_hasta',
                    fieldLabel: 'Fecha hasta',
                    allowBlank: false,
                    anchor: '44.5%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y') : ''
                    },
                    startDateField: this.idContenedor + '_fecha_desde',
                },
                type: 'DateField',
                filters: {pfiltro: 'fecha_hasta', type: 'date'},
                id_grupo: 0,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'formato_reporte',
                    fieldLabel: 'Formato del Reporte',
                    typeAhead: true,
                    allowBlank: true,
                    triggerAction: 'all',
                    emptyText: 'Formato...',
                    selectOnFocus: true,
                    mode: 'local',
                    store: new Ext.data.ArrayStore({
                        fields: ['ID', 'valor'],
                        data: [['1', 'PDF'],
                            ['2', 'Excel']]
                    }),
                    valueField: 'ID',
                    displayField: 'valor',
                    width: 200,

                },
                type: 'ComboBox',
                id_grupo: 0,
                form: false
            }
        ],
        title: 'Reporte Documentos con Firma Digital',
        ActSave: '../../sis_kactivos_fijos/control/Reportes/reporteDocumentosFirmaDigital',
        topBar: true,
        botones: false,
        labelSubmit: 'Imprimir',
        tooltipSubmit: '<b>Generar Reporte Documentos con Firma Digital</b>',

        constructor: function (config) {
            Phx.vista.DocumentosFirmaDigital.superclass.constructor.call(this, config);
            this.Cmp.desc_tipo.setValue(this.panel.title);
            this.panel.id.includes('FD1') ? this.Cmp.tipo.setValue('1') : this.Cmp.tipo.setValue('2');
            this.init();
            this.iniciarEventos();
        },

        iniciarEventos: function () {
            var fecha_fin = new Date();
            this.Cmp.fecha_desde.setMaxValue(fecha_fin);
            this.Cmp.fecha_hasta.setMaxValue(fecha_fin);
            this.Cmp.fecha_hasta.setMinValue(false);
        },
        onSubmit: function (o) {
            if (this.Cmp.fecha_desde.getValue() != '') {
                var fecha_inicio = new Date(this.Cmp.fecha_desde.getValue());
                this.Cmp.fecha_hasta.setMinValue(fecha_inicio);
            }
            //Phx.vista.DocumentosFirmaDigital.superclass.onSubmit.call(this, o);
            if (this.form.getForm().isValid()) {
                Ext.Ajax.request({
                    url: '../../sis_kactivos_fijos/control/Reportes/reporteDocumentosFirmaDigital',
                    params: {
                        tipo: this.Cmp.tipo.getValue(),
                        desc_tipo: this.Cmp.desc_tipo.getValue(),
                        fecha_desde: this.Cmp.fecha_desde.getRawValue(),
                        fecha_hasta: this.Cmp.fecha_hasta.getRawValue()
                    },
                    success: this.successExport,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
            }

        },
        onReset: function () {
            this.form.getForm().reset();
            this.Cmp.fecha_hasta.setMinValue(false);
        },
        tipo: 'reporte',
        clsSubmit: 'bprint'
    })
</script>