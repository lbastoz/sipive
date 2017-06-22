<?php

/**
 * SALVAR LA POSUTULACION PARA EL
 * EQUEMA DE CASA EN MANO
 * @author Bernardo Zerda <bzerdar@habitatbogota.gov.co>
 * @version 1.0 Ago 2013
 * @version 2.0 Ago 2017
 */

date_default_timezone_set("America/Bogota");

$txtPrefijoRuta = "../../";

include($txtPrefijoRuta . "recursos/archivos/verificarSesion.php");
include($txtPrefijoRuta . "recursos/archivos/lecturaConfiguracion.php");
include($txtPrefijoRuta . $arrConfiguracion['librerias']['funciones'] . "funciones.php");
include($txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/inclusionSmarty.php");
include($txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/coneccionBaseDatos.php");
include($txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "Ciudadano.class.php");
include($txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "FormularioSubsidios.class.php");
include($txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "Seguimiento.class.php");
include($txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "CasaMano.class.php");

$arrErrores = array(); // Todos los errores van aqui

/**********************************************************************************************************************
 * ARREGLOS PARA LA VERIFICACION DE DATOS ACTIVOS / INACTIVOS
 **********************************************************************************************************************/
$numMayorEdad = strtotime("-18 year", time());
$numTerceraEdad = strtotime("-65 year", time());
$numCondicionEspecialMayor65 = 2;
$bolCambiosCalificacion = false;

// tipos de documento invalidos para un mayor de edad
$arrDocumentos[] = 3; // REGISTRO CIVIL
$arrDocumentos[] = 4; // TARJETA DE IDENTIDAD
$arrDocumentos[] = 6; // NIT
$arrDocumentos[] = 7; // NUIP

// tipos de documento invalidos para un menor de edad
$arrDocumentosMayorEdad[] = 1; // Cedula Ciudadania
$arrDocumentosMayorEdad[] = 2; // Cedula extranjeria
$arrDocumentosMayorEdad[] = 6; // NIT

// Esquemas que deben tener proyectos y unidades seleccionados
$arrProyectoEsquema[] = 1;
$arrProyectoEsquema[] = 2;
$arrProyectoEsquema[] = 4;
$arrProyectoEsquema[] = 9;

// Esquemas que deben tener unidades habitacionales seleccionadas
$arrEsquemaUnidades[] = 1;
$arrEsquemaUnidades[] = 9;

$arrEstadosCiviles = obtenerDatosTabla("t_ciu_estado_civil", array("seqEstadoCivil", "txtEstadoCivil"), "seqEstadoCivil", "bolActivo = 1");
$arrParentescos = obtenerDatosTabla("t_ciu_parentesco", array("seqParentesco", "txtParentesco"), "seqParentesco", "bolActivo = 1");
$txtCondicion = ($_POST['seqPlanGobierno'] != 3)? "seqSisben <> 9" : "bolActivo = 1";
$arrSisben = obtenerDatosTabla("t_frm_sisben", array("seqSisben", "txtSisben"), "seqSisben", $txtCondicion);
$arrModalidad = obtenerDatosTabla("t_frm_modalidad", array("seqModalidad", "txtModalidad"), "seqModalidad", "seqPlanGobierno = " . $_POST['seqPlanGobierno']);

// Variables que si se cambian, debe irse a etapa de inscripcion.
$arrCamposCalificacion["formulario"]["valIngresoHogar"] = "Ingresos del hogar";
$arrCamposCalificacion["ciudadano"]["seqEtnia"] = "Condición Étnica";
$arrCamposCalificacion["ciudadano"]["seqParentesco"] = "Parentesco";
$arrCamposCalificacion["ciudadano"]["seqCondicionEspecial"] = "Condicion Especial";
$arrCamposCalificacion["ciudadano"]["seqCondicionEspecial2"] = "Condicion Especial 2";
$arrCamposCalificacion["ciudadano"]["seqCondicionEspecial3"] = "Condicion Especial 3";
$arrCamposCalificacion["ciudadano"]["fchNacimiento"] = "Fecha de Nacimiento";
$arrCamposCalificacion["ciudadano"]["seqNivelEducativo"] = "Nivel Educativo";

/**********************************************************************************************************************
 * LIMPIEZA DE CARACTERES
 **********************************************************************************************************************/

foreach ($_POST['hogar'] as $txtClave => $txtValor){
    $_POST['hogar'][$txtClave] = regularizarCampo($txtClave,$txtValor);
}
foreach ($_POST as $txtClave => $txtValor){
    if($txtClave != "hogar") {
        $_POST[$txtClave] = regularizarCampo($txtClave, $txtValor);
    }
}

/**********************************************************************************************************************
 * VALIDACIONES DEL FORMULARIO - PESTAÑA DE COMPOSICION FAMILIAR
 **********************************************************************************************************************/

if (!empty($_POST['hogar'])) {

    $numCabezaFamilia = 0;
    $numCondicionJefeHogar = 0;
    $numCedula = 0;
    foreach ($_POST['hogar'] as $numDocumento => $arrCiudadano) {

        // nombre del ciudadano
        $txtNombre  = trim($arrCiudadano['txtNombre1'] ) . " ";
        $txtNombre .= (trim($arrCiudadano['txtNombre2']) != "") ? trim($arrCiudadano['txtNombre2']) . " " : "";
        $txtNombre .= trim($arrCiudadano['txtApellido1'] ) . " " . trim( $arrCiudadano['txtApellido2'] );

        // el primer nombre no puede ser vacio
        if ($arrCiudadano['txtNombre1'] == "") {
            $arrErrores[] = "El ciudadano con numero de documento $numDocumento debe tener primer nombre";
        }

        // el primer apellido no debe estar vacio
        if ($arrCiudadano['txtApellido1'] == "") {
            $arrErrores[] = "El ciudadano con numero de documento $numDocumento debe tener primer apellido";
        }

        // Estado Civil
        if ($arrCiudadano['seqEstadoCivil'] == 0) {
            $arrErrores[] = "El ciudadano $txtNombre debe tener un estado civil.";
        } else{
            $seqEstadoCivil = $arrCiudadano['seqEstadoCivil'];
            if( ! isset( $arrEstadosCiviles[$seqEstadoCivil] ) ) {
                $arrErrores[] = "El ciudadano con numero de documento " . number_format($numDocumento) . " no tiene un estado civil válido";
            }
        }

        // Parentesco
        if ($arrCiudadano['seqParentesco'] == 0) {
            $arrErrores[] = "El ciudadano con numero de documento " . number_format($numDocumento) . " debe tener parentesco";
        } elseif ($arrCiudadano['seqParentesco'] == 1) {
            $numCabezaFamilia++; // si es Jefe de Hogar ( solo debe existir un miembro con parentesco 1 )
            if ($arrCiudadano['seqTipoDocumento'] != 1 and $arrCiudadano['seqTipoDocumento'] != 2) {
                $arrErrores[] = "El tipo de documento seleccionado para el postulante principal no es válido";
            }
        } else {
            $seqParentesco = $arrCiudadano['seqParentesco'];
            if (!isset($arrParentescos[$seqParentesco])) {
                $arrErrores[] = "El ciudadano con numero de documento " . number_format($numDocumento) . " debe tener parentesco válido";
            }
        }

        // solo puede haber una persona con condicion Especial "Madre / Padre Cabeza de Familia"
        if ($arrCiudadano['seqCondicionEspecial'] == 1) {
            $numCondicionJefeHogar++;
        }

        if ($arrCiudadano['seqCondicionEspecial2'] == 1) {
            $numCondicionJefeHogar++;
        }

        if ($arrCiudadano['seqCondicionEspecial3'] == 1) {
            $numCondicionJefeHogar++;
        }

        // por lo menos debe haber una cedula de ciudadania
        if ($arrCiudadano['seqTipoDocumento'] == 1) {
            $numCedula++; // si es cedula de ciudadania ( por lo menos 1 colombiano mayor de edad )
        }

        // Si es mayor de edad compare contra la fecha de postulacion si debe tener cedula
        if ( ! esFechaValida( $arrCiudadano['fchNacimiento'] ) ) {
            $arrErrores[] = "La fecha de Nacimiento del ciudadano " . $txtNombre . " no es valida, verifique los datos";
        } else {

            // fechas para comparar mayor de edad y tercera edad
            $numEdad = strtotime($arrCiudadano['fchNacimiento']);

            // se compara si es mayor de edad al momento de la postulacion
            if (($numEdad <= $numMayorEdad) and in_array($arrCiudadano['seqTipoDocumento'], $arrDocumentos)) {
                $arrErrores[] = "Tipo de documento errado para " . $txtNombre . " porque segun su fecha de nacimiento es mayor de edad";
            }

            // se compara si es menor de 65 aNos y tenga condicion especial "Mayor 65 aNos"
            if (($numEdad > $numTerceraEdad) and ($arrCiudadano["seqCondicionEspecial"] == $numCondicionEspecialMayor65 or
                    $arrCiudadano["seqCondicionEspecial2"] == $numCondicionEspecialMayor65 or
                    $arrCiudadano["seqCondicionEspecial3"] == $numCondicionEspecialMayor65)
            ) {
                $arrErrores[] = "Condicion especial errada para " . $txtNombre . " porque segun su fecha de nacimiento es menor de edad y se le esta asignando la condicion especial de Mayor de 65 Año";
            }

            // se compara si es menor de edad al momento de la postulacion
            if (($numEdad > $numMayorEdad) and in_array($arrCiudadano['seqTipoDocumento'], $arrDocumentosMayorEdad)) {
                $arrErrores[] = "Tipo de documento errado para " . $txtNombre . " porque segun su fecha de nacimiento es menor de edad";
            }

            // se compara si es tercera edad al momento de la postulacion
            if (($numEdad <= $numTerceraEdad) and ($arrCiudadano['seqCondicionEspecial'] != 2 and $arrCiudadano['seqCondicionEspecial2'] != 2 and $arrCiudadano['seqCondicionEspecial3'] != 2)) {
                $arrErrores[] = "Debe tener condicion especial de Mayor de 65 Años para el ciudadano " . $txtNombre;
            }

        } // fin fecha nacimiento valida

    } // fin foreach si hay miembros de hogar

    // errores que se producen dentro del grupo familiar
    switch (true) {
        case $numCabezaFamilia == 0:
            $arrErrores[] = "Debe haber un postulante principal para el hogar";
            break;
        case $numCabezaFamilia > 1:
            $arrErrores[] = "Solo puede tener un postulante principal para este hogar";
            break;
        case $numCondicionJefeHogar > 1:
            $arrErrores[] = "Solo puede haber un miembro de hogar con la condición especial de \"Madre / Padre cabeza de Familia\"";
            break;
        case $numCedula == 0:
            $arrErrores[] = "Debe haber por lo menos un mayor de edad colombiano dentro del nucleo familiar";
            break;
    }

    // si es independiente debe indicar ingresos
    if (intval($_POST['bolDesplazado']) == 0) {
        if (intval($_POST['valIngresoHogar']) == 0) {
            $arrErrores[] = "El ingreso del hogar no puede sumar cero";
        }
    }

} else { // no hay miembros de hogar

    $arrErrores[] = "Debe haber por lo menos una persona dentro del grupo familiar";

} // fin validacion si hay miembros del hogar


/**********************************************************************************************************************
 * VALIDACIONES DEL FORMULARIO - DATOS DEL HOGAR
 **********************************************************************************************************************/

// Vive en arriendo, entonces tiene que tener los datos necesarios
if (intval($_POST['seqVivienda']) == 1) {
    if (intval($_POST['valArriendo']) == 0) {
        $arrErrores[] = "Indique el valor del arrendamiento que esta pagando";
    }
    if (!esFechaValida($_POST['fchArriendoDesde'])) {
        $arrErrores[] = "Indique una fecha v&aacute;lida para la fecha de inicio del pago de arriendo";
    }
    if (trim($_POST['txtComprobanteArriendo']) == "") {
        $arrErrores[] = "Indique si tiene o no comoprobantes de arriendo";
    }
}

// direccion de residencia actual
if (trim($_POST['txtDireccion']) == "") {
    $arrErrores[] = "Indique la dirección donde reside actualmente";
}

// ciudad y validaciones relacionadas
if (intval($_POST['seqCiudad']) == 0) {
    $arrErrores[] = "Indique la ciudad de residencia";
} elseif (intval($_POST['seqCiudad']) == 149) { // vive en bogota
    if (intval($_POST['seqLocalidad']) == 0) {
        $arrErrores[] = "Debe seleccionar una localidad";
    }
    if (intval($_POST['seqBarrio']) == 0) {
        $arrErrores[] = "Debe seleccionar un barrio perteneciente a la localidad";
    }
} else { // fuera de bogota
    if (intval($_POST['seqLocalidad']) == 0) {
        $arrErrores[] = "Debe seleccionar la localidad 'Fuera de Bogota'";
    }
    if (intval($_POST['seqBarrio']) != 1142) {
        $arrErrores[] = "Debe seleccionar el barrio 'Fuera de Bogota'";
    }
}

// Formatos de expresion regular para telefonos fijos y celular
$txtFormatoFijo = "/^[0-9]{7,10}$/";
$txtFormatoCelular = "/^[3]{1}[0-9]{9}$/";

// Telefono Fijo 1
if (is_numeric($_POST['numTelefono1']) == true and intval($_POST['numTelefono1']) != 0) {
    if (!preg_match($txtFormatoFijo, trim($_POST['numTelefono1']))) {
        $arrErrores[] = "El número telefonico fijo 1 debe tener entre 7 y 10 digitos";
    }
}

// Telefono Celular
if (is_numeric($_POST['numCelular']) == true and intval($_POST['numCelular']) != 0) {
    if (!preg_match($txtFormatoCelular, trim($_POST['numCelular']))) {
        $arrErrores[] = "El número telefonico celular debe tener 10 digitos y debe iniciar con el número 3";
    }
}

// Debe haber telefono fijo o numero celular
if (intval($_POST['numCelular']) == 0 and intval($_POST['numTelefono1']) == 0) {
    $arrErrores[] = "Debe registrar un telefono fijo o celular de contacto";
}

// Si hay correo electronico debe ser valido
if (trim($_POST['txtCorreo']) != "") {
    if (!mb_ereg("^[0-9a-zA-Z._\-]+\@[a-zA-Z0-9._\-]+\.([a-zA-z]{2,4})(([\.]{1})([a-zA-Z]{2}))?$", trim($_POST['txtCorreo']))) {
        $arrErrores[] = "No es un correo electrónico válido";
    }
}

// Hogares que viven en la misma vivienda
if (intval($_POST['numHabitacion']) == 0) {
    $arrErrores[] = "Indique el numero de hogares que habitan la vivienda";
}

// Cantidad de dormitorios
if (intval($_POST['numDormitorios']) == 0) {
    $arrErrores[] = "Indique el numero de dormitorios que usa el hogar";
}

// Valor del sisben
$seqSisben = intval($_POST['seqSisben']);
if (!isset($arrSisben[$seqSisben])) {
    $arrErrores[] = "Indique un nivel del sisben válido";
}

/**********************************************************************************************************************
 * VALIDACIONES DEL FORMULARIO - DATOS DE LA POSTULACION
 **********************************************************************************************************************/
if( ! isset( $arrModalidad[ $_POST['seqModalidad'] ] ) ){
    $arrErrores[] = "La modalidad seleccionada no es válida";
}

// esquemas que deben tener proyectos seleccionados
// si no deben tener direccion matricula y chip digitados
if( in_array( $_POST['seqTipoEsquema'] , $arrProyectoEsquema) ){
    if( intval($_POST['seqProyecto']) == 0 or intval($_POST['seqProyecto']) == 37 ){
        $arrErrores[] = "Debe seleccionar un proyecto de la lista";
    }
}else{
    if( trim( $_POST['txtDireccionSolucion'] ) == "" ){
        $arrErrores[] = "Debe digitar la direccion de la solución";
    }
    if( trim( $_POST['txtMatriculaInmobiliaria'] ) == "" ){
        $arrErrores[] = "Debe digitar la matricula inmobiliaria de la solución";
    }
    if( trim( $_POST['txtChip'] ) == "" ){
        $arrErrores[] = "Debe digitar el chip de la solución";
    }
}

if( in_array( $_POST['seqTipoEsquema'] , $arrEsquemaUnidades) ){
    if( intval($_POST['seqUnidadProyecto']) == 0 or intval($_POST['seqUnidadProyecto']) == 1 ){
        $arrErrores[] = "Debe seleccionar una unidad habitacional de la lista";
    }
}

if (trim($_POST['txtDireccionSolucion']) == "") {
    $arrErrores[] = "Indique la dirección de la soluci&oacute;n de vivienda";
}

if (intval($_POST['seqSolucion']) == 1) {
    $arrErrores[] = "Indique el tipo de la solución de vivienda";
}

// se solicita promesa firmada cuando se este colocando en el estado POSTULACION - HOGAR POSTULADO
if( $_POST['seqEstadoProceso'] == 54 ){
    if (intval($_POST['bolPromesaFirmada']) == 0) {
        $arrErrores[] = "No puede continuar si no tiene una promesa de compra-venta firmada";
    }
}

/**********************************************************************************************************************
 * VALIDACIONES DEL FORMULARIO - DATOS FINANCIEROS
 **********************************************************************************************************************/

// Validaciones para el ahorro
if (intval($_POST['valSaldoCuentaAhorro']) != 0) {
    if (intval($_POST['seqBancoCuentaAhorro']) == 1) {
        $arrErrores[] = "Indique el banco de la cuenta de ahorro";
    }
    if (trim($_POST['txtSoporteCuentaAhorro']) == "") {
        $arrErrores[] = "Indique el soporte para la cuenta de ahorro";
    }
    if (!esFechaValida($_POST['fchAperturaCuentaAhorro'])) {
        $arrErrores[] = "Indique la fecha de apertura de la cuenta de ahorro";
    }
}

// Validacion para la otra cuenta de ahorro
if (intval($_POST['valSaldoCuentaAhorro2']) != 0) {
    if (intval($_POST['seqBancoCuentaAhorro2']) == 1) {
        $arrErrores[] = "Indique el banco del campo otro ahorro";
    }
    if (trim($_POST['txtSoporteCuentaAhorro2']) == "") {
        $arrErrores[] = "Indique el soporte del campo otro ahorro";
    }
    if (!esFechaValida($_POST['fchAperturaCuentaAhorro2'])) {
        $arrErrores[] = "Indique la fecha del campo otro ahorro";
    }
}

// valor del subsidio nacional
if (intval($_POST['valSubsidioNacional']) != 0) {
    if (trim($_POST['txtSoporteSubsidioNacional']) == "") {
        $arrErrores[] = "Indique el soporte para el subsidio nacional";
    }
}

// valor del saldo de cesantias
if (intval($_POST['valSaldoCesantias']) != 0) {
    if (trim($_POST['txtSoporteCesantias']) == "") {
        $arrErrores[] = "Indique el soporte para el aporte de cesantias";
    }
}

// valor del credito
if (intval($_POST['valCredito']) != 0) {
    if (intval($_POST['seqBancoCredito']) == 1) {
        $arrErrores[] = "Indique el banco que otorga el credito";
    }
    if (trim($_POST['txtSoporteCredito']) == "") {
        $arrErrores[] = "Indique el soporte para el credito";
    }
}

// valor de la donacion (VUR)
if (intval($_POST['valDonacion']) != 0) {
    if (intval($_POST['seqEmpresaDonante']) == 0) {
        $arrErrores[] = "Indique la empresa que ha realizado la donaci&oacute;n";
    }
    if (trim($_POST['txtSoporteDonacion']) == "") {
        $arrErrores[] = "Indique el soporte para la donaci&oacute;n";
    }
}

// validaciones para la modalidad de leasing
if( $_POST['seqModalidad'] == 13 and $_POST['seqEstadoProceso'] == 54){
    if( $_POST['bolViabilidadLeasing'] == 0 ){
        $arrErrores[] = "La solucion debe estar viabilizada por una entidad de un convenio de leasing";
    }
    if( $_POST['seqConvenio'] == 1 ){
        $arrErrores[] = "Seleccione un convenio para el leasing";
    }
    if( $_POST['valCartaLeasing'] == 0 ){
        $arrErrores[] = "El valor de las carta de leasing no es válido";
    }
    if( $_POST['txtSoporteLeasing'] == "" ){
        $arrErrores[] = "Indique cuál es el soporte de la carta de leasing";
    }
    if( esFechaValida($_POST['fchAprobacionLeasing']) ){
        $arrErrores[] = "Seleccione la fecha de aprobación de leasing";
    }
    if( $_POST['numDuracionLeasing'] == 0 ){
        $arrErrores[] = "Indique el numero de meses que dura el leasing";
    }
}

/**********************************************************************************************************************
 * VALIDACIONES ESPECIALES
 **********************************************************************************************************************/

// Salvar el registro si no hay errores
if (empty($arrErrores)) {

    $claCasaMano = new CasaMano();
    $arrCasaMano = $claCasaMano->cargar($_POST['seqFormulario'],$_POST['seqCasaMano']);
    $claCasaMano = end($arrCasaMano);

    // validar los permisos de usuario en sesion para editar informacion o cambiar datos cuando el frm esta cerrado
    if( $_SESSION['privilegios']['editar'] == 1 ){

        // valida los cambios en las variables de calificacion
        // adicion de ciudadanos
        $arrCedulasFormulario = array();
        foreach ($claCasaMano->objPostulacion->arrCiudadano as $objCiudadano) {
            $numDocumento = $objCiudadano->numDocumento;
            $arrCedulasFormulario[] = $numDocumento;
            if (! isset($_POST['hogar'][$numDocumento])) {
                $arrMensajes[] = "Ha modificado datos sensibles a la calificacion de hogares (eliminar ciudadanos), el hogar será devuelto a etapa de inscripcion";
                $bolCambiosCalificacion = true;
            }
        }

        // Determina cuando un ciudadano fue adicionado
        foreach ($_POST['hogar'] as $numDocumento => $arrMiembro) {
            if (!in_array($numDocumento, $arrCedulasFormulario)) {
                $arrMensajes[] = "Ha modificado datos sensibles a la calificacion de hogares (adicion de ciudadano), el hogar será devuelto a etapa de inscripcion";
                $bolCambiosCalificacion = true;
            }
        }

        // Revisa cambios en las variables de calificacion en el formulario
        foreach( $arrCamposCalificacion['formulario'] as $txtClave => $txtValor){
            if( $claCasaMano->objPostulacion->$txtClave != $_POST[$txtClave] ) {
                $arrMensajes[] = "Ha modificado datos sensibles a la calificacion de hogares ($txtValor), el hogar será devuelto a etapa de inscripcion";
                $bolCambiosCalificacion = true;
            }
        }

        // Revisa cambios en las variables de calificacion en los ciudadanos
        foreach( $arrCamposCalificacion['ciudadano'] as $txtClave => $txtValor ) {
            foreach ($claCasaMano->objPostulacion->arrCiudadano as $seqCiudadano => $objCiudadano ){
                if( $objCiudadano->$txtClave != $_POST['hogar'][$objCiudadano->numDocumento][$txtClave] ) {
                    $arrMensajes[] =
                        "Ha modificado datos sensibles a la calificacion del ciudadano " .
                        number_format($objCiudadano->numDocumento) .
                        "($txtValor), el hogar será devuelto a etapa de inscripcion";
                    $bolCambiosCalificacion = true;
                }
            }
        }

        $seqEstadoProceso = $_POST['seqEstadoProceso'];
        $arrEtapa = obtenerDatosTabla("T_FRM_ESTADO_PROCESO",array("seqEstadoProceso","seqEtapa"),"seqEstadoProceso","seqEstadoProceso = " . $seqEstadoProceso);
        $seqEtapa = $arrEtapa[$seqEstadoProceso];
        if( $bolCambiosCalificacion == true ){
            if($seqEtapa == 1 or $seqEtapa == 2) {
                $_POST['seqEstadoProceso'] = 37;
            }else{
                $arrErrores[] = "Esta intentando modificar variables que alteran la calificación en una etapa posterior a la postulación, no se realizará el cambio";
            }
        }

        // el numero de formulario se valida la primera vez que se pone, despues no lo puede cambiar
        if( $claCasaMano->objPostulacion->txtFormulario == "" ){
            if ( trim($_POST['txtFormulario']) != "") {
                $txtFormato = "/^[0-9]{2}[-][0-9]{3,6}$/"; // dos digitos de tutor y hasta seis de numero de formulario
                $txtFormulario = trim($_POST['txtFormulario']);
                if (preg_match($txtFormato, $txtFormulario)) {
                    $arrFormulario = mb_split("-", $txtFormulario);
                    $numFormulario = intval($arrFormulario[1]);
                    $numSiguiente = FormularioSubsidios::tutorSecuencia($txtFormulario);
                    if ($numFormulario != $numSiguiente) {
                        $arrErrores[] = "El formulario $numFormulario no es el n&uacute;mero correcto de secuencia, el numero correcto es $numSiguiente";
                    }
                } else {
                    $arrErrores[] = "El numero del formulario no tiene el formato correcto";
                }
            }
        }else{
            if ( $claCasaMano->objPostulacion->txtFormulario != $_POST['txtFormulario'] ) {
                $arrErrores[] = "No puede cambiar el numero de formulario";
            }
        }

        if( $claCasaMano->objPostulacion->bolCerrado == 1 ){
            if( $_SESSION['privilegios']['cambiar'] == 1 ) {
                if ($_POST['bolCerrado'] == 0) {
                    $_POST['fchPostulacion'] = null;
                    $_POST['txtFormulario'] = "";
                    $_POST['seqUnidadProyecto'] = 1;
                    $_POST['seqEstadoProceso'] = 37;
                }
            }else{
                $arrErrores[] = "No tiene permisos para cambiar información de un formulario cerrado";
            }
        }else {
            if ($_POST['bolCerrado'] == 1) {
                if( $_POST['txtFormulario'] == "" ){
                    $arrErrores[] = "Debe dar el número de formulario";
                }
                $_POST['fchPostulacion'] = date("Y-m-d H:i:s");
            }
        }

    }else {
        $arrErrores[] = "No tiene permisos para modificar registros";
    }

}

/**********************************************************************************************************************
 * SALVANDO EL REGISTRO DE LA POSTULACION
 **********************************************************************************************************************/

if (empty($arrErrores)) {
    $_POST['fchUltimaActualizacion'] = date("Y-m-d H:i:s");

    $claCasaMano->salvar($_POST);


    $arrErrores = $claCasaMano->arrErrores;
    foreach($claCasaMano->arrMensajes as $txtMensaje){
        $arrMensajes[] = $txtMensaje;
    }
}

/*********************************************************************************************************************
 * IMPRESION DE LOS MENSAJES GENERADOS POR EL CODIGO
 ********************************************************************************************************************/

imprimirMensajes($arrErrores,$arrMensajes);

?>
