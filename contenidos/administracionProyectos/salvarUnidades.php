<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$txtPrefijoRuta = "../../";

include( "../../librerias/phpExcel/Classes/PHPExcel.php" );
include( "../../librerias/phpExcel/Classes/PHPExcel/Writer/Excel2007.php" );
include( "../../librerias/phpExcel/Classes/PHPExcel/IOFactory.php" );
include( $txtPrefijoRuta . "recursos/archivos/verificarSesion.php" );
include( $txtPrefijoRuta . "recursos/archivos/lecturaConfiguracion.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['funciones'] . "funciones.php" );
include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/inclusionSmarty.php" );
include( $txtPrefijoRuta . $arrConfiguracion['carpetas']['recursos'] . "archivos/coneccionBaseDatos.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "SeguimientoProyectos.class.php" );
include( $txtPrefijoRuta . $arrConfiguracion['librerias']['clases'] . "DatosUnidades.class.php" );

$claDatosUnidades = new DatosUnidades();
$claSeguimiento = new SeguimientoProyectos();

if ($_POST['seqProyecto'] != "" && $_POST['seqProyecto'] != null) {

    $seqProyecto = $_POST['seqProyecto'];
    $infCantUnidades = $claDatosUnidades->ObtenerCantUnidadesProyecto($seqProyecto);
    $unidadesReg = $infCantUnidades['cantidad'];
    $totalUnidades = $infCantUnidades['valNumeroSoluciones'];
    $cantUDisponible = $totalUnidades - $unidadesReg;
    // echo "<p>" . $cantUDisponible . "</p>";
    if (isset($_FILES["archivo"]) && is_uploaded_file($_FILES['archivo']['tmp_name'])) {

        $txtTipoArchivo = PHPExcel_IOFactory::identify($_FILES['archivo']['tmp_name']);
        $name = basename($_FILES['archivoEstado']['name']);        
        $objReader = PHPExcel_IOFactory::createReader($txtTipoArchivo);
        $objPHPExcel = $objReader->load($_FILES['archivo']['tmp_name']);
        $objHoja = $objPHPExcel->getSheet(0);
        $arrayNum = array(1, 2, 3); //
        $arrayVal = array(4, 5, 6);
// obtiene las dimensiones del archivo para la obtencion del contenido por rangos
        $numFilas = $objHoja->getHighestRow();
        $numColumnas = PHPExcel_Cell::columnIndexFromString($objHoja->getHighestColumn()) - 1;

        //   echo "<p>".$cantUDisponible ."<". ($numFilas-1)."</p>";
        if ($cantUDisponible != ($numFilas - 1)) {
            $arrErrores[] = "<div class='alert alert-danger'><h5>Alerta!!! Verifique la cantidad de unidades a ingresar a este proyecto </h5></div>";
        }
// obtiene los datos del rango obtenido
        for ($numFila = 1; $numFila <= $numFilas; $numFila++) {
            if ($numFila != 1) {
                for ($numColumna = 0; $numColumna <= $numColumnas; $numColumna++) {
                    $numFilaArreglo = $numFila - 1;
                    $letra = chr(65 + ($numColumna));
                    if (in_array($numColumna, $arrayNum)) {
                        if (is_numeric($objHoja->getCellByColumnAndRow($numColumna, $numFila)->getValue())) {
                            $arrArchivo[$numFilaArreglo][$numColumna] = $objHoja->getCellByColumnAndRow($numColumna, $numFila)->getValue();
                        } else {
                            $arrErrores[] = "<div class='alert alert-danger'><h5>Alerta!! Por favor verifique que  el campo de la <b>Fila " . ($numFilaArreglo + 1) . "</b> en la <b>Columna " . $letra . " </b> Sea de tipo numerico </h5></div>";
                        }
                    } else if (in_array($numColumna, $arrayVal)) {
                        $arraExp = explode('-', $objHoja->getCellByColumnAndRow($numColumna, $numFila)->getValue());
                        if (!isset($arraExp[1])) {
                            $arrErrores[] = "<div class='alert alert-danger'><h5>Alerta!! Por favor verifique que  el campo de la <b>Fila " . ($numFilaArreglo + 1) . "</b> en la <b>Columna " . $letra . " </b> No tiene el formato </h5></div>";
                        } else {
                            $arrArchivo[$numFilaArreglo][$numColumna] = $objHoja->getCellByColumnAndRow($numColumna, $numFila)->getValue();
                        }
                    } else if ($objHoja->getCellByColumnAndRow($numColumna, $numFila)->getValue() == "") {
                        $arrErrores[] = "<div class='alert alert-danger'><h5>Alerta!! Por favor verifique que  el campo de la <b>Fila " . ($numFilaArreglo + 1) . "</b> en la <b>Columna " . $letra . " </b> se encuentra vacio </h5></div>";
                    } else {
                        $arrArchivo[$numFilaArreglo][$numColumna] = $objHoja->getCellByColumnAndRow($numColumna, $numFila)->getValue();
                    }
                }
            }
        }
        if (empty($arrErrores)) {
            $array = $claDatosUnidades->AlmacenarUnidades($arrArchivo, $seqProyecto);
            if (empty($array)) {
                $txtComentarios = $_POST['txtComentario'];
                $seqGestion = $_POST['seqGestion'];
                $arrayDatosProyNew = Array();
                $arrayDatosProyOld = Array();
                $arrayDatosProyOld[$seqProyecto]['unidades'] = "De un total de <b>" . $numFilaArreglo . "</b> Unidades ";
                $arrayDatosProyOld[$seqProyecto]['nombreArchivo'] = "";
                $arrayDatosProyNew[$seqProyecto]['unidades'] = " Se Almacenaron  <b>" . $numFilaArreglo . "</b> Unidades";
                $arrayDatosProyNew[$seqProyecto]['nombreArchivo'] = "Se realiz&oacute; La creaci&oacute; de la unidades bajo las especificaciones del archivo <b>" . $name . "</b>";
              
                // $txtComentarios = "Se realizó cambios de estado en ". count($arrayDatosProyNew[$seqProyecto])." unidades, bajo las especificaciones del archivo <b>".$name."</b>";
                $claSeguimiento->almacenarSeguimiento($seqProyecto, $txtComentarios, $seqGestion, $arrayDatosProyOld, $arrayDatosProyNew);
                ?>
                <div class='alert alert-success'><h5><b>Exito!!!</b> Los datos que se almacenaron se listan a continuación: </h5></div>
                <table>
                    <tr>
                        <th>Nombre de la unidad </th>
                        <th>Cant SMMLV</th>
                        <th>Valor SDVE Comercial</th>
                        <th>Valor Cierre </th>
                        <th>Plan de Gobierno</th>
                        <th>Modalidad</th>
                        <th>Esquema</th>
                    </tr>
                    <?php
                    foreach ($arrArchivo as $key => $value) {
                        ?>
                        <tr>
                            <td><?php echo $value[0] ?></td>
                            <td><?php echo $value[1] ?></td>
                            <td><?php echo $value[2] ?></td>
                            <td><?php echo $value[3] ?></td>
                            <td><?php echo $value[4] ?></td>
                            <td><?php echo $value[5] ?></td>
                            <td><?php echo $value[6] ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    imprimirMensajes($array, array(), 'mensajes');
                }
                ?>
            </table>
            <?php
            //var_dump($arrArchivo);
        } else {
            imprimirMensajes($arrErrores, array(), 'mensajes');
            //  var_dump($arrErrores);
        }
    }
} else {
    $arrErrores[] = "<div class='alert alert-danger'><h5>Alerta!!  Debe seleccionar un proyecto</h5></div>";
    imprimirMensajes($arrErrores, array(), 'mensajes');
}