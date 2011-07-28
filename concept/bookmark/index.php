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
 * @subpackage concepts
 * @author     Yuliya Bozhko
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2010 Yuliya Bozhko, yuliya.bozhko@gmail.com
 *
 */

define('INTERNAL', 1);
define('MENUITEM', 'myportfolio/bookmark');

define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'concept');
define('SECTION_PAGE', 'index');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once(get_config('libroot') . 'pieforms/pieform.php');
require_once(get_config('libroot') . 'pieforms/pieform/elements/calendar.php');
require_once('concept.php');
define('TITLE', get_string('bookmark', 'concept'));

$bookmarks = get_bookmarks();

$elements = array(
	'title' => array(
		'type'         => 'text',
		'title'        => 'Title',
		'description'  => 'Title of your example',
		'rules' 	   => array('required' => true),
	),
	'cdate' => array(
		'type'       => 'calendar',
		'caloptions' => array(
			'showsTime'      => false,
            'ifFormat'       => '%Y/%m/%d'
		),
		'title' => 'Fragment date',
		'description' => get_string('dateformatguide'),
        'rules' => array('required' => true),
	),
	'reflection' => array(
		'type'        => 'textarea',
		'title'       => 'Reflection',
		'rows'		  => 15,
		'cols'		  => 60,
		'description' => 'Reflection is an important aspect of your ePortfolio presentation. Describe why you think this fragment is important, what it represents, etc.',
		'rules' 	  => array('required' => true),
	),
	'etype' => array(                
			'type' => 'hidden',
			'value' => 'bookmark',
		),
	'concept' => array(
		'type'         => 'select',
		'title'        => 'Concept',
		'description'  => 'Select a concept related to this example',
		'options'      => array('' => 'Free fragment') + get_concept_list_by_user($USER->get('id')),
	),
	'submit' => array(
    	'type' => 'submit',
    	'value' => get_string('save'),
    	'goto' => get_config('wwwroot') . '/concept/bookmark/',
	)
);

if($bookmarks)
	foreach($bookmarks as $b) {
		$elements['aid'] = array(                
			'type' => 'hidden',
			'value' => $b->id,
		);
		$form = array(
    		'name' => 'newform' . $b->id,
    		'plugintype' => 'artefact',
    		'pluginname' => 'concept',
    		'elements' => $elements,
    		'successcallback' => 'newform_submit',
		);
		$b->form = pieform($form);
	}

$smarty = smarty(array('jquery'));
$smarty->assign('PAGEHEADING', TITLE);
$smarty->assign('bookmarks', $bookmarks);
$smarty->display('concept/bookmark/index.tpl');

function newform_submit(Pieform $form, $values) {
	global $SESSION;
	
	db_begin();        	
    $fordb = (object) array(
	    'id' => 0,
		'aid' => $values['aid'],
		'cid' => !empty($values['concept']) ? $values['concept'] : null,
    	'type' => $values['etype'],
		'title' => $values['title'],
    	'cdate' => db_format_timestamp($values['cdate']),
		'reflection' =>  $values['reflection'],
		'config' => 'none',
    	'complete' => 0,
    );
    insert_record('concept_example', $fordb, 'id');
    db_commit();
    
	$SESSION->add_ok_msg('New example was successfully created.');
    redirect('/concept/bookmark/');	
}

?>