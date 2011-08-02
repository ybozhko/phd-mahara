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
 * @subpackage concept-map
 * @author     Yuliya Bozhko
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2011 Yuliya Bozhko, yuliya.bozhko@gmail.com
 *
 */
define('INTERNAL', 1);
define('MENUITEM', 'myportfolio/blogs');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'blog');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once('pieforms/pieform/elements/calendar.php');
require_once('pieforms/pieform.php');
safe_require('artefact', 'blog');

$user = $USER->get('id');
$id = param_integer('bid', 0);
$delete = param_integer('delete', 0);
$aid = param_integer('id', 0);
$new = param_integer('new', 0);

$data = null;

if($delete != 0 && $aid != 0) {
	delete_fragment($delete, $aid);
}
else {
	$data = get_record_select('concept_example', 'id = ?', array($id));
	
	$artefact = artefact_instance_from_id($aid);

	$elements = get_fragment_form_elements($data, $user, $aid, null, true);
	$elements['submit']['goto'] = get_config('wwwroot') . 'artefact/blog/fragments.php?id=' . $aid;
	
	$form = pieform(array(
    	'name'            => 'editblog',
		'method'          => 'post',
    	'plugintype'      => 'artefact',
    	'pluginname'      => 'blog',
	   	'renderer'        => 'table',
    	'successcallback' => 'editblog_submit',
    	'elements'        => $elements
	)); 
	
	$smarty = smarty();
	$smarty->assign('form', $form);
	
	if ($artefact->count_published_posts() == 0) {
		$smarty->assign('noposts', 'There are no published posts in this blog for creating new fragment.');
		$smarty->assign('id', $aid);
	}

	$smarty->assign('PAGEHEADING', 'Edit Fragment');
	$smarty->display('artefact:blog:edit.tpl');	
}

function delete_fragment($todelete, $a) {
    global $SESSION;
    
    db_begin();
    delete_records('concept_example', 'id', $todelete);
    db_commit();
    
    $SESSION->add_ok_msg("Fragment successfully deleted.");
    redirect('/artefact/blog/fragments.php?id=' . $a);
}

function editblog_submit(Pieform $form, $values) {
    global $SESSION, $aid, $new;

    $fordb = (object) array(
	    'id' => isset($values['fragment']) ? $values['fragment'] : 0,
		'aid' => $aid,
		'cid' => !empty($values['concept']) ? $values['concept'] : null,
    	'type' => $values['etype'],
		'title' => $values['title'],
    	'cdate' => db_format_timestamp($values['cdate']),
		'reflection' =>  $values['reflection'],
		'config' => implode(',', $values['posts']),
    	'complete' => 0,
	);
	
	db_begin();
    if (!$new) {
    	update_record('concept_example', $fordb, 'id');
    	$SESSION->add_ok_msg("Fragment was successfully edited.");
    }
    else {
	    insert_record('concept_example', $fordb, 'id');
	    $SESSION->add_ok_msg("New fragment was successfully created.");
    }	
	db_commit();	

    redirect('/artefact/blog/fragments.php?id=' . $aid);
}
?>