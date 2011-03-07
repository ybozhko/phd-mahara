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
 * @subpackage element
 * @author     Nigel McNie <nigel@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2006-2008 Catalyst IT Ltd http://catalyst.net.nz
 *
 */

/**
 * Renders an <input type="image"> button
 *
 * @param Pieform  $form    The form to render the element for
 * @param array    $element The element to render
 * @return string           The HTML for the element
 */
function pieform_element_image(Pieform $form, $element) {/*{{{*/
    if (!isset($element['src'])) {
        throw new PieformException('"image" elements must have a "src" for the image');
    }
    if (!isset($element['value'])) {
        $element['value'] = true;
    }
    return '<input type="image" src="' . Pieform::hsc($element['src']) . '"'
        . $form->element_attributes($element)
        . ' value="' . Pieform::hsc($form->get_value($element)) . '">';
}/*}}}*/

function pieform_element_image_set_attributes($element) {/*{{{*/
    $element['submitelement'] = true;
    return $element;
}/*}}}*/

function pieform_element_image_get_value(Pieform $form, $element) {/*{{{*/
    if (isset($element['value'])) {
        return $element['value'];
    }
    
    $global = $form->get_property('method') == 'get' ? $_GET : $_POST;
    if ($form->is_submitted() && isset($global[$element['name'] . '_x'])) {
        return true;
    }

    return null;
}/*}}}*/

?>
