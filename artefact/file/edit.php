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
define('PUBLIC', 1);
define('MENUITEM', 'myportfolio/files');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'file');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once('pieforms/pieform/elements/calendar.php');
require_once('pieforms/pieform.php');
safe_require('artefact', 'file');
require_once('file.php');

$user = $USER->get('id');
$id = param_integer('fid', 0);
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
	
	$elements = get_fragment_form_elements($data, $user, $aid, $artefact->get('oldextension'));
	
	$form = pieform(array(
    	'name' => 'editfragment',
    	'plugintype' => 'artefact',
    	'pluginname' => 'file',
	   	'jsform' => true,
	   	'renderer'   => 'table',
    	'successcallback' => 'editfragment_submit',
    	'elements' => $elements,
	)); 
	
	$stylesheets = array('<link rel="stylesheet" type="text/css" href="' . get_config('wwwroot') . 'theme/jquery.jcrop.css">');

if ($artefact->get('artefacttype') == 'image') { 
	$data ? $selection = explode(',', $data->config) : $selection = array();
		$js = <<<EOF
			$(document).ready(function() {
				$('#editfragment_config').css('display', 'none');
			});
			
			$(window).load(function() {
				var jcrop_api = $.Jcrop('#cropbox', {
					boxWidth: 600,
					boxHeight: 400,
					onChange: showCoords,
					onSelect: showCoords,
					bgOpacity: 0.4
				});
				
				jcrop_api.setSelect([$selection[0],$selection[1],$selection[2],$selection[3]]);
				
				function nothing(e) {
					e.stopPropagation();
					e.preventDefault();
					return false;
				};
			});
			
			function showCoords(c) {
				$('#editfragment_config').val(c.x + ',' + c.y + ',' + c.x2 + ',' + c.y2);
			};
EOF;
} elseif ($artefact->get('artefacttype') == 'file' & $artefact->get('oldextension') == 'txt') {
	$path = get_config('wwwroot') . 'artefact/file/download.php?file=' . $aid;
	$js = <<<EOF
		$(document).ready(function() {
			$('#editfragment_config').css('display', 'none');
			$('#filebox').load('$path');
		
			$('#filebox').select(function () { 
				var selection = $().selectedText();
	   			$('#selection').html('<pre>' + selection.text + '</pre>'); 
	   			$('#editfragment_config').val('<pre>' + selection.text + '</pre>');
	    	});
		});
EOF;
}
	
	$smarty = smarty(array('jquery', 'jquery.jcrop', 'jquery-ui', 'jquery.select'), $stylesheets);
	$smarty->assign_by_ref('form', $form);
	$smarty->assign('INLINEJAVASCRIPT', $js);

	if (!check_extension($artefact->get('oldextension'))) {
		$smarty->assign('nosupport', get_string('nosupport', 'artefact.file'));
	}
	
	$smarty->assign('PAGEHEADING', 'Edit Fragment');
	$smarty->display('artefact:file:edit.tpl');
} 


function delete_fragment($todelete, $a) {
    global $SESSION;
    
    db_begin();
    delete_records('concept_example', 'id', $todelete);
    db_commit();
    
    $SESSION->add_ok_msg("Fragment successfully deleted.");
    redirect('/artefact/file/fragments.php?item=' . $a);
}

function editfragment_submit(Pieform $form, $values) {
    global $SESSION, $aid, $new;

    if (isset($values['end']) & isset($values['start'])) {
    	$values['config'] = time_to_sec($values['start']) . "," . time_to_sec($values['end']);
    }
    
    $fordb = (object) array(
	    'id' => isset($values['fragment']) ? $values['fragment'] : 0,
		'aid' => $aid,
		'cid' => !empty($values['concept']) ? $values['concept'] : null,
    	'type' => $values['etype'],
		'title' => $values['title'],
    	'cdate' => db_format_timestamp($values['cdate']),
		'reflection' =>  $values['reflection'],
		'config' => $values['config'],
    	'complete' => $values['complete'],
	);
	
	db_begin();
    if (!$new) {
	//echo '<pre>';
	//print_r($values);
	//echo '</pre>';
    	update_record('concept_example', $fordb, 'id');
    }
    else {
	    insert_record('concept_example', $fordb, 'id');
    }	
	db_commit();
    $SESSION->add_ok_msg('New fragment was successfully created.');
    redirect('/artefact/file/fragments.php?item=' . $aid);
}

function editfragment_cancel_submit() {
	global $aid;
    redirect('/artefact/file/fragments.php?item=' . $aid);
}

?>