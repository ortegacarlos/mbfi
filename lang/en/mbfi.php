<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     mod_mbfi
 * @category    string
 * @copyright   2020 Carlos Ortega <carlosortega@udenar.edu.co> Oscar Revelo Sánchez <orevelo@udenar.edu.co> Jesús Insuasti Portilla <insuasty@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Resultados BFI';
$string['modulename'] = 'Resultados BFI';
$string['modulename_help'] = 'El módulo "Resultados BFI" permite procesar las respuestas dadas a través de una actividad tipo Encuesta, al “Big-Five Inventory”, el cual es una adaptación del instrumento psicométrico "Big Five Questionnaire", realizada por (Benet-Martínez & John, 1998) para la medición de rasgos de la personalidad.';
$string['modulenameplural'] = 'Resultados BFI';

$string['mbfiname'] = 'Nombre';
$string['mbfiname_help'] = 'Digite el nombre identificador para la nueva actividad Resultados BFI.';

$string['nonewmodules'] = 'No hay intancias de la actividad Resultados BFI.';

// Add string by form
$string['datasource'] = 'Seleccione una opción';
$string['datasource_help'] = 'Seleccione una opción si desea procesar las respuestas obtenidas del módulo "M-BFI", de lo contrario seleccione la opción "Subir archivo" para procesar sus respuestas.';

$string['feedback'] = 'Encuesta previa';
$string['uploadfile'] = 'Subir archivo';

$string['feedbackar'] = 'Seleccione una encuesta';
$string['feedbackar_help'] = 'Escoja la encuesta para procesar sus respuestas y medir los rasgos de personalidad de los individuos que han diligenciado completamente la misma.';

$string['userfile'] = 'Archivo de datos';
$string['userfile_help'] = 'Campo para cargar un archivo de texto plano, con valores separados por comas (*.csv), que almacena los datos de una actividad tipo Encuesta del "Big-Five Inventory".';

// Add string errors
$string['err_feedbackcompleted'] = 'Ningún individuo ha completado la encuesta "{$a->name}".';
$string['err_answerscounting'] = 'El usuario "{$a->fullname}" aún no ha completado el cuestionario.';
$string['err_nonefeedback'] = 'No se ha seleccionado una encuesta.';
$string['err_savefile'] = 'Error al guardar el archivo o  no se cargó ningún archivo previamente.';
$string['err_deletefile'] = 'Error al eliminar el archivo.';
$string['err_readfile'] = 'Error al leer el archivo.';
$string['err_checkfile'] = 'Revise las inconsistencias encontradas en el archivo.';
$string['err_checkparameters'] = 'Hay inconsistencias en la línea {$a->number}. El número de respuestas guardadas en el archivo no coincide.';
$string['err_organizefiledata'] = 'Se ha producido un error al organizar los datos del archivo.';
$string['err_calculatedimensions'] = 'Se ha producido un error al calcular las dimensiones de cada individuo.';

// Add string by view
$string['fullnamehd'] = 'Nombre(s)/Apellido(s)';
$string['extraversionhd'] = 'E';
$string['agreeablenesshd'] = 'A';
$string['conscientiousnesshd'] = 'C';
$string['neuroticismhd'] = 'N';
$string['opennesshd'] = 'O';
$string['downloadcsv'] = 'Descargar en formato CSV';