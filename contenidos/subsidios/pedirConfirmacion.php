<?php

/**
 * PIDE CONFIRMACION DE LOS CAMBIOS GENERADOS EN EL FORMULARIO
 * SI NO HAY CAMBIOS EN EL FORMULARIO SOLO GRABA EL SEGUIMIENTO
 */
$txtPrefijoRuta = "../../";

include( $txtPrefijoRuta . "recursos/archivos/verificarSesion.php" );
include( $txtPrefijoRuta . "recursos/archivos/lecturaConfiguracion.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['funciones'] . "funciones.php" );
include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/inclusionSmarty.php" );
include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/coneccionBaseDatos.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "Ciudadano.class.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "FormularioSubsidios.class.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "Seguimiento.class.php" );

$bolConfirmacion = false;
$arrErrores = array();
$arrMensajes = array();
$arrCamposLibres = array();
$arrCamposCalificacion = array();
$eliminados = false;
$adicionados = false;

// Campos que se pueden cambiar sin privilegios
$arrCamposLibres[] = "txtDireccion";
$arrCamposLibres[] = "numTelefono1";
$arrCamposLibres[] = "numTelefono2";
$arrCamposLibres[] = "numCelular";
$arrCamposLibres[] = "seqCiudad";
$arrCamposLibres[] = "seqLocalidad";
$arrCamposLibres[] = "seqBarrio";
$arrCamposLibres[] = "txtCorreo";
$arrCamposLibres[] = "seqUpz";

$arrCamposCalificacion[] = "valIngresoHogar";
$arrCamposCalificacion[] = "seqEtnia";
$arrCamposCalificacion[] = "seqParentesco";
$arrCamposCalificacion[] = "seqCondicionEspecial";
$arrCamposCalificacion[] = "seqCondicionEspecial2";
$arrCamposCalificacion[] = "seqCondicionEspecial3";
$arrCamposCalificacion[] = "fchNacimiento";
$arrCamposCalificacion[] = "seqNivelEducativo";
$arrCamposCalificacion[] = "estadoCivil";
$arrCamposCalificacion[] = "anosAprobados";
$arrCamposCalificacion[] = "seqSalud";
$arrCamposCalificacion[] = "objCiudadano";
$arrCamposCalificacion[] = "bolIntegracionSocial";
$arrCamposCalificacion[] = "bolSecMujer";
$arrCamposCalificacion[] = "bolIpes";
$arrCamposCalificacion[] = "numHabitaciones";
$arrCamposCalificacion[] = "numHacinamiento";
$arrCamposCalificacion[] = "fechaNac";
$arrCamposCalificacion[] = "seqGrupoLgtbi";
$arrCamposCalificacion[] = "tipoDocumento";
$arrCamposCalificacion[] = "seqTipoVictima";



//$arrCamposCalificacion[] = "seqEstadoProceso";

/* * **********************************************************************************************************
 * VERIFICACION DE CAMBIOS AL FORMULARIO DE INSCRIPCION
 * ********************************************************************************************************** */

if (trim($_POST['txtArchivo']) == "./contenidos/subsidios/salvarInscripcion.php") {

    $estadoActual = $_POST['seqEstadoProceso'];
    if ($estadoActual == 12 || $estadoActual == 35) {
        // Cambios en los datos del ciudadano
        foreach ($claFormulario->arrCiudadano as $seqCiudadano => $objCiudadano) {
            foreach ($objCiudadano as $txtClave => $txtValor) {
                if ($objCiudadano->seqParentesco == 1) {
                    if (isset($_POST[$txtClave])) {
                        if ($objCiudadano->$txtClave != $_POST[$txtClave]) {
                            $arrCamposCambiados[] = $txtClave;
                            $bolCambiaDatos = true;
                        }
                    }
                }
            }
        }

        $_POST['valArriendo'] = mb_ereg_replace("[^0-9]", "", $_POST['valArriendo']);
        $_POST['valIngresoHogar'] = mb_ereg_replace("[^0-9]", "", $_POST['valIngresoHogar']);

        // Cambios en los datos del formulario
        unset($claFormulario->arrCiudadano);
        $arraynombrecampos = array();
        foreach ($claFormulario as $txtClave => $txtValor) {
            if (isset($_POST[$txtClave])) {
                if ($txtValor != $_POST[$txtClave]) {
                    $arrCamposCambiados[] = $txtClave;
                    $arraynombrecampos[] = $txtClave;
                    $bolCambiaDatos = true;
                }
            }
        }

        // si los cambios son solo sobre los campos libres
        // se considera que no hay cambios y se realiza la modificacion
        $bolCambiosLibres = true;

        foreach ($arrCamposCambiados as $txtCampoCambiado) {
            if (!in_array($txtCampoCambiado, $arrCamposLibres)) {
                $bolCambiosLibres = false;
            }
        }

        // si los campos son solo sobre los campos libres UNICAMENTE
        // se considera que no hay cambios y se realiza la modificacion
        if ($bolCambiosLibres and ! $bolCambiaDatosMiembroHogar) {
            $bolCambiaDatos = false;
        }

        // si no hay cambios no muestra la ventana de confirmacion
        if ($bolCambiaDatos) {
            $txtMensaje = "Es necesario que confirme la accion que esta apunto de realizar:";
            $txtMensaje .= "<div class='msgError' style='font-size:12px;'>¿Desea modificar la inscripción para el documento " . $_POST['numDocumento'] . "?</div>";
            $numDocumentoHogar = $_POST['numDocumento'];
        } else {
            $bolMostrarConfirmacion = false;
            $numDocumentoAtendido = $objCiudadano->numDocumento;
            $txtCiudadanoAtendido = $objCiudadano->txtNombre1 . " " . $objCiudadano->txtNombre2 . " " .
                    $objCiudadano->txtApellido1 . " " . $objCiudadano->txtApellido2;
        }
        $bolConfirmacion = true;
    } else {
        $bolConfirmacion = true;
    }
}

/* * **********************************************************************************************************
 * VERIFICACION DE CAMBIOS AL FORMULARIO DE ACTUALIZACION
 * ********************************************************************************************************** */

if (trim($_POST['txtArchivo']) == "./contenidos/subsidios/salvarActualizacion.php") {

    // Carga los datos
    $claFormulario = new FormularioSubsidios();
    $claFormulario->cargarFormulario($_POST['seqFormulario']);

    // Verifica cambios en los ciudadanos que estan en el post y en la clase
    // ademas verifica si un ciudadano ha sido eliminado del formulario
    $arrCedulasFormulario = array();
    foreach ($claFormulario->arrCiudadano as $seqCiudadano => $objCiudadano) {
        $numDocumento = $objCiudadano->numDocumento;
        $arrCedulasFormulario[] = $numDocumento;
        if (isset($_POST['hogar'][$numDocumento])) {
            foreach ($objCiudadano as $txtClave => $txtValor) {
                if (isset($_POST['hogar'][$numDocumento][$txtClave])) {
                    if (trim($txtValor) != trim($_POST['hogar'][$numDocumento][$txtClave])) {
                        $arrCamposCambiados[] = $txtClave;
                        $bolConfirmacion = true;
                        //echo $txtClave . " ==> POST " . $_POST['hogar'][$numDocumento][$txtClave] . " ==> CLASE " .  $txtValor . "<br>";
                    }
                }
            }
        } else {
            $arrCamposCambiados[] = "objCiudadano";
            $bolConfirmacion = true;
            $eliminados = true;
            //echo "Ciudadano eliminado <br>";
        }
    }

    // Verifica si hay ciudadano agregados en el formulario
    foreach ($_POST['hogar'] as $numDocumento => $arrMiembro) {
        if (!in_array($numDocumento, $arrCedulasFormulario)) {
            $bolConfirmacion = true;
            $adicionados = true;
            //echo "Ciudadano adicionado<br>";
        }
    }

    // Verifica si hay cambios en los datos del formulario
    foreach ($claFormulario as $txtClave => $txtValor) {
        if ($txtClave != "arrCiudadano") {
            if (!in_array($txtClave, $arrCamposLibres)) {
                if (isset($_POST[$txtClave])) {
                    $txtValor = ( $txtValor === "0000-00-00" ) ? "" : $txtValor;
                    $txtValor = ( $txtValor === null ) ? 0 : $txtValor;
                    if (substr($txtClave, 0, 3) == "val") {
                        $valFormateado = mb_ereg_replace("[^0-9]", "", $_POST[$txtClave]);
                    } else {
                        $valFormateado = $_POST[$txtClave];
                    }
                    if ($txtValor != $valFormateado) {
                        if ($_SESSION['seqUsuario'] == 251) {
                            echo htmlentities($objCiudadano->txtApellido1);
                        }
                        if ($txtClave == 'seqEstadoProceso' || $txtClave == 'valAspiraSubsidio' || $txtClave == 'seqBancoCuentaAhorro2') {
                            $a = 0;
                        } else {
                            $arrCamposCambiados[] = $txtClave;
                            $bolConfirmacion = true;
                            //echo $txtClave . ' ==> txtValor: ' . $txtValor . ' ==> ' . $valFormateado . '<br>';
                        }
                    }
                }
            }
        }
    }
}

/* * **********************************************************************************************************
 * MOSTRAR EL CUADRO DE CONFIRMACION DE DATOS
 * ********************************************************************************************************** */

//var_dump( $bolConfirmacion ); die();

if ($bolConfirmacion == true) {
    if ($_POST["bolSancion"] == 1) {
        $arrErrores[] = "No se puede modificar la postulacion del hogar debido a que esta Sancionado.";
        imprimirMensajes($arrErrores, array());
    } else {

        if ($claFormulario->seqEstadoProceso == 35 and $claFormulario->seqPlanGobierno == 3) {
            $_POST['seqEstadoProceso'] = 37;
        } else {
            // revisa si los cambios son en campos que afectan la calificacion
            // de ser asi, se altera el estado del proceso para que quede en 
            // 37. Inscripcion - Hogar Actualizado
            if ($eliminados == true || $adicionados == true) {
                $_POST['seqEstadoProceso'] = 37;
            }
            if (count($arrCamposCambiados) > 0) {
                foreach ($arrCamposCambiados as $txtCampo) {
                    if (in_array($txtCampo, $arrCamposCalificacion)) {
                        $bolRetorno = true;
                        $_POST['seqEstadoProceso'] = 37;
                        break;
                    }
                }
            }
        }

        if ($_POST['seqEstadoProceso'] == 37) {
            $_POST['bolCerrado'] = 0;
            $_POST['fchPostulacion'] = "";
        }

        $txtMensaje = "Es necesario que confirme la accion que esta apunto de realizar:";
        $txtMensaje .= "<div class='msgOk' style='font-size:12px;'>";
        $txtMensaje .= "¿Desea salvar los cambios realizados para el documento " . number_format($_POST['numDocumento']) . "?";
        $txtMensaje .= "</div>";

        $claSmarty->assign("txtMensaje", $txtMensaje);
        $claSmarty->assign("bolConfirmacion", $bolConfirmacion);
        $claSmarty->assign("txtArchivo", $_POST['txtArchivo']);
        $claSmarty->assign("arrPost", $_POST);

        $claSmarty->display("subsidios/pedirConfirmacion.tpl");
    }
} else {
  
    if (trim($_POST['txtComentario']) == "") {
        $arrErrores[] = "Debe digitar el campo de comentarios";
    }

    if (intval($_POST['seqGrupoGestion']) == 0) {
        $arrErrores[] = "Seleccione el grupo de gestion";
    }

    if (intval($_POST['seqGestion']) == 0) {
        $arrErrores[] = "Seleccione la gestion realizada";
    }

    if (empty($arrErrores)) {

        // modificacion del formulario de los campos libres
        $sql = "UPDATE T_FRM_FORMULARIO SET ";
        foreach ($arrCamposLibres as $txtCampo) {
            $sql .= $txtCampo . " = '" . $_POST[$txtCampo] . "',";
        }
        $sql = trim($sql, ",");
        $sql .= " WHERE seqFormulario = " . $_POST['seqFormulario'];
        $aptBd->execute($sql);

        // una vez hechos los cambios se carga el formulario para compararlo con el de antes
        $claFormularioNuevo = new FormularioSubsidios();
        $claFormularioNuevo->cargarFormulario($_POST['seqFormulario']);

        // Ciudadano Atendido
        foreach ($claFormulario->arrCiudadano as $seqCiudadano => $objCiudadano) {
            if ($objCiudadano->numDocumento == $_POST['numDocumento']) {
                $txtNombre = trim($objCiudadano->txtNombre1) . " ";
                $txtNombre .= ( trim($objCiudadano->txtNombre2) != "" ) ? trim($objCiudadano->txtNombre2) . " " : "";
                $txtNombre .= trim($objCiudadano->txtApellido1) . " ";
                $txtNombre .= trim($objCiudadano->txtApellido2);
            }
        }

        // obtiene los cambios para dejar el registro
        $claSeguimiento = new Seguimiento;
        $txtCambios = $claSeguimiento->cambiosPostulacion($_POST);

        $sql = "
                INSERT INTO T_SEG_SEGUIMIENTO ( 
                       seqFormulario, 
                       fchMovimiento, 
                       seqUsuario, 
                       txtComentario, 
                       txtCambios, 
                       numDocumento, 
                       txtNombre, 
                       seqGestion
                ) VALUES (
                       " . $_POST['seqFormulario'] . ",
                       now(),
                       " . $_SESSION['seqUsuario'] . ",
                       \"" . mb_ereg_replace("\n", "", $_POST['txtComentario']) . "\",
                       \"$txtCambios\",
                       " . $_POST['numDocumento'] . ",
                       \"" . $txtNombre . "\",
                       " . $_POST['seqGestion'] . "
                )					
         ";
        try {
            $aptBd->execute($sql);
            $seqSeguimiento = $aptBd->Insert_ID();
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido registrar la actividad del hogar, contacte al administrador";
        }

        if (empty($arrErrores)) {
            $arrMensajes[] = "Ha salvado un registro de actividad, el numero del registro es " .
                    number_format($seqSeguimiento, 0, ".", ",") .
                    ". Conserve este número para su referencia.";
        }

    }
    imprimirMensajes($arrErrores, $arrMensajes);
}
?>
