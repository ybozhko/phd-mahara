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
define('MENUITEM', 'myportfolio/bookmark');

define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'concept');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once('pieforms/pieform.php');
require_once('pieforms/pieform/elements/calendar.php');
require_once('concept.php');

$user = $USER->get('id');
$aid = param_integer('id', 0);
$delete = param_integer('delete', 0);
$edit = param_integer('edit', 0);
$new = param_boolean('new', 0);

$smarty = smarty();

if($delete != 0) {
	$form = pieform(array(
    	'name' => 'deleteitem',
    	'renderer' => 'div',
    	'elements' => array(
        	'submit' => array(
            	'type' => 'submitcancel',
            	'value' => array(get_string('yes'), get_string('no')),
            	'goto' => get_config('wwwroot') . 'concept/bookmark/',
        	),
    	),
	));
	$smarty->assign('PAGEHEADING', 'Deleting record');
	$smarty->assign('message', "Are you sure that you want to delete this record?");
} 
else {	
	if($edit != 0)
		$data = get_record('concept_example', 'id', $edit);
	else 
		$data = get_record('artefact', 'id', $aid);
 	
	$elements = array(
		'title' => array(
			'type'         => 'text',
			'title'        => $edit != 0 ? 'Title' : 'URL',
			'description'  => $edit != 0 ? 'Title of your example' : 'URL address that you want to bookmark',
			'defaultvalue' => $edit != 0 ? (!$new ? $data->title : '') : $data->title,
			'rules' 	   => array('required' => true),
		),
		'cdate' => array(
			'type'       => 'calendar',
			'caloptions' => array(
				'showsTime'	=> false,
            	'ifFormat'	=> '%Y/%m/%d'
			),
			'title' => $edit != 0 ? 'Fragment date' : 'Last access date',
				'description' => get_string('dateformatguide'),
        		'rules' => array('required' => true),
			'defaultvalue' => $edit != 0 ? (!$new ? strtotime($data->cdate) : null) : strtotime($data->note),
		),
		'reflection' => array(
			'type'        => 'textarea',
			'title'       => $edit != 0 ? 'Reflection' : 'Description',
			'rows'		  => 15,
			'cols'		  => 60,
			'description' => $edit != 0 ? 
				'Reflection is an important aspect of your ePortfolio presentation. Describe why you think this fragment is important, what it represents, etc.' :
				'Describe what this page is about',
			'rules' 	  => array('required' => $edit != 0 ? true : false),
			'defaultvalue' => $edit != 0 ? (!$new ? $data->reflection : null) : $data->description,
		),
	);
	
	if($edit != 0) {
		$elements['concept'] = array(
			'type'         => 'select',
			'title'        => 'Concept',
			'description'  => 'Select a concept related to this example',
			'options'      => array('' => 'Free fragment') + get_concept_list_by_user($USER->get('id')),
			'defaultvalue' => $data->cid,
		);
	}
	else {
		$elements['id'] = array(
			'type' => 'hidden',
			'value' => !$new ? $aid : null,
		);
	}
	
	$elements['submit'] = array(
		'type' => 'submitcancel',
		'value' => array('Save', 'Cancel'),
	    'goto' => get_config('wwwroot') . 'concept/bookmark/',
	);
	    
	$form = pieform(array(
	    'name' => 'edit_item',
	    'plugintype' => 'core',
	    'pluginname' => 'concept',
		'renderer'   => 'table',
	    'successcallback' => 'edit_item_submit',
		'elements' => $elements
	));
	$smarty->assign('PAGEHEADING', 'Editing record');
}

$smarty->assign('form', $form);
$smarty->display('concept/bookmark/edit.tpl');

function deleteitem_submit(Pieform $form, $values) {
    global $SESSION, $delete, $aid;
    
    db_begin();    
    if ($aid != 0) {
    	delete_records('concept_example', 'id', $delete);
    }
    else {
    	delete_records('concept_example', 'aid', $delete);
    	delete_records('artefact', 'id', $delete);
    }    
    db_commit();
    
    $SESSION->add_ok_msg("Record was successfully removed.");
    redirect('/concept/bookmark/');
}

function edit_item_submit(Pieform $form, $values) {
    global $SESSION, $aid, $new, $edit, $user;
    
    if($new) {
		$dbnow = db_format_timestamp(time());
    	$fordb = (object) array(    	
	    	'id' => 0,
			'artefacttype' => 'bookmark',
			'owner'	=> $user,
    		'ctime' => $dbnow,
    		'mtime' => $dbnow,
    		'atime' => $dbnow,
    		'title' => $values['title'],
			'description' =>  $values['reflection'],
			'note' => db_format_timestamp($values['cdate']),
    		'author' => $user,
		);
    	
    	insert_record('artefact', $fordb, 'id');    
    	$SESSION->add_ok_msg("Bookmark was successfully created.");	
    }
    elseif($edit == 0) {   	
		execute_sql("UPDATE {artefact} SET title = '" . $values['title'] . "', description = '". $values['reflection'] ."', 
			note = '". db_format_timestamp($values['cdate']) ."' WHERE id = ". $aid);
		$SESSION->add_ok_msg("Bookmark was successfully edited.");
    }
    else {
    	$fordb = (object) array(
	    	'id' => $edit,
			'aid' => $aid,
			'cid' => !empty($values['concept']) ? $values['concept'] : null,
    		'type' => 'bookmark',
			'title' => $values['title'],
    		'cdate' => db_format_timestamp($values['cdate']),
			'reflection' =>  $values['reflection'],
			'config' => 'none',
    		'complete' => 0,
		);
    	
    	update_record('concept_example', $fordb, 'id');
    	$SESSION->add_ok_msg("Example was successfully edited.");
    }	    
    
	redirect('/concept/bookmark/');
}

function edit_item_cancel_submit() {

    redirect('/concept/bookmark/');
}

?>