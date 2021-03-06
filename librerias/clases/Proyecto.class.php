<?php

/**
 * CLASE QUE REALIZA TODAS LAS OPERACIONES 
 * RELACIONADAS CON LOS PROYECTOS
 * 
 * REQUIERE QUE ESTE CONECTADO A LA BASE DE DATOS
 * 
 * @author Bernardo Zerda
 * @version 0.1 Marzo 2009
 */
class Proyecto {

    public $txtProyecto;      // Nombre de la Proyecto
    public $numLimiteActivos; // Cantidad de activos que se pueden manejar
    public $fchVencimiento;   // Fecha en la que termina el proyecto
    public $bolActivo;        // si la Proyecto esta activa o no
    public $arrProyectoGrupo; // Grupos que estan asociados a la Proyecto
    public $seqUsuario;
    public $txtArchivo;
    public $seqPryEstadoProceso;
    public $txtNombreProyecto;
    public $numNitProyecto;
    public $txtNombrePlanParcial;
    public $txtNombreComercial;
    public $seqTipoEsquema;
    public $seqPryTipoModalidad;
    public $seqOpv;
    public $txtNombreOperador;
    public $txtObjetoProyecto;
    public $seqTipoProyecto;
    public $seqTipoUrbanizacion;
    public $seqTipoSolucion;
    public $txtDescripcionProyecto;
    public $seqLocalidad;
    public $seqBarrio;
    public $txtOtrosBarrios;
    public $bolDireccion;
    public $txtDireccion;
    public $valNumeroSoluciones;
    public $valSalarioMinimo;
    public $numSubsidios;
    public $valTorres;
    public $valAreaLote;
    public $valAreaConstruida;
    public $txtChipLote;
    public $txtMatriculaInmobiliariaLote;
    public $txtRegistroEnajenacion;
    public $fchRegistroEnajenacion;
    public $bolEquipamientoComunal;
    public $txtDescEquipamientoComunal;
    public $seqTutorProyecto;
    public $seqProyectoOferente;

    /**
     * CONSTRUCTOR DE LA CLASE
     * @author Liliana Basto
     * @param Void
     * @return Void
     * @version 2.0 Junio 2017
     */
    public function Proyecto() {
        $this->txtProyecto = "";
        $this->fchVencimiento = NULL;
        $this->bolActivo = false;
        $this->arrProyectoGrupo = array();
        $this->seqUsuario = 0;
        $this->txtArchivo = "";
        $this->seqPryEstadoProceso = 0;
        $this->txtNombreProyecto = "";
        $this->numNitProyecto = 0;
        $this->txtNombrePlanParcial = "";
        $this->txtNombreComercial = "";
        $this->seqTipoEsquema = 0;
        $this->seqPryTipoModalidad = 0;
        $this->seqOpv = 0;
        $this->txtNombreOperador = "";
        $this->txtObjetoProyecto = "";
        $this->seqTipoProyecto = 0;
        $this->seqTipoUrbanizacion = 0;
        $this->seqTipoSolucion = 0;
        $this->txtDescripcionProyecto = "";
        $this->seqLocalidad = 0;
        $this->seqBarrio = 0;
        $this->txtOtrosBarrios = "";
        $this->bolDireccion = false;
        $this->txtDireccion = "";
        $this->valNumeroSoluciones = 0;
        $this->valSalarioMinimo = 0;
        $this->numSubsidios = 0;
        $this->valTorres = 0;
        $this->valAreaLote = 0;
        $this->valAreaConstruida = 0;
        $this->txtChipLote = "";
        $this->txtMatriculaInmobiliariaLote = "";
        $this->txtRegistroEnajenacion = "";
        $this->fchRegistroEnajenacion = NULL;
        $this->bolEquipamientoComunal = FALSE;
        $this->txtDescEquipamientoComunal = "";
        $this->seqTutorProyecto = 0;
        $this->seqProyectoOferente = 0;
    }

// Fin Constructor

    /**
     * CARGA UNA O TODAS LOS PROYECTOS QUE HAY EN
     * LA BASE DE DATOS, DEPENDE DEL PARAMETRO QUE
     * SE LE PASE A LA FUNCION
     * @author Bernardo Zerda
     * @param integer seqProyecto = 0
     * @return array arrProyectos
     * @version 1.0 Marzo de 2009
     */
    public function cargarProyecto($seqProyecto = 0) {

        global $aptBd;

        // Arreglo que se retorna
        $arrProyectos = array();

        // Si viene parametro la consulta es para una sola Proyecto
        $txtCondicion = "";
        if ($seqProyecto != 0) {
            $txtCondicion = " AND seqProyecto = $seqProyecto";
        }

        // Consulta de Proyectos
        $sql = "
	    		SELECT
            		seqProyecto, 
	    			ucwords(txtProyecto) as txtProyecto,
	    			fchVencimiento,
	    			bolActivo,
					seqMenu
	    		FROM 
	    			T_COR_PROYECTO
				WHERE seqProyecto > 1
	    		$txtCondicion
	          ORDER BY  
	            txtProyecto
	    	";
        $objRes = $aptBd->execute($sql);
        if ($aptBd->ErrorMsg() == "") {

            while ($objRes->fields) {

                $seqProyecto = $objRes->fields['seqProyecto'];

                $objProyecto = new Proyecto;
                $objProyecto->txtProyecto = $objRes->fields['txtProyecto'];
                $objProyecto->fchVencimiento = $objRes->fields['fchVencimiento'];
                $objProyecto->bolActivo = $objRes->fields['bolActivo'];
                $objProyecto->seqMenu = $objRes->fields['seqMenu'];
                $arrProyectos[$seqProyecto] = $objProyecto; // arreglo de objetos
                $objRes->MoveNext();
            }

            // Obtiene la informacion de las empesas
            // y su relacion con los grupos
            if (!empty($arrProyectos)) {
                $sql = "
                SELECT
                    seqProyectoGrupo,
                    seqProyecto,
                    seqGrupo
                FROM 
                    T_COR_PROYECTO_GRUPO
				WHERE seqProyecto > 1 
                $txtCondicion
            ";

                $objRes = $aptBd->execute($sql);
                while ($objRes->fields) {

                    $seqProyectoGrupo = $objRes->fields['seqProyectoGrupo'];
                    $seqProyecto = $objRes->fields['seqProyecto'];
                    $seqGrupo = $objRes->fields['seqGrupo'];

                    // Grupos asociados a la Proyecto
                    if (isset($arrProyectos[$seqProyecto])) {
                        $arrProyectos[$seqProyecto]->arrProyectoGrupo[$seqGrupo] = $seqProyectoGrupo;
                    }

                    $objRes->MoveNext();
                }
            }
        }

        return $arrProyectos;
    }

// Fin Cargar Proyecto

    public function listarProyectos() {

        global $aptBd;

        $sql = " SELECT seqProyecto, txtNombreProyecto FROM T_PRY_PROYECTO ORDER BY txtNombreProyecto";

        $objRes = $aptBd->execute($sql);
        $datos = Array();
        while ($objRes->fields) {

            $datos[$objRes->fields['seqProyecto']] = $objRes->fields['txtNombreProyecto'];
            $objRes->MoveNext();
        }
        return $datos;
    }

    /**
     * GUARDA EN LA BASE DE DATOS LA INFORMACION DE 
     * LOS ProyectoOS
     * @author Bernardo Zerda
     * @param String txtNombre
     * @param Date fchVencimiento
     * @param Boolean bolActivo
     * @return Array arrErrores
     * @version 0,1 Marzo 2009
     */
    public function guardarProyecto($txtNombre, $fchVencimiento, $bolActivo, $seqMenu) {

        global $aptBd;
        $arrErrores = array();

        // Instruccion para insertar la emrpesa en la base de datos
        $sql = "
                INSERT INTO T_COR_PROYECTO ( 
                    txtProyecto, 
                    bolActivo,
                    fchVencimiento,
					seqMenu
                ) VALUES (
                    \"" . ereg_replace('\"', "", $txtNombre) . "\", 
                    $bolActivo, 
                    '$fchVencimiento',
					$seqMenu
                )
            ";

        try {
            $aptBd->execute($sql);
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido guardar la Proyecto <b>$txtNombre</b> reporte este error al administrador del sistema";
        }

        return $arrErrores;
    }

// Fin guardar Proyecto
// Fin editar Proyecto

    /**
     * VERIFICA SI SE PUEDE BORRAR LA Proyecto
     * Y SI ES POSIBLE LA BORRA DEL SISTEMA
     * @author Bernardo Zerda
     * @param integer seqProyecto
     * @return array arrErrores
     * @version 1.0 Marzo 2009
     */
    public function borrarProyecto($seqProyecto) {

        global $aptBd;
        $arrErrores = array();

        // Valida que se pueda borrar la Proyecto
        $arrErrores = $this->validarBorrarProyecto($seqProyecto);

        // si no hay errores entra a eliminar
        if (empty($arrErrores)) {

            $sql = "
                    DELETE
                    FROM T_COR_PROYECTO
                    WHERE seqProyecto = $seqProyecto
                ";

            // borra la Proyecto
            try {
                $aptBd->execute($sql);
            } catch (Exception $objError) {
                $arrProyecto = $this->cargarProyecto($seqProyecto);
                $arrErrores[] = "No se ha podido borrar la Proyecto <b>" . $arrProyecto[$seqProyecto]->txtProyecto . "</b>";
                //pr( $objError->getMessage() );
            }
        }

        return $arrErrores;
    }

// Fin borrar Proyecto

    /**
     * VERIFICA SI LA Proyecto TIENE GRUPOS
     * ASOCIADOS Y SI ES ASI LA Proyecto NO
     * SE PUEDE ELIMINAR DEL SISTEMA
     * @author Bernardo Zerda
     * @param integer seqProyecto
     * @return array arrErrores
     * @version 1.0 Marzo 2009
     */
    private function validarBorrarProyecto($seqProyecto) {

        global $aptBd;
        $arrErrores = array();

        // obtiene los datos de la emrpesa para efectos de mensajes al usuario
        $arrProyecto = $this->cargarProyecto($seqProyecto);

        // consulta para ver si hay grupos asociados
        $sql = "
                SELECT seqProyectoGrupo
                FROM T_COR_PROYECTO_GRUPO
                WHERE seqProyecto = $seqProyecto
            ";

        try {
            $objRes = $aptBd->execute($sql);
            if ($objRes->RecordCount() > 0) {
                $arrErrores[] = "No se puede eliminar la Proyecto <b>" . $arrProyecto[$seqProyecto]->txtProyecto . "</b> porque tiene grupos de usuarios asociados";
            }
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido consultar si la Proyecto <b>" . $arrProyecto[$seqProyecto]->txtProyecto . "</b> tiene grupos asociados, reporte este error al administrador del sistema";
            //pr( $objError->getMessage() );
        }

        return $arrErrores;
    }

    public function obtenerDatosProyecto($seqProyecto) {

        global $aptBd;

        $sql = "SELECT pry.*, pol.*, fid.*, pry.seqProyecto as seqProyecto "
                . "FROM  t_pry_proyecto pry "
                . "LEFT JOIN t_pry_poliza pol on(pry.seqProyecto = pol.seqProyecto) "
                . "LEFT JOIN t_pry_datos_fiducia fid on(pry.seqProyecto = fid.seqProyecto)";
        if ($seqProyecto > 0) {
            $sql .= " where  pry.seqProyecto = " . $seqProyecto;
        }
        $sql . " ORDER BY  pry.seqProyecto";
        $objRes = $aptBd->getAssoc($sql);

        return $objRes;
    }

    public function obtenerEntidades($seqProyecto) {
        global $aptBd;
        $sql = "SELECT * FROM `t_pry_proyecto_entidad` pre "
                . "LEFT JOIN T_PRY_ENTIDAD_DISTRITAL ent USING(seqEntidadDistrital) "
                . "WHERE pre.seqProyecto = 205";
        $objRes = $aptBd->getAssoc($sql);
        return $objRes;
    }

    public function almacenarProyecto($post) {

        global $aptBd;
        $txtNombreInterventor = '';
        $txtDireccionInterventor = '';
        $txtCorreoInterventor = '';
        $bolTipoPersonaInterventor = 0;
        $numCedulaInterventor = 0;
        $numTProfesionalInterventor = 0;
        $numNitInterventor = 0;
        $txtNombreRepLegalInterventor = '';
        $numTelefonoRepLegalInterventor = 0;
        $txtDireccionRepLegalInterventor = '';
        $txtCorreoRepLegalInterventor = '';

        foreach ($post as $nombre_campo => $valor) {

            if ($valor == "" || $valor == " " || $valor == null || $valor == '0000-00-00') {
                $valor = (count(explode('txt', $nombre_campo)) > 1) ? NULL : 'NULL';
                $valor = (count(explode('fch', $nombre_campo)) > 1) ? NULL : 'NULL';
                $valor = (count(explode('numTel', $nombre_campo)) > 1) ? 0 : 0;
                if (count(explode('seq', $nombre_campo)) > 1) {
                    $valor = 0;
                }
            }
            if ($valor == 'NaN') {
                $valor = 0;
            }
            //echo " 2. " . $asignacion = "\$" . $nombre_campo . "='" . $valor . "';<br><br>";
            $$nombre_campo = $valor;
        }
        $numNitProyecto = str_replace(".", "", $numNitProyecto);
        $numNitProyecto = str_replace(" ", "", $numNitProyecto);
        $arrErrores = array();
        // Instruccion para insertar el Oferente en la base de datos
        $sql = "INSERT INTO t_pry_proyecto
                (   seqPryEstadoProceso,
                    txtNombreProyecto,
                    numNitProyecto,
                    txtNombrePlanParcial,
                    txtNombreComercial,
                    seqPlanGobierno,
                    seqPryTipoModalidad,
                    seqOpv,
                    txtNombreOperador,
                    txtObjetoProyecto,
                    seqTipoProyecto,
                    seqTipoUrbanizacion,
                    seqTipoSolucion,
                    txtDescripcionProyecto,
                    seqLocalidad,
                    seqBarrio,
                    txtOtrosBarrios,
                    txtDireccion,
                    valNumeroSoluciones, 
                    numCantSolDisc,
                    numParqueaderos,
                    numParqueaderosDisc,
                    valTorres,
                    valAreaLote,
                    valAreaConstruida,
                    txtChipLote,
                    txtMatriculaInmobiliariaLote,
                    txtRegistroEnajenacion,
                    fchRegistroEnajenacion,
                    txtDescEquipamientoComunal,
                    seqTutorProyecto,
                    seqConstructor,
                    txtNombreInterventor,
                    txtDireccionInterventor,
                    txtCorreoInterventor,
                    bolTipoPersonaInterventor,
                    numCedulaInterventor,
                    numTProfesionalInterventor,
                    numNitInterventor,
                    txtNombreRepLegalInterventor,
                    numTelefonoRepLegalInterventor,
                    txtDireccionRepLegalInterventor,
                    txtCorreoRepLegalInterventor,
                    valCostosDirectos,
                    valCostosIndirectos,
                    valTerreno,
                    valGastosFinancieros,
                    valGastosVentas,
                    valTotalCostos,
                    valTotalVentas,
                    valUtilidadProyecto,
                    valRecursosPropios,
                    valCreditoEntidadFinanciera,
                    valCreditoParticulares,
                    valVentasProyecto,
                    valSDVE,
                    valOtros,
                    valDevolucionIVA,
                    valTotalRecursos,                   
                    txtNombreVendedor,
                    numTelVendedor,
                    numNitVendedor,
                    txtCorreoVendedor,
                    txtCedulaCatastral,
                    txtEscritura,
                    fchEscritura,
                    numNotaria,
                    seqProyectoGrupo,
                    numRadicadoJuridico,
                    fchRadicadoJuridico,
                    numRadicadoTecnico,
                    fchRadicadoTecnico,
                    numRadicadoFinanciero,
                    fchRadicadoFinanciero,
                    bolActivo,
                    seqUsuario)
                    VALUES (
                    $seqPryEstadoProceso,
                    '$txtNombreProyecto',
                    $numNitProyecto,
                    '$txtNombrePlanParcial',
                    '$txtNombreComercial',
                    $seqPlanGobierno,
                    $seqPryTipoModalidad,
                    $seqOpv,
                    '$txtNombreOperador',
                    '$txtObjetoProyecto',
                    $seqTipoProyecto,
                    $seqTipoUrbanizacion,
                    $seqTipoSolucion,
                    '$txtDescripcionProyecto',
                    $seqLocalidad,
                    $seqBarrio,
                    '$txtOtrosBarrios',                   
                    '$txtDireccion',
                    $valNumeroSoluciones,  
                    $numCantSolDisc,
                    $numParqueaderos,
                    $numParqueaderosDisc,
                    $valTorres,
                    $valAreaLote,
                    $valAreaConstruida,
                    '$txtChipLote',
                    '$txtMatriculaInmobiliariaLote',
                    '$txtRegistroEnajenacion',
                    '$fchRegistroEnajenacion',                    
                    '$txtDescEquipamientoComunal',
                    $seqTutorProyecto,
                    $seqConstructor,
                    '$txtNombreInterventor',
                    '$txtDireccionInterventor' ,                       
                    '$txtCorreoInterventor',
                    $bolTipoPersonaInterventor,
                    $numCedulaInterventor,
                    '$numTProfesionalInterventor',
                    $numNitInterventor,
                    '$txtNombreRepLegalInterventor',
                    $numTelefonoRepLegalInterventor,
                    '$txtDireccionRepLegalInterventor',
                    '$txtCorreoRepLegalInterventor',
                    $valCostosDirectos,
                    $valCostosIndirectos,
                    $valTerreno,
                    $valGastosFinancieros,
                    $valGastosVentas,
                    $valTotalCostos,
                    $valTotalVentas,
                    $valUtilidadProyecto,
                    $valRecursosPropios,
                    $valCreditoEntidadFinanciera,
                    $valCreditoParticulares,
                    $valVentasProyecto,
                    $valSDVE,
                    $valOtros,
                    $valDevolucionIVA,
                    $valTotalRecursos,                    
                    '$txtNombreVendedor',
                    $numTelVendedor,
                    '$numNitVendedor',
                    '$txtCorreoVendedor',
                    '$txtCedulaCatastral',
                    '$txtEscritura',
                    '$fchEscritura',
                    $numNotaria,
                    $seqProyectoGrupo,
                    '$numRadicadoJuridico',
                    '$fchRadicadoJuridico',
                    '$numRadicadoTecnico',
                    '$fchRadicadoTecnico',
                    '$numRadicadoFinanciero',
                    '$fchRadicadoFinanciero',
                     1,
                    $seqUsuario
                    ) ";
        try {
            // echo "<br>" . $sql;
            //   die();
            $aptBd->execute($sql);
            $seqProyecto = $aptBd->Insert_ID();
            // 
            $band = false;
            $sqlInsOfe = "INSERT INTO t_pry_proyecto_oferente
                        (
                        seqProyecto,
                        seqOferente,
                        txtNombreContactoOferente,
                        txtCorreoOferente,
                        numTelContactoOferente,
                        bolTipoOferente)
                        VALUES";
            foreach ($seqOferente as $key => $value) {
                if ($seqProyectoOferente[$key] == 0 || $seqProyectoOferente[$key] == '' || $seqProyectoOferente[$key] == '0') {
                    $sqlInsOfe .= "(" . $seqProyecto . ", " . $value . " , '" . $txtNombreContactoOferente[$key] . "', '" . $txtCorreoOferente[$key] . "'," . $numTelContactoOferente[$key] . " , 1),";
                    $band = true;
                }
            }
            try {
                if ($band) {
                    $sqlInsOfe = substr_replace($sqlInsOfe, ';', -1, 1);
                    $aptBd->execute($sqlInsOfe);
                }
            } catch (Exception $ex) {
                pr($ex->getMessage());
            }
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido guardar el Proyecto <b>$txtNombreProyecto</b>. Reporte este error al administrador del sistema";
            pr($objError->getMessage());
        }

        return $seqProyecto;
    }

    public function obtenerDatosProyectos($seqProyecto) {

        global $aptBd;
        $sql = "SELECT pry.*, pol.*, fid.*, pry.seqProyecto as seqProyecto "
                . "FROM  t_pry_proyecto pry "
                . "LEFT JOIN t_pry_poliza pol on(pry.seqProyecto = pol.seqProyecto) "
                . "LEFT JOIN t_pry_datos_fiducia fid on(pry.seqProyecto = fid.seqProyecto)";
        if ($seqProyecto > 0) {
            $sql .= " where  pry.seqProyecto = " . $seqProyecto;
        }
        $sql . " ORDER BY  pry.seqProyecto";

        $objRes = $aptBd->execute($sql);
        $datos = Array();
        while ($objRes->fields) {
            $datos[] = $objRes->fields;
            $objRes->MoveNext();
        }
        return $datos;
    }

    public function obtenerDatosProyectosFicha($seqProyecto) {

        global $aptBd;
        $sql = "SELECT pry.*, pol.*, fid.*, con.*, loc.*, gru.*, pry.seqProyecto as seqProyecto, txtTipoProyecto, txtTipoFinanciacion, "
                . " ofProy.nombreOferente AS oferente "
                . "FROM  t_pry_proyecto pry "
                . "LEFT JOIN t_pry_poliza pol on(pry.seqProyecto = pol.seqProyecto) "
                . "LEFT JOIN t_pry_datos_fiducia fid on(pry.seqProyecto = fid.seqProyecto) "
                . "LEFT JOIN t_pry_constructor con USING(seqConstructor) "
                . "LEFT JOIN t_frm_localidad loc USING(seqLocalidad) "
                . "LEFT JOIN t_pry_proyecto_grupo gru USING(seqProyectoGrupo) "
                . "LEFT JOIN T_PRY_TIPO_PROYECTO USING(seqTipoProyecto)"
                . "LEFT JOIN T_FRM_TIPO_FINANCIACION USING(seqTipoFinanciacion)"
                . " LEFT JOIN 
     (SELECT 
            GROUP_CONCAT(txtNombreOferente
                    SEPARATOR ', ') as 'nombreOferente', pOf.seqProyecto
        FROM
            t_pry_proyecto_oferente pOf
                LEFT JOIN
            t_pry_entidad_oferente entO USING (seqOferente)
        ) ofProy on(pry.seqProyecto = ofProy.seqProyecto)";
        if ($seqProyecto > 0) {
            $sql .= " where  pry.seqProyecto = " . $seqProyecto;
        }
        $sql . " ORDER BY  pry.seqProyecto";
        // echo "<p>" . $sql . "</p>";
        $objRes = $aptBd->execute($sql);
        $datos = Array();
        while ($objRes->fields) {
            $datos[] = $objRes->fields;
            $objRes->MoveNext();
        }
        return $datos;
    }

    public function datosOferenteProyecto($seqProyecto) {
        global $aptBd;
        $sql = "SELECT group_concat(txtNombreOferente separator ' ') as oferente FROM  t_pry_proyecto_oferente
                LEFT JOIN t_pry_entidad_oferente using(seqOferente)  ";
        if ($seqProyecto > 0) {
            $sql .= " where  seqProyecto = " . $seqProyecto;
        }
        $sql .=" Group by seqProyecto ORDER BY txtNombreOferente ASC";
        //  echo "<p>" . $sql . "</p>";
        $objRes = $aptBd->execute($sql);
        $datos = "";
        while ($objRes->fields) {
            $datos = $objRes->fields['oferente'];
            $objRes->MoveNext();
        }
        return $datos;
    }

    public function obtenerDatosviviendaFicha($seqProyecto) {

        global $aptBd;
        $sql = "SELECT case  when sum(numCantParqDisc)  > 0 then sum(numCantParqDisc) else numParqueaderosDisc end as totalParqDisc, 
		case  when sum(numCantUdsDisc)  > 0  then sum(numCantUdsDisc) else numCantSolDisc end as totalUdsDisc,
		case  when sum(numTotalParq) > 0  then sum(numTotalParq) else numParqueaderos end as totalParq, valNumeroSoluciones as totalUnidades 
		FROM t_pry_proyecto pry 
                left join t_pry_tipo_vivienda ptv using(seqProyecto)";
        if ($seqProyecto > 0) {
            $sql .= " where  pry.seqProyecto = " . $seqProyecto;
        }

        //echo "<p>" . $sql . "</p>";
        $objRes = $aptBd->execute($sql);
        $datos = Array();
        while ($objRes->fields) {
            $datos[] = $objRes->fields;
            $objRes->MoveNext();
        }
        // var_dump($datos);
        return $datos;
    }

    public function datosAvanceObraFicha($seqProyecto) {
        global $aptBd;
        $sql = "SELECT  fchFinalTerreno as fecha FROM t_pry_cronograma_obras ";
        $sql = "SELECT concat(numPorcentajeEjecucion,'**', "
                . "(SELECT fchFinalTerreno as fecha FROM t_pry_cronograma_obras where seqProyecto = $seqProyecto ORDER BY fchFinalTerreno DESC LIMIT 1),'**',valEjecutado ) as datosAvance FROM t_pry_informe_interventoria ";
        if ($seqProyecto > 0) {
            $sql .= " where  seqProyecto = " . $seqProyecto;
        }
        $sql . " ORDER BY fchInformeInterventoria DESC LIMIT 1";
        //  echo "<p>" . $sql . "</p>";
        $objRes = $aptBd->execute($sql);
        $datos = "";
        while ($objRes->fields) {
            $datos = $objRes->fields['datosAvance'];
            $objRes->MoveNext();
        }
        return $datos;
    }

    /**
     * MODIFICA LA INFORMACION DE LA Proyecto
     * SELECCIONADA Y GUARDA LOS NUEVOS DATOS
     * @author Bernardo Zerda
     * @param integer seqProyecto
     * @param String txtNombre
     * @param Date fchVencimiento
     * @param Boolean bolActivo
     * @return Array arrErrores
     * @version 0,1 Marzo 2009
     */
    public function editarProyecto($seqProyecto, $txtNombre, $fchVencimiento, $bolActivo, $seqMenu) {

        global $aptBd;
        $arrErrores = array();

        // Consulta para hacer la actualizacion
        $sql = "
                UPDATE T_COR_PROYECTO SET
                    txtProyecto = \"" . ereg_replace('\"', "", $txtNombre) . "\", 
                    bolActivo = $bolActivo,
                    fchVencimiento = '$fchVencimiento',
					seqMenu = $seqMenu
                WHERE seqProyecto = $seqProyecto
            ";

        try {
            $aptBd->execute($sql);
        } catch (Exception $objError) {
            $arrProyecto = $this->cargarProyecto($seqProyecto);
            $arrErrores[] = "No se ha podido editar la Proyecto <b>" . $arrProyecto[$seqProyecto]->txtProyecto . "</b> reporte este error al administrador del sistema";
        }

        return $arrErrores;
    }

    public function editarProyectoPRY($post) {
        global $aptBd;

        $txtNombreInterventor = '';
        $txtDireccionInterventor = '';
        $txtCorreoInterventor = '';
        $bolTipoPersonaInterventor = 0;
        $numCedulaInterventor = 0;
        $numTProfesionalInterventor = '';
        $numNitInterventor = 'NULL';
        $txtNombreRepLegalInterventor = '';
        $numTelefonoRepLegalInterventor = 'NULL';
        $txtDireccionRepLegalInterventor = '';
        $txtCorreoRepLegalInterventor = '';

        foreach ($post as $nombre_campo => $valor) {

            if ($valor == "" || $valor == " " || $valor == null || $valor == '0000-00-00') {
                $valor = (count(explode('txt', $nombre_campo)) > 1) ? NULL : 'NULL';
                $valor = (count(explode('fch', $nombre_campo)) > 1) ? NULL : NULL;

                if (count(explode('seq', $nombre_campo)) > 1 || count(explode('num', $nombre_campo)) > 1 || count(explode('val', $nombre_campo)) > 1) {
                    // echo "<br> $" . $nombre_campo . "=> " . $valor;
                    $valor = 0;
                }
            }
            if ($valor == 'NaN') {
                $valor = 0;
            }

            $$nombre_campo = $valor;
        }
        $arrErrores = array();

        // Consulta para hacer la actualizacion
        $sql = "
                UPDATE t_pry_proyecto
                    SET                    
                        txtNombreProyecto = \"" . ereg_replace('\"', "", $txtNombreProyecto) . "\", 
                        numNitProyecto = " . doubleval($numNitProyecto) . ",
                        txtNombrePlanParcial = '" . $txtNombrePlanParcial . "',
                        txtNombreComercial = '" . $txtNombreComercial . "',  
                        seqPlanGobierno = " . $seqPlanGobierno . ",
                        seqPryTipoModalidad = " . $seqPryTipoModalidad . ",
                        seqOpv = " . $seqOpv . ",
                        txtNombreOperador = '" . $txtNombreOperador . "',
                        txtObjetoProyecto  = '" . $txtObjetoProyecto . "',
                        seqTipoProyecto = " . $seqTipoProyecto . ",
                        seqTipoUrbanizacion = " . $seqTipoUrbanizacion . ",
                        seqTipoSolucion = " . $seqTipoSolucion . ",
                        txtDescripcionProyecto = '" . $txtDescripcionProyecto . "',
                        seqLocalidad = " . $seqLocalidad . ",
                        seqBarrio = " . $seqBarrio . ",
                        txtOtrosBarrios  = '" . $txtOtrosBarrios . "',                       
                        txtDireccion  = '" . $txtDireccion . "',
                        valNumeroSoluciones = " . $valNumeroSoluciones . ", 
                        numCantSolDisc = " . $numCantSolDisc . ",
                        numParqueaderos = " . $numParqueaderos . ",
                        numParqueaderosDisc = " . $numParqueaderosDisc . ",
                        valTorres = " . $valTorres . ",
                        valAreaLote = " . $valAreaLote . ",
                        valAreaConstruida = " . $valAreaConstruida . ",
                        txtChipLote  = '" . $txtChipLote . "',
                        txtMatriculaInmobiliariaLote  = '" . $txtMatriculaInmobiliariaLote . "',
                        txtRegistroEnajenacion  = '" . $txtRegistroEnajenacion . "',
                        fchRegistroEnajenacion  = '" . $fchRegistroEnajenacion . "',
                        txtDescEquipamientoComunal  = '" . $txtDescEquipamientoComunal . "',
                        seqTutorProyecto = " . $seqTutorProyecto . " ,
                        seqConstructor = " . $seqConstructor . ",
                        txtNombreInterventor = '" . $txtNombreInterventor . "',                                
                        txtDireccionInterventor = '" . $txtDireccionInterventor . "' ,   
                        txtCorreoInterventor = 	'" . $txtCorreoInterventor . "' , 
                        bolTipoPersonaInterventor = " . $bolTipoPersonaInterventor . ",
                        numCedulaInterventor = 	" . $numCedulaInterventor . ",
                        numTProfesionalInterventor ='" . $numTProfesionalInterventor . "',
                        numNitInterventor =" . $numNitInterventor . ",
                        txtNombreRepLegalInterventor = '" . $txtNombreRepLegalInterventor . "' , 
                        numTelefonoRepLegalInterventor =" . $numTelefonoRepLegalInterventor . ",
                        txtDireccionRepLegalInterventor = '" . $txtDireccionRepLegalInterventor . "' , 
                        txtCorreoRepLegalInterventor = '" . $txtCorreoRepLegalInterventor . "', 
                        valCostosDirectos = " . $valCostosDirectos . ",
                        valCostosIndirectos = " . $valCostosIndirectos . ",
                        valTerreno = " . $valTerreno . ",
                        valGastosFinancieros = " . $valGastosFinancieros . ",
                        valGastosVentas = " . $valGastosVentas . ",
                        valTotalCostos = " . $valTotalCostos . ",
                        valTotalVentas = " . $valTotalVentas . ",
                        valUtilidadProyecto = " . $valUtilidadProyecto . ",
                        valRecursosPropios = " . $valRecursosPropios . ",
                        valCreditoEntidadFinanciera = " . $valCreditoEntidadFinanciera . ",
                        valCreditoParticulares = " . $valCreditoParticulares . ",
                        valVentasProyecto = " . $valVentasProyecto . ",
                        valSDVE = " . $valSDVE . ",
                        valOtros = " . $valOtros . ",
                        valDevolucionIVA = " . $valDevolucionIVA . ",
                        valTotalRecursos = " . $valTotalRecursos . ",                      
                        txtNombreVendedor = '$txtNombreVendedor',
                        numTelVendedor = $numTelVendedor,
                        numNitVendedor = '$numNitVendedor',
                        txtCorreoVendedor = '$txtCorreoVendedor',
                        txtCedulaCatastral = '$txtCedulaCatastral',
                        txtEscritura = '$txtEscritura',
                        fchEscritura = '$fchEscritura',
                        numNotaria = $numNotaria,
                        seqProyectoGrupo = $seqProyectoGrupo,
                        numRadicadoJuridico = '$numRadicadoJuridico',
                        fchRadicadoJuridico = '$fchRadicadoJuridico',
                        numRadicadoTecnico = '$numRadicadoTecnico',
                        fchRadicadoTecnico = '$fchRadicadoTecnico',
                        numRadicadoFinanciero = '$numRadicadoFinanciero',
                        fchRadicadoFinanciero = '$fchRadicadoFinanciero'
                        WHERE seqProyecto = $seqProyecto
            ";
        //  echo $sql;
        // die();

        try {
            $band = false;
            $aptBd->execute($sql);
            $sqlOf = "select * from t_pry_proyecto_oferente where seqProyecto = " . $seqProyecto;
            $rs = $aptBd->execute($sqlOf);
            $rows = $rs->RecordCount();
            $sqlInsOfe = "INSERT INTO t_pry_proyecto_oferente
                        (
                        seqProyecto,
                        seqOferente,
                        txtNombreContactoOferente,
                        txtCorreoOferente,
                        numTelContactoOferente,
                        bolTipoOferente)
                        VALUES";
            $arrayOferentes = count($seqOferente);
            $cont = $rows - $arrayOferentes;
            $arraDelete = "";

            foreach ($seqOferente as $key => $value) {
                if ($seqProyectoOferente[$key] == 0 || $seqProyectoOferente[$key] == '' || $seqProyectoOferente[$key] == '0') {
                    $sqlInsOfe .= "(" . $seqProyecto . ", " . $value . ", '" . $txtNombreContactoOferente[$key] . "','" . $txtCorreoOferente[$key] . "','" . $numTelContactoOferente[$key] . "', 1),";
                    $band = true;
                } else {
                    if ($seqProyectoOferente[$key] != "") {
                        $sqlOfUpd = "UPDATE t_pry_proyecto_oferente SET
                                seqProyecto = $seqProyecto,
                                seqOferente = $value,
                                txtNombreContactoOferente = '$txtNombreContactoOferente[$key]',
                                txtCorreoOferente = '$txtCorreoOferente[$key]',
                                numTelContactoOferente = $numTelContactoOferente[$key],
                                bolTipoOferente = 1
                                WHERE seqProyectoOferente = $seqProyectoOferente[$key];";
                        $aptBd->execute($sqlOfUpd);
                        $arraDelete .= $seqProyectoOferente[$key] . ",";
                    }
                }
            }
            if ($cont > 0) {
                $arraDelete = substr_replace($arraDelete, '', -1, 1);
                $sqlDel = "DELETE FROM t_pry_proyecto_oferente WHERE seqProyecto = " . $seqProyecto . " AND seqProyectoOferente NOT IN (" . $arraDelete . ");";
                $aptBd->execute($sqlDel);
            }
            if ($band) {
                $sqlInsOfe = substr_replace($sqlInsOfe, ';', -1, 1);
                $aptBd->execute($sqlInsOfe);
            }
        } catch (Exception $objError) {
            pr($objError->getMessage());
            //$arrOferente = $this->cargarOferente($seqOferente);
            $arrErrores[] = "No se ha podido editar el Oferente <b>" . $arrOferente[$seqOferente]->txtNombreOferente . "</b>. Reporte este error al administrador del sistema";
        }

        return $arrErrores;
    }

    public function obtenerListaDocumentos($seqProyecto, $cant) {
        global $aptBd;

        $sql = "select * from t_pry_documentos";

        if ($seqProyecto > 0 && $cant > 0) {
            $sql .= " left join t_pry_proyecto_documentos using(seqDocumento) where seqProyecto = " . $seqProyecto;
        }
        $sql . " ORDER BY seqDocumento";

        $objRes = $aptBd->execute($sql);

        $datos = Array();
        while ($objRes->fields) {
            $datos[] = $objRes->fields;
            $objRes->MoveNext();
        }
        return $datos;
    }

    public function almacenarDocumentos($seqProyecto, $arraydoc, $arraDocAdd) {

        global $aptBd;

        $arrErrores = array();
        $sql = "INSERT INTO t_pry_proyecto_documentos
                (txtNombreArchivo, bolEstado, seqProyecto, seqDocumento) VALUES";
        foreach ($arraydoc as $key => $value) {
            $val = 0;
            if ($arraDocAdd[$key] != "") {
                $val = 1;
            }
            $sql .= "  ('NULL', " . $val . ", " . $seqProyecto . ", " . $value . "),";
        }
        $sql = substr_replace($sql, ';', -1, 1);

        try {
            $aptBd->execute($sql);
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido cargar los archivos<b></b>";
            pr($objError->getMessage());
        }
        return $arrErrores;
    }

    public function modificarDocumentos($seqProyecto, $arraydoc, $arraDocAdd, $cant) {
        global $aptBd;

        $arrErrores = array();
        $documentos = $this->obtenerListaDocumentos($seqProyecto, $cant);
        $sqlUp = "UPDATE t_pry_proyecto_documentos SET bolEstado = CASE";
        $seqDocs;
        foreach ($documentos as $key => $value) {
            $val = 0;
            if ($value['seqDocumento'] == $arraydoc[$key + 1]) {
                if ($arraDocAdd[$key + 1] != "") {
                    $val = 1;
                }
                $seqDocs .= $value['seqDocumento'] . ",";
                $sqlUp .= " WHEN seqDocumento = " . $value['seqDocumento'] . " THEN " . $val;
            }
            //  echo "<br>*** valor " . $value['seqDocumento'] . " key -> " . $key . " valueDoc -> " . $arraydoc[$key + 1] . " arraDocAdd -> " . $arraDocAdd[$key + 1];
        }
        $seqDocs = substr_replace($seqDocs, '', -1, 1);
        $sqlUp .= " ELSE bolEstado END WHERE seqDocumento IN (" . $seqDocs . ")";

        try {
            $aptBd->execute($sqlUp);
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido cargar los archivos<b></b>";
            pr($objError->getMessage());
        }
        return $arrErrores;
    }

    public function almacenarLicencias($seqProyecto, $array, $cant) {
        global $aptBd;
        $arrErrores = array();
        $sql = "INSERT INTO t_pry_proyecto_licencias (txtLicencia, txtExpideLicencia, fchLicencia, fchVigenciaLicencia, fchEjecutoriaLicencia, txtResEjecutoria, seqTipoLicencia,  fchLicenciaProrroga, fchLicenciaProrroga1, fchLicenciaProrroga2, seqProyecto) VALUES";

        for ($index = 0; $index < $cant; $index++) {
            foreach ($array[$seqProyecto] as $key => $value) {
                if ($value[($index)] == NULL && $value[($index)] == "") {
                    $$key = 0;
                    $$key = (count(explode('fch', $key)) > 1) ? NULL : 'NULL';
                } else {
                    $$key = $value[($index)];
                }
            }
            $sql .= "(
                        '$txtLicencia', 
                        '$txtExpideLicencia',
                        '$fchLicencia',
                        '$fchVigenciaLicencia',
                        '$fchEjecutoriaLicencia',
                        '$txtResEjecutoria',
                        '$seqTipoLicencia',
                        '$fchLicenciaProrroga',
                        '$fchLicenciaProrroga1',
                        '$fchLicenciaProrroga2',
                        " . $seqProyecto . "),";
        }
        $sql = substr_replace($sql, ';', -1, 1);
        //$sql .= $sqlLicencia;
        // echo "<p> Almacenar" . $sql . "</p>";
        try {
            $aptBd->execute($sql);
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido cargar las Licencias<b></b>";
            pr($objError->getMessage());
        }
        return $arrErrores;
    }

    public function obtenerListaLicencias($seqProyecto) {
        global $aptBd;
        $sql = "select * from t_pry_proyecto_licencias where seqProyecto = " . $seqProyecto . " group by seqTipoLicencia ORDER BY seqTipoLicencia ";
        //echo "<p>".$sql."</p>";
        $objRes = $aptBd->execute($sql);

        $datos = Array();
        $datos[0] = 0;
        $datos[1] = 0;
        while ($objRes->fields) {
            if ($objRes->fields['seqTipoLicencia'] == 1) {
                $datos[0] = $objRes->fields;
            } else {
                $datos[1] = $objRes->fields;
            }

            $objRes->MoveNext();
        }

        //  pr($datos);
        return $datos;
    }

    public function obtenerListaLicenciasPadre($seqProyecto) {
        global $aptBd;
        $sql = "select lic.* from t_pry_proyecto_licencias lic left join t_pry_proyecto Using(seqProyecto) where seqProyecto = " . $seqProyecto . " ORDER BY seqTipoLicencia";
        $objRes = $aptBd->execute($sql);

        $datos = Array();
        while ($objRes->fields) {
            $datos[] = $objRes->fields;
            $objRes->MoveNext();
        }
        return $datos;
    }

    public function modificarLicencias($seqProyecto, $array, $cant) {

        global $aptBd;
        $arrErrores = array();
        $sqlExistentes = "SELECT seqProyectoLicencia FROM t_pry_proyecto_licencias WHERE seqProyecto = $seqProyecto";
        $exeExistentes = $aptBd->execute($sqlExistentes);

        //$cant = $exeExistentes->numRows();

        $datos = Array();
        $datosDiff = Array();
        while ($exeExistentes->fields) {
            $datos[] = $exeExistentes->fields['seqProyectoLicencia'];
            $exeExistentes->MoveNext();
        }
        for ($index = 0; $index <= $cant; $index++) {
            $txtNombreProyectoHijo = '';
            foreach ($array[$seqProyecto] as $key => $value) {
                if ($value[($index)] == NULL && $value[($index)] == "") {
                    $$key = 0;
                    $$key = (count(explode('fch', $value[($index)])) > 1) ? NULL : 'NULL';
                } else {
                    $$key = $value[($index)];
                }
            }
            $datosDiff[] = $seqProyectoLicencia;

            if (in_array($seqProyectoLicencia, $datos)) {
                $query = 'UPDATE t_pry_proyecto_licencias
                            SET            
                            txtLicencia = "' . $txtLicencia . '",
                            txtExpideLicencia = "' . $txtExpideLicencia . '",
                            fchLicencia ="' . $fchLicencia . '",
                            fchVigenciaLicencia = "' . $fchVigenciaLicencia . '",
                            fchLicenciaProrroga = "' . $fchLicenciaProrroga . '",
                            fchLicenciaProrroga1 = "' . $fchLicenciaProrroga1 . '",
                            fchLicenciaProrroga2 = "' . $fchLicenciaProrroga2 . '",
                            fchEjecutoriaLicencia ="' . $fchEjecutoriaLicencia . '",
                            txtResEjecutoria = "' . $txtResEjecutoria . '"  
                            WHERE seqProyectoLicencia = ' . $seqProyectoLicencia . ' '
                        . 'and seqProyecto = "' . $seqProyecto . '" '
                        . 'and seqTipoLicencia = "' . $seqTipoLicencia . '";';
                try {
                    //echo "<p>".$query ."</p>";
                    $aptBd->execute($query);
                } catch (Exception $objError) {
                    $arrErrores[] = "No se ha podido cargar las licencias<b></b>";
                    pr($objError->getMessage());
                }
            } else if ($cant != $exeExistentes->numRows()) {

                $arrayInsLicencias = Array();
                $arrayInsLicencias[$seqProyecto]['txtLicencia'][] = $txtLicencia;
                $arrayInsLicencias[$seqProyecto]['txtExpideLicencia'][] = $txtExpideLicencia;
                $arrayInsLicencias[$seqProyecto]['fchLicencia'][] = $fchLicencia;
                $arrayInsLicencias[$seqProyecto]['fchVigenciaLicencia'][] = $fchVigenciaLicencia;
                $arrayInsLicencias[$seqProyecto]['fchLicenciaProrroga'][] = $fchLicenciaProrroga;
                $arrayInsLicencias[$seqProyecto]['fchLicenciaProrroga1'][] = $fchLicenciaProrroga1;
                $arrayInsLicencias[$seqProyecto]['fchLicenciaProrroga2'][] = $fchLicenciaProrroga2;
                $arrayInsLicencias[$seqProyecto]['fchEjecutoriaLicencia'][] = $fchEjecutoriaLicencia;
                $arrayInsLicencias[$seqProyecto]['txtResEjecutoria'][] = $txtResEjecutoria;
                $arrayInsLicencias[$seqProyecto]['seqTipoLicencia'][] = $seqTipoLicencia;
                $this->almacenarLicencias($seqProyecto, $arrayInsLicencias, count($arrayInsLicencias));
            }
            //echo "<br><br> update" . $query;
        }

        return $arrErrores;
    }

    function almacenarConjuntos($seqProyecto, $arrayConjuntos, $cant) {

        global $aptBd;
        $arrErrores = array();

        $query = "INSERT INTO T_PRY_PROYECTO (
                txtNombreProyecto,
                txtNombreComercial,
                seqLocalidad,
                seqBarrio,
                seqProyectoPadre,
                txtDireccion,
                valNumeroSoluciones,
                txtMatriculaInmobiliariaLote,
                txtChipLote,               
                txtNombreVendedor,
                seqTipoDocumentoVendedor,
                numNitVendedor,
                numTelVendedor,
                txtCorreoVendedor,
                txtCedulaCatastral,
                txtEscritura,
                fchEscritura,
                numNotaria,
                seqTutorProyecto,
                seqPryEstadoProceso,
                fchInscripcion,
                fchUltimaActualizacion,
                valAreaLote,
                valTorres,
                seqUsuario) 
            VALUES";
        for ($index = 0; $index < $cant; $index++) {
            $txtNombreProyectoHijo = '';
            foreach ($arrayConjuntos[$seqProyecto] as $key => $value) {
                if ($value[($index)] == NULL && $value[($index)] == "") {
                    //$$key = 0;
                    $$key = (count(explode('txt', $key)) > 1) ? NULL : 'NULL';
                    $$key = (count(explode('fch', $key)) > 1) ? NULL : 'NULL';
                } else {
                    $$key = $value[($index)];
                }
            }
            //  echo "<br> $".$key ." => ".$value[($index)];
            //if ($txtNombreProyectoHijo != "") {
            $query .= "(
                        '$txtNombreProyectoHijo', 
                        '$txtNombreComercialHijo',
                        '$seqLocalidadHijo',
                        '$seqBarrioHijo',
                        '$seqProyecto',
                        '$txtDireccionHijo',
                        '$valNumeroSolucionesHijo',
                        '$txtMatriculaInmobiliariaLoteHijo',
                        '$txtChipLoteHijo',                         
                        '$txtNombreVendedorHijo',
                         $seqTipoDocumentoVendedorHijo,
                        '$numNitVendedorHijo',
                        '$numTelVendedorHijo',
                        '$txtCorreoVendedorHijo',
                        '$txtCedulaCatastralHijo',
                        '$txtEscrituraHijo',
                        '$fchEscrituraHijo',
                        $numNotariaHijo,
                        '$seqTutorProyecto',
                        '2',
                        '$fchGestion',
                        '$fchGestion',
                        '$valAreaLoteHijo',
                        '$valTorresHijo',
                        " . $_SESSION['seqUsuario'] . ");";
            try {
                $aptBd->execute($query);
                $idProyecto = $aptBd->Insert_ID();
                $arrayInsLicencias = Array();
                $arrayInsLicencias[$idProyecto]['txtLicencia'][0] = $txtLicenciaUrbanismoHijo;
                $arrayInsLicencias[$idProyecto]['txtExpideLicencia'][0] = $txtExpideLicenciaUrbanismoHijo;
                $arrayInsLicencias[$idProyecto]['fchLicencia'][0] = $fchLicenciaUrbanismo1Hijo;
                $arrayInsLicencias[$idProyecto]['fchVigenciaLicencia'][0] = $fchVigenciaLicenciaUrbanismoHijo;
                $arrayInsLicencias[$idProyecto]['fchEjecutoriaLicencia'][0] = $fchEjecutoriaLicenciaUrbanismoHijo;
                $arrayInsLicencias[$idProyecto]['seqTipoLicencia'][0] = 1;
                $arrayInsLicencias[$idProyecto]['txtLicencia'][1] = $txtLicenciaConstruccionHijo;
                $arrayInsLicencias[$idProyecto]['fchLicencia'][1] = $fchLicenciaConstruccion1Hijo;
                $arrayInsLicencias[$idProyecto]['txtExpideLicencia'][1] = $txtExpideLicenciaConstruccionHijo;
                $arrayInsLicencias[$idProyecto]['fchVigenciaLicencia'][1] = $fchVigenciaLicenciaConstruccionHijo;
                $arrayInsLicencias[$idProyecto]['fchEjecutoriaLicencia'][1] = $fchEjecutoriaLicenciaConstruccionHijo;
                $arrayInsLicencias[$idProyecto]['seqTipoLicencia'][1] = 2;
                //var_dump($arrayInsLicencias);
                $this->almacenarLicencias($idProyecto, $arrayInsLicencias, 2);
            } catch (Exception $objError) {
                $arrErrores[] = "No se ha podido cargar las licencias<b></b>";
                pr($objError->getMessage());
            }
        }

        // echo "<br>" . $query . "<br>";
    }

    function modificarConjuntos($seqProyecto, $arrayConjuntos, $cant) {

        global $aptBd;
        $arrErrores = array();
        $sqlExistentes = "SELECT seqProyecto FROM T_PRY_PROYECTO WHERE seqProyectoPadre = $seqProyecto";
        $exeExistentes = $aptBd->execute($sqlExistentes);
        //$exeExistentes->numRows() . "->" . $cant;
        $datos = Array();
        $datosDiff = Array();
        while ($exeExistentes->fields) {
            $datos[] = $exeExistentes->fields['seqProyecto'];
            $exeExistentes->MoveNext();
        }
        for ($index = 0; $index < $cant; $index++) {
            $txtNombreProyectoHijo = '';
            //  pr($arrayConjuntos[$seqProyecto]);
            foreach ($arrayConjuntos[$seqProyecto] as $key => $value) {
                if ($value[($index)] == NULL || $value[($index)] == "" || $value[($index)] == "0000-00-00") {
                    $$key = 0;
                    $$key = (count(explode('txt', $key)) > 1) ? NULL : 'NULL';
                    $$key = (count(explode('fch', $key)) > 1) ? NULL : 'NULL';
                    //  echo "<br> ** key = " . $key . " =>" . $$key;
                } else {
                    $$key = (count(explode('fch', $key)) > 1) ? NULL : 'NULL';
                    //echo "<br> ** key = " . $key . " =>" . $$key;
                    $$key = $value[($index)];
                }
            }
            $datosDiff[] = $seqProyectoHijo;

            if (in_array($seqProyectoHijo, $datos)) {
                $query = "UPDATE T_PRY_PROYECTO 
                    SET txtNombreProyecto = '$txtNombreProyectoHijo',
                        txtNombreComercial = '$txtNombreComercialHijo',
                        seqLocalidad = '$seqLocalidadHijo',
                        seqBarrio = '$seqBarrioHijo',
                        seqProyectoPadre = '$seqProyecto',
                        txtDireccion = '$txtDireccionHijo',
                        valNumeroSoluciones = '$valNumeroSolucionesHijo',
                        txtChipLote = '$txtChipLoteHijo',
                        txtMatriculaInmobiliariaLote = '$txtMatriculaInmobiliariaLoteHijo',                        
                        txtNombreVendedor = '$txtNombreVendedorHijo',
                        seqTipoDocumentoVendedor = '$seqTipoDocumentoVendedorHijo',
                        numNitVendedor = '$numNitVendedorHijo',
                        numTelVendedor = '$numTelVendedorHijo',
                        txtCorreoVendedor = '$txtCorreoVendedorHijo',
                        txtCedulaCatastral = '$txtCedulaCatastralHijo',
                        txtEscritura = '$txtEscrituraHijo',
                        fchEscritura = '$fchEscrituraHijo',
                        numNotaria = '$numNotariaHijo',
                        seqTutorProyecto = '$seqTutorProyecto', 
                        fchUltimaActualizacion = '$fchGestion', 
                        valAreaLote = '$valAreaLoteHijo',
                        valTorres = '$valTorresHijo',
                        seqUsuario = " . $_SESSION['seqUsuario'] . " 
                    WHERE seqProyecto = $seqProyectoHijo;";
                $arrayInsLicencias = Array();
                $arrayInsLicencias[$seqProyectoHijo]['seqProyectoLicencia'][0] = $seqProyectoLicenciaUrbHijo;
                $arrayInsLicencias[$seqProyectoHijo]['txtLicencia'][0] = $txtLicenciaUrbanismoHijo;
                $arrayInsLicencias[$seqProyectoHijo]['txtExpideLicencia'][0] = $txtExpideLicenciaUrbanismoHijo;
                $arrayInsLicencias[$seqProyectoHijo]['fchLicencia'][0] = $fchLicenciaUrbanismo1Hijo;
                $arrayInsLicencias[$seqProyectoHijo]['fchVigenciaLicencia'][0] = $fchVigenciaLicenciaUrbanismoHijo;
                $arrayInsLicencias[$seqProyectoHijo]['fchEjecutoriaLicencia'][0] = $fchEjecutoriaLicenciaUrbanismoHijo;
                $arrayInsLicencias[$seqProyectoHijo]['seqTipoLicencia'][0] = 1;
                $arrayInsLicencias[$seqProyectoHijo]['seqProyectoLicencia'][1] = $seqProyectoLicenciaConsHijo;
                $arrayInsLicencias[$seqProyectoHijo]['txtLicencia'][1] = $txtLicenciaConstruccionHijo;
                $arrayInsLicencias[$seqProyectoHijo]['fchLicencia'][1] = $fchLicenciaConstruccion1Hijo;
                $arrayInsLicencias[$seqProyectoHijo]['txtExpideLicencia'][1] = $txtExpideLicenciaConstruccionHijo;
                $arrayInsLicencias[$seqProyectoHijo]['fchEjecutoriaLicencia'][1] = $fchEjecutoriaLicenciaConstruccionHijo;
                $arrayInsLicencias[$seqProyectoHijo]['fchVigenciaLicencia'][1] = $fchVigenciaLicenciaConstruccionHijo;
                $arrayInsLicencias[$seqProyectoHijo]['seqTipoLicencia'][1] = 2;

                $this->modificarLicencias($seqProyectoHijo, $arrayInsLicencias, count($arrayInsLicencias));
            } else if ($cant >= $exeExistentes->numRows()) {

                $arrayconjuntos = Array();
                $arrayconjuntos[$seqProyecto]['seqProyectoHijo'][] = $seqProyectoHijo;
                $arrayconjuntos[$seqProyecto]['txtNombreProyectoHijo'][] = $txtNombreProyectoHijo;
                $arrayconjuntos[$seqProyecto]['txtNombreComercialHijo'][] = $txtNombreComercialHijo;
                $arrayconjuntos[$seqProyecto]['seqLocalidadHijo'][] = $seqLocalidadHijo;
                $arrayconjuntos[$seqProyecto]['seqBarrioHijo'][] = $seqBarrioHijo;
                $arrayconjuntos[$seqProyecto]['txtDireccionHijo'][] = $txtDireccionHijo;
                $arrayconjuntos[$seqProyecto]['valNumeroSolucionesHijo'][] = $valNumeroSolucionesHijo;
                $arrayconjuntos[$seqProyecto]['txtChipLoteHijo'][] = $txtChipLoteHijo;
                $arrayconjuntos[$seqProyecto]['txtMatriculaInmobiliariaLoteHijo'][] = $txtMatriculaInmobiliariaLoteHijo;
                $arrayconjuntos[$seqProyecto]['txtLicenciaUrbanismoHijo'][] = $txtLicenciaUrbanismoHijo;
                $arrayconjuntos[$seqProyecto]['fchLicenciaUrbanismo1Hijo'][] = $fchLicenciaUrbanismo1Hijo;
                $arrayconjuntos[$seqProyecto]['fchVigenciaLicenciaUrbanismoHijo'][] = $fchVigenciaLicenciaUrbanismoHijo;
                $arrayconjuntos[$seqProyecto]['fchEjecutoriaLicenciaUrbanismoHijo'][] = $fchEjecutoriaLicenciaUrbanismoHijo;
                $arrayconjuntos[$seqProyecto]['txtExpideLicenciaUrbanismoHijo'][] = $txtExpideLicenciaUrbanismoHijo;
                $arrayconjuntos[$seqProyecto]['txtLicenciaConstruccionHijo'][] = $txtLicenciaConstruccionHijo;
                $arrayconjuntos[$seqProyecto]['fchLicenciaConstruccion1Hijo'][] = $fchLicenciaConstruccion1Hijo;
                $arrayconjuntos[$seqProyecto]['fchVigenciaLicenciaConstruccionHijo'][] = $fchVigenciaLicenciaConstruccionHijo;
                $arrayconjuntos[$seqProyecto]['fchEjecutoriaLicenciaConstruccionHijo'][] = $fchEjecutoriaLicenciaConstruccionHijo;
                $arrayconjuntos[$seqProyecto]['txtExpideLicenciaConstruccionHijo'][] = $txtExpideLicenciaConstruccionHijo;
                $arrayconjuntos[$seqProyecto]['txtNombreVendedorHijo'][] = $txtNombreVendedorHijo;
                $arrayconjuntos[$seqProyecto]['seqTipoDocumentoVendedorHijo'][] = $seqTipoDocumentoVendedorHijo;
                $arrayconjuntos[$seqProyecto]['numNitVendedorHijo'][] = $numNitVendedorHijo;
                $arrayconjuntos[$seqProyecto]['numTelVendedorHijo'][] = $numTelVendedorHijo;
                $arrayconjuntos[$seqProyecto]['txtCedulaCatastralHijo'][] = $txtCedulaCatastralHijo;
                $arrayconjuntos[$seqProyecto]['txtEscrituraHijo'][] = $txtEscrituraHijo;
                $arrayconjuntos[$seqProyecto]['fchEscrituraHijo'][] = $fchEscrituraHijo;
                $arrayconjuntos[$seqProyecto]['numNotariaHijo'][] = $numNotariaHijo;
                $this->almacenarConjuntos($seqProyecto, $arrayconjuntos, count($arrayconjuntos));
            }
            // echo "<br><br>" . $query;
            try {
                $aptBd->execute($query);
            } catch (Exception $objError) {
                $arrErrores[] = "No se ha podido cargar las licencias<b></b>";
                pr($objError->getMessage());
            }
        }
        if ($cant < $exeExistentes->numRows()) {
            $resultado = array_diff($datos, $datosDiff);
            $delete = "";
            foreach ($resultado as $value) {
                $delete .= $value . ",";
            }
            //  print_r($resultado);
            $delete = substr_replace($delete, '', -1, 1);
            $sqlLic = "DELETE  FROM  t_pry_proyecto_licencias WHERE seqProyecto in (" . $delete . ")";
            $sql = "DELETE FROM T_PRY_PROYECTO WHERE seqProyecto in (" . $delete . ")";
            try {
                $aptBd->execute($sqlLic);
                $aptBd->execute($sql);
            } catch (Exception $objError) {
                $arrErrores[] = "No se ha podido eliminar conjunto<b></b>";
                pr($objError->getMessage());
            }
        }
    }

// fin validacion de borrar Proyecto

    function almacenarTipoVivienda($seqProyecto, $array, $cant) {

        global $aptBd;
        $arrErrores = array();
        $query = "INSERT INTO t_pry_tipo_vivienda
                  (
                    txtNombreTipoVivienda,
                    numCantidad,
                    numCantUdsDisc,
                    numTotalParq,
                    numCantParqDisc,
                    numArea,
                    numAnoVenta,
                    valPrecioVenta,
                    txtDescripcion,
                    valCierre,
                    seqProyecto,
                    fchGestion)
                    VALUES";
        for ($index = 0; $index <= $cant; $index++) {

            foreach ($array[$seqProyecto] as $key => $value) {
                ECHO "<P> KEY -> " . $key . "</P>";
                if ($value[($index)] == NULL && $value[($index)] == "") {
                    $$key = (count(explode('txt', $key)) > 1) ? NULL : 'NULL';
                    // $$key = 0;
                } else {
                    $$key = $value[($index)];
                }
            }
            if ($txtNombreTipoVivienda != "") {
                $query .= "(
                        '$txtNombreTipoVivienda',
                        $numCantidad,
                        $numCantUdsDisc,
                        $numTotalParq,
                        $numCantParqDisc,
                        $numArea,
                        $numAnoVenta,
                        $valPrecioVenta,
                        '$txtDescripcion',
                        $valCierre,
                        $seqProyecto,
                        '$fchGestion'),";
            }
        }
        $query = substr_replace($query, ';', -1, 1);
        //echo "<br>" . $query . "<br>";
        try {
            $aptBd->execute($query);
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido cargar las los tipo de vivienda<b></b>";
            pr($objError->getMessage());
        }
    }

    function modificarTipoVivienda($seqProyecto, $array, $cant) {
        //  echo "****" . count($array[$seqProyecto]);
        global $aptBd;
        $arrErrores = array();
        $sqlExistentes = "SELECT seqTipoVivienda FROM T_PRY_TIPO_VIVIENDA WHERE seqProyecto = $seqProyecto";
        $exeExistentes = $aptBd->execute($sqlExistentes);
        //$cant = $exeExistentes->numRows();
        $datos = Array();
        $datosDiff = Array();
        while ($exeExistentes->fields) {
            $datos[] = $exeExistentes->fields['seqTipoVivienda'];
            $exeExistentes->MoveNext();
        }
        for ($index = 0; $index < $cant; $index++) {
            $txtNombreProyectoHijo = '';
            foreach ($array[$seqProyecto] as $key => $value) {
                if ($value[($index)] == NULL && $value[($index)] == "") {
                    $$key = 0;
                    $$key = (count(explode('fch', $key)) > 1) ? NULL : 'NULL';
                } else {
                    $$key = $value[($index)];
                }
            }
            $datosDiff[] = $seqTipoVivienda;

            if (in_array($seqTipoVivienda, $datos)) {
                $query = "UPDATE T_PRY_TIPO_VIVIENDA 
                    SET 
                    txtNombreTipoVivienda = '$txtNombreTipoVivienda',
                       numCantidad = '$numCantidad', "
                        . "numCantUdsDisc = '$numCantUdsDisc', "
                        . "numTotalParq = '$numTotalParq', "
                        . "numCantParqDisc = '$numCantParqDisc', "
                        . "numArea = '$numArea', "
                        . "numAnoVenta = '$numAnoVenta', "
                        . "valPrecioVenta = '$valPrecioVenta', "
                        . "txtDescripcion = '$txtDescripcion', "
                        . "valCierre = '$valCierre', "
                        . "fchGestion = '$fchGestion' "
                        . "WHERE seqTipoVivienda = $seqTipoVivienda "
                        . "AND seqProyecto = $seqProyecto;
                        ";
            } else if ($cant >= $exeExistentes->numRows()) {
                $arrayTipoVivienda = Array();
                $arrayTipoVivienda[$seqProyecto]['seqTipoVivienda'][] = $seqTipoVivienda;
                $arrayTipoVivienda[$seqProyecto]['txtNombreTipoVivienda'][] = $txtNombreTipoVivienda;
                $arrayTipoVivienda[$seqProyecto]['numCantidad'][] = $numCantidad;
                $arrayTipoVivienda[$seqProyecto]['numArea'][] = $numArea;
                $arrayTipoVivienda[$seqProyecto]['numCantUdsDisc'][] = $numCantUdsDisc;
                $arrayTipoVivienda[$seqProyecto]['numTotalParq'][] = $numTotalParq;
                $arrayTipoVivienda[$seqProyecto]['numCantParqDisc'][] = $numCantParqDisc;
                $arrayTipoVivienda[$seqProyecto]['numAnoVenta'][] = $numAnoVenta;
                $arrayTipoVivienda[$seqProyecto]['valPrecioVenta'][] = $valPrecioVenta;
                $arrayTipoVivienda[$seqProyecto]['valCierre'][] = $valCierre;
                $arrayTipoVivienda[$seqProyecto]['txtDescripcion'][] = $txtDescripcion;
                $this->almacenarTipoVivienda($seqProyecto, $arrayTipoVivienda, count($arrayTipoVivienda));
            }
            // echo "<br><br>" . $query;
            try {
                $aptBd->execute($query);
            } catch (Exception $objError) {
                $arrErrores[] = "No se ha podido cargar las licencias<b></b>";
                pr($objError->getMessage());
            }
        }
        if ($cant < $exeExistentes->numRows()) {
            $resultado = array_diff($datos, $datosDiff);
            $delete = "";
            foreach ($resultado as $value) {
                $delete .= $value . ",";
            }
            //  print_r($resultado);
            $delete = substr_replace($delete, '', -1, 1);
            $sql = "DELETE FROM T_PRY_TIPO_VIVIENDA WHERE seqTipoVivienda in (" . $delete . ")";
            try {
                $aptBd->execute($sql);
            } catch (Exception $objError) {
                $arrErrores[] = "No se ha podido eliminar conjunto<b></b>";
                pr($objError->getMessage
                        ());
            }
        }
    }

    function almacenarCronograma($seqProyecto, $arrayCronograma, $cant) {

        global $aptBd;
        $arrErrores = array();
        $query = "  INSERT INTO t_pry_cronograma_fechas
                        (
                        numActaDescriptiva,
                        numAnoActaDescriptiva,
                        fchInicialProyecto,
                        fchFinalProyecto,
                        valPlazoEjecucion,
                        fchInicialEntrega,
                        fchFinalEntrega,
                        fchInicialEscrituracion,
                        fchFinalEscrituracion,
                        seqProyecto,
                        fchGestion)
                        VALUES";
        for ($index = 0; $index < $cant; $index++) {

            foreach ($arrayCronograma[$seqProyecto] as $key => $value) {
                $value[($index)] == "" ? $$key = '0' : $$key = $value[($index)];
            }

            $query .= "(
                        $numActaDescriptiva,
                        $numAnoActaDescriptiva,
                        '$fchInicialProyecto',
                        '$fchFinalProyecto',
                        $valPlazoEjecucion,
                        '$fchInicialEntrega',
                        '$fchFinalEntrega',
                        '$fchInicialEscrituracion',
                        '$fchFinalEscrituracion',
                        $seqProyecto,
                        '$fchGestion'),";
            // }
        }
        $query = substr_replace($query, ';', -1, 1);
        // echo "<br>" . $query . "<br>";
        try {
            $aptBd->execute($query);
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido cargar el cronograma<b></b>";
            pr($objError->getMessage
                    ());
        }
    }

    function modificarCronograma($seqProyecto, $array, $cant) {

        global $aptBd;
        $arrErrores = array();
        $sqlExistentes = "  SELECT seqCronogramaFecha FROM t_pry_cronograma_fechas WHERE seqProyecto = $seqProyecto";
        $exeExistentes = $aptBd->execute($sqlExistentes);
        //$cant = $exeExistentes->numRows();
        //echo "<p>".$exeExistentes->numRows()."</p>";
        $datos = Array();
        $datosDiff = Array();
        while ($exeExistentes->fields) {
            $datos[] = $exeExistentes->fields['seqCronogramaFecha'];
            $exeExistentes->MoveNext();
        }
        for ($index = 0; $index < $cant; $index++) {

            foreach ($array[$seqProyecto] as $key => $value) {
                $value[($index)] == "" ? $$key = '0' : $$key = $value[($index)];
            }
            //  echo "<br> seqCronogramaFecha->".$seqCronogramaFecha;
            $datosDiff[] = $seqCronogramaFecha;

            if (in_array($seqCronogramaFecha, $datos)) {
                $query = "UPDATE t_pry_cronograma_fechas
                        SET
                        numActaDescriptiva = $numActaDescriptiva,
                        numAnoActaDescriptiva = $numAnoActaDescriptiva,
                        fchInicialProyecto = '$fchInicialProyecto',
                        fchFinalProyecto = '$fchFinalProyecto',
                        valPlazoEjecucion = $valPlazoEjecucion,
                        fchInicialEntrega = '$fchInicialEntrega',
                        fchFinalEntrega = '$fchFinalEntrega',
                        fchInicialEscrituracion = '$fchInicialEscrituracion',
                        fchFinalEscrituracion = '$fchFinalEscrituracion',
                        fchGestion = '$fchGestion'
                        WHERE seqCronogramaFecha = $seqCronogramaFecha "
                        . "AND seqProyecto = $seqProyecto;
                        ";
            } else if ($cant >= $exeExistentes->numRows()) {
                $arraycronograma = Array();
                $arraycronograma[$seqProyecto]['seqCronogramaFecha'][] = $seqCronogramaFecha;
                $arraycronograma[$seqProyecto]['numActaDescriptiva'][] = $numActaDescriptiva;
                $arraycronograma[$seqProyecto]['numAnoActaDescriptiva'][] = $numAnoActaDescriptiva;
                $arraycronograma[$seqProyecto]['fchInicialProyecto'][] = $fchInicialProyecto;
                $arraycronograma[$seqProyecto]['fchFinalProyecto'][] = $fchFinalProyecto;
                $arraycronograma[$seqProyecto]['valPlazoEjecucion'][] = $valPlazoEjecucion;
                $arraycronograma[$seqProyecto]['fchInicialEntrega'][] = $fchInicialEntrega;
                $arraycronograma[$seqProyecto]['fchFinalEntrega'][] = $fchFinalEntrega;
                $arraycronograma[$seqProyecto]['fchInicialEscrituracion'][] = $fchInicialEscrituracion;
                $arraycronograma[$seqProyecto]['fchFinalEscrituracion'][] = $fchFinalEscrituracion;
                $this->almacenarCronograma($seqProyecto, $arraycronograma, count($arraycronograma));
            }
            //echo "<br><br>" . $query;
            try {
                $aptBd->execute($query);
            } catch (Exception $objError) {
                $arrErrores[] = "No se ha podido cargar las licencias<b></b>";
                pr($objError->getMessage());
            }
        }

        if ($cant < $exeExistentes->numRows()) {
            $resultado = array_diff($datos, $datosDiff);
            $delete = "";
            foreach ($resultado as $value) {
                $delete .= $value . ",";
            }
            //  print_r($resultado);
            $delete = substr_replace($delete, '', -1, 1);
            $sql = "DELETE FROM t_pry_cronograma_fechas WHERE seqCronogramaFecha in (" . $delete . ")";
            try {
                $aptBd->execute($sql);
            } catch (Exception $objError) {
                $arrErrores[] = "No se ha podido eliminar Cronograma<b></b>";
                pr($objError->getMessage());
            }
        }
    }

    function almacenarPoliza($seqProyecto, $seqAseguradora, $numPoliza, $fchExpedicion, $seqUsuario, $bolAprobo, $arrayAmparos) {
        global $aptBd;
        $arrErrores = array();
        if ($bolAprobo == "") {
            $bolAprobo = 0;
        }
        $sql = "INSERT INTO t_pry_poliza
                ( numPoliza,fchExpedicion,seqUsuario, bolAprobo,  seqAseguradora, seqProyecto)
                VALUES
                ('$numPoliza','$fchExpedicion', $seqUsuario, $bolAprobo, $seqAseguradora,$seqProyecto);";
        $query = "  INSERT INTO t_pry_amparo
                    (fchVigenciaIni, fchVigenciaFin, valAsegurado, bolAproboAmparo, seqTipoAmparo,seqAmparoPadre, seqPoliza) VALUES";
        try {
            $aptBd->execute($sql);
            $seqPoliza = $aptBd->Insert_ID();
            // echo "<p> " . count($arrayAmparos[$seqProyecto]['seqTipoAmparo']) . "</p>";
            for ($ind = 0; $ind < count($arrayAmparos[$seqProyecto]['seqTipoAmparo']); $ind++) {
                foreach ($arrayAmparos[$seqProyecto] as $key => $value) {
                    //  echo "<p> key = " . $key . " value = " . $value[$ind] . "</p>";
                    $$key = $value[$ind];
                }
                if ($bolAproboAmparo != "") {
                    $bolAproboAmparo = 1;
                } else {
                    $bolAproboAmparo = 0;
                }
                if ($seqAmparoPadre == "") {
                    $seqAmparoPadre = 'NULL';
                }
                $query .= "(
                      '$fchVigenciaIni ',
                      '$fchVigenciaFin',
                      $valAsegurado,
                      $bolAproboAmparo,   
                      $seqTipoAmparo,
                      $seqAmparoPadre,
                      $seqPoliza),";
            }
            try {
                $query = substr_replace($query, ';', -1, 1);
                //  echo "<p>" . $query . "</p>";
                $aptBd->execute($query);
            } catch (Exception $objError) {
                $arrErrores[] = "No se ha podido cargar el cronograma<b></b>";
                pr($objError->getMessage
                        ());
            }
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido cargar la poliza<b></b>";
            pr($objError->getMessage());
        }
    }

    function modificarPoliza($seqProyecto, $seqPoliza, $seqAseguradora, $numPoliza, $fchExpedicion, $seqUsuario, $bolAprobo, $arrayAmparos) {

        global $aptBd;
        $arrErrores = array();
        if ($bolAprobo == "") {
            $bolAprobo = 0;
        }
        $sql = "UPDATE t_pry_poliza
                SET                
                numPoliza = '$numPoliza',
                fchExpedicion = '$fchExpedicion',
                seqUsuario = $seqUsuario,
                bolAprobo = $bolAprobo,
                seqAseguradora = $seqAseguradora,
                seqProyecto = $seqProyecto
                WHERE seqPoliza = $seqPoliza;";

        // $sqlCantAmparos = "SELECT COUNT(*) AS cant t_pry_amparo WHERE seqPoliza = $seqPoliza ";
        $arrErrores = array();
        $sqlExistentes = "  SELECT seqAmparo FROM t_pry_amparo WHERE seqPoliza = $seqPoliza";
        $exeExistentes = $aptBd->execute($sqlExistentes);

        $datos = Array();
        $datosDiff = Array();
        while ($exeExistentes->fields) {
            $datos[] = $exeExistentes->fields['seqAmparo'];
            $exeExistentes->MoveNext();
        }

        try {
            $aptBd->execute($sql);
            for ($ind = 0; $ind < count($arrayAmparos[$seqProyecto]['seqTipoAmparo']); $ind++) {
                foreach ($arrayAmparos[$seqProyecto] as $key => $value) {

                    $$key = $value[$ind];
                    //echo "<br>key => ".$key ." valor => ".$value[$ind];
                    if ($bolAproboAmparo != "") {
                        $bolAproboAmparo = 1;
                    } else {
                        $bolAproboAmparo = 0;
                    }
                    if ($seqAmparoPadre == "") {
                        $seqAmparoPadre = 'NULL';
                    }
                }
                //  echo "<br>*****" . $seqAmparoPadre . "******<br>";

                $datosDiff[] = $seqAmparo;
                if (in_array($seqAmparo, $datos)) {
                    $update = "UPDATE t_pry_amparo
                                SET
                                seqTipoAmparo = $seqTipoAmparo,
                                fchVigenciaIni = '$fchVigenciaIni',
                                fchVigenciaFin = '$fchVigenciaFin',
                                valAsegurado = $valAsegurado,
                                seqUsuario = $seqUsuario,
                                bolAproboAmparo = $bolAproboAmparo,
                                seqPoliza = $seqPoliza
                                WHERE seqAmparo = $seqAmparo;";
                    try {
                        // echo "<p>" . $update . "</p>";
                        $aptBd->execute($update);
                    } catch (Exception $objError) {
                        $arrErrores[] = "No se ha podido modificar el amparo<b></b>";
                        pr($objError->getMessage());
                    }
                } else if (count($arrayAmparos[$seqProyecto]['seqTipoAmparo']) >= $exeExistentes->numRows() && $seqAmparo == "") {
                    $query = "  INSERT INTO t_pry_amparo
                    (fchVigenciaIni, fchVigenciaFin, valAsegurado, seqTipoAmparo, seqAmparoPadre, seqUsuario, bolAproboAmparo, seqPoliza) VALUES";
                    if ($seqUsuario > 0) {
                        $bolAproboAmparo = 1;
                    } else {
                        $bolAproboAmparo = 0;
                        $seqUsuario = 0;
                    }
                    $query .= "(
                              '$fchVigenciaIni ',
                              '$fchVigenciaFin',
                              $valAsegurado,
                              $seqTipoAmparo,
                              $seqAmparoPadre,
                              $seqUsuario, 
                              $bolAproboAmparo,
                              $seqPoliza),";

                    $query = substr_replace($query, ';', -1, 1);
                    try {
                        // echo "<p>" . $query . "</p>";
                        $aptBd->execute($query);
                    } catch (Exception $objError) {
                        $arrErrores[] = "No se ha podido cargar el Amparo<b></b>";
                        pr($objError->getMessage());
                    }
                }
            }
            if (count($arrayAmparos[$seqProyecto]['seqTipoAmparo']) < $exeExistentes->numRows()) {
                $resultado = array_diff($datos, $datosDiff);
                $delete = "";
                foreach ($resultado as $value) {
                    $delete .= $value . ",";
                }
                //  print_r($resultado);
                $delete = substr_replace($delete, '', -1, 1);
                $sqlDelete = "DELETE FROM t_pry_amparo WHERE seqAmparoPadre in (" . $delete . ")";
                $sqlDeletePadre = "DELETE FROM t_pry_amparo WHERE seqAmparo in (" . $delete . ")";
                try {
                    $aptBd->execute($sqlDelete);
                    try {
                        $aptBd->execute($sqlDeletePadre);
                    } catch (Exception $objError) {
                        $arrErrores[] = "No se ha podido eliminar el Amparo<b></b>";
                        pr($objError->getMessage());
                    }
                } catch (Exception $objError) {
                    $arrErrores[] = "No se ha podido eliminar el Amparo<b></b>";
                    pr($objError->getMessage());
                }
            }
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido modificar la poliza<b></b>";
            pr($objError->getMessage());
        }
    }

    function almacenarFiducia($seqProyecto, $arrayFiducia) {

        global $aptBd;
        $arrErrores = array();
        foreach ($arrayFiducia as $key => $value) {
            $value == "" ? $$key = '0' : $$key = $value;
            //echo "key = " . $key . " value -> " . $value;
        }
        $seqBanco = ($seqBanco == 0) ? 1 : $seqBanco;
        $seqFiducia = ($seqFiducia == 0) ? 'NULL' : $seqFiducia;
        $seqCiudad = ($seqCiudad == 0) ? 'NULL' : $seqCiudad;

        $sql = "INSERT INTO t_pry_datos_fiducia
                (
                    seqTipoContrato,
                    numContratoFiducia,
                    fchContratoFiducia,
                    numCuentaFiducia,
                    seqTipoCuentaFiducia,
                    txtContactoFiducia,
                    numTelContactoFiducia,
                    seqEntidadFiducia,
                    txtEntidadFiducia,
                    numIdEntidad,
                    seqCiudad,
                    valContratoFiducia,
                    fchVigenciaContratoFiducia,
                    seqTipoRecursoFiducia,
                    txtRazonSocialFiducia,
                    numNitFiducia,
                    seqBanco,
                    seqFiducia,
                    seqProyecto)
                VALUES (
                    $seqTipoContrato,
                    '$numContratoFiducia',
                    '$fchContratoFiducia',
                    '$numCuentaFiducia',
                    $seqTipoCuentaFiducia,
                    '$txtContactoFiducia',
                    $numTelContactoFiducia,
                    $seqEntidadFiducia,
                    '$txtEntidadFiducia',
                    $numIdEntidad,
                    $seqCiudad,
                    $valContratoFiducia,
                    '$fchVigenciaContratoFiducia',
                    $seqTipoRecursoFiducia,
                    '$txtRazonSocialFiducia',
                    $numNitFiducia,
                    $seqBanco,
                    $seqFiducia,
                    $seqProyecto);";
        try {
            $aptBd->execute($sql);
            $seqDatoFiducia = $aptBd->Insert_ID();
            if (count($arrayFiducia['seqTipoFideicomitente']) > 0 && $arrayFiducia['seqTipoFideicomitente'][0] != "") {
                $insertSql = "INSERT INTO t_pry_fideicomitente (
                    txtNombreFideicomitente, seqTipoFideicomitente, seqDatoFiducia) VALUES";
                foreach ($arrayFiducia['seqTipoFideicomitente'] as $keyFid => $valueFid) {
                    $insertSql .= "('" . $arrayFiducia['txtNombreFideicomitente'][$keyFid] . "', $valueFid, $seqDatoFiducia),";
                }
                try {
                    $insertSql = substr_replace($insertSql, ';', -1, 1);
                    $aptBd->execute($insertSql);
                } catch (Exception $objError) {
                    $arrErrores[] = "<b>No se ha podido almacenar el fideicomitente </b>";
                    pr($objError->getMessage());
                }
            }
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido insertar la fiducia<b></b>";
            pr($objError->getMessage());
        }
    }

    function modificarFiducia($seqProyecto, $arrayFiducia) {

        global $aptBd;
        $arrErrores = array();
        foreach ($arrayFiducia as $key => $value) {
            $value == "" ? $$key = '0' : $$key = $value;
        }

        $sql = "UPDATE t_pry_datos_fiducia
                SET               
                seqTipoContrato = $seqTipoContrato,
                numContratoFiducia = '$numContratoFiducia',
                fchContratoFiducia = '$fchContratoFiducia',
                numCuentaFiducia = '$numCuentaFiducia',
                seqTipoCuentaFiducia = $seqTipoCuentaFiducia,
                txtContactoFiducia = '$txtContactoFiducia',
                numTelContactoFiducia = $numTelContactoFiducia,
                seqEntidadFiducia = $seqEntidadFiducia,
                txtEntidadFiducia = '$txtEntidadFiducia',
                numIdEntidad = $numIdEntidad,
                seqCiudad = $seqCiudad,
                valContratoFiducia = $valContratoFiducia,
                fchVigenciaContratoFiducia = '$fchVigenciaContratoFiducia',
                seqTipoRecursoFiducia = $seqTipoRecursoFiducia,
                txtRazonSocialFiducia = '$txtRazonSocialFiducia',
                numNitFiducia = $numNitFiducia,
                seqBanco = $seqBanco,
                seqFiducia = $seqFiducia                
                WHERE seqProyecto = $seqProyecto";
        try {

            $aptBd->execute($sql);
            $this->modificarFideicomitente($seqDatoFiducia, $arrayFiducia);
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido insertar la fiducia<b></b>";
            pr($objError->getMessage());
        }
    }

    function modificarFideicomitente($seqDatoFiducia, $arrayFiducia) {
        global $aptBd;
        $arrErrores = array();
        $sqlExistentes = "  SELECT seqFideicomitente FROM t_pry_fideicomitente WHERE seqDatoFiducia = $seqDatoFiducia";
        $exeExistentes = $aptBd->execute($sqlExistentes);
        $datos = Array();
        $datosDiff = Array();
        while ($exeExistentes->fields) {
            $datos[] = $exeExistentes->fields['seqFideicomitente'];
            $exeExistentes->MoveNext();
        }
        //var_dump($arrayFiducia);
        if (count($arrayFiducia['seqTipoFideicomitente']) > 0) {
            foreach ($arrayFiducia['seqTipoFideicomitente'] as $keyFid => $valueFid) {
                $seqFideicomitente = $arrayFiducia['seqFideicomitente'][$keyFid];
                $txtNombreFideicomitente = $arrayFiducia['txtNombreFideicomitente'][$keyFid];
                $datosDiff[] = $seqFideicomitente;
                // var_dump($datos);
                // die();
                if (in_array($seqFideicomitente, $datos)) {
                    $updFideicomitente = "UPDATE t_pry_fideicomitente
                                        SET                                               
                                        txtNombreFideicomitente ='$txtNombreFideicomitente',
                                        seqTipoFideicomitente = " . $arrayFiducia['seqTipoFideicomitente'][$keyFid] . "                                                
                                        WHERE seqFideicomitente = $seqFideicomitente and seqDatoFiducia = $seqDatoFiducia;";

                    try {
                        // echo "<p>" . $update . "</p>";
                        $aptBd->execute($updFideicomitente);
                    } catch (Exception $objError) {
                        $arrErrores[] = "No se ha podido modificar el fideicomitente<b></b>";
                        pr($objError->getMessage());
                    }
                } else if (count($arrayFiducia['seqTipoFideicomitente']) >= $exeExistentes->numRows() && $seqFideicomitente == "") {
                    $insertSql = "INSERT INTO t_pry_fideicomitente (
                    txtNombreFideicomitente, seqTipoFideicomitente, seqDatoFiducia) VALUES('" . $arrayFiducia['txtNombreFideicomitente'][$keyFid] . "', $valueFid, $seqDatoFiducia);";
                    try {
                        //  $insertSql = substr_replace($insertSql, ';', -1, 1);
                        $aptBd->execute($insertSql);
                    } catch (Exception $objError) {
                        $arrErrores[] = "<b>No se ha podido almacenar el fideicomitente </b>";
                        pr($objError->getMessage());
                    }
                }
            }
            if (count($arrayFiducia['seqTipoFideicomitente']) < $exeExistentes->numRows()) {
                $resultado = array_diff($datos, $datosDiff);
                $delete = "";
                foreach ($resultado as $value) {
                    $delete .= $value . ",";
                }
                $delete = substr_replace($delete, '', -1, 1);
                $sqlDelete = "DELETE FROM t_pry_fideicomitente WHERE seqFideicomitente in (" . $delete . ")";
                try {
                    $aptBd->execute($sqlDelete);
                } catch (Exception $objError) {
                    $arrErrores[] = "No se ha podido eliminar el Amparo<b></b>";
                    pr($objError->getMessage());
                }
            }
        }
    }

    public function obtenerDatosPoliza($seqProyecto) {

        global $aptBd;
        $sql = "SELECT max(fchVigenciaFin) as vigencia , seqTipoAmparo, seqAmparoPadre, txtNombreAseguradora, seqAmparo, txtTipoAmparo
                FROM t_pry_poliza pol
                LEFT JOIN t_pry_amparo amp  USING(seqPoliza)
                LEFT JOIN t_pry_tipo_amparo tamp using(seqTipoAmparo)
                LEFT JOIN t_pry_aseguradoras ase using(seqAseguradora)";
        if ($seqProyecto > 0) {
            $sql .= " where  pol.seqProyecto = " . $seqProyecto;
        }
        $sql .="  group by tamp.seqTipoAmparo, seqAmparoPadre";
        //   echo "<p>".$sql."</p>";
        $objRes = $aptBd->execute($sql);
        $datos = Array();
        while ($objRes->fields) {
            $datos[] = $objRes->fields;
            $objRes->MoveNext();
        }
        return $datos;
    }

    public function datosTecnicosOcupacion($seqProyecto) {
        global $aptBd;
        $sql = "select count(*) as cant
                from  t_pry_tecnico
                LEFT JOIN t_pry_unidad_proyecto und  using(seqUnidadProyecto)
                LEFT JOIN t_pry_proyecto USING(seqProyecto)
                where  txtPermisoOcupacion like UPPER('SI')";
        if ($seqProyecto > 0) {
            $sql .= " and case  when seqProyectoPadre IS NOT NULL then  und.seqProyecto in (select concat(seqProyecto, ',') from  t_pry_proyecto where seqProyectoPadre = " . $seqProyecto . ") else  und.seqProyecto = " . $seqProyecto . " end";
        }
        // $sql .="  group by tamp.seqTipoAmparo, seqAmparoPadre";
        // echo "<p>".$sql."</p>";
        $objRes = $aptBd->execute($sql);
        $datos = 0;

        while ($objRes->fields) {
            $datos = $objRes->fields['cant'];
            $objRes->MoveNext();
        }
        return $datos;
    }

    public function datosTecnicosExistencia($seqProyecto) {
        global $aptBd;
        $sql = "select count(*) as cant
                from  t_pry_tecnico
                LEFT JOIN t_pry_unidad_proyecto und  using(seqUnidadProyecto)
                LEFT JOIN t_pry_proyecto USING(seqProyecto)
                where  txtExistencia like UPPER('SI')";
        if ($seqProyecto > 0) {
            $sql .= " and case  when seqProyectoPadre IS NOT NULL then  und.seqProyecto in (select concat(seqProyecto, ',') from  t_pry_proyecto where seqProyectoPadre = " . $seqProyecto . ") else  und.seqProyecto = " . $seqProyecto . " end";
        }
        //$sql .="  group by tamp.seqTipoAmparo, seqAmparoPadre";
        //   echo "<p>".$sql."</p>";
        $objRes = $aptBd->execute($sql);
        $datos = 0;

        while ($objRes->fields) {
            $datos = $objRes->fields['cant'];
            $objRes->MoveNext();
        }
        return $datos;
    }

    function almacenarActaComite($seqProyecto, $arrayActasComite, $cant) {

        global $aptBd;
        $arrErrores = array();
        $sql = "INSERT INTO t_pry_proyecto_comite
                (
                numActaComite,
                fchActaComite,
                numResolucionComite,
                fchResolucionComite,
                txtObservacionesComite,
                bolCondicionesComite,
                txtCondicionesComite,
                bolAproboProyecto,
                seqProyecto,
                seqEntidadComite)
                VALUES";
        for ($index = 0; $index < $cant; $index++) {

            foreach ($arrayActasComite[$seqProyecto] as $key => $value) {
                //echo "<p>".count($value)."</p>";
                if (count($value) >= 1) {
                    $value[($index)] == "" ? $$key = '0' : $$key = $value[($index)];
                } else {
                    $value == "" ? $$key = '0' : $$key = $value;
                }
            }
            $sql .= "(
                        $numActaComite,
                        '$fchActaComite',
                        $numResolucionComite,
                        '$fchResolucionComite',
                        '$txtObservacionesComite',
                        $bolCondicionesComite,
                       '$txtCondicionesComite',
                        $bolAproboProyecto,
                        $seqProyecto,
                        $seqEntidadComite),";
        }

        try {
            $sql = substr_replace($sql, ';', -1, 1);
            // echo "<p>" . $sql . "</p>";
            $aptBd->execute($sql);
        } catch (Exception $objError) {
            $arrErrores[] = "<b>No se ha podido almacenar el El acta del comite </b>";
            pr($objError->getMessage());
        }
    }

    function modificarActasComite($seqProyecto, $arrayActasComite, $cant) {

        global $aptBd;
        $arrErrores = array();
        $sqlExistentes = "SELECT seqProyectoComite FROM t_pry_proyecto_comite WHERE seqProyecto = $seqProyecto";
        $exeExistentes = $aptBd->execute($sqlExistentes);
        //$cant = $exeExistentes->numRows();
        $datos = Array();
        $datosDiff = Array();
        while ($exeExistentes->fields) {
            $datos[] = $exeExistentes->fields['seqProyectoComite'];
            $exeExistentes->MoveNext();
        }

        for ($index = 0; $index < $cant; $index++) {
            $txtNombreProyectoHijo = '';
            foreach ($arrayActasComite[$seqProyecto] as $key => $value) {
                if ($value[($index)] == NULL && $value[($index)] == "") {
                    $$key = 0;
                } else {
                    $$key = $value[($index)];
                }
            }
            $datosDiff[] = $seqProyectoComite;

            if (in_array($seqProyectoComite, $datos)) {
                $query = "UPDATE t_pry_proyecto_comite 
                        SET                   
                        numActaComite = $numActaComite,
                        fchActaComite = '$fchActaComite',
                        numResolucionComite = $numResolucionComite,
                        fchResolucionComite = '$fchResolucionComite',
                        txtObservacionesComite = '$txtObservacionesComite',
                        bolCondicionesComite = $bolCondicionesComite,
                        txtCondicionesComite = '$txtCondicionesComite',
                        bolAproboProyecto = $bolAproboProyecto,
                        seqEntidadComite = $seqEntidadComite"
                        . " WHERE seqProyectoComite = $seqProyectoComite"
                        . " AND seqProyecto = $seqProyecto;
                        ";
            } else if ($cant >= $exeExistentes->numRows() && ($seqProyectoComite == "" || $seqProyectoComite == 0)) {
                $arrayActasComite = Array();
                $arrayActasComite[$seqProyecto]['numActaComite'][] = $numActaComite;
                $arrayActasComite[$seqProyecto]['fchActaComite'][] = $fchActaComite;
                $arrayActasComite[$seqProyecto]['bolAproboProyecto'][] = $bolAproboProyecto;
                $arrayActasComite[$seqProyecto]['numResolucionComite'][] = $numResolucionComite;
                $arrayActasComite[$seqProyecto]['fchResolucionComite'][] = $fchResolucionComite;
                $arrayActasComite[$seqProyecto]['txtObservacionesComite'][] = $txtObservacionesComite;
                $arrayActasComite[$seqProyecto]['bolCondicionesComite'][] = $bolCondicionesComite;
                $arrayActasComite[$seqProyecto]['txtCondicionesComite'][] = $txtCondicionesComite;
                $arrayActasComite[$seqProyecto]['seqEntidadComite'][] = $seqEntidadComite;
                $this->almacenarActaComite($seqProyecto, $arrayActasComite, count($arrayActasComite));
            }
            //echo "<p>" . $query . "</p><br>";
            try {
                $aptBd->execute($query);
            } catch (Exception $objError) {
                $arrErrores[] = "No se ha podido cargar las actas de comite<b></b>";
                pr($objError->getMessage());
            }
        }
        //  die();
        if ($cant < $exeExistentes->numRows()) {
            $resultado = array_diff($datos, $datosDiff);
            $delete = "";
            foreach ($resultado as $value) {
                $delete .= $value . ",";
            }
            //  print_r($resultado);
            $delete = substr_replace($delete, '', -1, 1);
            $sql = "DELETE FROM t_pry_proyecto_comite WHERE seqProyectoComite in (" . $delete . ")";
            try {
                $aptBd->execute($sql);
            } catch (Exception $objError) {
                $arrErrores[] = "No se ha podido eliminar Acta de comite<b></b>";
                pr($objError->getMessage());
            }
        }
    }

    public function almacenarSeguimientoFicha($post) {

        global $aptBd;
        $arrErrores = array();
        $numSeguimientoFicha = 0;
        $fchSeguimientoFicha = '0000-00-00';
        $bolCerrar = 0;
        $seqProyecto;
        foreach ($post as $key => $value) {
            $$key = $value;
            //  echo "<br>" . $key . " ->" . $value;
        }
        $bolCerrar != "" ? $bolCerrar = 1 : $bolCerrar = 0;

        $sql = "INSERT INTO t_pry_seguimiento_ficha
                    (
                    numSeguimientoFicha,
                    fchSeguimientoFicha,
                    bolCerrar,
                    seqProyecto)
                    VALUES
                    (
                    $numSeguimientoFicha,
                    '$fchSeguimientoFicha',
                    $bolCerrar,
                    $seqProyecto);";
        try {

            $aptBd->execute($sql);
            $seqSeguimientoFicha = $aptBd->Insert_ID();
            $query = "INSERT INTO t_pry_ficha_texto
                    (
                        txtFichaTexto,
                        fchFichaTexto,
                        seqSeguimientoFicha) VALUES";
            foreach ($txtFichaTexto as $keyText => $valueText) {
                $query .= "('$valueText', NOW(), $seqSeguimientoFicha),";
            }
            try {
                $query = substr_replace($query, ';', -1, 1);
                $aptBd->execute($query);
            } catch (Exception $ex) {
                pr($ex->getMessage());
            }
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido guardar los datos de seguimiento de la ficha consulte este error al administrador del sistema";
            pr($objError->getMessage());
        }
        return $seqSeguimientoFicha;
    }

    public function editarSeguimientoFicha($post, $cant) {

        global $aptBd;
        $bolCerrar = 0;
        $seqSeguimientoFicha = 0;

        foreach ($post as $key => $value) {
            $$key = $value;
            //  echo "<br>" . $key . " ->" . $value;
        }

        $sqlExistentes = "SELECT seqFichaTexto FROM t_pry_ficha_texto WHERE seqSeguimientoFicha = $seqSeguimientoFicha";
        $exeExistentes = $aptBd->execute($sqlExistentes);
        //$cant = $exeExistentes->numRows();
        $datos = Array();
        $datosDiff = Array();
        while ($exeExistentes->fields) {
            $datos[] = $exeExistentes->fields['seqFichaTexto'];
            $exeExistentes->MoveNext();
        }
        $bolCerrar != "" ? $bolCerrar = 1 : $bolCerrar = 0;



        $sql = "UPDATE t_pry_seguimiento_ficha
            SET
            numSeguimientoFicha = $numSeguimientoFicha,
            fchSeguimientoFicha = '$fchSeguimientoFicha',
            bolCerrar = $bolCerrar
            WHERE seqSeguimientoFicha = $seqSeguimientoFicha and seqProyecto = $seqProyecto;";
        try {
            $aptBd->execute($sql);
            $ind = 0;
            foreach ($txtFichaTexto as $keyText => $valueText) {
                $datosDiff[] = $seqFichaTexto[$ind];

                if ($seqFichaTexto[$ind] > 0 && $valueText != "") {
                    $queryUP = "UPDATE t_pry_ficha_texto
                        SET
                        txtFichaTexto = '$valueText'
                        WHERE seqFichaTexto = $seqFichaTexto[$ind]
                        AND seqSeguimientoFicha = $seqSeguimientoFicha";
                    $aptBd->execute($queryUP);
                } else if ($seqFichaTexto[$ind] == "" && $valueText != "") {
                    $query = "INSERT INTO t_pry_ficha_texto
                    (
                        txtFichaTexto,
                        fchFichaTexto,
                        seqSeguimientoFicha) VALUES";
                    try {

                        $query .= "('$valueText', NOW(), $seqSeguimientoFicha);";

                        $aptBd->execute($query);
                    } catch (Exception $ex) {
                        pr($ex->getMessage());
                    }
                }
                $ind++;
            }

            if ($cant < $exeExistentes->numRows()) {
                $resultado = array_diff($datos, $datosDiff);
                $delete = "";
                foreach ($resultado as $value) {
                    $delete .= $value . ",";
                }
                //  print_r($resultado);
                $delete = substr_replace($delete, '', -1, 1);
                $sql = "DELETE FROM t_pry_ficha_texto WHERE seqFichaTexto in (" . $delete . ")";
                try {
                    $aptBd->execute($sql);
                } catch (Exception $objError) {
                    $arrErrores[] = "No se ha podido eliminar El texto de la ficha<b></b>";
                    pr($objError->getMessage());
                }
            }
        } catch (Exception $ex) {
            $arrErrores[] = "No se ha podido modificar los datos de seguimiento de la ficha consulte este error al administrador del sistema";
            pr($objError->getMessage());
        }
    }

    function obtenerDatosSeguimiento($idProyectos) {

        global $aptBd;
        $sql = "SELECT  t_pry_ficha_texto.* FROM "
                . "t_pry_seguimiento_ficha LEFT JOIN t_pry_ficha_texto USING(seqSeguimientoFicha)";
        if ($idProyectos > 0) {
            $sql .=" where seqProyecto =" . $idProyectos . " AND fchSeguimientoFicha = (select max(fchSeguimientoFicha)"
                    . " FROM t_pry_ficha_texto where seqProyecto = $idProyectos   AND bolCerrar = 1 ORDER BY fchSeguimientoFicha DESC limit 1)";
        }
        $sql .=" ORDER BY fchSeguimientoFicha DESC ";
        $objRes = $aptBd->execute($sql);
        $datos = Array();
        while ($objRes->fields) {
            $datos[] = $objRes->fields;
            $objRes->MoveNext();
        }
        return $datos;
    }

    public function almacenarInformeInterventoria($post, $file) {

        global $aptBd;
        $arrErrores = array();
        $numPorcentajeEjecucion = 0;
        $fchCreacionInforme = 'NOW()';
        $seqProyecto;
        $txtNombreArchivo = $file;


        foreach ($post as $key => $value) {
            $$key = $value;
            //  echo "<br>" . $key . " ->" . $value;
        }
        $sql = "INSERT INTO t_pry_informe_interventoria
                    (
                    txtInformeInterventoria,
                    fchInformeInterventoria,
                    fchCreacionInforme,
                    txtNombreArchivo,
                    numPorcentajeEjecucion,
                    valEjecutado,
                    seqProyecto)
                    VALUES
                    (
                    '$txtInformeInterventoria',
                    '$fchInformeInterventoria',
                    $fchCreacionInforme,
                    '$txtNombreArchivo',
                    $numPorcentajeEjecucion,
                    $valEjecutado,
                    $seqProyecto);";
        try {
            $aptBd->execute($sql);
            $seqInformeInterventoria = $aptBd->Insert_ID();
            $query = "INSERT INTO t_pry_interventoria_texto
                    (
                        txtObservaciones,
                        fchTexto,
                        seqInformeInterventoria) VALUES";
            foreach ($txtObservaciones as $keyText => $valueText) {
                $query .= "('$valueText', NOW(), $seqInformeInterventoria),";
            }
            try {
                $query = substr_replace($query, ';', -1, 1);
                $aptBd->execute($query);
            } catch (Exception $ex) {
                pr($ex->getMessage());
            }
        } catch (Exception $objError) {
            $arrErrores[] = "No se ha podido guardar los datos de seguimiento de la ficha consulte este error al administrador del sistema";
            pr($objError->getMessage());
        }
        return $seqInformeInterventoria;
    }

    public function editarDatosInterventoria($post, $cant) {

        global $aptBd;

        $seqInformeInterventoria = 0;

        foreach ($post as $key => $value) {
            $$key = $value;
            //  echo "<br>" . $key . " ->" . $value;
        }

        $sqlExistentes = "SELECT seqInterventoriaTexto FROM t_pry_interventoria_texto WHERE seqInformeInterventoria = $seqInformeInterventoria";
        $exeExistentes = $aptBd->execute($sqlExistentes);
        //$cant = $exeExistentes->numRows();
        $datos = Array();
        $datosDiff = Array();
        while ($exeExistentes->fields) {
            $datos[] = $exeExistentes->fields['seqInterventoriaTexto'];
            $exeExistentes->MoveNext();
        }
        $sql = "UPDATE t_pry_informe_interventoria
            SET
            txtInformeInterventoria = '$txtInformeInterventoria',
            fchInformeInterventoria = '$fchInformeInterventoria',
            numPorcentajeEjecucion = $numPorcentajeEjecucion,
            valEjecutado = $valEjecutado          
            WHERE seqInformeInterventoria = $seqInformeInterventoria and seqProyecto = $seqProyecto;";
        try {
            // echo "<p>" . $sql . "</p>";
            $aptBd->execute($sql);
            $ind = 0;
            foreach ($txtObservaciones as $keyText => $valueText) {
                $datosDiff[] = $seqInterventoriaTexto[$ind];

                if ($seqInterventoriaTexto[$ind] > 0 && $valueText != "") {
                    $queryUP = "UPDATE t_pry_interventoria_texto
                        SET
                        txtObservaciones = '$valueText'
                        WHERE seqInterventoriaTexto = $seqInterventoriaTexto[$ind]
                        AND seqInformeInterventoria = $seqInformeInterventoria";
                    $aptBd->execute($queryUP);
                } else if ($seqInterventoriaTexto[$ind] == "" && $valueText != "") {
                    $query = "INSERT INTO t_pry_interventoria_texto
                    (
                        txtObservaciones,
                        fchTexto,
                        seqInformeInterventoria) VALUES";
                    try {
                        //echo "<p>" . $query . "</p>";
                        $query .= "('$valueText', NOW(), $seqInformeInterventoria);";
                        $aptBd->execute($query);
                    } catch (Exception $ex) {
                        pr($ex->getMessage());
                    }
                }
                $ind++;
            }

            if ($cant < $exeExistentes->numRows()) {
                $resultado = array_diff($datos, $datosDiff);
                $delete = "";
                foreach ($resultado as $value) {
                    $delete .= $value . ",";
                }
                //  print_r($resultado);
                $delete = substr_replace($delete, '', -1, 1);
                $sql = "DELETE FROM t_pry_interventoria_texto WHERE seqInterventoriaTexto in (" . $delete . ")";
                try {
                    $aptBd->execute($sql);
                } catch (Exception $objError) {
                    $arrErrores[] = "No se ha podido eliminar El texto de la ficha<b></b>";
                    pr($objError->getMessage());
                }
            }
        } catch (Exception $ex) {
            $arrErrores[] = "No se ha podido modificar los datos de seguimiento de la ficha consulte este error al administrador del sistema";
            pr($objError->getMessage());
        }
    }

    public function obtenerDatosProyectosDependencia() {

        global $aptBd;
        $sql = "select (select count(*)  from t_pry_proyecto where seqProyectoGrupo = 1 and bolActivo = 1  AND (seqProyectoPadre = 0
        OR seqProyectoPadre IS NULL)) as canProySdht,
        (select count(*)  from t_pry_proyecto where seqProyectoGrupo in(2)  and bolActivo = 1 AND (seqProyectoPadre = 0
        OR seqProyectoPadre IS NULL)) as canProyPublicos,
        (select count(*)  from t_pry_proyecto 
        left join t_pry_unidad_proyecto und using(seqProyecto)
        where seqProyectoGrupo = 1 ) as undProySdht,
        (select count(*)  from t_pry_proyecto 
        left join t_pry_unidad_proyecto und using(seqProyecto)
        where seqProyectoGrupo in(2) ) as undProyPublicos
         from t_pry_proyecto pry
         left join t_pry_proyecto_grupo using(seqProyectoGrupo)
         limit 1";

        /* and und.seqFormulario is not NULL AND und.seqFormulario != '' and und.seqFormulario > 0 and tec.txtExistencia = 'SI' */
        //echo $sql;
        $objRes = $aptBd->execute($sql);
        $datos = Array();
        while ($objRes->fields) {
            $datos[] = $objRes->fields;
            $objRes->MoveNext();
        }
        return $datos;
    }

    public function obtenerDatosProyectosTableroPal($seqProyectoGrupo) {

        global $aptBd;
        $sql = "select seqPryEstadoProceso, txtPryEstadoProceso, count(pry.seqProyecto) as cantidad, 
            (select count(seqUnidadProyecto)  from t_pry_unidad_proyecto und 
            left join t_pry_proyecto pry1 on (und.seqProyecto = pry1.seqProyecto or und.seqProyecto = pry1.seqProyectoPadre)
            left join t_pry_tecnico tec using(seqUnidadProyecto)
            LEFT JOIN T_FRM_TIPO_FINANCIACION USING(seqTipoFinanciacion)
            where  pry1.seqPryEstadoProceso = pry.seqPryEstadoProceso  and und.bolActivo = 1 AND seqProyectoGrupo IN (" . $seqProyectoGrupo . ") ) as unidades
             from t_pry_proyecto pry
            left join t_pry_estado_proceso using(seqPryEstadoProceso) 
            LEFT JOIN
            T_FRM_TIPO_FINANCIACION USING (seqTipoFinanciacion)
            WHERE pry.seqProyectoGrupo in (" . $seqProyectoGrupo . ")";
//          if ($seqProyectoGrupo == 1) {
        $sql .= " AND (seqProyectoPadre =  0 or seqProyectoPadre is null) and pry.bolActivo = 1 ";
//          }
        $sql .= " group by seqPryEstadoProceso order by seqPryEstadoProceso DESC ";

        /* and und.seqFormulario is not NULL AND und.seqFormulario != '' and und.seqFormulario > 0 and tec.txtExistencia = 'SI' */
        //echo $sql;
        $objRes = $aptBd->execute($sql);
        $datos = Array();
        while ($objRes->fields) {
            $datos[] = $objRes->fields;
            $objRes->MoveNext();
        }
        return $datos;
    }

    public function obtenerDatosProyectosEstados($seqPryEstadoProceso, $seqProyectoGrupo) {

        global $aptBd;
        $sql = "SELECT 
                        seqProyecto,
                        txtNombreProyecto,
                        seqPlanGobierno,
                        CASE
                            WHEN
                                txtNombreConstructor IS NULL
                            THEN
                                (SELECT 
                                        GROUP_CONCAT(txtNombreOferente
                                                SEPARATOR ', ')
                                    FROM
                                        t_pry_proyecto_oferente pOf
                                            LEFT JOIN
                                        t_pry_entidad_oferente entO USING (seqOferente)
                                    WHERE
                                        pry.seqProyecto = pOf.seqProyecto)
                            ELSE txtNombreConstructor
                        END AS constructor,
                        txtLocalidad,
                        (SELECT 
                            COUNT(seqUnidadProyecto)
                        FROM
                            t_pry_unidad_proyecto und
                                LEFT JOIN
                            t_pry_proyecto pry1 ON (und.seqProyecto = pry1.seqProyecto
                                OR und.seqProyecto = pry1.seqProyectoPadre)                                
                        WHERE
                            #pry1.seqPryEstadoProceso = pry.seqPryEstadoProceso and
                             und.bolActivo = 1
                                AND seqProyectoGrupo IN (" . $seqProyectoGrupo . ") and 
                                (und.seqProyecto = pry.seqProyecto or und.seqProyecto = pry.seqProyectoPadre or pry1.seqProyectoPadre = pry.seqProyecto)) 
                                AS unidades,
                            (SELECT 
                                COUNT(*) AS cant
                            FROM
                                T_PRY_UNIDAD_PROYECTO und
                                    LEFT JOIN
                                t_frm_formulario frm USING (seqFormulario)
                                    LEFT JOIN
                                t_pry_proyecto pry1 ON (und.seqProyecto = pry1.seqProyecto)
                            WHERE
                             #pry1.seqPryEstadoProceso = pry.seqPryEstadoProceso AND
                              seqProyectoGrupo IN (" . $seqProyectoGrupo . ")
                                    AND (und.seqProyecto = pry.seqProyecto
                                    OR und.seqProyecto = pry.seqProyectoPadre
                                    OR pry1.seqProyectoPadre = pry.seqProyecto) and
                                (frm.bolCerrado = 0
                                    OR (und.seqFormulario IS NULL
                                    OR und.seqFormulario = 0)
                                    AND und.bolActivo = 1) ) AS pendientes,
                        (SELECT 
                            COUNT(*) AS cant
                        FROM
                            t_pry_unidad_proyecto und
                                INNER JOIN
                            t_frm_formulario frm USING (seqFormulario)
                                LEFT JOIN
                            t_pry_proyecto pry1 ON (und.seqProyecto = pry1.seqProyecto)
                        WHERE
                         #pry1.seqPryEstadoProceso = pry.seqPryEstadoProceso AND
                                 seqProyectoGrupo IN (" . $seqProyectoGrupo . ")
                                AND (und.seqProyecto = pry.seqProyecto
                                OR und.seqProyecto = pry.seqProyectoPadre
                                OR pry1.seqProyectoPadre = pry.seqProyecto)
                                AND seqEstadoProceso = 40 AND bolCerrado = 1) AS undLegalizadadas,                                  
                        (SELECT 
                            COUNT(*) AS cant
                        FROM
                            T_PRY_UNIDAD_PROYECTO und
                                LEFT JOIN
                            t_frm_formulario frm USING (seqFormulario)
                                LEFT JOIN
                            t_pry_proyecto pry1 ON (und.seqProyecto = pry1.seqProyecto)
                        WHERE
                        #pry1.seqPryEstadoProceso = pry.seqPryEstadoProceso AND
                                 seqProyectoGrupo IN (" . $seqProyectoGrupo . ")
                                AND (und.seqProyecto = pry.seqProyecto
                                OR und.seqProyecto = pry.seqProyectoPadre
                                OR pry1.seqProyectoPadre = pry.seqProyecto)
                                AND 
                            frm.bolCerrado = 1
                                AND und.seqFormulario IS NOT NULL
                                AND (seqEstadoProceso = 15
                                OR seqEstadoProceso = 62
                                OR seqEstadoProceso = 17
                                OR seqEstadoProceso = 19
                                OR seqEstadoProceso = 22
                                OR seqEstadoProceso = 23
                                    OR seqEstadoProceso = 25
                                    OR seqEstadoProceso = 26
                                OR seqEstadoProceso = 27
                                OR seqEstadoProceso = 28
                                OR seqEstadoProceso = 31
                                OR seqEstadoProceso = 29
                                OR seqEstadoProceso = 40)
                                AND und.bolActivo = 1) AS vinculadas,
                                (SELECT count(*) as cant FROM T_PRY_UNIDAD_PROYECTO und
                                LEFT JOIN t_frm_formulario frm USING(seqFormulario) 
                                 LEFT JOIN
                            t_pry_proyecto pry1 ON (und.seqProyecto = pry1.seqProyecto)
                                WHERE 
                               # pry1.seqPryEstadoProceso = pry.seqPryEstadoProceso AND  
                                (und.seqProyecto = pry.seqProyecto
                                 AND seqProyectoGrupo IN (" . $seqProyectoGrupo . ") AND
                                frm.bolCerrado =1  and und.seqFormulario is not null
                                and (seqEstadoProceso = 7 OR seqEstadoProceso = 54 OR 
                                seqEstadoProceso = 16 OR seqEstadoProceso = 47 OR seqEstadoProceso = 56) 
                                and und.bolActivo =1)) as postuladas,
                        txtTipoFinanciacion,
                        (select count(*) AS cantHijos from t_pry_proyecto where seqProyectoPadre = pry.seqProyecto)as cantHijos
                    FROM
                        t_pry_proyecto pry
                            LEFT JOIN
                        t_pry_estado_proceso USING (seqPryEstadoProceso)
                            LEFT JOIN
                        t_pry_constructor con USING (seqConstructor)
                            LEFT JOIN
                        t_frm_localidad loc USING (seqLocalidad)
                            LEFT JOIN
                        t_pry_proyecto_grupo gru USING (seqProyectoGrupo)
                            LEFT JOIN
                        T_FRM_TIPO_FINANCIACION USING (seqTipoFinanciacion)
                    WHERE
                        seqPryEstadoProceso = $seqPryEstadoProceso
                            AND pry.seqProyectoGrupo IN (" . $seqProyectoGrupo . ")";
        //  if ($seqProyectoGrupo == 1) {
        $sql .= " AND (seqProyectoPadre = 0
                            OR seqProyectoPadre IS NULL)";
        //    }
        $sql .= " GROUP BY seqProyecto
                    ORDER BY seqProyecto ASC ";
        //echo "<p>".$sql."</p>";
        $objRes = $aptBd->execute($sql);

        $datos = Array();
        while ($objRes->fields) {
            $datos[] = $objRes->fields;
            $objRes->MoveNext();
        }
        //var_dump($datos);
        return $datos;
    }

    public function determinarSiEsPadre($seqProyecto) {
        global $aptBd;
        $sql = "select count(*) AS cantHijos from t_pry_proyecto where seqProyectoPadre =  " . $seqProyecto;
        $objRes = $aptBd->execute($sql);
        return $objRes->fields['cantHijos'];
    }

    public function obtenerDatosProyectosIndividual($seqProyecto) {

        global $aptBd;
        $sql = "select pry.seqProyecto, pry.txtNombreProyecto, 
            case when txtNombreConstructor is null 
            then (select txtNombreConstructor from t_pry_proyecto pry1
            LEFT JOIN t_pry_constructor USING (seqConstructor) where pry1.seqProyecto = pry.seqProyectoPadre) 
             else txtNombreConstructor end as txtNombreConstructor , txtLocalidad, txtTipoFinanciacion, "
                . "(select count(*) as cant from t_pry_unidad_proyecto und1 where und1.seqProyecto = pry.seqProyecto and und1.bolActivo =1) as unidades,"
                . "(SELECT count(*) as cant FROM T_PRY_UNIDAD_PROYECTO und1
                    LEFT JOIN t_frm_formulario frm USING(seqFormulario)                    
                    WHERE (frm.bolCerrado =0  OR (und1.seqFormulario IS NULL OR  und1.seqFormulario = 0) and und1.bolActivo =1) and und1.seqProyecto = pry.seqProyecto)  as pendientes, "
                . "(SELECT count(*) as cant FROM T_PRY_UNIDAD_PROYECTO und1
                    LEFT JOIN t_frm_formulario frm USING(seqFormulario)                     
                    WHERE frm.bolCerrado =1  and und1.seqFormulario is not null and und1.seqProyecto = pry.seqProyecto
                    and (seqEstadoProceso = 7 OR seqEstadoProceso = 54 OR seqEstadoProceso = 16 OR seqEstadoProceso = 47 OR seqEstadoProceso = 56) and und1.bolActivo =1) As postuladas, "
                . "(SELECT count(*) as cant FROM t_pry_unidad_proyecto und1
                    INNER JOIN t_frm_formulario frm USING (seqFormulario)                    
                     WHERE seqEstadoProceso = 40 AND bolCerrado = 1 and und1.seqProyecto = pry.seqProyecto) AS legalizadas, "
                . "(SELECT count(*) as cant FROM T_PRY_UNIDAD_PROYECTO und1
                    LEFT JOIN t_frm_formulario frm USING(seqFormulario)                     
                    WHERE frm.bolCerrado =1  and und1.seqFormulario is not null
                    and (seqEstadoProceso = 15 OR seqEstadoProceso = 62 OR seqEstadoProceso = 17
                    OR seqEstadoProceso = 19 OR seqEstadoProceso = 22 OR seqEstadoProceso = 23 OR seqEstadoProceso = 25
                    OR seqEstadoProceso = 26 OR seqEstadoProceso = 27 OR seqEstadoProceso = 28 OR seqEstadoProceso = 31
                    OR seqEstadoProceso = 29 OR seqEstadoProceso = 40) and und1.bolActivo =1 and und1.seqProyecto = pry.seqProyecto) as vinculadas "
                . "from t_pry_proyecto pry"
                . " left join t_pry_constructor using(seqConstructor) "
                . " LEFT  JOIN t_frm_localidad  USING(seqLocalidad)"
                . " LEFT JOIN T_FRM_TIPO_FINANCIACION USING (seqTipoFinanciacion) "
                . " where pry.seqProyectoPadre = " . $seqProyecto;
        //echo "<p>".$sql."</p>";
        $objRes = $aptBd->execute($sql);

        $datos = Array();
        while ($objRes->fields) {
            $datos[] = $objRes->fields;
            $objRes->MoveNext();
        }
        //var_dump($datos);
        return $datos;
    }

    // Fin clase 
}

?>