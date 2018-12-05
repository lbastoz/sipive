<?php

$txtPrefijoRuta = "../../";
include( $txtPrefijoRuta . "recursos/archivos/verificarSesion.php" );
include( $txtPrefijoRuta . "recursos/archivos/lecturaConfiguracion.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['funciones'] . "funciones.php" );
include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/inclusionSmarty.php" );
include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/coneccionBaseDatos.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "ArchivoMCY.class.php" );

$arrErrores = array();

$claArchivoMcy = new ArchivoMCY();
$claArchivoMcy->editar($_POST['datos']);

$arrLineas = $claArchivoMcy->cargarLineas($_POST['seqTipoDocumento'],$_POST['numDocumento']);

$claSmarty->assign("seqTipoDocumento",$_POST['seqTipoDocumento']);
$claSmarty->assign("numDocumento",$_POST['numDocumento']);
$claSmarty->assign("arrLineas",$arrLineas);
$claSmarty->assign("arrErrores",$claArchivoMcy->arrErrores);
$claSmarty->assign("arrMensajes",$claArchivoMcy->arrMensajes);
$claSmarty->display("archivoMcy/ver.tpl");

?>