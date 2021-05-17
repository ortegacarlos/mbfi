# M-BFI #

El módulo permite procesar las respuestas dadas al *“Big-Five Inventory”*, el
cual es una adaptación del instrumento psicométrico *"Big Five Questionnaire"*
realizada por (Benet-Martínez & John, 1998) para la medición de rasgos de la
personalidad.

Requiere como fuente de datos un archivo de texto plano, con valores separados
por comas, en el que se encuentra registradas las respuestas del instrumento
aplicado a cada individuo, o permite utilizar las respuestas dadas al
instrumento por medio de una actividad tipo **Encuesta**.

## Instalación del módulo ##

* Verificar que la versión de Moodle sea igual o superior a la 3.0.
* Escoger una de las 3 formas de llevar a cabo la instalación del módulo:
  * Buscar e instalar directamente desde el directorio de [plugins](https://moodle.org/plugins/).
  * Instalar módulos externos desde la administración del sitio mediante un
    archivo ZIP.
  * Descomprimir el archivo y copiar la carpeta en el directorio `/mod` ubicado
    en el directorio raíz de la instalación.

## Archivo de Datos ##
Las respuestas de cada individuo dadas al instrumento, se leen desde un archivo
de texto plano, con valores separados por comas (.csv), con una estructura por
registro similar a la que se muestra:

Nombre completo del usuario|Grupos|Dirección de Correo|Fecha|¿De acuerdo?|P1|P2|P3|P4|P5|...|P44
---------------------------|------|-------------------|-----|------------|--|--|--|--|--|---|---
"Primer Individuo"|"Grupo 1 Grupo2"|"primerindividuo@prueba.com"|"viernes, 28 de febrero de 2020, 15:46"|Si|4|3|3|2|4|...|2

Cabe mencionar que los individuos que no estén de acuerdo con el consentimiento
informado indicado en el cuestionario, no serán tenidos en cuenta al momento de
calcular los resultados.

## Funcionamiento ##
1. Crear una actividad de **Resultados BFI**.
2. Diligenciar el formulario con los valores que considere adecuados.
3. Guardar y mostrar los resultados.

## License ##

Carlos Ortega <carlosortega@udenar.edu.co><br>
Oscar Revelo Sánchez <orevelo@udenar.edu.co><br>
Jesús Insuasti Portilla <insuasty@udenar.edu.co><br>
2020

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
