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
 * @subpackage artefact-competencies
 * @author     Yuliya Bozhko
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2010 Yuliya Bozhko, yuliya.bozhko@gmail.com
 *
 */

define('INTERNAL', 1);
define('MENUITEM', 'myportfolio/concept');

define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'concept');
define('SECTION_PAGE', 'index');

require(dirname(dirname(__FILE__)) . '/init.php');
require_once('pieforms/pieform.php');
require_once('concept.php');

$delete = $_POST['delete'];
$tf = $_POST['tf'];
$cn = $_POST['cn'];
$def = $_POST['def'];
$rename = $_POST['ren_id'];

if(isset($tf) && isset($cn)) {
	$stylesheet = array(
			'<link rel="stylesheet" type="text/css" href="' . get_config('wwwroot') . 'theme/concept.css">',
			'<link rel="stylesheet" type="text/css" href="' . get_config('wwwroot') . 'theme/jquery-ui.css">',
			'<link rel="stylesheet" type="text/css" href="' . get_config('wwwroot') . 'theme/views.css">'
			);
	
	$data = Concepts::get_concepts_timeline($_POST['map'], $tf, $cn);
	$smarty = smarty(array('jquery', 'timeline', 'jquery-ui', 'jquery.jcrop'), $stylesheet);
	$smarty->assign('id', $_POST['map']);	
	$smarty->assign('examples', $data);
	$output = $smarty->fetch('concept/frame.tpl');
	echo($output);
}
else {
	if (!isset($delete)) {
		if(isset($rename)) {
			$newname = $_POST['newname'];
			db_begin();
			execute_sql("UPDATE {concepts} SET name = ? WHERE id = ?", array($_POST['newname'], $_POST['ren_id']));
			db_commit();
		}
		elseif(!isset($def)) {
			$data = (object)array(
				'map' => $_POST['map'],
				'parent' => $_POST['parent'],
				'type' => $_POST['type'],
				'name' => $_POST['name'],
				'description' => $_POST['description'],
				'link' => null
			);
		
			Concepts::save($data);
		}
		else {	
			$ids = array();
					
			foreach($_POST['db'] as $i)
				$ids[] = $i;

			if($ids) {
				$strids = join(',', $ids);
				db_begin();
				execute_sql("UPDATE {concept_example} SET cid = ? WHERE id IN ($strids)", array($def));
				db_commit();
			}
		}
	} 
	else {
		$toremove = $_POST['id'];
		Concepts::remove_all($toremove);
	}
	
	$records = Concepts::get_concepts($_POST['map']);
	$jnodes = json_encode($records['concepts']);
	
	echo($jnodes);
}
?>
