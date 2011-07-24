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

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once(get_config('libroot') . 'pieforms/pieform.php');
require_once('concept.php');

$new = param_boolean('new', 0);
$id = param_integer('id', 0);
$del = param_integer('del', 0);

$smarty = smarty();

if($del == 0) {
	if ($id == 0) 
		$data= null;
	else
		$data = get_records_sql_array("SELECT start, end, name 
			FROM {concept_timeframe} WHERE parent = ? OR id = ? ORDER BY start, id ASC", array($id, $id)) ;

	for($i = 1; $i < count($data); $i++) {
		$frames[$i-1] .= $data[$i]->start . ';' . $data[$i]->end . ';' . $data[$i]->name;
	}
	$f = implode("\n", $frames);

	$form = pieform(array(
	    'name' => 'editmap',
	    'plugintype' => 'core',
	    'pluginname' => 'concept',
	    'successcallback' => 'submit',
	    'elements' => array(
			'name' => array(
	        	'type' => 'text',
	        	'defaultvalue' => $data ? $data[0]->name : null,
	        	'title' => 'Title',
	         	'size' => 30,
	         	'rules' => array(
	            	'required' => true,
	            ),
	        ),
			'timeframes' => array(
	        	'type'  => 'textarea',
	            'rows' => 10,
	            'cols' => 50,
	            'resizable' => false,
	            'defaultvalue' => $data ? $f : null,
	            'title' => 'Timeframes',
	        	'description' => "Enter timeframes in form YYYY-MM-DD;YYYY-MM-DD;Name starting new line each",
	            'rules' => array(
	            	'required' => true,
	            ),
			),
			'id' => array(
				'type' => 'hidden',
				'value' => !$new ? $id : null,
			),
	        'submit' => array(
	            'type' => 'submitcancel',
	            'value' => array('Save', 'Cancel'),
	            'goto' => get_config('wwwroot') . 'concept/timeframes/',
	        ),
	    ),
	));
	}
else {
	$form = pieform(array(
    	'name' => 'deleteframe',
    	'renderer' => 'div',
    	'elements' => array(
        	'submit' => array(
            	'type' => 'submitcancel',
            	'value' => array(get_string('yes'), get_string('no')),
            	'goto' => get_config('wwwroot') . 'concept/timeframes/',
        	),
    	),
	));
	$smarty->assign('message', "Are you sure that you want to delete this timeframe?");
}
 
$smarty->assign('form', $form);
$smarty->display('concept/timeframes/edit.tpl');

function submit(Pieform $form, $values) {
    global $SESSION, $new, $id;
	save_timeframes($values, $new);
    $SESSION->add_ok_msg("Timeframe saved");
    redirect('/concept/timeframes/');
}

function edit_cancel_submit() {
    redirect('/concept/timeframes/');
} 

function deleteframe_submit(Pieform $form, $values) {
    global $SESSION, $del;
    
    db_begin();
    delete_records('concept_map_timeframe', 'timeframe', $del);
    delete_records('concept_timeframe', 'parent', $del);
    delete_records('concept_timeframe', 'id', $del);
    db_commit();
    
    $SESSION->add_ok_msg("Timeframe deleted");
    redirect('/concept/timeframes/');
}

?>