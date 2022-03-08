<?php
/**
 *@package pXP
 *@file    SubirArchivo.php
 *@author  Grover Velasquez Colque
 *@date    22-03-2012
 *@description permite subir archivos csv con el extracto bancario de una cuenta en la tabla de ts_libro_bancos_extracto
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.AnexoTipo=Ext.extend(Phx.frmInterfaz,{

            constructor:function(config)
            {
                Phx.vista.AnexoTipo.superclass.constructor.call(this,config);
                this.init();
                this.loadValoresIniciales();
            },

            loadValoresIniciales:function()
            {
                Phx.vista.AnexoTipo.superclass.loadValoresIniciales.call(this);
                this.getComponente('id_anexo').setValue(this.id_anexo);
                this.getComponente('id_partida').setValue(this.id_partida);
                this.getComponente('monto_contrato').setValue(this.monto_contrato);
                this.getComponente('monto_tercer').setValue(this.monto_tercer);                
                this.getComponente('monto_alta').setValue(this.monto_alta);
                this.getComponente('monto_erp').setValue(this.monto_erp);
               // this.getComponente('EXTACM').setValue(this.id_plantilla_archivo_excel);
            },

            successSave:function(resp)
            {
                Phx.CP.loadingHide();
                Phx.CP.getPagina(this.idContenedorPadre).reload();
                this.panel.close();
            },


            Atributos:[
                {
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_anexo'

                    },
                    type:'Field',
                    form:true

                },
                {
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_partida'

                    },
                    type:'Field',
                    form:true

                },
			    {
			      config:{
			      	labelSeparator:'',
			        inputType : 'hidden',
			        name: 'monto_tercer',    
			      },
			        type:'NumberField',			        
			        form:true,
			    },
			    {
			      config:{
			      	labelSeparator:'',
			        inputType : 'hidden',
			        name: 'monto_alta',    
			      },
			        type:'NumberField',			        
			        form:true,
			    },			    
			    {
			      config:{
			      	labelSeparator:'',
			        inputType : 'hidden',
			        name: 'monto_contrato',    
			      },
			        type:'NumberField',			        
			        form:true,
			    },
			    {
			      config:{
			      	labelSeparator:'',
			        inputType : 'hidden',
			        name: 'monto_erp',    
			      },
			        type:'NumberField',			        
			        form:true,
			    },			    			                                    
                {
            			config: {
            				name: 'tipo_anexo',
            				fieldLabel: 'Mover a',
            				anchor: '80%',
            				tinit: false,
            				allowBlank: false,
            				origen: 'CATALOGO',
            				gdisplayField: 'tipo_anexo',
            				hiddenName: 'tipo_anexo',
            				gwidth: 180,
            				baseParams:{
            					cod_subsistema:'KAF',
            					catalogo_tipo:'anexo'
            				},
            				valueField: 'codigo'
            			},
            			type: 'ComboRec',
            			id_grupo: 1,
            			filters:{pfiltro:'anex.tipo_anexo',type:'numeric'},
            			grid: true,
            			form: true
            		},

            ],
            title:'Mover Anexo',
            //fileUpload:true,  reload() 
            ActSave:'../../sis_kactivos_fijos/control/Anexo/MoverAnexo'
        }
    )
</script>
