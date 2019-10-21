<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$txtPrefijoRuta = "../../../";
include( $txtPrefijoRuta . "recursos/archivos/verificarSesion.php" );
include( $txtPrefijoRuta . "recursos/archivos/lecturaConfiguracion.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['funciones'] . "funciones.php" );
include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/inclusionSmarty.php" );
include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/coneccionBaseDatos.php" );

include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "funciones.class.php" );
$clafunciones = new funciones();

$numDocumento = $_REQUEST['numDocumento'];
$tipoDocumento = $_REQUEST['tipoDoc'];
$seqFormulario = $clafunciones->consultarFormulario($tipoDocumento, $numDocumento);
$datosBasicos = $clafunciones->consultaBasicaHogar($seqFormulario);
$miembrosHogar = $clafunciones->consultaMiembros($seqFormulario);
$datosSeguimientos = $clafunciones->consultaSegumientosHogar($seqFormulario);
?>
<div>
    <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <tr>
            <th colspan="6"><h4>DATOS BÁSICOS</h4></th>
        </tr>
        <?php
        $cont = 0;
        foreach ($datosBasicos[0] as $key => $value) {
            ?>
            <?php if ($cont % 3 == 0) { ?> <tr>

                <?php } else { ?>
                    <th><h5><?= $key ?></h5></th>                  
                    <td> <?= $value ?></td>  
                    <td>&nbsp;</td>
                <?php } if ($cont % 3 == 0) { ?> </tr> <?php } ?>



            <?php
            $cont++;
        }
        ?>
    </table>
    <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">

        <thead>
            <tr>
                <th colspan="9"><h4>MIEMBROS DEL HOGAR</h4></th>
        </tr>
        <tr>
            <th class="th-sm">Ciudadano </th>
            <th class="th-sm">Parentesco</th>
            <th class="th-sm">Estado Civil</th>
            <th class="th-sm">Documento</th>
            <th class="th-sm">Tipo de Documento </th>
            <th class="th-sm">Nombre  </th>
            <th class="th-sm">Ingresos  </th>
            <th class="th-sm">Tipo de Victima  </th>
            <th class="th-sm">Grupo LGTBI  </th>
        </tr>
        </thead>
        <tbody>  
            <?php foreach ($miembrosHogar as $key => $value) { ?>   
                <tr>
                    <td><?= $value['Ciudadano'] ?></td>
                    <td><?= $value['Parentesco'] ?></td>
                    <td><?= $value['Estado Civil'] ?></td>
                    <td><?= $value['Documento'] ?></td>
                    <td><?= $value['Tipo de Documento'] ?></td>
                    <td><?= $value['Nombre'] ?></td>
                    <td><?= $value['Ingresos'] ?></td>
                    <td><?= $value['Tipo de Victima'] ?></td>
                    <td><?= $value['Grupo LGTBI'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <th class="th-sm">Ciudadano</th>
                <th class="th-sm">Parentesco</th>
                <th class="th-sm">Estado Civil</th>
                <th class="th-sm">Documento</th>
                <th class="th-sm">Tipo de Documento </th>
                <th class="th-sm">Nombre </th>
                <th class="th-sm">Ingresos  </th>
                <th class="th-sm">Tipo de Victima  </th>
                <th class="th-sm">Grupo LGTBI </th>
            </tr>
        </tfoot>
    </table>
    <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th colspan="9"><h4>SEGUIMIENTOS DEL HOGAR</h4></th>
        </tr>
        <tr>
            <th class="th-sm">Fecha Movimiento</th>               
            <th class="th-sm" style="width: 50%">Comentario</th>
            <th class="th-sm">Usuario </th>
          

        </tr>
        </thead>
        <tbody>  
            <?php foreach ($datosSeguimientos as $key => $value) { ?>   
                <tr>                   
                    <td><?= $value['fchMovimiento'] ?></td>                 
                    <td><?= $value['txtComentario'] ?></td>
                    <td><?= $value['txtAtendido'] ?></td>
                  

                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>              
                <th class="th-sm">Fecha Movimiento</th>               
                <th class="th-sm">Comentario</th>
                <th class="th-sm">Usuario </th>                
            </tr>
        </tfoot>
    </table>
</div>