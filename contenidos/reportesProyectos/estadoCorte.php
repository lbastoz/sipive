<?php

	$txtPrefijoRuta = "../../";
	include( $txtPrefijoRuta . "recursos/archivos/verificarSesion.php" );
    include( $txtPrefijoRuta . "recursos/archivos/lecturaConfiguracion.php" );
    include( $txtPrefijoRuta . $arrConfiguracion['librerias']['funciones'] . "funciones.php" );
    include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/inclusionSmarty.php" );
    include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/coneccionBaseDatos.php" );
	include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases']. "ReportesProyectos.class.php" );

	$claReporte = new Reportes; 
	$claReporte->estadoCorte();
		
	$claSmarty->assign( "txtIdFormulario" , "ReporteEstadoCorte" );
	$claSmarty->assign( "arrTablas" , $claReporte->arrTablas );
	$claSmarty->assign( "txtGraficas" , $claReporte->php2js() );	
	$claSmarty->display( "reportes/baseReportes.tpl"  );
	
?>
