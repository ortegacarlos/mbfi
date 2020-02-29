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
 * Add or remove checkbox according to the number of characteristics.
 *
 * @module      mod_mgroup/add_remove_characteristics
 * @copyright   2019 Carlos Ortega <carlosortega@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    return {
        init: function () {

            function addRadioButton(element) {
                var fitemid = $('#fgroup_id_grouphomocharacteristic').children('.felement').find('.fitem').prop('id');
                var numberofcharacteristics = parseInt(element.val());
                var index = fitemid.split('_');
                var labelid = fitemid.substr(0, fitemid.length - index[index.length - 1].length);
                index = parseInt(index[index.length - 1]);
                var style = '';
                var hidden = '';
                var disabled = '';
                if(!$('#id_groupingtype_2').is(':checked')) {
                        style = '="display: none;"';
                        hidden = 'hidden="hidden"';
                        disabled = 'disabled="disabled"';
                }
                $('#fgroup_id_grouphomocharacteristic').children('.felement').empty();
                $('#fgroup_id_grouphetecharacteristic').children('.felement').empty();
                for (let i = 0; i < numberofcharacteristics; i++) {
                    $('#fgroup_id_grouphomocharacteristic').children('.felement').append(
                        '<label class="form-check-inline form-check-label  fitem  " id="'+labelid+index+'" '+hidden+' style'+style+'>'+
                        '<input type="radio" class="form-check-input " name="char'+(i+1)+'" id="id_char'+(i+1)+'_0" value="0" checked '+disabled+'>'+
                            'C'+(i+1)+
                        '</label>'+
                        '<span class="form-control-feedback invalid-feedback" id="id_error_char'+(i+1)+'_0"></span>'
                    );
                    index++;
                    $('#fgroup_id_grouphetecharacteristic').children('.felement').append(
                        '<label class="form-check-inline form-check-label  fitem  " id="'+labelid+index+'" '+hidden+' style'+style+'>'+
                        '<input type="radio" class="form-check-input " name="char'+(i+1)+'" id="id_char'+(i+1)+'_1" value="1" '+disabled+'>'+
                            'C'+(i+1)+
                        '</label>'+
                        '<span class="form-control-feedback invalid-feedback" id="id_error_char'+(i+1)+'_1"></span>'
                    );
                    index++;
                }
            }

            function changeAttributes() {
                $('#fgroup_id_grouphomocharacteristic').children('.felement').find('label').attr('hidden', 'hidden');
                $('#fgroup_id_grouphomocharacteristic').children('.felement').find('label').attr('style', 'display: none;');
                $('#fgroup_id_grouphomocharacteristic').children('.felement').find('input:radio').attr('disabled', 'disabled');
                $('#fgroup_id_grouphetecharacteristic').children('.felement').find('label').attr('hidden', 'hiden');
                $('#fgroup_id_grouphetecharacteristic').children('.felement').find('label').attr('style', 'display: none;');
                $('#fgroup_id_grouphetecharacteristic').children('.felement').find('input:radio').attr('disabled', 'disabled');
            }

            $('#id_numberofcharacteristics').on('change', function(e) {
                e.preventDefault();
                console.log($(this));
                addRadioButton($(this));
            });

            $('#id_groupingtype_2').on('change', function(e) {
                if($(this).is(':checked')) {
                    addRadioButton($('#id_numberofcharacteristics'));
                    $('#fgroup_id_grouphomocharacteristic').children('.felement').find('label').removeAttr('hidden');
                    $('#fgroup_id_grouphomocharacteristic').children('.felement').find('label').attr('style', '');
                    $('#fgroup_id_grouphomocharacteristic').children('.felement').find('input:radio').removeAttr('disabled');
                    $('#fgroup_id_grouphetecharacteristic').children('.felement').find('label').removeAttr('hidden');
                    $('#fgroup_id_grouphetecharacteristic').children('.felement').find('label').attr('style', '');
                    $('#fgroup_id_grouphetecharacteristic').children('.felement').find('input:radio').removeAttr('disabled');
                }
            });

            $('#id_groupingtype_1').on('change', function(e) {
                if($(this).is(':checked')) {
                    changeAttributes();
                }
            });

            $('#id_groupingtype_0').on('change', function(e) {
                if($(this).is(':checked')) {
                    changeAttributes();
                }
            });
        }
    };
});
