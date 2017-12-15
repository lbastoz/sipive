&nbsp;
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
      integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">[{$claCruces->arrDatos.seqCruce}] {$claCruces->arrDatos.txtNombre}</h4>
    </div>
    <div class="panel-body">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#datos" data-toggle="tab">Datos del Cruce</a></li>
            <li><a href="#hogares" data-toggle="tab">Hogares Vinculados</a></li>
            <li><a href="#auditoria" data-toggle="tab">Auditoria</a></li>
        </ul>
        <div class="tab-content" style="border-left: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; border-right: 1px solid #DDDDDD;">
            <div class="tab-pane active" id="datos" style="padding: 10px;">
                <form method="post" onSubmit="return false;" id="frmModificaCruce">
                    <table cellpadding="0" cellspacing="0" class="table table-striped">
                        <tr>
                            <td width="150px"><strong>Fecha de Creación</strong></td>
                            <td colspan="3">
                                {$claCruces->arrDatos.fchCreacionCruce->format("Y-m-d")}
                            </td>
                        </tr>
                        <tr>
                            <td width="150px"><strong>Fecha de Actualización</strong></td>
                            <td>
                                {if $claCruces->arrDatos.fchActualizacionCruce != null}
                                    {$claCruces->arrDatos.fchActualizacionCruce->format("Y-m-d")}
                                {/if}
                            </td>
                            <td><strong>Fecha de Publicación</strong></td>
                            <td>
                                <input type="text"
                                       id="fchCruce"
                                       name="fchCruce"
                                       value="{$claCruces->arrDatos.fchCruce->format("Y-m-d")}"
                                       style="width: 100px"
                                       onfocus="calendarioPopUp('fchCruce')"
                                       required
                                >
                            </td>
                        </tr>
                        <tr>
                            <td width="150px"><strong>Usuario de Creación</strong></td>
                            <td>
                                {$claCruces->arrDatos.txtUsuario}
                            </td>
                            <td width="150px"><strong>Usuario de Actualización</strong></td>
                            <td>
                                {$claCruces->arrDatos.txtUsuarioActualiza}
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Cuerpo</strong></td>
                            <td colspan="3">
                                {$claCruces->arrDatos.txtCuerpo}
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Pie</strong></td>
                            <td colspan="3">
                                {$claCruces->arrDatos.txtPie}
                            </td>
                        </tr>
                        <tr>
                            <td>Firma</td>
                            <td colspan="3">
                                {$claCruces->arrDatos.txtFirma}
                            </td>
                        </tr>
                        <tr>
                            <td>Elaboró</td>
                            <td colspan="3">
                                {$claCruces->arrDatos.txtElaboro}
                            </td>
                        </tr>
                        <tr>
                            <td>Revisó</td>
                            <td colspan="3">
                                {$claCruces->arrDatos.txtReviso}
                            </td>
                        </tr>
                        <tr>
                            <td>Archivo</td>
                            <td colspan="3">
                                <input type="file" name="archivo">
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="seqCruce" value="{$claCruces->arrDatos.seqCruce}">
                </form>
            </div>
            <div class="tab-pane" id="hogares" style="padding: 10px;">
                <table data-order='[[ 0, "asc" ]]' id="listadoCruces" class="table table-condensed table-hover" width="840px">
                    <thead>
                        <tr>
                            <th align="center">Documento</th>
                            <th align="center">Nombre</th>
                            <th align="center">Estado</th>
                            <th align="center">&nbsp;</th>
                            <th align="center">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$arrVer key=seqFormulario item=arrResultado}
                            <tr>
                                <td>{$arrResultado.documento}</td>
                                <td>{$arrResultado.nombre}</td>
                                <td>{$arrResultado.estado}</td>
                                <td>
                                    <a href="#" onClick="location.href='./contenidos/cruces2/exportar.php?seqCruce={$claCruces->arrDatos.seqCruce}&seqFormulario={$seqFormulario}'">
                                        Exportar
                                    </a>
                                </td>
                                <td align="center">
                                    {if $arrResultado.inhabilitar == 1}
                                        <a class="label label-danger"
                                           onClick="popUpPdfCasaMano('exportarPdf.php', 'exportar[]={$seqFormulario}', {$claCruces->arrDatos.seqCruce});"
                                        >Pendiente
                                        </a>
                                    {else}
                                        <span class="label label-success">Sin Cruces</span>
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            <div class="tab-pane" id="auditoria" style="padding: 10px; height: 400px; overflow: auto;">
                <table data-order='[[ 0, "asc" ]]' id="auditoriaCruces" class="table table-condensed table-hover" width="840px">
                    <thead>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Principal</th>
                        <th>Docuemnto</th>
                        <th>Entidad</th>
                        <th>Causa</th>
                        <th>Detalle</th>
                        <th>Inhabilitar</th>
                        <th>Observaciones</th>
                    </thead>
                    <tbody>
                        {foreach from=$claCruces->arrAuditoria item=arrAuditoria}
                            <tr>
                                <td>{$arrAuditoria.fchMovimiento}</td>
                                <td>{$arrAuditoria.txtUsuario}</td>
                                <td>{$arrAuditoria.numDocumentoPrincipal}</td>
                                <td>{$arrAuditoria.numDocumento}</td>
                                <td>{$arrAuditoria.txtEntidad}</td>
                                <td>{$arrAuditoria.txtCausa}</td>
                                <td>{$arrAuditoria.txtDetalle}</td>
                                <td>
                                    {if $arrAuditoria.bolInhabilitar == 1}
                                        <span class="label label-danger">Pendiente</span>
                                    {else}
                                        <span class="label label-success">Sin Cruces</span>
                                    {/if}
                                </td>
                                <td>{$arrAuditoria.txtObservacion}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="panel-footer" align="center">
        <button type="button" class="btn btn-primary" style="width: 100px" onClick="someterFormulario('contenido',document.getElementById('frmModificaCruce'),'./contenidos/cruces2/editar.php',true,true);">
            Salvar
        </button>&nbsp;
        <button type="button" class="btn btn-danger" style="width: 100px"
                onClick="popUpPdfCasaMano('exportarPdf.php', '', {$claCruces->arrDatos.seqCruce});"
        >
            Cartas PDF
        </button>&nbsp;
        <button type="button" class="btn btn-success" style="width: 100px"
                onClick="location.href='./contenidos/cruces2/exportar.php?seqCruce={$claCruces->arrDatos.seqCruce}'"
        >
            Excel
        </button>&nbsp;
        <button type="button" class="btn btn-default" onclick="cargarContenido('contenido','./contenidos/cruces2/cruces.php','',true);" style="width: 100px">
            Volver
        </button>
    </div>
</div>

<div id="listadoCrucesListener"></div>
<div id="auditoriaCrucesListener"></div>