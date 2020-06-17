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
$string['modulename_help'] = 'El módulo "Resultados BFI" permite procesar las respuestas dadas a través de una actividad tipo Encuesta, al “Big-Five Inventory”, el cual es una adaptación al español del instrumento psicométrico "Big Five Questionnaire", realizada por (Benet-Martínez & John, 1998) para la medición de rasgos de la personalidad.';

//Previamente los integrantes del curso deben haber diligenciado una actividad de tipo encuesta con las preguntas del "Big Five Questionnaire", cuyas respuestas serán tomadas para medir los rasgos de personalidad de los individuos.';
$string['modulenameplural'] = 'Resultados BFI';

$string['mbfiname'] = 'Nombre';
$string['mbfiname_help'] = 'Digite el nombre identificador para la nueva actividad Resultados BFI.';

$string['nonewmodules'] = 'No hay intancias de la actividad Resultados BFI.';

// Add string by form
$string['feedbackar'] = 'Seleccione una encuesta: ';
$string['feedbackar_help'] = 'Escoja la encuesta para medir los rasgos de personalidad de los individuos que han diligenciado completamente la misma.';

// Add string errors
$string['err_recordsfeedback'] = 'No se encontraron encuestas en el curso.';
$string['err_feedbackcompleted'] = 'Ningún individuo ha completado la encuesta "{$a->name}".';
$string['err_answerscounting'] = 'El usuario "{$a->fullname}" aún no ha completado el cuestionario.';

// Add string by view
//$string['imageuserhd'] = 'Imagen del usuario';
//$string['usernamehd'] = 'Nombre de usuario';
$string['fullnamehd'] = 'Nombre(s)/Apellido(s)';
$string['extraversionhd'] = 'E';
$string['agreeablenesshd'] = 'A';
$string['conscientiousnesshd'] = 'C';
$string['neuroticismhd'] = 'N';
$string['opennesshd'] = 'O';