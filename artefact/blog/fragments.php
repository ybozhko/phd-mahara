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
 * @subpackage artefact-blog
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2006-2009 Catalyst IT Ltd http://catalyst.net.nz
 *
 */

define('INTERNAL', 1);
define('MENUITEM', 'myportfolio/blogs');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'blog');
define('SECTION_PAGE', 'fragments');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('artefact', 'blog');

$wwwroot = get_config('wwwroot');
$id = param_integer('id', 0);

$artefact = artefact_instance_from_id($id);

$fragments = get_fragments($id);

$smarty = smarty();
$smarty->assign('PAGEHEADING', "Fragments: '" . $artefact->get('title') . "'");
$smarty->assign('fragments', $fragments);
$smarty->assign('id', $id);
$smarty->display('artefact:blog:fragments.tpl');
?>