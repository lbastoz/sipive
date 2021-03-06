<?php
$txtPrefijoRuta = "../../";
include( $txtPrefijoRuta . "recursos/archivos/verificarSesion.php" );
include( $txtPrefijoRuta . "recursos/archivos/lecturaConfiguracion.php" );
include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/inclusionSmarty.php" );
include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/coneccionBaseDatos.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "calificacion.class.php" );

include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "FormularioSubsidios.class.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "Encuestas.class.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['funciones'] . "funciones.php" );

$claCalificacion = new calificacion();
$claEncuestas = new Encuestas();
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<link rel="stylesheet" href="./librerias/jquery/css/bootstrap.min.css"/> 
<link href="./librerias/bootstrap/css/bootstrap.css" rel="stylesheet" />        
<link href="./librerias/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
<link href="./recursos/estilos/inputFile.css" rel="stylesheet" />
<style>
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{
        padding: 3px;
        margin: 0;
        line-height: 1;
    }
</style>
<?php
$arrayIndicadores = $claCalificacion->listarIndicadores();

include './calculoCalificacion.php';
$documento = str_replace('.', '', $_POST['documento']);
$formularios = $claCalificacion->obtenerFormulario($documento);

$doc = "";
foreach ($formularios as $keyF => $valueF) {
    $doc .= $valueF['seqFormulario'];
}


if ($doc == "") {
    echo "Este documento no se encuentra afiliado a un formulario por favor rectificar el número";
    die();
}
$arrayCalificacion = $claCalificacion->datosUltimaCalificacion($doc);
$sumTotalCalificacion = $claCalificacion->datosSumaTotalCalificacion($doc);
//echo ($sumTotalCalificacion);
$arraySalud = $claCalificacion->obtenerDatosSalud();

$ejecutaConsultaPersonalizada = true;
$arraDatosActuales = $claCalificacion->obtenerDatosCalificacion($ejecutaConsultaPersonalizada, $doc, false);

$array = calcularCalificacion($arraDatosActuales);


$arrayDatosEncuentas = $claEncuestas->obtenerVariablesCalificacion($documento);
if (count($arrayDatosEncuentas['errores']) > 0) {
    echo $arrayDatosEncuentas['errores'][0];
    Die();
}
$claCalificacion->obtenerValorIndicadores();
$totalcalBle = 0;
$totalRSA = 0;
$totalCohabitacion;
$totalHacinamiento = 0;
$totalIPC = 0;
$totalTDE = 0;
$totalHN12 = 0;
$totalMCF = 0;
$totalHAMY = 0;
$totalCDISC = 0;
$totalHPGE = 0;
$totalHN18 = 0;
$totalHCF = 0;
$totalPLGTBI = 0;
$totalPPGD = 0;
$totalRFPB = 0;

$totalEnccalBle = 0;
$totalEncRSA = 0;
$totalEncCohabitacion;
$totalEncHacinamiento = 0;
$totalEncIPC = 0;
$totalEncTDE = 0;
$totalEncHN12 = 0;
$totalEncMCF = 0;
$totalEncHAMY = 0;
$totalEncCDISC = 0;
$totalEncHPGE = 0;
$totalEncHN18 = 0;
$totalEncHCF = 0;
$totalEncPLGTBI = 0;
$totalEncPPGD = 0;
$totalEncRFPB = 0;

$ingresos1 = 0;
$miembros1 = 0;
$totalUltCal = 0;

//var_dump($arraDatosActuales);
if ($arrayIndicadores) {
    $cont = 0;
    ?>    
    <div class="col-lg-12 col-md-12" >
        <table id="example" class="table" cellspacing="0">
            <?php if ($arraDatosActuales) { 
                foreach ($arraDatosActuales as $keyAct => $valueAct) {

                    $calcEducacion = ($valueAct['aprobados'] / ($valueAct['cantMayor']));
                    $educacion = 0;
                    if ($calcEducacion < 9) {
                        $educacion = 1;
                    } else if ($valueAct['cantMayor'] == 0) {
                        $educacion = 1;
                    } else {
                        $educacion = 0;
                    }
                    $totalcalBle = ($claCalificacion->BLE * ($educacion * 100));
                    $saludSubsidiados = 0;
                    $saludSubsidiados = ($valueAct['afiliacion'] / $valueAct['cant']);
                    $totalRSA = $claCalificacion->RSA * ($saludSubsidiados * 100);

                    $cohabitacion = 0;
                    if ($valueAct['cohabitacion'] > 1) {
                        $cohabitacion = 1;
                    }
                    $totalCohabitacion = $claCalificacion->COH * ($cohabitacion * 100);

                    $dormitorios = $valueAct['dormitorios'];
                    $hacinamiento = 0;
                    $calchacinamiento = 0;
                    if ($dormitorios != 0) {
                        $calchacinamiento = ($valueAct['cant'] / $dormitorios);
                        if ($calchacinamiento >= 4) {
                            $hacinamiento = 1;
                        } else {
                            $hacinamiento = 0;
                        }
                    } else
                        $hacinamiento = 1;

                    $totalHacinamiento = $claCalificacion->HACN * ($hacinamiento * 100);

                    $ingresos = $valueAct['ingresos'] / $valueAct['cant'];
                    $arrConfiguracion['constantes']['salarioMinimo'] . "/" . ($ingresos + 1000);
                    $totalIngresos = ($arrConfiguracion['constantes']['salarioMinimo']) / ($ingresos + 1000);
                    $totalIPC = ($claCalificacion->IPC * (100 * (1 - exp(-$totalIngresos / 52.05))));

                    $dependenciaEcon = 0;
                    $adultos = 0;
                    if ($valueAct['adultos'] > 0) {
                        $adultos = $valueAct['cant'] / $valueAct['adultos'];
                    } else {
                        $adultos = 3.5;
                    }
                    //echo "<br>".$value['aprobadosJefe']; 
                    if ($adultos > 3 && $valueAct['aprobadosJefe'] <= 2) {
                        $dependenciaEcon = 1;
                    }
                    $totalTDE = ($claCalificacion->TDE * ($dependenciaEcon * 100));

                    $menores = $valueAct['cantMenores'] / $valueAct['cant'];
                    $totalHN12 = ($claCalificacion->HN12 * ($menores * 100));
                    $monoparentalFem = 0;

                    $tipo = 0;
                    if ($valueAct['mujerCabHogar'] == 1) {
                        $tipo = 1;
                    }
                    if ($valueAct['mujerCabHogar'] == 1 && $valueAct['conyugueHogar'] == 0 && $valueAct['cantHijos'] > 0) {
                        $monoparentalFem = 1;
                    }
                    $totalMCF = ($claCalificacion->MCF * ($monoparentalFem * 100));

                    $cantAdultoMayor = $valueAct['cantadultoMayor'] / $valueAct['cant'];
                    $totalHAMY = $claCalificacion->HAMY * ($cantAdultoMayor * 100);

                    $discapacidad = $valueAct['cantCondEspecial'] / $valueAct['cant'];
                    $totalCDISC = ($claCalificacion->CDISC * ($discapacidad * 100));

                    $grupoEtnico = 0;
                    if ($valueAct['condicionEtnica'] > 0) {
                        $grupoEtnico = 1;
                    }
                    $totalHPGE = ($claCalificacion->HPGE * ($grupoEtnico * 100));

                    $cantAdolecentes = $valueAct['adolecentes'] / $valueAct['cant'];

                    $totalHN18 = ($claCalificacion->HN18 * ($cantAdolecentes * 100));

                    $monoparentalMasc = 0;
                    $tipo = 0;
                    if ($valueAct['hombreCabHogar'] == 1) {
                        $tipo = 2;
                    }
                    if ($valueAct['hombreCabHogar'] == 1 && $valueAct['conyugueHogar'] == 0 && $valueAct['cantHijos'] > 0) {
                        $monoparentalMasc = 1;
                    }
                    $totalHCF = ($claCalificacion->HCF * ($monoparentalMasc * 100));


                    $grupoLGTBI = 0;
                    if ($valueAct['grupoLgtbi'] > 0) {
                        $grupoLGTBI = 1;
                    }
                    $totalPLGTBI = ($claCalificacion->PLGTBI * ($grupoLGTBI * 100));

                    $programa = 0;

                    if ($valueAct['bolIntegracionSocial'] > 0 || $valueAct['bolSecMujer'] > 0 || $valueAct['bolIpes'] > 0) {
                        $programa = 1;
                    }
                    if ($valueAct['bolSecMujer'] == "") {
                        $valueAct['bolSecMujer'] = 0;
                    }

                    $totalPPGD = ($claCalificacion->PPGD * ($programa * 100));

                    $bolRFPB = 0;
                    if ($valueAct['bolReconocimientoFP'] > 0) {
                        $bolRFPB = 1;
                    }
                    $totalRFPB = ($claCalificacion->RFPB * ($bolRFPB * 100));
                    ?>
                    <tr>
                        <th>Miembros de Hogar</th>
                        <td><?php echo $valueAct['cant'] ?></td>
                        <th>Ingresos</th>
                        <td><?php echo $valueAct['ingresos'] ?></td>                       
                        <th>Miembros Mayores (Resago)</th>
                        <td><?php echo $valueAct['cantMayor'] ?></td>   
                    </tr>
                    <tr>
                        <th>Miembros Adultos entre 15 y 60 años </th>
                        <td><?php echo $valueAct['adultos'] ?></td>                       
                        <th>Años Aprobados Jefe </th>
                        <td><?php echo $valueAct['aprobadosJefe'] ?></td>                       
                        <th>Años Aprobados Miembros </th>
                        <td><?php echo $valueAct['aprobados'] ?></td> 
                    </tr>
                    <tr>
                        <th>Afiliación Salud</th>
                        <td><?php echo $valueAct['afiliacion'] ?></td>           
                        <th>cohabitacion</th>
                        <td><?php echo $valueAct['cohabitacion'] ?></td>                       
                        <th>Cantidad Menores </th>
                        <td><?php echo $valueAct['cantMenores'] ?></td> 
                    </tr>
                    <tr>
                        <th>Hijos</th>
                        <td><?php echo $valueAct['cantHijos'] ?></td>
                        <th>mujer Cabeza de Hogar</th>
                        <td><?php echo $valueAct['mujerCabHogar'] ?></td>                         
                        <th>Conyugue</th>
                        <td><?php echo $valueAct['conyugueHogar'] ?></td>    
                    </tr>
                    <tr>
                        <th>Cant Adulto Mayor</th>
                        <td><?php echo $valueAct['cantadultoMayor'] ?></td>           
                        <th>Cant Condicion Especial</th>
                        <td><?php echo $valueAct['cantCondEspecial'] ?></td>                        
                        <th>Condicion Etnica</th>
                        <td><?php echo $valueAct['condicionEtnica'] ?></td>
                    </tr>
                    <tr>
                        <th>Adolecentes</th>
                        <td><?php echo $valueAct['adolecentes'] ?></td>                       
                        <th>hombre Cabeza de Hogar</th>
                        <td><?php echo $valueAct['hombreCabHogar'] ?></td>
                        <th>Grupo Lgtbi</th>
                        <td><?php echo $valueAct['grupoLgtbi'] ?></td>  
                    </tr>
                    <tr>
                        <th>bolIntegracionSocial</th>
                        <td><?php echo $valueAct['bolIntegracionSocial'] ?></td>
                        <th>bolSecEducacion</th>
                        <td><?php echo $valueAct['bolSecEducacion'] ?></td>
                        <th>bolSecMujer</th>
                        <td><?php echo $valueAct['bolSecMujer'] ?></td>
                    </tr>
                    <tr>
                        <th>bolSecSalud</th>
                        <td><?php echo $valueAct['bolSecSalud'] ?></td>
                    </tr>
                    <tr>
                        <th>bolAltaCon</th>
                        <td><?php echo $valueAct['bolAltaCon'] ?></td>
                        <th>bolAltaCon</th>
                        <td><?php echo $valueAct['bolAltaCon'] ?></td>                       
                        <th>Dormitorios</th>
                        <td><?php echo $valueAct['dormitorios'] ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </table>

        <table id="example" class="table table-striped table-bordered" cellspacing="0">
            <tr>
                <th colspan="8" style="text-align: center">Datos Hogar Encuestas</th>
            </tr>

            <?php
            if ($arrayDatosEncuentas) {
                $calcEducacion = ($arrayDatosEncuentas['variables']['aprobados'] / ($arrayDatosEncuentas['variables']['cantMayor']));
                $educacion = 0;
                if ($calcEducacion < 9) {
                    $educacion = 1;
                } else if ($arrayDatosEncuentas['variables']['cantMayor'] == 0) {
                    $educacion = 1;
                } else {
                    $educacion = 0;
                }
                $totalEnccalBle = ($claCalificacion->BLE * ($educacion * 100));
                $saludSubsidiados = 0;
                $saludSubsidiados = ($arrayDatosEncuentas['variables']['afiliacion'] / $arrayDatosEncuentas['variables']['cant']);
                $totalEncRSA = $claCalificacion->RSA * ($saludSubsidiados * 100);

                $cohabitacion = 0;
                if ($arrayDatosEncuentas['variables']['cohabitacion'] > 1) {
                    $cohabitacion = 1;
                }
                $totalEncCohabitacion = $claCalificacion->COH * ($cohabitacion * 100);

                $dormitorios = $arrayDatosEncuentas['variables']['dormitorios'];
                $hacinamiento = 0;
                $calchacinamiento = 0;
                if ($dormitorios != 0) {
                    $calchacinamiento = ($arrayDatosEncuentas['variables']['cant'] / $dormitorios);
                    if ($calchacinamiento >= 4) {
                        $hacinamiento = 1;
                    } else {
                        $hacinamiento = 0;
                    }
                } else
                    $hacinamiento = 1;

                $totalEncHacinamiento = $claCalificacion->HACN * ($hacinamiento * 100);

                $ingresos = $arrayDatosEncuentas['variables']['ingresos'] / $arrayDatosEncuentas['variables']['cant'];
                $arrConfiguracion['constantes']['salarioMinimo'] . "/" . ($ingresos + 1000);
                $totalEncIngresos = ($arrConfiguracion['constantes']['salarioMinimo']) / ($ingresos + 1000);
                $totalEncIPC = ($claCalificacion->IPC * (100 * (1 - exp(-$totalEncIngresos / 52.05))));

                $dependenciaEcon = 0;
                $adultos = 0;
                if ($arrayDatosEncuentas['variables']['adultos'] > 0) {
                    $adultos = $arrayDatosEncuentas['variables']['cant'] / $arrayDatosEncuentas['variables']['adultos'];
                } else {
                    $adultos = 3.5;
                }
                //echo "<br>".$value['aprobadosJefe']; 
                if ($adultos > 3 && $arrayDatosEncuentas['variables']['aprobadosJefe'] <= 2) {
                    $dependenciaEcon = 1;
                }
                $totalEncTDE = ($claCalificacion->TDE * ($dependenciaEcon * 100));

                $menores = $arrayDatosEncuentas['variables']['cantMenores'] / $arrayDatosEncuentas['variables']['cant'];
                $totalEncHN12 = ($claCalificacion->HN12 * ($menores * 100));
                $monoparentalFem = 0;

                $tipo = 0;
                if ($arrayDatosEncuentas['variables']['mujerCabHogar'] == 1) {
                    $tipo = 1;
                }
                if ($arrayDatosEncuentas['variables']['mujerCabHogar'] == 1 && $arrayDatosEncuentas['variables']['conyugueHogar'] == 0 && $arrayDatosEncuentas['variables']['cantHijos'] > 0) {
                    $monoparentalFem = 1;
                }
                $totalEncMCF = ($claCalificacion->MCF * ($monoparentalFem * 100));

                $cantAdultoMayor = $arrayDatosEncuentas['variables']['cantadultoMayor'] / $arrayDatosEncuentas['variables']['cant'];
                $totalEncHAMY = $claCalificacion->HAMY * ($cantAdultoMayor * 100);

                $discapacidad = $arrayDatosEncuentas['variables']['cantCondEspecial'] / $arrayDatosEncuentas['variables']['cant'];
                $totalEncCDISC = ($claCalificacion->CDISC * ($discapacidad * 100));

                $grupoEtnico = 0;
                if ($arrayDatosEncuentas['variables']['condicionEtnica'] > 0) {
                    $grupoEtnico = 1;
                }
                $totalEncHPGE = ($claCalificacion->HPGE * ($grupoEtnico * 100));

                $cantAdolecentes = $arrayDatosEncuentas['variables']['adolecentes'] / $arrayDatosEncuentas['variables']['cant'];

                $totalEncHN18 = ($claCalificacion->HN18 * ($cantAdolecentes * 100));

                $monoparentalMasc = 0;
                $tipo = 0;
                if ($arrayDatosEncuentas['variables']['hombreCabHogar'] == 1) {
                    $tipo = 2;
                }
                if ($arrayDatosEncuentas['variables']['hombreCabHogar'] == 1 && $arrayDatosEncuentas['variables']['conyugueHogar'] == 0 && $arrayDatosEncuentas['variables']['cantHijos'] > 0) {
                    $monoparentalMasc = 1;
                }
                $totalEncHCF = ($claCalificacion->HCF * ($monoparentalMasc * 100));


                $grupoLGTBI = 0;
                if ($arrayDatosEncuentas['variables']['grupoLgtbi'] > 0) {
                    $grupoLGTBI = 1;
                }
                $totalEncPLGTBI = ($claCalificacion->PLGTBI * ($grupoLGTBI * 100));

                $programa = 0;

                if ($arrayDatosEncuentas['variables']['bolIntegracionSocial'] > 0 || $arrayDatosEncuentas['variables']['bolSecMujer'] > 0 || $arrayDatosEncuentas['variables']['bolIpes'] > 0) {
                    $programa = 1;
                }
                if ($arrayDatosEncuentas['variables']['bolSecMujer'] == "") {
                    $arrayDatosEncuentas['variables']['bolSecMujer'] = 0;
                }

                $totalEncPPGD = ($claCalificacion->PPGD * ($programa * 100));

                $bolRFPB = 0;
                if ($arrayDatosEncuentas['bolReconocimientoFP'] > 0) {
                    $bolRFPB = 1;
                }
                $totalEncRFPB = ($claCalificacion->RFPB * ($bolRFPB * 100));
                ?>
               
                <tr>
                    <th>Miembros de Hogar</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['cant'] ?></td>
                    <th>Ingresos</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['ingresos'] ?></td>
                    <th>Miembros Mayores (Resago)</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['cantMayor'] ?></td>  
                    <th>Miembros Adultos entre 15 y 60 años </th>
                    <td><?php echo $arrayDatosEncuentas['variables']['adultos'] ?></td> 
                </tr>
                <tr>

                    <th>Años Aprobados Jefe </th>
                    <td><?php echo $arrayDatosEncuentas['variables']['aprobadosJefe'] ?></td>           
                    <th>Años Aprobados Miembros </th>
                    <td><?php echo $arrayDatosEncuentas['variables']['aprobados'] ?></td>
                    <th>Afiliación Salud</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['afiliacion'] ?></td>           
                    <th>cohabitacion</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['cohabitacion'] ?></td> 
                </tr>
                <tr>
                    <th>Cantidad Menores </th>
                    <td><?php echo $arrayDatosEncuentas['variables']['cantMenores'] ?></td>
                    <th>Hijos</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['cantHijos'] ?></td>
                    <th>mujer Cabeza de Hogar</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['mujerCabHogar'] ?></td>            
                    <th>Conyugue</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['conyugueHogar'] ?></td>
                </tr>
                <tr>
                    <th>Cant Adulto Mayor</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['cantadultoMayor'] ?></td>           
                    <th>Cant Condicion Especial</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['cantCondEspecial'] ?></td>
                    <th>Condicion Etnica</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['condicionEtnica'] ?></td> 
                    <th>Adolecentes</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['adolecentes'] ?></td>
                </tr>
                <tr>                       
                    <th>hombre Cabeza de Hogar</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['hombreCabHogar'] ?></td>
                    <th>Grupo Lgtbi</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['grupoLgtbi'] ?></td>
                    <th>bolIntegracionSocial</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['bolIntegracionSocial'] ?></td>
                    <th>bolSecEducacion</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['bolSecEducacion'] ?></td>
                </tr>
                <tr>                       
                    <th>bolSecMujer</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['bolSecMujer'] ?></td>
                    <th>bolSecSalud</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['bolSecSalud'] ?></td>
                    <th>bolAltaCon</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['bolAltaCon'] ?></td>
                    <th>bolAltaCon</th>
                    <td><?php echo $arrayDatosEncuentas['variables']['bolAltaCon'] ?></td>
                </tr>

            <?php } ?>
        </table>
    </div>

    <div class="col-lg-6 col-md-6" >

        <table class="table table-striped table-bordered" >

            <tr>
                <th style="text-align: center">Indicador</th>
            </tr>
            <?php foreach ($arrayIndicadores as $key => $value) { ?>
                <tr>
                    <td><?php echo $value['txtIndicador'] ?></td>                 
                </tr>
            <?php } ?>
            <tr>
                <th>Tota General</th> 
            </tr>
        </table>
    </div>
    <div class="col-lg-6 col-md-6" >
        <table class="table table-striped table-bordered" style="width: 100%; text-align: left">
            <tr>
                <th style="text-align: center">Total Calificación</th>
                <th style="text-align: center">Total SIPIVE Hoy</th>
                <th style="text-align: center">Total Encuesta</th>
            </tr>
            <?php foreach ($arrayIndicadores as $key => $value) { ?>
                <tr>
                    <!--<td><?php echo $value['txtIndicador'] ?></td>-->
                    <?php if ($arrayCalificacion) { ?>
                        <?php foreach ($arrayCalificacion as $keyCal => $valueCal) { ?>
                            <?php if ($valueCal['seqIndicador'] == $value['seqIndicador']) { ?>
                                                                                                                                                                                                                            <!--<td style="text-align: center"><?php echo $valueCal['cantidadMiembros'] ?></td>   -->                                       
                                <td style="text-align: center"><?php echo str_replace(".", ",", $valueCal['total']) ?></td>

                            <?php } ?>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 1) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalcalBle) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEnccalBle) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 2) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalRSA) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncRSA) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 3) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalCohabitacion) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncCohabitacion) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 4) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalHacinamiento) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncHacinamiento) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 5) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalIPC) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncIPC) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 6) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalTDE) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncTDE) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 7) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalHN12) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncHN12) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 8) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalMCF) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncMCF) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 9) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalHAMY) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncHAMY) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 10) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalCDISC) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncCDISC) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 11) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalHPGE) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncHPGE) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 12) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalHN18) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncHN18) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 13) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalHCF) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncHCF) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 14) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalPLGTBI) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncPLGTBI) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 15) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalPPGD) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncPPGD) ?></td>
                        <?php } ?>
                        <?php if ($value['seqIndicador'] == 16) { ?>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalRFPB) ?></td>
                            <td style="text-align: center"><?php echo str_replace(".", ",", $totalEncRFPB) ?>pp</td>
                        <?php } ?>
                    <?php } ?>
                </tr>   
                <?php
                $cont++;
            }
            ?>
            <?php
            $total1 = $totalcalBle + $totalRSA + $totalCohabitacion + $totalHacinamiento + $totalIPC + $totalTDE + $totalHN12 + $totalMCF + $totalHAMY + $totalCDISC + $totalHPGE + $totalHN18 + $totalHCF + $totalPLGTBI + $totalPPGD;
            $total2 = $totalEnccalBle + $totalEncRSA + $totalEncCohabitacion + $totalEncHacinamiento + $totalEncIPC + $totalEncTDE + $totalEncHN12 + $totalEncMCF + $totalEncHAMY + $totalEncCDISC + $totalEncHPGE + $totalEncHN18 + $totalEncHCF + $totalEncPLGTBI + $totalEncPPGD;
            ?>
            <tr style="text-align: center">          
                <th><?php echo str_replace(".", ",", $sumTotalCalificacion) ?></th>
                <th><?php echo str_replace(".", ",", $total1) ?></th>
                <th><?php echo str_replace(".", ",", $total2) ?></th>
            </tr>
            <?php
        }
        ?>
    </table>
</div>





