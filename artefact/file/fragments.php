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
 * @copyright  (C) 2010 Yuliya Bozhko, yuliya.bozhko@gmail.com
 *
 */

define('INTERNAL', 1);
define('MENUITEM', 'myportfolio/files');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'file');
define('SECTION_PAGE', 'fragments');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('artefact', 'file');

$wwwroot = get_config('wwwroot');
$id = param_integer('item', 0);

$artefact = artefact_instance_from_id($id);

$fragments = get_fragments($id);

$smarty = smarty(array('jquery', 'jquery-ui'));
$smarty->assign('PAGEHEADING', "Fragments: '" . $artefact->get('title') . "'");
$smarty->assign('fragments', $fragments);
$smarty->assign('id', $id);
$smarty->display('artefact:file:fragments.tpl');

?>