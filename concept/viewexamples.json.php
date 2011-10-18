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
define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'concept');

require(dirname(dirname(__FILE__)) . '/init.php');
require_once('pieforms/pieform.php');
require_once('group.php');
require_once(get_config('libroot') . 'concept.php');

$cid = $_POST['id'];
$concept = new Concepts($cid);

//// access key for roaming teachers
//$mnettoken = $SESSION->get('mnetuser') ? param_alphanum('mt', null) : null;
//
//// access key for logged out users
//$usertoken = (is_null($mnettoken)) ? param_alphanum('t', null) : null;
//
//if ($mnettoken) {
//    if (!$mapid = get_map_from_token($mnettoken)) {
//        throw new AccessDeniedException(get_string('accessdenied', 'error'));
//    }
//}
//else if ($usertoken) {
//    if (!$mapid = get_map_from_token($usertoken)) {
//        throw new AccessDeniedException(get_string('accessdenied', 'error'));
//    }
//}
//else {
//    $mapid = $concept->get('map');
//}
//
if (!can_view_map($mapid)) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}

define('TITLE', "Examples of '" . $concept->get('name') . "'");

$examples = Concepts::get_examples($cid);

$num = count($examples) - 1;

$smarty = smarty(array('jquery', 'jquery-ui', 'jquery.jcrop')); 

$smarty->assign('title', TITLE);
$smarty->assign('examples', $examples);
$smarty->assign('map', $concept->get('map'));

$output = $smarty->fetch('concept/viewexamples.tpl');
echo($output);

?>