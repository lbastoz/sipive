<form name="frmProyectos" id="frmProyectos" onSubmit="return false;" method="$_POST" >
    <!-- CODIGO PARA EL POPUP DE SEGUIMIENTO -->
    {foreach from=$arrProyectos key=key item=value} 
        {assign var=seqPryEstadoProceso value=$value.seqPryEstadoProceso}
        {include file='proyectos/pedirSeguimiento.tpl'}
        {if $value.seqPryEstadoProceso != "" }
            {assign var=seqPryEstadoProceso value=$value.seqPryEstadoProceso}
        {else}
            {assign var=seqPryEstadoProceso value = 1}
        {/if}

        {if $seqPryEstadoProceso == 1}
            {assign var=style value = "border-radius: 0 15px 0 0;"}
            {assign var=styleLic value = "border-radius: 0 0 0 0;"}
            {assign var=nav value = "width: 19%"}
            {assign var=nav1 value = "width: 20.6%"}
        {else}
            {assign var=style value = "border-radius: 0 0 0 0;"}
            {assign var=styleLic value = "border-radius: 0 15px 0 0;"}
            {assign var=nav value = "width: 20%"}
            {assign var=nav1 value = "width: 20%"}
        {/if}

        {assign var=seqProyecto value=$value.seqProyecto}

        <div id="wrapper" class="container tab-content">
            <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist" style="width: 100%">
                {if $seqPryEstadoProceso == "" or $seqPryEstadoProceso >= 1}

                    <li class="nav-item active"  style="{$nav1}">
                        <a class="nav-item" id="home-tab" data-toggle="tab" href="#datos" role="tab" aria-controls="home" aria-selected="true" onclick="allDate();
                                verificarSesion();" >Datos Básicos</a>
                    </li>
                    <li  class="nav-item"  style="{$nav1}">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#tiposVivienda" role="tab" aria-controls="profile" aria-selected="false" style="border-radius: 0 0 0 0;" onclick=" verificarSesion();"><em>Tipos Vivienda</em></a>
                    </li>
                    <li  class="nav-item"  style="{$nav}">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#conjuntosResidenciales" role="tab" aria-controls="profile" aria-selected="false" style="border-radius: 0 0 0 0;" onclick="verificarSesion();"><em>Conjuntos Residenciales</em></a>
                    </li>
                    <li class="nav-item" style="{$nav}">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#licencias" role="tab" aria-controls="profile" aria-selected="false" style="border-radius: 0 0 0 0;" onclick="activarAutocompletar('txtNombreInformador', 'txtNombreInformadorContenedor', './contenidos/cruces2/nombres.php', 1);">Licencias <br></a>
                    </li>

                {/if}
                {if $seqPryEstadoProceso > 1}

                    <li class="nav-item" style="{$nav1}">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#financiero" role="tab" aria-controls="profile" aria-selected="false" style="{$styleLic}" onclick=" verificarSesion();">Datos Financieros</a>
                    </li>             
                    <li  class="nav-item" style="{$nav}">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#datosComite" role="tab" aria-controls="profile" aria-selected="false" style="border-radius: 0 0 0 0;" onclick=" verificarSesion();"><em>Comite </em></a>
                    </li>
                    <li  class="nav-item" style="{$nav}">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#datosCronograma" role="tab" aria-controls="profile" aria-selected="false" style="border-radius: 0 0 0 0;" onclick="verificarSesion();"><em>Cronograma </em></a>
                    </li>
                    <li  class="nav-item" style="{$nav}">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#datosPolizas" role="tab" aria-controls="profile" aria-selected="false" style="border-radius: 0 0 0 0;" 
                           onclick="
                           {* {if isset($smarty.session.arrGrupos.5.13) or isset($smarty.session.arrGrupos.6.20)}
                           activarAutocompletar('txtNombreFideicomitente', 'txtNombreFideicomitenteContenedor', './contenidos/cruces2/fideicomitentesAdd.php', {$arrayFideicomitente|@sizeof});
                           *}verificarSesion();
                           "><em>Polizas  y Fiducia</em></a>
                    </li>
                    <li class="nav-item" style="{$nav1}">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#actosAdmon" role="tab" aria-controls="profile" aria-selected="false" style="{$style}">Actos Administrativos<br></a>
                    </li>
                {/if}
                <li class="nav-item"  style="{$nav1}">   
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#seg" role="tab" aria-controls="profile" aria-selected="false" style="{$style}">Seguimientos <br></a>
                </li>

            </ul>
            <div class="tab-pane active" id="datos" role="tabpanel" aria-labelledby="home-tab">
                <fieldset>
                    <legend style="text-align: left" class="legend">
                        <h4 style="position: relative; float: left; width: 50%; margin: 0; padding: 5px;">
                            Datos del Proyecto 
                        </h4>
                        <!--<div class="dropdown" style="position: relative; float: left; width: 30%; margin: 0; ">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" style="margin: 0; ">INFORMACIÓN
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="#"  data-toggle="modal" data-target="#myModal">Documentos</a></li>

                            </ul>
                        </div>-->

                        <h6 style="position: relative; float: right; width: 50%; margin: 0; padding: 0;">
                            <input type="hidden" id="seqProyecto" name="seqProyecto" value="{if $value.seqProyecto != ""}{$value.seqProyecto}{else}0{/if}" > 
                            <input type="hidden" id="seqUsuario" name="seqUsuario" value="{$seqUsuario}" >               
                            <input type="hidden" id="txtArchivo" name="txtArchivo" value="./contenidos/administracionProyectos/salvarProyecto.php">
                        </h6>
                    </legend>
                    <div id="divContent">                                   
                        <div class="form-group">
                            <div class="col-md-6">
                                <label class="control-label" for="nome">Nombre Comit&eacute; Elegibilidad (*)</label>
                                {* <input name="txtNombreProyecto" type="text" id="txtNombreProyecto" value="{$value.txtNombreProyecto}" class="form-control required" onBlur="sinCaracteresEspeciales(this);" style="width:250px;"/>*}
                                <textarea name="txtNombreProyecto" type="text"  id="txtNombreProyecto" onBlur="sinCaracteresEspeciales(this);" class="form-control required" style=" height: 40px" >{$value.txtNombreProyecto}</textarea>
                                <div id="val_txtNombreProyecto" class="divError">Debe diligenciar el campo Nombre del Proyecto</div>
                            </div>
                        </div>
                        <div class="form-group"  id="idLineaTipoSolucionDescripcion">
                            <div class="col-md-6"> 
                                <label class="control-label" >Descripci&oacute;n del Proyecto</label>
                                <textarea name="txtDescripcionProyecto" type="text"  id="txtDescripcionProyecto" onBlur="sinCaracteresEspeciales(this);" class="form-control required" style=" height: 40px" >{$value.txtDescripcionProyecto}</textarea>
                                <div id="val_txtDescripcionProyecto" class="divError">Debe diligenciar el campo Descripción del Proyecto</div>
                            </div>
                        </div> 
                        <div class="form-group">
                            <div class="col-md-4">
                                <label class="control-label" >Nit del Proyecto (*)</label>
                                <input type="text" 
                                       name="numNitProyecto" 
                                       id="numNitProyecto" 
                                       value="{$value.numNitProyecto}"
                                       onFocus="this.style.backgroundColor = '#ADD8E6';" 
                                       onBlur="soloNumeros(this);
                                               this.style.backgroundColor = '#FFFFFF';"
                                       onkeyup="formatoSeparadores(this)" onchange="formatoSeparadores(this)"
                                       class="form-control required" style="width:250px;"  
                                       {if $value.numNitProyecto > 0}readonly {/if}

                                       />
                            </div>
                            <div class="col-md-4">
                                <label class="control-label" ><div id="idTituloPlanParcial">Nombre del Plan Parcial</div></label>    
                                {if $arrPrivilegios.editar == 1}
                                    {assign var=soloLectura value=""}
                                {else}
                                    {assign var=soloLectura value="readonly"}
                                {/if}


                                <div id="idCampoPlanParcial">
                                    <input name="txtNombrePlanParcial" type="text" id="txtNombrePlanParcial"  value="{$value.txtNombrePlanParcial}" onBlur="sinCaracteresEspeciales(this);" class="form-control" style="width:250px;"/></div>
                            </div>
                        </div>
                        <div class="form-group" >
                            <div class="col-md-4"> 
                                <label class="control-label" >Nombre Comercial</label>   
                                <input name="txtNombreComercial" type="text" id="txtNombreComercial" value="{$value.txtNombreComercial}" onBlur="sinCaracteresEspeciales(this);" class="form-control required5" style="width:250px;"/>
                            </div>
                        </div>

                        <!-- TIPO DE MODALIDAD  onchange="obtenerModalidadProyecto(this.value)"-->
                        <div class="form-group" >
                            <div class="col-md-4" > 
                                <label class="control-label" >Plan de gobierno(*)</label>
                                <div  >
                                    <select name="seqPlanGobierno"
                                            id="seqPlanGobierno"
                                            style="width:250px;"                                            
                                            class="form-control required">
                                        <option value="0">Seleccione una opci&oacute;n</option>
                                        {foreach from=$arrPlanGobierno key=seqPlanGobierno item=txtPlanGobierno}
                                            <option value="{$seqPlanGobierno}" {if $value.seqPlanGobierno == $seqPlanGobierno} selected {/if}>{$txtPlanGobierno}</option>
                                        {/foreach}
                                    </select>
                                    <div id="val_seqPlanGobierno" class="divError">Debe seleccionar el Plan de gobierno</div>
                                </div>
                            </div>
                            <div class="col-md-4" > 
                                <label class="control-label" >Tipo de Modalidad (*)</label>
                                <div id="tdModalidad"  >
                                    <!--<select name="seqPryTipoModalidad"
                                            id="seqPryTipoModalidad"
                                            style="width:250px;" 
                                            class="form-control required">
                                        <option value="0">Seleccione una opci&oacute;n</option>
                                    {foreach from=$arrPryTipoModalidad key=seqPryTipoModalidad item=txtPryTipoModalidad}
                                        <option value="{$seqPryTipoModalidad}" {if $value.seqPryTipoModalidad == $seqPryTipoModalidad} selected {/if}>{$txtPryTipoModalidad}</option>
                                    {/foreach}
                                </select>-->

                                    <select name="seqPryTipoModalidad"
                                            id="seqPryTipoModalidad"
                                            style="width:250px;" 
                                            class="form-control required">
                                        <option value="0" {if $value.seqPryTipoModalidad == 0} selected {/if}>Seleccione una opci&oacute;n</option>
                                        <option value="1" {if $value.seqPryTipoModalidad == 1} selected {/if}>Adquisición de Vivienda Nueva</option>
                                        <option value="2" {if $value.seqPryTipoModalidad == 2} selected {/if}>Construcción en Sitio Propio</option>
                                        <option value="3" {if $value.seqPryTipoModalidad == 3} selected {/if}>Mejoramiento Habitacional</option>
                                        <option value="4" {if $value.seqPryTipoModalidad == 4} selected {/if}>Adquisición de Vivienda Usada</option>
                                        <option value="5" {if $value.seqPryTipoModalidad == 5} selected {/if}>Adquisición de Vivienda por Cierre Financiero</option>
                                    </select>
                                    <div id="val_seqPryTipoModalidad" class="divError">Debe seleccionar el Tipo de Modalidad</div>
                                </div>
                            </div>
                        </div>
                        <!-- NOMBRE DE LA OPV -->
                        <div class="form-group" id="lineaOpv" style="display:none" >
                            <div class="col-md-4"> 
                                <label class="control-label" >Nombre de la OPV (*)</table>
                                    <select name="seqOpv"
                                            id="seqOpv"
                                            style="width:250px"
                                            class="form-control"
                                            >
                                        <option value="0">Seleccione una opci&oacute;n</option>
                                        {foreach from=$arrOpv key=seqOpv item=txtNombreOpv}
                                            <option value="{$seqOpv}" {if $value.seqOpv == $seqOpv} selected {/if}>{$txtNombreOpv}</option>
                                        {/foreach}
                                    </select>
                                    <div id="val_seqOpv" class="divError">Este campo es requerido</div>
                            </div>  
                        </div>
                        <div class="form-group"  id="lineaTDirigida" style="display:none">
                            <div class="col-md-4"> 
                                <label class="control-label" >Nombre del Operador (*)</label>    
                                <input name="txtNombreOperador" id="txtNombreOperador" type="text"  value="{$value.txtNombreOperador}" onBlur="sinCaracteresEspeciales(this);" style="width:250px;"/>
                            </div>
                        </div>             
                        <div class="form-group"  id="lineaTDirigida" style="display:none">
                            <div class="col-md-4"> 
                                <label class="control-label" >Objeto del Proyecto (*)</label>   
                                <textarea name="txtObjetoProyecto" type="text" rows="2" id="txtObjetoProyecto"  class="form-group" onBlur="sinCaracteresEspeciales(this);" style="width:250px; height: 26px" >{$value.txtObjetoProyecto}</textarea>
                                <div id="val_txtObjetoProyecto" class="divError">Este campo es requerido</div>
                            </div>
                        </div>
                        <div class="form-group" id="idLineaProyectoUrbanizacion">
                            <div class="col-md-4"> 
                                <label class="control-label" >Tipo de Proyecto (*)</label> 
                                <select name="seqTipoProyecto"
                                        id="seqTipoProyecto"
                                        style="width:250px" 
                                        class="form-control required">
                                    <option value="0">Seleccione una opci&oacute;n</option>
                                    {foreach from=$arrTipoProyecto key=seqTipoProyecto item=txtTipoProyecto}
                                        <option value="{$seqTipoProyecto}" {if $value.seqTipoProyecto == $seqTipoProyecto} selected {/if}>{$txtTipoProyecto}</option>
                                    {/foreach}
                                </select>
                                <div id="val_seqTipoProyecto" class="divError">Debe seleccionar el Tipo de Proyecto</div>
                            </div>
                        </div>
                        <div class="form-group" id="idLineaProyectoUrbanizacion" >
                            <div class="col-md-4"> 
                                <label class="control-label" >Tipo de Urbanizaci&oacute;n (*)</label>    
                                <select name="seqTipoUrbanizacion"
                                        id="seqTipoUrbanizacion"
                                        style="width:250px" 
                                        class="form-control required">
                                    <option value="0">Seleccione una opci&oacute;n</option>
                                    {foreach from=$arrTipoUrbanizacion key=seqTipoUrbanizacion item=txtTipoUrbanizacion}
                                        <option value="{$seqTipoUrbanizacion}" {if $value.seqTipoUrbanizacion == $seqTipoUrbanizacion} selected {/if}>{$txtTipoUrbanizacion}</option>
                                    {/foreach}
                                </select>
                                <div id="val_seqTipoUrbanizacion" class="divError">Debe seleccionar el Tipo de Urbanización</div>
                            </div>
                        </div> 
                        <div class="form-group" id="idLineaTipoSolucionDescripcion">
                            <div class="col-md-4"> 
                                <label class="control-label" >Tipo de Soluci&oacute;n (*)</label> 
                                <select name="seqTipoSolucion"
                                        id="seqTipoSolucion"
                                        style="width:250px" 
                                        class="form-control required">
                                    <option value="0">Seleccione una opci&oacute;n</option>
                                    {foreach from=$arrTipoSolucion key=seqTipoSolucion item=txtTipoSolucion}
                                        <option value="{$seqTipoSolucion}" {if $value.seqTipoSolucion == $seqTipoSolucion} selected {/if}>{$txtTipoSolucion}</option>
                                    {/foreach}
                                </select>
                                <div id="val_seqTipoSolucion" class="divError">Debe seleccionar el Tipo de Solución</div>
                            </div>
                        </div>
                        <div class="form-group" >
                            <div class="col-md-4"> 
                                <label class="control-label" >Localidad (*)</label>  
                                <select name="seqLocalidad"
                                        id="seqlocalidad"
                                        onChange="obtenerBarrioProyecto(this, 'tdBarrio');"
                                        style="width:250px" 
                                        class="form-control required">
                                    <option value="0">Seleccione una opci&oacute;n</option>
                                    {foreach from=$arrLocalidad key=seqLocalidad item=txtLocalidad}
                                        <option value="{$seqLocalidad}" {if $value.seqLocalidad == $seqLocalidad} selected {/if}>{$txtLocalidad}</option>
                                    {/foreach}
                                </select>
                                <div id="val_seqlocalidad" class="divError">Debe seleccionar la Localidad</div>
                            </div>
                        </div>  
                        <div class="form-group" >
                            <div class="col-md-4" > 
                                <label class="control-label" >Barrio (*)</label> 
                                <span  id="tdBarrio">
                                    <select onFocus="this.style.backgroundColor = '#ADD8E6';" 
                                            onBlur="this.style.backgroundColor = '#FFFFFF';" 
                                            name="seqBarrio" 
                                            id="seqBarrio" 
                                            style="width:250px;" 
                                            class="form-control required">
                                        <option value="0">Seleccione</option>
                                        {if intval( $value.seqLocalidad ) != 0}
                                            {foreach from=$arrBarrio key=seqBarrio item=txtBarrio}
                                                <option value="{$seqBarrio}" 
                                                        {if $value.seqBarrio == $seqBarrio} 
                                                            selected 
                                                        {/if}
                                                        >{$txtBarrio}</option>            
                                            {/foreach}
                                        {/if}
                                    </select>
                                    <div id="val_seqBarrio" class="divError">Debe seleccionar el Barrio</div>
                                </span>
                            </div>
                        </div>
                        <div class="form-group" >
                            <div class="col-md-4"> 
                                <label class="control-label" >Grupo Proyecto (*)</label> 
                                <select name="seqProyectoGrupo"
                                        id="seqProyectoGrupo" 
                                        class="form-control required">
                                    <option value="0">Seleccione una opci&oacute;n</option>
                                    {foreach from=$arrProyectoGrupo key=seqProyectoGrupo item=txtProyectoGrupo}
                                        <option value="{$txtProyectoGrupo.seqProyectoGrupo}" {if $value.seqProyectoGrupo == $txtProyectoGrupo.seqProyectoGrupo} selected {/if}>{$txtProyectoGrupo.txtProyectoGrupo}</option>
                                    {/foreach}
                                </select>
                                <div id="val_seqProyectoGrupo" class="divError">Este campo es requerido</div> 
                            </div>
                        </div>                   
                        <div class="form-group" >
                            <div class="col-md-4"> 
                                <label class="control-label"  onclick="recogerDireccion('txtDireccion', 'objDireccionOcultoSolucion');" style="cursor: hand; text-decoration-line: underline">Direcci&oacute;n</label>
                                <input name="txtDireccion" type="text" id="txtDireccion" class="form-control required" value="{$value.txtDireccion}" txtDireccion style="width:150px;" readonly="true"/>
                                <div id="val_txtDireccion" class="divError">Este campo es requerido</div>
                            </div>
                        </div>

                        <div class="form-group" >
                            <div class="col-md-4"> 
                                <label class="control-label" >Nombre del Tutor (*)</label>
                                <select name="seqTutorProyecto"
                                        id="seqTutorProyecto"
                                        class="form-control required2">
                                    <option value="0">Seleccione una opci&oacute;n</option>
                                    {foreach from=$arrTutorProyecto key=seqTutorProyecto item=txtTutorProyecto}
                                        <option value="{$seqTutorProyecto}" {if $value.seqTutorProyecto == $seqTutorProyecto} selected {/if}>{$txtTutorProyecto}</option>
                                    {/foreach}
                                </select>
                                <div id="val_seqTutorProyecto" class="divError">Debe seleccionar un tutor para el Proyecto</div> 
                            </div>
                        </div>
                        <div class="form-group" >
                            <div class="col-md-4"> 
                                <label class="control-label" >Constructor (*)</label> 
                                <select name="seqConstructor"
                                        id="seqConstructor" 
                                        class="form-control required2">
                                    <option value="0">Seleccione una opci&oacute;n</option>
                                    {foreach from=$arrConstructor key=seqConstructor item=txtNombreConstructor}
                                        <option value="{$txtNombreConstructor.seqConstructor}" {if $value.seqConstructor == $txtNombreConstructor.seqConstructor} selected {/if}>{$txtNombreConstructor.txtNombreConstructor}</option>
                                    {/foreach}
                                </select>
                                <div id="val_seqConstructor" class="divError">Seleccione constructor</div> 
                            </div>
                        </div>
                        <div class="form-group" id="idLineaAreaLoteConstruida">
                            <div class="col-md-4"> 
                                <label class="control-label" >Area a Construir en <b>m²</b> (*)</label> 
                                <input name="valAreaConstruida" type="text" id="valAreaConstruida" class="form-control required2" value="{$value.valAreaConstruida}" onBlur="sinCaracteresEspeciales(this);
                                        soloNumeros(this);" style="width:65px;"/>
                                <div id="val_valAreaConstruida" class="divError">Este campo es requerido</div>    
                            </div>
                        </div> 
                        <div class="form-group">
                            <div class="col-md-4"> 
                                <label class="control-label" >N&uacute;mero Soluciones (*)</label>    
                                <input name="valNumeroSoluciones" type="text" id="valNumeroSoluciones" class="form-control required" value="{$value.valNumeroSoluciones}" 
                                       onBlur="sinCaracteresEspeciales(this);
                                               soloNumeros(this);"
                                       style="width:65px;"
                                       class="form-control required"/>                                       
                                <input name="valSalarioMinimo" type="hidden" id="valSalarioMinimo" value="{$valSalarioMinimo}"  class="form-control"/>
                                <input name="numSubsidios" type="hidden" id="numSubsidios" value="{$numSubsidios}"  class="form-control"/>
                                <div id="val_valNumeroSoluciones" class="divError">Debe diligenciar el número de soluciones</div>
                            </div>
                        </div>
                        <div class="form-group" >
                            <div class="col-md-4"> 
                                <label class="control-label" >N&uacute;m de Soluciones Disc (*)</label> 
                                <input name="numCantSolDisc" type="text" id="numCantSolDisc" class="form-control required3" value="{$value.numCantSolDisc}" onBlur="sinCaracteresEspeciales(this);
                                        soloNumeros(this);" style="width:65px;"/>
                                <div id="val_numCantSolDisc" class="divError">Este campo es requerido</div>
                            </div>
                        </div> 
                        <div class="form-group" >
                            <div class="col-md-4"> 
                                <label class="control-label" >Cant. Parqueaderos (*)</label> 
                                <input name="numParqueaderos" type="text" id="numParqueaderos" class="form-control required3" value="{$value.numParqueaderos}" onBlur="sinCaracteresEspeciales(this);
                                        soloNumeros(this);" style="width:65px;"/>
                                <div id="val_numParqueaderos" class="divError">Este campo es requerido</div>
                            </div>
                        </div> 
                        <div class="form-group" >
                            <div class="col-md-4"> 
                                <label class="control-label" >Parqueaderos Discapacitados (*)</label> 
                                <input name="numParqueaderosDisc" type="text" id="numParqueaderosDisc" class="form-control required3" value="{$value.numParqueaderosDisc}" onBlur="sinCaracteresEspeciales(this);
                                        soloNumeros(this);" style="width:65px;"/>
                                <div id="val_numParqueaderosDisc" class="divError">Este campo es requerido</div>
                            </div>
                        </div>  
                        <div class="form-group" >
                            <div class="col-md-4"> 
                                <label class="control-label" >Torres (*)</label> 
                                <input name="valTorres" type="text" id="valTorres" class="form-control required3" value="{$value.valTorres}" onBlur="sinCaracteresEspeciales(this);
                                        soloNumeros(this);" style="width:65px;"/>
                                <div id="val_valTorres" class="divError">Este campo es requerido</div>
                            </div>
                        </div> 
                        <div class="form-group" id="idLineaAreaLoteConstruida">
                            <div class="col-md-4"> 
                                <label class="control-label" >Area Lote en <b>m²</b> (*)</label> 
                                <input name="valAreaLote" type="text" id="valAreaLote" class="form-control required3" value="{$value.valAreaLote}" onBlur="sinCaracteresEspeciales(this);
                                        soloNumeros(this);" style="width:65px;"/>
                                <div id="val_valAreaLote" class="divError">Debe diligenciar el Area del Lote</div>
                            </div>
                        </div>


                        <div class="form-group" id="idLineaRegistroFechaEnajenacion">
                            <div class="col-md-4"> 
                                <label class="control-label" >Registro de Enajenaci&oacute;n (*)</label> 
                                <input name="txtRegistroEnajenacion" type="text" id="txtRegistroEnajenacion" value="{$value.txtRegistroEnajenacion}" onBlur="sinCaracteresEspeciales(this);" class="form-control required3" style="width:150px;" />
                                <div id="val_txtRegistroEnajenacion" class="divError">Debe diligenciar el Registro de Enajenación</div> 
                            </div>
                        </div>  
                        <div class="form-group" id="idLineaRegistroFechaEnajenacion">
                            <div class="col-md-4 inner-addon left-addon"> 
                                <label class="control-label" >Fecha Registro de Enajenaci&oacute;n  </label><br>
                                <div class="inner-addon left-addon">
                                    <i class="glyphicon glyphicon-calendar"></i>
                                    <input class="form-control" id="fchRegistroEnajenacion" name="fchRegistroEnajenacion" placeholder="yyyy/mm/dd" type="text" value="{$value.fchRegistroEnajenacion}"  readonly="" style="width: 100px;"/>
                                </div>
                                <div id="val_fchRegistroEnajenacion" class="divError" style="position: relative; float: left; width: 100%">Este campo es requerido</div>    
                            </div>
                        </div>
                        <div class="form-group"  id="idTituloDescEquipamientoComunal" >
                            <div class="col-md-4"> 
                                <label class="control-label">Descripci&oacute;n Equipamiento Comunal</label> 
                                <textarea id="txtDescEquipamientoComunal" name="txtDescEquipamientoComunal"  class="form-control required3" style="height: 30px">{$value.txtDescEquipamientoComunal}</textarea>
                                <div id="val_txtDescEquipamientoComunal" class="divError" style="position: relative; float: left; width: 100%">Este campo es requerido</div>  
                            </div>
                        </div>

                        <div id="idLineaLicenciaUrbanismo1" style="display:none"></div>
                        <div id="idLineaLicenciaUrbanismo2" style="display:none"></div>
                        <div id="idLineaLicenciaUrbanismo3" style="display:none"></div>
                        <div id="lineaProrrogaUrbanismo" style="display:none"></div>
                        <div id="idLineaLicenciaUrbanismo4" style="display:none"></div>

                        <div id="idLineaLicenciaConstruccion1" style="display:none"></div>
                        <div id="idLineaLicenciaConstruccion2" style="display:none"></div>
                        <div id="idLineaLicenciaConstruccion3" style="display:none"></div>
                        <div id="lineaProrrogaConstruccion" style="display:none"></div>

                        <!-- ******************************* class="form-control required" ************************************************** 
                        <div class="form-group" >
                            <div class="col-md-4"> 
                                <label class="control-label" ></label>                         
                            </div>
                        </div>                  
                        <div class="form-group" >
                            <div class="col-md-4"> 
                                <label class="control-label" ></label>                         
                            </div>
                        </div>                        
                        -->
                    </div>


                </fieldset>
                <p style="width: 100%;">&nbsp;</p>
                <fieldset>
                    <legend class="legend">
                        <h4 style="position: relative; float: left; width: 50%; margin: 0; padding: 5px;">
                            Datos Lote Mayor Extensi&oacute;n</h4>
                    </legend>
                    <div class="form-group">

                        <div class="col-md-4"> 
                            <label class="control-label">Cédula Catastral</label> <br>
                            <input name="txtCedulaCatastral" type="text" id="txtCedulaCatastral" value="{$value.txtCedulaCatastral}" onblur="sinCaracteresEspeciales(this);" style="width:200px;" class="form-control required3">
                            <div id="val_txtCedulaCatastral" class="divError">Este campo es requerido</div>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label">No. Escritura</label><br> 
                            <input name="txtEscritura" type="text" id="txtEscritura" value="{$value.txtEscritura}" onblur="sinCaracteresEspeciales(this);" class="form-control required3"  style="width: 30%; position: relative; float: left"> <b style="width: 5%; position: relative; float: left; left: 2%;top: 5px">Del</b>&nbsp;&nbsp;
                            <div class="inner-addon left-addon" style="position: relative;float: right;right: 20%; ">
                                <i class="glyphicon glyphicon-calendar" style="z-index: 1; padding-left: 0; margin-left: -3px"></i>
                                <input class="form-control" id="fchEscritura" name="fchEscritura" placeholder="yyyy/mm/dd" value="{$value.fchEscritura}" type="text"  readonly="" style="width: 100px; position: relative;float: right; right: 25%"/>
                            </div>
                            <div id="val_fchEscritura" class="divError" style="width: 50%; position: relative;float: left;">Este campo es requerido</div>
                            <div id="val_txtEscritura" class="divError" style="width: 50%; position: relative;float: right;">Este campo es requerido</div>
                        </div>  
                        <div class="col-md-4"> 
                            <label class="control-label">No. Notaría</label> 
                            <input name="numNotaria" type="text" id="numNotaria" value="{$value.numNotaria}" onblur="sinCaracteresEspeciales(this);" style="width:200px;" class="form-control required3">
                            <div id="val_numNotaria" class="divError">Este campo es requerido</div>
                        </div>
                        <div class="form-group" id="idLineaChipLoteMatricula" >
                            <div class="col-md-4"> 
                                <label class="control-label" >Chip Lote</label>
                                <input name="txtChipLote" type="text" id="txtChipLote" value="{$value.txtChipLote}" onBlur="sinCaracteresEspeciales(this);" class="form-control required2" style="width:150px;"/>
                            </div>
                            <div id="val_txtChipLote" class="divError">Debe diligenciar el Chip del Lote</div>    
                        </div>
                        <div class="form-group" id="idLineaChipLoteMatricula"  >
                            <div class="col-md-4"> 
                                <label class="control-label" >Matr&iacute;cula Inmobiliaria Lote (*)</label>  
                                <input name="txtMatriculaInmobiliariaLote" type="text" id="txtMatriculaInmobiliariaLote" value="{$value.txtMatriculaInmobiliariaLote}" onBlur="sinCaracteresEspeciales(this);" style="width:150px;" class="form-control required"/>
                                <div id="val_txtMatriculaInmobiliariaLote" class="divError">Debe diligenciar la Matricula Inmobiliaria del Lote</div> 
                            </div>
                        </div>  
                    </div>
                </fieldset>
                <p>&nbsp;</p>
                <div>
                    <fieldset>
                        <legend class="legend">
                            <h4 style="position: relative; float: left; width: 100%; margin: 0; padding: 5px;">
                                Datos del Vendedor
                            </h4>
                        </legend>
                        <div class="form-group">
                            <div class="col-md-4"> 
                                <label class="control-label">Nombre del vendedor</label> 
                                <input name="txtNombreVendedor" type="text" id="txtNombreVendedor" value="{$value.txtNombreVendedor}" onblur="sinCaracteresEspeciales(this);" style="width:200px;" class="form-control required5">
                                <div id="val_txtNombreVendedor" class="divError">Este campo es requerido</div>
                            </div>
                            <div class="col-md-2"> 
                                <label class="control-label">Telefono del vendedor</label> 
                                <input name="numTelVendedor" type="text" id="numTelVendedor" value="{$value.numTelVendedor}" onblur="sinCaracteresEspeciales(this);" style="width:200px;" class="form-control required5">
                                <div id="val_numTelVendedor" class="divError">Este campo es requerido</div>
                            </div>
                            <div class="col-md-3"> 
                                <label class="control-label">Nit</label>                         
                                <input name="numNitVendedor" type="text" id="numNitVendedor" value="{$value.numNitVendedor}" onblur=" sinCaracteresEspeciales(this);
                                        soloNumeros(this);" style="width:200px;" class="form-control required5">
                                <div id="val_numNitVendedor" class="divError">Este campo es requerido</div>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label" >Correo del Vendedor</label>   
                                <input name="txtCorreoVendedor" type="text" id="txtCorreoVendedor" value="{$value.txtCorreoVendedor}" onBlur="sinCaracteresEspeciales(this);" class="form-control required5"/>
                                <div id="val_txtCorreoOferente_{$cont}" class="divError">Debe diligenciar el correo del vendedor</div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <p>&nbsp;</p>
                <div>
                    <fieldset>
                        <legend class="legend">
                            <h4 style="position: relative; float: left; width: 100%; margin: 0; padding: 5px;">
                                Datos de Contacto del Proyecto 
                            </h4>
                        </legend>
                        <div  id="buildyourform">
                            {assign var=cont value = "0"}
                            {foreach from=$arrOferentesProy key=seqOferentesProy item=valueOferentesProy}
                                {assign var=cont value = $cont+1}
                                <div class="form-group" id="field{$seqOferentesProy+1}"> 
                                    <div id="table{$cont}">
                                        <div class="col-md-3">
                                            <label class="control-label" >Oferente (*)</label>                                      
                                            <input type="hidden" name="seqProyectoOferente[]" value="{$valueOferentesProy.seqProyectoOferente}" />
                                            <select name="seqOferente[]"
                                                    id="seqOferente_{$cont}" 
                                                    class="form-control required"
                                                    onchange="limpiarOferente({$cont});"
                                                    style="position: relative;float: left; width: 85%">
                                                <option value="0">Seleccione una opci&oacute;n</option>
                                                {foreach from=$arrOferente key=seqOferente item=valueOferente}
                                                    <option value="{$valueOferente.seqOferente}" {if $valueOferentesProy.seqOferente == $valueOferente.seqOferente} selected {/if}>{$valueOferente.txtNombreOferente}</option>
                                                {/foreach}
                                            </select>   
                                            <div id="val_seqOferente_{$cont}" class="divError" style="width: 100%; position: relative;float: left;">Debe seleccionar el oferente</div>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="control-label" >Nombre Contacto Oferente</label>   
                                            <input name="txtNombreContactoOferente[]" type="text" id="txtNombreContactoOferente_{$cont}" value="{$valueOferentesProy.txtNombreContactoOferente}" onBlur="sinCaracteresEspeciales(this);" class="form-control required" style="width:160px;"/>
                                            <div id="val_txtNombreContactoOferente_{$cont}" class="divError">Debe diligenciar el nombre de contacto oferente</div>
                                        </div>                                
                                        <div class="col-md-3">
                                            <label class="control-label" >Correo Contacto</label>   
                                            <input name="txtCorreoOferente[]" type="text" id="txtCorreoOferente_{$cont}" value="{$valueOferentesProy.txtCorreoOferente}" onBlur="sinCaracteresEspeciales(this);" class="form-control required" style="width:140px;"/>
                                            <div id="val_txtCorreoOferente_{$cont}" class="divError">Debe diligenciar el correo de contacto del Oferente</div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label" >Telefono Oferente</label>   
                                            <input name="numTelContactoOferente[]" type="text" id="numTelContactoOferente_{$cont}" value="{$valueOferentesProy.numTelContactoOferente}" onBlur="sinCaracteresEspeciales(this);
                                                    soloNumeros(this)" class="form-control required" style="position: relative; float: left;width:60%;"/>
                                            <img src="recursos/imagenes/add.png" width="20px" onclick="adicionarOferente();"  style="position: relative; float: left; width:20% "/>
                                            {if $cont > 1}
                                                <img src="recursos/imagenes/remove.png" width="20px"  style="position: relative; float: left; width:20% "onclick="removerOferente(table{$cont})"/>
                                            {/if}
                                            <div id="val_numTelContactoOferente_{$cont}" class="divError">Debe diligenciar el numero de contacto del Oferente</div>
                                        </div> 
                                    </div>
                                </div> 
                            {/foreach} 
                            {if !$arrOferentesProy|@count gt 0}
                                <div class="col-md-3">
                                    <label>Oferente (*)</label>                            
                                    <select name="seqOferente[]"
                                            id="seqOferente_1" 
                                            class="form-control required" 
                                            onchange="limpiarOferente(1);"
                                            style="
                                            position: relative;
                                            float: left;
                                            width: 85%;">
                                        <option value="0">Seleccione una opci&oacute;n</option>
                                        {foreach from=$arrOferente key=seqOferente item=valueOferente}
                                            <option value="{$valueOferente.seqOferente}" {if $value.seqOferente == $valueOferente.seqOferente} selected {/if}>{$valueOferente.txtNombreOferente}</option>
                                        {/foreach}
                                    </select>
                                    <input type="hidden" name="seqProyectoOferente[]" value="0" />
                                </div>
                                <div id="val_seqOferente1" class="divError">Debe seleccionar el oferente</div>
                                <div class="col-md-3">
                                    <label class="control-label" >Nombre Contacto Oferente</label>   
                                    <input name="txtNombreContactoOferente[]" type="text" id="txtNombreContactoOferente_1" value="{$valueOferentesProy.txtNombreContactoOferente}" onBlur="sinCaracteresEspeciales(this);" class="form-control " style="width:180px;"/>
                                    <div id="val_txtNombreContactoOferente" class="divError">Debe diligenciar el nombre de contacto del Oferente</div>
                                </div>                                
                                <div class="col-md-3">
                                    <label class="control-label" >Correo Contacto</label>   
                                    <input name="txtCorreoOferente[]" type="text" id="txtCorreoOferente_1" value="{$valueOferentesProy.txtCorreoOferente}" onBlur="sinCaracteresEspeciales(this);" class="form-control" style="width:150px;"/>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label" >Telefono Contacto</label>   
                                    <input name="numTelContactoOferente[]" type="text" id="numTelContactoOferente" value="{$valueOferentesProy.numTelContactoOferente}" onBlur="sinCaracteresEspeciales(this);
                                            soloNumeros(this);" class="form-control" style="position: relative; float: left;width:70%;"/>
                                    <img src="recursos/imagenes/add.png" width="20px" onclick="adicionarOferente();"  style="position: relative; float: left; width:20% "/>
                                    <div id="val_numTelContactoOferente_1" class="divError">Debe diligenciar el numero de contacto del Oferente</div>
                                </div>
                            {/if}
                        </div>
                    </fieldset>
                </div>
            </div>
            <div id="seg" class="tab-pane"  role="tabpanel" aria-labelledby="profile-tab" style="max-height: 550px; overflow-y: scroll">
                <div class="form-group" >
                    <div class="col-md12" style="padding: 20px"> 
                        {include file="seguimientoProyectos/seguimientoFormulario.tpl"}
                        <div id="contenidoBusqueda" >
                            {include file="seguimientoProyectos/buscarSeguimiento.tpl"}
                        </div>                 
                    </div>
                </div> 
            </div>
            <div id="licencias" class="tab-pane"  role="tabpanel" aria-labelledby="profile-tab" style="max-height: 550px; overflow-y: scroll">
                {include file="proyectos/vistas/inscripcionLicencias.tpl"}
            </div> 
            <div id="financiero" class="tab-pane"  role="tabpanel" aria-labelledby="profile-tab" style="max-height: 550px; overflow-y: scroll">
                {include file="proyectos/vistas/inscripcionFinanciero.tpl"}
            </div> 
            <!-- TIPOS DE VIVIENDA (ESTRUCTURA DEL PROYECTO) -->
            <div id="tiposVivienda" class="tab-pane"  role="tabpanel" aria-labelledby="profile-tab" style="max-height: 550px; overflow-y: scroll">
                {include file="proyectos/secTipoVivienda.tpl"}
            </div>

            <!-- CONJUNTOS RESIDENCIALES (SUBPROYECTOS) -->
            <div id="conjuntosResidenciales" class="tab-pane"  role="tabpanel" aria-labelledby="profile-tab" style="max-height: 550px; overflow-y: scroll">
                {include file="proyectos/secConjuntoResidencial.tpl"}
            </div>

            <!-- CRRONOGRAMA DE OBRAS -->
            <div id="datosCronograma" class="tab-pane"  role="tabpanel" aria-labelledby="profile-tab" style="max-height: 550px; overflow-y: scroll">
                {include file="proyectos/secCronogramaFechas.tpl"}
            </div>
            <div id="datosPolizas" class="tab-pane"  role="tabpanel" aria-labelledby="profile-tab" style="max-height: 550px; overflow-y: scroll">
                {include file="proyectos/vistas/inscripcionPoliza.tpl"}
                <div class="tab-pane">
                    {include file="proyectos/vistas/inscripcionFiducia.tpl"}
                </div>
            </div>
            <div id="datosComite" class="tab-pane"  role="tabpanel" aria-labelledby="profile-tab" style="max-height: 550px; overflow-y: scroll">
                {include file="proyectos/vistas/inscripcionComite.tpl"}
            </div>
            <div id="actosAdmon" class="tab-pane"  role="tabpanel" aria-labelledby="profile-tab" style="max-height: 550px; overflow-y: scroll; text-align: left; font-size: 13  px">
                <legend class="legend">
                    <h4 style="position: relative; float: left; width: 100%; margin: 0; padding-top: 3%">
                        Datos de Actos Administrativos                             
                    </h4>
                </legend><p>&nbsp;</p>

                <fieldset style="border: 1px dotted #024457; width: 95%;margin-left: 10px; padding: 5px;">

                    <div class="form-group" style="padding: 2%">
                        <div class="col-md-4"><label class="control-label">Resolución de asignación N°:</label></div>
                        <div class="col-md-4"><label class="control-label">Cantidad de unidades:</label></div>
                        <div class="col-md-4"><label class="control-label">Valor Total de Asignación:</label></div>
                    </div>
                    <div class="form-group" style="padding-left: 2%; color: #204d74;margin-top: 5px; margin-bottom: 0;">
                        <div class="col-md-4">{$arrFinanciera.$seqProyecto.aprobado.numero} de {$arrFinanciera.$seqProyecto.aprobado.fecha}  </div>
                        <div class="col-md-4">{$arrFinanciera.$seqProyecto.aprobado.unidades|@count} SFV por </div>
                        <div class="col-md-4">$ {$arrFinanciera.$seqProyecto.aprobado.valor|number_format:0:',':'.'} </div>
                    </div>  
                    <br><br>
                    <div class="form-group" style="padding: 2%">
                        <div class="col-md-4"><label class="control-label">Resolución de indexación N°:</label></div>
                        <div class="col-md-4"><label class="control-label">Cantidad de unidades </label></div>
                        <div class="col-md-4"><label class="control-label">Valor Total de Indexación: </label></div>
                    </div>  
                    {foreach from=$arrFinanciera.$seqProyecto.indexado.detalle key=seqUnidadActo item=arrResolucion}
                        <div class="form-group" style="padding-left: 2%; color: #204d74;margin-top: 5px; margin-bottom: 0;">
                            <div class="col-md-4">{$arrResolucion.numero} de {$arrResolucion.fecha}<br></div>
                            <div class="col-md-4">{$arrResolucion.unidades|@count} SFV por<br></div>
                            <div class="col-md-4"> $ {$arrResolucion.valor|number_format:0:',':'.'}<br></div>
                        </div>
                    {/foreach} 
                    <br><br>
                    <div class="form-group" style="padding: 2%">
                        <div class="col-md-4"><label class="control-label">Resolución de Modificación N°:</label></div>
                        <div class="col-md-4"><label class="control-label">Cantidad de unidades </label></div>
                        <div class="col-md-4"><label class="control-label">Valor Total de Modificación:</label></div>
                    </div> 
                    {foreach from=$arrFinanciera.$seqProyecto.menor.detalle key=seqUnidadActo item=arrResolucion} 
                        <div class="form-group" style="padding-left: 2%; color: #204d74;margin-top: 5px; margin-bottom: 0;">
                            <div class="col-md-4">{$arrResolucion.numero} de {$arrResolucion.fecha}</div>
                            <div class="col-md-4">{$arrResolucion.unidades} SFV por {$seqProyecto}</div>  
                            <div class="col-md-4">  $ {$arrResolucion.valor|number_format:0:',':'.'}</div>
                        </div>
                    {/foreach}
                    <br><br>
                </fieldset>
                <br><br>
            </div> 
        {/foreach}
        {*    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">HISTORIAL DE SEGUIMIENTOS</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body" style="height: 45%">
        {foreach from=$arrayDocumentos key=keyDoc item=valueDoc}
        <div class="form-group" >                       
        <div class="col-md-8" style="text-align: left"> 
        <label class="control-label" >{$valueDoc.txtNombreDocumento}</label>  
        </div>
        <div class="col-md-3" style="text-align: left; ">                            
        <input type="file" id="file" name="file">
        <input type="hidden" name="documentId_{$value.seqProyecto}[{$valueDoc.seqDocumento}]" value="{$valueDoc.seqDocumento}" />
        </div> 
        <div class="col-md-1" style="text-align: left"> 
        <input type="checkbox" name="document_{$value.seqProyecto}[{$valueDoc.seqDocumento}]"  value="{$valueDoc.seqDocumento}" {if $valueDoc.bolEstado == 1 } checked {/if}/>                           
        </div>
        </div>  
        {/foreach}
        </div>                
        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>                
        </div>
        </div>
        </div>
        </div> *}
    </div>
</form>
<div id="postulacionTabView"></div>
<div id="objDireccionOculto" style="display:none"></div>
<div id="divCalendar" style="display:none"></div>
<div id="objDireccionOcultoSolucion" style="display:none"></div>
<!-- ******************************************** INICIO DE MODALES INFORMATIVOS ********************************* -->

<!-- Modal -->

