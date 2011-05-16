<?php
/**
 * Pieforms: Advanced web forms made easy
 * Copyright (C) 2006-2008 Catalyst IT Ltd (http://www.catalyst.net.nz)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    pieform
 * @subpackage rule
 * @author     Nigel McNie <nigel@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2006-2008 Catalyst IT Ltd http://catalyst.net.nz
 *
 */

/**
 * Checks whether the given value is at least a certain size.
 *
 * @param Pieform $form      The form the rule is being applied to
 * @param string  $value     The value to check
 * @param array   $element   The element to check
 * @param int     $maxlength The value to check for
 * @return string            The error message, if the value is invalid.
 */
function pieform_rule_minvalue(Pieform $form, $value, $element, $minvalue) {/*{{{*/
    if ($value != '' && doubleval($value) < $minvalue) {
        return sprintf($form->i18n('rule', 'minvalue', 'minvalue', $element), $minvalue);
    }
}/*}}}*/

function pieform_rule_minvalue_i18n() {/*{{{*/
    return array(
        'en.utf8' => array(
            'minvalue' => 'This value can not be smaller than %d'
        ),
        'de.utf8' => array(
            'minvalue' => 'Dieser Wert kann nicht kleiner als %d sein'
        ),
        'fr.utf8' => array(
            'minvalue' => 'Cette valeur ne peut pas être inférieure à %d'
        ),
        'ja.utf8' => array(
            'minvalue' => 'この値は %d 以下にすることはできません'
        ),
        'es.utf8' => array(
            'minvalue' => 'Este valor no puede ser inferior a %d'
        ),
        'sl.utf8' => array(
            'minvalue' => 'Ta vrednost ne sme biti manjša od %d'
        ),
        'nl.utf8' => array(
            'minvalue' => 'Deze waarde kan niet kleiner zijn dan %d'
        ),
        'cs.utf8' => array(
            'minvalue' => 'Nemůžete zadat hodnotu menší než %d'
        ),

    );
}/*}}}*/

?>
