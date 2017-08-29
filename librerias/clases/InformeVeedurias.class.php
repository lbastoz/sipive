<?php

/**
 * Created by PhpStorm.
 * User: bzerdar
 * Date: 15/08/2017
 * Time: 05:57 PM
 */
class InformeVeedurias
{

    public $arrErrores;
    public $seqCorte;
    public $txtCorte;
    public $fchCorte;
    public $txtNombre;
    private $arrEstadosVinculado;
    private $arrEstadosLegalizado;

    function __construct()
    {
        $this->arrErrores = array();
        $this->seqCorte = 0;
        $this->txtCorte = "";
        $this->fchCorte = null;
        $this->txtNombre = "";
        $this->arrEstadosVinculado = array( 15, 62, 17, 19, 22, 23, 24, 25, 26, 27, 28, 29, 31 , 40 );
        $this->arrEstadosLegalizado = array( 40 );
    }

    public function obtenerCortes($seqCorte = 0)
    {
        global $aptBd;
        $txtCondicion = ( $seqCorte == 0 )? "" : "WHERE seqCorte = " . $seqCorte;
        $sql = "
            SELECT 
              seqCorte,
              txtCorte,
              fchCorte,
              concat(usu.txtNombre, ' ', usu.txtApellido) as txtNombre
            FROM t_vee_corte cor
            inner join t_cor_usuario usu on cor.seqUsuario = usu.seqUsuario
            $txtCondicion
        ";
        return $aptBd->GetAll($sql);
    }

    public function reporteProyectos($seqCorte)
    {
        global $aptBd;

        $sql = "
            select
                pry1.txtNombreProyecto as txtNombreProyectoPadre, 
                pry1.numNitProyecto as txtNitProyectoPadre, 
                loc1.txtLocalidad as txtLocalidadPadre,
                bar1.txtBarrio as txtBarrioPadre, 
                eof11.txtNombreOferente as txtNombreOferentePadre1,
                eof11.numNitOferente as numNitOferentePadre1, 
                eof12.txtNombreOferente2 as txtNombreOferentePadre2,
                eof12.numNitOferente2 as numNitOferentePadre2, 
                eof13.txtNombreOferente3 as txtNombreOferentePadre3,
                eof13.numNitOferente3 as numNitOferentePadre3, 
                con1.txtNombreConstructor as txtNombreConstructorPadre,
                con1.numDocumentoConstructor as numDocumentoConstructorPadre,
                pry1.txtNombreVendedor as txtNombreVendedorPadre, 
                pry1.numNitVendedor as txtNitVendedorPadre,
                pry.txtNombreProyecto as txtNombreProyectoHijo,
                pry.numNitProyecto as numNitProyectoHijo, 
                loc2.txtLocalidad as txtLocalidadHijo,
                bar2.txtBarrio as txtBarrioHijo,
                eof.txtNombreOferente as txtNombreOferenteHijo1,
                eof.numNitOferente as numNitOferenteHijo1, 
                eof2.txtNombreOferente2 as txtNombreOferenteHijo2,
                eof2.numNitOferente2 as numNitOferenteHijo2, 
                eof3.txtNombreOferente3 as txtNombreOferenteHijo3,
                eof3.numNitOferente3 as numNitOferenteHijo3, 
                con2.txtNombreConstructor as txtNombreConstructorHijo,
                con2.numDocumentoConstructor as numDocumentoConstructorHijo,
                pry.txtNombreVendedor as txtNombreVendedorHijo, 
                pry.numNitVendedor as numNitVendedorHijo,
                upr.seqUnidadProyecto,
                upr.txtNombreUnidad, 
                upr.txtMatriculaInmobiliaria, 
                upr.txtChipLote, 
                upr.valSDVEAprobado, 
                upr.valSDVEActual, 
                upr.valSDVEComplementario, 
                upr.bolLegalizado,
                if(upr.bolLegalizado = 1,'SI','NO') as bolLegalizado,
                upr.fchLegalizado,  
                pgo.txtPlanGobierno,
                upr.seqModalidad, 
                moa.txtModalidad,
                upr.seqTipoEsquema,
                tes.txtTipoEsquema,
                uac.numActo as numActoProyecto, 
                uac.fchActo as fchActoProyecto,
                if(upr.bolActivo = 1,'SI','NO') as bolActivo,
                uac.seqTipoActoUnidad,
                tac.txtTipoActoUnidad,
                uvi.valIndexado,
                frm.seqFormulario,
                frm.seqEstadoProceso,
                frm.bolCerrado,
                aad.numActo as numActoHogar, 
                aad.fchActo as fchActoHogar
            from t_vee_proyecto pry
            left join t_pry_proyecto pry1 on pry.seqProyectoPadre = pry1.seqProyecto and pry.seqCorte
            inner join t_vee_unidad_proyecto upr on pry.seqProyectoVeeduria = upr.seqProyectoVeeduria
            inner join t_vee_unidades_vinculadas uvi on upr.seqUnidadProyectoVeeduria = uvi.seqUnidadProyectoVeeduria
            inner join t_vee_unidad_acto uac on uvi.seqUnidadActoVeeduria = uac.seqUnidadActoVeeduria
            inner join t_pry_aad_unidad_tipo_acto tac on uac.seqTipoActoUnidad = tac.seqTipoActoUnidad
            left join t_vee_formulario frm on upr.seqFormulario = frm.seqFormulario and frm.seqCorte = $seqCorte
            left join (
              select
              frm.seqFormulario, 
              hvi.numActo, 
              hvi.fchActo
              from t_aad_hogares_vinculados hvi
              inner join (
                select 
                fac.seqFormulario,
                max(fac.seqFormularioActo) as seqFormularioActo
                from t_aad_hogares_vinculados hvi 
                inner join t_aad_formulario_acto fac on hvi.seqFormularioActo = fac.seqFormularioActo
                where hvi.seqTipoActo = 1
                group by fac.seqFormulario
              ) frm on hvi.seqFormularioActo = frm.seqFormularioActo
            ) aad on frm.seqFormulario = aad.seqFormulario
            inner join t_frm_plan_gobierno pgo on upr.seqPlanGobierno = pgo.seqPlanGobierno
            inner join t_frm_modalidad moa on moa.seqModalidad = upr.seqModalidad
            inner join t_pry_tipo_esquema tes on upr.seqTipoEsquema = tes.seqTipoEsquema
            left  join t_frm_localidad loc1 on pry1.seqLocalidad = loc1.seqLocalidad
            left  join t_frm_barrio bar1 on pry1.seqBarrio = bar1.seqBarrio
            left  join t_pry_entidad_oferente eof11 on pry1.seqProyecto = eof11.seqProyecto
            left  join t_pry_entidad_oferente eof12 on pry1.seqProyecto = eof12.seqProyecto
            left  join t_pry_entidad_oferente eof13 on pry1.seqProyecto = eof13.seqProyecto
            left  join t_pry_entidad_oferente eof on pry.seqProyecto = eof.seqProyecto
            left  join t_pry_entidad_oferente eof2 on pry.seqProyecto = eof2.seqProyecto
            left  join t_pry_entidad_oferente eof3 on pry.seqProyecto = eof3.seqProyecto
            left  join t_pry_constructor con1 on pry1.seqConstructor = con1.seqConstructor
            left  join t_pry_constructor con2 on pry.seqConstructor = con2.seqConstructor
            left  join t_frm_localidad loc2 on pry.seqLocalidad = loc2.seqLocalidad
            left  join t_frm_barrio bar2 on pry.seqBarrio = bar2.seqBarrio
            where pry.seqCorte = $seqCorte
            and pry.bolActivo = 1
            -- and upr.bolActivo = 1
            order by pry.txtNombreProyecto, uac.seqTipoActoUnidad
        ";
        $objRes = $aptBd->execute($sql);
        $arrReporte = array();
        $numAnioMinimoGenerado = 0;
        $numAnioMaximoGenerado = 0;
        $numAnioMinimoVinculado = 0;
        $numAnioMaximoVinculado = 0;
        $numAnioMinimoLegalizado = 0;
        $numAnioMaximoLegalizado = 0;
        $arrFormularios = array();
        while ($objRes->fields) {

            /***************************************************************************
             * PROCESAMIENTO DE LA HOJA DE REPORTE
             ***************************************************************************/
            $seqUnidadProyecto = $objRes->fields['seqUnidadProyecto'];

            // debe acumular sobre proyecto padre cuando aplique
            $txtProyecto = ( trim( $objRes->fields['txtNombreProyectoPadre'] ) != "" )? $objRes->fields['txtNombreProyectoPadre'] : $objRes->fields['txtNombreProyectoHijo'];

            // Determina el año menor y mayor para imprimir el numero de columnas correcto en el excel (XML)
            $numAnioResolucionProyecto = date("Y", strtotime($objRes->fields['fchActoProyecto']));

            $numAnioMinimoGenerado = (($numAnioMinimoGenerado == 0) or ($numAnioMinimoGenerado >= $numAnioResolucionProyecto)) ? $numAnioResolucionProyecto : $numAnioMinimoGenerado;
            $numAnioMaximoGenerado = (($numAnioMaximoGenerado == 0) or ($numAnioMaximoGenerado <= $numAnioResolucionProyecto)) ? $numAnioResolucionProyecto : $numAnioMaximoGenerado;
            $arrReporte['reporte']['generados']['minimo'] = $numAnioMinimoGenerado;
            $arrReporte['reporte']['generados']['maximo'] = $numAnioMaximoGenerado;

            if( $objRes->fields['seqTipoActoUnidad'] == 1 ){
                $txtNombreResolucion = $objRes->fields['numActoProyecto'] . " de " . date("Y", strtotime($objRes->fields['fchActoProyecto']));
            }

            // de acuerdo al tipo de aad de proyecto suma o resta
            // para las indexaciones va calculando el valor de la unidad
            switch( $objRes->fields['seqTipoActoUnidad'] ){
                case 1: // Asignacion de unidades
                    $arrReporte['reporte']['generados']['datos'][$txtProyecto][$txtNombreResolucion][$numAnioResolucionProyecto]++;
                    $arrReporte['reporte']['generados']['datos'][$txtProyecto][$txtNombreResolucion]['total']++;
                    $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'valIndexado' ] += $objRes->fields['valIndexado'];
                    break;
                case 2: // Indexacion de unidades

                    // calcular el valor definitivo de la unidad
                    $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'valIndexado' ] += $objRes->fields['valIndexado'];

                    break;
                case 3: // modificatoria (valor positivo incluye unidades // valor negativo excluye unidades)

                    if( $objRes->fields['valIndexado'] > 0 ){
                        $arrReporte['reporte']['generados']['datos'][$txtProyecto][$txtNombreResolucion][$numAnioResolucionProyecto]++;
                        $arrReporte['reporte']['generados']['datos'][$txtProyecto][$txtNombreResolucion]['total']++;
                    }else{
                        $arrReporte['reporte']['generados']['datos'][$txtProyecto][$txtNombreResolucion][$numAnioResolucionProyecto] =
                            intval($arrReporte['reporte']['generados']['datos'][$txtProyecto][$txtNombreResolucion][$numAnioResolucionProyecto]) - 1;
                        $arrReporte['reporte']['generados']['datos'][$txtProyecto][$txtNombreResolucion]['total']--;
                    }
                    $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'valIndexado' ] += $objRes->fields['valIndexado'];
                    break;
            }

            $seqFormulario = $objRes->fields['seqFormulario'];

            // conteo para las columnas de vinculados segun el estado del proceso
            if( $objRes->fields['bolCerrado'] == 1 and in_array( $objRes->fields['seqEstadoProceso'] , $this->arrEstadosVinculado ) and ! isset( $arrFormularios[$seqFormulario] ) ) {
                $numAnioResolucionHogar = date("Y", strtotime($objRes->fields['fchActoHogar']));
                $numAnioMinimoVinculado = (($numAnioMinimoVinculado == 0) or ($numAnioMinimoVinculado >= $numAnioResolucionHogar)) ? $numAnioResolucionHogar : $numAnioMinimoVinculado;
                $numAnioMaximoVinculado = (($numAnioMaximoVinculado == 0) or ($numAnioMaximoVinculado <= $numAnioResolucionHogar)) ? $numAnioResolucionHogar : $numAnioMaximoVinculado;
                $arrReporte['reporte']['vinculados']['minimo'] = $numAnioMinimoVinculado;
                $arrReporte['reporte']['vinculados']['maximo'] = $numAnioMaximoVinculado;
                $arrReporte['reporte']['vinculados']['datos'][$txtProyecto][$txtNombreResolucion][$numAnioResolucionHogar]++;
                $arrReporte['reporte']['vinculados']['datos'][$txtProyecto][$txtNombreResolucion]['total']++;
            }

            // conteo para las columnas de legalizados segun el estado del proceso
            if( $objRes->fields['bolCerrado'] == 1 and in_array( $objRes->fields['seqEstadoProceso'] , $this->arrEstadosLegalizado ) and ! isset( $arrFormularios[$seqFormulario] ) ){
                $numAnioLegalizado = date("Y", strtotime($objRes->fields['fchLegalizado']));
                $numAnioMinimoLegalizado = (($numAnioMinimoLegalizado == 0) or ($numAnioMinimoLegalizado >= $numAnioLegalizado)) ? $numAnioLegalizado : $numAnioMinimoLegalizado;
                $numAnioMaximoLegalizado = (($numAnioMaximoLegalizado == 0) or ($numAnioMaximoLegalizado <= $numAnioLegalizado)) ? $numAnioLegalizado : $numAnioMaximoLegalizado;
                $arrReporte['reporte']['legalizados']['minimo'] = $numAnioMinimoLegalizado;
                $arrReporte['reporte']['legalizados']['maximo'] = $numAnioMaximoLegalizado;
                $arrReporte['reporte']['legalizados']['datos'][$txtProyecto][$txtNombreResolucion][$numAnioLegalizado]++;
                $arrReporte['reporte']['legalizados']['datos'][$txtProyecto][$txtNombreResolucion]['total']++;
            }

            // se usa mas adelante para completar la informacion de la hoja de hogares
            if( intval( $seqFormulario ) != 0 ) {
                $arrFormularios[$seqFormulario]['numResolucion'] = $objRes->fields['numActoHogar'];
                $arrFormularios[$seqFormulario]['fchResolucion'] = $objRes->fields['fchActoHogar'];
            }

            // Prepara los datos para la hoja de proyectos
            $txtNombreProyectoPadre = (trim($objRes->fields['txtNombreProyectoPadre']) != "")? trim($objRes->fields['txtNombreProyectoPadre']) : trim($objRes->fields['txtNombreProyectoHijo']);
            $txtNombreProyectoHijo  = (trim($objRes->fields['txtNombreProyectoHijo']) != "")? trim($objRes->fields['txtNombreProyectoHijo']) : "No Aplica";
            $txtNitProyecto         = (trim($objRes->fields['txtNitProyectoPadre']) != "")? trim( $objRes->fields['txtNitProyectoPadre'] ) : trim( $objRes->fields['txtNitProyectoHijo'] );
            $txtLocalidad           = (trim($objRes->fields['txtLocalidadPadre']) != "")?  trim($objRes->fields['txtLocalidadPadre']) : trim($objRes->fields['txtLocalidadHijo']);
            $txtBarrio              = (trim($objRes->fields['txtBarrioPadre']) != "")? trim($objRes->fields['txtBarrioPadre']) : trim($objRes->fields['txtBarrioHijo']);
            $txtNombreOferente1     = (trim($objRes->fields['txtNombreOferentePadre1']) != "")? trim($objRes->fields['txtNombreOferentePadre1']) : trim($objRes->fields['txtNombreOferenteHijo1']);
            $numNitOferente1        = (trim($objRes->fields['numNitOferentePadre1']) != "")? trim($objRes->fields['numNitOferentePadre1']) : trim($objRes->fields['numNitOferenteHijo1']);
            $txtNombreOferente2     = (trim($objRes->fields['txtNombreOferentePadre2']) != "")? trim($objRes->fields['txtNombreOferentePadre2']) : trim($objRes->fields['txtNombreOferenteHijo2']);
            $numNitOferente2        = (trim($objRes->fields['numNitOferentePadre2']) != "")? trim($objRes->fields['numNitOferentePadre2']) : trim($objRes->fields['numNitOferenteHijo2']);
            $txtNombreOferente3     = (trim($objRes->fields['txtNombreOferentePadre3']) != "")? trim($objRes->fields['txtNombreOferentePadre3']) : trim($objRes->fields['txtNombreOferenteHijo3']);
            $numNitOferente3        = (trim($objRes->fields['numNitOferentePadre3']) != "")? trim($objRes->fields['numNitOferentePadre3']) : trim($objRes->fields['numNitOferenteHijo3']);
            $txtNombreConstructor   = (trim($objRes->fields['txtNombreConstructorPadre']) != "")? trim($objRes->fields['txtNombreConstructorPadre']) : trim($objRes->fields['txtNombreConstructorHijo']);
            $txtNombreVendedor      = (trim($objRes->fields['txtNombreVendedorPadre']) != "")? trim($objRes->fields['txtNombreVendedorPadre']) : trim($objRes->fields['txtNombreVendedorHijo']);
            $numNitVendedor         = (trim($objRes->fields['txtNitVendedorPadre']) != "")? trim($objRes->fields['txtNitVendedorPadre']) : trim($objRes->fields['txtNitVendedorHijo']);

            /***************************************************************************
             * PROCESAMIENTO DE LA HOJA DE PROYECTOS
             ***************************************************************************/

            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'seqUnidadProyecto' ]  = $seqUnidadProyecto;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Proyecto Padre' ]     = $txtNombreProyectoPadre;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Proyecto Hijo' ]      = $txtNombreProyectoHijo;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Nit Proyecto' ]       = $txtNitProyecto;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Localidad Proyecto' ] = $txtLocalidad;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Barrio Proyecto' ]    = $txtBarrio;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Oferente 1' ]         = $txtNombreOferente1;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Nit Oferente 1' ]     = $numNitOferente1;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Oferente 2' ]         = $txtNombreOferente2;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Nit Oferente 2' ]     = $numNitOferente2;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Oferente 3' ]         = $txtNombreOferente3;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Nit Oferente 3' ]     = $numNitOferente3;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Constructor' ]        = $txtNombreConstructor;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Vendedor' ]           = $txtNombreVendedor;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Nit Vendedor' ]       = $numNitVendedor;
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Unidad' ]                 = $objRes->fields['txtNombreUnidad'];
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Matricula Inmobiliaria' ] = $objRes->fields['txtMatriculaInmobiliaria'];
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'CHIP' ]                   = $objRes->fields['txtChipLote'];
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'SDVE Aprobado' ]          = $objRes->fields['valSDVEAprobado'];
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'SDVE Actual' ]            = $objRes->fields['valSDVEActual'];
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'SDVE Complementario' ]    = $objRes->fields['valSDVEComplementario'];
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Valor Indexado' ]         = $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'valIndexado' ];
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Legalizado' ]             = $objRes->fields['bolLegalizado'];
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Fecha de Legalización' ]  = $objRes->fields['fchLegalizado'];
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Plan de Gobierno' ]       = $objRes->fields['txtPlanGobierno'];
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Modalidad' ]              = $objRes->fields['txtModalidad'];
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Esquema' ]                = $objRes->fields['txtTipoEsquema'];
            $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'Unidad Activa' ]          = $objRes->fields['bolActivo'];

            /***************************************************************************
             * PROCESAMIENTO DE LA HOJA DE RESOLUCIONES
             ***************************************************************************/

            $numPosicion = count( $arrReporte['resoluciones'] );
            $arrReporte['resoluciones'][ $numPosicion ]['seqUnidadProyecto'] = $seqUnidadProyecto;
            $arrReporte['resoluciones'][ $numPosicion ]['Proyecto Padre'] = $txtNombreProyectoPadre;
            $arrReporte['resoluciones'][ $numPosicion ]['Proyecto Hijo'] = $txtNombreProyectoHijo;
            $arrReporte['resoluciones'][ $numPosicion ]['Nombre Unidad'] = $objRes->fields['txtNombreUnidad'];
            $arrReporte['resoluciones'][ $numPosicion ]['Tipo Resolución'] = $objRes->fields['txtTipoActoUnidad'];
            $arrReporte['resoluciones'][ $numPosicion ]['Numero Resolución'] = $objRes->fields['numActoProyecto'];
            $arrReporte['resoluciones'][ $numPosicion ]['Fecha Resolución'] = $objRes->fields['fchActoProyecto'];
            $arrReporte['resoluciones'][ $numPosicion ]['Año Resolución'] = (esFechaValida($objRes->fields['fchActoProyecto']))?
                                                                                date("Y" , strtotime($objRes->fields['fchActoProyecto'])) :
                                                                                "";
            $arrReporte['resoluciones'][$numPosicion]['Valor Indexación'] = $objRes->fields['valIndexado'];
            switch($objRes->fields['seqTipoActoUnidad']){
                case 1:
                    $arrReporte['resoluciones'][$numPosicion]['Observación'] = "Generacion";
                    break;
                case 2:
                    $arrReporte['resoluciones'][$numPosicion]['Observación'] = ($objRes->fields['valIndexado'] > 0)? "Positiva" : "Negativa";
                    break;
                case 3:
                    $arrReporte['resoluciones'][$numPosicion]['Observación'] = ($objRes->fields['valIndexado'] > 0)? "Adiciona" : "Desvincula";
                    break;
            }

            $objRes->MoveNext();
        }

        ksort($arrReporte['reporte']['generados']['datos']);

        // obtiene los datos del hogar
        $arrReporte['hogares'] = $this->obtenerHogares($arrFormularios,$seqCorte);

        // adiciona el dato del aad del hogar
        foreach( $arrReporte['hogares'] as $numLinea => $arrDatos ){
            $seqFormulario = $arrDatos['Formulario'];
            if( isset( $arrFormularios[$seqFormulario] ) ){
                $arrReporte['hogares'][$numLinea]['Resolución'] = $arrFormularios[$seqFormulario]['numResolucion'];
                $arrReporte['hogares'][$numLinea]['Fecha'] = $arrFormularios[$seqFormulario]['fchResolucion'];
                $arrReporte['hogares'][$numLinea]['Año'] = (esFechaValida($arrFormularios[$seqFormulario]['fchResolucion']))?
                                                                date( "Y" , strtotime( $arrFormularios[$seqFormulario]['fchResolucion'] )) :
                                                                "";
            }
        }

        // quita la variable de paso de calculo del valor indexado de proyectos
        foreach( $arrReporte['proyectos'] as $seqUnidadProyecto => $arrDatos ){
            unset( $arrReporte['proyectos'][ $seqUnidadProyecto ][ 'valIndexado' ] );
        }

        return $arrReporte;
    }

    private function obtenerHogares($arrFormularios , $seqCorte )
    {
        global $aptBd;
        $sql = "
            SELECT
              frm.seqFormulario as Formulario,
              eta.txtEtapa as Etapa,
              epr.txtEstadoProceso as Estado,
              pgo.txtPlanGobierno as 'Plan de Gobierno',
              IF(frm.seqModalidad <> 0, moa.txtModalidad,'No Disponible') as Modalidad,
              IF(frm.seqTipoEsquema is not null, tes.txtTipoEsquema,'No Disponible') as Esquema,
              sol.txtSolucion as Solucion, 
              sol.txtDescripcion as 'Descripcion de la Solucion',
              frm.fchInscripcion as 'Fecha de Inscripcion',
              frm.fchPostulacion as 'Fecha de Postulacion',
              frm.fchUltimaActualizacion as 'Ultima Actualizacion',
              frm.fchVencimiento as 'Fecha de Vencimiento',
              IF(frm.bolCerrado = 1, 'SI', 'NO') as Cerrado,
              frm.txtFormulario as 'Numero de Formulario',
              IF(frm.bolSancion = 1, 'SI', 'NO') as Sancionado,
              sis.txtSisben as Sisben,
              IF(frm.seqProyecto is null or frm.seqProyecto = 0,'No Disponible',pry.txtNombreProyecto) as Proyecto,
              IF(frm.seqProyectoHijo is null or frm.seqProyectoHijo = 0,'No Disponible',pry1.txtNombreProyecto) as Conjunto,
              IF(frm.seqUnidadProyecto is null or frm.seqUnidadProyecto = 1,'No Disponible',upr.txtNombreUnidad) as Unidad,
              loc.seqLocalidad as Localidad,
              if(bar.txtBarrio is null,'No Disponible',bar.txtBarrio) as Barrio,
              if(frm.numHabitaciones is null,0,frm.numHabitaciones) as Dormitorios,
              if(frm.numHacinamiento is null,0,frm.numHacinamiento) as Hacinamiento,
              ppal.txtNombre as 'Postulante Principal - Nombre',
              ppal.txtTipoDocumento as 'Postulante Principal - Tipo de Documento',
              ppal.numDocumento as 'Postulante Principal - Documento',
              upper(concat( ciu.txtNombre1,' ', ciu.txtNombre2,' ', ciu.txtApellido1,' ', ciu.txtApellido2 )) as Nombre,
              tdo.txtTipoDocumento as 'Tipo de Documento',
              ciu.numDocumento as Documento,
              par.txtParentesco as Parentesco,
              FLOOR((DATEDIFF(NOW(), ciu.fchNacimiento) / 365)) AS Edad,
              rangoEdad(FLOOR((DATEDIFF(NOW(), ciu.fchNacimiento) / 365))) AS 'Rango Edad',
              ned.txtNivelEducativo as 'Nivel Educativo', 
              ciu.numAnosAprobados as 'Años Aprobados',
              etn.txtEtnia as 'Condicion Etnica', 
              eci.txtEstadoCivil as 'Estado Civil', 
              ocu.txtOcupacion as 'Ocupacion',   
              sal.txtSalud as 'Afiliacion a Salud',
              ucwords( cabezaFamilia( ciu.seqCondicionEspecial , ciu.seqCondicionEspecial2 , ciu.seqCondicionEspecial3 ) ) as 'Cabeza de Familia',
              ucwords( mayor65anos( ciu.seqCondicionEspecial , ciu.seqCondicionEspecial2 , ciu.seqCondicionEspecial3 ) ) as 'Mayor de 65',
              ucwords( discapacitado( ciu.seqCondicionEspecial , ciu.seqCondicionEspecial2 , ciu.seqCondicionEspecial3 ) ) as 'Discapacitado',
              ucwords( ningunaCondicionEspecial( ciu.seqCondicionEspecial , ciu.seqCondicionEspecial2 , ciu.seqCondicionEspecial3 ) ) as 'Ninguna Condicion',    
              sex.txtSexo as Sexo, 
              if(ciu.bolLgtb=1,'Si','No') as LGTBI, 
              glg.txtGrupoLgtbi as 'Grupo LGTBI', 
              if(frm.bolDesplazado = 1, 'Si','No') as Desplazado,
              tvi.txtTipoVictima 'Hecho Victimizante'
            FROM t_vee_formulario frm
            INNER JOIN t_vee_hogar hog ON frm.seqFormularioVeeduria = hog.seqFormularioVeeduria
            INNER JOIN t_vee_ciudadano ciu ON hog.seqCiudadanoVeeduria = ciu.seqCiudadanoVeeduria and ciu.seqCorte = $seqCorte
            INNER JOIN t_frm_estado_proceso epr on frm.seqEstadoProceso = epr.seqEstadoProceso
            INNER JOIN t_frm_etapa eta on epr.seqEtapa = eta.seqEtapa
            INNER JOIN t_frm_plan_gobierno pgo on frm.seqPlanGobierno = pgo.seqPlanGobierno
            LEFT  JOIN t_frm_modalidad moa on frm.seqModalidad = moa.seqModalidad
            LEFT  JOIN t_pry_tipo_esquema tes on frm.seqTipoEsquema = tes.seqTipoEsquema
            INNER JOIN t_frm_solucion sol on frm.seqSolucion = sol.seqSolucion 
            INNER JOIN t_frm_sisben sis on frm.seqSisben = sis.seqSisben
            LEFT  JOIN t_pry_proyecto pry on frm.seqProyecto = pry.seqProyecto 
            LEFT  JOIN t_pry_proyecto pry1 on frm.seqProyectoHijo = pry1.seqProyecto
            LEFT  JOIN t_vee_unidad_proyecto upr on frm.seqUnidadProyecto = upr.seqUnidadProyecto
            INNER JOIN t_frm_localidad loc on frm.seqLocalidad = loc.seqLocalidad
            LEFT  JOIN t_frm_barrio bar on frm.seqBarrio = bar.seqBarrio
            INNER JOIN t_ciu_parentesco par on hog.seqParentesco = par.seqParentesco
            INNER JOIN (
              SELECT 
                frm1.seqFormularioVeeduria,
                upper(concat( ciu1.txtNombre1,' ', ciu1.txtNombre2,' ', ciu1.txtApellido1,' ', ciu1.txtApellido2 )) as txtNombre,
                ciu1.numDocumento,
                tdo1.txtTipoDocumento
              FROM t_vee_formulario frm1
              INNER JOIN t_vee_hogar hog1 ON frm1.seqFormularioVeeduria = hog1.seqFormularioVeeduria and hog1.seqParentesco = 1
              INNER JOIN t_vee_ciudadano ciu1 ON hog1.seqCiudadanoVeeduria = ciu1.seqCiudadanoVeeduria
              INNER JOIN t_ciu_tipo_documento tdo1 on ciu1.seqTipoDocumento = tdo1.seqTipoDocumento
            ) ppal on frm.seqFormularioVeeduria = ppal.seqFormularioVeeduria
            INNER JOIN t_ciu_tipo_documento tdo on ciu.seqTipoDocumento = tdo.seqTipoDocumento
            LEFT  JOIN t_ciu_nivel_educativo ned on ciu.seqNivelEducativo = ned.seqNivelEducativo
            INNER JOIN t_ciu_etnia etn on ciu.seqEtnia = etn.seqEtnia
            INNER JOIN t_ciu_estado_civil eci on ciu.seqEstadoCivil = eci.seqEstadoCivil
            INNER JOIN t_ciu_ocupacion ocu on ciu.seqOcupacion = ocu.seqOcupacion
            INNER JOIN t_ciu_sexo sex on ciu.seqSexo = sex.seqSexo
            LEFT  JOIN t_ciu_salud sal on ciu.seqSalud = sal.seqSalud
            LEFT  JOIN t_frm_tipovictima tvi on ciu.seqTipoVictima = tvi.seqTipoVictima
            LEFT  JOIN t_frm_grupo_lgtbi glg on ciu.seqGrupoLgtbi = glg.seqGrupoLgtbi
            WHERE frm.seqCorte = $seqCorte
            AND frm.seqFormulario IN ( " . implode("," , array_keys( $arrFormularios ) ) . " )
        ";
        $objRes = $aptBd->execute($sql);
        $arrHogares = array();
        while( $objRes->fields ){
            $arrHogares[] = $objRes->fields;
            $objRes->MoveNext();
        }
        return $arrHogares;
    }


    private function fuentesXML(){

        $xmlEstilos = "<Styles>";

        $xmlEstilos .= "
            <Style ss:ID='Default' ss:Name='Normal'>
                <Alignment/>
                <Borders/>
                <Font ss:FontName='Calibri' ss:Size='8'/>
                <Interior/>
                <NumberFormat/>
                <Protection/>
            </Style>
        ";
        $xmlEstilos .= "
            <Style ss:ID='s1'>
                <Alignment ss:Vertical='Center' ss:Horizontal='Center'/>
                <Font ss:FontName='Calibri' ss:Size='8' ss:Bold='1'/>
                <Interior ss:Color='#D8D8D8' ss:Pattern='Solid'/>
            </Style>
        ";
        $xmlEstilos .= "
            <Style ss:ID='s2'>
                <Alignment ss:Vertical='Center' ss:Horizontal='Right'/>
                <Font ss:FontName='Calibri' ss:Size='8' ss:Bold='1'/>
                <Interior ss:Color='#D8D8D8' ss:Pattern='Solid'/>
            </Style>
        ";
        $xmlEstilos .= "
            <Style ss:ID='s3'>
                <Alignment ss:Vertical='Center' ss:Horizontal='Right'/>
                <Font ss:FontName='Calibri' ss:Size='8' ss:Bold='1'/>
                <Interior ss:Color='#D8D8D8' ss:Pattern='Solid'/>
            </Style>
        ";

        $xmlEstilos .= "
            <Style ss:ID='s4'>
                <Borders>
                    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
                </Borders>
                <Alignment ss:Vertical='Center' ss:Horizontal='Right'/>
                <Font ss:FontName='Calibri' ss:Size='8' ss:Bold='1'/>
                <Interior ss:Color='#F2F2F2' ss:Pattern='Solid'/>
            </Style>
        ";

        $xmlEstilos .= "
            <Style ss:ID='s5'>
                <NumberFormat ss:Format='yyyy-mm-dd'/>
            </Style>
        ";

        $xmlEstilos .= "</Styles>";

        return $xmlEstilos;

    }


    public function imprimirReporteProyectos($arrReporte)
    {

        $numAcrossGenerados   = ($arrReporte['reporte']['generados']['maximo']   - $arrReporte['reporte']['generados']['minimo']  ) + 1;
        $numAcrossVinculados  = ($arrReporte['reporte']['vinculados']['maximo']  - $arrReporte['reporte']['vinculados']['minimo'] ) + 1;
        $numAcrossLegalizados = ($arrReporte['reporte']['legalizados']['maximo'] - $arrReporte['reporte']['legalizados']['minimo']) + 1;

        /***********************************************
         * ENCABEZADO
         ***********************************************/

        $xmlArchivo  = "<?xml version='1.0'?> ";
        $xmlArchivo .= "<?mso-application progid='Excel.Sheet'?> ";
        $xmlArchivo .= "<Workbook xmlns='urn:schemas-microsoft-com:office:spreadsheet' ";
        $xmlArchivo .= "xmlns:o='urn:schemas-microsoft-com:office:office' ";
        $xmlArchivo .= "xmlns:x='urn:schemas-microsoft-com:office:excel' ";
        $xmlArchivo .= "xmlns:ss='urn:schemas-microsoft-com:office:spreadsheet' ";
        $xmlArchivo .= "xmlns:html='http://www.w3.org/TR/REC-html40'>";

        /***********************************************
         * ESTILOS DE FUENTES
         ***********************************************/

        $xmlArchivo .= $this->fuentesXML();

        /***********************************************
         * HOJA REPORTE
         ***********************************************/

        $xmlArchivo .= "<ss:Worksheet ss:Name='Reporte Proyectos'>";
        $xmlArchivo .= "<ss:Table>";
        $xmlArchivo .= "<Column ss:AutoFitWidth='0' ss:Width='180'/>";

        // titulos
        $xmlArchivo .= "<ss:Row>";
        $xmlArchivo .= "<ss:Cell ss:StyleID='s1' ss:MergeDown='1'><ss:Data ss:Type='String'>Proyecto</ss:Data></ss:Cell>";
        $xmlArchivo .= "<ss:Cell ss:StyleID='s1' ss:MergeDown='1'><ss:Data ss:Type='String'>Resoluciones</ss:Data></ss:Cell>";
        $xmlArchivo .= "<ss:Cell ss:StyleID='s1' ss:MergeAcross='$numAcrossGenerados'><ss:Data ss:Type='String'>Subsidios Generados</ss:Data></ss:Cell>";
        $xmlArchivo .= "<ss:Cell ss:StyleID='s1' ss:MergeAcross='$numAcrossVinculados'><ss:Data ss:Type='String'>Vinculados</ss:Data></ss:Cell>";
        $xmlArchivo .= "<ss:Cell ss:StyleID='s1' ss:MergeAcross='$numAcrossLegalizados'><ss:Data ss:Type='String'>Legalizados</ss:Data></ss:Cell>";
        $xmlArchivo .= "</ss:Row>";

        // titulos - anios para generados
        $xmlArchivo .= "<ss:Row>";
        for( $numAnio = $arrReporte['reporte']['generados']['minimo'] ; $numAnio <= $arrReporte['reporte']['generados']['maximo']; $numAnio++ ){
            if( $numAnio == $arrReporte['reporte']['generados']['minimo'] ){
                $xmlArchivo .= "<ss:Cell ss:StyleID='s2' ss:Index='3'><ss:Data ss:Type='Number'>$numAnio</ss:Data></ss:Cell>";
            }else{
                $xmlArchivo .= "<ss:Cell ss:StyleID='s2'><ss:Data ss:Type='Number'>$numAnio</ss:Data></ss:Cell>";
            }
        }
        $xmlArchivo .= "<ss:Cell ss:StyleID='s3'><ss:Data ss:Type='String'>Total</ss:Data></ss:Cell>";

        // titulos - anios para vinculados
        for( $numAnio = $arrReporte['reporte']['vinculados']['minimo'] ; $numAnio <= $arrReporte['reporte']['vinculados']['maximo']; $numAnio++ ){
            $xmlArchivo .= "<ss:Cell ss:StyleID='s2'><ss:Data ss:Type='Number'>$numAnio</ss:Data></ss:Cell>";
        }
        $xmlArchivo .= "<ss:Cell ss:StyleID='s3'><ss:Data ss:Type='String'>Total</ss:Data></ss:Cell>";

        // titulos - anios para legalizados
        for( $numAnio = $arrReporte['reporte']['legalizados']['minimo'] ; $numAnio <= $arrReporte['reporte']['legalizados']['maximo']; $numAnio++ ){
            $xmlArchivo .= "<ss:Cell ss:StyleID='s2'><ss:Data ss:Type='Number'>$numAnio</ss:Data></ss:Cell>";
        }
        $xmlArchivo .= "<ss:Cell ss:StyleID='s3'><ss:Data ss:Type='String'>Total</ss:Data></ss:Cell>";

        $xmlArchivo .= "</ss:Row>";

        // datos del reporte
        foreach( $arrReporte['reporte']['generados']['datos'] as $txtProyecto => $arrResoluciones ){
            foreach( $arrResoluciones as $txtNombreResolucion => $arrAnios ){
                $xmlArchivo .= "<ss:Row>";

                // GENERADOS
                $xmlArchivo .= "<ss:Cell><ss:Data ss:Type='String'>$txtProyecto</ss:Data></ss:Cell>";
                $xmlArchivo .= "<ss:Cell><ss:Data ss:Type='String'>$txtNombreResolucion</ss:Data></ss:Cell>";
                for ($numAnio = $arrReporte['reporte']['generados']['minimo']; $numAnio <= $arrReporte['reporte']['generados']['maximo']; $numAnio++) {
                    if ( isset( $arrAnios[ $numAnio ] ) ) {
                        $xmlArchivo .= "<ss:Cell><ss:Data ss:Type='Number'>" . $arrAnios[ $numAnio ] . "</ss:Data></ss:Cell>";
                    } else {
                        $xmlArchivo .= "<ss:Cell><ss:Data ss:Type='Number'>0</ss:Data></ss:Cell>";
                    }
                }
                $xmlArchivo .= "<ss:Cell ss:StyleID='s4'><ss:Data ss:Type='Number'>" . $arrAnios['total'] . "</ss:Data></ss:Cell>";

                // VINCULADOS
                if( isset( $arrReporte['reporte']['vinculados']['datos'][ $txtProyecto ][ $txtNombreResolucion ] ) ){
                    for ($numAnio = $arrReporte['reporte']['vinculados']['minimo']; $numAnio <= $arrReporte['reporte']['vinculados']['maximo']; $numAnio++) {
                        if ( isset( $arrReporte['reporte']['vinculados']['datos'][ $txtProyecto ][ $txtNombreResolucion ][ $numAnio ] ) ) {
                            $xmlArchivo .= "<ss:Cell><ss:Data ss:Type='Number'>" . $arrReporte['reporte']['vinculados']['datos'][ $txtProyecto ][ $txtNombreResolucion ][ $numAnio ] . "</ss:Data></ss:Cell>";
                        } else {
                            $xmlArchivo .= "<ss:Cell><ss:Data ss:Type='Number'>0</ss:Data></ss:Cell>";
                        }
                    }
                }else{
                    for ($numAnio = $arrReporte['reporte']['vinculados']['minimo']; $numAnio <= $arrReporte['reporte']['vinculados']['maximo']; $numAnio++) {
                        $xmlArchivo .= "<ss:Cell><ss:Data ss:Type='Number'>0</ss:Data></ss:Cell>";
                    }
                }
                $xmlArchivo .= "<ss:Cell ss:StyleID='s4'><ss:Data ss:Type='Number'>" . $arrReporte['reporte']['vinculados']['datos'][ $txtProyecto ][ $txtNombreResolucion ]['total'] . "</ss:Data></ss:Cell>";

                // LEGALIZADOS
                if( isset( $arrReporte['reporte']['legalizados']['datos'][ $txtProyecto ][ $txtNombreResolucion ] ) ){
                    for ($numAnio = $arrReporte['reporte']['legalizados']['minimo']; $numAnio <= $arrReporte['reporte']['legalizados']['maximo']; $numAnio++) {
                        if ( isset( $arrReporte['reporte']['legalizados']['datos'][ $txtProyecto ][ $txtNombreResolucion ][ $numAnio ] ) ) {
                            $xmlArchivo .= "<ss:Cell><ss:Data ss:Type='Number'>" . $arrReporte['reporte']['legalizados']['datos'][ $txtProyecto ][ $txtNombreResolucion ][ $numAnio ] . "</ss:Data></ss:Cell>";
                        } else {
                            $xmlArchivo .= "<ss:Cell><ss:Data ss:Type='Number'>0</ss:Data></ss:Cell>";
                        }
                    }
                }else{
                    for ($numAnio = $arrReporte['reporte']['legalizados']['minimo']; $numAnio <= $arrReporte['reporte']['legalizados']['maximo']; $numAnio++) {
                        $xmlArchivo .= "<ss:Cell><ss:Data ss:Type='Number'>0</ss:Data></ss:Cell>";
                    }
                }
                $xmlArchivo .= "<ss:Cell ss:StyleID='s4'><ss:Data ss:Type='Number'>" . $arrReporte['reporte']['legalizados']['datos'][ $txtProyecto ][ $txtNombreResolucion ]['total'] . "</ss:Data></ss:Cell>";

                $xmlArchivo .= "</ss:Row>";
            }
        }

        $xmlArchivo .= "</ss:Table>";
        $xmlArchivo .= "</ss:Worksheet>";

        /***********************************************
         * HOJA REPORTE DE HOGARES
         ***********************************************/

        $xmlArchivo .= "<ss:Worksheet ss:Name='Hogares'>";
        $xmlArchivo .= "<ss:Table>";

        // titulos
        $arrTitulos = array_keys($arrReporte['hogares'][0]);
        $xmlArchivo .= "<ss:Row>";
        foreach ($arrTitulos as $txtTitulo){
            $xmlArchivo .= "<ss:Cell ss:StyleID='s1'><ss:Data ss:Type='String'>$txtTitulo</ss:Data></ss:Cell>";
        }
        $xmlArchivo .= "</ss:Row>";

        // datos
        foreach ($arrReporte['hogares'] as $numLinea => $arrDatos){
            $xmlArchivo .= "<ss:Row>";
            foreach($arrDatos as $txtTitulo => $txtValor) {
                $txtTipo = $this->tipoDato( $txtValor );
                $txtEstilo = "";
                switch($txtTipo){
                    case "DateTime":
                        $txtValor = date( "Y-m-d" , strtotime( $txtValor ) );
                        $txtEstilo = "ss:StyleID='s5'";
                        break;
                    case "Number":
                        $txtValor = doubleval($txtValor);
                        break;
                    default:
                        $txtValor = trim($txtValor);
                        break;
                }
                $xmlArchivo .= "<ss:Cell $txtEstilo><ss:Data ss:Type='$txtTipo'>$txtValor</ss:Data></ss:Cell>";
            }
            $xmlArchivo .= "</ss:Row>";
        }

        $xmlArchivo .= "</ss:Table>";
        $xmlArchivo .= "</ss:Worksheet>";


        /***********************************************
         * HOJA PROYECTOS
         ***********************************************/

        $xmlArchivo .= "<ss:Worksheet ss:Name='Proyectos'>";
        $xmlArchivo .= "<ss:Table>";

        // titulos
        $arrTitulos = array_keys( array_shift( $arrReporte['proyectos'] ) );
        $xmlArchivo .= "<ss:Row>";
        foreach ($arrTitulos as $txtTitulo){
            $xmlArchivo .= "<ss:Cell ss:StyleID='s1'><ss:Data ss:Type='String'>$txtTitulo</ss:Data></ss:Cell>";
        }
        $xmlArchivo .= "</ss:Row>";

        // datos
        foreach ($arrReporte['proyectos'] as $seqUnidadProyecto => $arrDatos){
            $xmlArchivo .= "<ss:Row>";
            foreach($arrDatos as $txtTitulo => $txtValor) {
                $txtTipo = $this->tipoDato( $txtValor );
                $txtEstilo = "";
                switch($txtTipo){
                    case "DateTime":
                        $txtValor = date( "Y-m-d" , strtotime( $txtValor ) );
                        $txtEstilo = "ss:StyleID='s5'";
                        break;
                    case "Number":
                        $txtValor = doubleval($txtValor);
                        break;
                    default:
                        $txtValor = trim($txtValor);
                        break;
                }
                $xmlArchivo .= "<ss:Cell $txtEstilo><ss:Data ss:Type='$txtTipo'>$txtValor</ss:Data></ss:Cell>";
            }
            $xmlArchivo .= "</ss:Row>";
        }

        $xmlArchivo .= "</ss:Table>";
        $xmlArchivo .= "</ss:Worksheet>";

        /***********************************************
         * HOJA RESOLUCIONES
         ***********************************************/

        $xmlArchivo .= "<ss:Worksheet ss:Name='Resoluciones'>";
        $xmlArchivo .= "<ss:Table>";

        // titulos
        $arrTitulos = array_keys($arrReporte['resoluciones'][0]);
        $xmlArchivo .= "<ss:Row>";
        foreach ($arrTitulos as $txtTitulo){
            $xmlArchivo .= "<ss:Cell ss:StyleID='s1'><ss:Data ss:Type='String'>$txtTitulo</ss:Data></ss:Cell>";
        }
        $xmlArchivo .= "</ss:Row>";

        // datos
        foreach ($arrReporte['resoluciones'] as $numLinea => $arrDatos){
            $xmlArchivo .= "<ss:Row>";
            foreach($arrDatos as $txtTitulo => $txtValor) {
                $txtTipo = $this->tipoDato( $txtValor );
                $txtEstilo = "";
                switch($txtTipo){
                    case "DateTime":
                        $txtValor = date( "Y-m-d" , strtotime( $txtValor ) );
                        $txtEstilo = "ss:StyleID='s5'";
                        break;
                    case "Number":
                        $txtValor = doubleval($txtValor);
                        break;
                    default:
                        $txtValor = trim($txtValor);
                        break;
                }
                $xmlArchivo .= "<ss:Cell $txtEstilo><ss:Data ss:Type='$txtTipo'>$txtValor</ss:Data></ss:Cell>";
            }
            $xmlArchivo .= "</ss:Row>";
        }

        $xmlArchivo .= "</ss:Table>";
        $xmlArchivo .= "</ss:Worksheet>";

        $xmlArchivo .= "</ss:Workbook>";

        $txtNombre = "InformeProyectos" . date("YmdHis") . ".xls";
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: inline; filename=\"" . $txtNombre . "\"");
        echo $xmlArchivo;

    }

    private function tipoDato($txtValor){
        switch(true){
            case esFechaValida($txtValor):
                $txtTipo = "DateTime";
                break;
            case is_numeric($txtValor):
                $txtTipo = "Number";
                break;
            default:
                $txtTipo = "String";
                break;
        }
        return $txtTipo;
    }



}