<?php

ini_set("max_execution_time", "-1");

$txtPrefijoRuta = "../../";
include( $txtPrefijoRuta . "recursos/archivos/verificarSesion.php" );
include( $txtPrefijoRuta . "recursos/archivos/lecturaConfiguracion.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['funciones'] . "funciones.php" );
include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/inclusionSmarty.php" );
include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/coneccionBaseDatos.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "InscripcionFonvivienda.class.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "Ciudadano.class.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "FormularioSubsidios.class.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "Seguimiento.class.php" );

$claInscripcion = new InscripcionFonvivienda();
$tipo = $_REQUEST['tipo'];

$claInscripcion->inhabilitarCargueProy($_POST['seqCargue'], $_REQUEST['lista'], $tipo);
$claInscripcion->cargar($_POST['seqCargue']);
$arraProy = Array();
$bolProcesar = false;
$arrPryProcess = Array();
foreach ($claInscripcion->arrHogares as $numHogar => $arrHogar) {
    //if($arrHogar['seqEstadoHogar'] == 2){

    if ($arrHogar['seqEstadoHogar'] == 2 || $arrHogar['seqEstadoHogar'] == 3) {
        $bolProcesar = true;
    } 
    if ($arrHogar['seqEstadoHogar'] < 4) {
        $bolProcesarProy = true;
    }
    $arraProy[] = $arrHogar['txtDireccionSolucion'];

    if ($arrHogar['txtEstadoHogar'] == 'No procesar') {
        $arrPryProcess[$arrHogar['txtDireccionSolucion']] = true;
    }
}
$arraProy = array_unique($arraProy);

$claSmarty->assign("bolProcesar", $bolProcesar);
$claSmarty->assign("bolProcesarProy", $bolProcesarProy);
$claSmarty->assign("claInscripcion", $claInscripcion);
$claSmarty->assign("arraProy", $arraProy);
$claSmarty->assign("arrPryProcess", $arrPryProcess);
$claSmarty->display("inscripcionFonvivienda/informacion.tpl");
?>