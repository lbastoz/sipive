<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once "lib/mysqli/shared/ez_sql_core.php";
include_once "lib/mysqli/ez_sql_mysqli.php";
include '../../recursos/archivos/lecturaConfiguracion.php';

$db = new ezSQL_mysqli($arrConfiguracion['baseDatos']['clave'], $arrConfiguracion['baseDatos']['clave'], $arrConfiguracion['baseDatos']['nombre'], $arrConfiguracion['baseDatos']['servidor']);

