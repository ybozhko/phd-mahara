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
 * @copyright  (C) 2011 Yuliya Bozhko, yuliya.bozhko@gmail.com
 *
 */

define('INTERNAL', 1);
define('MENUITEM', 'myportfolio/concept');

define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'concept');
define('SECTION_PAGE', 'edit');

require(dirname(dirname(__FILE__)) . '/init.php');
require_once('pieforms/pieform.php');
require_once('concept.php');

$new = param_boolean('new', 0);

$id = param_integer('id', 0);

$data = null;
if ($data = get_record_select('concept_maps', 'id = ?', array($id))) {
	
    $map = new ConceptMap($id, (array)$data);
    
    if (!$USER->can_edit_map($map)) {
        $SESSION->add_error_msg(get_string('canteditdontown'));
        redirect('/concept/');
    }
}

// if not a new map 
if (!$new) {
    define('TITLE', $map->get('name').': '.get_string('edittitleanddesc', 'concept'));
}
else {
    define('TITLE', get_string('edittitleanddesc', 'concept'));
}

$elements = ConceptMap::get_mapform_elements($data);
$submitstr = $new ? array('cancel' => get_string('cancel'), 'submit' => get_string('next') . ': ' . get_string('addconcepts', 'concept'))
    : array(get_string('save'), get_string('cancel'));
$confirm = $new ? array('cancel' => get_string('confirmcancelcreatingmap','concept')) : null;

$elements['submit'] = array(
    'type'      => 'submitcancel',
    'value'     => $submitstr,
    'confirm'   => $confirm,
);

$form = pieform(array(
    'name' => 'edit',
    'plugintype' => 'core',
    'pluginname' => 'concept',
    'successcallback' => 'submit',
    'elements' => $elements,
));

$smarty = smarty();
$smarty->assign('PAGEHEADING', TITLE);
$smarty->assign_by_ref('form', $form);
$smarty->display('concept/edit.tpl');

function submit(Pieform $form, $values) {
    global $SESSION, $new;
    $map = ConceptMap::save($values);
    if (!$new) {
        $SESSION->add_ok_msg(get_string('mapsaved', 'concept'));
    }
    $map->post_edit_redirect($new);
}

function edit_cancel_submit() {
    global $map, $new;
    if ($new && $map) {
       $map->delete();
    }
    redirect('/concept/');
} 
   
?>