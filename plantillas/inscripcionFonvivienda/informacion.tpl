
<link rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
      crossorigin="anonymous"
>

{if not empty($claInscripcion->arrErrores.general)}
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {foreach from=$claInscripcion->arrErrores.general item=txtError}
            <li>{$txtError}</li>
        {/foreach}
    </div>
{/if}

{if not empty($claInscripcion->arrMensajes)}
    <div class="alert alert-success alert-dismissible" role="alert" style="font-size: 12px">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Hecho!</strong> {$claInscripcion->arrMensajes.0}
    </div>
{/if}

<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Información del cargue</h6>
    </div>
    <div class="panel-body">

        {if $claInscripcion->seqEstado == 2}
            <div id="progreso" style="height: 150px;">
                {include file="inscripcionFonvivienda/barraProgreso.tpl"}
            </div>
        {else}

            <table id="listadoAadPry" class="table table-hover" data-order='[[ 0, "desc" ]]' width="850px">
                <thead>
                <tr>
                    <th>Id Hogar</th>
                    <th>Datos</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    {foreach from=$claInscripcion->arrHogares key=numHogar item=arrHogar}
                        <tr>
                            <td class="h4" width="30px">{$numHogar}</td>
                            <td>
                                <strong>Modalidad:</strong> {$arrHogar.txtModalidad} <br>
                                <strong>Esquema:</strong> {$arrHogar.txtTipoEsquema} <br>
                                <strong>Rango de Ingresos:</strong> {$arrHogar.txtRangoIngresos} <br>
                                <strong>Solución:</strong> {$arrHogar.txtDescripcion} <br>
                                <strong>Dirección Solución:</strong> {$arrHogar.txtDireccionSolucion}
                            </td>
                            <td width="150px">
                                <h5>
                                    {if $arrHogar.seqEstadoHogar == 4}
                                        <span class="text-success">{$arrHogar.txtEstadoHogar}</span>
                                    {else}
                                        {$arrHogar.txtEstadoHogar}
                                    {/if}
                                </h5>
                            </td>
                            <td width="100px">
                                <button type="button"
                                        class="btn btn-primary btn-sm"
                                        onclick="cargarContenido('contenido','./contenidos/inscripcionFonvivienda/detalles.php','seqCargue={$claInscripcion->seqCargue}&numHogar={$numHogar}')"
                                >
                                    Detalles
                                </button>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
            <div id="listadoAadProyectos"></div>

        {/if}

    </div>
    <div class="panel-footer text-center">
        <div class="row text-center">
            <div class="{if $claInscripcion->seqEstado != 2} col-sm-offset-3 {else} col-sm-offset-4 {/if} col-sm-4 text-center">
                <button type="button"
                        class="btn btn-default btn-sm"
                        onclick="cargarContenido('contenido','./contenidos/inscripcionFonvivienda/inscripcionFonvivienda.php','',true);"
                >
                    Volver
                </button>
            </div>
            <div class="col-sm-4 text-center">
                {if $claInscripcion->seqEstado != 2}
                    <button type="button"
                            class="btn btn-danger btn-sm {if $bolProcesar == false} disabled {/if}"
                            onclick="cargarContenido('contenido','./contenidos/inscripcionFonvivienda/procesarCargue.php','seqCargue={$claInscripcion->seqCargue}',true);"
                            {if $bolProcesar == false} disabled {/if}
                    >
                        Procesar Cargue
                    </button>
                {/if}
            </div>
        </div>
    </div>
</div>
