<?php

class GestionFinancieraProyectos
{

    public $arrProyectos;
    public $arrFiducia;
    public $arrResoluciones;
    public $arrErrores;
    public $arrMensajes;
    public $txtCreador;
    public $arrTitulos;
    private $arrExtensiones;


    public function __construct()
    {
        $this->arrProyectos = array();
        $this->arrFiducia = array();
        $this->arrResoluciones = array();
        $this->arrErrores = array();
        $this->arrMensajes = array();
        $this->arrExtensiones = array("txt","xls","xlsx");
        $this->txtCreador = "SiPIVE - SDHT";
        $this->arrTitulos[] = "Identificador del Proyecto";
        $this->arrTitulos[] = "Nombre del Proyecto";
        $this->arrTitulos[] = "Identificador de la Unidad";
        $this->arrTitulos[] = "Descripcion de la Unidad";
        $this->arrTitulos[] = "Valor a Girar";

    }

    /**
     * obtiene el listado de proyectos
     * @author Bernardo Zerda
     * @version 1.0 Abril 2018
     */
    public function proyectos(){
        global $aptBd;

        $sql = "
            select 
                seqProyecto, 
                txtNombreProyecto
            from t_pry_proyecto
            where bolActivo = 1 
              and seqProyectoPadre is null
            order by txtNombreProyecto
        ";
        $objRes = $aptBd->execute($sql);
        while($objRes->fields){
            $seqProyecto = $objRes->fields['seqProyecto'];
            $this->arrProyectos[$seqProyecto] = $objRes->fields['txtNombreProyecto'];
            $objRes->MoveNext();
        }

    }

    public function informacionResoluciones($seqProyecto){

        $this->arrResoluciones = array();

        $this->datosBasicos($seqProyecto);

        $this->liberaciones($seqProyecto);

        $this->giros($seqProyecto);

    }

    private function datosBasicos($seqProyecto){
        global $aptBd;

        $sql = "
            select
                uac.seqUnidadActo,
                tac.txtTipoActoUnidad,
                uac.numActo,
                uac.fchActo,
                rpr.seqRegistroPresupuestal,
                rpr.numNumeroCDP, 
                rpr.fchFechaCDP, 
                rpr.valValorCDP, 
                rpr.numVigenciaCDP, 
                rpr.numProyectoInversionCDP, 
                rpr.numNumeroRP, 
                rpr.fchFechaRP, 
                rpr.valValorRP, 
                rpr.numVigenciaRP,
                if(pry.seqProyecto is null, con.seqProyecto, pry.seqProyecto) as seqProyecto,
                if(pry.seqProyecto is null, con.txtNombreProyecto, pry.txtNombreProyecto) as txtNombreProyecto,
                if(pry.seqProyecto is null, null, con.seqProyecto) as seqConjunto,
                if(pry.seqProyecto is null, null, con.txtNombreProyecto) as txtNombreConjunto,
                upr.seqUnidadProyecto,
                upr.txtNombreUnidad,
                upr.valSDVEActual,
                uvi.valIndexado
            from t_pry_aad_unidades_vinculadas uvi 
            left join t_pry_unidad_proyecto upr on upr.seqUnidadProyecto = uvi.seqUnidadProyecto
            left join t_pry_proyecto con on uvi.seqProyecto = con.seqProyecto
            left join t_pry_proyecto pry on pry.seqProyecto = con.seqProyectoPadre
            inner join t_pry_aad_unidad_acto uac on uac.seqUnidadActo = uvi.seqUnidadActo
            inner join t_pry_aad_unidad_tipo_acto tac on uac.seqTipoActoUnidad = tac.seqTipoActoUnidad
            left join t_pry_aad_registro_presupuestal rpr on uvi.seqRegistroPresupuestal = rpr.seqRegistroPresupuestal
            where uvi.seqProyecto in (
                select seqProyecto
                from t_pry_proyecto
                where seqProyecto = $seqProyecto
                or seqProyectoPadre = $seqProyecto
            ) and (pry.bolActivo = 1 or pry.bolActivo is null)
            and (con.bolActivo = 1 or con.bolActivo is null)
            order by 
                uac.fchActo,
                upr.seqUnidadProyecto
        ";
        $objRes = $aptBd->execute($sql);
        while ($objRes->fields) {

            $seqUnidadActo = $objRes->fields['seqUnidadActo'];
            $seqRegistroPresupuestal = $objRes->fields['seqRegistroPresupuestal'];
            $seqUnidadProyecto = intval($objRes->fields['seqUnidadProyecto']);

            $this->arrResoluciones[$seqUnidadActo]['tipo'] = $objRes->fields['txtTipoActoUnidad'];
            $this->arrResoluciones[$seqUnidadActo]['numero'] = $objRes->fields['numActo'];
            $this->arrResoluciones[$seqUnidadActo]['fecha'] = new DateTime($objRes->fields['fchActo']);
            $this->arrResoluciones[$seqUnidadActo]['total'] += doubleval($objRes->fields['valIndexado']);
            if(intval($seqRegistroPresupuestal)) {
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['proyectoInversion'] = $objRes->fields['numProyectoInversionCDP'];
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['numeroCDP'] = $objRes->fields['numNumeroCDP'];
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['fechaCDP'] = new DateTime($objRes->fields['fchFechaCDP']);
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['valorCDP'] = $objRes->fields['numValorCDP'];
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['valorCDP'] = $objRes->fields['valValorCDP'];
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['vigenciaCDP'] = $objRes->fields['numVigenciaCDP'];
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['numeroRP'] = $objRes->fields['numNumeroRP'];
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['fechaRP'] = new DateTime($objRes->fields['fchFechaRP']);
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['valorRP'] = $objRes->fields['numValorRP'];
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['valorRP'] = $objRes->fields['valValorRP'];
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['vigenciaRP'] = $objRes->fields['numVigenciaRP'];
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['unidades'][$seqUnidadProyecto]['seqProyecto'] = $objRes->fields['seqProyecto'];
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['unidades'][$seqUnidadProyecto]['proyecto'] = $objRes->fields['txtNombreProyecto'];
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['unidades'][$seqUnidadProyecto]['seqConjunto'] = $objRes->fields['seqConjunto'];
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['unidades'][$seqUnidadProyecto]['conjunto'] = $objRes->fields['txtNombreConjunto'];
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['unidades'][$seqUnidadProyecto]['unidad'] = $objRes->fields['txtNombreUnidad'];
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['unidades'][$seqUnidadProyecto]['valor'] = $objRes->fields['valIndexado'];
            }

            $objRes->MoveNext();
        }

    }

    private function liberaciones($seqProyecto){
        global $aptBd;

        $sql = "
            select distinct
                lib.seqLiberacion, 
                lib.seqUnidadActo,
                lib.seqRegistroPresupuestal,
                lib.valLiberado,
                lib.fchLiberacion,
                concat(usu.txtNombre, ' ',usu.txtApellido) as txtUsuario
            from t_pry_aad_liberacion lib
            inner join t_cor_usuario usu on lib.seqUsuario = usu.seqUsuario
            inner join t_pry_aad_unidades_vinculadas uvi on lib.seqRegistroPresupuestal = uvi.seqRegistroPresupuestal
            where uvi.seqProyecto  in (
                select seqProyecto
                from t_pry_proyecto pry
                where pry.seqProyecto = $seqProyecto
                   or pry.seqProyectoPadre = $seqProyecto
            )
        ";
        $objRes = $aptBd->execute($sql);
        while($objRes->fields){

            $seqLiberacion = $objRes->fields['seqLiberacion'];
            $seqUnidadActo = $objRes->fields['seqUnidadActo'];
            $seqRegistroPresupuestal = $objRes->fields['seqRegistroPresupuestal'];

            $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['total'] += $objRes->fields['valLiberado'];
            $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['detalle'][$seqLiberacion]['valor'] = $objRes->fields['valLiberado'];
            $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['detalle'][$seqLiberacion]['fecha'] = new DateTime($objRes->fields['fchLiberacion']);
            $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['detalle'][$seqLiberacion]['usuario'] = $objRes->fields['txtUsuario'];

            foreach($this->arrResoluciones as $seqUnidadActoResolucion => $arrResoluciones){
                foreach($arrResoluciones['cdp'] as $seqRegistroPresupuestalResolucion => $arrCDP){
                    if($seqRegistroPresupuestalResolucion == $seqRegistroPresupuestal and $seqUnidadActo != $seqUnidadActoResolucion){

                        $this->arrResoluciones[$seqUnidadActoResolucion]['liberaciones'] += $objRes->fields['valLiberado'];
                        $this->arrResoluciones[$seqUnidadActoResolucion]['saldo'] =
                            $this->arrResoluciones[$seqUnidadActoResolucion]['total'] +
                            $this->arrResoluciones[$seqUnidadActoResolucion]['liberaciones'];

                        $this->arrResoluciones[$seqUnidadActo]['liberaciones'] += $objRes->fields['valLiberado'];
                        $this->arrResoluciones[$seqUnidadActo]['saldo'] =
                            $this->arrResoluciones[$seqUnidadActo]['total'] -
                            $this->arrResoluciones[$seqUnidadActo]['liberaciones'];

                        $this->arrResoluciones[$seqUnidadActoResolucion]['cdp'][$seqRegistroPresupuestalResolucion]['liberaciones'] += $objRes->fields['valLiberado'];
                        $this->arrResoluciones[$seqUnidadActoResolucion]['cdp'][$seqRegistroPresupuestalResolucion]['saldo'] =
                            $this->arrResoluciones[$seqUnidadActoResolucion]['cdp'][$seqRegistroPresupuestalResolucion]['valorRP'] +
                            $this->arrResoluciones[$seqUnidadActoResolucion]['cdp'][$seqRegistroPresupuestalResolucion]['liberaciones'];
                    }
                }
            }

            $objRes->MoveNext();
        }

    }

    private function giros($seqProyecto){
        global $aptBd;

//                gfi.txtCertificacion,
//                gfi.bolCedulaOferente,
//                gfi.bolRitOferente,
//                gfi.bolRutOferente,
//                gfi.bolExistenciaOferente,
//                gfi.bolConstitucionFiducia,
//                gfi.bolCedulaFiducia,
//                gfi.bolBancariaFiducia,
//                gfi.bolSuperintendenciaFiducia,
//                gfi.bolCamaraFiducia,
//                gfi.bolRutFiducia,
//                gfi.bolResolucionProyecto,
//                gfi.bolMemorandoProyecto,
//                gfi.fchCreacion,
//                usu.seqUsuario,
//                concat(usu.txtNombre,' ',usu.txtApellido) as txtUsuario,
//                gfd.seqProyecto,
//                gfi.numSecuencia,

        $sql = "
            select 
                gfi.seqGiroFiducia,
                gfd.seqGiroFiduciaDetalle,
                gfd.seqUnidadActo,
                gfd.seqRegistroPresupuestal,
                gfd.seqUnidadProyecto,
                gfd.valGiro
            from t_pry_aad_giro_fiducia gfi
            inner join t_pry_aad_giro_fiducia_detalle gfd on gfi.seqGiroFiducia = gfd.seqGiroFiducia
            inner join t_cor_usuario usu on gfi.seqUsuario = usu.seqUsuario
            where gfd.seqProyecto in (
                select seqProyecto
                from t_pry_proyecto pry
                where pry.seqProyecto = $seqProyecto
                   or pry.seqProyectoPadre = $seqProyecto
            )        
        ";
        $objRes = $aptBd->execute($sql);
        while($objRes->fields){

            $seqGiroFiducia = $objRes->fields['seqGiroFiducia'];
            $seqGiroFiduciaDetalle = $objRes->fields['seqGiroFiduciaDetalle'];
            $seqUnidadActo = $objRes->fields['seqUnidadActo'];
            $seqRegistroPresupuestal = $objRes->fields['seqRegistroPresupuestal'];
            $seqUnidadProyecto = intval($objRes->fields['seqUnidadProyecto']);

            $this->arrResoluciones[$seqUnidadActo]['giros'] += $objRes->fields['valGiro'];
            $this->arrResoluciones[$seqUnidadActo]['saldo'] =
                $this->arrResoluciones[$seqUnidadActo]['total'] +
                $this->arrResoluciones[$seqUnidadActo]['liberaciones'] -
                $this->arrResoluciones[$seqUnidadActo]['giros'];

            $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['giros'] += $objRes->fields['valGiro'];
            $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['saldo'] =
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['valorRP'] +
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['liberaciones'] -
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['giros'];

            $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['unidades'][$seqUnidadProyecto]['giros'] += $objRes->fields['valGiro'];

            $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['unidades'][$seqUnidadProyecto]['saldo'] =
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['unidades'][$seqUnidadProyecto]['valor'] -
                $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['unidades'][$seqUnidadProyecto]['giros'];

            $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['unidades'][$seqUnidadProyecto]['detalle'][$seqGiroFiducia][$seqGiroFiduciaDetalle] = $objRes->fields['valGiro'];

            $objRes->MoveNext();
        }



    }

    public function salvarLiberacion($arrPost){
        global $aptBd;

        // carga la infomacion previa para hacer las validaciones
        $this->informacionResoluciones($arrPost['seqProyecto']);

        // datos necesarios
        $valLiberado = doubleval(mb_ereg_replace("[^0-9]","", $arrPost['valor']));
        $seqUnidadActoPrimario = $arrPost['seqUnidadActoPrimario'];
        $seqUnidadActo = $arrPost['seqUnidadActo'];
        $seqRegistroPresupuestal = $arrPost['seqRegistroPresupuestal'];

        // validacion del valor
        if($valLiberado == 0){
            $this->arrErrores[] = "No debe dejar vacío el valor a liberar";
        }

        // validacion para liberacion del CDP
        if(isset($this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['saldo'])){
            if($valLiberado > $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['saldo']){
                $this->arrErrores[] = "No hay suficientes recursos para liberar del RP " .
                    $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['numeroRP'] . " del " .
                    $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['fechaRP']->format("Y");
            }
        }else{
            if($valLiberado > $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['valorRP']){
                $this->arrErrores[] = "No hay suficientes recursos para liberar del RP " .
                    $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['numeroRP'] . " del " .
                    $this->arrResoluciones[$seqUnidadActo]['cdp'][$seqRegistroPresupuestal]['fechaRP']->format("Y");
            }
        }

        // validacion contra la resolucion de liberacion
        if(isset($this->arrResoluciones[$seqUnidadActoPrimario]['saldo'])){
            if($valLiberado > abs($this->arrResoluciones[$seqUnidadActoPrimario]['saldo'])){
                $this->arrErrores[] = "No hay suficientes recursos para liberar de la resolución " .
                    $this->arrResoluciones[$seqUnidadActoPrimario]['numero'] . " del " .
                    $this->arrResoluciones[$seqUnidadActoPrimario]['fecha']->format("Y");
            }
        }else{
            if($valLiberado > abs($this->arrResoluciones[$seqUnidadActoPrimario]['total'])){
                $this->arrErrores[] = "No hay suficientes recursos para liberar de la resolución " .
                    $this->arrResoluciones[$seqUnidadActoPrimario]['numero'] . " del " .
                    $this->arrResoluciones[$seqUnidadActoPrimario]['fecha']->format("Y");
            }
        }

        // salva registro
        if(empty($this->arrErrores)){

            try{
                $aptBd->BeginTrans();

                $sql = "
                    insert into t_pry_aad_liberacion(
                        seqUnidadActo,
                        seqRegistroPresupuestal,
                        valLiberado,
                        fchLiberacion,
                        seqUsuario
                    ) values (
                        $seqUnidadActoPrimario,
                        $seqRegistroPresupuestal,
                        " . ($valLiberado * -1) . ",
                        now(),
                        " . $_SESSION['seqUsuario'] . "  
                    )
                ";
                $aptBd->execute($sql);

                $this->arrMensajes[] = "Registro de liberación de recursos ha sido salvado";

                // carga la informacion posterior a la salvada del registro
                $this->informacionResoluciones($arrPost['seqProyecto']);

                $aptBd->CommitTrans();
            } catch ( Exception $objError ){
                $aptBd->RollbackTrans();
                $this->arrErrores[] = $objError->getMessage();
                $this->Mensajes[] = array();
            }

        }

    }

    public function eliminarLiberacion($arrPost){
        global $aptBd;

        try{
            $aptBd->BeginTrans();

            $sql = "
                delete 
                from t_pry_aad_liberacion
                where seqLiberacion = " . $arrPost['seqLiberacion'] . "
            ";
            $aptBd->execute($sql);

            $this->arrMensajes[] = "Registro de liberación de recursos eliminado";

            // carga la informacion posterior a la salvada del registro
            $this->informacionResoluciones($arrPost['seqProyecto']);

            $aptBd->CommitTrans();
        } catch ( Exception $objError ){
            $aptBd->RollbackTrans();
            $this->arrErrores[] = $objError->getMessage();
            $this->Mensajes[] = array();
        }

    }

    /**
     * OBTIENE LOS DATOS CARGADOS EN EL ARCHIVO
     * SEA UN EXCEL O UN ARCHIVO PLANO
     * @return array
     */
    public function cargarArchivo(){

        $arrArchivo = array();

        // valida si el archivo fue cargado y si corresponde a las extensiones válidas
        switch ($_FILES['archivo']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $this->arrErrores[] = "El archivo \"" . $_FILES['archivo']['name'] . "\" Excede el tamaño permitido";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $this->arrErrores[] = "El archivo \"" . $_FILES['archivo']['name'] . "\" Excede el tamaño permitido";
                break;
            case UPLOAD_ERR_PARTIAL:
                $this->arrErrores[] = "El archivo \"" . $_FILES['archivo']['name'] . "\" no fue completamente cargado, intente de nuevo, si el error persiste contacte al administrador";
                break;
            case UPLOAD_ERR_NO_FILE:
                $this->arrErrores[] = "Debe especificar un archivo para cargar";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->arrErrores[] = "El archivo \"" . $_FILES['archivo']['name'] . "\" no se pudo cargar por falta de carpeta temporal, contacte al administrador";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $this->arrErrores[] = "El archivo \"" . $_FILES['archivo']['name'] . "\" no se pudo guardar en el servidor, contacte al administrador";
                break;
            case UPLOAD_ERR_EXTENSION:
                $this->arrErrores[] = "El archivo \"" . $_FILES['archivo']['name'] . "\" no se pudo guardar en el servidor por un problema de extensiones, contacte al administrador";
                break;
            default:
                $numPunto = strpos($_FILES['archivo']['name'], ".") + 1;
                $numRestar = ( strlen($_FILES['archivo']['name']) - $numPunto ) * -1;
                $txtExtension = substr($_FILES['archivo']['name'], $numRestar);
                if (!in_array(strtolower($txtExtension), $this->arrExtensiones)) {
                    $this->arrErrores[] = "Tipo de Archivo no permitido $txtExtension ";
                }
                break;
        }

        if( empty( $this->arrErrores ) ){

            // si es un archivo de texto obtiene los datos
            if( $_FILES['archivo']['type'] == "text/plain" ){
                foreach( file( $_FILES['archivo']['tmp_name'] ) as $numLinea => $txtLinea ){
                    if( trim( $txtLinea ) != "" ) {
                        $arrArchivo[$numLinea] = explode("\t", trim($txtLinea));
                        foreach( $arrArchivo[$numLinea] as $numColumna => $txtCelda ){
                            if( $numColumna < count( $this->arrFormatoArchivo ) ) {
                                $arrArchivo[$numLinea][$numColumna] = trim($txtCelda);
                            }else{
                                unset( $arrArchivo[$numLinea][$numColumna] );
                            }
                        }
                    }
                }
            }else{

                try{

                    // crea las clases para la obtencion de los datos
                    $txtTipoArchivo = PHPExcel_IOFactory::identify($_FILES['archivo']['tmp_name']);
                    $objReader = PHPExcel_IOFactory::createReader($txtTipoArchivo);
                    $objPHPExcel = $objReader->load($_FILES['archivo']['tmp_name']);
                    $objHoja = $objPHPExcel->getSheet(0);

                    // obtiene las dimensiones del archivo para la obtencion del contenido por rangos
                    $numFilas = $objHoja->getHighestRow();
                    $numColumnas = PHPExcel_Cell::columnIndexFromString( $objHoja->getHighestColumn() ) - 1;

                    // obtiene los datos del rango obtenido
                    for( $numFila = 1; $numFila <= $numFilas; $numFila++ ){
                        for( $numColumna = 0; $numColumna < $numColumnas; $numColumna++ ){
                            $numFilaArreglo = $numFila - 1;
                            $arrArchivo[$numFilaArreglo][$numColumna] = $objHoja->getCellByColumnAndRow($numColumna,$numFila)->getValue();
                            if( $this->arrFormatoArchivo[$numColumna]['tipo'] == "fecha" and is_numeric( $arrArchivo[$numFilaArreglo][$numColumna] ) ) {
                                $claFecha = PHPExcel_Shared_Date::ExcelToPHPObject($arrArchivo[$numFilaArreglo][$numColumna]);
                                $arrArchivo[$numFilaArreglo][$numColumna] = $claFecha->format("Y-m-d");

                            }
                        }
                    }

                    // si no tiene la celda de clave llena no carga
                    if( $objPHPExcel->getProperties()->getCreator() == $this->txtCreador ) {

                        // limpia las lineas vacias
                        foreach ($arrArchivo as $numLinea => $arrLinea) {
                            $bolLineaVacia = true;
                            foreach ($arrLinea as $numColumna => $txtCelda) {
                                if ($txtCelda != "") {
                                    $bolLineaVacia = false;
                                    $arrArchivo[$numLinea][$numColumna] = trim($txtCelda);
                                }
                            }
                            if ($bolLineaVacia == true) {
                                unset($arrArchivo[$numLinea]);
                            }
                        }

                    }else{
                        $this->arrErrores[] = "No se va a cargar el archivo porque no corresponde a la plantilla que se obtiene de la aplicación";
                    }

                } catch ( Exception $objError ){
                    $this->arrErrores[] = $objError->getMessage();
                }


            }

        }

        if(count($arrArchivo) == 1){
            $this->arrErrores[] = "Un archivo que contiene solo los titulos se considera vacío";
        }

        return $arrArchivo;
    }

    public function validarArchivo($arrPost, $arrArchivo){

        $this->validarTitulos($arrArchivo[0]);

        $arrRetorno = array();

        for($i = 1; $i < count($arrArchivo); $i++){

            $seqProyecto       = intval($arrArchivo[$i][0]);
            $txtNombreProyecto = trim(mb_strtolower($arrArchivo[$i][1]));
            $seqUnidadProyecto = intval($arrArchivo[$i][2]);
            $txtUnidadProyecto = trim(mb_strtolower($arrArchivo[$i][3]));
            $valGiro           = doubleval($arrArchivo[$i][4]);

            // valida el identificador del proyecto
            if($seqProyecto == 0){
                $this->arrErrores[] = "Error linea " . ($i + 1) . ": El valor de la columna " . $this->arrTitulos[0] . " no es válido";
            }else{

                // debe coincidir el identificador del proyecto con el identificador en el archivo
                $arrProyecto = obtenerDatosTabla(
                    "t_pry_proyecto",
                    array("0", "seqProyecto","seqProyectoPadre"),
                    "0",
                    "seqProyecto = '" . $seqProyecto . "'"
                );

                // debe coincidir el proyecto en el arcivo con el del formulario
                if($arrPost['seqProyecto'] != $arrProyecto[0]['seqProyecto'] and $arrPost['seqProyecto'] != $arrProyecto[0]['seqProyectoPadre']){
                    $this->arrErrores[] = "Error linea " . ($i + 1) . ": El proyecto no coincide con el seleccionado en el formulario";
                }

                // debe coincidir el nombre del proyecto con el identificador en el archivo
                $arrProyecto = obtenerDatosTabla(
                    "t_pry_proyecto",
                    array("lower(txtNombreProyecto)","seqProyecto","seqProyectoPadre"),
                    "lower(txtNombreProyecto)",
                    "lower(txtNombreProyecto) = '" . $txtNombreProyecto . "'"
                );

                if($arrProyecto[$txtNombreProyecto]['seqProyecto'] != $seqProyecto and $arrProyecto[$txtNombreProyecto]['seqProyectoPadre'] != $seqProyecto){
                    $this->arrErrores[] = "Error linea " . ($i + 1) . ": El identificador del proyecto no coincide con el nombre consignado en el archivo";
                }

            }

            // cantidad de unidades del proyecto
            $numUnidades = array_shift(
                obtenerDatosTabla(
                    "t_pry_unidad_proyecto",
                    array("seqProyecto","count(seqUnidadProyecto) as cantidad"),
                    "seqProyecto",
                    "seqProyecto = " . $seqProyecto
                )
            );

            // validacion de los campos de las unidades
            if($numUnidades == 0){
                if($seqUnidadProyecto != 0){
                    $this->arrErrores[] = "Error linea " . ($i + 1) . ": El proyecto no tiene unidades relacionadas, no debe tener identificador de unidad";
                }
                if($txtUnidadProyecto != ""){
                    $this->arrErrores[] = "Error linea " . ($i + 1) . ": El proyecto no tiene unidades relacionadas, no debe tener nombre de unidad";
                }
            }else{

                // validacion del dato de la unidad
                if($seqUnidadProyecto == 0){
                    $this->arrErrores[] = "Error linea " . ($i + 1) . ": El valor de la columna " . $this->arrTitulos[2] . " no es válido";
                }
                if($txtUnidadProyecto == ""){
                    $this->arrErrores[] = "Error linea " . ($i + 1) . ": El valor de la columna " . $this->arrTitulos[3] . " no es válido";
                }

                // datos de la unidad
                $arrUnidad = obtenerDatosTabla(
                    "t_pry_unidad_proyecto",
                    array("seqUnidadProyecto","lower(txtNombreUnidad) as txtNombreUnidad","seqProyecto"),
                    "seqUnidadProyecto",
                    "seqUnidadProyecto = " . $seqUnidadProyecto
                );

                // la unidad debe coincidir en nombre e identificador
                if($arrUnidad[$seqUnidadProyecto]['txtNombreUnidad'] != $txtUnidadProyecto){
                    $this->arrErrores[] = "Error linea " . ($i + 1) . ": El nombre de la unidad no coincide con el identificador";
                }

                // la unidad debe pertenecer al proyecto
                if($arrUnidad[$seqUnidadProyecto]['seqProyecto'] != $seqProyecto){
                    $this->arrErrores[] = "Error linea " . ($i + 1) . ": La unidad no pertenece al proyecto";
                }

            }

            // validacion del monto agirar
            if(! is_numeric($arrArchivo[$i][4])){
                $this->arrErrores[] = "Error linea " . ($i + 1) . ": El valor de la columna " . $this->arrTitulos[5] . " no es válido";
            }

            if(empty($this->arrErrores)){
                if($valGiro != 0) {
                    $seqProyecto = $arrPost['seqProyecto'];
                    $seqUnidadActo = $arrPost['seqUnidadActo'];
                    $seqRegistroPresupuestal = $arrPost['seqRegistroPresupuestal'];
                    $arrRetorno[$seqProyecto][$seqUnidadActo][$seqRegistroPresupuestal][$seqUnidadProyecto] = $valGiro;
                }
            }

        }

        return $arrRetorno;
    }

    private function validarTitulos($arrTitulos){
        foreach($this->arrTitulos as $i => $txtTitulo){
            if(mb_strtolower(trim($txtTitulo)) != mb_strtolower(trim($arrTitulos[$i]))){
                $this->arrErrores[] = "La columna del archivo " . $txtTitulo . " no se encuentra o no está en el lugar correcto";
            }
        }
    }

    public function salvarGiro($arrPost){
        global $aptBd;

        /**
         * VALIDACIONES DE LOS CAMPOS
         */

        if($arrPost['seqProyecto'] == 0){
            $this->arrErrores[] = "Seleccione el proyecto para el que desea hacer el giro";
        }

        if($arrPost['seqUnidadActo'] == 0){
            $this->arrErrores[] = "Seleccione el acto administrativo para el que desea hacer el giro";
        }

        if($arrPost['seqRegistroPresupuestal'] == 0){
            $this->arrErrores[] = "Seleccione el registro presupuestal para el que desea hacer el giro";
        }

        if((! isset($arrPost['unidades'])) or empty($arrPost['unidades'])){
            $this->arrErrores[] = "No ha seleccionado las unidades para el giro";
        }

        if(trim($arrPost['txtCertificacion']) == ""){
            $this->arrErrores[] = "No puede dejar el campo Certificacion vacío";
        }

        if((! isset($arrPost['documentos'])) or empty($arrPost['documentos'])){
            $this->arrErrores[] = "Seleccione al menos un documento para salvar el giro";
        }

        if(trim($arrPost['txtSubsecretario']) == "" and trim($arrPost['txtSubdirector']) == ""){
            $this->arrErrores[] = "Debe estar al menos el nombre del Subsecretario o el del Subdirector";
        }

        if(trim($arrPost['txtReviso']) == ""){
            $this->arrErrores[] = "Debe dar el nombre de quien revisa el documento";
        }

        $arrPost['txtSubsecretario'] = (trim($arrPost['txtSubsecretario']) == "")? "null" :  trim($arrPost['txtSubsecretario']);
        $arrPost['txtSubdirector']   = (trim($arrPost['txtSubdirector']) == "")? "null"   :  trim($arrPost['txtSubdirector']);
        $arrPost['txtReviso']        = (trim($arrPost['txtReviso']) == "")? "null"        :  trim($arrPost['txtReviso']);

        /**
         * SALVA EL REGISTRO
         */

        if(empty($this->arrErrores)){

            try{
                $aptBd->BeginTrans();

                $sql = "
                    insert into t_pry_aad_giro_fiducia (
                      numSecuencia,
                      txtCertificacion,
                      bolCedulaOferente,
                      bolRitOferente,
                      bolRutOferente,
                      bolExistenciaOferente,
                      bolConstitucionFiducia,
                      bolCedulaFiducia,
                      bolBancariaFiducia,
                      bolSuperintendenciaFiducia,
                      bolCamaraFiducia,
                      bolRutFiducia,
                      bolResolucionProyecto,
                      bolMemorandoProyecto,
                      txtSubsecretario,
                      bolEncargoSubsecretario,
                      txtSubdirector,
                      bolEncargoSubdirector,
                      txtReviso,
                      fchCreacion,
                      seqUsuario
                  ) values (
                      " . $this->obtenerSecuencia($arrPost['seqProyecto']) . ",
                      '" . mb_strtoupper($arrPost['txtCertificacion']) . "',
                      " . intval($arrPost['documentos']['bolCedulaOferente']) . ",  
                      " . intval($arrPost['documentos']['bolRitOferente']) . ",  
                      " . intval($arrPost['documentos']['bolRutOferente']) . ",  
                      " . intval($arrPost['documentos']['bolExistenciaOferente']) . ",  
                      " . intval($arrPost['documentos']['bolConstitucionFiducia']) . ",  
                      " . intval($arrPost['documentos']['bolCedulaFiducia']) . ",  
                      " . intval($arrPost['documentos']['bolBancariaFiducia']) . ",  
                      " . intval($arrPost['documentos']['bolSuperintendenciaFiducia']) . ",  
                      " . intval($arrPost['documentos']['bolCamaraFiducia']) . ",  
                      " . intval($arrPost['documentos']['bolRutFiducia']) . ",  
                      " . intval($arrPost['documentos']['bolResolucionProyecto']) . ",  
                      " . intval($arrPost['documentos']['bolMemorandoProyecto']) . ",  
                      '" . $arrPost['txtSubsecretario'] . "',
                      " . intval($arrPost['bolEncargoSubsecretario']) . ",
                      '" . $arrPost['txtSubdirector'] . "',
                      " . intval($arrPost['bolEncargoSubdirector']) . ",
                      '" . $arrPost['txtReviso'] . "',
                      now(),
                      " . $_SESSION['seqUsuario'] . "
                  )
                ";
                $aptBd->execute($sql);

                $seqGiroFiducia = $aptBd->Insert_ID();

                $seqProyecto = $arrPost['seqProyecto'];
                $seqUnidadActo = $arrPost['seqUnidadActo'];
                $seqRegistroPresupuestal = $arrPost['seqRegistroPresupuestal'];

                foreach ($arrPost['unidades'][$seqProyecto][$seqUnidadActo][$seqRegistroPresupuestal] as $seqUnidadProyecto => $valGiro){

                    if(intval($seqUnidadProyecto) != 0) {
                        $seqProyectoInsertar = array_shift(
                            obtenerDatosTabla(
                                "t_pry_unidad_proyecto",
                                array("seqUnidadProyecto", "seqProyecto"),
                                "seqUnidadProyecto",
                                "seqUnidadProyecto = " . $seqUnidadProyecto
                            )
                        );
                    }else{
                        $seqProyectoInsertar = $seqProyecto;
                        $seqUnidadProyecto = "null";
                    }
                    $sql = "
                        insert into t_pry_aad_giro_fiducia_detalle(
                            seqGiroFiducia, 
                            seqProyecto, 
                            seqUnidadActo, 
                            seqRegistroPresupuestal, 
                            seqUnidadProyecto, 
                            valGiro
                        ) values (
                            $seqGiroFiducia,
                            $seqProyectoInsertar,
                            $seqUnidadActo,
                            $seqRegistroPresupuestal,
                            $seqUnidadProyecto,
                            $valGiro
                        ) 
                    ";
                    $aptBd->execute($sql);
                }

                $this->arrMensajes[] = "Registro salvado satisfactoriamente";

                $aptBd->CommitTrans();

                return $seqGiroFiducia;

            } catch ( Exception $objError ){
                $aptBd->RollbackTrans();
                $this->arrMensajes = array();
                $this->arrErrores[] = $objError->getMessage();

                return 0;
            }

        }

    }

    private function obtenerSecuencia($seqProyecto){
        global $aptBd;

        $sql = "
            select 
              if(max(gfi.numSecuencia) is null, 0 ,max(gfi.numSecuencia)) + 1 as numSecuencia
            from t_pry_aad_giro_fiducia gfi
            inner join t_pry_aad_giro_fiducia_detalle gfd on gfi.seqGiroFiducia = gfd.seqGiroFiducia
            where gfd.seqProyecto in (
                select seqProyecto
                from t_pry_proyecto pry
                where pry.seqProyecto = $seqProyecto
                   or pry.seqProyectoPadre = $seqProyecto
            ) and year(gfi.fchCreacion) = year(now())
        ";
        return array_shift($aptBd->GetAll($sql))['numSecuencia'];
    }

    public function pdfGiroFiducia($seqProyecto, $seqGiroFiducia){
        global $aptBd;

        $arrDatosFormato = array();

        $sql = "
            select 
                upper(pry.txtNombreProyecto) as txtNombreProyecto,
                upper(pry.txtNombreVendedor) as txtNombreVendedor,
                pry.numNitVendedor,
                pry.seqDatoFiducia,
                ban.txtBanco, 
                ban.numNit,
                dfi.numContrato, 
                dfi.fchContrato, 
                dfi.numCuenta, 
                dfi.txtTipoCuenta
            from t_pry_proyecto pry
            left join t_pry_datos_fiducia dfi on pry.seqDatoFiducia = dfi.seqDatoFiducia
            left join t_frm_banco ban on dfi.seqBanco = ban.seqBanco
            where seqProyecto = $seqProyecto          
        ";
        $objRes = $aptBd->execute($sql);
        while($objRes->fields){

            $arrDatosFormato['secciones']['Beneficiario del giro'][0][0] = "Nombre del Vendedor";
            $arrDatosFormato['secciones']['Beneficiario del giro'][0][1] = $objRes->fields['txtNombreVendedor'];;

            $arrDatosFormato['secciones']['Beneficiario del giro'][1][0] = "NIT del Vendedor";
            $arrDatosFormato['secciones']['Beneficiario del giro'][1][1] = $objRes->fields['numNitVendedor'];

            $arrDatosFormato['secciones']['Beneficiario del giro'][2][0] = "Nombre del Proyecto";
            $arrDatosFormato['secciones']['Beneficiario del giro'][2][1] = $objRes->fields['txtNombreProyecto'];

            $arrDatosFormato['secciones']['Información para el giro'][0][0] = "Número del contrato suscrito";
            $arrDatosFormato['secciones']['Información para el giro'][0][1] = (intval($objRes->fields['numContrato']) == 0)? "No disponible" : number_format(intval($objRes->fields['numContrato']),0,',','.');

            $arrDatosFormato['secciones']['Información para el giro'][1][0] = "Fecha del contrato suscrito";
            $arrDatosFormato['secciones']['Información para el giro'][1][1] = (!esFechaValida($objRes->fields['fchContrato']))? "No disponible" : strftime("%d de %B de %Y" , strtotime( $objRes->fields['fchContrato'] ) );

            $arrDatosFormato['secciones']['Información para el giro'][3][0] = "Nombre de la entidad financiera";
            $arrDatosFormato['secciones']['Información para el giro'][3][1] = $objRes->fields['txtBanco'];

            $arrDatosFormato['secciones']['Información para el giro'][4][0] = "NIT de la entidad financiera";
            $arrDatosFormato['secciones']['Información para el giro'][4][1] = $objRes->fields['numNit'];

            $arrDatosFormato['secciones']['Información para el giro'][5][0] = "Número de cuenta";
            $arrDatosFormato['secciones']['Información para el giro'][5][1] = $objRes->fields['numCuenta'];

            $arrDatosFormato['secciones']['Información para el giro'][5][0] = "Tipo de cuenta";
            $arrDatosFormato['secciones']['Información para el giro'][5][1] = $objRes->fields['txtTipoCuenta'];

            $objRes->MoveNext();
        }

        $sql = "
            SELECT
                gfi.numSecuencia,
                gfi.txtCertificacion,
                gfi.bolCedulaOferente,
                gfi.bolRitOferente,
                gfi.bolRutOferente,
                gfi.bolExistenciaOferente,
                gfi.bolConstitucionFiducia,
                gfi.bolCedulaFiducia,
                gfi.bolBancariaFiducia,
                gfi.bolSuperintendenciaFiducia,
                gfi.bolCamaraFiducia,
                gfi.bolRutFiducia,
                gfi.bolResolucionProyecto,
                gfi.bolMemorandoProyecto,
                gfi.txtSubsecretario,
                gfi.bolEncargoSubsecretario,
                gfi.txtSubdirector,
                gfi.bolEncargoSubdirector,
                gfi.txtReviso,
                gfi.fchCreacion,
                gfi.seqUsuario,
                concat(usu.txtNombre, ' ', usu.txtApellido) as txtUsuario,
                gfd.seqUnidadActo,
                gfd.seqRegistroPresupuestal,
                count(gfd.seqUnidadProyecto) as numUnidades,
                sum(gfd.valGiro) as valGiros
            FROM t_pry_aad_giro_fiducia gfi
            INNER JOIN t_pry_aad_giro_fiducia_detalle gfd ON gfi.seqGiroFiducia = gfd.seqGiroFiducia
            INNER JOIN t_cor_usuario usu on gfi.seqUsuario = usu.seqUsuario
            WHERE gfi.seqGiroFiducia = $seqGiroFiducia
              AND gfd.seqProyecto IN (
                SELECT pry.seqProyecto
                FROM t_pry_proyecto pry
                WHERE pry.seqProyecto = $seqProyecto 
                   OR pry.seqProyectoPadre = $seqProyecto
              )
            GROUP BY 
                gfi.numSecuencia,
                gfi.txtCertificacion,
                gfi.bolCedulaOferente,
                gfi.bolRitOferente,
                gfi.bolRutOferente,
                gfi.bolExistenciaOferente,
                gfi.bolConstitucionFiducia,
                gfi.bolCedulaFiducia,
                gfi.bolBancariaFiducia,
                gfi.bolSuperintendenciaFiducia,
                gfi.bolCamaraFiducia,
                gfi.bolRutFiducia,
                gfi.bolResolucionProyecto,
                gfi.bolMemorandoProyecto,
                gfi.txtSubsecretario,
                gfi.bolEncargoSubsecretario,
                gfi.txtSubdirector,
                gfi.bolEncargoSubdirector,
                gfi.txtReviso,
                gfi.fchCreacion,
                gfi.seqUsuario,
                concat(usu.txtNombre, ' ', usu.txtApellido),
                gfd.seqUnidadActo,
                gfd.seqRegistroPresupuestal      
        ";
        $objRes = $aptBd->execute($sql);
        while($objRes->fields){

            $fchCreacion = new DateTime($objRes->fields['fchCreacion']);

            $arrDatosFormato['secuencia'] = "SDHT-SGF-SDRPL-" . $seqProyecto . "-" . $objRes->fields['numSecuencia'] . "-" . $fchCreacion->format(y);

            $arrDatosFormato['secciones']['Beneficiario del giro'][3][0] = "Valor del giro";
            $arrDatosFormato['secciones']['Beneficiario del giro'][3][1] = "$ " . number_format($objRes->fields['valGiros'],0,',','.');

            $arrDatosFormato['secciones']['Beneficiario del giro'][4][0] = "Cantidad de unidades";
            $arrDatosFormato['secciones']['Beneficiario del giro'][4][1] = number_format($objRes->fields['numUnidades'],0,',','.');


            $arrDatosFormato['certificacion'] = $objRes->fields['txtCertificacion'];

            $arrDatosFormato['documentos']['Del Oferente'][0][0] = "Copia cedula de ciudadanía";
            $arrDatosFormato['documentos']['Del Oferente'][0][1] = (intval($objRes->fields['bolCedulaOferente']) == 1)? "SI" : "NO";

            $arrDatosFormato['documentos']['Del Oferente'][1][0] = "Copia del Registro de Información Tributaria / RIT";
            $arrDatosFormato['documentos']['Del Oferente'][1][1] = (intval($objRes->fields['bolRitOferente']) == 1)? "SI" : "NO";

            $arrDatosFormato['documentos']['Del Oferente'][2][0] = "Copia del Registro Único Tributario / RUT";
            $arrDatosFormato['documentos']['Del Oferente'][2][1] = (intval($objRes->fields['bolRutOferente']) == 1)? "SI" : "NO";

            $arrDatosFormato['documentos']['Del Oferente'][3][0] = "Copia del Certificado de existencia y representación legal";
            $arrDatosFormato['documentos']['Del Oferente'][3][1] = (intval($objRes->fields['bolExistenciaOferente']) == 1)? "SI" : "NO";

            $arrDatosFormato['documentos']['De la Entidad Financiera con la cual se constituyó el  Encargo Fiduciario'][0][0] = "Copia constitución Encargo Fiduciario";
            $arrDatosFormato['documentos']['De la Entidad Financiera con la cual se constituyó el  Encargo Fiduciario'][0][1] = (intval($objRes->fields['bolConstitucionFiducia']) == 1)? "SI" : "NO";

            $arrDatosFormato['documentos']['De la Entidad Financiera con la cual se constituyó el  Encargo Fiduciario'][1][0] = "Copia cedula de ciudadanía";
            $arrDatosFormato['documentos']['De la Entidad Financiera con la cual se constituyó el  Encargo Fiduciario'][1][1] = (intval($objRes->fields['bolCedulaFiducia']) == 1)? "SI" : "NO";

            $arrDatosFormato['documentos']['De la Entidad Financiera con la cual se constituyó el  Encargo Fiduciario'][2][0] = "Certificación Bancaria de la cuenta en la cual se va a realizar el giro";
            $arrDatosFormato['documentos']['De la Entidad Financiera con la cual se constituyó el  Encargo Fiduciario'][2][1] = (intval($objRes->fields['bolBancariaFiducia']) == 1)? "SI" : "NO";

            $arrDatosFormato['documentos']['De la Entidad Financiera con la cual se constituyó el  Encargo Fiduciario'][3][0] = "Copia del Certificado de existencia y representación legal expedido por la Superintendencia Financiera";
            $arrDatosFormato['documentos']['De la Entidad Financiera con la cual se constituyó el  Encargo Fiduciario'][3][1] = (intval($objRes->fields['bolSuperintendenciaFiducia']) == 1)? "SI" : "NO";

            $arrDatosFormato['documentos']['De la Entidad Financiera con la cual se constituyó el  Encargo Fiduciario'][4][0] = "Copia del Certificado de existencia y representación legal expedido por la Cámara de Comercio";
            $arrDatosFormato['documentos']['De la Entidad Financiera con la cual se constituyó el  Encargo Fiduciario'][4][1] = (intval($objRes->fields['bolCamaraFiducia']) == 1)? "SI" : "NO";

            $arrDatosFormato['documentos']['De la Entidad Financiera con la cual se constituyó el  Encargo Fiduciario'][5][0] = "Copia del Registro Único Tributario – RUT de la entidad financiera";
            $arrDatosFormato['documentos']['De la Entidad Financiera con la cual se constituyó el  Encargo Fiduciario'][5][1] = (intval($objRes->fields['bolRutFiducia']) == 1)? "SI" : "NO";

            $arrDatosFormato['documentos']['Del Proyecto'][0][0] = "Copia Resolución 488 de 2016 y 541 de 2016";
            $arrDatosFormato['documentos']['Del Proyecto'][0][1] = (intval($objRes->fields['bolResolucionProyecto']) == 1)? "SI" : "NO";

            $arrDatosFormato['documentos']['Del Proyecto'][1][0] = "Copia memorando de solicitud de aprobación póliza de cumplimiento mediante radicado No. 3-2015-35230- con fecha del 05 junio de 2015";
            $arrDatosFormato['documentos']['Del Proyecto'][1][1] = (intval($objRes->fields['bolMemorandoProyecto']) == 1)? "SI" : "NO";


            switch(true){

                // ambas firmas
                case $objRes->fields['txtSubdirector'] != "" and $objRes->fields['txtSubsecretario'] != "":

                    $txtEncargoSubdirector = ($objRes->fields['bolEncargoSubdirector'] == 1) ? "(E)" : "";
                    $txtEncargoSubsecretario = ($objRes->fields['bolEncargoSubsecretario'] == 1) ? "(E)" : "";

                    $arrDatosFormato['firmas'][0][0] = utf8_decode("Subdirector(a) de Recursos Públicos " . $txtEncargoSubdirector);
                    $arrDatosFormato['firmas'][1][0] = utf8_decode(mb_strtoupper($objRes->fields['txtSubdirector']) );
                    $arrDatosFormato['firmas'][0][1] = utf8_decode("Subsecretario(a) de Gestión Financiera " . $txtEncargoSubsecretario);
                    $arrDatosFormato['firmas'][1][1] = utf8_decode(mb_strtoupper($objRes->fields['txtSubsecretario']));

                    break;

                // solo firma el secretario
                case $objRes->fields['txtSubdirector'] == "" and $objRes->fields['txtSubsecretario'] != "":

                    $txtEncargoSubsecretario = ($objRes->fields['bolEncargoSubsecretario'] == 1) ? "(E)" : "";

                    $arrDatosFormato['firmas'][0][0] = utf8_decode("Subsecretario(a) de Gestión Financiera " . $txtEncargoSubsecretario);
                    $arrDatosFormato['firmas'][1][0] = utf8_decode(mb_strtoupper($objRes->fields['txtSubsecretario']));
                    $arrDatosFormato['firmas'][0][1] = "";
                    $arrDatosFormato['firmas'][1][1] = "";

                    break;

                // solo firma el subdirector
                case $objRes->fields['txtSubdirector'] != "" and $objRes->fields['txtSubsecretario'] == "":

                    $txtEncargoSubdirector = ($objRes->fields['bolEncargoSubdirector'] == 1) ? "(E)" : "";

                    $arrDatosFormato['firmas'][0][0] = utf8_decode("Subdirector(a) de Recursos Públicos " . $txtEncargoSubdirector);
                    $arrDatosFormato['firmas'][1][0] = utf8_decode(mb_strtoupper($objRes->fields['txtSubdirector']));
                    $arrDatosFormato['firmas'][0][1] = "";
                    $arrDatosFormato['firmas'][1][1] = "";

                    break;

            }

            $arrDatosFormato['subfirmas'][0][0] = utf8_decode("Revisó");
            $arrDatosFormato['subfirmas'][0][1] = utf8_decode($objRes->fields['txtReviso']) . " - Contratista";

            $arrDatosFormato['subfirmas'][1][0] = utf8_decode("Elaboró");
            $arrDatosFormato['subfirmas'][1][1] = utf8_decode($objRes->fields['txtUsuario']);

            $objRes->MoveNext();
        }


        return $arrDatosFormato;
    }


}


?>