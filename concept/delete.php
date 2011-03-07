<?php
/**
 * Mahara: Electronic portfolio, weblog, resume builder and social networking
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
 * @package    mahara
 * @subpackage concept
 * @author     Yuliya Bozhko
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2010 Yuliya Bozhko, yuliya.bozhko@gmail.com
 *
 */

define('INTERNAL', 1);
define('MENUITEM', 'myportfolio/concept');

define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'concept');
define('SECTION_PAGE', 'delete');

require(dirname(dirname(__FILE__)) . '/init.php');
require_once('pieforms/pieform.php');
require_once('concept.php');
define('TITLE', get_string('deletemap', 'concept'));

$id = param_integer('id');

$data = get_record_select('concept_maps', 'id = ?', array($id));
$map = new ConceptMap($id, (array)$data);
if (!$USER->can_edit_map($map)) {
    $SESSION->add_error_msg(get_string('canteditdontown'));
    redirect('/concept/');
}
$form = pieform(array(
    'name' => 'deletemap',
    'renderer' => 'div',
    'elements' => array(
        'submit' => array(
            'type' => 'submitcancel',
            'value' => array(get_string('yes'), get_string('no')),
            'goto' => get_config('wwwroot') . 'concept/',
        ),
    ),
));

$smarty = smarty();
$smarty->assign('subheading', TITLE);
$smarty->assign('message', get_string('mapconfirmdelete', 'concept'));
$smarty->assign('form', $form);
$smarty->display('concept/delete.tpl');

function deletemap_submit(Pieform $form, $values) {
    global $SESSION, $map;
    $map->delete();
    $SESSION->add_ok_msg(get_string('mapdeleted', 'concept'));
    redirect('/concept/');
}

?>
