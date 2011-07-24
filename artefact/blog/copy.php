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
define('MENUITEM', 'myportfolio/blogs');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'blog');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');

$id = param_integer('bid', 0);
$aid = param_integer('id', 0);

if($id != 0 && $aid != 0) {
	copy_fragment($id, $aid);
}

function copy_fragment($id, $a) {
    global $SESSION;
    
    db_begin();
    $record = get_record('concept_example', 'id', $id);

    $data = (object) array (
    	'aid'  		 => $record->aid,
    	'cid'  		 => null,
    	'type' 	 	 => $record->type,
    	'title' 	 => 'Copy of ' . $record->title,
    	'cdate' 	 => $record->cdate,
    	'reflection' => $record->reflection,
    	'config' 	 => $record->config,
    	'complete' 	 => $record->complete
    );

    $newrecord = insert_record('concept_example', $data, 'id', true);
    db_commit();

    $SESSION->add_ok_msg("Fragment successfully copied.");
    redirect('/artefact/blog/edit.php?bid=' . $newrecord . '&id=' . $a);
}

?>
