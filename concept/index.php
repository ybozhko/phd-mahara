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
define('MENUITEM', 'myportfolio/concept');

define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'concept');
define('SECTION_PAGE', 'index');

require(dirname(dirname(__FILE__)) . '/init.php');
require_once('pieforms/pieform.php');
require_once('concept.php');
define('TITLE', get_string('myconcepts', 'concept'));

$offset = param_integer('offset', 0);
$limit  = param_integer('limit', 5);

$data = ConceptMap::get_mymaps_data($offset, $limit);

$pagination = build_pagination(array(
    'id' => 'mapslist_pagination',
    'class' => 'center',
    'url' => get_config('wwwroot') . 'concept/index.php',
    'jsonscript' => 'concept/maps.json.php',
    'datatable' => 'mapslist',
    'count' => $data->count,
    'limit' => $data->limit,
    'offset' => $data->offset,
    'firsttext' => '',
    'previoustext' => '',
    'nexttext' => '',
    'lasttext' => '',
    'numbersincludefirstlast' => false,
    'resultcounttextsingular' => get_string('map', 'concept'),
    'resultcounttextplural' => get_string('maps', 'concept'),
));

$stylesheet = array('<link rel="stylesheet" type="text/css" href="' . get_config('wwwroot') . 'theme/concept.css">');

$smarty = smarty(array('paginator'), $stylesheet); 
$smarty->assign('maps', $data->data);
$smarty->assign('pagination', $pagination['html']);
$smarty->assign('strnomapsaddone',
    get_string('nomapsaddone','concept','<a href="' . get_config('wwwroot') . 'concept/edit.php?new=1">', '</a>'));
$smarty->assign('PAGEHEADING', hsc(get_string('myconcepts', 'concept')));
$smarty->display('concept/index.tpl');
?>
