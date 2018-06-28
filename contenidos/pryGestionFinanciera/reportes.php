<?php

$txtPrefijoRuta = "../../";

include( $txtPrefijoRuta . "recursos/archivos/verificarSesion.php" );
include( $txtPrefijoRuta . "recursos/archivos/lecturaConfiguracion.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['funciones'] . "funciones.php" );
include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/inclusionSmarty.php" );
include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/coneccionBaseDatos.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases']   . "GestionFinancieraProyectos.class.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases']   . "PHPExcel.php" );
include( "../../librerias/phpExcel/Classes/PHPExcel/Writer/Excel2007.php" );
include( "../../librerias/phpExcel/Classes/PHPExcel/IOFactory.php" );

$arrReporte = array();

$arrReporte['general']['funcion'] = "reporteGeneral";
$arrReporte['general']['titulo'] = "Exportable General";
$arrReporte['general']['descripcion'] = "Información general de proyectos en cifras";

$arrReporte['fichaTecnica']['funcion'] = "reporteFichaTecnica";
$arrReporte['fichaTecnica']['titulo'] = "Exportable Ficha Técnica";
$arrReporte['fichaTecnica']['descripcion'] = "Obtiene la información consignada en la ficha técnica de todos los proyectos";

if($_GET['reporte'] != "") {

    $txtClave = $_GET['reporte'];
    $txtReporte = $arrReporte[$txtClave]['funcion'];

    $claGestion = new GestionFinancieraProyectos();
    $claGestion->$txtReporte();

}else{

    $claSmarty->assign("arrReporte", $arrReporte);
    $claSmarty->display( "pryGestionFinanciera/reportes.tpl" );

}

?>