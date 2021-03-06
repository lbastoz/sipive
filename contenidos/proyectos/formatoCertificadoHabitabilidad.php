<?php

$txtPrefijoRuta = "../../";
include( $txtPrefijoRuta . "recursos/archivos/verificarSesion.php" );
include( $txtPrefijoRuta . "recursos/archivos/lecturaConfiguracion.php" );
include( $txtPrefijoRuta . $arrConfiguracion["librerias"]["funciones"] . "funciones.php" );
include( $txtPrefijoRuta . $arrConfiguracion["carpetas"]["recursos"] . "archivos/inclusionSmarty.php" );
include( $txtPrefijoRuta . $arrConfiguracion["carpetas"]["recursos"] . "archivos/coneccionBaseDatos.php" );

$objTecnico = new stdClass();

$sql = "select * from t_pry_tecnico where seqUnidadProyecto = " . intval($_GET['seqUnidadProyecto']);
$objRes = $aptBd->execute($sql);
while ($objRes->fields) {
    foreach ($objRes->fields as $txtTitulo => $txtValor) {
        $objTecnico->$txtTitulo = $txtValor;
    }
    $objRes->MoveNext();
}

$idHijo = 0;
$sql = "
    select 
      if(pry.seqProyecto is null, con.seqProyecto, pry.seqProyecto) as seqProyecto, 
      if(pry.seqProyecto is null, con.txtNombreProyecto, pry.txtNombreProyecto) as txtProyecto, 
      if(pry.seqProyecto is null, null, con.seqProyecto) as txtConjunto, 
      if(pry.seqProyecto is null, null, con.txtNombreProyecto) as txtConjunto, 
      if(pry.seqProyecto is null, null, con.seqProyecto) as seqProyectoHijo, 
      upr.txtNombreUnidad,
      con.txtDireccion,
      if(locc.seqLocalidad is null,locp.txtLocalidad,locc.txtLocalidad) as txtLocalidad,
      if(barc.seqBarrio is null,barp.txtBarrio,barc.txtBarrio) as txtBarrio,
       pry.seqProyecto AS idProyecto
    from t_pry_unidad_proyecto upr
    inner join t_pry_proyecto con on upr.seqProyecto = con.seqProyecto
    left join t_pry_proyecto pry on con.seqProyectoPadre = pry.seqProyecto
    left join t_frm_localidad locc on con.seqLocalidad = locc.seqLocalidad
    left join t_frm_localidad locp on pry.seqLocalidad = locp.seqLocalidad
    left join t_frm_barrio barc on con.seqBarrio = barc.seqBarrio
    left join t_frm_barrio barp on pry.seqBarrio = barp.seqBarrio
    where upr.seqUnidadProyecto = " . intval($_GET['seqUnidadProyecto']) . "
";
$objRes = $aptBd->execute($sql);

while ($objRes->fields) {
    foreach ($objRes->fields as $txtTitulo => $txtValor) {
        //$idHijo = ($txtTitulo == 'seqProyectoHijo') ? $txtValor : 0;
        if ($txtTitulo != 'seqProyectoHijo') {
            $objTecnico->$txtTitulo = mb_strtoupper($txtValor);
        } else {
            $idHijo = $txtValor;
        }
    }
    $objRes->MoveNext();
}

$idHijo = ($idHijo > 0) ? $idHijo : $objTecnico->seqProyecto;
$sql = "
    select 
        seqProyecto,
        seqTipoLicencia,
        txtLicencia,
        txtExpideLicencia,
        fchLicencia,
        fchEjecutoriaLicencia
    from t_pry_proyecto_licencias lic
    where lic.seqProyecto = " . $idHijo . "    
";
$objRes = $aptBd->execute($sql);
while ($objRes->fields) {
    if ($objRes->fields['seqTipoLicencia'] == 2) {
        $objTecnico->txtLicenciaConstruccion = $objRes->fields['txtLicencia'];
        $objTecnico->fchLicencia = $objRes->fields['fchLicencia'];
       
        $objTecnico->fchEjecutoriaLicencia = $objRes->fields['fchEjecutoriaLicencia'];
        if($objTecnico->fchEjecutoriaLicencia == "0000-00-00" or $objTecnico->fchEjecutoriaLicencia ==""){
            $objTecnico->fchEjecutoriaLicencia = $objRes->fields['fchLicencia'];
        }
    } elseif ($objRes->fields['seqTipoLicencia'] == 1) {
        $objTecnico->txtExpideLicenciaUrbanismo = $objRes->fields['txtExpideLicencia'];
    }
    $objRes->MoveNext();
}

$sql = "select * from t_pry_adjuntos_tecnicos where seqTecnicoUnidad = " . intval($objTecnico->seqTecnicoUnidad);
$objRes = $aptBd->execute($sql);
while ($objRes->fields) {
    switch ($objRes->fields['seqAdjuntoTecnicoProyecto']) {
        case 2:
            $numContador = count($objTecnico->observaciones);
            $objTecnico->observaciones[$numContador] = $objRes->fields['txtNombreAdjunto'];
            break;
        default: // Imagenes
            $numContador = count($objTecnico->imagenes);
            $objTecnico->imagenes[$numContador]['nombre'] = $objRes->fields['txtNombreAdjunto'];
            $objTecnico->imagenes[$numContador]['ruta'] = $objRes->fields['txtNombreArchivo'];
            if (!file_exists($txtPrefijoRuta . "recursos/imagenes/desembolsos/" . $objRes->fields['txtNombreArchivo'])) {
                $objTecnico->imagenes[$numContador]['nombre'] = "No Disponible";
                $objTecnico->imagenes[$numContador]['ruta'] = "no_disponible.jpg";
            }
            break;
    }
    $objRes->MoveNext();
}

$txtFecha = ucwords(strftime("%A %#d de %B del %Y")) . " " . date("H:i:s");
$txtFechaVisita = ucwords(strftime("%A %#d de %B del %Y", strtotime($objTecnico->fchVisita)));
$txtFechaExpedicion = ucwords(strftime("%A %#d de %B del %Y", strtotime($objTecnico->fchExpedicion)));
$numDiaActual = date("d");
$txtMesActual = ucwords(strftime("%B"));
$numAnoActual = date("Y");

$claSmarty->assign("objTecnico", $objTecnico);
$claSmarty->assign("txtFuente12", "font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 12px;");
$claSmarty->assign("txtFuente10", "font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px;");
$claSmarty->assign("txtFecha", $txtFecha);
$claSmarty->assign("numDiaActual", $numDiaActual);
$claSmarty->assign("txtMesActual", $txtMesActual);
$claSmarty->assign("numAnoActual", $numAnoActual);
$claSmarty->assign("txtMatriculaProfesional", obtenerMatriculaProfesional());
$claSmarty->assign("txtUsuarioSesion", $_SESSION['txtNombre'] . " " . $_SESSION['txtApellido']);
$claSmarty->assign("txtFechaVisita", $txtFechaVisita);
$claSmarty->assign("txtFechaExpedicion", $txtFechaExpedicion);
$claSmarty->display("proyectos/formatoCertificadoHabitabilidad.tpl");
?>