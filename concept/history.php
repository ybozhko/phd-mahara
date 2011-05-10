<?php
/**
 * Mahara: Electronic portfolio, weblog, resume builder and social networking
 * Copyright (C) 2006-2009 Catalyst IT Ltd and others; see:
 *                         http://wiki.mahara.org/Contributors
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
 * @subpackage core
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2006-2009 Catalyst IT Ltd http://catalyst.net.nz
 *
 */


/**
 * ===== Database trigger that is used for this functionality =====
 * DROP TRIGGER IF EXISTS before_delete_concept_access;
 * DELIMITER //
 * CREATE TRIGGER before_delete_concept_access
 *   BEFORE DELETE ON `concept_access` FOR EACH ROW
 *   BEGIN
 *       IF (SELECT COUNT(*) FROM concept_access_log WHERE map=OLD.map 
 *                         AND (accesstype=OLD.accesstype OR OLD.accesstype IS NULL) 
 * AND (`group`=OLD.group OR OLD.group IS NULL) 
 * AND (role=OLD.role OR OLD.role IS NULL) 
 * AND (usr=OLD.usr OR OLD.usr IS NULL) 
 * AND (token=OLD.token OR OLD.token IS NULL) 
 * AND (startdate=OLD.startdate OR OLD.startdate IS NULL) 
 * AND (stopdate=OLD.stopdate OR OLD.stopdate IS NULL)) = 0 THEN
 *           INSERT INTO concept_access_log (map, accesstype, `group`, role, usr, token, startdate, stopdate)
 *           VALUES (OLD.map, OLD.accesstype, OLD.group, OLD.role, OLD.usr, OLD.token, OLD.startdate, OLD.stopdate);
 *       END IF;
 *   END//
 *DELIMITER ;

 * 
 **/

define('INTERNAL', 1);
define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PAGE', 'editaccess');
define('MENUITEM', 'myportfolio/concept');

require(dirname(dirname(__FILE__)) . '/init.php');
require_once(get_config('libroot') . 'concept.php');

$mapid = param_integer('map', null);
$map = new ConceptMap($mapid);
define('TITLE', get_string('accesslogdsc', 'concept') . $map->get('name'));

$smarty = smarty(array('tablerenderer'));
$smarty->assign('data', $data);
$smarty->assign('PAGEHEADING', TITLE);
$smarty->display('concept/access_log.tpl');
?>