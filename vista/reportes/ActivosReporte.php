<?php
header("content-type: text/javascript; charset=UTF-8");
//fRnk: nueva vista reporte
?>
<script>
    Phx.vista.ActivosReporte = Ext.extend(Phx.frmInterfaz, {
        Atributos: [
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
                    name: 'id_movimiento',
                    fieldLabel: 'Número de Trámite',
                    typeAhead: false,
                    forceSelection: true,
                    hiddenName: 'id_movimiento',
                    allowBlank: true,
                    emptyText: 'Elija una Opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_kactivos_fijos/control/Movimiento/listarMovimiento',
                        id: 'id_movimiento',
                        root: 'datos',
                        sortInfo: {
                            field: 'codigo',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_movimiento', 'num_tramite', 'movimiento', 'estado'],
                        remoteSort: true,
                        baseParams: {cod_movimiento:'deprec,actua',repdetdep:'si',par_filtro: 'mov.num_tramite'}
                    }),
                    valueField: 'id_movimiento',
                    displayField: 'num_tramite',
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 200,
                    listWidth: 230,
                    minChars: 3,
                    resizable: true,
                    width: 230,
                    tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>Número trámite:</b><strong style= "color : green;"> {num_tramite}</strong></p><p><b>Estado:</b><strong> {estado}</strong></p></div></tpl>'
                },
                type: 'ComboBox',
                id_grupo: 0,
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
                form: true
            }
        ],
        title: 'Reporte Detalle Depreciación',
        ActSave: '../../sis_kactivos_fijos/control/Movimiento/reporteDepreciacion',
        topBar: true,
        botones: false,
        labelSubmit: 'Imprimir',
        tooltipSubmit: '<b>Generar Reporte Detalle Depreciación</b>',

        constructor: function (config) {
            Phx.vista.ActivosReporte.superclass.constructor.call(this, config);
            this.init();
            this.iniciarEventos();
        },

        iniciarEventos: function () {
        },
        onSubmit: function (o) {
            Phx.vista.ActivosReporte.superclass.onSubmit.call(this, o);
        },
        tipo: 'reporte',
        clsSubmit: 'bprint'
    })
</script>