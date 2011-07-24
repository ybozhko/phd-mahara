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

define('INTERNAL', 1);
define('PUBLIC', 1);
define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'concept');
define('SECTION_PAGE', 'viewmap');

require(dirname(dirname(__FILE__)) . '/init.php');

require_once(get_config('libroot') . 'concept.php');
require_once('group.php');
safe_require('artefact', 'comment');

$wwwroot = get_config('wwwroot');

// access key for roaming teachers
$mnettoken = $SESSION->get('mnetuser') ? param_alphanum('mt', null) : null;

// access key for logged out users
$usertoken = (is_null($mnettoken)) ? param_alphanum('t', null) : null;

if ($mnettoken) {
    if (!$mapid = get_map_from_token($mnettoken)) {
        throw new AccessDeniedException(get_string('accessdenied', 'error'));
    }
}
else if ($usertoken) {
    if (!$mapid = get_map_from_token($usertoken)) {
        throw new AccessDeniedException(get_string('accessdenied', 'error'));
    }
}
else {
    $mapid = param_integer('id');
}

if (!can_view_map($mapid)) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}

// Feedback list pagination requires limit/offset params
$limit       = param_integer('limit', 10);
$offset      = param_integer('offset', 0);
$showcomment = param_integer('showcomment', null);

$map = new ConceptMap($mapid);

//create map
$records = Concepts::get_concepts($mapid);
$jnodes = json_encode($records['concepts']);

$javascript = <<<EOF
		$(document).ready(function() {
			CreateTree($jnodes);
			$("#examples").css({'overflow':'hidden', 'height':'0px'});
		});
		
		var t = null;

		function CreateTree(nodes) {
			t = new ECOTree('t','conceptmap');	
			t.config.useTarget = true;
			for (i in nodes) {
				if (nodes[i][2] == 1) {
					t.add(nodes[i][0], nodes[i][1], nodes[i][2], nodes[i][3]);	
				}
				else {
					t.add(nodes[i][0], nodes[i][1], nodes[i][2], nodes[i][3], 100, 75);
				}			
				t.setNodeTarget(nodes[i][0], 'javascript:showExamples('+ nodes[i][0] +')', true);
			}
			t.UpdateTree();
		};	

		function showExamples(nodeid) {
			$("#conceptmap").css({'overflow':'hidden', 'height':'0px'});

			$.post('viewexamples.php', 
				'id=' + nodeid, 
				function (result) {
            		$('#examples').append(result);
            	});
            	
			$('#examples').css('height', '100%');
		}
		
		function showMap() {
			$("#examples").empty();
			$('#backBtn').remove();
			$('#conceptmap').css('height', '100%');
		}
EOF;

// Create the "make feedback private form" now if it's been submitted
if (param_variable('make_public_submit', null)) {
    pieform(ArtefactTypeComment::make_public_form(param_integer('comment')));
}
else if (param_variable('delete_comment_submit', null)) {
    pieform(ArtefactTypeComment::delete_comment_form(param_integer('comment')));
}

// If the view has comments turned off, tutors can still leave
// comments if the view is submitted to their group.
if ($commenttype = $map->user_comments_allowed($USER)) {
    $moderate = isset($commenttype) && $commenttype === 'private';
    $addfeedbackform = pieform(ArtefactTypeComment::add_comment_form(false, $moderate));
}

//Added next 2 lines as these variables are passed by reference
$view = null;
$artefact = null;

$feedback = ArtefactTypeComment::get_comments($limit, $offset, $showcomment, $view, $artefact, $map);

//echo "<pre>";
//	print_r($feedback);
//echo "</pre>";

$stylesheet = array(
				'<link rel="stylesheet" type="text/css" href="' . $wwwroot . 'theme/concept.css">',
				'<link rel="stylesheet" type="text/css" href="' . $wwwroot . 'theme/jquery-ui.css">',
				'<link rel="stylesheet" type="text/css" href="' . $wwwroot . 'theme/views.css">'
			  );

$smarty = smarty(
	array('paginator', 'mapfeedback', 'artefact/resume/resumeshowhide.js', 'jquery', 'CTree', 'jquery-ui', 'jquery.jcrop'), 
	$stylesheet, 
	array(), 
	array('stylesheets' => array('style/views.css'), 'sidebars' => false,)
);

$javascript .= <<<EOF
var mapid = {$mapid};
addLoadEvent(function () {
    paginator = {$feedback->pagination_js}
});
EOF;

//microheaders
$can_edit = $USER->can_edit_map($map);

if (get_config('viewmicroheaders')) {
    $smarty->assign('microheaders', true);
    $smarty->assign('microheadertitle', $map->display_title());

    if ($can_edit) {
        $microheaderlinks = array(
                array(
                    'name' => get_string('edittitle', 'view'),
                    'url' => $wwwroot . 'concept/edit.php?id=' . $mapid,
                    'type' => 'edit',
                ),
                array(
                    'name' => get_string('editcontent', 'view'),
                    'url' => $wwwroot . 'concept/map.php?id=' . $mapid,
                    'type' => 'edit',
                ),
                array(
                    'name' => get_string('editaccess', 'view'),
                    'url' => $wwwroot . 'concept/access.php?map=' . $mapid,
                    'type' => 'edit',
                ),
        );
        $smarty->assign('microheaderlinks', $microheaderlinks);
    }

    if ($USER->is_logged_in()) {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $page = $wwwroot . 'concept/viewmap.php?id=' . $mapid;
            if ($_SERVER['HTTP_REFERER'] != $page) {
                $smarty->assign('backurl', $_SERVER['HTTP_REFERER']);
            }
        }
    }
}

$smarty->assign('PAGETITLE', strip_tags($map->display_title()));

$smarty->assign('INLINEJAVASCRIPT', $javascript);
$smarty->assign('feedback', $feedback);
$smarty->assign('map', $map);
$smarty->assign('mapdescription', $map->get('description'));
$smarty->assign('id', $mapid);
$smarty->assign('layout', 1);

if (isset($addfeedbackform)) {
    $smarty->assign('enablecomments', 1);
    $smarty->assign('addfeedbackform', $addfeedbackform);
}
$smarty->display('concept/viewmap.tpl');

?>
