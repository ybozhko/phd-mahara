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
define('PUBLIC', 1);
define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'concept');
define('SECTION_PAGE', 'viewtimeline');

require(dirname(dirname(__FILE__)) . '/init.php');

require_once(get_config('libroot') . 'concept.php');
require_once('group.php');
safe_require('artefact', 'comment');

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

$wwwroot = get_config('wwwroot');

// Feedback list pagination requires limit/offset params
$limit       = param_integer('limit', 10);
$offset      = param_integer('offset', 0);
$showcomment = param_integer('showcomment', null);

$map = new ConceptMap($mapid);
$data = Concepts::get_concepts_timeline($mapid, 'M-Y');

$tf = get_records_sql_array('SELECT t.name, t.id FROM {concept_timeframe} t 
							INNER JOIN {concept_map_timeframe} m on m.timeframe=t.id WHERE m.map = ? ', array($mapid));

$concepts = Concepts::get_concepts_list($mapid);

$stylesheet = array(
			'<link rel="stylesheet" type="text/css" href="' . $wwwroot . 'theme/concept.css">',
			'<link rel="stylesheet" type="text/css" href="' . $wwwroot . 'theme/jquery-ui.css">',
			'<link rel="stylesheet" type="text/css" href="' . $wwwroot . 'theme/views.css">'
			);
			
$js = <<<EOF
	function changeTimeFrame(tf) {
		$.post('process.php', 
			'map=' + $mapid + '&tf=' + tf + '&cn=' + $('#concepts').val(), 
			function (result) {
            	$('#main-timeline').html(result);
            	loadTimeline();
            });
    }; 
    
    function changeConcept(cn) {
		$.post('process.php', 
			'map=' + $mapid + '&tf=' + $('#timeframes').val() + '&cn=' + cn, 
			function (result) {
            	$('#main-timeline').html(result);
            	loadTimeline();
            });
    }; 

    $(document).ready(function(){
		loadTimeline();
	});
    
    function stopVideo(id, startTime, stopTime) {
	    if ($('#video_' + id).attr('currentTime') > stopTime) {
	        $('#video_' + id).trigger('pause');
			$('#video_' + id).attr('currentTime', startTime);
			$('#playpause_' + id).val('Replay');
	    }
    };
   
    function startVideo(id, startTime) {
    	$('#video_' + id).attr('currentTime', startTime);
    };

	function playOrPause(id) {
		if ($('#video_' + id).attr('paused')) {
	    	$('#video_' + id).trigger('play');
	    	$('#playpause_' + id).val('Pause');
	  	} else {
	    	$('#video_' + id).trigger('pause');
	   		$('#playpause_' + id).val('Play');
	  	}
	};
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

$smarty = smarty(
	array('paginator', 'viewmenu', 'jquery', 'timeline', 'jquery-ui', 'jquery.jcrop'), 
	$stylesheet,
	array(),
	array('stylesheets' => array('style/views.css'), 'sidebars' => false,)
); 

$js .= <<<EOF
var viewid = {$mapid};
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

$smarty->assign('examples', $data);
$smarty->assign('id', $mapid);
$smarty->assign('map', $map);
$smarty->assign('layout', 0);
$smarty->assign('tf', $tf);
$smarty->assign('concepts', $concepts);
$smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->assign('feedback', $feedback);

if (isset($addfeedbackform)) {
    $smarty->assign('enablecomments', 1);
    $smarty->assign('addfeedbackform', $addfeedbackform);
}

$smarty->display('concept/timeline.tpl');

?>