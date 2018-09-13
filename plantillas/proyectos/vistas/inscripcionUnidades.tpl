<form name="frmProyectos" id="frmProyectos" onSubmit="return false;" method="$_POST" >
    <!-- CODIGO PARA EL POPUP DE SEGUIMIENTO -->
    {include file='proyectos/pedirSeguimiento.tpl'}
    {assign var=style value = "border-radius: 20px 20px 0 0;"}
    {assign var=styleLic value = "border-radius: 20px 20px 0 0;"}
    {assign var=nav value = "width: 100%;"}
    {assign var=nav1 value = "width: 100%"}

    <div id="wrapper" class="container tab-content">
        <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist" style="width: 100%">                
            <li class="nav-item active"  style="{$nav}">   
                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#Unidades" role="tab" aria-controls="profile" aria-selected="false" style="{$style}" onclick="listenerFile('fileAction', 'nameArchivo');
                        removeFile('fileAction', 'nameArchivo');
                        $('#div2').html('');
                        $('#divEstados').html('');">Crear Unidades <br></a>
            </li>

        </ul>
        <div id="Unidades" class="tab-pane active"  role="tabpanel" aria-labelledby="profile-tab" style="min-height: 300px; max-height: 550px; overflow-y: auto">

            <div class="form-group">
                <div class="col-md12" style="padding: 20px"> 
                    <fieldset>
                        <legend class="legend">
                            <h4 style="position: relative; float: left; width: 50%; margin: 0; padding: 3px;">
                                Módulo de Importación de Unidades</h4>
                        </legend>     
                        <p>&nbsp;</p>
                        <div class="form-group" >
                            <fieldset style="border: 1px dotted #024457; width: 95%;margin-left: 10px; padding: 5px;">                                
                                <div class="col-md-4">
                                    <label class="control-label" >Proyecto</label>
                                    <input type="hidden" name="idProyecto" value="{$idProyecto}" />
                                    <select name="seqProyecto"
                                            id="seqProyecto"
                                            style="width:230px;" 
                                            class="form-control required">
                                        <option value="">Seleccione</option>
                                        {foreach from=$arrayProyectos key=key item=value} 
                                            <option value="{$value.seqProyecto}">{$value.txtNombreProyecto}</option>
                                        {/foreach}
                                    </select>
                                    <div id="val_seqProyecto" class="divError">Debe Seleccionar proyecto</div> 
                                </div>
                                <div class="col-md-4" style="text-align: left">
                                    <div class="custom-file" style="top: 5px">
                                        <input type="file" name="archivo" class="custom-file-input" id="customFile">
                                        <label class="custom-file-label" for="customFile" id="nameArchivo">Seleccione Archivo</label>
                                    </div>
                                    <div id="fileAction"></div>                                  

                                </div>
                                <div class="col-md-2">
                                    <label class="control-label" >&nbsp;</label><br>
                                    <input type="button" class="btn_volver" value="Importar &nbsp;" id="enviarDoc" onclick="if (validarCampos())
                                                someterFormulario('div2', this.form, 'contenidos/administracionProyectos/salvarUnidades.php', true, true);"/>
                                </div>            

                                <div class="col-md-2" style="text-align: center">
                                    <label class="control-label" >&nbsp;</label><br>
                                    <input type="button" class="btn_volver" value="Plantilla &nbsp;" id="plantillaUnidad" onclick="obtenerPlantillaUnidades(1);" />
                                </div>                               
                                <p>&nbsp;</p> 
                                <div id="div2"></div>
                            </fieldset>
                        </div>
                        <p>&nbsp;</p> 
                    </fieldset>             
                </div>
            </div> 
        </div>
        <p>&nbsp;</p>
    </div>
</form>